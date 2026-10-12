<?php

namespace App\Controllers\Landing;

use App\Controllers\BaseController;

class HomeController extends BaseController
{
    public function index(): string
    {
        $db = db_connect();

        // 1. Ekosistem Live Statistics
        $stats = [
            'total_regulations' => 0,
            'total_courses'     => 0,
            'total_classes'     => 0,
            'total_knowledge'   => 0,
            'total_downloads'   => 0,
            'total_users'       => 0,
        ];

        try {
            $stats['total_regulations'] = (int) $db->table('regulations')
                ->where('deleted_at', null)
                ->where('workflow_status', 'published')
                ->countAllResults();
        } catch (\Throwable) {}

        try {
            $stats['total_courses'] = (int) $db->table('courses')
                ->where('deleted_at', null)
                ->where('status', 'published')
                ->where('visibility', 'public')
                ->countAllResults();
        } catch (\Throwable) {}

        try {
            $stats['total_classes'] = (int) $db->table('learning_classes')
                ->where('deleted_at', null)
                ->whereIn('status', ['scheduled', 'active'])
                ->countAllResults();
        } catch (\Throwable) {}

        try {
            $stats['total_knowledge'] = (int) $db->table('knowledge_articles')
                ->where('deleted_at', null)
                ->where('workflow_status', 'published')
                ->where('visibility', 'public')
                ->countAllResults();
        } catch (\Throwable) {}

        try {
            $stats['total_downloads'] = (int) $db->table('regulation_downloads')->countAllResults();
        } catch (\Throwable) {}

        try {
            $stats['total_users'] = (int) $db->table('users')
                ->where('deleted_at', null)
                ->where('account_status', 'active')
                ->countAllResults();
        } catch (\Throwable) {}

        // 2. Featured / Popular Courses (Arena Belajar)
        $courses = [];
        try {
            $courses = $db->table('courses c')
                ->select('c.*, p.title AS program_title, p.slug AS program_slug, fa.path AS cover_asset_path')
                ->join('programs p', 'p.id = c.program_id', 'left')
                ->join('file_assets fa', 'fa.id = c.cover_file_id', 'left')
                ->where('c.status', 'published')
                ->where('c.visibility', 'public')
                ->where('c.deleted_at', null)
                ->orderBy('c.featured', 'DESC')
                ->orderBy('c.id', 'DESC')
                ->limit(6)
                ->get()->getResultArray();
        } catch (\Throwable) {}

        // 3. Featured & Latest Regulations (Regulasi PBJ)
        $regulations = [];
        try {
            $regulations = $db->table('regulations r')
                ->select('r.*, rc.name AS category_name, rc.slug AS category_slug, fa.path AS cover_asset_path')
                ->join('regulation_categories rc', 'rc.id = r.category_id', 'left')
                ->join('file_assets fa', 'fa.id = r.cover_file_id', 'left')
                ->where('r.workflow_status', 'published')
                ->where('r.visibility', 'public')
                ->where('r.deleted_at', null)
                ->orderBy('r.is_featured', 'DESC')
                ->orderBy('r.document_year', 'DESC')
                ->orderBy('r.id', 'DESC')
                ->limit(6)
                ->get()->getResultArray();
        } catch (\Throwable) {}

        // 4. Bunga Rampai (Multi-Pillar Knowledge Hub)
        $articles = [];
        $templates = [];
        $caseStudies = [];
        $faqs = [];
        $mediaItems = [];

        try {
            $articles = $db->table('knowledge_articles ka')
                ->select('ka.*, u.name AS author_name, fa.path AS cover_path')
                ->join('users u', 'u.id = ka.author_id', 'left')
                ->join('file_assets fa', 'fa.id = ka.cover_file_id', 'left')
                ->where('ka.workflow_status', 'published')
                ->where('ka.visibility', 'public')
                ->where('ka.deleted_at', null)
                ->where('ka.type', 'article')
                ->orderBy('ka.featured', 'DESC')
                ->orderBy('ka.published_at', 'DESC')
                ->orderBy('ka.id', 'DESC')
                ->limit(4)
                ->get()->getResultArray();
        } catch (\Throwable) {}

        try {
            $templates = $db->table('knowledge_articles ka')
                ->select('ka.*, fa.path AS cover_path, fa.mime_type')
                ->join('file_assets fa', 'fa.id = ka.cover_file_id', 'left')
                ->where('ka.workflow_status', 'published')
                ->where('ka.visibility', 'public')
                ->where('ka.deleted_at', null)
                ->where('ka.type', 'template')
                ->orderBy('ka.download_count', 'DESC')
                ->orderBy('ka.id', 'DESC')
                ->limit(4)
                ->get()->getResultArray();
        } catch (\Throwable) {}

        try {
            $caseStudies = $db->table('knowledge_articles ka')
                ->select('ka.*, u.name AS author_name')
                ->join('users u', 'u.id = ka.author_id', 'left')
                ->where('ka.workflow_status', 'published')
                ->where('ka.visibility', 'public')
                ->where('ka.deleted_at', null)
                ->where('ka.type', 'case_study')
                ->orderBy('ka.id', 'DESC')
                ->limit(4)
                ->get()->getResultArray();
        } catch (\Throwable) {}

        try {
            $faqs = $db->table('knowledge_articles ka')
                ->where('ka.workflow_status', 'published')
                ->where('ka.visibility', 'public')
                ->where('ka.deleted_at', null)
                ->where('ka.type', 'faq')
                ->orderBy('ka.id', 'ASC')
                ->limit(5)
                ->get()->getResultArray();
        } catch (\Throwable) {}

        try {
            $mediaItems = $db->table('knowledge_media km')
                ->select('km.*, fa.path AS file_path, fa.mime_type, ft.path AS thumb_path')
                ->join('file_assets fa', 'fa.id = km.file_id', 'left')
                ->join('file_assets ft', 'ft.id = km.thumbnail_file_id', 'left')
                ->where('km.workflow_status', 'published')
                ->where('km.visibility', 'public')
                ->orderBy('km.sort_order', 'ASC')
                ->orderBy('km.id', 'DESC')
                ->limit(4)
                ->get()->getResultArray();
        } catch (\Throwable) {}

        // 5. Procurement Stages (Tahapan Siklus PBJ)
        $stages = [];
        try {
            $stages = $db->table('procurement_stages')
                ->where('is_active', 1)
                ->orderBy('sort_order', 'ASC')
                ->get()->getResultArray();
        } catch (\Throwable) {}

        // 6. Upcoming Events / Bimtek
        $upcomingEvents = [];
        try {
            $upcomingEvents = $db->table('events')
                ->where('deleted_at', null)
                ->where('visibility', 'public')
                ->whereIn('status', ['open_registration', 'scheduled', 'ongoing'])
                ->orderBy('start_at', 'ASC')
                ->limit(3)
                ->get()->getResultArray();
        } catch (\Throwable) {}

        return view('landing/home/index', [
            'title'            => 'BegawiPBJ | Platform Pengadaan Barang/Jasa Pemerintah Provinsi Lampung',
            'pageTitle'        => 'BegawiPBJ | Ekosistem Digital Pengadaan Barang/Jasa Provinsi Lampung',
            'metaDescription'  => 'Platform resmi terintegrasi Pembelajaran Pengadaan (Arena Belajar), Repositori Pengetahuan (Bunga Rampai), dan Pusat Regulasi Pengadaan Barang/Jasa Pemerintah Provinsi Lampung.',
            'metaKeywords'     => 'BegawiPBJ, PBJ Lampung, Biro PBJ Setda Lampung, Arena Belajar, Bunga Rampai, Regulasi PBJ, Pengadaan Barang Jasa Pemerintah, LKPP',
            'stats'            => $stats,
            'courses'          => $courses,
            'regulations'      => $regulations,
            'articles'         => $articles,
            'templates'        => $templates,
            'caseStudies'      => $caseStudies,
            'faqs'             => $faqs,
            'mediaItems'       => $mediaItems,
            'stages'           => $stages,
            'upcomingEvents'   => $upcomingEvents,
        ]);
    }

