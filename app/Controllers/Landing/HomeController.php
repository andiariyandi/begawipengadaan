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
            'total_users'                 => 0,
            'total_programs'              => 0,
            'total_regulation_categories' => 0,
            'total_knowledge_categories'  => 0,
        ];

        try {
            $stats['total_regulations'] = (int) $db->table('regulations')
                ->where('deleted_at', null)
                ->where('workflow_status', 'published')
                ->countAllResults();
        } catch (\Throwable) {
        }

        try {
            $stats['total_courses'] = (int) $db->table('courses')
                ->where('deleted_at', null)
                ->where('status', 'published')
                ->where('visibility', 'public')
                ->countAllResults();
        } catch (\Throwable) {
        }

        try {
            $stats['total_classes'] = (int) $db->table('learning_classes')
                ->where('deleted_at', null)
                ->whereIn('status', ['scheduled', 'active'])
                ->countAllResults();
        } catch (\Throwable) {
        }

        try {
            $stats['total_knowledge'] = (int) $db->table('knowledge_articles')
                ->where('deleted_at', null)
                ->where('workflow_status', 'published')
                ->where('visibility', 'public')
                ->countAllResults();
        } catch (\Throwable) {
        }

        try {
            $stats['total_downloads'] = (int) $db->table('regulation_downloads')->countAllResults();
        } catch (\Throwable) {
        }

        try {
            $stats['total_users'] = (int) $db->table('users')
                ->where('deleted_at', null)
                ->where('account_status', 'active')
                ->countAllResults();
        } catch (\Throwable) {
        }

        try {
            $stats['total_programs'] = (int) $db->table('programs')
                ->where('deleted_at', null)
                ->where('status', 'published')
                ->countAllResults();
        } catch (\Throwable) {
        }

        try {
            $stats['total_regulation_categories'] = (int) $db->table('regulation_categories')
                ->where('is_active', 1)
                ->countAllResults();
        } catch (\Throwable) {
        }

        try {
            $stats['total_knowledge_categories'] = (int) $db->table('knowledge_categories')
                ->where('is_active', 1)
                ->countAllResults();
        } catch (\Throwable) {
        }

        // 2. Kelas PBJ Terbaru (Arena Belajar)
        // Strictly 3 Kelas Terbaru tanpa terikat aturan sorotan-carousel
        $latestClasses = [];
        try {
            $latestClasses = $db->table('learning_classes lc')
                ->select('lc.*, c.title AS course_title, c.slug AS course_slug, c.level AS course_level,
                          c.duration_minutes AS course_duration_minutes,
                          p.title AS program_title, p.slug AS program_slug, fa.path AS cover_asset_path,
                          (SELECT COUNT(*) FROM enrollments en WHERE en.class_id = lc.id AND en.status != "cancelled") AS enrollments_count')
                ->join('courses c', 'c.id = lc.course_id', 'left')
                ->join('programs p', 'p.id = c.program_id', 'left')
                ->join('file_assets fa', 'fa.id = lc.cover_file_id', 'left')
                ->where('lc.deleted_at', null)
                ->where('lc.name NOT LIKE', '%Test%')
                ->where('lc.name NOT LIKE', '%Fixture%')
                ->orderBy('lc.id', 'DESC')
                ->limit(3)
                ->get()->getResultArray();
        } catch (\Throwable) {
        }
        $courses = $latestClasses;

        // 3. Bank Naskah Hukum PBJ (Regulasi PBJ)
        // Strictly 3 Regulasi Terbaru berdasarkan tanggal berlaku (effective_date DESC)
        $regulations = [];
        try {
            $regulations = $db->table('regulations r')
                ->select('r.*, rc.name AS category_name, rc.slug AS category_slug, fa.path AS cover_asset_path,
                         (SELECT COUNT(*) FROM regulation_downloads rd WHERE rd.regulation_id = r.id) AS downloads_count')
                ->join('regulation_categories rc', 'rc.id = r.category_id', 'left')
                ->join('file_assets fa', 'fa.id = r.cover_file_id', 'left')
                ->where('r.workflow_status', 'published')
                ->where('r.visibility', 'public')
                ->where('r.deleted_at', null)
                ->where('r.title NOT LIKE', '%Test%')
                ->where('r.title NOT LIKE', '%Regulasi A UI%')
                ->orderBy('r.effective_date', 'DESC')
                ->orderBy('r.document_year', 'DESC')
                ->orderBy('r.id', 'DESC')
                ->limit(3)
                ->get()->getResultArray();
        } catch (\Throwable) {
        }

        // 4. Bunga Rampai Pengetahuan (Multi-Pillar Knowledge Hub)
        // Real statistical totals for tab counters & exactly 3 latest items per category
        $knowledgeCounts = [
            'articles'    => 0,
            'templates'   => 0,
            'caseStudies' => 0,
            'faqs'        => 0,
        ];
        $articles = [];
        $templates = [];
        $caseStudies = [];
        $faqs = [];
        $mediaItems = [];

        try {
            // Real statistical counts across published knowledge assets
            $knowledgeCounts['articles'] = (int) $db->table('knowledge_articles')
                ->where('workflow_status', 'published')
                ->where('visibility', 'public')
                ->where('deleted_at', null)
                ->where('type', 'article')
                ->where('title NOT LIKE', '%bbbnjmnm%')
                ->where('title NOT LIKE', '%Test Artikel%')
                ->countAllResults();

            $knowledgeCounts['templates'] = (int) $db->table('knowledge_articles')
                ->where('workflow_status', 'published')
                ->where('visibility', 'public')
                ->where('deleted_at', null)
                ->where('type', 'template')
                ->where('title NOT LIKE', '%zfdfdfsdfd%')
                ->countAllResults();

            $knowledgeCounts['caseStudies'] = (int) $db->table('knowledge_articles')
                ->where('workflow_status', 'published')
                ->where('visibility', 'public')
                ->where('deleted_at', null)
                ->where('type', 'case_study')
                ->where('title NOT LIKE', '%xvxxb%')
                ->where('title NOT LIKE', '%QA AUDIT%')
                ->countAllResults();

            $knowledgeCounts['faqs'] = (int) $db->table('knowledge_articles')
                ->where('workflow_status', 'published')
                ->where('visibility', 'public')
                ->where('deleted_at', null)
                ->where('type', 'faq')
                ->where('title NOT LIKE', '%zccsass%')
                ->where('title NOT LIKE', '%QA AUDIT%')
                ->countAllResults();

            // 3 Artikel Terbaru
            $articles = $db->table('knowledge_articles ka')
                ->select('ka.*, u.name AS author_name, fa.path AS cover_path')
                ->join('users u', 'u.id = ka.author_id', 'left')
                ->join('file_assets fa', 'fa.id = ka.cover_file_id', 'left')
                ->where('ka.workflow_status', 'published')
                ->where('ka.visibility', 'public')
                ->where('ka.deleted_at', null)
                ->where('ka.type', 'article')
                ->where('ka.title NOT LIKE', '%bbbnjmnm%')
                ->where('ka.title NOT LIKE', '%Test Artikel%')
                ->orderBy('ka.published_at', 'DESC')
                ->orderBy('ka.id', 'DESC')
                ->limit(3)
                ->get()->getResultArray();

            // 3 Template Dokumen Terbaru
            $templates = $db->table('knowledge_articles ka')
                ->select('ka.*, fa.path AS cover_path, fa.mime_type')
                ->join('file_assets fa', 'fa.id = ka.cover_file_id', 'left')
                ->where('ka.workflow_status', 'published')
                ->where('ka.visibility', 'public')
                ->where('ka.deleted_at', null)
                ->where('ka.type', 'template')
                ->where('ka.title NOT LIKE', '%zfdfdfsdfd%')
                ->orderBy('ka.published_at', 'DESC')
                ->orderBy('ka.id', 'DESC')
                ->limit(3)
                ->get()->getResultArray();

            // 3 Studi Kasus Terbaru
            $caseStudies = $db->table('knowledge_articles ka')
                ->select('ka.*, u.name AS author_name')
                ->join('users u', 'u.id = ka.author_id', 'left')
                ->where('ka.workflow_status', 'published')
                ->where('ka.visibility', 'public')
                ->where('ka.deleted_at', null)
                ->where('ka.type', 'case_study')
                ->where('ka.title NOT LIKE', '%xvxxb%')
                ->where('ka.title NOT LIKE', '%QA AUDIT%')
                ->orderBy('ka.published_at', 'DESC')
                ->orderBy('ka.id', 'DESC')
                ->limit(3)
                ->get()->getResultArray();

            // 3 FAQ Terbaru
            $faqs = $db->table('knowledge_articles ka')
                ->where('ka.workflow_status', 'published')
                ->where('ka.visibility', 'public')
                ->where('ka.deleted_at', null)
                ->where('ka.type', 'faq')
                ->where('ka.title NOT LIKE', '%zccsass%')
                ->where('ka.title NOT LIKE', '%QA AUDIT%')
                ->orderBy('ka.id', 'ASC')
                ->limit(3)
                ->get()->getResultArray();

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
        } catch (\Throwable) {
        }

        // 5. Procurement Stages (Tahapan Siklus PBJ)
        $stages = [];
        try {
            $stages = $db->table('procurement_stages')
                ->where('is_active', 1)
                ->where('name NOT LIKE', '%QA AUDIT%')
                ->orderBy('sort_order', 'ASC')
                ->get()->getResultArray();
        } catch (\Throwable) {
        }

        // 6. Upcoming Events / Bimtek
        // 3 Event Terdekat Hari Ini / Sedang Berlangsung & 3 Event Terjauh Mendatang
        $upcomingEventsNear = [];
        $upcomingEventsFar  = [];
        $nowDatetime        = date('Y-m-d H:i:s');
        $todayDate          = date('Y-m-d');

        try {
            // Set 1: 3 Event Terdekat / Sedang Berlangsung dari Hari Ini
            $upcomingEventsNear = $db->table('events e')
                ->select('e.*, fa.path AS cover_path, COALESCE(u.name, u.username, "Biro PBJ Setda Lampung") AS creator_name')
                ->join('file_assets fa', 'fa.id = e.cover_file_id', 'left')
                ->join('users u', 'u.id = e.created_by', 'left')
                ->where('e.deleted_at', null)
                ->where('e.visibility', 'public')
                ->whereIn('e.status', ['open_registration', 'scheduled', 'ongoing', 'coming_soon'])
                ->where('e.end_at >=', $nowDatetime)
                ->where('e.title NOT LIKE', '%QA AUDIT%')
                ->where('e.title NOT LIKE', '%Simulasi pengujian%')
                ->where('e.title NOT LIKE', '%Pengujian serti%')
                ->orderBy('CASE WHEN e.start_at <= "' . $nowDatetime . '" AND e.end_at >= "' . $nowDatetime . '" THEN 0 ELSE 1 END', 'ASC', false)
                ->orderBy('e.start_at', 'ASC')
                ->limit(3)
                ->get()->getResultArray();

            // Set 2: 3 Event Terbaru Berdasarkan Tanggal yang Akan Datang Paling Jauh (start_at DESC)
            $upcomingEventsFar = $db->table('events e')
                ->select('e.*, fa.path AS cover_path, COALESCE(u.name, u.username, "Biro PBJ Setda Lampung") AS creator_name')
                ->join('file_assets fa', 'fa.id = e.cover_file_id', 'left')
                ->join('users u', 'u.id = e.created_by', 'left')
                ->where('e.deleted_at', null)
                ->where('e.visibility', 'public')
                ->whereIn('e.status', ['open_registration', 'scheduled', 'ongoing', 'coming_soon'])
                ->where('e.start_at >=', $todayDate . ' 00:00:00')
                ->where('e.title NOT LIKE', '%QA AUDIT%')
                ->where('e.title NOT LIKE', '%Simulasi pengujian%')
                ->where('e.title NOT LIKE', '%Pengujian serti%')
                ->orderBy('e.start_at', 'DESC')
                ->limit(3)
                ->get()->getResultArray();
        } catch (\Throwable) {
        }
        $upcomingEvents = $upcomingEventsNear;

        // 7. Visitor Statistics Tracking (Non-unique page views)
        $visitorStats = [];
        try {
            $visitorService = new \App\Services\VisitorStatisticService();
            $visitorService->recordHit('home');
            $visitorStats = $visitorService->getVisitorStats();
        } catch (\Throwable) {
        }

        // 8. Dynamic Landing Page CMS Data (Sliders, Announcements, Highlights, Portals, Testimonials, Team)
        $sliders = [];
        try {
            $sliders = $db->table('home_sliders')
                ->where('is_active', 1)
                ->orderBy('sort_order', 'ASC')
                ->get()->getResultArray();
        } catch (\Throwable) {
        }

        $announcements = [];
        try {
            $announcements = $db->table('site_announcements')
                ->where('is_active', 1)
                ->orderBy('sort_order', 'ASC')
                ->get()->getResultArray();
        } catch (\Throwable) {
        }

        $featureHighlights = [];
        try {
            $featureHighlights = $db->table('feature_highlights')
                ->where('is_active', 1)
                ->orderBy('sort_order', 'ASC')
                ->get()->getResultArray();
        } catch (\Throwable) {
        }

        $externalPortals = [];
        try {
            $externalPortals = $db->table('external_portals')
                ->where('is_active', 1)
                ->orderBy('sort_order', 'ASC')
                ->get()->getResultArray();
        } catch (\Throwable) {
        }

        $testimonials = [];
        try {
            $testimonials = $db->table('testimonials')
                ->where('is_active', 1)
                ->orderBy('sort_order', 'ASC')
                ->get()->getResultArray();
        } catch (\Throwable) {
        }

        $teamMembers = [];
        try {
            $teamMembers = $db->table('team_members')
                ->where('is_active', 1)
                ->orderBy('sort_order', 'ASC')
                ->get()->getResultArray();
        } catch (\Throwable) {
        }

        return view('landing/home/index', [
            'isHome'            => true,
            'title'             => 'BegawiPengadaan | Platform Bimbingan Edukasi & Gerakan Akselerasi Wawasan PBJ',
            'pageTitle'         => 'BegawiPengadaan | Transformasi Digital Bimbingan Edukasi & Gerakan Akselerasi Wawasan PBJ',
            'metaDescription'   => 'Platform resmi terintegrasi Pembelajaran Pengadaan (Arena Belajar), Repositori Pengetahuan (Bunga Rampai), dan Pusat Regulasi Pengadaan Barang/Jasa Pemerintah Provinsi Lampung.',
            'metaKeywords'      => 'BegawiPengadaan, PBJ Lampung, Biro PBJ Setda Lampung, Arena Belajar, Bunga Rampai, Regulasi PBJ, Pengadaan Barang Jasa Pemerintah, LKPP, Transformasi Digital Bimbingan Edukasi & Gerakan Akselerasi Wawasan PBJ',
            'stats'              => $stats,
            'courses'            => $courses,
            'latestClasses'      => $latestClasses,
            'regulations'        => $regulations,
            'articles'           => $articles,
            'templates'          => $templates,
            'caseStudies'        => $caseStudies,
            'faqs'               => $faqs,
            'knowledgeCounts'    => $knowledgeCounts,
            'mediaItems'         => $mediaItems,
            'stages'             => $stages,
            'upcomingEvents'     => $upcomingEvents,
            'upcomingEventsNear' => $upcomingEventsNear,
            'upcomingEventsFar'  => $upcomingEventsFar,
            'visitorStats'      => $visitorStats,
            'sliders'           => $sliders,
            'announcements'     => $announcements,
            'featureHighlights' => $featureHighlights,
            'externalPortals'   => $externalPortals,
            'testimonials'      => $testimonials,
            'teamMembers'       => $teamMembers,
        ]);
    }

    public function about(): string
    {
        $db = db_connect();

        $stats = [
            'total_regulations'  => 0,
            'total_courses'      => 0,
            'total_certificates' => 0,
            'total_users'        => 0,
            'total_knowledge'    => 0,
        ];

        try {
            $stats['total_regulations'] = (int) $db->table('regulations')
                ->where('deleted_at', null)
                ->where('workflow_status', 'published')
                ->countAllResults();
        } catch (\Throwable) {
        }

        try {
            $stats['total_courses'] = (int) $db->table('courses')
                ->where('deleted_at', null)
                ->where('status', 'published')
                ->countAllResults();
        } catch (\Throwable) {
        }

        try {
            $stats['total_certificates'] = (int) $db->table('certificates')
                ->where('status', 'issued')
                ->countAllResults();
        } catch (\Throwable) {
        }

        try {
            $stats['total_users'] = (int) $db->table('users')
                ->where('deleted_at', null)
                ->where('account_status', 'active')
                ->countAllResults();
        } catch (\Throwable) {
        }

        try {
            $stats['total_knowledge'] = (int) $db->table('knowledge_articles')
                ->where('deleted_at', null)
                ->where('workflow_status', 'published')
                ->countAllResults();
        } catch (\Throwable) {
        }

        $pageBanner = null;
        try {
            $pageBanner = $db->table('page_banners')
                ->where('page_key', 'about')
                ->get()->getRowArray();
        } catch (\Throwable) {
        }

        $teamMembers = [];
        try {
            $teamMembers = $db->table('team_members')
                ->where('is_active', 1)
                ->orderBy('sort_order', 'ASC')
                ->get()->getResultArray();
        } catch (\Throwable) {
        }

        $featureHighlights = [];
        try {
            $featureHighlights = $db->table('feature_highlights')
                ->where('is_active', 1)
                ->orderBy('sort_order', 'ASC')
                ->get()->getResultArray();
        } catch (\Throwable) {
        }

        return view('landing/pages/about', [
            'title'             => 'Tentang BegawiPengadaan | Biro PBJ Setda Provinsi Lampung',
            'metaDescription'   => 'Profil resmi, visi, misi, nilai budaya kerja PATIK, struktur organisasi, dan 5 pilar ekosistem digital Biro Pengadaan Barang dan Jasa Sekretariat Daerah Provinsi Lampung.',
            'metaKeywords'      => 'tentang BegawiPengadaan, Biro PBJ Lampung, UKPBJ Lampung, pengadaan barang jasa Lampung, visi misi PBJ Lampung',
            'breadcrumbs'       => [
                ['label' => 'Tentang Kami'],
            ],
            'stats'             => $stats,
            'pageBanner'        => $pageBanner,
            'teamMembers'       => $teamMembers,
            'featureHighlights' => $featureHighlights,
        ]);
    }

    public function contact(): string
    {
        $db = db_connect();

        $contactInfo = [
            'phone'          => setting('organization.phone', '(0721) 481103'),
            'whatsapp'       => setting('helpdesk.wa_phone', '+62 851-1777-9783'),
            'whatsapp_link'  => 'https://wa.me/' . preg_replace('/[^0-9]/', '', setting('helpdesk.wa_phone', '6285117779783')) . '?text=' . rawurlencode('Halo Helpdesk Biro PBJ Setda Provinsi Lampung, saya ingin berkonsultasi mengenai: '),
            'email'          => setting('organization.email', 'biropbj@lampungprov.go.id'),
            'email_alt'      => setting('organization.email_alt', 'lpse.lampung@gmail.com'),
            'address'        => setting('organization.address', 'Gedung Biro Pengadaan Barang dan Jasa, Kompleks Kantor Gubernur Provinsi Lampung, Jl. Wolter Monginsidi No. 69, Teluk Betung, Bandar Lampung 35211'),
            'hours_weekday'  => setting('organization.hours_weekday', 'Senin – Kamis: 08.00 – 16.00 WIB'),
            'hours_friday'   => setting('organization.hours_friday', 'Jumat: 08.00 – 15.30 WIB'),
            'hours_weekend'  => setting('organization.hours_weekend', 'Sabtu – Minggu & Hari Libur Nasional: Tutup'),
            'maps_embed_url' => setting('organization.maps_embed_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3971.970891823157!2d105.2570003!3d-5.4388889!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e40da4ea912bb29%3A0x633da4b46c645479!2sKantor%20Gubernur%20Lampung!5e0!3m2!1sid!2sid!4v1700000000000'),
            'maps_dir_link'  => setting('organization.maps_dir_link', 'https://maps.google.com/?q=Kantor+Gubernur+Lampung'),
        ];

        // Align service categories with Workspace Helpdesk categories
        $serviceCategories = \App\Controllers\Workspace\ConsultationController::getCategories();

        $pageBanner = null;
        try {
            $pageBanner = $db->table('page_banners')
                ->where('page_key', 'contact')
                ->get()->getRowArray();
        } catch (\Throwable) {
        }

        // Fitur Pelacakan Tiket (Lacak Tiket via No. Tiket)
        $trackedTicket = null;
        $trackCode = trim((string) $this->request->getGet('lacak'));
        if ($trackCode !== '') {
            try {
                $ticketRow = $db->table('contact_messages')->where('ticket_number', $trackCode)->get()->getRowArray();
                if ($ticketRow) {
                    $notes = \App\Models\Communication\ContactMessageModel::parseAdminNotes($ticketRow['admin_notes'] ?? null);
                    $ticketRow['public_threads'] = array_values(array_filter($notes['threads'] ?? [], static fn($th) => ($th['channel'] ?? 'portal') !== 'internal'));
                    $ticketRow['parsed_notes']   = $notes;
                    $trackedTicket = $ticketRow;
                }
            } catch (\Throwable) {
            }
        }

        // Generate Simple Math Captcha
        $cNum1 = rand(2, 9);
        $cNum2 = rand(1, 9);
        session()->set('contact_captcha_answer', $cNum1 + $cNum2);
        $captchaQuestion = "{$cNum1} + {$cNum2}";

        return view('landing/pages/contact', [
            'title'             => 'Hubungi Kami & Helpdesk PBJ | Biro Pengadaan Barang dan Jasa Lampung',
            'metaDescription'   => 'Layanan informasi resmi, konsultasi teknis, dan helpdesk LPSE Biro Pengadaan Barang dan Jasa Sekretariat Daerah Provinsi Lampung.',
            'metaKeywords'      => 'kontak BegawiPengadaan, helpdesk LPSE Lampung, biro PBJ Setda Lampung, alamat kantor gubernur lampung',
            'breadcrumbs'       => [
                ['label' => 'Hubungi Kami'],
            ],
            'contact'           => $contactInfo,
            'serviceCategories' => $serviceCategories,
            'pageBanner'        => $pageBanner,
            'trackedTicket'     => $trackedTicket,
            'trackCode'         => $trackCode,
            'captchaQuestion'   => $captchaQuestion,
        ]);
    }

    public function sendContact()
    {
        $throttler = service('throttler');
        $clientIp = $this->request->getIPAddress();
        if ($throttler->check('contact_' . md5($clientIp), 5, 60) === false) {
            return redirect()->back()
                ->withInput()
                ->with('contact_error', 'Terlalu banyak permintaan pengiriman pesan dari perangkat Anda. Silakan tunggu satu menit sebelum mencoba kembali.');
        }

        // Honeypot anti-bot validation
        $honeypot = trim((string) $this->request->getPost('website_url'));
        if ($honeypot !== '') {
            return redirect()->to(site_url('kontak#form-pesan'))->with('contact_error', 'Permintaan pengiriman tidak dapat diproses.');
        }

        // Simple Math Captcha validation
        $userCaptcha = trim((string) $this->request->getPost('captcha_answer'));
        $expectedCaptcha = session()->get('contact_captcha_answer');
        if ($expectedCaptcha !== null && (int) $userCaptcha !== (int) $expectedCaptcha) {
            return redirect()->back()
                ->withInput()
                ->with('contact_error', 'Jawaban verifikasi keamanan (captcha matematika) belum tepat. Silakan hitung kembali.');
        }

        $rules = [
            'name'         => 'required|min_length[3]|max_length[100]',
            'email'        => 'required|valid_email|max_length[150]',
            'phone'        => 'permit_empty|min_length[8]|max_length[20]',
            'organization' => 'permit_empty|max_length[150]',
            'service_type' => 'permit_empty|max_length[100]',
            'subject'      => 'required|min_length[3]|max_length[200]',
            'message'      => 'required|min_length[10]|max_length[3000]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('contact_error', 'Mohon lengkapi formulir dengan data yang valid: ' . implode(' ', $this->validator->getErrors()));
        }

        // Simpan ke database contact_messages
        $db = \Config\Database::connect();
        $ticketNumber = 'TKT-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));

        $isLoggedIn = session()->get('isLoggedIn') === true;
        $userId     = $isLoggedIn ? (int) session()->get('user_id') : null;
        $senderName = substr((string) $this->request->getPost('name'), 0, 150);

        // Optional attachment upload
        $attachmentData = null;
        $file = $this->request->getFile('attachment');
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $allowedExt = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'webp', 'zip', 'rar'];
            $ext = strtolower((string) $file->getClientExtension());
            if (! in_array($ext, $allowedExt, true)) {
                return redirect()->back()->withInput()->with('contact_error', 'Format berkas lampiran tidak didukung. Gunakan PDF, Office, Gambar, atau ZIP.');
            }

            $mimeType = $file->getMimeType();
            $disallowedMimes = ['application/x-php', 'text/x-php', 'application/x-httpd-php', 'application/x-sh', 'application/x-msdos-program', 'application/x-executable'];
            if (in_array(strtolower($mimeType), $disallowedMimes, true) || str_contains($mimeType, 'php') || str_contains($mimeType, 'script')) {
                return redirect()->back()->withInput()->with('contact_error', 'Tipe berkas tidak diizinkan demi alasan keamanan sistem.');
            }

            if ($file->getSize() > 10 * 1024 * 1024) {
                return redirect()->back()->withInput()->with('contact_error', 'Ukuran berkas lampiran maksimal 1 MB.');
            }

            $origName = $file->getClientName();
            $sizeBytes = (int) $file->getSize();
            $newName = 'pub_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $targetDir = FCPATH . 'uploads/consultations';
            if (! is_dir($targetDir)) {
                @mkdir($targetDir, 0775, true);
            }
            $file->move($targetDir, $newName);

            $sizeHuman = $sizeBytes >= 1048576
                ? number_format($sizeBytes / 1048576, 2) . ' MB'
                : number_format(max(1, $sizeBytes / 1024), 1) . ' KB';
            $relativePath = 'uploads/consultations/' . $newName;

            $fileAssetId = null;
            try {
                if ($db->tableExists('file_assets')) {
                    $db->table('file_assets')->insert([
                        'storage_disk'  => 'public',
                        'file_path'     => $relativePath,
                        'original_name' => $origName,
                        'mime_type'     => $mimeType,
                        'file_size'     => $sizeBytes,
                        'uploaded_by'   => $userId ?: null,
                        'created_at'    => date('Y-m-d H:i:s'),
                    ]);
                    $fileAssetId = (int) $db->insertID();
                }
            } catch (\Throwable) {
            }

            $attachmentData = [
                'file_id'    => $fileAssetId,
                'name'       => $origName,
                'path'       => $relativePath,
                'url'        => base_url($relativePath),
                'ext'        => strtoupper($ext),
                'mime'       => $mimeType,
                'size_bytes' => $sizeBytes,
                'size_human' => $sizeHuman,
            ];
        }

        $detailLog = 'Tiket konsultasi diajukan melalui portal publik.' . ($isLoggedIn ? ' (Pengguna Terdaftar)' : '');
        if ($attachmentData) {
            $detailLog .= ' Disertai lampiran: ' . $attachmentData['name'];
        }

        $initialNotes = [
            'user_id'       => $userId,
            'priority'      => 'normal',
            'assigned_to'   => null,
            'internal_note' => '',
            'attachments'   => $attachmentData ? [$attachmentData] : [],
            'threads'       => [],
            'logs'          => [
                [
                    'time'       => date('Y-m-d H:i:s'),
                    'actor'      => $senderName,
                    'actor_role' => $isLoggedIn ? 'user' : 'guest',
                    'action'     => 'Tiket Dibuat',
                    'detail'     => $detailLog,
                ],
            ],
        ];

        $insertId = 0;
        $db->transStart();
        try {
            $db->table('contact_messages')->insert([
                'ticket_number'    => $ticketNumber,
                'sender_name'      => $senderName,
                'sender_email'     => substr((string) $this->request->getPost('email'), 0, 150),
                'sender_phone'     => substr((string) $this->request->getPost('phone'), 0, 50),
                'service_category' => substr((string) $this->request->getPost('service_type'), 0, 100),
                'subject'          => substr((string) $this->request->getPost('subject'), 0, 255),
                'message'          => (string) $this->request->getPost('message'),
                'status'           => 'unread',
                'admin_notes'      => json_encode($initialNotes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ]);
            $insertId = (int) $db->insertID();

            // Catat log audit di dalam transaksi
            $auditService = new \App\Services\AuditService();
            $auditService->log($userId, 'contact.inquiry_submitted', 'contact_messages', $ticketNumber, 0, [
                'name'         => substr((string) $this->request->getPost('name'), 0, 100),
                'email'        => substr((string) $this->request->getPost('email'), 0, 100),
                'service_type' => substr((string) $this->request->getPost('service_type'), 0, 100),
                'subject'      => substr((string) $this->request->getPost('subject'), 0, 150),
            ]);
            $db->transComplete();

            if ($db->transStatus()) {
                try {
                    (new \App\Services\NotificationService())->sendShared(
                        'shared:helpdesk',
                        'contact_inquiry_received',
                        "Pesan Masuk Publik Baru #{$ticketNumber}",
                        "Pesan dari {$senderName}: \"" . mb_strimwidth((string) $this->request->getPost('subject'), 0, 100, '...') . "\"",
                        [
                            'ticket_number' => $ticketNumber,
                            'sender_name'   => $senderName,
                            'url'           => site_url('dashboard/komunikasi/pesan-masuk/detail/' . $insertId),
                        ],
                        $userId ?: 1
                    );
                } catch (\Throwable) {
                }
            }
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Contact message save error: ' . $e->getMessage());
        }

        // Refresh captcha for next request
        session()->remove('contact_captcha_answer');

        if ($isLoggedIn) {
            $successMsg = 'Terima kasih! Tiket ' . $ticketNumber . ' berhasil dibuat. Karena Anda telah masuk, Anda dapat melihat riwayat dan saling membalas pesan secara langsung melalui Ruang Kerja (Workspace) Anda.';
            return redirect()->to(site_url('workspace/konsultasi/detail/' . $ticketNumber))->with('success', $successMsg);
        }

        return redirect()->to(site_url('kontak#form-pesan'))
            ->with('contact_success', 'Terima kasih atas pesan Anda! Tiket ' . $ticketNumber . ' berhasil dibuat dan telah diteruskan ke Helpdesk Biro Pengadaan Barang dan Jasa Provinsi Lampung. Anda dapat memantau statusnya di form lacak tiket dengan nomor ' . $ticketNumber . '.');
    }

    public function subscribeNewsletter()
    {
        $throttler = service('throttler');
        $clientIp = $this->request->getIPAddress();
        if ($throttler->check('newsletter_' . md5($clientIp), 5, 60) === false) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(429)->setJSON(['status' => 'error', 'message' => 'Terlalu banyak permintaan. Silakan tunggu beberapa saat lagi.']);
            }
            return redirect()->back()->with('newsletter_error', 'Terlalu banyak permintaan. Silakan tunggu beberapa saat lagi.');
        }

        $email = trim((string) $this->request->getPost('email'));
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Alamat email tidak valid.']);
            }
            return redirect()->back()->with('newsletter_error', 'Alamat email tidak valid.');
        }

        $db = \Config\Database::connect();
        $db->transStart();
        try {
            $existing = $db->table('newsletter_subscribers')->where('email', $email)->get()->getRow();
            if (! $existing) {
                $db->table('newsletter_subscribers')->insert([
                    'email'         => $email,
                    'status'        => 'subscribed',
                    'subscribed_at' => date('Y-m-d H:i:s'),
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s'),
                ]);
            }
            $db->transComplete();
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Newsletter subscribe DB error: ' . $e->getMessage());
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Terima kasih telah berlangganan newsletter BegawiPengadaan!']);
        }
        return redirect()->back()->with('newsletter_success', 'Terima kasih telah berlangganan newsletter BegawiPengadaan!');
    }


    public function survey(): string
    {
        $surveyModel = new \App\Models\SatisfactionSurveyModel();
        $stats = $surveyModel->getAggregateStats();

        return view('landing/survey/index', [
            'pageTitle'       => 'Survei Kepuasan Pengguna (IKM) | BegawiPengadaan Provinsi Lampung',
            'metaDescription' => 'Survei Kepuasan Masyarakat (IKM) Biro Pengadaan Barang dan Jasa Provinsi Lampung. Sampaikan aspirasi, penilaian, dan evaluasi layanan Anda secara transparan dan akuntabel.',
            'breadcrumbs'     => [
                ['label' => 'Beranda', 'url' => site_url('/')],
                ['label' => 'Survei Kepuasan Pengguna'],
            ],
            'stats'           => $stats,
        ]);
    }

    public function submitSurvey()
    {
        $throttler = service('throttler');
        $clientIp = $this->request->getIPAddress();
        if ($throttler->check('survey_' . md5($clientIp), 5, 60) === false) {
            if ($this->request->isAJAX() || $this->request->getHeaderLine('Accept') === 'application/json' || $this->request->getGet('ajax') === '1') {
                return $this->response->setStatusCode(429)->setJSON([
                    'success' => false,
                    'message' => 'Terlalu banyak pengiriman survei dari perangkat Anda. Silakan tunggu beberapa saat.',
                ]);
            }
            return redirect()->back()->with('error', 'Terlalu banyak pengiriman survei dari perangkat Anda. Silakan tunggu beberapa saat.');
        }

        $rating = (int) $this->request->getPost('rating_overall');
        if ($rating < 1 || $rating > 5) {
            $rating = 5;
        }

        $isAnonymous = $this->request->getPost('is_anonymous') !== '0';
        $respondentRole = trim((string) $this->request->getPost('respondent_role'));
        if ($respondentRole === '') {
            if ($this->request->isAJAX() || $this->request->getHeaderLine('Accept') === 'application/json' || $this->request->getGet('ajax') === '1') {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Silakan pilih Kategori / Peran Pengguna terlebih dahulu.',
                ]);
            }
            return redirect()->back()->with('error', 'Silakan pilih Kategori / Peran Pengguna terlebih dahulu.');
        }
        $name = $isAnonymous ? 'Anonim' : trim((string) ($this->request->getPost('respondent_name') ?: 'Anonim'));
        $institution = $isAnonymous ? null : trim((string) $this->request->getPost('respondent_institution'));
        $contact = $isAnonymous ? null : trim((string) $this->request->getPost('respondent_contact'));
        $feedback = trim((string) $this->request->getPost('feedback'));
        $recommendation = trim((string) ($this->request->getPost('recommendation') ?: 'Pasti Merekomendasikan'));

        $serviceAccess = (int) $this->request->getPost('service_access_rating');
        $serviceRegulation = (int) $this->request->getPost('service_regulation_rating');
        $serviceLearning = (int) $this->request->getPost('service_learning_rating');
        $serviceHelpdesk = (int) $this->request->getPost('service_helpdesk_rating');

        $surveyModel = new \App\Models\SatisfactionSurveyModel();
        $ticketNumber = $surveyModel->generateTicketNumber();

        $data = [
            'ticket_number'              => $ticketNumber,
            'rating_overall'             => $rating,
            'service_access_rating'      => ($serviceAccess >= 1 && $serviceAccess <= 5) ? $serviceAccess : $rating,
            'service_regulation_rating'  => ($serviceRegulation >= 1 && $serviceRegulation <= 5) ? $serviceRegulation : $rating,
            'service_learning_rating'    => ($serviceLearning >= 1 && $serviceLearning <= 5) ? $serviceLearning : $rating,
            'service_helpdesk_rating'    => ($serviceHelpdesk >= 1 && $serviceHelpdesk <= 5) ? $serviceHelpdesk : $rating,
            'respondent_role'            => substr($respondentRole, 0, 100),
            'is_anonymous'               => $isAnonymous ? 1 : 0,
            'respondent_name'            => substr($name, 0, 150),
            'respondent_institution'     => $institution ? substr($institution, 0, 150) : null,
            'respondent_contact'         => $contact ? substr($contact, 0, 100) : null,
            'feedback'                   => $feedback ? substr($feedback, 0, 2000) : null,
            'recommendation'             => substr($recommendation, 0, 50),
            'ip_address'                 => substr((string) $this->request->getIPAddress(), 0, 45),
            'user_agent'                 => substr((string) $this->request->getUserAgent(), 0, 255),
        ];

        $db = \Config\Database::connect();
        $db->transStart();
        try {
            $surveyModel->insert($data);
            $auditService = new \App\Services\AuditService();
            $auditService->log(null, 'survey.satisfaction_submitted', 'survey', 'satisfaction_surveys', 0, [
                'ticket_number'   => $ticketNumber,
                'rating_overall'  => $rating,
                'respondent_role' => $respondentRole,
            ]);
            $db->transComplete();
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Satisfaction Survey insert error: ' . $e->getMessage());
        }

        $indonesianMonths = [
            'January' => 'Januari',
            'February' => 'Februari',
            'March' => 'Maret',
            'April' => 'April',
            'May' => 'Mei',
            'June' => 'Juni',
            'July' => 'Juli',
            'August' => 'Agustus',
            'September' => 'September',
            'October' => 'Oktober',
            'November' => 'November',
            'December' => 'Desember',
        ];
        $formattedDate = strtr(date('d F Y, H:i'), $indonesianMonths) . ' WIB';

        $ratingLabels = [
            1 => 'Sangat Tidak Puas',
            2 => 'Kurang Puas',
            3 => 'Cukup Puas',
            4 => 'Puas',
            5 => 'Sangat Puas',
        ];

        if ($this->request->isAJAX() || $this->request->getHeaderLine('Accept') === 'application/json' || $this->request->getGet('ajax') === '1') {
            return $this->response->setJSON([
                'success'         => true,
                'ticket_number'   => $ticketNumber,
                'rating_overall'  => $rating,
                'rating_label'    => $ratingLabels[$rating] ?? 'Puas',
                'respondent_role' => $respondentRole,
                'submitted_at'    => $formattedDate,
                'message'         => 'Terima kasih atas partisipasi Anda! Penilaian Anda telah berhasil tercatat dalam Sistem IKM BegawiPengadaan.',
            ]);
        }

        return redirect()->to(site_url('survei-kepuasan'))
            ->with('survey_success', true)
            ->with('ticket_number', $ticketNumber)
            ->with('rating_overall', $rating)
            ->with('rating_label', $ratingLabels[$rating] ?? 'Puas')
            ->with('respondent_role', $respondentRole)
            ->with('submitted_at', $formattedDate);
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
            'title' => $statusCode . ' | ' . $pageTitle . ' | BegawiPengadaan',
            'metaDescription' => $message,
            'pageTitle' => $pageTitle,
            'message' => $message,
            'statusCode' => $statusCode,
        ]);
    }
}
