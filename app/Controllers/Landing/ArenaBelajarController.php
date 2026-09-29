<?php

declare(strict_types=1);

namespace App\Controllers\Landing;

use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;

class ArenaBelajarController extends BaseController
{
    private array $programs = [
        [
            'title' => 'Program Kompetensi Dasar PBJ',
            'slug' => 'program-kompetensi-dasar-pbj',
            'summary' => 'Program pembelajaran dasar untuk memahami prinsip, proses, dan tata kelola Pengadaan Barang/Jasa Pemerintah.',
            'image' => 'assets/main/images/courses/courses-1-1.jpg',
            'level' => 'Dasar',
            'duration' => '12 Jam Belajar',
            'courses' => 3,
        ],
        [
            'title' => 'Program Pengelolaan Kontrak',
            'slug' => 'program-pengelolaan-kontrak',
            'summary' => 'Program peningkatan kompetensi dalam penyusunan, pelaksanaan, pengendalian, dan evaluasi kontrak PBJ.',
            'image' => 'assets/main/images/courses/courses-1-2.jpg',
            'level' => 'Menengah',
            'duration' => '16 Jam Belajar',
            'courses' => 4,
        ],
        [
            'title' => 'Program Perencanaan Pengadaan',
            'slug' => 'program-perencanaan-pengadaan',
            'summary' => 'Program untuk memperkuat penyusunan kebutuhan, spesifikasi teknis, KAK, dan perencanaan pengadaan.',
            'image' => 'assets/main/images/courses/courses-1-3.jpg',
            'level' => 'Menengah',
            'duration' => '14 Jam Belajar',
            'courses' => 3,
        ],
    ];

    private array $courses = [
        [
            'title' => 'Dasar-Dasar Pengadaan Barang/Jasa Pemerintah',
            'slug' => 'dasar-dasar-pengadaan-barang-jasa-pemerintah',
            'program' => 'Program Kompetensi Dasar PBJ',
            'summary' => 'Memahami prinsip, tujuan, pelaku, proses, serta tata kelola dasar Pengadaan Barang/Jasa Pemerintah.',
            'description' => 'Course ini memberikan fondasi pengetahuan mengenai Pengadaan Barang/Jasa Pemerintah. Peserta mempelajari prinsip pengadaan, siklus proses, peran pelaku, dan praktik tata kelola yang akuntabel.',
            'image' => 'assets/main/images/courses/courses-1-1.jpg',
            'level' => 'Dasar',
            'duration' => '4 Jam Belajar',
            'lessons' => 8,
            'instructor' => 'Tim Pembelajaran BegawiPBJ',
            'instructorSlug' => 'tim-pembelajaran-begawipbj',
            'category' => 'Dasar PBJ',
        ],
        [
            'title' => 'Perencanaan Pengadaan dan Penyusunan KAK',
            'slug' => 'perencanaan-pengadaan-dan-penyusunan-kak',
            'program' => 'Program Perencanaan Pengadaan',
            'summary' => 'Menyusun kebutuhan, spesifikasi teknis, dan Kerangka Acuan Kerja secara tepat dan terukur.',
            'description' => 'Course ini membahas langkah praktis merencanakan pengadaan, menyusun spesifikasi teknis, serta membuat Kerangka Acuan Kerja yang sesuai dengan kebutuhan organisasi dan prinsip PBJ.',
            'image' => 'assets/main/images/courses/courses-1-2.jpg',
            'level' => 'Menengah',
            'duration' => '5 Jam Belajar',
            'lessons' => 10,
            'instructor' => 'Narasumber Perencanaan PBJ',
            'instructorSlug' => 'narasumber-perencanaan-pbj',
            'category' => 'Perencanaan',
        ],
        [
            'title' => 'Manajemen Kontrak Pengadaan',
            'slug' => 'manajemen-kontrak-pengadaan',
            'program' => 'Program Pengelolaan Kontrak',
            'summary' => 'Memahami pengelolaan kontrak dari persiapan, pelaksanaan, pengendalian, hingga serah terima pekerjaan.',
            'description' => 'Course ini membekali peserta dengan pengetahuan pengelolaan kontrak yang efektif, akuntabel, dan terdokumentasi selama pelaksanaan pekerjaan pengadaan.',
            'image' => 'assets/main/images/courses/courses-1-3.jpg',
            'level' => 'Menengah',
            'duration' => '6 Jam Belajar',
            'lessons' => 12,
            'instructor' => 'Narasumber Manajemen Kontrak',
            'instructorSlug' => 'narasumber-manajemen-kontrak',
            'category' => 'Manajemen Kontrak',
        ],
    ];

    private array $classes = [
        [
            'name' => 'Kelas Dasar PBJ Angkatan I',
            'slug' => 'kelas-dasar-pbj-angkatan-i',
            'course' => 'Dasar-Dasar Pengadaan Barang/Jasa Pemerintah',
            'courseSlug' => 'dasar-dasar-pengadaan-barang-jasa-pemerintah',
            'status' => 'Pendaftaran Dibuka',
            'schedule' => 'Januari - Februari 2027',
            'capacity' => '40 Peserta',
            'image' => 'assets/main/images/courses/courses-1-1.jpg',
            'trainer' => 'Tim Pembelajaran BegawiPBJ',
        ],
        [
            'name' => 'Kelas Penyusunan KAK Angkatan I',
            'slug' => 'kelas-penyusunan-kak-angkatan-i',
            'course' => 'Perencanaan Pengadaan dan Penyusunan KAK',
            'courseSlug' => 'perencanaan-pengadaan-dan-penyusunan-kak',
            'status' => 'Segera Dibuka',
            'schedule' => 'Februari 2027',
            'capacity' => '35 Peserta',
            'image' => 'assets/main/images/courses/courses-1-2.jpg',
            'trainer' => 'Narasumber Perencanaan PBJ',
        ],
        [
            'name' => 'Kelas Manajemen Kontrak Angkatan I',
            'slug' => 'kelas-manajemen-kontrak-angkatan-i',
            'course' => 'Manajemen Kontrak Pengadaan',
            'courseSlug' => 'manajemen-kontrak-pengadaan',
            'status' => 'Pendaftaran Dibuka',
            'schedule' => 'Maret 2027',
            'capacity' => '30 Peserta',
            'image' => 'assets/main/images/courses/courses-1-3.jpg',
            'trainer' => 'Narasumber Manajemen Kontrak',
        ],
    ];

