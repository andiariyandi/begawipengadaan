<?php

namespace App\Controllers\Admin\Landing;

use App\Controllers\BaseController;
use App\Services\AuditService;
use CodeIgniter\Database\ConnectionInterface;

class HighlightFeatureController extends BaseController
{
    private AuditService $auditService;
    private ConnectionInterface $db;

    public const VALID_SECTIONS = [
        'pilar_begawi' => [
            'name'        => '5 Pilar Ekosistem Begawi',
            'tag'         => 'Arsitektur Layanan',
            'badge'       => 'Pilar Ekosistem',
            'subtitle'    => 'Pilar utama layanan digital pengadaan barang dan jasa Pemprov Lampung',
            'icon'        => 'solar:layers-bold',
            'color'       => '#0c5aa6',
            'default_num' => 5,
        ],
        'budaya_patik' => [
            'name'        => '5 Budaya Kerja PATIK',
            'tag'         => 'Nilai Dasar ASN',
            'badge'       => 'Etos Kerja PBJ',
            'subtitle'    => 'Profesional, Akuntabel, Transparan, Integritas, dan Kredibel',
            'icon'        => 'solar:medal-ribbons-star-bold',
            'color'       => '#0284c7',
            'default_num' => 5,
        ],
        'hierarki_hukum' => [
            'name'        => '4 Tingkat Hierarki Hukum PBJ',
            'tag'         => 'Landasan Regulasi',
            'badge'       => 'Hierarki Hukum',
            'subtitle'    => 'Dari UU Pengadaan, Perpres 16/2018 jo 12/2021, PerLKPP hingga Regulasi Daerah Lampung',
            'icon'        => 'solar:scale-bold',
            'color'       => '#10b981',
            'default_num' => 4,
        ],
        'roadmap_lkpp' => [
            'name'        => '4 Level Roadmap Kompetensi LKPP',
            'tag'         => 'Jenjang Karir PBJ',
            'badge'       => 'Roadmap Standar',
            'subtitle'    => 'Roadmap standar kompetensi pengadaan nasional dari Level 1 (Dasar) s.d. Level 4 (Strategis)',
            'icon'        => 'solar:diploma-verified-bold',
            'color'       => '#f59e0b',
            'default_num' => 4,
        ],
        'keamanan_sertifikat' => [
            'name'        => '4 Pilar Kriptografi & Keamanan Sertifikat',
            'tag'         => 'Integritas Digital',
            'badge'       => 'Keamanan Digital',
            'subtitle'    => 'Fitur perlindungan QR Code dinamis, hash SHA-256, verifikasi publik 24 jam, dan TTE sah',
            'icon'        => 'solar:shield-check-bold',
            'color'       => '#6366f1',
            'default_num' => 4,
        ],
    ];

    public function __construct()
    {
        $this->auditService = new AuditService();
        $this->db           = \Config\Database::connect();
    }

    public function index()
    {
        $section = $this->request->getGet('section') ?: 'pilar_begawi';

        if (! array_key_exists($section, self::VALID_SECTIONS)) {
            $section = 'pilar_begawi';
        }

        // All items in the database to build metrics
        $allHighlights = $this->db->table('feature_highlights')
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();

        // Calculate global & per-section stats
        $totalItems    = count($allHighlights);
        $activeItems   = count(array_filter($allHighlights, fn($h) => (int) ($h['is_active'] ?? 0) === 1));
        $inactiveItems = $totalItems - $activeItems;

        $sectionCounts = [];
        foreach (self::VALID_SECTIONS as $key => $meta) {
            $sectionCounts[$key] = [
                'total'  => 0,
                'active' => 0,
            ];
        }

        foreach ($allHighlights as $h) {
            $sKey = $h['section_key'] ?? '';
            if (isset($sectionCounts[$sKey])) {
                $sectionCounts[$sKey]['total']++;
                if ((int) ($h['is_active'] ?? 0) === 1) {
                    $sectionCounts[$sKey]['active']++;
                }
            }
        }

        // Current section items
        $items = array_values(array_filter($allHighlights, fn($h) => ($h['section_key'] ?? '') === $section));

        $stats = [
            'total_items'    => $totalItems,
            'active_items'   => $activeItems,
            'inactive_items' => $inactiveItems,
            'total_sections' => count(self::VALID_SECTIONS),
            'section_counts' => $sectionCounts,
        ];

        // Legacy format for view compatibility
        $validSections = [];
        foreach (self::VALID_SECTIONS as $k => $v) {
            $validSections[$k] = $v['name'];
        }

        return view('dashboard/landing/highlight/index', [
            'title'          => 'Kelola Pilar & Hierarki | Dashboard BegawiPengadaan',
            'pageTitle'      => 'Pilar, Budaya & Hierarki Regulasi',
            'activeMenu'     => 'landing',
            'activeSubmenu'  => 'pilar-hierarki',
            'items'          => $items,
            'currentSection' => $section,
            'sectionMeta'    => self::VALID_SECTIONS[$section],
            'allSections'    => self::VALID_SECTIONS,
            'validSections'  => $validSections,
            'stats'          => $stats,
        ]);
    }

