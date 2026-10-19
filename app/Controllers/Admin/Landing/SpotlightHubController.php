<?php

namespace App\Controllers\Admin\Landing;

use App\Controllers\BaseController;
use App\Services\AuditService;
use App\Services\SystemSettingService;
use CodeIgniter\Database\ConnectionInterface;

class SpotlightHubController extends BaseController
{
    private AuditService $auditService;
    private SystemSettingService $settingService;
    private ConnectionInterface $db;

    public function __construct()
    {
        $this->auditService   = new AuditService();
        $this->settingService = new SystemSettingService();
        $this->db             = \Config\Database::connect();
    }

    /**
     * Centralized Spotlight & Carousel Management Dashboard
     */
    public function index()
    {
        $activeTab = trim((string) ($this->request->getGet('tab') ?: 'overview'));
        $search    = trim((string) ($this->request->getGet('q') ?: ''));

        // 1. Fetch Arena Belajar (Programs, Courses, Classes)
        $programs = $this->fetchPrograms($search);
        $courses  = $this->fetchCourses($search);
        $classes  = $this->fetchClasses($search);

        // 2. Fetch Bunga Rampai (Articles, Cases, Templates, Multimedia)
        $knowledgeArticles = $this->fetchKnowledgeArticles($search);
        $knowledgeMedia    = $this->fetchKnowledgeMedia($search);

        // 3. Fetch Regulasi
        $regulations = $this->fetchRegulations($search);

        // 4. Fetch Berita
        $news = $this->fetchNews($search);

        // 5. Fetch Event & Agenda
        $events = $this->fetchEvents($search);

        // 6. Settings Snapshot
        $settings = [
            'learning' => [
                'mode'  => (string) setting('learning.spotlight_mode', 'all'),
                'limit' => (int) setting('learning.spotlight_limit', 6),
            ],
            'knowledge' => [
                'mode'  => (string) setting('knowledge.spotlight_mode', 'all'),
                'limit' => (int) setting('knowledge.spotlight_limit', 6),
            ],
            'regulation' => [
                'mode'  => (string) setting('regulation.spotlight_mode', 'all'),
                'limit' => (int) setting('regulation.spotlight_limit', 6),
            ],
            'news' => [
                'mode'  => (string) setting('news.spotlight_mode', 'all'),
                'limit' => (int) setting('news.spotlight_limit', 5),
            ],
            'event' => [
                'mode'  => (string) setting('event.spotlight_mode', 'all'),
                'limit' => (int) setting('event.spotlight_limit', 5),
            ],
        ];

        // 7. Aggregate Statistics
        $countCarousel = fn(array $items): int => count(array_filter($items, fn($i) => ! empty($i['carousel_enabled'])));
        $countFeatured = fn(array $items): int => count(array_filter($items, fn($i) => ! empty($i['is_featured'])));

        $stats = [
            'total_carousel_portal' => $countCarousel($programs) + $countCarousel($courses) + $countCarousel($classes)
                + $countCarousel($knowledgeArticles) + $countCarousel($knowledgeMedia)
                + $countCarousel($regulations) + $countCarousel($news) + $countCarousel($events),
            'total_featured_portal' => $countFeatured($programs) + $countFeatured($courses) + $countFeatured($classes)
                + $countFeatured($knowledgeArticles) + $countFeatured($knowledgeMedia)
                + $countFeatured($regulations) + $countFeatured($news) + $countFeatured($events),
            'arena' => [
                'carousel_count' => $countCarousel($programs) + $countCarousel($courses) + $countCarousel($classes),
                'featured_count' => $countFeatured($programs) + $countFeatured($courses) + $countFeatured($classes),
                'programs'       => $countCarousel($programs),
                'courses'        => $countCarousel($courses),
                'classes'        => $countCarousel($classes),
            ],
            'knowledge' => [
                'carousel_count' => $countCarousel($knowledgeArticles) + $countCarousel($knowledgeMedia),
                'featured_count' => $countFeatured($knowledgeArticles) + $countFeatured($knowledgeMedia),
                'articles'       => $countCarousel($knowledgeArticles),
                'media'          => $countCarousel($knowledgeMedia),
            ],
            'regulation' => [
                'carousel_count' => $countCarousel($regulations),
                'featured_count' => $countFeatured($regulations),
            ],
            'news' => [
                'carousel_count' => $countCarousel($news),
                'featured_count' => $countFeatured($news),
            ],
            'event' => [
                'carousel_count' => $countCarousel($events),
                'featured_count' => $countFeatured($events),
            ],
        ];

        return view('dashboard/landing/spotlight_hub/index', [
            'title'             => 'Pusat Manajemen Carousel & Sorotan | Dashboard BegawiPengadaan',
            'pageTitle'         => 'Pusat Manajemen Carousel & Sorotan',
            'activeMenu'        => 'landing',
            'activeSubmenu'     => 'spotlight-center',
            'activeTab'         => $activeTab,
            'search'            => $search,
            'programs'          => $programs,
            'courses'           => $courses,
            'classes'           => $classes,
            'knowledgeArticles' => $knowledgeArticles,
            'knowledgeMedia'    => $knowledgeMedia,
            'regulations'       => $regulations,
            'news'              => $news,
            'events'            => $events,
            'settings'          => $settings,
            'stats'             => $stats,
        ]);
    }

