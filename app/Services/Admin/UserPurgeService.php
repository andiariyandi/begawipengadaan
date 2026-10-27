<?php

namespace App\Services\Admin;

use App\Models\UserModel;
use App\Services\AuditService;
use CodeIgniter\Exceptions\PageNotFoundException;
use InvalidArgumentException;
use RuntimeException;

/**
 * UserPurgeService
 *
 * Mengorkestrasi penghapusan fisik permanen pengguna beserta seluruh data
 * relasi (foreign key cascade) secara transaksional dan aman.
 *
 * Menerapkan Smart Safety Guardrails:
 * 1. Self-deletion Blocker: Melarang penghapusan akun sendiri.
 * 2. Root & Super Admin Blocker: Melindungi User ID 13, 14 dan akun berperan superadmin.
 * 3. Article & News Reassignment: Mengalihkan penulis artikel PBJ dan berita ke Super Admin Master
 *    agar portal publik tidak 404 dan constraint NOT NULL database terpenuhi.
 * 4. Hierarchical Bottom-up Deletion: Cicit -> Cucu -> Anak -> Pengguna.
 */
class UserPurgeService
{
    /**
     * ID akun root yang dilindungi permanen dari penghapusan.
     */
    public const PROTECTED_USER_IDS = [13, 14];

    public function __construct(
        private readonly AuditService $audit = new AuditService()
    ) {}

    /**
     * Mengambil ringkasan data yang berelasi dengan pengguna untuk modal konfirmasi.
     */
    public function getImpactSummary(int $userId): array
    {
        $db = db_connect();
        $user = $db->table('users')->where('id', $userId)->get()->getRowArray();
        if (! $user) {
            throw PageNotFoundException::forPageNotFound('Pengguna tidak ditemukan.');
        }

        // Cek apakah akun memiliki peran superadmin
        $hasSuperadminRole = $db->table('user_roles ur')
            ->join('roles r', 'r.id = ur.role_id')
            ->where('ur.user_id', $userId)
            ->where('ur.status', 'active')
            ->where('r.key', 'superadmin')
            ->countAllResults() > 0;

        $isProtected = in_array($userId, self::PROTECTED_USER_IDS, true) || $hasSuperadminRole;

        // Hitung LMS Enrollments & anak-anaknya
        $enrollmentRows = $db->table('enrollments')->select('id')->where('user_id', $userId)->get()->getResultArray();
        $enrollmentCount = count($enrollmentRows);
        $enrollmentIds = array_column($enrollmentRows, 'id');

        $quizAttemptCount = 0;
        $assignmentSubCount = 0;
        if (! empty($enrollmentIds)) {
            $quizAttemptCount = $db->table('quiz_attempts')->whereIn('enrollment_id', $enrollmentIds)->countAllResults();
            $assignmentSubCount = $db->table('assignment_submissions')->whereIn('enrollment_id', $enrollmentIds)->countAllResults();
        }

        // Hitung Sertifikat
        $certificateCount = $db->table('certificates')->where('user_id', $userId)->countAllResults();

        // Hitung Event Registrations
        $eventRegCount = $db->table('event_registrations')->where('user_id', $userId)->countAllResults();

        // Hitung Artikel & Berita yang Ditulis
        $articleCount = $db->table('knowledge_articles')->where('author_id', $userId)->countAllResults();
        $newsCount = $db->table('news')->where('author_id', $userId)->countAllResults();

        // Hitung Kelas sebagai Instruktur / Penilai
        $trainerCount = $db->table('class_trainers')->where('user_id', $userId)->countAllResults();
        $evaluatorCount = $db->table('class_evaluators')->where('user_id', $userId)->countAllResults();

        // Hitung Role
        $roleCount = $db->table('user_roles')->where('user_id', $userId)->countAllResults();

        return [
            'user' => [
                'id'             => (int) $user['id'],
                'name'           => $user['name'] ?: $user['username'],
                'username'       => $user['username'],
                'email'          => $user['email'],
                'profile_type'   => $user['profile_type'],
                'account_status' => $user['account_status'],
            ],
            'counts' => [
                'enrollments'            => $enrollmentCount,
                'quiz_attempts'          => $quizAttemptCount,
                'assignment_submissions' => $assignmentSubCount,
                'certificates'           => $certificateCount,
                'event_registrations'    => $eventRegCount,
                'knowledge_articles'     => $articleCount,
                'news'                   => $newsCount,
                'class_trainers'         => $trainerCount,
                'class_evaluators'       => $evaluatorCount,
                'user_roles'             => $roleCount,
            ],
            'is_protected'        => $isProtected,
            'has_superadmin_role' => $hasSuperadminRole,
        ];
    }

