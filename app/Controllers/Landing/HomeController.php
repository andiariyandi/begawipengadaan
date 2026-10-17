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

        // 2. Featured / Popular Courses (Arena Belajar)
        $courses = [];
        try {
            $courses = $db->table('courses c')
                ->select('c.*, p.title AS program_title, p.slug AS program_slug, fa.path AS cover_asset_path,
                         (SELECT COUNT(*) FROM course_enrollments ce WHERE ce.course_id = c.id AND ce.status != "cancelled") AS enrollments_count')
                ->join('programs p', 'p.id = c.program_id', 'left')
                ->join('file_assets fa', 'fa.id = c.cover_file_id', 'left')
                ->where('c.status', 'published')
                ->where('c.visibility', 'public')
                ->where('c.deleted_at', null)
                ->where('c.title NOT LIKE', '%bbbb%')
                ->where('c.title NOT LIKE', '%Fixture%')
                ->orderBy('c.featured', 'DESC')
                ->orderBy('c.id', 'DESC')
                ->limit(6)
                ->get()->getResultArray();
        } catch (\Throwable) {
        }

        // 3. Featured & Latest Regulations (Regulasi PBJ)
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
                ->orderBy('r.is_featured', 'DESC')
                ->orderBy('r.document_year', 'DESC')
                ->orderBy('r.id', 'DESC')
                ->limit(6)
                ->get()->getResultArray();
        } catch (\Throwable) {
        }

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
                ->where('ka.title NOT LIKE', '%bbbnjmnm%')
                ->where('ka.title NOT LIKE', '%Test Artikel%')
                ->orderBy('ka.featured', 'DESC')
                ->orderBy('ka.published_at', 'DESC')
                ->orderBy('ka.id', 'DESC')
                ->limit(4)
                ->get()->getResultArray();
        } catch (\Throwable) {
        }

        try {
            $templates = $db->table('knowledge_articles ka')
                ->select('ka.*, fa.path AS cover_path, fa.mime_type')
                ->join('file_assets fa', 'fa.id = ka.cover_file_id', 'left')
                ->where('ka.workflow_status', 'published')
                ->where('ka.visibility', 'public')
                ->where('ka.deleted_at', null)
                ->where('ka.type', 'template')
                ->where('ka.title NOT LIKE', '%zfdfdfsdfd%')
                ->orderBy('ka.download_count', 'DESC')
                ->orderBy('ka.id', 'DESC')
                ->limit(4)
                ->get()->getResultArray();
        } catch (\Throwable) {
        }

        try {
            $caseStudies = $db->table('knowledge_articles ka')
                ->select('ka.*, u.name AS author_name')
                ->join('users u', 'u.id = ka.author_id', 'left')
                ->where('ka.workflow_status', 'published')
                ->where('ka.visibility', 'public')
                ->where('ka.deleted_at', null)
                ->where('ka.type', 'case_study')
                ->where('ka.title NOT LIKE', '%xvxxb%')
                ->where('ka.title NOT LIKE', '%QA AUDIT%')
                ->orderBy('ka.id', 'DESC')
                ->limit(4)
                ->get()->getResultArray();
        } catch (\Throwable) {
        }

        try {
            $faqs = $db->table('knowledge_articles ka')
                ->where('ka.workflow_status', 'published')
                ->where('ka.visibility', 'public')
                ->where('ka.deleted_at', null)
                ->where('ka.type', 'faq')
                ->where('ka.title NOT LIKE', '%zccsass%')
                ->where('ka.title NOT LIKE', '%QA AUDIT%')
                ->orderBy('ka.id', 'ASC')
                ->limit(5)
                ->get()->getResultArray();
        } catch (\Throwable) {
        }

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
        $upcomingEvents = [];
        try {
            $upcomingEvents = $db->table('events e')
                ->select('e.*, fa.path AS cover_path, COALESCE(u.name, u.username, "Biro PBJ Setda Lampung") AS creator_name')
                ->join('file_assets fa', 'fa.id = e.cover_file_id', 'left')
                ->join('users u', 'u.id = e.created_by', 'left')
                ->where('e.deleted_at', null)
                ->where('e.visibility', 'public')
                ->whereIn('e.status', ['open_registration', 'scheduled', 'ongoing'])
                ->where('e.start_at >', '2026-01-01')
                ->where('e.title NOT LIKE', '%QA AUDIT%')
                ->where('e.title NOT LIKE', '%Simulasi pengujian%')
                ->where('e.title NOT LIKE', '%Pengujian serti%')
                ->orderBy('e.start_at', 'ASC')
                ->limit(3)
                ->get()->getResultArray();
        } catch (\Throwable) {
        }

        // 7. Visitor Statistics Tracking (Non-unique page views)
        $visitorStats = [];
        try {
            $visitorService = new \App\Services\VisitorStatisticService();
            $visitorService->recordHit('home');
            $visitorStats = $visitorService->getVisitorStats();
        } catch (\Throwable) {
        }

        return view('landing/home/index', [
            'isHome'           => true,
            'title'            => 'BegawiPengadaan | Platform Pengadaan Barang/Jasa Pemerintah Provinsi Lampung',
            'pageTitle'        => 'BegawiPengadaan | Transformasi Digital Pengadaan Barang/Jasa Provinsi Lampung',
            'metaDescription'  => 'Platform resmi terintegrasi Pembelajaran Pengadaan (Arena Belajar), Repositori Pengetahuan (Bunga Rampai), dan Pusat Regulasi Pengadaan Barang/Jasa Pemerintah Provinsi Lampung.',
            'metaKeywords'     => 'BegawiPengadaan, PBJ Lampung, Biro PBJ Setda Lampung, Arena Belajar, Bunga Rampai, Regulasi PBJ, Pengadaan Barang Jasa Pemerintah, LKPP',
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
            'visitorStats'     => $visitorStats,
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

        return view('landing/pages/about', [
            'title'           => 'Tentang BegawiPengadaan | Biro PBJ Setda Provinsi Lampung',
            'metaDescription' => 'Profil resmi, visi, misi, nilai budaya kerja PATIK, struktur organisasi, dan 5 pilar ekosistem digital Biro Pengadaan Barang dan Jasa Sekretariat Daerah Provinsi Lampung.',
            'metaKeywords'    => 'tentang BegawiPengadaan, Biro PBJ Lampung, UKPBJ Lampung, pengadaan barang jasa Lampung, visi misi PBJ Lampung',
            'breadcrumbs'     => [
                ['label' => 'Tentang Kami'],
            ],
            'stats'           => $stats,
        ]);
    }

    public function contact(): string
    {
        $contactInfo = [
            'phone'          => '(0721) 481103',
            'whatsapp'       => '+62 851-1777-9783',
            'whatsapp_link'  => 'https://wa.me/6285117779783?text=' . rawurlencode('Halo Helpdesk Biro PBJ Setda Provinsi Lampung, saya ingin berkonsultasi mengenai: '),
            'email'          => 'biropbj@lampungprov.go.id',
            'email_alt'      => 'lpse.lampung@gmail.com',
            'address'        => 'Gedung Biro Pengadaan Barang dan Jasa, Kompleks Kantor Gubernur Provinsi Lampung, Jl. Wolter Monginsidi No. 69, Teluk Betung, Bandar Lampung 35211',
            'hours_weekday'  => 'Senin – Kamis: 08.00 – 16.00 WIB',
            'hours_friday'   => 'Jumat: 08.00 – 15.30 WIB',
            'hours_weekend'  => 'Sabtu – Minggu & Hari Libur Nasional: Tutup',
            'maps_embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3971.970891823157!2d105.2570003!3d-5.4388889!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e40da4ea912bb29%3A0x633da4b46c645479!2sKantor%20Gubernur%20Lampung!5e0!3m2!1sid!2sid!4v1700000000000',
            'maps_dir_link'  => 'https://maps.google.com/?q=Kantor+Gubernur+Lampung',
        ];

        $serviceCategories = [
            'Konsultasi Perencanaan & Pemilihan PBJ',
            'Helpdesk LPSE & Sistem SPSE Lampung',
            'Pelatihan Kompetensi (Arena Belajar)',
            'Verifikasi & Validasi Sertifikat Digital',
            'Katalog Elektronik Lokal & E-Purchasing',
            'Advokasi & Bantuan Hukum PBJ',
            'Pengaduan & Informasi Publik Lainnya',
        ];

        return view('landing/pages/contact', [
            'title'             => 'Hubungi Kami & Helpdesk PBJ | Biro Pengadaan Barang dan Jasa Lampung',
            'metaDescription'   => 'Layanan informasi resmi, konsultasi teknis, dan helpdesk LPSE Biro Pengadaan Barang dan Jasa Sekretariat Daerah Provinsi Lampung.',
            'metaKeywords'      => 'kontak BegawiPengadaan, helpdesk LPSE Lampung, biro PBJ Setda Lampung, alamat kantor gubernur lampung',
            'breadcrumbs'       => [
                ['label' => 'Hubungi Kami'],
            ],
            'contact'           => $contactInfo,
            'serviceCategories' => $serviceCategories,
        ]);
    }

    public function sendContact()
    {
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

        // Simpan / catat log pesan konsultasi
        try {
            $auditService = new \App\Services\AuditService();
            $auditService->log(null, 'contact.inquiry_submitted', 'contact', 'contact_form', 0, [
                'name'         => substr((string) $this->request->getPost('name'), 0, 100),
                'email'        => substr((string) $this->request->getPost('email'), 0, 100),
                'service_type' => substr((string) $this->request->getPost('service_type'), 0, 100),
                'subject'      => substr((string) $this->request->getPost('subject'), 0, 150),
            ]);
        } catch (\Throwable) {
        }

        return redirect()->to(site_url('kontak#form-pesan'))
            ->with('contact_success', 'Terima kasih atas pesan Anda! Konsultasi dan pertanyaan Anda telah berhasil diteruskan ke Helpdesk Biro Pengadaan Barang dan Jasa Provinsi Lampung. Petugas kami akan segera menindaklanjuti.');
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
        $rating = (int) $this->request->getPost('rating_overall');
        if ($rating < 1 || $rating > 5) {
            $rating = 5;
        }

        $isAnonymous = $this->request->getPost('is_anonymous') !== '0';
        $respondentRole = trim((string) ($this->request->getPost('respondent_role') ?: 'Pengguna Layanan / Masyarakat'));
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

        try {
            $surveyModel->insert($data);
        } catch (\Throwable $e) {
            log_message('error', 'Satisfaction Survey insert error: ' . $e->getMessage());
        }

        try {
            $auditService = new \App\Services\AuditService();
            $auditService->log(null, 'survey.satisfaction_submitted', 'survey', 'satisfaction_surveys', 0, [
                'ticket_number'   => $ticketNumber,
                'rating_overall'  => $rating,
                'respondent_role' => $respondentRole,
            ]);
        } catch (\Throwable) {
        }

        $indonesianMonths = [
            'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
            'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
            'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
            'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember',
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
