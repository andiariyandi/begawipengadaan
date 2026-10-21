<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;

/**
 * Service pemersatu ringkasan dan metrik analitik Dashboard Back-office (WowDash).
 *
 * Mengonsolidasikan query operasional lintas domain bisnis:
 * 1. Manajemen Akses (User, Verifikasi, Profil)
 * 2. Arena Belajar (Program, Course, Kelas, Enrollment, Kelulusan)
 * 3. Bunga Rampai (Artikel, FAQ, Studi Kasus, Template)
 * 4. Regulasi PBJ (Dokumen Regulasi, Kategori, Versi)
 * 5. Komunikasi (Berita, Event & Agenda)
 * 6. Sertifikat (Penerbitan, Antrean Approval)
 * 7. Audit & Aktivitas Sistem
 */
class DashboardService
{
    private BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? db_connect();
    }

    /**
     * Ringkasan Modul Manajemen Akses & Pengguna
     */
    public function getAccessSummary(): array
    {
        $totalUsers   = (int) $this->db->table('users')->where('deleted_at', null)->countAllResults();
        $activeUsers  = (int) $this->db->table('users')->where('deleted_at', null)->where('account_status', 'active')->countAllResults();
        $pendingUsers = (int) $this->db->table('users')->where('deleted_at', null)->whereIn('account_status', ['pending_verification', 'submitted'])->countAllResults();
        $otherUsers   = max(0, $totalUsers - $activeUsers - $pendingUsers);

        $monthStart = date('Y-m-01 00:00:00');
        $newUsersThisMonth = (int) $this->db->table('users')
            ->where('deleted_at', null)
            ->where('created_at >=', $monthStart)
            ->countAllResults();

        $pendingVerifications = (int) $this->db->table('profile_verifications')
            ->where('status', 'pending')
            ->countAllResults();

        $usersByProfile = [];
        $rows = $this->db->table('users')
            ->select('profile_type, COUNT(*) AS total')
            ->where('deleted_at', null)
            ->groupBy('profile_type')
            ->orderBy('profile_type')
            ->get()
            ->getResultArray();
        foreach ($rows as $row) {
            $usersByProfile[(string) $row['profile_type']] = (int) $row['total'];
        }

        $usersByAccount = [];
        $rows = $this->db->table('users')
            ->select('account_status, COUNT(*) AS total')
            ->where('deleted_at', null)
            ->groupBy('account_status')
            ->orderBy('account_status')
            ->get()
            ->getResultArray();
        foreach ($rows as $row) {
            $usersByAccount[(string) $row['account_status']] = (int) $row['total'];
        }

        $recentVerifications = $this->db->table('profile_verifications pv')
            ->select('pv.id, pv.user_id, pv.verification_type, pv.status, pv.created_at, u.name, u.email, u.profile_type')
            ->join('users u', 'u.id = pv.user_id', 'left')
            ->where('pv.status', 'pending')
            ->orderBy('pv.created_at', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        return [
            'totalUsers'            => $totalUsers,
            'activeUsers'           => $activeUsers,
            'pendingUsers'          => $pendingUsers,
            'otherUsers'            => $otherUsers,
            'newUsersThisMonth'     => $newUsersThisMonth,
            'pendingVerifications'  => $pendingVerifications,
            'usersByProfile'        => $usersByProfile,
            'usersByAccount'        => $usersByAccount,
            'recentVerifications'   => $recentVerifications,
            'accountStatusDonut'    => [
                'series' => [$activeUsers, $pendingUsers, $otherUsers],
                'labels' => ['Aktif', 'Pending', 'Lainnya'],
                'colors' => ['#487FFF', '#FF9F29', '#EF4A4A'],
                'total'  => $totalUsers,
            ],
        ];
    }

    /**
     * Ringkasan Modul Arena Belajar
     */
    public function getLearningSummary(): array
    {
        $totalPrograms        = (int) $this->db->table('programs')->countAllResults();
        $totalCourses         = (int) $this->db->table('courses')->countAllResults();
        $activeClasses        = (int) $this->db->table('learning_classes')->whereIn('status', ['published', 'active', 'scheduled', 'ongoing', 'open'])->countAllResults();
        $activeEnrollments    = (int) $this->db->table('enrollments')->where('status', 'active')->countAllResults();
        $completedEnrollments = (int) $this->db->table('enrollments')->where('status', 'completed')->countAllResults();
        $totalEnrollments     = (int) $this->db->table('enrollments')->countAllResults();

        $completionRate = ($totalEnrollments > 0)
            ? round(($completedEnrollments / $totalEnrollments) * 100, 1)
            : 0.0;

        $monthStart = date('Y-m-01 00:00:00');
        $newEnrollmentsThisMonth = (int) $this->db->table('enrollments')
            ->where('enrolled_at >=', $monthStart)
            ->countAllResults();

        $activeClassList = $this->db->table('learning_classes lc')
            ->select('lc.id, lc.name, lc.code, lc.status, lc.passing_grade, c.title AS course_title')
            ->join('courses c', 'c.id = lc.course_id', 'left')
            ->whereIn('lc.status', ['published', 'active'])
            ->orderBy('lc.created_at', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        // 12 bulan tren registrasi dan enrollment untuk ApexCharts
        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $now = new \DateTimeImmutable('first day of this month');
        $registration = array_fill(0, 12, 0);
        $enrollment   = array_fill(0, 12, 0);

        for ($i = 11; $i >= 0; $i--) {
            $mStart = $now->modify("-{$i} months")->format('Y-m-01 00:00:00');
            $mEnd   = $now->modify("-{$i} months")->modify('+1 month')->format('Y-m-01 00:00:00');

            $registration[11 - $i] = (int) $this->db->table('users')
                ->where('deleted_at', null)
                ->where('created_at >=', $mStart)
                ->where('created_at <', $mEnd)
                ->countAllResults();

            $enrollment[11 - $i] = (int) $this->db->table('enrollments')
                ->where('enrolled_at >=', $mStart)
                ->where('enrolled_at <', $mEnd)
                ->countAllResults();
        }

        return [
            'totalPrograms'           => $totalPrograms,
            'totalCourses'            => $totalCourses,
            'activeClasses'           => $activeClasses,
            'activeEnrollments'       => $activeEnrollments,
            'completedEnrollments'    => $completedEnrollments,
            'totalEnrollments'        => $totalEnrollments,
            'completionRate'          => $completionRate,
            'newEnrollmentsThisMonth' => $newEnrollmentsThisMonth,
            'activeClassList'         => $activeClassList,
            'enrollmentMonthly'       => [
                'categories'   => $labels,
                'registration' => $registration,
                'enrollment'   => $enrollment,
            ],
        ];
    }

    /**
     * Ringkasan Modul Bunga Rampai (Knowledge)
     */
    public function getKnowledgeSummary(): array
    {
        $totalArticles    = (int) $this->db->table('knowledge_articles')->where('type', 'article')->countAllResults();
        $totalFaqs        = (int) $this->db->table('knowledge_articles')->where('type', 'faq')->countAllResults();
        $totalCaseStudies = (int) $this->db->table('knowledge_articles')->where('type', 'case_study')->countAllResults();
        $totalTemplates   = (int) $this->db->table('knowledge_articles')->where('type', 'template')->countAllResults();
        $totalCategories  = (int) $this->db->table('knowledge_categories')->countAllResults();

        $latestArticles = $this->db->table('knowledge_articles ka')
            ->select('ka.id, ka.title, ka.slug, ka.type, ka.workflow_status, ka.created_at, u.name AS author_name')
            ->join('users u', 'u.id = ka.author_id', 'left')
            ->where('ka.deleted_at', null)
            ->orderBy('ka.created_at', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        return [
            'totalArticles'    => $totalArticles,
            'totalFaqs'        => $totalFaqs,
            'totalCaseStudies' => $totalCaseStudies,
            'totalTemplates'   => $totalTemplates,
            'totalCategories'  => $totalCategories,
            'latestArticles'   => $latestArticles,
        ];
    }

    /**
     * Ringkasan Modul Regulasi PBJ
     */
    public function getRegulationSummary(): array
    {
        $totalRegulations = (int) $this->db->table('regulations')->countAllResults();
        $totalCategories  = (int) $this->db->table('regulation_categories')->countAllResults();
        $totalVersions    = (int) $this->db->table('regulation_versions')->countAllResults();

        $latestRegulations = $this->db->table('regulations r')
            ->select('r.id, r.title, r.slug, r.document_number, r.document_year, r.workflow_status, r.legal_status, r.created_at, rc.name AS category_name')
            ->join('regulation_categories rc', 'rc.id = r.category_id', 'left')
            ->where('r.deleted_at', null)
            ->orderBy('r.created_at', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        return [
            'totalRegulations'  => $totalRegulations,
            'totalCategories'   => $totalCategories,
            'totalVersions'     => $totalVersions,
            'latestRegulations' => $latestRegulations,
        ];
    }

    /**
     * Ringkasan Modul Komunikasi (Berita & Event)
     */
    public function getCommunicationSummary(): array
    {
        $totalNews       = (int) $this->db->table('news')->countAllResults();
        $totalNewsCat    = (int) $this->db->table('news_categories')->countAllResults();
        $totalEvents     = (int) $this->db->table('events')->countAllResults();
        $totalRegistrasi = (int) $this->db->table('event_registrations')->countAllResults();

        $latestNews = $this->db->table('news n')
            ->select('n.id, n.title, n.slug, n.workflow_status, n.created_at, nc.name AS category_name')
            ->join('news_categories nc', 'nc.id = n.category_id', 'left')
            ->where('n.deleted_at', null)
            ->orderBy('n.created_at', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        $upcomingEvents = $this->db->table('events e')
            ->select('e.id, e.title, e.slug, e.type, e.mode, e.status, e.start_at, e.location')
            ->where('e.deleted_at', null)
            ->orderBy('e.start_at', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        return [
            'totalNews'        => $totalNews,
            'totalNewsCat'     => $totalNewsCat,
            'totalEvents'      => $totalEvents,
            'totalRegistrasi'  => $totalRegistrasi,
            'latestNews'       => $latestNews,
            'upcomingEvents'   => $upcomingEvents,
        ];
    }

    /**
     * Ringkasan Modul Sertifikat
     */
    public function getCertificateSummary(): array
    {
        $issuedCertificates = (int) $this->db->table('certificates')
            ->where('status', 'issued')
            ->countAllResults();

        $pendingApprovals = (int) $this->db->table('approvals')
            ->where('status', 'pending')
            ->where('resource_type', 'certificate')
            ->countAllResults();

        $totalPendingApprovals = (int) $this->db->table('approvals')
            ->where('status', 'pending')
            ->countAllResults();

        $monthStart = date('Y-m-01 00:00:00');
        $newCertificatesThisMonth = (int) $this->db->table('certificates')
            ->where('created_at >=', $monthStart)
            ->countAllResults();

        $rawRows = $this->db->table('approvals a')
            ->select('a.id, a.resource_type, a.requested_action, a.status, a.created_at, u.username AS requester_username, u.email AS requester_email, u.name AS requester_name')
            ->join('users u', 'u.id = a.requested_by', 'left')
            ->where('a.status', 'pending')
            ->orderBy('a.created_at', 'DESC')
            ->limit(6)
            ->get()
            ->getResultArray();

        $approvalQueue = [];
        foreach ($rawRows as $row) {
            $row['requester'] = (string) ($row['requester_name'] ?? $row['requester_username'] ?? $row['requester_email'] ?? '-');
            $approvalQueue[]  = $row;
        }

        return [
            'issuedCertificates'       => $issuedCertificates,
            'pendingApprovals'         => $pendingApprovals,
            'totalPendingApprovals'    => $totalPendingApprovals,
            'newCertificatesThisMonth' => $newCertificatesThisMonth,
            'approvalQueue'            => $approvalQueue,
        ];
    }

    /**
     * Ringkasan Audit Log dan Aktivitas Pengguna
     */
    public function getAuditSummary(int $limit = 6): array
    {
        $rawAudit = $this->db->table('audit_logs al')
            ->select('al.id, al.actor_id, al.action, al.resource_type, al.resource_id, al.result, al.reason, al.created_at, u.username AS actor_username, u.name AS actor_name')
            ->join('users u', 'u.id = al.actor_id', 'left')
            ->orderBy('al.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();

        $auditList = [];
        foreach ($rawAudit as $entry) {
            $entry['actor'] = (string) ($entry['actor_username'] ?? $entry['actor_name'] ?? 'sistem');
            $auditList[]    = $entry;
        }

        return $auditList;
    }

    /**
     * Ringkasan Gabungan (KPIs + Module Overviews)
     */
    public function getFullDashboardSummary(): array
    {
        $access        = $this->getAccessSummary();
        $learning      = $this->getLearningSummary();
        $knowledge     = $this->getKnowledgeSummary();
        $regulations   = $this->getRegulationSummary();
        $communication = $this->getCommunicationSummary();
        $certificate   = $this->getCertificateSummary();
        $recentAudit   = $this->getAuditSummary(6);

        $kpis = [
            'totalUsers'               => $access['totalUsers'],
            'activeUsers'              => $access['activeUsers'],
            'pendingUsers'             => $access['pendingUsers'],
            'activeEnrollments'        => $learning['activeEnrollments'],
            'completedEnrollments'     => $learning['completedEnrollments'],
            'issuedCertificates'       => $certificate['issuedCertificates'],
            'pendingApprovals'         => $certificate['totalPendingApprovals'],
            'newUsersThisMonth'        => $access['newUsersThisMonth'],
            'newEnrollmentsThisMonth'  => $learning['newEnrollmentsThisMonth'],
            'newCertificatesThisMonth' => $certificate['newCertificatesThisMonth'],
            'usersByProfile'           => $access['usersByProfile'],
            'usersByAccount'           => $access['usersByAccount'],
            'accountStatusDonut'       => $access['accountStatusDonut'],
            'enrollmentMonthly'        => $learning['enrollmentMonthly'],
        ];

        return [
            'kpis'          => $kpis,
            'access'        => $access,
            'learning'      => $learning,
            'knowledge'     => $knowledge,
            'regulations'   => $regulations,
            'communication' => $communication,
            'certificate'   => $certificate,
            'recentActivity'=> $recentAudit,
            'approvalQueue' => $certificate['approvalQueue'],
        ];
    }
}
