<?php

namespace App\Controllers\Landing;

use App\Controllers\BaseController;
use App\Models\Learning\CourseModel;
use App\Models\Learning\LearningClassModel;
use App\Models\Learning\ProgramModel;

class ArenaBelajarController extends BaseController
{
    protected $helpers = ['text', 'url', 'form'];

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        if ($this->request->getMethod() === 'GET' && ! $this->request->isAJAX() && ! is_cli()) {
            try {
                (new \App\Services\VisitorStatisticService())->recordHit('arena_belajar');
            } catch (\Throwable) {
            }
        }
    }

    public function index(): string
    {
        $programModel = new ProgramModel();
        $courseModel  = new CourseModel();
        $classModel   = new LearningClassModel();
        $db           = db_connect();

        // 1. Live platform metrics
        $stats = [
            'total_programs' => (int) $programModel->where('status', 'published')->where('visibility', 'public')->countAllResults(),
            'total_courses'  => (int) $courseModel->where('status', 'published')->where('visibility', 'public')->countAllResults(),
            'total_classes'  => (int) $classModel->whereIn('status', ['scheduled', 'active', 'open', 'published', 'ongoing'])->countAllResults(),
            'total_trainers' => (int) $db->table('class_trainers ct')
                ->join('users u', 'u.id = ct.user_id')
                ->where('ct.status', 'active')
                ->where('u.account_status', 'active')
                ->where('u.deleted_at IS NULL', null, false)
                ->select('COUNT(DISTINCT ct.user_id) AS cnt')
                ->get()->getRowArray()['cnt'] ?? 0,
        ];

        // 2. Featured Programs for Showcase (prioritize featured badge)
        $featuredPrograms = $programModel
            ->where('status', 'published')
            ->where('visibility', 'public')
            ->orderBy('featured', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll(4);

        foreach ($featuredPrograms as &$prog) {
            $prog['course_count'] = (int) $courseModel
                ->where('program_id', (int) $prog['id'])
                ->where('status', 'published')
                ->where('visibility', 'public')
                ->countAllResults();
        }
        unset($prog);

        // 3. Featured & Top Courses for Showcase (prioritize featured badge)
        $featuredCourses = $courseModel
            ->select('courses.*, programs.title AS program_title, programs.slug AS program_slug')
            ->join('programs', 'programs.id = courses.program_id')
            ->where('courses.status', 'published')
            ->where('courses.visibility', 'public')
            ->where('programs.status', 'published')
            ->where('programs.visibility', 'public')
            ->orderBy('courses.featured', 'DESC')
            ->orderBy('courses.id', 'DESC')
            ->findAll(6);

        $popularCourses = $featuredCourses;

        // 4. Active & Scheduled Classes Ready to Enroll for Showcase (prioritize featured badge)
        $activeClasses = $classModel
            ->select('learning_classes.*, courses.title AS course_title, courses.slug AS course_slug, courses.cover_url AS course_cover_url, programs.title AS program_title')
            ->join('courses', 'courses.id = learning_classes.course_id')
            ->join('programs', 'programs.id = courses.program_id', 'left')
            ->where('courses.deleted_at IS NULL', null, false)
            ->where('(programs.deleted_at IS NULL OR programs.id IS NULL)', null, false)
            ->whereIn('learning_classes.status', ['scheduled', 'active', 'open', 'published', 'ongoing'])
            ->where('courses.status', 'published')
            ->where('courses.visibility', 'public')
            ->orderBy('learning_classes.featured', 'DESC')
            ->orderBy('learning_classes.start_at', 'ASC')
            ->findAll(6);

        foreach ($activeClasses as &$cl) {
            $cl['trainers'] = $db->table('class_trainers ct')
                ->select('users.name, users.username, ct.role')
                ->join('users', 'users.id = ct.user_id')
                ->where('ct.class_id', (int) $cl['id'])
                ->where('ct.status', 'active')
                ->where('users.deleted_at IS NULL', null, false)
                ->orderBy('users.name', 'ASC')
                ->get()->getResultArray();
            $cl['enrolled_count'] = (int) $db->table('enrollments')
                ->where('class_id', (int) $cl['id'])
                ->whereIn('status', ['active', 'completed'])
                ->countAllResults();
        }
        unset($cl);

        // 5. Featured Trainers / Instructors (limit 4)
        $trainersQuery = $db->table('class_trainers ct')
            ->select('users.id, users.name, users.username, users.profile_type, COUNT(ct.class_id) as total_classes')
            ->join('users', 'users.id = ct.user_id')
            ->join('learning_classes lc', 'lc.id = ct.class_id')
            ->where('ct.status', 'active')
            ->where('users.account_status', 'active')
            ->where('users.deleted_at IS NULL', null, false)
            ->where('lc.deleted_at IS NULL', null, false)
            ->whereIn('lc.status', ['scheduled', 'active', 'open', 'published', 'ongoing'])
            ->groupBy('users.id, users.name, users.username, users.profile_type')
            ->orderBy('total_classes', 'DESC')
            ->orderBy('users.name', 'ASC')
            ->limit(4)
            ->get()->getResultArray();

        $spotlightMode  = (string) setting('learning.spotlight_mode', 'all');
        $spotlightLimit = max(3, min(15, (int) setting('learning.spotlight_limit', 6)));
        $sliderItems    = $this->getCarouselSlides($spotlightMode, $spotlightLimit);

        return view('landing/pages/learning', [
            'title'            => 'Arena Belajar | BegawiPengadaan',
            'metaDescription'  => 'Pusat pembelajaran, program terstruktur, course mandiri, kelas aktif, dan pengajar Pengadaan Barang/Jasa Pemerintah Provinsi Lampung.',
            'metaKeywords'     => 'Arena Belajar, pembelajaran PBJ, pelatihan pengadaan, sertifikasi PBJ, BegawiPengadaan',
            'stats'            => $stats,
            'sliderItems'      => $sliderItems,
            'featuredPrograms' => $featuredPrograms,
            'featuredCourses'  => $featuredCourses,
            'popularCourses'   => $popularCourses,
            'activeClasses'    => $activeClasses,
            'featuredTrainers' => $trainersQuery,
            'spotlightMode'    => $spotlightMode,
            'spotlightLimit'   => $spotlightLimit,
        ]);
    }

    /**
     * Build unified Hero Carousel items for Arena Belajar adhering to Priority Rules:
     * 1. Manual active sliders (carousel_enabled = 1) prioritized first.
     * 2. Remaining quota filled with latest items based on mode without duplicate items.
     */
    private function getCarouselSlides(string $mode = 'all', int $limit = 6): array
    {
        $db = db_connect();
        $bgSliderCovers = [
            base_url('assets/main/images/course/course-details-img-1.jpg'),
            base_url('assets/main/images/course/course-list-img-1.jpg'),
            base_url('assets/main/images/course/course-list-img-2.jpg'),
            base_url('assets/main/images/course/course-list-img-3.jpg'),
        ];

        $formatProgSlide = function (array $fProg, int $idx) use ($bgSliderCovers): array {
            $cover = ! empty($fProg['cover_url'])
                ? (str_starts_with($fProg['cover_url'], 'http') ? $fProg['cover_url'] : base_url($fProg['cover_url']))
                : $bgSliderCovers[$idx % count($bgSliderCovers)];
            return [
                'category'       => 'program',
                'id'             => (int) $fProg['id'],
                'title'          => $fProg['title'],
                'url'            => route_to('public.learning.program.detail', $fProg['slug']),
                'cover_url'      => $cover,
                'tag_class'      => 'evt-badge-flag--featured',
                'status_tag'     => '<iconify-icon icon="solar:star-bold" class="align-middle"></iconify-icon> Kurikulum Unggulan',
                'type_label'     => 'Program Kurikulum',
                'badge_extra'    => 'Standar LKPP RI',
                'meta_1'         => '<iconify-icon icon="solar:layers-minimalistic-bold" class="text-warning align-middle"></iconify-icon> ' . (int) ($fProg['course_count'] ?? 0) . ' Course Terpadu',
                'meta_2'         => '<iconify-icon icon="solar:shield-check-bold" class="text-info align-middle"></iconify-icon> Jalur Kompetensi',
                'meta_3'         => '<iconify-icon icon="solar:diploma-verified-bold" class="text-success align-middle"></iconify-icon> Terhubung Uji Kompetensi',
                'action_text'    => 'Eksplorasi Kurikulum',
                'carousel_order' => $fProg['carousel_order'] ?? null,
            ];
        };

        $formatCourseSlide = function (array $fCourse, int $idx) use ($bgSliderCovers): array {
            $cover = ! empty($fCourse['cover_url'])
                ? (str_starts_with($fCourse['cover_url'], 'http') ? $fCourse['cover_url'] : base_url($fCourse['cover_url']))
                : $bgSliderCovers[($idx + 1) % count($bgSliderCovers)];
            $isFeat = (int) ($fCourse['featured'] ?? 0) === 1;
            return [
                'category'       => 'course',
                'id'             => (int) $fCourse['id'],
                'title'          => $fCourse['title'],
                'url'            => route_to('public.learning.course.detail', $fCourse['slug']),
                'cover_url'      => $cover,
                'tag_class'      => $isFeat ? 'evt-badge-flag--course' : 'evt-badge-flag--coming',
                'status_tag'     => '<iconify-icon icon="solar:book-bookmark-bold" class="align-middle"></iconify-icon> ' . ($isFeat ? 'Course Pilihan' : 'Course Mandiri'),
                'type_label'     => 'Course PBJ',
                'badge_extra'    => ucfirst($fCourse['level'] ?? 'Dasar') . ' Level',
                'meta_1'         => '<iconify-icon icon="solar:clock-circle-bold" class="text-warning align-middle"></iconify-icon> ' . format_learning_jp($fCourse['duration_minutes'] ?? null),
                'meta_2'         => '<iconify-icon icon="solar:folder-with-files-bold" class="text-info align-middle"></iconify-icon> ' . esc($fCourse['program_title'] ?? 'Kurikulum PBJ'),
                'meta_3'         => '<iconify-icon icon="solar:laptop-minimalistic-bold" class="text-success align-middle"></iconify-icon> Pembelajaran Digital LMS',
                'action_text'    => 'Pelajari Course Ini',
                'carousel_order' => $fCourse['carousel_order'] ?? null,
            ];
        };

        $formatClassSlide = function (array $aCls, int $idx) use ($bgSliderCovers): array {
            $effectiveClassCover = ! empty($aCls['cover_url']) ? $aCls['cover_url'] : ($aCls['course_cover_url'] ?? '');
            $cover = ! empty($effectiveClassCover)
                ? (str_starts_with($effectiveClassCover, 'http') ? $effectiveClassCover : base_url($effectiveClassCover))
                : $bgSliderCovers[($idx + 2) % count($bgSliderCovers)];
            return [
                'category'       => 'class',
                'id'             => (int) $aCls['id'],
                'title'          => $aCls['name'],
                'url'            => route_to('public.learning.class.detail', $aCls['code']),
                'cover_url'      => $cover,
                'tag_class'      => 'evt-badge-flag--open',
                'status_tag'     => '<span class="dot"></span> ' . ($aCls['status'] === 'active' ? 'Sedang Berjalan' : 'Pendaftaran Dibuka'),
                'type_label'     => 'Batch Kelas Terbuka',
                'badge_extra'    => strtoupper($aCls['learning_method'] ?? 'Online') . ' Pelatihan',
                'meta_1'         => '<iconify-icon icon="solar:calendar-date-bold" class="text-warning align-middle"></iconify-icon> Mulai: ' . (! empty($aCls['start_at']) ? date('d M Y', strtotime($aCls['start_at'])) : 'Segera'),
                'meta_2'         => '<iconify-icon icon="solar:notebook-bold" class="text-info align-middle"></iconify-icon> ' . esc($aCls['course_title'] ?? 'Course PBJ'),
                'meta_3'         => '<iconify-icon icon="solar:users-group-rounded-bold" class="text-success align-middle"></iconify-icon> ' . ((int) ($aCls['capacity'] ?? 0) > 0 ? 'Kuota ' . (int) $aCls['capacity'] . ' Peserta' : 'Kuota Fleksibel'),
                'action_text'    => 'Detail &amp; Daftar Kelas',
                'carousel_order' => $aCls['carousel_order'] ?? null,
            ];
        };

        // 1. Fetch ALL manual active slider items (carousel_enabled = 1)
        $manualProgRaw = $db->table('programs')
            ->where('deleted_at', null)
            ->where('status', 'published')
            ->where('visibility', 'public')
            ->where('carousel_enabled', 1)
            ->orderBy('CASE WHEN carousel_order IS NULL THEN 9999 ELSE carousel_order END', 'ASC', false)
            ->orderBy('id', 'DESC')
            ->get()->getResultArray();

        foreach ($manualProgRaw as &$p) {
            $p['course_count'] = (int) $db->table('courses')->where('program_id', (int) $p['id'])->where('status', 'published')->where('visibility', 'public')->where('deleted_at', null)->countAllResults();
        }
        unset($p);

        $manualCourseRaw = $db->table('courses c')
            ->select('c.*, p.title AS program_title')
            ->join('programs p', 'p.id = c.program_id', 'left')
            ->where('c.deleted_at', null)
            ->where('c.status', 'published')
            ->where('c.visibility', 'public')
            ->where('c.carousel_enabled', 1)
            ->orderBy('CASE WHEN c.carousel_order IS NULL THEN 9999 ELSE c.carousel_order END', 'ASC', false)
            ->orderBy('c.id', 'DESC')
            ->get()->getResultArray();

        $manualClassRaw = $db->table('learning_classes lc')
            ->select('lc.*, c.title AS course_title, c.cover_url AS course_cover_url')
            ->join('courses c', 'c.id = lc.course_id', 'left')
            ->where('lc.deleted_at', null)
            ->whereIn('lc.status', ['scheduled', 'active', 'open', 'published', 'ongoing'])
            ->where('lc.carousel_enabled', 1)
            ->orderBy('CASE WHEN lc.carousel_order IS NULL THEN 9999 ELSE lc.carousel_order END', 'ASC', false)
            ->orderBy('lc.id', 'DESC')
            ->get()->getResultArray();

        $manualSlides = [];
        if ($mode === 'program') {
            foreach ($manualProgRaw as $idx => $p) {
                $manualSlides[] = $formatProgSlide($p, $idx);
            }
        } elseif ($mode === 'course') {
            foreach ($manualCourseRaw as $idx => $c) {
                $manualSlides[] = $formatCourseSlide($c, $idx);
            }
        } elseif ($mode === 'class') {
            foreach ($manualClassRaw as $idx => $cl) {
                $manualSlides[] = $formatClassSlide($cl, $idx);
            }
        } else {
            // mode 'all': combine all manual active items and sort by carousel_order, id DESC
            $tempManual = [];
            foreach ($manualProgRaw as $idx => $p) {
                $tempManual[] = $formatProgSlide($p, $idx);
            }
            foreach ($manualCourseRaw as $idx => $c) {
                $tempManual[] = $formatCourseSlide($c, $idx);
            }
            foreach ($manualClassRaw as $idx => $cl) {
                $tempManual[] = $formatClassSlide($cl, $idx);
            }
            usort($tempManual, function ($a, $b) {
                $oa = $a['carousel_order'] !== null ? (int) $a['carousel_order'] : 9999;
                $ob = $b['carousel_order'] !== null ? (int) $b['carousel_order'] : 9999;
                if ($oa !== $ob) {
                    return $oa <=> $ob;
                }
                return (int) $b['id'] <=> (int) $a['id'];
            });
            $manualSlides = $tempManual;
        }

        // Limit manual slides up to $limit
        $finalSlides = array_slice($manualSlides, 0, $limit);

        // 2. If quota not yet filled, fill remaining with newest items according to mode
        if (count($finalSlides) < $limit) {
            $remaining = $limit - count($finalSlides);
            $usedProgIds   = array_column(array_filter($finalSlides, fn($s) => $s['category'] === 'program'), 'id');
            $usedCourseIds = array_column(array_filter($finalSlides, fn($s) => $s['category'] === 'course'), 'id');
            $usedClassIds  = array_column(array_filter($finalSlides, fn($s) => $s['category'] === 'class'), 'id');

            if ($mode === 'program') {
                $pQuery = $db->table('programs')
                    ->where('deleted_at', null)
                    ->where('status', 'published')
                    ->where('visibility', 'public');
                if (! empty($usedProgIds)) {
                    $pQuery->whereNotIn('id', $usedProgIds);
                }
                $fillerProg = $pQuery->orderBy('id', 'DESC')->limit($remaining)->get()->getResultArray();
                foreach ($fillerProg as $idx => $p) {
                    $p['course_count'] = (int) $db->table('courses')->where('program_id', (int) $p['id'])->where('status', 'published')->where('visibility', 'public')->where('deleted_at', null)->countAllResults();
                    $finalSlides[] = $formatProgSlide($p, $idx);
                }
            } elseif ($mode === 'course') {
                $cQuery = $db->table('courses c')
                    ->select('c.*, p.title AS program_title')
                    ->join('programs p', 'p.id = c.program_id', 'left')
                    ->where('c.deleted_at', null)
                    ->where('c.status', 'published')
                    ->where('c.visibility', 'public');
                if (! empty($usedCourseIds)) {
                    $cQuery->whereNotIn('c.id', $usedCourseIds);
                }
                $fillerCourses = $cQuery->orderBy('c.id', 'DESC')->limit($remaining)->get()->getResultArray();
                foreach ($fillerCourses as $idx => $c) {
                    $finalSlides[] = $formatCourseSlide($c, $idx);
                }
            } elseif ($mode === 'class') {
                $clQuery = $db->table('learning_classes lc')
                    ->select('lc.*, c.title AS course_title, c.cover_url AS course_cover_url')
                    ->join('courses c', 'c.id = lc.course_id', 'left')
                    ->where('lc.deleted_at', null)
                    ->whereIn('lc.status', ['scheduled', 'active', 'open', 'published', 'ongoing']);
                if (! empty($usedClassIds)) {
                    $clQuery->whereNotIn('lc.id', $usedClassIds);
                }
                $fillerClasses = $clQuery->orderBy('lc.id', 'DESC')->limit($remaining)->get()->getResultArray();
                foreach ($fillerClasses as $idx => $cl) {
                    $finalSlides[] = $formatClassSlide($cl, $idx);
                }
            } else {
                // mode 'all': fetch newest programs, courses, and classes, then interleave
                $pQuery = $db->table('programs')
                    ->where('deleted_at', null)
                    ->where('status', 'published')
                    ->where('visibility', 'public');
                if (! empty($usedProgIds)) {
                    $pQuery->whereNotIn('id', $usedProgIds);
                }
                $fillerProgRaw = $pQuery->orderBy('id', 'DESC')->limit($remaining)->get()->getResultArray();
                $fillerProgSlides = [];
                foreach ($fillerProgRaw as $idx => $p) {
                    $p['course_count'] = (int) $db->table('courses')->where('program_id', (int) $p['id'])->where('status', 'published')->where('visibility', 'public')->where('deleted_at', null)->countAllResults();
                    $fillerProgSlides[] = $formatProgSlide($p, $idx);
                }

                $cQuery = $db->table('courses c')
                    ->select('c.*, p.title AS program_title')
                    ->join('programs p', 'p.id = c.program_id', 'left')
                    ->where('c.deleted_at', null)
                    ->where('c.status', 'published')
                    ->where('c.visibility', 'public');
                if (! empty($usedCourseIds)) {
                    $cQuery->whereNotIn('c.id', $usedCourseIds);
                }
                $fillerCourseRaw = $cQuery->orderBy('c.id', 'DESC')->limit($remaining)->get()->getResultArray();
                $fillerCourseSlides = [];
                foreach ($fillerCourseRaw as $idx => $c) {
                    $fillerCourseSlides[] = $formatCourseSlide($c, $idx);
                }

                $clQuery = $db->table('learning_classes lc')
                    ->select('lc.*, c.title AS course_title, c.cover_url AS course_cover_url')
                    ->join('courses c', 'c.id = lc.course_id', 'left')
                    ->where('lc.deleted_at', null)
                    ->whereIn('lc.status', ['scheduled', 'active', 'open', 'published', 'ongoing']);
                if (! empty($usedClassIds)) {
                    $clQuery->whereNotIn('lc.id', $usedClassIds);
                }
                $fillerClassRaw = $clQuery->orderBy('lc.id', 'DESC')->limit($remaining)->get()->getResultArray();
                $fillerClassSlides = [];
                foreach ($fillerClassRaw as $idx => $cl) {
                    $fillerClassSlides[] = $formatClassSlide($cl, $idx);
                }

                $maxF = max(count($fillerProgSlides), count($fillerCourseSlides), count($fillerClassSlides));
                for ($i = 0; $i < $maxF && count($finalSlides) < $limit; $i++) {
                    if (isset($fillerProgSlides[$i]) && count($finalSlides) < $limit) {
                        $finalSlides[] = $fillerProgSlides[$i];
                    }
                    if (isset($fillerCourseSlides[$i]) && count($finalSlides) < $limit) {
                        $finalSlides[] = $fillerCourseSlides[$i];
                    }
                    if (isset($fillerClassSlides[$i]) && count($finalSlides) < $limit) {
                        $finalSlides[] = $fillerClassSlides[$i];
                    }
                }
            }
        }

        return $finalSlides;
    }

    public function programs(): string
    {
        $model = new ProgramModel();
        $request = $this->request ?? service('request');
        $q = trim((string) $request->getGet('q'));
        $featuredFilter = (string) $request->getGet('featured');

        $builder = $model
            ->where('status', 'published')
            ->where('visibility', 'public');

        if ($q !== '') {
            $builder->groupStart()
                ->like('title', $q)
                ->orLike('summary', $q)
                ->orLike('description', $q)
                ->groupEnd();
        }

        if ($featuredFilter === '1') {
            $builder->where('featured', 1);
        }

        $items = $builder
            ->orderBy('featured', 'DESC')
            ->orderBy('title', 'ASC')
            ->paginate(12, 'default');

        $db          = \Config\Database::connect();
        $courseModel = new CourseModel();
        foreach ($items as &$item) {
            $item['course_count'] = (int) $courseModel
                ->where('program_id', (int) $item['id'])
                ->where('status', 'published')
                ->where('visibility', 'public')
                ->countAllResults();

            // Active or scheduled classes connected to courses under this program
            $item['class_count'] = (int) $db->table('learning_classes lc')
                ->join('courses c', 'c.id = lc.course_id')
                ->where('c.program_id', (int) $item['id'])
                ->where('lc.deleted_at IS NULL', null, false)
                ->where('c.deleted_at IS NULL', null, false)
                ->whereIn('lc.status', ['scheduled', 'active', 'open', 'published', 'ongoing'])
                ->countAllResults();

            // Total participants enrolled in classes of this program
            $item['enrolled_count'] = (int) $db->table('enrollments e')
                ->join('learning_classes lc', 'lc.id = e.class_id')
                ->join('courses c', 'c.id = lc.course_id')
                ->where('c.program_id', (int) $item['id'])
                ->where('lc.deleted_at IS NULL', null, false)
                ->where('c.deleted_at IS NULL', null, false)
                ->whereIn('e.status', ['active', 'completed'])
                ->countAllResults();

            // Preview sample courses (up to 3)
            $item['sample_courses'] = $db->table('courses')
                ->select('id, title, slug, level')
                ->where('program_id', (int) $item['id'])
                ->where('deleted_at IS NULL', null, false)
                ->where('status', 'published')
                ->where('visibility', 'public')
                ->orderBy('id', 'ASC')
                ->limit(3)
                ->get()->getResultArray();

            $item['text'] = $item['summary'] ?? '';
            $item['icon'] = 'why-choose-one-icon-1.png';
        }
        unset($item);

        return $this->catalog(
            'Program Pembelajaran',
            'programs',
            'Program kurikulum terstruktur untuk membangun kompetensi PBJ berjenjang dari dasar hingga lanjutan.',
            $items,
            ['q' => $q, 'featuredFilter' => $featuredFilter]
        );
    }

    public function courses(): string
    {
        $model = new CourseModel();
        $request = $this->request ?? service('request');
        $q = trim((string) $request->getGet('q'));
        $programSlug = trim((string) $request->getGet('program'));
        $level = trim((string) $request->getGet('level'));
        $perPage = (int) ($request->getGet('per_page') ?: 9);
        if ($perPage < 1 || $perPage > 24) {
            $perPage = 9;
        }

        $builder = $model
            ->select('courses.*, programs.title AS program_title, programs.slug AS program_slug')
            ->join('programs', 'programs.id = courses.program_id')
            ->where('courses.status', 'published')
            ->where('courses.visibility', 'public')
            ->where('programs.status', 'published')
            ->where('programs.visibility', 'public');

        if ($q !== '') {
            $builder->groupStart()
                ->like('courses.title', $q)
                ->orLike('courses.summary', $q)
                ->orLike('courses.description', $q)
                ->orLike('programs.title', $q)
                ->groupEnd();
        }

        if ($programSlug !== '') {
            $builder->where('programs.slug', $programSlug);
        }

        if ($level !== '') {
            $builder->where('courses.level', $level);
        }

        $items = $builder
            ->orderBy('courses.title', 'ASC')
            ->paginate($perPage, 'courses');

        $pager = $model->pager;

        $db = \Config\Database::connect();
        foreach ($items as &$item) {
            // Count active or scheduled classes
            $item['class_count'] = (int) $db->table('learning_classes')
                ->where('course_id', (int) $item['id'])
                ->whereIn('status', ['scheduled', 'active'])
                ->countAllResults();

            // Total participants enrolled
            $item['enrolled_count'] = (int) $db->table('enrollments e')
                ->join('learning_classes lc', 'lc.id = e.class_id')
                ->where('lc.course_id', (int) $item['id'])
                ->whereIn('e.status', ['active', 'completed'])
                ->countAllResults();

            $item['text'] = $item['summary'] ?? '';
            $item['icon'] = 'why-choose-one-icon-1.png';
        }
        unset($item);

        $availablePrograms = (new ProgramModel())
            ->where('status', 'published')
            ->where('visibility', 'public')
            ->orderBy('title', 'ASC')
            ->findAll();

        return $this->catalog(
            'Course PBJ',
            'courses',
            'Materi mandiri dan kurikulum topik khusus regulasi, perencanaan, pemilihan, kontrak, dan evaluasi PBJ.',
            $items,
            [
                'q'               => $q,
                'selectedProgram' => $programSlug,
                'selectedLevel'   => $level,
                'programsList'    => $availablePrograms,
                'pager'           => $pager,
            ]
        );
    }

    public function classes(): string
    {
        $model = new LearningClassModel();
        $request = $this->request ?? service('request');
        $q = trim((string) $request->getGet('q'));
        $status = trim((string) $request->getGet('status'));
        $courseSlug = trim((string) $request->getGet('course'));
        $perPage = (int) ($request->getGet('per_page') ?: 8);
        if ($perPage < 1 || $perPage > 24) {
            $perPage = 8;
        }

        $builder = $model
            ->select('learning_classes.*, courses.title AS course_title, courses.slug AS course_slug, courses.level AS course_level, courses.duration_minutes AS course_duration, courses.cover_url AS course_cover_url, programs.title AS program_title, programs.slug AS program_slug')
            ->join('courses', 'courses.id = learning_classes.course_id')
            ->join('programs', 'programs.id = courses.program_id', 'left')
            ->where('courses.deleted_at IS NULL', null, false)
            ->where('(programs.deleted_at IS NULL OR programs.id IS NULL)', null, false)
            ->whereIn('learning_classes.status', ['scheduled', 'active', 'open', 'published', 'ongoing'])
            ->where('courses.status', 'published')
            ->where('courses.visibility', 'public');

        if ($q !== '') {
            $builder->groupStart()
                ->like('learning_classes.name', $q)
                ->orLike('learning_classes.code', $q)
                ->orLike('courses.title', $q)
                ->orLike('programs.title', $q)
                ->groupEnd();
        }

        if ($status !== '' && in_array($status, ['scheduled', 'active', 'open', 'published', 'ongoing'], true)) {
            if (in_array($status, ['active', 'ongoing'], true)) {
                $builder->whereIn('learning_classes.status', ['active', 'ongoing']);
            } else {
                $builder->whereIn('learning_classes.status', ['scheduled', 'open', 'published']);
            }
        }

        if ($courseSlug !== '') {
            $builder->where('courses.slug', $courseSlug);
        }

        $items = $builder
            ->orderBy('learning_classes.start_at', 'ASC')
            ->paginate($perPage, 'classes');

        $pager = $model->pager;

        $db = db_connect();
        foreach ($items as &$item) {
            $item['slug']  = $item['code'];
            $item['title'] = $item['name'];
            $item['text']  = $item['course_title'] ?? 'Kelas PBJ';
            $item['icon']  = 'why-choose-one-icon-1.png';

            $item['trainers'] = $db->table('class_trainers ct')
                ->select('users.id, users.name, users.username, users.profile_type, ct.role')
                ->join('users', 'users.id = ct.user_id')
                ->where('ct.class_id', (int) $item['id'])
                ->where('ct.status', 'active')
                ->orderBy('users.name', 'ASC')
                ->get()->getResultArray();

            $item['enrolled_count'] = (int) $db->table('enrollments')
                ->where('class_id', (int) $item['id'])
                ->whereIn('status', ['active', 'completed'])
                ->countAllResults();

            $item['remaining_quota'] = max(0, (int) ($item['capacity'] ?? 0) - $item['enrolled_count']);

            $item['modules_count'] = (int) $db->table('learning_modules')
                ->where('class_id', (int) $item['id'])
                ->where('status', 'published')
                ->countAllResults();
        }
        unset($item);

        $coursesList = (new CourseModel())
            ->where('status', 'published')
            ->where('visibility', 'public')
            ->orderBy('title', 'ASC')
            ->findAll();

        return $this->catalog(
            'Kelas & Batch PBJ',
            'classes',
            'Daftar kelas pembelajaran aktif, jadwal batch pelatihan, dan ruang diskusi bersama pengajar bersertifikat.',
            $items,
            [
                'q'              => $q,
                'selectedStatus' => $status,
                'selectedCourse' => $courseSlug,
                'coursesList'    => $coursesList,
                'pager'          => $pager,
            ]
        );
    }

    public function trainers(): string
    {
        $request = $this->request ?? service('request');
        $q       = trim((string) $request->getGet('q'));
        $profile = trim((string) $request->getGet('profile'));
        $page    = max(1, (int) ($request->getGet('page_trainers') ?: $request->getGet('page') ?: 1));
        $perPage = 8;

        $db = db_connect();
        $builder = $db->table('class_trainers')
            ->select('users.id, users.name, users.username, users.profile_type, users.email, class_trainers.role AS trainer_role, learning_classes.id AS class_id, learning_classes.name AS class_name, learning_classes.code AS class_code, learning_classes.status AS class_status, learning_classes.learning_method, learning_classes.location_or_link, courses.id AS course_id, courses.title AS course_title, courses.slug AS course_slug, courses.cover_url AS course_cover_url')
            ->join('users', 'users.id = class_trainers.user_id')
            ->join('learning_classes', 'learning_classes.id = class_trainers.class_id')
            ->join('courses', 'courses.id = learning_classes.course_id')
            ->where('class_trainers.status', 'active')
            ->where('users.account_status', 'active')
            ->where('users.deleted_at IS NULL', null, false)
            ->where('learning_classes.deleted_at IS NULL', null, false)
            ->where('courses.deleted_at IS NULL', null, false)
            ->whereIn('learning_classes.status', ['scheduled', 'active', 'open', 'published', 'ongoing'])
            ->where('courses.status', 'published')
            ->where('courses.visibility', 'public');

        if ($q !== '') {
            $builder->groupStart()
                ->like('users.name', $q)
                ->orLike('users.username', $q)
                ->orLike('users.profile_type', $q)
                ->orLike('learning_classes.name', $q)
                ->orLike('courses.title', $q)
                ->groupEnd();
        }

        if ($profile !== '') {
            $builder->where('users.profile_type', $profile);
        }

        $rows = $builder->orderBy('users.name', 'ASC')->get()->getResultArray();
        $items = [];
        foreach ($rows as $row) {
            $key = (string) $row['id'];
            if (! isset($items[$key])) {
                $items[$key] = [
                    'id'             => $row['id'],
                    'title'          => $row['name'] ?: $row['username'],
                    'name'           => $row['name'] ?: $row['username'],
                    'username'       => $row['username'],
                    'slug'           => $row['username'] ?: (string) $row['id'],
                    'text'           => $row['profile_type'] ?: 'Pengajar PBJ',
                    'profile_type'   => $row['profile_type'],
                    'classes'        => [],
                    'courses'        => [],
                    'total_classes'  => 0,
                    'total_courses'  => 0,
                    'total_students' => 0,
                    'icon'           => 'why-choose-one-icon-1.png',
                ];
            }
            $items[$key]['classes'][] = [
                'id'              => $row['class_id'],
                'name'            => $row['class_name'],
                'code'            => $row['class_code'],
                'status'          => $row['class_status'],
                'learning_method' => $row['learning_method'] ?? 'online',
                'location_or_link' => $row['location_or_link'] ?? null,
                'role'            => $row['trainer_role'] ?? 'primary',
            ];
            $cKey = (string) $row['course_id'];
            if (! isset($items[$key]['courses'][$cKey])) {
                $items[$key]['courses'][$cKey] = [
                    'title' => $row['course_title'],
                    'slug'  => $row['course_slug'],
                ];
            }
        }

        // Count students and finalize metrics
        foreach ($items as &$t) {
            $t['total_classes'] = count($t['classes']);
            $t['total_courses'] = count($t['courses']);
            $t['courses']       = array_values($t['courses']);
            $classIds = array_column($t['classes'], 'id');
            if ($classIds !== []) {
                $t['total_students'] = (int) $db->table('enrollments')
                    ->whereIn('class_id', $classIds)
                    ->whereIn('status', ['active', 'completed'])
                    ->countAllResults();
            }
        }
        unset($t);

        $totalTrainers = count($items);
        $offset        = ($page - 1) * $perPage;
        $pagedItems    = array_slice(array_values($items), $offset, $perPage);

        $pager = service('pager');
        $pager->makeLinks($page, $perPage, $totalTrainers, 'default_full', 0, 'trainers');

        return $this->catalog(
            'Instruktur & Pengajar PBJ',
            'trainers',
            'Pengajar, widyaiswara, dan praktisi pengadaan yang memandu kelas serta berbagi praktik terbaik PBJ.',
            $pagedItems,
            [
                'q'               => $q,
                'selectedProfile' => $profile,
                'totalTrainers'   => $totalTrainers,
                'pager'           => $pager,
            ]
        );
    }

    public function programDetail(string $slug): string
    {
        $program = (new ProgramModel())
            ->where('slug', $slug)
            ->where('status', 'published')
            ->where('visibility', 'public')
            ->first();
        if ($program === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Program tidak ditemukan.');
        }

        $request = $this->request ?? service('request');
        $courseSearch = trim((string) $request->getGet('q_course'));
        $perPage = (int) ($request->getGet('per_page') ?: 3);
        if ($perPage < 1 || $perPage > 24) {
            $perPage = 3;
        }

        $courseModel = new CourseModel();
        $courseBuilder = $courseModel
            ->where('program_id', (int) $program['id'])
            ->where('status', 'published')
            ->where('visibility', 'public');

        if ($courseSearch !== '') {
            $courseBuilder->groupStart()
                ->like('title', $courseSearch)
                ->orLike('summary', $courseSearch)
                ->orLike('description', $courseSearch)
                ->groupEnd();
        }

        // Paginate courses for this program
        $courses = $courseBuilder
            ->orderBy('id', 'ASC')
            ->paginate($perPage, 'courses');

        $coursePager = $courseModel->pager;

        $totalCourses = (int) (new CourseModel())
            ->where('program_id', (int) $program['id'])
            ->where('status', 'published')
            ->where('visibility', 'public')
            ->countAllResults();

        $db = \Config\Database::connect();
        $classes = $db->table('learning_classes lc')
            ->select('lc.*, c.title AS course_title, c.slug AS course_slug, c.cover_url AS course_cover_url')
            ->join('courses c', 'c.id = lc.course_id')
            ->where('c.program_id', (int) $program['id'])
            ->where('lc.deleted_at IS NULL', null, false)
            ->where('c.deleted_at IS NULL', null, false)
            ->whereIn('lc.status', ['scheduled', 'active', 'open', 'published', 'ongoing'])
            ->orderBy('lc.start_at', 'ASC')
            ->get()->getResultArray();

        $enrolledCount = (int) $db->table('enrollments e')
            ->join('learning_classes lc', 'lc.id = e.class_id')
            ->join('courses c', 'c.id = lc.course_id')
            ->where('c.program_id', (int) $program['id'])
            ->where('lc.deleted_at IS NULL', null, false)
            ->where('c.deleted_at IS NULL', null, false)
            ->whereIn('e.status', ['active', 'completed'])
            ->countAllResults();

        // Trainers assigned to classes under this program
        $trainers = $db->table('class_trainers ct')
            ->select('u.id, u.name, u.username, u.profile_type, ct.role, COUNT(DISTINCT ct.class_id) as total_classes')
            ->join('users u', 'u.id = ct.user_id')
            ->join('learning_classes lc', 'lc.id = ct.class_id')
            ->join('courses c', 'c.id = lc.course_id')
            ->where('c.program_id', (int) $program['id'])
            ->where('ct.status', 'active')
            ->where('u.account_status', 'active')
            ->where('u.deleted_at IS NULL', null, false)
            ->where('lc.deleted_at IS NULL', null, false)
            ->where('c.deleted_at IS NULL', null, false)
            ->groupBy('u.id, u.name, u.username, u.profile_type, ct.role')
            ->orderBy('total_classes', 'DESC')
            ->limit(4)
            ->get()->getResultArray();

        $breadcrumb = [
            ['label' => 'Arena Belajar', 'url' => site_url('arena-belajar')],
            ['label' => 'Program Pembelajaran', 'url' => site_url('arena-belajar/program')],
            ['label' => $program['title']],
        ];

        return $this->detail('Detail Program Pembelajaran', $program['title'], 'programs', $program, [
            'courses'        => $courses,
            'coursePager'    => $coursePager,
            'totalCourses'   => $totalCourses,
            'courseSearch'   => $courseSearch,
            'classes'        => $classes,
            'enrolled_count' => $enrolledCount,
            'trainers'       => $trainers,
            'breadcrumb'     => $breadcrumb,
        ]);
    }

    public function courseDetail(string $slug): string
    {
        $course = (new CourseModel())
            ->select('courses.*, programs.title AS program_title, programs.slug AS program_slug')
            ->join('programs', 'programs.id = courses.program_id')
            ->where('courses.slug', $slug)
            ->where('courses.status', 'published')
            ->where('courses.visibility', 'public')
            ->where('programs.status', 'published')
            ->where('programs.visibility', 'public')
            ->first();
        if ($course === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Course tidak ditemukan.');
        }

        $request = $this->request ?? service('request');
        $classSearch = trim((string) $request->getGet('q_class'));
        $perPage = (int) ($request->getGet('per_page') ?: 4);
        if ($perPage < 1 || $perPage > 24) {
            $perPage = 4;
        }

        $classModel = new LearningClassModel();
        $classBuilder = $classModel
            ->select('learning_classes.*, courses.title AS course_title, courses.slug AS course_slug, courses.cover_url AS course_cover_url')
            ->join('courses', 'courses.id = learning_classes.course_id')
            ->where('learning_classes.course_id', (int) $course['id'])
            ->whereIn('learning_classes.status', ['scheduled', 'active', 'open', 'published', 'ongoing'])
            ->where('courses.status', 'published');

        if ($classSearch !== '') {
            $classBuilder->groupStart()
                ->like('learning_classes.name', $classSearch)
                ->orLike('learning_classes.code', $classSearch)
                ->groupEnd();
        }

        $classes = $classBuilder
            ->orderBy('learning_classes.start_at', 'ASC')
            ->paginate($perPage, 'classes');

        $classPager = $classModel->pager;

        $totalClasses = (int) (new LearningClassModel())
            ->where('course_id', (int) $course['id'])
            ->whereIn('status', ['scheduled', 'active', 'open', 'published', 'ongoing'])
            ->countAllResults();

        $db = \Config\Database::connect();
        foreach ($classes as &$class) {
            $class['slug']  = $class['code'];
            $class['title'] = $class['name'];

            $class['trainers'] = $db->table('class_trainers ct')
                ->select('users.id, users.name, users.username, users.profile_type, ct.role')
                ->join('users', 'users.id = ct.user_id')
                ->where('ct.class_id', (int) $class['id'])
                ->where('ct.status', 'active')
                ->orderBy('users.name', 'ASC')
                ->get()->getResultArray();

            $class['enrolled_count'] = (int) $db->table('enrollments')
                ->where('class_id', (int) $class['id'])
                ->whereIn('status', ['active', 'completed'])
                ->countAllResults();

            $class['remaining_quota'] = max(0, (int) ($class['capacity'] ?? 0) - $class['enrolled_count']);
        }
        unset($class);

        // Total enrolled participants across this course
        $enrolledCount = (int) $db->table('enrollments e')
            ->join('learning_classes lc', 'lc.id = e.class_id')
            ->where('lc.course_id', (int) $course['id'])
            ->whereIn('e.status', ['active', 'completed'])
            ->countAllResults();

        // Trainers teaching classes under this course
        $trainers = $db->table('class_trainers ct')
            ->select('u.id, u.name, u.username, u.profile_type, ct.role, COUNT(DISTINCT ct.class_id) as total_classes')
            ->join('users u', 'u.id = ct.user_id')
            ->join('learning_classes lc', 'lc.id = ct.class_id')
            ->where('lc.course_id', (int) $course['id'])
            ->where('ct.status', 'active')
            ->where('u.account_status', 'active')
            ->where('u.deleted_at IS NULL', null, false)
            ->groupBy('u.id, u.name, u.username, u.profile_type, ct.role')
            ->orderBy('total_classes', 'DESC')
            ->limit(4)
            ->get()->getResultArray();

        // Modules preview if available
        $modules = $db->table('learning_modules lm')
            ->select('lm.id, lm.title, lm.description as summary, lm.sort_order')
            ->join('learning_classes lc', 'lc.id = lm.class_id')
            ->where('lc.course_id', (int) $course['id'])
            ->where('lm.status', 'published')
            ->groupBy('lm.id, lm.title, lm.description, lm.sort_order')
            ->orderBy('lm.sort_order', 'ASC')
            ->limit(8)
            ->get()->getResultArray();

        // Other courses under same program
        $relatedCourses = (new CourseModel())
            ->select('courses.*, programs.title AS program_title')
            ->join('programs', 'programs.id = courses.program_id')
            ->where('courses.program_id', (int) $course['program_id'])
            ->where('courses.id !=', (int) $course['id'])
            ->where('courses.status', 'published')
            ->where('courses.visibility', 'public')
            ->orderBy('courses.id', 'ASC')
            ->limit(3)
            ->findAll();

        $course['jp_formatted'] = format_learning_jp($course['duration_minutes'] ?? null);
        $course['jp_count']     = learning_jp_count($course['duration_minutes'] ?? null);

        $breadcrumb = [
            ['label' => 'Arena Belajar', 'url' => site_url('arena-belajar')],
            ['label' => 'Course PBJ', 'url' => site_url('arena-belajar/course')],
            ['label' => $course['program_title'] ?? 'Program', 'url' => ! empty($course['program_slug']) ? route_to('public.learning.program.detail', $course['program_slug']) : site_url('arena-belajar/program')],
            ['label' => $course['title']],
        ];

        return $this->detail('Detail Course PBJ', $course['title'], 'courses', $course, [
            'classes'        => $classes,
            'classPager'     => $classPager,
            'totalClasses'   => $totalClasses,
            'classSearch'    => $classSearch,
            'enrolled_count' => $enrolledCount,
            'trainers'       => $trainers,
            'modules'        => $modules,
            'relatedCourses' => $relatedCourses,
            'breadcrumb'     => $breadcrumb,
        ]);
    }

    public function classDetail(string $slug): string
    {
        $item = (new LearningClassModel())
            ->select('learning_classes.*, courses.title AS course_title, courses.slug AS course_slug, courses.level AS course_level, courses.duration_minutes AS course_duration, courses.cover_url AS course_cover_url, programs.title AS program_title, programs.slug AS program_slug')
            ->join('courses', 'courses.id = learning_classes.course_id')
            ->join('programs', 'programs.id = courses.program_id', 'left')
            ->where('learning_classes.code', $slug)
            ->whereIn('learning_classes.status', ['scheduled', 'active', 'open', 'published', 'ongoing'])
            ->where('courses.status', 'published')
            ->where('courses.visibility', 'public')
            ->first();
        if ($item === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kelas tidak ditemukan.');
        }
        $item['title'] = $item['name'];
        $item['slug']  = $item['code'];

        $parsedBatch = parse_class_batch_data($item['description'] ?? '');
        $item['description']             = $parsedBatch['description'];
        $item['batch_stages']            = $parsedBatch['stages'];
        $item['batch_stages_subtitle']   = $parsedBatch['stages_subtitle'];
        $durationMinutes                 = $parsedBatch['custom_duration_minutes'] ?? $item['course_duration'] ?? 180;
        $item['duration_minutes']        = $durationMinutes;
        $item['jp_formatted']            = format_learning_jp($durationMinutes);
        $item['jp_count']                = learning_jp_count($durationMinutes);

        $db = \Config\Database::connect();
        $trainers = $db->table('class_trainers ct')
            ->select('users.id, users.name, users.username, users.profile_type, users.email, ct.role')
            ->join('users', 'users.id = ct.user_id')
            ->where('ct.class_id', (int) $item['id'])
            ->where('ct.status', 'active')
            ->orderBy('users.name', 'ASC')
            ->get()->getResultArray();

        $enrolledCount = (int) $db->table('enrollments')
            ->where('class_id', (int) $item['id'])
            ->whereIn('status', ['active', 'completed'])
            ->countAllResults();

        $isLoggedIn = session()->get('isLoggedIn') === true;
        $userId     = (int) session()->get('user_id');
        $enrollmentStatus = null;
        if ($isLoggedIn && $userId > 0) {
            $enrollmentStatus = $this->lookupEnrollmentStatus((int) $item['id'], $userId);
        }
        $item['enrollment_status'] = $enrollmentStatus;
        $item['trainers']          = $trainers;
        $item['enrolled_count']    = $enrolledCount;
        $item['remaining_quota']   = max(0, (int) ($item['capacity'] ?? 0) - $enrolledCount);

        // Modules for this class
        $modules = $db->table('learning_modules lm')
            ->select('lm.*')
            ->where('lm.class_id', (int) $item['id'])
            ->where('lm.status', 'published')
            ->orderBy('lm.sort_order', 'ASC')
            ->get()->getResultArray();

        foreach ($modules as &$mod) {
            $mod['lesson_count'] = (int) $db->table('lessons')
                ->where('module_id', (int) $mod['id'])
                ->where('status', 'published')
                ->countAllResults();
            $mod['quiz_count'] = (int) $db->table('quizzes')
                ->join('lessons', 'lessons.id = quizzes.lesson_id')
                ->where('lessons.module_id', (int) $mod['id'])
                ->where('quizzes.status', 'published')
                ->countAllResults();
        }
        unset($mod);

        // Other batch classes for the same course
        $otherClasses = (new LearningClassModel())
            ->select('learning_classes.*, courses.title AS course_title, courses.slug AS course_slug, courses.cover_url AS course_cover_url')
            ->join('courses', 'courses.id = learning_classes.course_id')
            ->where('learning_classes.course_id', (int) $item['course_id'])
            ->where('learning_classes.id !=', (int) $item['id'])
            ->whereIn('learning_classes.status', ['scheduled', 'active', 'open', 'published', 'ongoing'])
            ->orderBy('learning_classes.start_at', 'ASC')
            ->limit(3)
            ->findAll();

        $breadcrumb = [
            ['label' => 'Arena Belajar', 'url' => site_url('arena-belajar')],
            ['label' => 'Program', 'url' => site_url('arena-belajar/program')],
            ['label' => $item['program_title'] ?? 'Program', 'url' => ! empty($item['program_slug']) ? route_to('public.learning.program.detail', $item['program_slug']) : site_url('arena-belajar/program')],
            ['label' => 'Course', 'url' => ! empty($item['course_slug']) ? route_to('public.learning.course.detail', $item['course_slug']) : site_url('arena-belajar/course')],
            ['label' => $item['course_title'] ?? 'Course', 'url' => ! empty($item['course_slug']) ? route_to('public.learning.course.detail', $item['course_slug']) : site_url('arena-belajar/course')],
            ['label' => $item['name']],
        ];

        return $this->detail('Detail Kelas PBJ', $item['name'], 'classes', $item, [
            'modules'             => $modules,
            'otherClasses'        => $otherClasses,
            'breadcrumb'          => $breadcrumb,
            'batchStages'         => $parsedBatch['stages'],
            'batchStagesSubtitle' => $parsedBatch['stages_subtitle'],
        ]);
    }

    public function enroll(string $slug)
    {
        if (session()->get('isLoggedIn') !== true) {
            return redirect()->to(route_to('dashboard.auth.login'))
                ->with('error', 'Silakan masuk terlebih dahulu untuk mendaftar kelas.');
        }
        $userId = (int) session()->get('user_id');
        if ($userId < 1) {
            return redirect()->to(route_to('dashboard.auth.login'))
                ->with('error', 'Sesi tidak valid, silakan masuk ulang.');
        }
        $user = db_connect()->table('users')
            ->select('id, account_status, deleted_at')
            ->where('id', $userId)
            ->get()->getRowArray();
        if (! $user || ($user['account_status'] ?? null) !== 'active' || ! empty($user['deleted_at'])) {
            return redirect()->to(route_to('dashboard.auth.login'))
                ->with('error', 'Akun Anda belum aktif atau tidak dapat digunakan untuk enrollment.');
        }
        $class = (new LearningClassModel())
            ->where('code', $slug)
            ->first();
        if ($class === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kelas tidak ditemukan.');
        }
        if (! in_array($class['status'] ?? null, ['scheduled', 'active', 'open', 'published', 'ongoing'], true)) {
            return redirect()->to(route_to('public.learning.class.detail', $slug))
                ->with('error', 'Kelas tidak dapat diikuti pada status saat ini.');
        }
        $now = time();
        if (! empty($class['registration_open_at']) && strtotime((string) $class['registration_open_at']) > $now) {
            return redirect()->to(route_to('public.learning.class.detail', $slug))
                ->with('error', 'Pendaftaran untuk kelas ini belum dibuka.');
        }
        if (! empty($class['registration_close_at']) && strtotime((string) $class['registration_close_at']) < $now) {
            return redirect()->to(route_to('public.learning.class.detail', $slug))
                ->with('error', 'Pendaftaran untuk kelas ini sudah ditutup.');
        }
        if ((new \App\Models\EnrollmentModel())->alreadyEnrolled($userId, (int) $class['id'])) {
            return redirect()->to(route_to('public.learning.class.detail', $slug))
                ->with('error', 'Anda sudah terdaftar pada kelas ini.');
        }
        try {
            (new \App\Services\LearningVerticalSliceService())->enroll($userId, (int) $class['id']);
        } catch (\RuntimeException $e) {
            return redirect()->to(route_to('public.learning.class.detail', $slug))
                ->with('error', $e->getMessage());
        }
        return redirect()->to(route_to('public.learning.class.detail', $slug))
            ->with('success', 'Pengajuan enrollment berhasil. Menunggu persetujuan administrator.');
    }

    private function lookupEnrollmentStatus(int $classId, int $userId): ?string
    {
        $row = db_connect()->table('enrollments')
            ->select('status')
            ->where(['class_id' => $classId, 'user_id' => $userId])
            ->orderBy('id', 'DESC')
            ->get()->getRowArray();
        return $row['status'] ?? null;
    }

    public function trainerDetail(string $slug): string
    {
        $db = db_connect();
        $user = $db->table('users')
            ->select('users.id, users.name, users.username, users.email, users.phone, users.profile_type, users.account_status, users.created_at')
            ->where('users.account_status', 'active')
            ->where('users.deleted_at IS NULL', null, false)
            ->groupStart()
            ->where('users.username', $slug)
            ->orWhere('users.id', is_numeric($slug) ? (int) $slug : 0)
            ->groupEnd()
            ->get()
            ->getRowArray();

        if (! $user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Pengajar tidak ditemukan.');
        }

        // Fetch detailed classes taught by this trainer
        $classes = $db->table('class_trainers ct')
            ->select('lc.*, c.title AS course_title, c.slug AS course_slug, c.level AS course_level, c.duration_minutes AS course_duration, c.cover_url AS course_cover_url, p.title AS program_title, p.slug AS program_slug, ct.role')
            ->join('learning_classes lc', 'lc.id = ct.class_id')
            ->join('courses c', 'c.id = lc.course_id')
            ->join('programs p', 'p.id = c.program_id', 'left')
            ->where('ct.user_id', (int) $user['id'])
            ->where('ct.status', 'active')
            ->whereIn('lc.status', ['scheduled', 'active', 'completed'])
            ->where('c.status', 'published')
            ->where('c.visibility', 'public')
            ->orderBy('lc.start_at', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($classes as &$cls) {
            $enrolledCount = (int) $db->table('enrollments')
                ->where('class_id', (int) $cls['id'])
                ->whereIn('status', ['active', 'completed'])
                ->countAllResults();
            $cls['enrolled_count']  = $enrolledCount;
            $cls['remaining_quota'] = max(0, (int) ($cls['capacity'] ?? 0) - $enrolledCount);
        }
        unset($cls);

        // Group courses taught
        $coursesTaught = [];
        foreach ($classes as $cls) {
            $cKey = (string) $cls['course_id'];
            if (! isset($coursesTaught[$cKey])) {
                $coursesTaught[$cKey] = [
                    'id'            => $cls['course_id'],
                    'title'         => $cls['course_title'],
                    'slug'          => $cls['course_slug'],
                    'level'         => $cls['course_level'],
                    'duration'      => $cls['course_duration'],
                    'program_title' => $cls['program_title'],
                    'program_slug'  => $cls['program_slug'],
                    'class_count'   => 0,
                ];
            }
            $coursesTaught[$cKey]['class_count']++;
        }

        // Total students mentored
        $totalStudents = 0;
        $classIds = array_column($classes, 'id');
        if ($classIds !== []) {
            $totalStudents = (int) $db->table('enrollments')
                ->whereIn('class_id', $classIds)
                ->whereIn('status', ['active', 'completed'])
                ->countAllResults();
        }

        // Other trainers for sidebar recommendations
        $otherTrainers = $db->table('class_trainers ct')
            ->select('u.id, u.name, u.username, u.profile_type, COUNT(DISTINCT ct.class_id) AS total_classes')
            ->join('users u', 'u.id = ct.user_id')
            ->where('ct.user_id !=', (int) $user['id'])
            ->where('ct.status', 'active')
            ->where('u.account_status', 'active')
            ->where('u.deleted_at IS NULL', null, false)
            ->groupBy('u.id, u.name, u.username, u.profile_type')
            ->orderBy('total_classes', 'DESC')
            ->limit(4)
            ->get()->getResultArray();

        $displayName = $user['name'] ?: $user['username'];
        $breadcrumb = [
            ['label' => 'Arena Belajar', 'url' => site_url('arena-belajar')],
            ['label' => 'Instruktur & Pengajar', 'url' => site_url('arena-belajar/pengajar')],
            ['label' => $displayName],
        ];

        $item = [
            'id'              => $user['id'],
            'title'           => $displayName,
            'name'            => $displayName,
            'username'        => $user['username'],
            'slug'            => $user['username'],
            'email'           => $user['email'],
            'profile_type'    => $user['profile_type'],
            'classes'         => array_map(static fn(array $c): array => ['name' => $c['name'], 'code' => $c['code']], $classes),
            'detailedClasses' => $classes,
            'coursesTaught'   => array_values($coursesTaught),
            'total_classes'   => count($classes),
            'total_courses'   => count($coursesTaught),
            'total_students'  => $totalStudents,
        ];

        return $this->detail('Profil Instruktur & Pengajar PBJ', $displayName, 'trainers', $item, [
            'trainer'        => $user,
            'classes'        => $classes,
            'coursesTaught'  => array_values($coursesTaught),
            'totalStudents'  => $totalStudents,
            'otherTrainers'  => $otherTrainers,
            'breadcrumb'     => $breadcrumb,
        ]);
    }

    private function catalog(string $pageTitle, string $catalogType, string $description, array $items = [], array $extraData = []): string
    {
        $programModel = new ProgramModel();
        $courseModel  = new CourseModel();
        $classModel   = new LearningClassModel();
        $db           = db_connect();

        $stats = [
            'total_programs' => (int) $programModel->where('status', 'published')->where('visibility', 'public')->countAllResults(),
            'total_courses'  => (int) $courseModel->where('status', 'published')->where('visibility', 'public')->countAllResults(),
            'total_classes'  => (int) $classModel->whereIn('status', ['scheduled', 'active', 'open', 'published', 'ongoing'])->countAllResults(),
            'total_trainers' => (int) $db->table('class_trainers ct')
                ->join('users u', 'u.id = ct.user_id')
                ->where('ct.status', 'active')
                ->where('u.account_status', 'active')
                ->where('u.deleted_at IS NULL', null, false)
                ->select('COUNT(DISTINCT ct.user_id) AS cnt')
                ->get()->getRowArray()['cnt'] ?? 0,
        ];

        $topPrograms = $programModel->where('status', 'published')->where('visibility', 'public')->orderBy('id', 'DESC')->findAll(3);
        $topCourses  = $courseModel->where('status', 'published')->where('visibility', 'public')->orderBy('id', 'DESC')->findAll(3);
        $topClasses  = $classModel->whereIn('status', ['scheduled', 'active', 'open', 'published', 'ongoing'])->orderBy('id', 'DESC')->findAll(3);

        return view('landing/pages/learning_catalog', array_merge([
            'title'           => $pageTitle . ' | Arena Belajar BegawiPengadaan',
            'metaDescription' => $description,
            'metaKeywords'    => 'Arena Belajar, ' . strtolower($pageTitle) . ', BegawiPengadaan',
            'pageTitle'       => $pageTitle,
            'catalogType'     => $catalogType,
            'description'     => $description,
            'items'           => $items,
            'stats'           => $stats,
            'topPrograms'     => $topPrograms,
            'topCourses'      => $topCourses,
            'topClasses'      => $topClasses,
            'pager'           => service('pager'),
        ], $extraData));
    }

    private function detail(string $pageTitle, string $itemTitle, string $catalogType, ?array $item = null, array $extra = []): string
    {
        return view('landing/pages/learning_detail', array_merge([
            'title'           => $itemTitle . ' | Arena Belajar BegawiPengadaan',
            'metaDescription' => 'Informasi ' . strtolower($pageTitle) . ' pada Arena Belajar BegawiPengadaan.',
            'metaKeywords'    => 'BegawiPengadaan, Arena Belajar, ' . strtolower($pageTitle),
            'pageTitle'       => $pageTitle,
            'itemTitle'       => $itemTitle,
            'catalogType'     => $catalogType,
            'item'            => $item,
        ], $extra));
    }

    private function formatSlug(string $slug): string
    {
        return ucwords(str_replace(['-', '_'], ' ', $slug));
    }
}