    private array $instructors = [
        [
            'name' => 'Tim Pembelajaran BegawiPBJ',
            'slug' => 'tim-pembelajaran-begawipbj',
            'role' => 'Tim Pengembangan Pembelajaran PBJ',
            'image' => 'assets/main/images/team/team-1-1.jpg',
            'expertise' => 'Dasar PBJ, tata kelola, dan pembelajaran digital',
            'bio' => 'Tim pembelajaran yang menyusun materi dan pengalaman belajar untuk peningkatan kompetensi Pengadaan Barang/Jasa Pemerintah.',
            'courses' => 1,
        ],
        [
            'name' => 'Narasumber Perencanaan PBJ',
            'slug' => 'narasumber-perencanaan-pbj',
            'role' => 'Praktisi Perencanaan Pengadaan',
            'image' => 'assets/main/images/team/team-1-2.jpg',
            'expertise' => 'Perencanaan pengadaan, KAK, dan spesifikasi teknis',
            'bio' => 'Narasumber yang berfokus pada penguatan kualitas perencanaan kebutuhan dan penyusunan dokumen persiapan pengadaan.',
            'courses' => 1,
        ],
        [
            'name' => 'Narasumber Manajemen Kontrak',
            'slug' => 'narasumber-manajemen-kontrak',
            'role' => 'Praktisi Manajemen Kontrak',
            'image' => 'assets/main/images/team/team-1-3.jpg',
            'expertise' => 'Manajemen kontrak dan pengendalian pelaksanaan',
            'bio' => 'Narasumber yang membahas pengelolaan kontrak, pengendalian pelaksanaan, dan dokumentasi hasil pekerjaan.',
            'courses' => 1,
        ],
    ];

    public function index(): string
    {
        return view('landing/learning/index', [
            'title' => 'Arena Belajar | BegawiPBJ',
            'activeMenu' => 'learning',
            'programs' => $this->programs,
            'courses' => $this->courses,
            'classes' => $this->classes,
            'instructors' => $this->instructors,
        ]);
    }

    public function programs(): string
    {
        return view('landing/learning/programs', [
            'title' => 'Program Pembelajaran | BegawiPBJ',
            'activeMenu' => 'learning',
            'programs' => $this->programs,
        ]);
    }

    public function program(string $slug): string
    {
        $program = $this->findBySlug($this->programs, $slug);

        if ($program === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $courses = array_values(array_filter(
            $this->courses,
            static fn (array $course): bool => $course['program'] === $program['title']
        ));

        return view('landing/learning/program-detail', [
            'title' => $program['title'] . ' | BegawiPBJ',
            'activeMenu' => 'learning',
            'program' => $program,
            'courses' => $courses,
        ]);
    }

    public function courses(): string
    {
        return view('landing/learning/courses', [
            'title' => 'Course | Arena Belajar | BegawiPBJ',
            'activeMenu' => 'learning',
            'courses' => $this->courses,
        ]);
    }

    public function course(string $slug): string
    {
        $course = $this->findBySlug($this->courses, $slug);

        if ($course === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $classes = array_values(array_filter(
            $this->classes,
            static fn (array $class): bool => $class['courseSlug'] === $course['slug']
        ));

        return view('landing/learning/course-detail', [
            'title' => $course['title'] . ' | BegawiPBJ',
            'activeMenu' => 'learning',
            'course' => $course,
            'classes' => $classes,
        ]);
    }

    public function classes(): string
    {
        return view('landing/learning/classes', [
            'title' => 'Kelas | Arena Belajar | BegawiPBJ',
            'activeMenu' => 'learning',
            'classes' => $this->classes,
        ]);
    }

    public function classDetail(string $slug): string
    {
        $class = $this->findBySlug($this->classes, $slug);

        if ($class === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('landing/learning/class-detail', [
            'title' => $class['name'] . ' | BegawiPBJ',
            'activeMenu' => 'learning',
            'class' => $class,
            'course' => $this->findBySlug($this->courses, $class['courseSlug']),
        ]);
    }

    public function instructors(): string
    {
        return view('landing/learning/instructors', [
            'title' => 'Instruktur | Arena Belajar | BegawiPBJ',
            'activeMenu' => 'learning',
            'instructors' => $this->instructors,
        ]);
    }

    public function instructor(string $slug): string
    {
        $instructor = $this->findBySlug($this->instructors, $slug);

        if ($instructor === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $courses = array_values(array_filter(
            $this->courses,
            static fn (array $course): bool => $course['instructorSlug'] === $instructor['slug']
        ));

        return view('landing/learning/instructor-detail', [
            'title' => $instructor['name'] . ' | BegawiPBJ',
            'activeMenu' => 'learning',
            'instructor' => $instructor,
            'courses' => $courses,
        ]);
    }

    private function findBySlug(array $items, string $slug): ?array
    {
        foreach ($items as $item) {
            if ($item['slug'] === $slug) {
                return $item;
            }
        }

        return null;
    }
}