    public function create()
    {
        $section = $this->request->getGet('section') ?: 'pilar_begawi';

        if (! array_key_exists($section, self::VALID_SECTIONS)) {
            $section = 'pilar_begawi';
        }

        // Determine recommended next sort_order
        $maxOrderRow = $this->db->table('feature_highlights')
            ->selectMax('sort_order', 'max_order')
            ->where('section_key', $section)
            ->get()->getRowArray();
        $nextOrder = ((int) ($maxOrderRow['max_order'] ?? 0)) + 1;

        $validSections = [];
        foreach (self::VALID_SECTIONS as $k => $v) {
            $validSections[$k] = $v['name'];
        }

        return view('dashboard/landing/highlight/form', [
            'title'          => 'Tambah Item Highlight | Dashboard BegawiPengadaan',
            'pageTitle'      => 'Tambah Item Pilar & Hierarki',
            'activeMenu'     => 'landing',
            'activeSubmenu'  => 'pilar-hierarki',
            'item'           => null,
            'currentSection' => $section,
            'nextOrder'      => $nextOrder,
            'sectionMeta'    => self::VALID_SECTIONS[$section],
            'allSections'    => self::VALID_SECTIONS,
            'validSections'  => $validSections,
        ]);
    }

    public function store()
    {
        $rules = [
            'section_key' => 'required',
            'title'       => 'required|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $sectionKey = trim((string) $this->request->getPost('section_key'));
        if (! array_key_exists($sectionKey, self::VALID_SECTIONS)) {
            $sectionKey = 'pilar_begawi';
        }

        $rawOrder = $this->request->getPost('sort_order');
        $sortOrder = ($rawOrder !== null && $rawOrder !== '') ? (int) $rawOrder : 1;

        $colorHex = trim((string) $this->request->getPost('color_hex'));
        if ($colorHex === '') {
            $colorHex = self::VALID_SECTIONS[$sectionKey]['color'] ?? '#0c5aa6';
        }

        $iconClass = trim((string) $this->request->getPost('icon_class'));
        if ($iconClass === '') {
            $iconClass = self::VALID_SECTIONS[$sectionKey]['icon'] ?? 'solar:star-bold';
        }

        $data = [
            'section_key' => $sectionKey,
            'code'        => trim((string) $this->request->getPost('code')),
            'badge_text'  => trim((string) $this->request->getPost('badge_text')),
            'title'       => trim((string) $this->request->getPost('title')),
            'subtitle'    => trim((string) $this->request->getPost('subtitle')),
            'description' => trim((string) $this->request->getPost('description')),
            'icon_class'  => $iconClass,
            'color_hex'   => $colorHex,
            'link_url'    => trim((string) $this->request->getPost('link_url')),
            'sort_order'  => $sortOrder,
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        $this->db->table('feature_highlights')->insert($data);
        $insertId = $this->db->insertID();

        $rawActorId = (int) (session()->get('user_id') ?: 0);
        $actorId    = $rawActorId > 0 ? $rawActorId : null;
        $this->auditService->log($actorId, 'landing.highlight.created', 'feature_highlights', 'id', (int) $insertId, $data);

        return redirect()->to('dashboard/landing/pilar-hierarki?section=' . urlencode($sectionKey))
            ->with('success', 'Item highlight berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $item = $this->db->table('feature_highlights')->where('id', $id)->get()->getRowArray();
        if (! $item) {
            return redirect()->to('dashboard/landing/pilar-hierarki')->with('error', 'Item highlight tidak ditemukan.');
        }

        $section = $item['section_key'];
        if (! array_key_exists($section, self::VALID_SECTIONS)) {
            $section = 'pilar_begawi';
        }

        $validSections = [];
        foreach (self::VALID_SECTIONS as $k => $v) {
            $validSections[$k] = $v['name'];
        }

        return view('dashboard/landing/highlight/form', [
            'title'          => 'Ubah Item Highlight | Dashboard BegawiPengadaan',
            'pageTitle'      => 'Ubah Item Pilar & Hierarki',
            'activeMenu'     => 'landing',
            'activeSubmenu'  => 'pilar-hierarki',
            'item'           => $item,
            'currentSection' => $section,
            'sectionMeta'    => self::VALID_SECTIONS[$section],
            'allSections'    => self::VALID_SECTIONS,
            'validSections'  => $validSections,
        ]);
    }

    public function update($id)
    {
        $item = $this->db->table('feature_highlights')->where('id', $id)->get()->getRowArray();
        if (! $item) {
            return redirect()->to('dashboard/landing/pilar-hierarki')->with('error', 'Item highlight tidak ditemukan.');
        }

        $rules = [
            'title' => 'required|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $rawOrder  = $this->request->getPost('sort_order');
        $sortOrder = ($rawOrder !== null && $rawOrder !== '') ? (int) $rawOrder : (int) ($item['sort_order'] ?? 1);

        $colorHex = trim((string) $this->request->getPost('color_hex'));
        if ($colorHex === '') {
            $colorHex = $item['color_hex'] ?: '#0c5aa6';
        }

        $iconClass = trim((string) $this->request->getPost('icon_class'));
        if ($iconClass === '') {
            $iconClass = $item['icon_class'] ?: 'solar:star-bold';
        }

        $data = [
            'code'        => trim((string) $this->request->getPost('code')),
            'badge_text'  => trim((string) $this->request->getPost('badge_text')),
            'title'       => trim((string) $this->request->getPost('title')),
            'subtitle'    => trim((string) $this->request->getPost('subtitle')),
            'description' => trim((string) $this->request->getPost('description')),
            'icon_class'  => $iconClass,
            'color_hex'   => $colorHex,
            'link_url'    => trim((string) $this->request->getPost('link_url')),
            'sort_order'  => $sortOrder,
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        // Allow moving to a different section if provided
        $sectionKey = trim((string) $this->request->getPost('section_key'));
        if ($sectionKey !== '' && array_key_exists($sectionKey, self::VALID_SECTIONS)) {
            $data['section_key'] = $sectionKey;
        }

        $this->db->table('feature_highlights')->where('id', $id)->update($data);

        $rawActorId = (int) (session()->get('user_id') ?: 0);
        $actorId    = $rawActorId > 0 ? $rawActorId : null;
        $this->auditService->log($actorId, 'landing.highlight.updated', 'feature_highlights', 'id', (int) $id, ['before' => $item, 'after' => $data]);

        $targetSection = $data['section_key'] ?? $item['section_key'];

        return redirect()->to('dashboard/landing/pilar-hierarki?section=' . urlencode($targetSection))
            ->with('success', 'Item highlight berhasil diperbarui.');
    }

    public function duplicate($id)
    {
        $item = $this->db->table('feature_highlights')->where('id', $id)->get()->getRowArray();
        if (! $item) {
            return redirect()->to('dashboard/landing/pilar-hierarki')->with('error', 'Item highlight tidak ditemukan.');
        }

        // Max sort order in current section
        $maxOrderRow = $this->db->table('feature_highlights')
            ->selectMax('sort_order', 'max_order')
            ->where('section_key', $item['section_key'])
            ->get()->getRowArray();
        $nextOrder = ((int) ($maxOrderRow['max_order'] ?? $item['sort_order'])) + 1;

        $newData = [
            'section_key' => $item['section_key'],
            'code'        => $item['code'] ? ($item['code'] . '-COPY') : '',
            'badge_text'  => $item['badge_text'] ? ($item['badge_text'] . ' (Draf)') : 'Draf',
            'title'       => $item['title'] . ' (Salinan)',
            'subtitle'    => $item['subtitle'],
            'description' => $item['description'],
            'icon_class'  => $item['icon_class'],
            'color_hex'   => $item['color_hex'],
            'link_url'    => $item['link_url'],
            'sort_order'  => $nextOrder,
            'is_active'   => 0, // Duplicate starts as inactive draft
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        $this->db->table('feature_highlights')->insert($newData);
        $newId = $this->db->insertID();

        $rawActorId = (int) (session()->get('user_id') ?: 0);
        $actorId    = $rawActorId > 0 ? $rawActorId : null;
        $this->auditService->log($actorId, 'landing.highlight.duplicated', 'feature_highlights', 'id', (int) $newId, $newData);

        return redirect()->to('dashboard/landing/pilar-hierarki?section=' . urlencode($item['section_key']))
            ->with('success', 'Item berhasil diduplikat sebagai draf.');
    }

    public function updateOrder()
    {
        $id       = (int) ($this->request->getPost('id') ?: $this->request->getVar('id') ?: ($_POST['id'] ?? 0));
        $rawOrder = $this->request->getPost('order') ?? $this->request->getVar('order') ?? ($_POST['order'] ?? null);
        $order    = ($rawOrder !== null && $rawOrder !== '') ? (int) $rawOrder : 1;

        if ($id <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID item tidak valid.'])->setStatusCode(400);
        }

        $item = $this->db->table('feature_highlights')->where('id', $id)->get()->getRowArray();
        if (! $item) {
            return $this->response->setJSON(['success' => false, 'message' => 'Item tidak ditemukan.'])->setStatusCode(404);
        }

        $this->db->table('feature_highlights')->where('id', $id)->update([
            'sort_order' => $order,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $rawActorId = (int) (session()->get('user_id') ?: 0);
        $actorId    = $rawActorId > 0 ? $rawActorId : null;
        $this->auditService->log($actorId, 'landing.highlight.reordered', 'feature_highlights', 'sort_order', $id, ['order' => $order]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Urutan item berhasil diperbarui.',
                'order'   => $order,
            ]);
        }

        return redirect()->to('dashboard/landing/pilar-hierarki?section=' . urlencode($item['section_key']))
            ->with('success', 'Urutan item berhasil diperbarui.');
    }

    public function delete($id)
    {
        if ($res = $this->validateSafeMutation()) {
            return $res;
        }

        $item = $this->db->table('feature_highlights')->where('id', $id)->get()->getRowArray();
        if ($item) {
            $this->db->table('feature_highlights')->where('id', $id)->delete();

            $rawActorId = (int) (session()->get('user_id') ?: 0);
            $actorId    = $rawActorId > 0 ? $rawActorId : null;
            $this->auditService->log($actorId, 'landing.highlight.deleted', 'feature_highlights', 'id', (int) $id, ['deleted' => $item]);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Item berhasil dihapus.',
                ]);
            }

            return redirect()->to('dashboard/landing/pilar-hierarki?section=' . urlencode($item['section_key']))
                ->with('success', 'Item berhasil dihapus.');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Item tidak ditemukan.'])->setStatusCode(404);
        }

        return redirect()->to('dashboard/landing/pilar-hierarki')->with('error', 'Item tidak ditemukan.');
    }

    public function toggle($id)
    {
        if ($res = $this->validateSafeMutation()) {
            return $res;
        }

        $item = $this->db->table('feature_highlights')->where('id', $id)->get()->getRowArray();
        if ($item) {
            $newStatus = $item['is_active'] ? 0 : 1;
            $this->db->table('feature_highlights')->where('id', $id)->update([
                'is_active'  => $newStatus,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $rawActorId = (int) (session()->get('user_id') ?: 0);
            $actorId    = $rawActorId > 0 ? $rawActorId : null;
            $this->auditService->log($actorId, 'landing.highlight.toggled', 'feature_highlights', 'is_active', (int) $id, ['new_status' => $newStatus]);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success'   => true,
                    'message'   => 'Status item berhasil diubah.',
                    'is_active' => $newStatus,
                    'id'        => (int) $id,
                ]);
            }

            return redirect()->to('dashboard/landing/pilar-hierarki?section=' . urlencode($item['section_key']))
                ->with('success', 'Status item berhasil diubah.');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Item tidak ditemukan.'])->setStatusCode(404);
        }

        return redirect()->to('dashboard/landing/pilar-hierarki')->with('error', 'Item tidak ditemukan.');
    }

    /**
     * Seed canonical standard recommendations if a section is empty or user requests standard preset.
     */
    public function seedRecommendations($sectionKey)
    {
        if (! array_key_exists($sectionKey, self::VALID_SECTIONS)) {
            return redirect()->to('dashboard/landing/pilar-hierarki')->with('error', 'Kategori tidak valid.');
        }

        $presets = $this->getSectionPresets($sectionKey);
        if (empty($presets)) {
            return redirect()->to('dashboard/landing/pilar-hierarki?section=' . urlencode($sectionKey))
                ->with('error', 'Tidak ada rekomendasi standar untuk kategori ini.');
        }

        $insertedCount = 0;
        foreach ($presets as $p) {
            // Check if title or code already exists in this section
            $exists = $this->db->table('feature_highlights')
                ->where('section_key', $sectionKey)
                ->where('title', $p['title'])
                ->countAllResults();

            if ($exists === 0) {
                $p['created_at'] = date('Y-m-d H:i:s');
                $p['updated_at'] = date('Y-m-d H:i:s');
                $this->db->table('feature_highlights')->insert($p);
                $insertedCount++;
            }
        }

        $rawActorId = (int) (session()->get('user_id') ?: 0);
        $actorId    = $rawActorId > 0 ? $rawActorId : null;
        $this->auditService->log($actorId, 'landing.highlight.seeded_presets', 'feature_highlights', 'section_key', null, [
            'section' => $sectionKey,
            'count'   => $insertedCount,
        ]);

        return redirect()->to('dashboard/landing/pilar-hierarki?section=' . urlencode($sectionKey))
            ->with('success', "Berhasil menambahkan {$insertedCount} item rekomendasi standar untuk kategori " . self::VALID_SECTIONS[$sectionKey]['name'] . ".");
    }

    /**
     * Canonical standard preset definitions for PBJ Lampung ecosystem.
     */
    private function getSectionPresets(string $section): array
    {
        $allPresets = [
            'pilar_begawi' => [
                [
                    'section_key' => 'pilar_begawi',
                    'code'        => '01',
                    'badge_text'  => 'Pilar 01',
                    'title'       => 'Arena Belajar LMS',
                    'subtitle'    => 'Silabus LKPP',
                    'description' => 'Platform diklat PBJ interaktif dengan modul berjenjang, studi kasus nyata, dan kuis uji pemahaman kompetensi aparatur.',
                    'icon_class'  => 'solar:square-academic-cap-bold',
                    'color_hex'   => '#0c5aa6',
                    'link_url'    => 'arena-belajar',
                    'sort_order'  => 1,
                    'is_active'   => 1,
                ],
                [
                    'section_key' => 'pilar_begawi',
                    'code'        => '02',
                    'badge_text'  => 'Pilar 02',
                    'title'       => 'Bunga Rampai Dokumen',
                    'subtitle'    => 'Template & Praktik Baik',
                    'description' => 'Repositori template dokumen pengadaan, telaah hukum, artikel teknis, dan panduan mitigasi risiko.',
                    'icon_class'  => 'solar:folder-with-files-bold',
                    'color_hex'   => '#0284c7',
                    'link_url'    => 'bunga-rampai',
                    'sort_order'  => 2,
                    'is_active'   => 1,
                ],
                [
                    'section_key' => 'pilar_begawi',
                    'code'        => '03',
                    'badge_text'  => 'Pilar 03',
                    'title'       => 'Pusat Regulasi PBJ',
                    'subtitle'    => 'Hierarki Hukum',
                    'description' => 'Pangkalan data regulasi PBJ terlengkap dari UU, Perpres 16/2018 jo 12/2021, PerLKPP hingga Pergub Lampung.',
                    'icon_class'  => 'solar:document-text-bold',
                    'color_hex'   => '#10b981',
                    'link_url'    => 'regulasi',
                    'sort_order'  => 3,
                    'is_active'   => 1,
                ],
                [
                    'section_key' => 'pilar_begawi',
                    'code'        => '04',
                    'badge_text'  => 'Pilar 04',
                    'title'       => 'Klinik Konsultasi PBJ',
                    'subtitle'    => 'Bantuan Interaktif',
                    'description' => 'Layanan pendampingan dan konsultasi langsung bersama fungsional PBJ berpengalaman secara profesional.',
                    'icon_class'  => 'solar:chat-round-dots-bold',
                    'color_hex'   => '#f59e0b',
                    'link_url'    => 'kontak',
                    'sort_order'  => 4,
                    'is_active'   => 1,
                ],
                [
                    'section_key' => 'pilar_begawi',
                    'code'        => '05',
                    'badge_text'  => 'Pilar 05',
                    'title'       => 'Verifikasi Sertifikat',
                    'subtitle'    => 'Integritas QR Code',
                    'description' => 'Validasi keaslian sertifikat diklat berstandar kriptografi SHA-256 untuk memastikan keabsahan kelulusan.',
                    'icon_class'  => 'solar:verified-check-bold',
                    'color_hex'   => '#6366f1',
                    'link_url'    => 'verifikasi-sertifikat',
                    'sort_order'  => 5,
                    'is_active'   => 1,
                ],
            ],
            'budaya_patik' => [
                [
                    'section_key' => 'budaya_patik',
                    'code'        => 'P',
                    'badge_text'  => 'P',
                    'title'       => 'Profesional',
                    'subtitle'    => 'Kompetensi Tinggi',
                    'description' => 'Menjalankan tugas berlandaskan kompetensi, standar operasional, dan kode etik aparatur pengadaan.',
                    'icon_class'  => 'solar:star-bold',
                    'color_hex'   => '#0c5aa6',
                    'link_url'    => '',
                    'sort_order'  => 1,
                    'is_active'   => 1,
                ],
                [
                    'section_key' => 'budaya_patik',
                    'code'        => 'A',
                    'badge_text'  => 'A',
                    'title'       => 'Akuntabel',
                    'subtitle'    => 'Dapat Dipertanggungjawabkan',
                    'description' => 'Setiap keputusan pengadaan dapat diuji, didokumentasikan, dan dipertanggungjawabkan kepada publik.',
                    'icon_class'  => 'solar:shield-check-bold',
                    'color_hex'   => '#0284c7',
                    'link_url'    => '',
                    'sort_order'  => 2,
                    'is_active'   => 1,
                ],
                [
                    'section_key' => 'budaya_patik',
                    'code'        => 'T',
                    'badge_text'  => 'T',
                    'title'       => 'Transparan',
                    'subtitle'    => 'Keterbukaan Informasi',
                    'description' => 'Keterbukaan informasi proses dan hasil pengadaan bagi seluruh pelaku usaha dan masyarakat luas.',
                    'icon_class'  => 'solar:eye-bold',
                    'color_hex'   => '#10b981',
                    'link_url'    => '',
                    'sort_order'  => 3,
                    'is_active'   => 1,
                ],
                [
                    'section_key' => 'budaya_patik',
                    'code'        => 'I',
                    'badge_text'  => 'I',
                    'title'       => 'Integritas',
                    'subtitle'    => 'Bebas Gratifikasi',
                    'description' => 'Menolak segala bentuk gratifikasi, benturan kepentingan, dan praktik suap demi zona integritas bersih.',
                    'icon_class'  => 'solar:lock-keyhole-bold',
                    'color_hex'   => '#f59e0b',
                    'link_url'    => '',
                    'sort_order'  => 4,
                    'is_active'   => 1,
                ],
                [
                    'section_key' => 'budaya_patik',
                    'code'        => 'K',
                    'badge_text'  => 'K',
                    'title'       => 'Kredibel',
                    'subtitle'    => 'Terpercaya & Sahih',
                    'description' => 'Menjaga kepercayaan publik melalui proses pengadaan yang adil, objektif, dan memberikan nilai manfaat terbaik.',
                    'icon_class'  => 'solar:medal-ribbons-star-bold',
                    'color_hex'   => '#ef4444',
                    'link_url'    => '',
                    'sort_order'  => 5,
                    'is_active'   => 1,
                ],
            ],
            'hierarki_hukum' => [
                [
                    'section_key' => 'hierarki_hukum',
                    'code'        => 'UU',
                    'badge_text'  => 'Tingkat I',
                    'title'       => 'Undang-Undang & Perppu',
                    'subtitle'    => 'Landasan Pokok',
                    'description' => 'UU No. 1 Tahun 2004 (Perbendaharaan Negara) dan UU Cipta Kerja Klaster Pengadaan & Kemudahan Usaha.',
                    'icon_class'  => 'solar:diploma-verified-bold',
                    'color_hex'   => '#0c5aa6',
                    'link_url'    => 'regulasi?kategori=uu',
                    'sort_order'  => 1,
                    'is_active'   => 1,
                ],
                [
                    'section_key' => 'hierarki_hukum',
                    'code'        => 'PERPRES',
                    'badge_text'  => 'Tingkat II',
                    'title'       => 'Peraturan Presiden (Perpres)',
                    'subtitle'    => 'Payung Operasional',
                    'description' => 'Perpres No. 16 Tahun 2018 jo. Perpres No. 12 Tahun 2021 tentang Pengadaan Barang/Jasa Pemerintah.',
                    'icon_class'  => 'solar:document-bold',
                    'color_hex'   => '#0284c7',
                    'link_url'    => 'regulasi?kategori=perpres',
                    'sort_order'  => 2,
                    'is_active'   => 1,
                ],
                [
                    'section_key' => 'hierarki_hukum',
                    'code'        => 'PERLKPP',
                    'badge_text'  => 'Tingkat III',
                    'title'       => 'Peraturan LKPP & SE Kepala',
                    'subtitle'    => 'Pedoman Teknis',
                    'description' => 'Peraturan LKPP No. 9 s.d. 12 Tahun 2021 dan Surat Edaran LKPP terkait e-purchasing serta mitigasi kontrak.',
                    'icon_class'  => 'solar:book-bookmark-bold',
                    'color_hex'   => '#10b981',
                    'link_url'    => 'regulasi?kategori=perlkpp',
                    'sort_order'  => 3,
                    'is_active'   => 1,
                ],
                [
                    'section_key' => 'hierarki_hukum',
                    'code'        => 'PERGUB',
                    'badge_text'  => 'Tingkat IV',
                    'title'       => 'Regulasi Daerah Provinsi Lampung',
                    'subtitle'    => 'Instruksi & Kepgub',
                    'description' => 'Pergub dan Keputusan Gubernur Lampung tentang Tata Kelola Pengadaan dan P3DN UMKM Lokal Lampung.',
                    'icon_class'  => 'solar:city-bold',
                    'color_hex'   => '#f59e0b',
                    'link_url'    => 'regulasi?kategori=pergub',
                    'sort_order'  => 4,
                    'is_active'   => 1,
                ],
            ],
            'roadmap_lkpp' => [
                [
                    'section_key' => 'roadmap_lkpp',
                    'code'        => 'L1',
                    'badge_text'  => 'Level 1',
                    'title'       => 'Kompetensi Dasar PBJ',
                    'subtitle'    => 'Fondasi Pengadaan',
                    'description' => 'Pemahaman prinsip dasar, etika, perencanaan, pemilihan penyedia, dan swakelola pengadaan pemerintah.',
                    'icon_class'  => 'solar:medal-star-bold',
                    'color_hex'   => '#0c5aa6',
                    'link_url'    => 'arena-belajar',
                    'sort_order'  => 1,
                    'is_active'   => 1,
                ],
                [
                    'section_key' => 'roadmap_lkpp',
                    'code'        => 'L2',
                    'badge_text'  => 'Level 2',
                    'title'       => 'Kompetensi Teknis Operasional',
                    'subtitle'    => 'Pokja & Pejabat Pengadaan',
                    'description' => 'Penyusunan KAK/Spesifikasi, evaluasi penawaran e-tendering, negosiasi teknis, dan manajemen risiko awal.',
                    'icon_class'  => 'solar:wrench-bold',
                    'color_hex'   => '#0284c7',
                    'link_url'    => 'arena-belajar',
                    'sort_order'  => 2,
                    'is_active'   => 1,
                ],
                [
                    'section_key' => 'roadmap_lkpp',
                    'code'        => 'L3',
                    'badge_text'  => 'Level 3',
                    'title'       => 'Kompetensi Manajerial Kontrak',
                    'subtitle'    => 'Pejabat Pembuat Komitmen (PPK)',
                    'description' => 'Pengendalian kontrak kritis, perubahan kontrak (adendum), serah terima pekerjaan, dan mitigasi dispute.',
                    'icon_class'  => 'solar:shield-check-bold',
                    'color_hex'   => '#10b981',
                    'link_url'    => 'arena-belajar',
                    'sort_order'  => 3,
                    'is_active'   => 1,
                ],
                [
                    'section_key' => 'roadmap_lkpp',
                    'code'        => 'L4',
                    'badge_text'  => 'Level 4',
                    'title'       => 'Kompetensi Strategis & Advokasi',
                    'subtitle'    => 'PA / KPA & Konsultan Ahli',
                    'description' => 'Penyusunan kebijakan pengadaan strategis, pembelaan hukum/advokasi sanggahan, dan tata kelola UKPBJ mandiri.',
                    'icon_class'  => 'solar:crown-line-duotone',
                    'color_hex'   => '#f59e0b',
                    'link_url'    => 'arena-belajar',
                    'sort_order'  => 4,
                    'is_active'   => 1,
                ],
            ],
            'keamanan_sertifikat' => [
                [
                    'section_key' => 'keamanan_sertifikat',
                    'code'        => 'QR',
                    'badge_text'  => 'Kriptografi',
                    'title'       => 'Kode QR Dinamis',
                    'subtitle'    => 'Pencegahan Manipulasi',
                    'description' => 'Tiap sertifikat memuat tautan verifikasi unik yang langsung memeriksa integritas data di basis data resmi.',
                    'icon_class'  => 'solar:qr-code-bold',
                    'color_hex'   => '#0c5aa6',
                    'link_url'    => 'verifikasi-sertifikat',
                    'sort_order'  => 1,
                    'is_active'   => 1,
                ],
                [
                    'section_key' => 'keamanan_sertifikat',
                    'code'        => 'SHA',
                    'badge_text'  => 'Integritas',
                    'title'       => 'Hash Digital SHA-256',
                    'subtitle'    => 'Kunci Validitas',
                    'description' => 'Tanda tangan digital dan hash algoritma SHA-256 memastikan berkas sertifikat tidak dapat diubah.',
                    'icon_class'  => 'solar:lock-keyhole-bold',
                    'color_hex'   => '#10b981',
                    'link_url'    => 'verifikasi-sertifikat',
                    'sort_order'  => 2,
                    'is_active'   => 1,
                ],
                [
                    'section_key' => 'keamanan_sertifikat',
                    'code'        => 'PUB',
                    'badge_text'  => 'Publik',
                    'title'       => 'Verifikasi Publik Real-time',
                    'subtitle'    => 'Akses Transparan 24 Jam',
                    'description' => 'Instansi, auditor, dan publik dapat memeriksa masa berlaku sertifikat tanpa perlu login.',
                    'icon_class'  => 'solar:eye-bold',
                    'color_hex'   => '#0284c7',
                    'link_url'    => 'verifikasi-sertifikat',
                    'sort_order'  => 3,
                    'is_active'   => 1,
                ],
                [
                    'section_key' => 'keamanan_sertifikat',
                    'code'        => 'TTE',
                    'badge_text'  => 'Otoritas',
                    'title'       => 'Tanda Tangan Elektronik Sah',
                    'subtitle'    => 'Biro PBJ Setda Lampung',
                    'description' => 'Diterbitkan secara sah oleh Unit Kerja Pengadaan Barang/Jasa Provinsi Lampung dengan registrasi resmi.',
                    'icon_class'  => 'solar:diploma-verified-bold',
                    'color_hex'   => '#f59e0b',
                    'link_url'    => 'verifikasi-sertifikat',
                    'sort_order'  => 4,
                    'is_active'   => 1,
                ],
            ],
        ];

        return $allPresets[$section] ?? [];
    }
}
