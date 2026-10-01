<?php

namespace App\Services;

use CodeIgniter\Database\ConnectionInterface;

/**
 * ASUMSI (belum ada keputusan bisnis eksplisit):
 * - "Peserta aktif" = enrollment dengan final_status = 'in_progress'.
 * - "Tingkat kelulusan" = passed / (passed+failed), bukan dibagi seluruh enrollment.
 * - "Nilai rata-rata" = rata-rata SEMUA percobaan quiz, bukan hanya percobaan terakhir.
 * - "Sertifikat tervalidasi" = jumlah sertifikat unik yang pernah di-scan QR minimal sekali.
 */
class DashboardService
{
    private ConnectionInterface $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function userStats(): array
    {
        $users = $this->db->table('users')->where('deleted_at IS NULL');
        $total = (clone $users)->countAllResults();
        $asn   = (clone $users)->where('user_type', 'asn')->countAllResults();
        $umum  = (clone $users)->where('user_type', 'umum')->countAllResults();

        $byRole = $this->db->table('user_roles')
            ->select('roles.name, COUNT(*) as total')
            ->join('roles', 'roles.id = user_roles.role_id')
            ->join('users', 'users.id = user_roles.user_id AND users.deleted_at IS NULL')
            ->groupBy('roles.name')
            ->get()->getResultArray();

        return ['total' => $total, 'asn' => $asn, 'umum' => $umum, 'by_role' => $byRole];
    }

    public function trainingStats(?string $from = null, ?string $to = null): array
    {
        $totalCourses = $this->db->table('courses')->where('deleted_at IS NULL')->countAllResults();
        $totalClasses = $this->db->table('classes')->where('deleted_at IS NULL')->countAllResults();

        $enrollmentBuilder = $this->db->table('enrollments');
        if ($from) $enrollmentBuilder->where('created_at >=', $from . ' 00:00:00');
        if ($to)   $enrollmentBuilder->where('created_at <=', $to . ' 23:59:59');

        $activeParticipants = (clone $enrollmentBuilder)->where('final_status', 'in_progress')->countAllResults();
        $passed             = (clone $enrollmentBuilder)->where('final_status', 'passed')->countAllResults();
        $failed             = (clone $enrollmentBuilder)->where('final_status', 'failed')->countAllResults();

        $finalized = $passed + $failed;
        $passRate  = $finalized > 0 ? round(($passed / $finalized) * 100, 1) : 0;

        $scoreBuilder = $this->db->table('assessment_results')->selectAvg('assessment_results.score');
        if ($from) {
            $scoreBuilder->where('assessment_results.created_at >=', $from . ' 00:00:00');
        }
        if ($to) {
            $scoreBuilder->where('assessment_results.created_at <=', $to . ' 23:59:59');
        }
        $avgScoreRow = $scoreBuilder->get()->getRowArray();
        $avgScore    = $avgScoreRow['score'] !== null ? round((float) $avgScoreRow['score'], 1) : 0;

        return [
            'total_courses'       => $totalCourses,
            'total_classes'       => $totalClasses,
            'active_participants' => $activeParticipants,
            'pass_rate'           => $passRate,
            'average_score'       => $avgScore,
        ];
    }

    public function certificateStats(): array
    {
        $total    = $this->db->table('certificates')->countAllResults();
        $active   = $this->db->table('certificates')->where('status', 'issued')->countAllResults();
        $verified = $this->db->table('certificate_verifications')->select('certificate_id')->distinct()->countAllResults();

        return ['total' => $total, 'active' => $active, 'verified' => $verified];
    }

    public function regulationStats(): array
    {
        $total = $this->db->table('regulations')->where('deleted_at IS NULL')->countAllResults();

        $byCategory = $this->db->table('regulations')
            ->select('regulation_categories.name, COUNT(*) as total')
            ->join('regulation_categories', 'regulation_categories.id = regulations.regulation_category_id')
            ->where('regulations.deleted_at IS NULL')
            ->groupBy('regulation_categories.name')
            ->get()->getResultArray();

        $latest = $this->db->table('regulations')
            ->select('regulations.title, regulations.nomor_regulasi, regulations.created_at')
            ->where('regulations.deleted_at IS NULL')
            ->orderBy('regulations.created_at', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        return ['total' => $total, 'by_category' => $byCategory, 'latest' => $latest];
    }
}