    public function about(): string
    {
        return view('landing/pages/about', [
            'title' => 'Tentang BegawiPBJ | Provinsi Lampung',
            'metaDescription' => 'Tentang visi, misi, nilai, dan ekosistem layanan BegawiPBJ.',
            'metaKeywords' => 'tentang BegawiPBJ, Biro PBJ Lampung, pengadaan barang jasa Lampung',
            'breadcrumbs' => [
                ['label' => 'Tentang BegawiPBJ'],
            ],
        ]);
    }

    public function contact(): string
    {
        return view('landing/home/contact', [
            'title' => 'Kontak BegawiPBJ | Provinsi Lampung',
            'metaDescription' => 'Hubungi tim BegawiPBJ untuk informasi dan layanan Pengadaan Barang/Jasa.',
            'metaKeywords' => 'kontak BegawiPBJ, Biro PBJ Lampung, pengadaan barang jasa',
            'breadcrumbs' => [
                ['label' => 'Kontak'],
            ],
        ]);
    }

    public function forbiddenPreview(): string
    {
        return $this->errorPreview(
            'Akses Ditolak',
            'Anda tidak memiliki izin untuk mengakses halaman ini.',
            '403'
        );
    }

    public function notFoundPreview(): string
    {
        return $this->errorPreview(
            'Halaman Tidak Ditemukan',
            'Halaman yang Anda cari tidak tersedia atau telah dipindahkan.',
            '404'
        );
    }

    public function serverErrorPreview(): string
    {
        return $this->errorPreview(
            'Terjadi Gangguan Sistem',
            'Sistem sedang mengalami kendala. Silakan coba kembali beberapa saat lagi.',
            '500'
        );
    }

    private function errorPreview(
        string $pageTitle,
        string $message,
        string $statusCode
    ): string {
        return view('landing/home/error_preview', [
            'title' => $statusCode . ' | ' . $pageTitle . ' | BegawiPBJ',
            'metaDescription' => $message,
            'pageTitle' => $pageTitle,
            'message' => $message,
            'statusCode' => $statusCode,
        ]);
    }
}
