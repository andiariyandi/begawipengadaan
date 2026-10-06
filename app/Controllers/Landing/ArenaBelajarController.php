<?php

namespace App\Controllers\Landing;

use App\Controllers\BaseController;
use App\Models\Learning\CourseModel;
use App\Models\Learning\LearningClassModel;
use App\Models\Learning\ProgramModel;

class ArenaBelajarController extends BaseController
{
    public function index(): string
    {
        return view('landing/pages/learning', [
            'title' => 'Arena Belajar | BegawiPBJ',
            'metaDescription' => 'Pusat pembelajaran, program, kelas, dan pengajar Pengadaan Barang/Jasa Pemerintah Provinsi Lampung.',
            'metaKeywords' => 'Arena Belajar, pembelajaran PBJ, pelatihan pengadaan, BegawiPBJ',
        ]);
    }

    public function programs(): string
    {
        $model = new ProgramModel();
        $items = $model
            ->orderBy('featured', 'DESC')
            ->orderBy('title', 'ASC')
            ->paginate(15, 'default');
        return $this->catalog('Program Pembelajaran', 'programs', 'Program terstruktur untuk membangun kompetensi PBJ dari dasar hingga lanjutan.', $items);
    }

    public function courses(): string
    {
        $model = new CourseModel();
        $items = $model
            ->select('courses.*, programs.title AS program_title')
            ->join('programs', 'programs.id = courses.program_id')
            ->orderBy('courses.title', 'ASC')
            ->paginate(15, 'default');
        return $this->catalog('Course PBJ', 'courses', 'Materi mandiri tentang regulasi, perencanaan, pelaksanaan, dan evaluasi pengadaan.', $items);
    }

    public function classes(): string
    {
        $model = new LearningClassModel();
        $items = $model
            ->select('learning_classes.*, courses.title AS course_title')
            ->join('courses', 'courses.id = learning_classes.course_id')
            ->orderBy('learning_classes.start_at', 'ASC')
            ->paginate(15, 'default');
        foreach ($items as &$item) {
            $item['slug'] = $item['code'];
            $item['title'] = $item['name'];
            $item['text'] = $item['course_title'] ?? 'Kelas PBJ';
            $item['icon'] = 'why-choose-one-icon-1.png';
        }
        unset($item);

        return $this->catalog('Kelas dan Diskusi', 'classes', 'Daftar kelas PBJ beserta status dan periodenya.', $items);
    }

    public function trainers(): string
    {
        $rows = db_connect()->table('class_trainers')
            ->select('users.id, users.name, users.username, users.profile_type, learning_classes.name AS class_name, learning_classes.code AS class_code')
            ->join('users', 'users.id = class_trainers.user_id')
            ->join('learning_classes', 'learning_classes.id = class_trainers.class_id')
            ->join('courses', 'courses.id = learning_classes.course_id')
            ->where('class_trainers.status', 'active')
            ->where('users.account_status', 'active')
            ->where('users.deleted_at IS NULL', null, false)
            ->whereIn('learning_classes.status', ['scheduled', 'active'])
            ->where('courses.status', 'published')
            ->where('courses.visibility', 'public')
            ->orderBy('users.name', 'ASC')
            ->get()
            ->getResultArray();
        $items = [];
        foreach ($rows as $row) {
            $key = (string) $row['id'];
            if (! isset($items[$key])) {
                $items[$key] = [
                    'id' => $row['id'],
                    'title' => $row['name'] ?: $row['username'],
                    'slug' => $row['username'] ?: (string) $row['id'],
                    'text' => $row['profile_type'] ?: 'Pengajar PBJ',
                    'profile_type' => $row['profile_type'],
                    'classes' => [],
                    'icon' => 'why-choose-one-icon-1.png',
                ];
            }
            $items[$key]['classes'][] = [
                'name' => $row['class_name'],
                'code' => $row['class_code'],
            ];
        }

        return $this->catalog('Pengajar PBJ', 'trainers', 'Pengajar dan praktisi yang berbagi pengalaman serta praktik baik Pengadaan Barang/Jasa.', array_values($items));
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

        $courses = (new CourseModel())
            ->where('program_id', (int) $program['id'])
            ->where('status', 'published')
            ->where('visibility', 'public')
            ->orderBy('title', 'ASC')
            ->findAll();

        return $this->detail('Detail Program Pembelajaran', $program['title'], 'programs', $program, ['courses' => $courses]);
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

        $classes = (new LearningClassModel())
            ->select('learning_classes.*, courses.title AS course_title')
            ->join('courses', 'courses.id = learning_classes.course_id')
            ->where('learning_classes.course_id', (int) $course['id'])
            ->whereIn('learning_classes.status', ['scheduled', 'active'])
            ->where('courses.status', 'published')
            ->orderBy('learning_classes.start_at', 'ASC')
            ->findAll();
        foreach ($classes as &$class) {
            $class['slug']  = $class['code'];
            $class['title'] = $class['name'];
        }
        unset($class);

        $breadcrumb = [
            ['label' => 'Arena Belajar', 'url' => site_url('arena-belajar')],
            ['label' => 'Program', 'url' => site_url('arena-belajar/program')],
            ['label' => $course['program_title'] ?? 'Program', 'url' => ! empty($course['program_slug']) ? route_to('public.learning.program.detail', $course['program_slug']) : site_url('arena-belajar/program')],
            ['label' => $course['title']],
        ];

        return $this->detail('Detail Course PBJ', $course['title'], 'courses', $course, ['classes' => $classes, 'breadcrumb' => $breadcrumb]);
    }

