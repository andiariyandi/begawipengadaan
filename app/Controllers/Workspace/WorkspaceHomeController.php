<?php

namespace App\Controllers\Workspace;

use App\Controllers\BaseController;

class WorkspaceHomeController extends BaseController
{
    public function index()
    {
        return $this->renderWorkspace('dashboard');
    }

    public function section(string $section)
    {
        return $this->renderWorkspace($section);
    }

    private function renderWorkspace(string $section)
    {
        $section = $this->normalizeSection($section);
        $role = $this->workspaceRole();

        $view = $section === 'dashboard' ? 'home' : $section;

        return view('main/workspace/pages/' . $view, [
            'title' => ucfirst($section) . ' | Workspace BegawiPBJ',
            'userName' => (string) (session()->get('name') ?: 'Pengguna BegawiPBJ'),
            'roles' => [$role],
            'primaryRole' => $role,
            'roleLabel' => $this->roleLabel($role),
            'menuItems' => $this->menuItems($role),
            'sectionContent' => $this->sectionContent($section),
        ]);
    }

    private function workspaceRole(): string
    {
        $roles = (array) session()->get('roles');

        foreach (['operator', 'evaluator', 'trainer', 'participant', 'peserta'] as $role) {
            if (in_array($role, $roles, true)) {
                return $role === 'peserta' ? 'participant' : $role;
            }
        }

        return 'participant';
    }

    private function roleLabel(string $role): string
    {
        return [
            'participant' => 'Peserta',
            'trainer' => 'Pengajar',
            'evaluator' => 'Evaluator',
            'operator' => 'Operator',
        ][$role] ?? 'Pengguna';
    }

    private function normalizeSection(string $section): string
    {
        $allowed = [
            'dashboard',
            'pembelajaran',
            'tugas',
            'penilaian',
            'bookmark',
            'notifikasi',
            'profil',
            'aktivitas',
        ];

        return in_array($section, $allowed, true) ? $section : 'dashboard';
    }

