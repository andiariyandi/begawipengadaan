<?php

namespace App\Controllers\Landing;

use App\Controllers\BaseController;
use App\Models\Certificate\CertificateModel;
use App\Services\AuditService;
use CodeIgniter\Database\ConnectionInterface;

/**
 * CertificateVerificationController — publik.
 *
 * Routes:
 *   GET  /verify/certificate/{token}        → sha256(token) → lookup → tampil
 *   POST /verifikasi-sertifikat/hasil       → cari by nomor
 *   GET  /verifikasi-sertifikat             → form kosong (untuk cek manual)
 *
 * Privasi:
 *   TIDAK menampilkan:
 *     - user_id, NIP/NIK, email, phone, identifier
 *     - path file PDF internal
 *     - verification_token_hash
 *     - revocation_reason (full text)
 *
 *   Yang ditampilkan:
 *     - participant_name
 *     - certificate_number
 *     - kegiatan / course / event (title)
 *     - issued_at
 *     - status ('issued' | 'revoked' | 'expired')
 *
 * Audit:
 *   certificate.verified dengan method=qr_token | number_lookup.
 */
class CertificateVerificationController extends BaseController
{
    private ConnectionInterface $db;
    private CertificateModel $certificateModel;
    private AuditService $auditService;

    public function __construct()
    {
        $this->db = db_connect();
        $this->certificateModel = new CertificateModel();
        $this->auditService     = new AuditService();
    }

    /**
     * Form pencarian by nomor sertifikat.
     */
    public function index()
    {
        return view('landing/pages/certificate_verify', [
            'title'     => 'Verifikasi Sertifikat | BegawiPBJ',
            'pageTitle' => 'Verifikasi Sertifikat',
            'result'    => null,
            'error'     => null,
        ]);
    }

    /**
     * POST /verifikasi-sertifikat/hasil
     */
    public function result()
    {
        $number = trim((string) $this->request->getPost('certificate_number'));
        if ($number === '') {
            return $this->renderResult(null, 'Nomor sertifikat wajib diisi.');
        }

        $row = $this->db->table('certificates c')
            ->select(['c.id', 'c.certificate_number', 'c.source_type', 'c.status',
                      'c.issued_at', 'c.expires_at', 'u.name AS user_name'])
            ->join('users u', 'u.id = c.user_id', 'left')
            ->where('c.certificate_number', $number)
            ->get()
            ->getRowArray();

        if (! $row) {
            return $this->renderResult(null, 'Sertifikat tidak ditemukan.');
        }

        $row['title'] = $this->resolvePublicTitle($row['source_type'], (int) $row['id']);
        $this->auditService->log(null, 'certificate.verified', 'certificate', 'certificate', (int) $row['id'], [
            'method' => 'number_lookup',
        ]);

        return $this->renderResult($this->toPublicView($row), null);
    }

    /**
     * GET /verify/certificate/{token} (public QR route).
     *
     * @param string $token token klar (bukan hash). Akan di-hash sha256 lalu di-lookup.
     */
    public function detail(string $token)
    {
        $token = trim($token);
        if ($token === '' || strlen($token) > 256 || ! preg_match('/^[a-f0-9]+$/i', $token)) {
            return $this->renderResult(null, 'Token verifikasi tidak valid.');
        }

        $row = $this->certificateModel->findByClearToken($token);
        if (! $row) {
            return $this->renderResult(null, 'Sertifikat tidak ditemukan atau token tidak valid.');
        }

        $row['user_name'] = $this->lookupSafeUserName((int) $row['user_id']);
        $row['title']     = $this->resolvePublicTitle((string) $row['source_type'], (int) $row['id']);

        $this->auditService->log(null, 'certificate.verified', 'certificate', 'certificate', (int) $row['id'], [
            'method' => 'qr_token',
        ]);

        return $this->renderResult($this->toPublicView($row), null);
    }

    // =============================================================
    // View helpers — minimum info aman
    // =============================================================

    /**
     * Mapping row DB → struktur siap-view publik.
     */
    private function toPublicView(array $row): array
    {
        $statusLabel = [
            'issued'  => 'Diterbitkan',
            'revoked' => 'Dicabut',
            'expired' => 'Kedaluwarsa',
            'pending' => 'Belum Diterbitkan',
        ];

        return [
            'certificate_number' => $row['certificate_number'] ?? '—',
            'user_name'          => $row['user_name']          ?? '—',
            'title'              => $row['title']              ?? '—',
            'source_type'        => $row['source_type']        ?? '',
            'status'             => $row['status']             ?? 'unknown',
            'status_label'       => $statusLabel[$row['status']] ?? 'Tidak Diketahui',
            'issued_at_human'    => ! empty($row['issued_at'])
                ? date('d F Y', strtotime((string) $row['issued_at']))
                : '—',
            'expires_at_human'   => ! empty($row['expires_at'])
                ? date('d F Y', strtotime((string) $row['expires_at']))
                : null,
            'is_revoked'         => ($row['status'] ?? '') === 'revoked',
        ];
    }

    private function renderResult(?array $result, ?string $error)
    {
        return view('landing/pages/certificate_result', [
            'title'     => 'Hasil Verifikasi | BegawiPBJ',
            'pageTitle' => 'Hasil Verifikasi',
            'result'    => $result,
            'error'     => $error,
        ]);
    }

    private function resolvePublicTitle(string $sourceType, int $certificateId): string
    {
        if ($sourceType === 'learning_enrollment') {
            $row = $this->db->table('certificates c')
                ->select('co.title AS course_title')
                ->join('enrollments e',           'e.id = c.source_id',         'left')
                ->join('learning_classes lc',     'lc.id = e.class_id',          'left')
                ->join('courses co',              'co.id = lc.course_id',        'left')
                ->where('c.id', $certificateId)
                ->get()
                ->getRowArray();
            return $row['course_title'] ?? '—';
        }
        if ($sourceType === 'event_registration') {
            $row = $this->db->table('certificates c')
                ->select('ev.title AS event_title')
                ->join('event_registrations er', 'er.id = c.source_id', 'left')
                ->join('events ev',              'ev.id = er.event_id',  'left')
                ->where('c.id', $certificateId)
                ->get()
                ->getRowArray();
            return $row['event_title'] ?? '—';
        }
        return '—';
    }

    private function lookupSafeUserName(int $userId): string
    {
        $row = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
        return $row['name'] ?? '—';
    }
}