    public function classDetail(string $slug): string
    {
        $item = (new LearningClassModel())
            ->select('learning_classes.*, courses.title AS course_title, courses.slug AS course_slug, programs.title AS program_title, programs.slug AS program_slug')
            ->join('courses', 'courses.id = learning_classes.course_id')
            ->join('programs', 'programs.id = courses.program_id', 'left')
            ->where('learning_classes.code', $slug)
            ->whereIn('learning_classes.status', ['scheduled', 'active'])
            ->where('courses.status', 'published')
            ->where('courses.visibility', 'public')
            ->first();
        if ($item === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kelas tidak ditemukan.');
        }
        $item['title'] = $item['name'];
        $item['slug']  = $item['code'];

        $trainers = db_connect()->table('class_trainers ct')
            ->select('users.id, users.name, users.username')
            ->join('users', 'users.id = ct.user_id')
            ->where('ct.class_id', (int) $item['id'])
            ->where('ct.status', 'active')
            ->orderBy('users.name', 'ASC')
            ->get()->getResultArray();

        $isLoggedIn = session()->get('isLoggedIn') === true;
        $userId     = (int) session()->get('user_id');
        $enrollmentStatus = null;
        if ($isLoggedIn && $userId > 0) {
            $enrollmentStatus = $this->lookupEnrollmentStatus((int) $item['id'], $userId);
        }
        $item['enrollment_status'] = $enrollmentStatus;
        $item['trainers']           = $trainers;

        $breadcrumb = [
            ['label' => 'Arena Belajar', 'url' => site_url('arena-belajar')],
            ['label' => 'Program', 'url' => site_url('arena-belajar/program')],
            ['label' => $item['program_title'] ?? 'Program', 'url' => ! empty($item['program_slug']) ? route_to('public.learning.program.detail', $item['program_slug']) : site_url('arena-belajar/program')],
            ['label' => 'Course', 'url' => ! empty($item['course_slug']) ? route_to('public.learning.course.detail', $item['course_slug']) : site_url('arena-belajar/course')],
            ['label' => $item['course_title'] ?? 'Course', 'url' => ! empty($item['course_slug']) ? route_to('public.learning.course.detail', $item['course_slug']) : site_url('arena-belajar/course')],
            ['label' => $item['name']],
        ];

        return $this->detail('Detail Kelas PBJ', $item['name'], 'classes', $item, ['breadcrumb' => $breadcrumb]);
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
        if (! in_array($class['status'] ?? null, ['scheduled', 'active'], true)) {
            return redirect()->to(route_to('public.learning.class.detail', $slug))
                ->with('error', 'Kelas tidak dapat diikuti pada status saat ini.');
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
            ->with('success', 'Pengajuan enrollment berhasil. Menunggu persetujuan admin.');
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
        $rows = db_connect()->table('class_trainers')
            ->select('users.id, users.name, users.username, users.profile_type, learning_classes.name AS class_name, learning_classes.code AS class_code')
            ->join('users', 'users.id = class_trainers.user_id')
            ->join('learning_classes', 'learning_classes.id = class_trainers.class_id')
            ->join('courses', 'courses.id = learning_classes.course_id')
            ->where('class_trainers.status', 'active')
            ->where('users.username', $slug)
            ->where('users.account_status', 'active')
            ->where('users.deleted_at IS NULL', null, false)
            ->whereIn('learning_classes.status', ['scheduled', 'active'])
            ->where('courses.status', 'published')
            ->where('courses.visibility', 'public')
            ->get()
            ->getResultArray();
        if ($rows === []) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Pengajar tidak ditemukan.');
        }
        $item = [
            'title' => $rows[0]['name'] ?: $rows[0]['username'],
            'slug' => $rows[0]['username'],
            'profile_type' => $rows[0]['profile_type'],
            'classes' => array_map(static fn (array $row): array => ['name' => $row['class_name'], 'code' => $row['class_code']], $rows),
        ];
        return $this->detail('Profil Pengajar PBJ', $item['title'], 'trainers', $item);
    }

    private function catalog(string $pageTitle, string $catalogType, string $description, array $items = []): string
    {
        return view('landing/pages/learning_catalog', [
            'title' => $pageTitle . ' | Arena Belajar BegawiPBJ',
            'metaDescription' => $description,
            'metaKeywords' => 'Arena Belajar, ' . strtolower($pageTitle) . ', BegawiPBJ',
            'pageTitle' => $pageTitle,
            'catalogType' => $catalogType,
            'description' => $description,
            'items' => $items,
            'pager' => service('pager'),
        ]);
    }

    private function detail(string $pageTitle, string $itemTitle, string $catalogType, ?array $item = null, array $extra = []): string
    {
        return view('landing/pages/learning_detail', array_merge([
            'title' => $itemTitle . ' | Arena Belajar BegawiPBJ',
            'metaDescription' => 'Informasi ' . strtolower($pageTitle) . ' pada Arena Belajar BegawiPBJ.',
            'metaKeywords' => 'BegawiPBJ, Arena Belajar, ' . strtolower($pageTitle),
            'pageTitle' => $pageTitle,
            'itemTitle' => $itemTitle,
            'catalogType' => $catalogType,
            'item' => $item,
        ], $extra));
    }

    private function formatSlug(string $slug): string
    {
        return ucwords(str_replace(['-', '_'], ' ', $slug));
    }
}
