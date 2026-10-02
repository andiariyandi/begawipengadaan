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
        $items = (new ProgramModel())->where('status', 'published')->where('visibility', 'public')->orderBy('featured', 'DESC')->orderBy('title', 'ASC')->findAll();
        return $this->catalog('Program Pembelajaran', 'programs', 'Program terstruktur untuk membangun kompetensi PBJ dari dasar hingga lanjutan.', $items);
    }

    public function courses(): string
    {
        $items = (new CourseModel())
            ->select('courses.*, programs.title AS program_title')
            ->join('programs', 'programs.id = courses.program_id')
            ->where('courses.status', 'published')
            ->where('courses.visibility', 'public')
            ->where('programs.status', 'published')
            ->where('programs.visibility', 'public')
            ->orderBy('courses.title', 'ASC')
            ->findAll();
        return $this->catalog('Course PBJ', 'courses', 'Materi mandiri tentang regulasi, perencanaan, pelaksanaan, dan evaluasi pengadaan.', $items);
    }

    public function classes(): string
    {
        $items = (new LearningClassModel())
            ->select('learning_classes.*, courses.title AS course_title')
            ->join('courses', 'courses.id = learning_classes.course_id')
            ->whereIn('learning_classes.status', ['scheduled', 'active'])
            ->where('courses.status', 'published')
            ->where('courses.visibility', 'public')
            ->orderBy('learning_classes.start_at', 'ASC')
            ->findAll();
        foreach ($items as &$item) {
            $item['slug'] = $item['code'];
            $item['title'] = $item['name'];
            $item['text'] = $item['course_title'] ?? 'Kelas PBJ';
            $item['icon'] = 'why-choose-one-icon-1.png';
        }
        unset($item);

        return $this->catalog('Kelas dan Diskusi', 'classes', 'Kelas aktif dan forum diskusi untuk belajar bersama pengajar serta peserta PBJ.', $items);
    }

    public function trainers(): string
    {
        $rows = db_connect()->table('class_trainers')
            ->select('users.id, users.name, users.username, users.profile_type, learning_classes.name AS class_name, learning_classes.code AS class_code')
            ->join('users', 'users.id = class_trainers.user_id')
            ->join('learning_classes', 'learning_classes.id = class_trainers.class_id')
            ->join('courses', 'courses.id = learning_classes.course_id')
            ->where('class_trainers.status', 'active')
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
        $item = (new ProgramModel())->where('slug', $slug)->where('status', 'published')->where('visibility', 'public')->first();
        if ($item === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Program tidak ditemukan.');
        }
        return $this->detail('Detail Program Pembelajaran', $item['title'], 'programs', $item);
    }

    public function courseDetail(string $slug): string
    {
        $item = (new CourseModel())
            ->select('courses.*, programs.title AS program_title')
            ->join('programs', 'programs.id = courses.program_id')
            ->where('courses.slug', $slug)
            ->where('courses.status', 'published')
            ->where('courses.visibility', 'public')
            ->where('programs.status', 'published')
            ->where('programs.visibility', 'public')
            ->first();
        if ($item === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Course tidak ditemukan.');
        }
        return $this->detail('Detail Course PBJ', $item['title'], 'courses', $item);
    }

    public function classDetail(string $slug): string
    {
        $item = (new LearningClassModel())
            ->select('learning_classes.*, courses.title AS course_title')
            ->join('courses', 'courses.id = learning_classes.course_id')
            ->where('learning_classes.code', $slug)
            ->whereIn('learning_classes.status', ['scheduled', 'active'])
            ->where('courses.status', 'published')
            ->where('courses.visibility', 'public')
            ->first();
        if ($item === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kelas tidak ditemukan.');
        }
        $item['title'] = $item['name'];
        $item['slug'] = $item['code'];
        return $this->detail('Detail Kelas PBJ', $item['name'], 'classes', $item);
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
        ]);
    }

    private function detail(string $pageTitle, string $itemTitle, string $catalogType, ?array $item = null): string
    {
        return view('landing/pages/learning_detail', [
            'title' => $itemTitle . ' | Arena Belajar BegawiPBJ',
            'metaDescription' => 'Informasi ' . strtolower($pageTitle) . ' pada Arena Belajar BegawiPBJ.',
            'metaKeywords' => 'BegawiPBJ, Arena Belajar, ' . strtolower($pageTitle),
            'pageTitle' => $pageTitle,
            'itemTitle' => $itemTitle,
            'catalogType' => $catalogType,
            'item' => $item,
        ]);
    }

    private function formatSlug(string $slug): string
    {
        return ucwords(str_replace(['-', '_'], ' ', $slug));
    }
}