    /**
     * Mengeksekusi pembersihan permanen data pengguna beserta dependensi foreign key.
     *
     * @param int $userId Target pengguna yang akan dihapus
     * @param int $actorId Administrator yang melakukan penghapusan
     * @param string $confirmationText Teks konfirmasi (harus 'HAPUS')
     * @return array Ringkasan pembersihan
     */
    public function purge(int $userId, int $actorId, string $confirmationText): array
    {
        if (strtoupper(trim($confirmationText)) !== 'HAPUS') {
            throw new InvalidArgumentException('Teks konfirmasi tidak sesuai. Harap ketik "HAPUS" untuk melanjutkan.');
        }

        if ($userId === $actorId) {
            throw new InvalidArgumentException('Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if (in_array($userId, self::PROTECTED_USER_IDS, true)) {
            throw new InvalidArgumentException('Akun Master Administrator (ID ' . $userId . ') dilindungi dan tidak dapat dihapus.');
        }

        $db = db_connect();
        $user = $db->table('users')->where('id', $userId)->get()->getRowArray();
        if (! $user) {
            throw PageNotFoundException::forPageNotFound('Pengguna tidak ditemukan.');
        }

        // Cek peran superadmin
        $isSuperadmin = $db->table('user_roles ur')
            ->join('roles r', 'r.id = ur.role_id')
            ->where('ur.user_id', $userId)
            ->where('ur.status', 'active')
            ->where('r.key', 'superadmin')
            ->countAllResults() > 0;

        if ($isSuperadmin) {
            throw new InvalidArgumentException('Akun dengan peran Super Administrator tidak dapat dihapus demi keamanan sistem.');
        }

        // Ambil data profil untuk pembersihan file avatar
        $userProfile = $db->table('user_profiles')->where('user_id', $userId)->get()->getRowArray();
        $avatarPath = $userProfile['avatar_path'] ?? null;

        // Ambil data sertifikat untuk pembersihan file PDF
        $certificates = $db->table('certificates')->where('user_id', $userId)->get()->getResultArray();

        $db->transStart();

        // 1. REASSIGN KEPEMILIKAN ARTIKEL & BERITA KE SUPER ADMIN MASTER (ID 14)
        // Agar portal publik tidak error karena NOT NULL dan artikel PBJ tidak hilang / 404
        $masterAdminId = 14;
        $db->table('knowledge_articles')->where('author_id', $userId)->update(['author_id' => $masterAdminId]);
        $db->table('news')->where('author_id', $userId)->update(['author_id' => $masterAdminId]);

        // 2. BERSIHKAN LMS (ARENA BELAJAR) HIRARKIS BOTTOM-UP
        $enrollmentRows = $db->table('enrollments')->select('id')->where('user_id', $userId)->get()->getResultArray();
        if (! empty($enrollmentRows)) {
            $eIds = array_column($enrollmentRows, 'id');

            // Cicit: quiz_answers
            $attemptRows = $db->table('quiz_attempts')->select('id')->whereIn('enrollment_id', $eIds)->get()->getResultArray();
            if (! empty($attemptRows)) {
                $attIds = array_column($attemptRows, 'id');
                $db->table('quiz_answers')->whereIn('attempt_id', $attIds)->delete();
                $db->table('quiz_attempts')->whereIn('id', $attIds)->delete();
            }

            // Cicit: assignment_submission_files
            $subRows = $db->table('assignment_submissions')->select('id')->whereIn('enrollment_id', $eIds)->get()->getResultArray();
            if (! empty($subRows)) {
                $sIds = array_column($subRows, 'id');
                $subFiles = $db->table('assignment_submission_files')->whereIn('submission_id', $sIds)->get()->getResultArray();
                foreach ($subFiles as $sf) {
                    if (! empty($sf['file_path'])) {
                        $fullSubPath = WRITEPATH . $sf['file_path'];
                        if (is_file($fullSubPath)) {
                            @unlink($fullSubPath);
                        }
                    }
                }
                $db->table('assignment_submission_files')->whereIn('submission_id', $sIds)->delete();
                $db->table('assignment_submissions')->whereIn('id', $sIds)->delete();
            }

            // Cucu lainnya
            $db->table('learning_progress')->whereIn('enrollment_id', $eIds)->delete();
            $db->table('feedbacks')->whereIn('enrollment_id', $eIds)->delete();

            // Anak: enrollments
            $db->table('enrollments')->whereIn('id', $eIds)->delete();
        }

        // 3. BERSIHKAN KEGIATAN & EVENT
        $eventRegRows = $db->table('event_registrations')->select('id')->where('user_id', $userId)->get()->getResultArray();
        if (! empty($eventRegRows)) {
            $erIds = array_column($eventRegRows, 'id');
            $db->table('attendances')->whereIn('event_registration_id', $erIds)->delete();
            $db->table('event_registrations')->whereIn('id', $erIds)->delete();
        }

        // 4. BERSIHKAN INSTRUKTUR & PENILAI KELAS
        $db->table('class_trainers')->where('user_id', $userId)->delete();
        $db->table('class_evaluators')->where('user_id', $userId)->delete();

        // 5. BERSIHKAN SERTIFIKAT & FILE PDF
        foreach ($certificates as $cert) {
            if (! empty($cert['pdf_path'])) {
                $fullCertPath = WRITEPATH . $cert['pdf_path'];
                if (is_file($fullCertPath)) {
                    @unlink($fullCertPath);
                }
            }
        }
        $db->table('certificates')->where('user_id', $userId)->delete();

        // 6. BERSIHKAN JF PPBJ & KOMPETENSI
        $jfRows = $db->table('jf_ppbj_records')->select('id')->where('user_id', $userId)->get()->getResultArray();
        if (! empty($jfRows)) {
            $jfIds = array_column($jfRows, 'id');
            $db->table('competency_certificates')->whereIn('jf_ppbj_record_id', $jfIds)->delete();
            $db->table('jf_ppbj_records')->whereIn('id', $jfIds)->delete();
        }

        // 7. BERSIHKAN PERIZINAN & PERMOHONAN ROLE/PROFIL
        $db->table('profile_change_requests')->where('user_id', $userId)->delete();
        $db->table('profile_verifications')->where('user_id', $userId)->delete();
        $db->table('role_requests')->where('user_id', $userId)->delete();
        $db->table('user_procurement_roles')->where('user_id', $userId)->delete();
        $db->table('user_roles')->where('user_id', $userId)->delete();

        // 8. BERSIHKAN DATA PROFIL SPESIFIK & UMUM
        $db->table('asn_profiles')->where('user_id', $userId)->delete();
        $db->table('provider_profiles')->where('user_id', $userId)->delete();
        $db->table('academic_profiles')->where('user_id', $userId)->delete();
        $db->table('public_profiles')->where('user_id', $userId)->delete();

        // Bersihkan Avatar Fisik
        if (! empty($avatarPath)) {
            $fullAvatarPath = WRITEPATH . $avatarPath;
            if (is_file($fullAvatarPath)) {
                @unlink($fullAvatarPath);
            }
            $db->table('file_assets')->where('path', $avatarPath)->delete();
        }
        $db->table('user_profiles')->where('user_id', $userId)->delete();

        // 9. BERSIHKAN FITUR PENDUKUNG (BOOKMARK, NOTIFIKASI, TOKEN, LOG DOWNLOAD, EXPORT)
        $db->table('bookmarks')->where('user_id', $userId)->delete();
        $db->table('notifications')->where('user_id', $userId)->delete();
        $db->table('email_verifications')->where('user_id', $userId)->delete();
        $db->table('password_resets')->where('user_id', $userId)->delete();
        $db->table('exports')->where('requested_by', $userId)->delete();
        $db->table('regulation_downloads')->where('user_id', $userId)->delete();

        // 10. BERSIHKAN WORKFLOW TRANSITIONS UNTUK PENGGUNA
        $db->table('workflow_transitions')
            ->where('resource_type', 'user')
            ->where('resource_id', $userId)
            ->delete();

        // 11. HAPUS REKORD PENGGUNA DARI TABEL USERS (HARD DELETE ATOMIK)
        $db->table('users')->where('id', $userId)->delete();

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new RuntimeException('Gagal mengeksekusi penghapusan pengguna secara transaksional database.');
        }

        // 12. LOG AUDIT ADMINISTRATIF
        $this->audit->log($actorId, 'user.purge_permanent', 'users', 'user', $userId, [
            'deleted_user_name'     => $user['name'] ?: $user['username'],
            'deleted_user_email'    => $user['email'],
            'deleted_user_username' => $user['username'],
            'deleted_user_profile'  => $user['profile_type'],
        ]);

        return [
            'success' => true,
            'message' => 'Pengguna "' . ($user['name'] ?: $user['username']) . '" beserta seluruh data relasi (foreign key) berhasil dihapus permanen dari sistem.',
        ];
    }
}