    /**
     * AJAX & POST Toggle for Carousel Status / Featured Status
     */
    public function toggle()
    {
        $feature = trim((string) ($this->request->getPost('feature') ?: $this->request->getVar('feature') ?: ($_POST['feature'] ?? '')));
        $id      = (int) ($this->request->getPost('id') ?: $this->request->getVar('id') ?: ($_POST['id'] ?? 0));
        $field   = trim((string) ($this->request->getPost('field') ?: $this->request->getVar('field') ?: ($_POST['field'] ?? ''))); // carousel_enabled | is_featured | featured
        $rawVal  = $this->request->getPost('value') ?? $this->request->getVar('value') ?? ($_POST['value'] ?? null);
        $hasVal  = $rawVal !== null;
        $val     = $hasVal ? (int) $rawVal : null;

        $target = $this->resolveFeatureTable($feature, $field);
        if ($target === null || $id <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Target fitur atau kolom tidak valid.',
            ])->setStatusCode(400);
        }

        [$table, $col] = $target;

        $current = $this->db->table($table)->where('id', $id)->get()->getRowArray();
        if (! $current) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak ditemukan.',
            ])->setStatusCode(404);
        }

        $newVal = $hasVal ? ($val ? 1 : 0) : (! empty($current[$col]) ? 0 : 1);

        $updateData = [$col => $newVal];
        if ($this->db->fieldExists('updated_at', $table)) {
            $updateData['updated_at'] = date('Y-m-d H:i:s');
        }

        $this->db->table($table)->where('id', $id)->update($updateData);

        $rawActorId = (int) (session()->get('user_id') ?: 0);
        $actorId    = $rawActorId > 0 ? $rawActorId : null;
        $this->auditService->log(
            $actorId,
            'spotlight.toggle',
            $table,
            $col,
            $id,
            ['feature' => $feature, 'field' => $col, 'new_value' => $newVal]
        );

        $featureLabels = [
            'program'           => 'Program Arena Belajar',
            'course'            => 'Course Arena Belajar',
            'class'             => 'Kelas Arena Belajar',
            'knowledge_article' => 'Artikel/Dokumen Bunga Rampai',
            'knowledge_media'   => 'Multimedia Bunga Rampai',
            'regulation'        => 'Regulasi PBJ',
            'news'              => 'Warta Berita PBJ',
            'event'             => 'Event & Agenda',
        ];
        $label = $featureLabels[$feature] ?? ucfirst($feature);
        $actionName = ($col === 'carousel_enabled' || $col === 'featured') ? 'Status sorotan hero' : 'Status unggulan';
        $stateText = $newVal ? 'diaktifkan' : 'dinonaktifkan';

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success'   => true,
                'message'   => "{$actionName} untuk {$label} berhasil {$stateText}.",
                'new_state' => $newVal,
                'field'     => $col,
                'feature'   => $feature,
                'id'        => $id,
            ]);
        }

        return redirect()->back()->with('message', "{$actionName} untuk {$label} berhasil {$stateText}.");
    }

    /**
     * AJAX & POST Reorder Slide Item
     */
    public function updateOrder()
    {
        $feature = trim((string) ($this->request->getPost('feature') ?: $this->request->getVar('feature') ?: ($_POST['feature'] ?? '')));
        $id      = (int) ($this->request->getPost('id') ?: $this->request->getVar('id') ?: ($_POST['id'] ?? 0));
        $rawOrder = $this->request->getPost('order') ?? $this->request->getVar('order') ?? ($_POST['order'] ?? null);
        $order   = ($rawOrder !== null && $rawOrder !== '') ? (int) $rawOrder : null;

        $target = $this->resolveFeatureTable($feature, 'carousel_order');
        if ($target === null || $id <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Target fitur tidak valid.',
            ])->setStatusCode(400);
        }

        [$table, $col] = $target;

        $updateData = [$col => $order];
        if ($this->db->fieldExists('updated_at', $table)) {
            $updateData['updated_at'] = date('Y-m-d H:i:s');
        }

        $this->db->table($table)->where('id', $id)->update($updateData);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Urutan slide berhasil diperbarui.',
                'order'   => $order,
            ]);
        }

        return redirect()->back()->with('message', 'Urutan slide berhasil diperbarui.');
    }

    /**
     * Quick save spotlight mode and limit for features
     */
    public function saveSettings()
    {
        $group = trim((string) ($this->request->getPost('group') ?: $this->request->getVar('group') ?: ($_POST['group'] ?? '')));
        $validGroups = ['learning', 'knowledge', 'regulation', 'news', 'event'];

        if (! in_array($group, $validGroups, true)) {
            return redirect()->back()->with('error', 'Kategori pengaturan tidak valid.');
        }

        $post = $this->request->getPost() ?: $_POST;
        $saveData = [];
        if (isset($post['spotlight_mode'])) {
            $saveData['spotlight_mode'] = trim((string) $post['spotlight_mode']);
        }
        if (isset($post['spotlight_limit'])) {
            $saveData['spotlight_limit'] = max(3, min(15, (int) $post['spotlight_limit']));
        }

        if (! empty($saveData)) {
            $this->settingService->saveGroup($group, $saveData);
            $rawActorId = (int) (session()->get('user_id') ?: 0);
            $actorId    = $rawActorId > 0 ? $rawActorId : null;
            $this->auditService->log(
                $actorId,
                'spotlight.settings.updated',
                'site_settings',
                $group,
                null,
                $saveData
            );
        }

        return redirect()->back()->with('message', 'Konfigurasi mode sorotan ' . ucfirst($group) . ' berhasil disimpan.');
    }

    /**
     * Search published items not yet highlighted in carousel
     */
    public function searchAvailable()
    {
        $feature = trim((string) $this->request->getGet('feature'));
        $q       = trim((string) $this->request->getGet('q'));

        $results = [];

        switch ($feature) {
            case 'program':
                $builder = $this->db->table('programs')
                    ->where('status', 'published')
                    ->where('visibility', 'public');
                if ($q !== '') $builder->like('title', $q);
                $rows = $builder->limit(10)->get()->getResultArray();
                foreach ($rows as $r) {
                    $results[] = [
                        'id'               => (int) $r['id'],
                        'title'            => $r['title'],
                        'subtitle'         => 'Program Pembelajaran',
                        'carousel_enabled' => (int) ($r['carousel_enabled'] ?? 0),
                        'is_featured'      => (int) ($r['featured'] ?? 0),
                        'cover_url'        => $this->resolveCover($r['cover_file_id'] ?? null, $r['cover_url'] ?? null),
                    ];
                }
                break;

            case 'course':
                $builder = $this->db->table('courses c')
                    ->select('c.*, p.title as program_title')
                    ->join('programs p', 'p.id = c.program_id', 'left')
                    ->where('c.status', 'published')
                    ->where('c.visibility', 'public');
                if ($q !== '') $builder->like('c.title', $q);
                $rows = $builder->limit(10)->get()->getResultArray();
                foreach ($rows as $r) {
                    $results[] = [
                        'id'               => (int) $r['id'],
                        'title'            => $r['title'],
                        'subtitle'         => $r['program_title'] ?: 'Course Mandiri',
                        'carousel_enabled' => (int) ($r['carousel_enabled'] ?? 0),
                        'is_featured'      => (int) ($r['featured'] ?? 0),
                        'cover_url'        => $this->resolveCover($r['cover_file_id'] ?? null, $r['cover_url'] ?? null),
                    ];
                }
                break;

            case 'class':
                $builder = $this->db->table('learning_classes lc')
                    ->select('lc.*, c.title as course_title')
                    ->join('courses c', 'c.id = lc.course_id', 'left')
                    ->whereIn('lc.status', ['scheduled', 'active']);
                if ($q !== '') $builder->like('lc.name', $q);
                $rows = $builder->limit(10)->get()->getResultArray();
                foreach ($rows as $r) {
                    $results[] = [
                        'id'               => (int) $r['id'],
                        'title'            => $r['name'],
                        'subtitle'         => 'Kode: ' . $r['code'] . ' &middot; ' . ($r['course_title'] ?? ''),
                        'carousel_enabled' => (int) ($r['carousel_enabled'] ?? 0),
                        'is_featured'      => (int) ($r['featured'] ?? 0),
                        'cover_url'        => $this->resolveCover($r['cover_file_id'] ?? null, $r['cover_url'] ?? null),
                    ];
                }
                break;

            case 'knowledge_article':
                $builder = $this->db->table('knowledge_articles')
                    ->where('workflow_status', 'published')
                    ->where('visibility', 'public');
                if ($q !== '') $builder->like('title', $q);
                $rows = $builder->limit(10)->get()->getResultArray();
                foreach ($rows as $r) {
                    $results[] = [
                        'id'               => (int) $r['id'],
                        'title'            => $r['title'],
                        'subtitle'         => strtoupper($r['type'] ?? 'Artikel'),
                        'carousel_enabled' => (int) ($r['carousel_enabled'] ?? 0),
                        'is_featured'      => (int) ($r['featured'] ?? 0),
                        'cover_url'        => $this->resolveCover($r['cover_file_id'] ?? null, $r['cover_url'] ?? null),
                    ];
                }
                break;

            case 'regulation':
                $builder = $this->db->table('regulations')
                    ->where('deleted_at', null)
                    ->where('workflow_status', 'published');
                if ($q !== '') $builder->like('title', $q);
                $rows = $builder->limit(10)->get()->getResultArray();
                foreach ($rows as $r) {
                    $results[] = [
                        'id'               => (int) $r['id'],
                        'title'            => $r['title'],
                        'subtitle'         => 'Nomor: ' . ($r['document_number'] ?? '-') . ' Th. ' . ($r['document_year'] ?? '-'),
                        'carousel_enabled' => (int) ($r['carousel_enabled'] ?? 0),
                        'is_featured'      => (int) ($r['is_featured'] ?? 0),
                        'cover_url'        => $this->resolveCover($r['cover_file_id'] ?? null, null, $r['cover_image_path'] ?? null),
                    ];
                }
                break;

            case 'news':
                $builder = $this->db->table('news')
                    ->where('workflow_status', 'published');
                if ($q !== '') $builder->like('title', $q);
                $rows = $builder->limit(10)->get()->getResultArray();
                foreach ($rows as $r) {
                    $results[] = [
                        'id'               => (int) $r['id'],
                        'title'            => $r['title'],
                        'subtitle'         => 'Warta Berita PBJ',
                        'carousel_enabled' => (int) ($r['carousel_enabled'] ?? 0),
                        'is_featured'      => (int) ($r['is_featured'] ?? 0),
                        'cover_url'        => $this->resolveCover($r['cover_file_id'] ?? null, null),
                    ];
                }
                break;

            case 'event':
                $builder = $this->db->table('events')
                    ->where('visibility', 'public')
                    ->where('deleted_at IS NULL', null, false);
                if ($q !== '') $builder->like('title', $q);
                $rows = $builder->limit(10)->get()->getResultArray();
                foreach ($rows as $r) {
                    $results[] = [
                        'id'               => (int) $r['id'],
                        'title'            => $r['title'],
                        'subtitle'         => strtoupper($r['type'] ?? 'Event') . ' &middot; ' . strtoupper($r['mode'] ?? 'online'),
                        'carousel_enabled' => (int) ($r['carousel_enabled'] ?? 0),
                        'is_featured'      => (int) ($r['is_featured'] ?? 0),
                        'cover_url'        => $this->resolveCover($r['cover_file_id'] ?? null, null),
                    ];
                }
                break;
        }

        return $this->response->setJSON([
            'success' => true,
            'results' => $results,
        ]);
    }

    // -------------------------------------------------------------------------
    // Private Query & Formatter Helpers
    // -------------------------------------------------------------------------

    private function fetchPrograms(string $q = ''): array
    {
        $builder = $this->db->table('programs')
            ->where('deleted_at', null)
            ->where('status', 'published')
            ->where('visibility', 'public');

        if ($q !== '') {
            $builder->groupStart()->like('title', $q)->orLike('summary', $q)->groupEnd();
        }

        $rows = $builder
            ->orderBy('carousel_enabled', 'DESC')
            ->orderBy('CASE WHEN carousel_order IS NULL THEN 9999 ELSE carousel_order END', 'ASC', false)
            ->orderBy('featured', 'DESC')
            ->orderBy('id', 'DESC')
            ->get()->getResultArray();

        return array_map(function ($r) {
            return [
                'id'               => (int) $r['id'],
                'feature'          => 'program',
                'feature_group'    => 'arena',
                'type_label'       => 'Program Pembelajaran',
                'badge_class'      => 'bg-primary-100 text-primary-600',
                'title'            => $r['title'],
                'subtitle'         => $r['slug'] ?? '',
                'summary'          => $r['summary'] ?: mb_strimwidth(strip_tags((string) $r['description']), 0, 100, '...'),
                'cover_url'        => $this->resolveCover($r['cover_file_id'] ?? null, $r['cover_url'] ?? null),
                'carousel_enabled' => (int) ($r['carousel_enabled'] ?? 0),
                'is_featured'      => (int) ($r['featured'] ?? 0),
                'carousel_order'   => $r['carousel_order'] ?? null,
                'public_url'       => site_url('program/' . ($r['slug'] ?? $r['id'])),
                'edit_url'         => site_url('dashboard/pembelajaran/program/' . $r['id'] . '/ubah'),
            ];
        }, $rows);
    }

    private function fetchCourses(string $q = ''): array
    {
        $builder = $this->db->table('courses c')
            ->select('c.*, p.title as program_title')
            ->join('programs p', 'p.id = c.program_id', 'left')
            ->where('c.deleted_at', null)
            ->where('c.status', 'published')
            ->where('c.visibility', 'public');

        if ($q !== '') {
            $builder->groupStart()->like('c.title', $q)->orLike('c.summary', $q)->groupEnd();
        }

        $rows = $builder
            ->orderBy('c.carousel_enabled', 'DESC')
            ->orderBy('CASE WHEN c.carousel_order IS NULL THEN 9999 ELSE c.carousel_order END', 'ASC', false)
            ->orderBy('c.featured', 'DESC')
            ->orderBy('c.id', 'DESC')
            ->get()->getResultArray();

        return array_map(function ($r) {
            return [
                'id'               => (int) $r['id'],
                'feature'          => 'course',
                'feature_group'    => 'arena',
                'type_label'       => 'Course Mandiri (' . strtoupper($r['level'] ?? 'Basic') . ')',
                'badge_class'      => 'bg-info-100 text-info-600',
                'title'            => $r['title'],
                'subtitle'         => $r['program_title'] ?: 'Course Terbuka',
                'summary'          => $r['summary'] ?: mb_strimwidth(strip_tags((string) $r['description']), 0, 100, '...'),
                'cover_url'        => $this->resolveCover($r['cover_file_id'] ?? null, $r['cover_url'] ?? null),
                'carousel_enabled' => (int) ($r['carousel_enabled'] ?? 0),
                'is_featured'      => (int) ($r['featured'] ?? 0),
                'carousel_order'   => $r['carousel_order'] ?? null,
                'public_url'       => site_url('course/' . ($r['slug'] ?? $r['id'])),
                'edit_url'         => site_url('dashboard/pembelajaran/course/' . $r['id'] . '/ubah'),
            ];
        }, $rows);
    }

    private function fetchClasses(string $q = ''): array
    {
        $builder = $this->db->table('learning_classes lc')
            ->select('lc.*, c.title as course_title')
            ->join('courses c', 'c.id = lc.course_id', 'left')
            ->where('lc.deleted_at', null)
            ->whereIn('lc.status', ['scheduled', 'active']);

        if ($q !== '') {
            $builder->groupStart()->like('lc.name', $q)->orLike('lc.code', $q)->groupEnd();
        }

        $rows = $builder
            ->orderBy('lc.carousel_enabled', 'DESC')
            ->orderBy('CASE WHEN lc.carousel_order IS NULL THEN 9999 ELSE lc.carousel_order END', 'ASC', false)
            ->orderBy('lc.featured', 'DESC')
            ->orderBy('lc.start_at', 'ASC')
            ->get()->getResultArray();

        return array_map(function ($r) {
            return [
                'id'               => (int) $r['id'],
                'feature'          => 'class',
                'feature_group'    => 'arena',
                'type_label'       => 'Kelas / Batch (' . strtoupper($r['status'] ?? 'Active') . ')',
                'badge_class'      => 'bg-success-100 text-success-600',
                'title'            => $r['name'],
                'subtitle'         => 'Kode: ' . $r['code'] . ' &middot; ' . ($r['course_title'] ?? ''),
                'summary'          => $r['description'] ?: 'Kelas pelatihan dengan pembimbing dan jadwal terstruktur.',
                'cover_url'        => $this->resolveCover($r['cover_file_id'] ?? null, $r['cover_url'] ?? null),
                'carousel_enabled' => (int) ($r['carousel_enabled'] ?? 0),
                'is_featured'      => (int) ($r['featured'] ?? 0),
                'carousel_order'   => $r['carousel_order'] ?? null,
                'public_url'       => site_url('kelas/' . ($r['code'] ?? $r['id'])),
                'edit_url'         => site_url('dashboard/pembelajaran/kelas/' . $r['id'] . '/ubah'),
            ];
        }, $rows);
    }

    private function fetchKnowledgeArticles(string $q = ''): array
    {
        $builder = $this->db->table('knowledge_articles ka')
            ->select('ka.*, fa.path as file_path')
            ->join('file_assets fa', 'fa.id = ka.cover_file_id', 'left')
            ->where('ka.deleted_at', null)
            ->where('ka.workflow_status', 'published')
            ->where('ka.visibility', 'public');

        if ($q !== '') {
            $builder->groupStart()->like('ka.title', $q)->orLike('ka.summary', $q)->groupEnd();
        }

        $rows = $builder
            ->orderBy('ka.carousel_enabled', 'DESC')
            ->orderBy('CASE WHEN ka.carousel_order IS NULL THEN 9999 ELSE ka.carousel_order END', 'ASC', false)
            ->orderBy('ka.featured', 'DESC')
            ->orderBy('ka.published_at', 'DESC')
            ->get()->getResultArray();

        return array_map(function ($r) {
            $typeLabel = match ($r['type'] ?? '') {
                'case_study' => 'Studi Kasus',
                'template'   => 'Template Dokumen',
                default      => 'Artikel PBJ',
            };
            $badgeClass = match ($r['type'] ?? '') {
                'case_study' => 'bg-danger-100 text-danger-600',
                'template'   => 'bg-lilac-100 text-lilac-600',
                default      => 'bg-primary-100 text-primary-600',
            };
            $publicRoute = match ($r['type'] ?? '') {
                'case_study' => site_url('bunga-rampai/studi-kasus/' . ($r['slug'] ?? $r['id'])),
                'template'   => site_url('bunga-rampai/template/' . ($r['slug'] ?? $r['id'])),
                default      => site_url('bunga-rampai/artikel/' . ($r['slug'] ?? $r['id'])),
            };
            $editRoute = match ($r['type'] ?? '') {
                'case_study' => site_url('dashboard/bunga-rampai/studi-kasus/' . $r['id'] . '/ubah'),
                'template'   => site_url('dashboard/bunga-rampai/template-dokumen/' . $r['id'] . '/ubah'),
                default      => site_url('dashboard/bunga-rampai/artikel/' . $r['id'] . '/ubah'),
            };

            return [
                'id'               => (int) $r['id'],
                'feature'          => 'knowledge_article',
                'feature_group'    => 'knowledge',
                'type_label'       => $typeLabel,
                'badge_class'      => $badgeClass,
                'title'            => $r['title'],
                'subtitle'         => 'Tahap: ' . ($r['procurement_stage'] ?: 'Umum') . ' &middot; ' . ($r['reading_time'] ? $r['reading_time'] . ' mnt' : ''),
                'summary'          => $r['summary'] ?: mb_strimwidth(strip_tags((string) $r['content']), 0, 100, '...'),
                'cover_url'        => $this->resolveCover($r['cover_file_id'] ?? null, $r['cover_url'] ?? null, $r['file_path'] ?? null),
                'carousel_enabled' => (int) ($r['carousel_enabled'] ?? 0),
                'is_featured'      => (int) ($r['featured'] ?? 0),
                'carousel_order'   => $r['carousel_order'] ?? null,
                'public_url'       => $publicRoute,
                'edit_url'         => $editRoute,
            ];
        }, $rows);
    }

    private function fetchKnowledgeMedia(string $q = ''): array
    {
        $builder = $this->db->table('knowledge_media km')
            ->select('km.*, fa.path as file_path, ft.path as thumb_path')
            ->join('file_assets fa', 'fa.id = km.file_id', 'left')
            ->join('file_assets ft', 'ft.id = km.thumbnail_file_id', 'left')
            ->where('km.workflow_status', 'published')
            ->where('km.visibility', 'public');

        if ($q !== '') {
            $builder->groupStart()->like('km.title', $q)->orLike('km.caption', $q)->groupEnd();
        }

        $rows = $builder
            ->orderBy('km.carousel_enabled', 'DESC')
            ->orderBy('CASE WHEN km.sort_order IS NULL THEN 9999 ELSE km.sort_order END', 'ASC', false)
            ->orderBy('km.featured', 'DESC')
            ->orderBy('km.published_at', 'DESC')
            ->get()->getResultArray();

        return array_map(function ($r) {
            $typeLabel = match ($r['type'] ?? '') {
                'youtube', 'video_file', 'video' => 'Video Edukasi',
                'gallery'                         => 'Galeri Foto',
                'infographic', 'image'            => 'Infografik PBJ',
                default                           => 'Multimedia PBJ',
            };
            $badgeClass = match ($r['type'] ?? '') {
                'youtube', 'video_file', 'video' => 'bg-danger-100 text-danger-600',
                'gallery'                         => 'bg-warning-100 text-warning-main',
                default                           => 'bg-info-100 text-info-600',
            };

            $coverUrl = null;
            if (! empty($r['cover_url'])) {
                $coverUrl = $r['cover_url'];
            } elseif (! empty($r['thumbnail_file_id'])) {
                $coverUrl = site_url('file/' . (int) $r['thumbnail_file_id']);
            } elseif (! empty($r['file_id'])) {
                $coverUrl = site_url('file/' . (int) $r['file_id']);
            } elseif (! empty($r['external_url']) && preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=))([\w-]{11})/', $r['external_url'], $m)) {
                $coverUrl = "https://img.youtube.com/vi/{$m[1]}/hqdefault.jpg";
            }

            return [
                'id'               => (int) $r['id'],
                'feature'          => 'knowledge_media',
                'feature_group'    => 'knowledge',
                'type_label'       => $typeLabel,
                'badge_class'      => $badgeClass,
                'title'            => $r['title'],
                'subtitle'         => 'Instagram Slide &middot; ' . ($r['location'] ?: 'Lampung'),
                'summary'          => $r['caption'] ?: 'Konten multimedia edukatif interaktif.',
                'cover_url'        => $coverUrl,
                'carousel_enabled' => (int) ($r['carousel_enabled'] ?? 0),
                'is_featured'      => (int) ($r['featured'] ?? 0),
                'carousel_order'   => $r['sort_order'] ?? null,
                'public_url'       => site_url('bunga-rampai/multimedia'),
                'edit_url'         => site_url('dashboard/bunga-rampai/media/' . $r['id'] . '/ubah'),
            ];
        }, $rows);
    }

    private function fetchRegulations(string $q = ''): array
    {
        $builder = $this->db->table('regulations r')
            ->select('r.*, rc.name as category_name')
            ->join('regulation_categories rc', 'rc.id = r.category_id', 'left')
            ->where('r.deleted_at', null)
            ->where('r.workflow_status', 'published');

        if ($q !== '') {
            $builder->groupStart()
                ->like('r.title', $q)
                ->orLike('r.document_number', $q)
                ->orLike('r.summary', $q)
                ->groupEnd();
        }

        $rows = $builder
            ->orderBy('r.carousel_enabled', 'DESC')
            ->orderBy('CASE WHEN r.carousel_order IS NULL THEN 9999 ELSE r.carousel_order END', 'ASC', false)
            ->orderBy('r.is_featured', 'DESC')
            ->orderBy('r.id', 'DESC')
            ->get()->getResultArray();

        return array_map(function ($r) {
            $nomorStr = trim(($r['document_number'] ?? '') . ' ' . (! empty($r['document_year']) ? 'Tahun ' . $r['document_year'] : ''));
            return [
                'id'               => (int) $r['id'],
                'feature'          => 'regulation',
                'feature_group'    => 'regulation',
                'type_label'       => $r['category_name'] ?: 'Regulasi PBJ',
                'badge_class'      => 'bg-neutral-200 text-neutral-800',
                'title'            => $r['title'],
                'subtitle'         => $nomorStr ?: 'Dokumen Regulasi Hukum',
                'summary'          => $r['summary'] ?: 'Dokumen peraturan dan ketentuan pengadaan barang dan jasa.',
                'cover_url'        => $this->resolveCover($r['cover_file_id'] ?? null, null, $r['cover_image_path'] ?? null),
                'carousel_enabled' => (int) ($r['carousel_enabled'] ?? 0),
                'is_featured'      => (int) ($r['is_featured'] ?? 0),
                'carousel_order'   => $r['carousel_order'] ?? null,
                'public_url'       => site_url('regulasi/' . ($r['slug'] ?? $r['id'])),
                'edit_url'         => site_url('dashboard/regulasi/peraturan/' . $r['id'] . '/ubah'),
            ];
        }, $rows);
    }

    private function fetchNews(string $q = ''): array
    {
        $builder = $this->db->table('news n')
            ->select('n.*, nc.name as category_name, fa.path as cover_path')
            ->join('news_categories nc', 'nc.id = n.category_id', 'left')
            ->join('file_assets fa', 'fa.id = n.cover_file_id', 'left')
            ->where('n.deleted_at', null)
            ->where('n.workflow_status', 'published');

        if ($q !== '') {
            $builder->groupStart()->like('n.title', $q)->orLike('n.summary', $q)->groupEnd();
        }

        $rows = $builder
            ->orderBy('n.carousel_enabled', 'DESC')
            ->orderBy('CASE WHEN n.carousel_order IS NULL THEN 9999 ELSE n.carousel_order END', 'ASC', false)
            ->orderBy('n.is_featured', 'DESC')
            ->orderBy('n.published_at', 'DESC')
            ->get()->getResultArray();

        return array_map(function ($r) {
            return [
                'id'               => (int) $r['id'],
                'feature'          => 'news',
                'feature_group'    => 'news',
                'type_label'       => $r['category_name'] ?: 'Warta Berita',
                'badge_class'      => 'bg-info-100 text-info-600',
                'title'            => $r['title'],
                'subtitle'         => ! empty($r['published_at']) ? date('d M Y', strtotime($r['published_at'])) : 'Terbaru',
                'summary'          => $r['summary'] ?: mb_strimwidth(strip_tags((string) $r['content']), 0, 100, '...'),
                'cover_url'        => $this->resolveCover($r['cover_file_id'] ?? null, null, $r['cover_path'] ?? null),
                'carousel_enabled' => (int) ($r['carousel_enabled'] ?? 0),
                'is_featured'      => (int) ($r['is_featured'] ?? 0),
                'carousel_order'   => $r['carousel_order'] ?? null,
                'public_url'       => site_url('berita/' . ($r['slug'] ?? $r['id'])),
                'edit_url'         => site_url('dashboard/komunikasi/berita/' . $r['id'] . '/ubah'),
            ];
        }, $rows);
    }

    private function fetchEvents(string $q = ''): array
    {
        $builder = $this->db->table('events e')
            ->select('e.*, fa.path as cover_path')
            ->join('file_assets fa', 'fa.id = e.cover_file_id', 'left')
            ->where('e.deleted_at', null)
            ->where('e.visibility', 'public');

        if ($q !== '') {
            $builder->groupStart()->like('e.title', $q)->orLike('e.summary', $q)->groupEnd();
        }

        $rows = $builder
            ->orderBy('e.carousel_enabled', 'DESC')
            ->orderBy('CASE WHEN e.carousel_order IS NULL THEN 9999 ELSE e.carousel_order END', 'ASC', false)
            ->orderBy('e.is_featured', 'DESC')
            ->orderBy('e.start_at', 'ASC')
            ->get()->getResultArray();

        return array_map(function ($r) {
            $dateStr = ! empty($r['start_at']) ? date('d M Y, H:i', strtotime($r['start_at'])) : 'Jadwal Segera';
            return [
                'id'               => (int) $r['id'],
                'feature'          => 'event',
                'feature_group'    => 'event',
                'type_label'       => strtoupper($r['type'] ?? 'Webinar') . ' &middot; ' . strtoupper($r['mode'] ?? 'Online'),
                'badge_class'      => 'bg-warning-100 text-warning-main',
                'title'            => $r['title'],
                'subtitle'         => $dateStr . ' &middot; ' . ($r['location'] ?: 'Lampung'),
                'summary'          => $r['summary'] ?: mb_strimwidth(strip_tags((string) $r['description']), 0, 100, '...'),
                'cover_url'        => $this->resolveCover($r['cover_file_id'] ?? null, null, $r['cover_path'] ?? null),
                'carousel_enabled' => (int) ($r['carousel_enabled'] ?? 0),
                'is_featured'      => (int) ($r['is_featured'] ?? 0),
                'carousel_order'   => $r['carousel_order'] ?? null,
                'public_url'       => site_url('event/' . ($r['slug'] ?? $r['id'])),
                'edit_url'         => site_url('dashboard/komunikasi/event-agenda/' . $r['id'] . '/ubah'),
            ];
        }, $rows);
    }

    private function resolveCover(?int $fileId, ?string $coverUrl, ?string $faPath = null): ?string
    {
        if (! empty($fileId)) {
            return site_url('file/' . $fileId);
        }
        if (! empty($coverUrl)) {
            return str_starts_with($coverUrl, 'http') ? $coverUrl : base_url($coverUrl);
        }
        if (! empty($faPath)) {
            return str_starts_with($faPath, 'http') ? $faPath : base_url($faPath);
        }
        return null;
    }

    private function resolveFeatureTable(string $feature, string $field): ?array
    {
        $map = [
            'program'           => ['table' => 'programs', 'carousel' => 'carousel_enabled', 'featured' => 'featured', 'order' => 'carousel_order'],
            'course'            => ['table' => 'courses', 'carousel' => 'carousel_enabled', 'featured' => 'featured', 'order' => 'carousel_order'],
            'class'             => ['table' => 'learning_classes', 'carousel' => 'carousel_enabled', 'featured' => 'featured', 'order' => 'carousel_order'],
            'knowledge_article' => ['table' => 'knowledge_articles', 'carousel' => 'carousel_enabled', 'featured' => 'featured', 'order' => 'carousel_order'],
            'knowledge_media'   => ['table' => 'knowledge_media', 'carousel' => 'carousel_enabled', 'featured' => 'featured', 'order' => 'sort_order'],
            'regulation'        => ['table' => 'regulations', 'carousel' => 'carousel_enabled', 'featured' => 'is_featured', 'order' => 'carousel_order'],
            'news'              => ['table' => 'news', 'carousel' => 'carousel_enabled', 'featured' => 'is_featured', 'order' => 'carousel_order'],
            'event'             => ['table' => 'events', 'carousel' => 'carousel_enabled', 'featured' => 'is_featured', 'order' => 'carousel_order'],
        ];

        if (! isset($map[$feature])) {
            return null;
        }

        $f = $map[$feature];
        $col = match ($field) {
            'carousel_enabled' => $f['carousel'],
            'is_featured', 'featured' => $f['featured'],
            'carousel_order', 'order' => $f['order'],
            default => null,
        };

        if ($col === null) {
            return null;
        }

        return [$f['table'], $col];
    }
}
