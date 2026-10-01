<?php

namespace App\Services;

use CodeIgniter\Database\ConnectionInterface;

/**
 * ASUMSI (belum ada keputusan bisnis eksplisit):
 * - "Peserta aktif" = enrollment dengan status aktif.
 * - "Tingkat kelulusan" = enrollment yang memiliki waktu lulus dibandingkan seluruh enrollment selesai.
 * - "Nilai rata-rata" = rata-rata percobaan quiz yang sudah dinilai.
 * - "Sertifikat tervalidasi" = sertifikat yang sudah diterbitkan.
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
        $asn   = (clone $users)->where('profile_type', 'asn')->countAllResults();
        $umum  = (clone $users)->where('profile_type', 'public')->countAllResults();

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
        $totalClasses = $this->db->table('learning_classes')->where('deleted_at IS NULL')->countAllResults();

        $enrollmentBuilder = $this->db->table('enrollments');
        if ($from) $enrollmentBuilder->where('created_at >=', $from . ' 00:00:00');
        if ($to)   $enrollmentBuilder->where('created_at <=', $to . ' 23:59:59');

        $activeParticipants = (clone $enrollmentBuilder)->where('status', 'active')->countAllResults();
        $passed             = (clone $enrollmentBuilder)->where('completed_at IS NOT NULL')->where('passed_at IS NOT NULL')->countAllResults();
        $failed             = (clone $enrollmentBuilder)->where('completed_at IS NOT NULL')->where('passed_at IS NULL')->countAllResults();

        $finalized = $passed + $failed;
        $passRate  = $finalized > 0 ? round(($passed / $finalized) * 100, 1) : 0;

        $scoreBuilder = $this->db->table('quiz_attempts')->selectAvg('quiz_attempts.score')
            ->where('quiz_attempts.status', 'graded');
        if ($from) {
            $scoreBuilder->where('quiz_attempts.created_at >=', $from . ' 00:00:00');
        }
        if ($to) {
            $scoreBuilder->where('quiz_attempts.created_at <=', $to . ' 23:59:59');
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
        $verified = $this->db->table('certificates')->where('status', 'issued')->countAllResults();

        return ['total' => $total, 'active' => $active, 'verified' => $verified];
    }

    public function regulationStats(): array
    {
        $total = $this->db->table('regulations')->where('deleted_at IS NULL')->countAllResults();

        $byCategory = $this->db->table('regulations')
            ->select('regulation_categories.name, COUNT(*) as total')
            ->join('regulation_categories', 'regulation_categories.id = regulations.category_id')
            ->where('regulations.deleted_at IS NULL')
            ->groupBy('regulation_categories.name')
            ->get()->getResultArray();

        $latest = $this->db->table('regulations')
            ->select('regulations.title, regulations.document_number, regulations.created_at')
            ->where('regulations.deleted_at IS NULL')
            ->orderBy('regulations.created_at', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        return ['total' => $total, 'by_category' => $byCategory, 'latest' => $latest];
    }
}