    private function menuItems(string $role): array
    {
        $workspace = route_to('workspace.home');
        $section = static fn (string $name): string => route_to('workspace.section', $name);
        $allowed = static fn (array $roles): bool => in_array('all', $roles, true) || in_array($role, $roles, true);

        $menu = [
            ['label' => 'Dashboard', 'items' => [
                ['label' => 'Ringkasan', 'url' => $workspace],
                ['label' => 'Aktivitas Terakhir', 'url' => $section('aktivitas')],
                ['label' => 'Statistik Role', 'url' => $workspace],
            ]],
            ['label' => 'Pembelajaran', 'items' => [
                ['label' => 'Kelas Saya', 'url' => $section('pembelajaran'), 'roles' => ['all']],
                ['label' => 'Jadwal Belajar', 'url' => $section('pembelajaran'), 'roles' => ['participant']],
                ['label' => 'Jadwal Mengajar', 'url' => $section('pembelajaran'), 'roles' => ['trainer']],
                ['label' => 'Progress Belajar', 'url' => $section('pembelajaran'), 'roles' => ['participant']],
                ['label' => 'Progress Peserta', 'url' => $section('pembelajaran'), 'roles' => ['trainer', 'evaluator']],
                ['label' => 'Course Saya', 'url' => $section('pembelajaran'), 'roles' => ['participant']],
                ['label' => 'Sertifikat Saya', 'url' => $section('pembelajaran'), 'roles' => ['participant']],
            ]],
            ['label' => 'Tugas', 'items' => [
                ['label' => 'Tugas Aktif', 'url' => $section('tugas'), 'roles' => ['participant']],
                ['label' => 'Pengumpulan Tugas', 'url' => $section('tugas'), 'roles' => ['participant']],
                ['label' => 'Revisi Tugas', 'url' => $section('tugas'), 'roles' => ['participant']],
                ['label' => 'Review Tugas', 'url' => $section('tugas'), 'roles' => ['trainer', 'evaluator']],
                ['label' => 'Riwayat Tugas', 'url' => $section('tugas'), 'roles' => ['all']],
            ]],
            ['label' => 'Penilaian', 'items' => [
                ['label' => 'Pre Test', 'url' => $section('penilaian'), 'roles' => ['participant']],
                ['label' => 'Quiz', 'url' => $section('penilaian'), 'roles' => ['participant']],
                ['label' => 'Post Test', 'url' => $section('penilaian'), 'roles' => ['participant']],
                ['label' => 'Penilaian Peserta', 'url' => $section('penilaian'), 'roles' => ['trainer', 'evaluator']],
                ['label' => 'Grading', 'url' => $section('penilaian'), 'roles' => ['evaluator']],
                ['label' => 'Nilai Saya', 'url' => $section('penilaian'), 'roles' => ['participant']],
                ['label' => 'Feedback', 'url' => $section('penilaian'), 'roles' => ['all']],
            ]],
            ['label' => 'Bookmark', 'items' => [
                ['label' => 'Pembelajaran', 'url' => $section('bookmark'), 'roles' => ['all']],
                ['label' => 'Bunga Rampai', 'url' => $section('bookmark'), 'roles' => ['all']],
                ['label' => 'Regulasi', 'url' => $section('bookmark'), 'roles' => ['all']],
                ['label' => 'Berita', 'url' => $section('bookmark'), 'roles' => ['all']],
                ['label' => 'Event', 'url' => $section('bookmark'), 'roles' => ['all']],
            ]],
            ['label' => 'Notifikasi', 'items' => [
                ['label' => 'Pembelajaran', 'url' => $section('notifikasi'), 'roles' => ['all']],
                ['label' => 'Tugas', 'url' => $section('notifikasi'), 'roles' => ['all']],
                ['label' => 'Penilaian', 'url' => $section('notifikasi'), 'roles' => ['all']],
                ['label' => 'Sistem', 'url' => $section('notifikasi'), 'roles' => ['all']],
            ]],
            ['label' => 'Profil', 'items' => [
                ['label' => 'Data Diri', 'url' => $section('profil'), 'roles' => ['all']],
                ['label' => 'Profil Kompetensi', 'url' => $section('profil'), 'roles' => ['all']],
                ['label' => 'Pengaturan', 'url' => $section('profil'), 'roles' => ['all']],
                ['label' => 'Riwayat Belajar', 'url' => $section('profil'), 'roles' => ['participant']],
                ['label' => 'Riwayat Mengajar', 'url' => $section('profil'), 'roles' => ['trainer']],
                ['label' => 'Riwayat Penilaian', 'url' => $section('profil'), 'roles' => ['evaluator']],
            ]],
            ['label' => 'Aktivitas', 'items' => [
                ['label' => 'Riwayat Login', 'url' => $section('aktivitas'), 'roles' => ['all']],
                ['label' => 'Aktivitas Belajar', 'url' => $section('aktivitas'), 'roles' => ['participant']],
                ['label' => 'Aktivitas Mengajar', 'url' => $section('aktivitas'), 'roles' => ['trainer']],
                ['label' => 'Aktivitas Penilaian', 'url' => $section('aktivitas'), 'roles' => ['evaluator']],
                ['label' => 'Aktivitas Sistem', 'url' => $section('aktivitas'), 'roles' => ['operator']],
            ]],
            ['label' => 'Operasional', 'items' => [
                ['label' => 'Verifikasi Operasional', 'url' => $section('operasional'), 'roles' => ['operator']],
                ['label' => 'Enrollment', 'url' => $section('operasional'), 'roles' => ['operator']],
                ['label' => 'Kelas', 'url' => $section('operasional'), 'roles' => ['operator']],
                ['label' => 'Approval', 'url' => $section('operasional'), 'roles' => ['operator']],
                ['label' => 'Laporan Operasional', 'url' => $section('operasional'), 'roles' => ['operator']],
            ]],
        ];

        foreach ($menu as &$group) {
            $group['items'] = array_values(array_filter(
                $group['items'],
                static fn (array $item): bool => ! isset($item['roles']) || $allowed($item['roles'])
            ));
        }
        unset($group);

        return array_values(array_filter($menu, static fn (array $group): bool => $group['items'] !== []));
    }

    private function sectionContent(string $section): array
    {
        $content = [
            'dashboard' => [
                'eyebrow' => 'Ringkasan Workspace',
                'title' => 'Dashboard Workspace',
                'description' => 'Pantau pembelajaran dan aktivitas personal Anda dari satu ruang kerja.',
                'cards' => [
                    ['title' => 'Program Saya', 'value' => '08', 'meta' => 'program tersedia'],
                    ['title' => 'Tugas Aktif', 'value' => '03', 'meta' => 'perlu diselesaikan'],
                    ['title' => 'Progress Belajar', 'value' => '72%', 'meta' => 'capaian prototype'],
                ],
                'items' => ['Menyelesaikan materi pengantar PBJ', 'Menyimpan program ke bookmark', 'Memperbarui profil Workspace'],
            ],
            'pembelajaran' => [
                'eyebrow' => 'Area Pembelajaran',
                'title' => 'Pembelajaran Saya',
                'description' => 'Temukan program, course, kelas, dan materi yang sedang Anda ikuti.',
                'cards' => [
                    ['title' => 'Program Saya', 'value' => '08', 'meta' => 'program diikuti'],
                    ['title' => 'Course Saya', 'value' => '12', 'meta' => 'course tersimpan'],
                    ['title' => 'Kelas Saya', 'value' => '04', 'meta' => 'kelas aktif'],
                ],
                'items' => ['Pengantar Pengadaan Barang/Jasa', 'Strategi Pengelolaan Kontrak', 'Penyusunan Dokumen Pemilihan'],
            ],
            'tugas' => [
                'eyebrow' => 'Aktivitas Belajar',
                'title' => 'Tugas',
                'description' => 'Daftar tugas dan aktivitas yang perlu Anda perhatikan.',
                'cards' => [['title' => 'Tugas Aktif', 'value' => '03', 'meta' => 'menunggu dikerjakan'], ['title' => 'Selesai', 'value' => '11', 'meta' => 'tugas terselesaikan']],
                'items' => ['Review materi Prinsip PBJ', 'Unggah refleksi pembelajaran', 'Persiapan diskusi kelas'],
            ],
            'penilaian' => [
                'eyebrow' => 'Aktivitas Belajar',
                'title' => 'Penilaian',
                'description' => 'Ringkasan penilaian pembelajaran Anda.',
                'cards' => [['title' => 'Nilai Rata-rata', 'value' => '86', 'meta' => 'dari 100'], ['title' => 'Assessment', 'value' => '05', 'meta' => 'tersedia']],
                'items' => ['Pre-test Pengantar PBJ', 'Kuis Modul 2', 'Evaluasi pembelajaran kelas'],
            ],
            'bookmark' => [
                'eyebrow' => 'Personal',
                'title' => 'Bookmark',
                'description' => 'Konten yang Anda simpan untuk dibaca kembali.',
                'cards' => [['title' => 'Pembelajaran', 'value' => '06', 'meta' => 'tersimpan'], ['title' => 'Referensi PBJ', 'value' => '04', 'meta' => 'tersimpan']],
                'items' => ['Panduan Pengadaan Berkelanjutan', 'Regulasi PBJ terbaru', 'Materi strategi pemilihan'],
            ],
            'notifikasi' => [
                'eyebrow' => 'Personal',
                'title' => 'Notifikasi',
                'description' => 'Informasi terbaru terkait aktivitas Workspace Anda.',
                'cards' => [['title' => 'Belum dibaca', 'value' => '04', 'meta' => 'notifikasi'], ['title' => 'Minggu ini', 'value' => '12', 'meta' => 'pembaruan']],
                'items' => ['Kelas baru tersedia untuk Anda', 'Tugas mendekati batas waktu', 'Profil Workspace berhasil diperbarui'],
            ],
            'profil' => [
                'eyebrow' => 'Personal',
                'title' => 'Profil Workspace',
                'description' => 'Kelola informasi dan preferensi personal Anda.',
                'cards' => [['title' => 'Status Akun', 'value' => 'Aktif', 'meta' => 'siap digunakan'], ['title' => 'Role', 'value' => 'Peserta', 'meta' => 'role Workspace']],
                'items' => ['Data dasar pengguna', 'Peran pengadaan', 'Preferensi notifikasi'],
            ],
            'aktivitas' => [
                'eyebrow' => 'Riwayat Aktivitas',
                'title' => 'Aktivitas Terakhir',
                'description' => 'Jejak aktivitas Workspace yang tercatat untuk pengguna.',
                'cards' => [['title' => 'Hari ini', 'value' => '05', 'meta' => 'aktivitas'], ['title' => 'Minggu ini', 'value' => '18', 'meta' => 'aktivitas']],
                'items' => ['Masuk ke Workspace', 'Membuka materi Pengantar PBJ', 'Melihat detail kelas'],
            ],
            'operasional' => [
                'eyebrow' => 'Operasional',
                'title' => 'Workspace Operasional',
                'description' => 'Ringkasan verifikasi, enrollment, approval, dan laporan operasional.',
                'cards' => [['title' => 'Menunggu Verifikasi', 'value' => '07', 'meta' => 'permintaan'], ['title' => 'Enrollment', 'value' => '12', 'meta' => 'permintaan'], ['title' => 'Approval', 'value' => '04', 'meta' => 'perlu ditinjau']],
                'items' => ['Verifikasi profil', 'Approval peserta', 'Monitoring kelas'],
            ],
        ];

        return $content[$section] ?? $content['dashboard'];
    }
}
