<?php

namespace App\Controllers\Landing;

use App\Controllers\BaseController;
use App\Models\Certificate\CertificateModel;
use App\Services\AuditService;
use CodeIgniter\Database\ConnectionInterface;

/**
 * CertificateVerificationController — Portal Publik Verifikasi Keabsahan Sertifikat Digital BegawiPengadaan.
 *
 * Routes:
 *   GET  /verifikasi-sertifikat               → Katalog publik & form pencarian interaktif
 *   MATCH ['get', 'post'] /verifikasi-sertifikat/hasil → Lookup by nomor atau token QR
 *   GET  /verifikasi-sertifikat/(:any)        → Direct verification by certificate number or token
 *   GET  /verify/certificate/(:segment)       → QR Code scanner redirect (token lookup)
 */
class CertificateVerificationController extends BaseController
{
    private ConnectionInterface $db;
    private CertificateModel $certificateModel;
    private AuditService $auditService;

    public function __construct()
    {
        $this->db               = db_connect();
        $this->certificateModel = new CertificateModel();
        $this->auditService     = new AuditService();
    }

    /**
     * Halaman Utama Verifikasi Sertifikat & Direktori Sertifikat Terdaftar.
     */
    public function index()
    {
        $keyword      = trim((string) ($this->request->getGet('q') ?? $this->request->getGet('nomor') ?? ''));
        $sourceFilter = trim((string) ($this->request->getGet('tipe') ?? 'all'));
        $statusFilter = trim((string) ($this->request->getGet('status') ?? 'all'));
        $page         = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage      = 9;

        // 1. Ekosistem Live Metrics Sertifikasi
        $stats = [
            'total_issued'   => 0,
            'total_learning' => 0,
            'total_event'    => 0,
            'total_all'      => 0,
        ];

        try {
            $stats['total_issued'] = (int) $this->db->table('certificates')
                ->where('status', 'issued')
                ->countAllResults();

            $stats['total_learning'] = (int) $this->db->table('certificates')
                ->where('status', 'issued')
                ->where('source_type', 'learning_enrollment')
                ->countAllResults();

            $stats['total_event'] = (int) $this->db->table('certificates')
                ->where('status', 'issued')
                ->where('source_type', 'event_registration')
                ->countAllResults();

            $stats['total_all'] = (int) $this->db->table('certificates')
                ->whereIn('status', ['issued', 'revoked', 'expired'])
                ->countAllResults();
        } catch (\Throwable) {
        }

        // 2. Query Direktori Sertifikat Terverifikasi
        $builder = $this->db->table('certificates c')
            ->select([
                'c.id',
                'c.certificate_number',
                'c.source_type',
                'c.source_id',
                'c.status',
                'c.issued_at',
                'c.expires_at',
                'c.verification_token_hash',
                'u.name AS user_name',
                't.name AS template_name',
            ])
            ->join('users u', 'u.id = c.user_id', 'left')
            ->join('certificate_templates t', 't.id = c.template_id', 'left')
            ->whereIn('c.status', ['issued', 'revoked', 'expired']);

        if ($keyword !== '') {
            $builder->groupStart()
                ->like('c.certificate_number', $keyword)
                ->orLike('u.name', $keyword)
                ->groupEnd();
        }

        if ($sourceFilter !== '' && $sourceFilter !== 'all') {
            $builder->where('c.source_type', $sourceFilter);
        }

        if ($statusFilter !== '' && $statusFilter !== 'all') {
            $builder->where('c.status', $statusFilter);
        }

        $totalRows = (clone $builder)->countAllResults();
        $totalPages = max(1, (int) ceil($totalRows / $perPage));
        $offset     = ($page - 1) * $perPage;

        $rows = $builder->orderBy('c.id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        // Enrich setiap baris sertifikat
        $certificates = [];
        foreach ($rows as $row) {
            $row['title'] = $this->resolvePublicTitle((string) $row['source_type'], (int) $row['id']);
            $certificates[] = $this->toPublicView($row);
        }

        // 3. Ambil contoh nomor sertifikat resmi untuk Quick-Test
        $sampleNumbers = [];
        try {
            $samples = $this->db->table('certificates')
                ->select('certificate_number')
                ->where('status', 'issued')
                ->orderBy('id', 'DESC')
                ->limit(4)
                ->get()
                ->getResultArray();
            foreach ($samples as $s) {
                if (! empty($s['certificate_number'])) {
                    $sampleNumbers[] = $s['certificate_number'];
                }
            }
        } catch (\Throwable) {
        }

        // 4. Data Pager
        $pagerData = [
            'page'       => $page,
            'perPage'    => $perPage,
            'totalRows'  => $totalRows,
            'totalPages' => $totalPages,
            'hasPrev'    => $page > 1,
            'hasNext'    => $page < $totalPages,
            'prevPage'   => $page - 1,
            'nextPage'   => $page + 1,
        ];

        return view('landing/pages/certificate_verify', [
            'title'           => 'Verifikasi Keabsahan Sertifikat Digital PBJ | BegawiPengadaan',
            'pageTitle'       => 'Verifikasi Sertifikat Digital',
            'metaDescription' => 'Portal resmi verifikasi keabsahan sertifikat kelulusan kegiatan belajar pengadaan barang/jasa Pemerintah Provinsi Lampung berbasis kode unik dan kriptografi digital.',
            'metaKeywords'    => 'verifikasi sertifikat, cek sertifikat PBJ Lampung, keabsahan sertifikat pengadaan, BegawiPengadaan sertifikasi',
            'breadcrumbs'     => [
                ['label' => 'Verifikasi Sertifikat'],
            ],
            'certificates'    => $certificates,
            'stats'           => $stats,
            'pagerData'       => $pagerData,
            'sampleNumbers'   => $sampleNumbers,
            'keyword'         => $keyword,
            'sourceFilter'    => $sourceFilter,
            'statusFilter'    => $statusFilter,
        ]);
    }

    /**
     * POST / MATCH GET /verifikasi-sertifikat/hasil
     */
    public function result()
    {
        $number = trim((string) (
            $this->request->getPost('certificate_number')
            ?? $this->request->getPost('nomor')
            ?? $this->request->getGet('certificate_number')
            ?? $this->request->getGet('nomor')
            ?? $this->request->getGet('q')
            ?? ''
        ));

        if ($number === '') {
            return $this->renderResult(null, 'Nomor sertifikat atau kode verifikasi wajib diisi.', '');
        }

        $row = $this->lookupCertificate($number);

        if (! $row) {
            return $this->renderResult(
                null,
                "Sertifikat dengan nomor atau kode verifikasi '{$number}' tidak ditemukan dalam pangkalan data resmi BegawiPengadaan.",
                $number
            );
        }

        $row['title'] = $this->resolvePublicTitle((string) $row['source_type'], (int) $row['id']);

        $this->auditService->log(null, 'certificate.verified', 'certificate', 'certificate', (int) $row['id'], [
            'method' => 'number_lookup',
            'query'  => $number,
        ]);

        return $this->renderResult($this->toPublicView($row), null, $number);
    }

    /**
     * GET /verifikasi-sertifikat/{segment} ATAU /verify/certificate/{token}
     */
    public function detail(...$segments)
    {
        $identifier = trim(urldecode(implode('/', $segments)));
        if ($identifier === '') {
            return $this->renderResult(null, 'Tautan atau nomor verifikasi tidak valid.', '');
        }

        $row = $this->lookupCertificate($identifier);

        if (! $row) {
            return $this->renderResult(
                null,
                "Sertifikat dengan identifikasi '{$identifier}' tidak ditemukan atau tautan verifikasi tidak valid.",
                $identifier
            );
        }

        if (empty($row['user_name']) && ! empty($row['user_id'])) {
            $row['user_name'] = $this->lookupSafeUserName((int) $row['user_id']);
        }
        $row['title'] = $this->resolvePublicTitle((string) $row['source_type'], (int) $row['id']);

        $this->auditService->log(null, 'certificate.verified', 'certificate', 'certificate', (int) $row['id'], [
            'method'     => 'direct_link',
            'identifier' => $identifier,
        ]);

        return $this->renderResult($this->toPublicView($row), null, $identifier);
    }

    // =============================================================
    // Helper Pencarian Fleksibel
    // =============================================================

    /**
     * Cari sertifikat berdasarkan:
     * 1. Nomor sertifikat persis
     * 2. Nomor sertifikat dengan normalisasi strip/slash
     * 3. Clear token (sha256)
     * 4. Verification token hash langsung
     */
    private function lookupCertificate(string $query): ?array
    {
        $normalizedSlash = str_replace('-', '/', $query);
        $normalizedDash  = str_replace('/', '-', $query);

        // 1. Coba pencarian nomor sertifikat
        $row = $this->db->table('certificates c')
            ->select([
                'c.id',
                'c.certificate_number',
                'c.source_type',
                'c.source_id',
                'c.status',
                'c.issued_at',
                'c.expires_at',
                'c.verification_token_hash',
                'c.user_id',
                'u.name AS user_name',
                't.name AS template_name'
            ])
            ->join('users u', 'u.id = c.user_id', 'left')
            ->join('certificate_templates t', 't.id = c.template_id', 'left')
            ->groupStart()
            ->where('c.certificate_number', $query)
            ->orWhere('c.certificate_number', $normalizedSlash)
            ->orWhere('c.certificate_number', $normalizedDash)
            ->groupEnd()
            ->get()
            ->getRowArray();

        if ($row) {
            return $row;
        }

        // 2. Coba pencarian token QR (hex sha256)
        if (preg_match('/^[a-f0-9]{32,64}$/i', $query)) {
            $rowByHash = $this->db->table('certificates c')
                ->select([
                    'c.id',
                    'c.certificate_number',
                    'c.source_type',
                    'c.source_id',
                    'c.status',
                    'c.issued_at',
                    'c.expires_at',
                    'c.verification_token_hash',
                    'c.user_id',
                    'u.name AS user_name',
                    't.name AS template_name'
                ])
                ->join('users u', 'u.id = c.user_id', 'left')
                ->join('certificate_templates t', 't.id = c.template_id', 'left')
                ->where('c.verification_token_hash', strtolower($query))
                ->get()
                ->getRowArray();

            if ($rowByHash) {
                return $rowByHash;
            }
        }

        // 3. Coba via findByClearToken jika token adalah string pendek dari QR
        $rowByToken = $this->certificateModel->findByClearToken($query);
        if ($rowByToken) {
            $fullRow = $this->db->table('certificates c')
                ->select([
                    'c.id',
                    'c.certificate_number',
                    'c.source_type',
                    'c.source_id',
                    'c.status',
                    'c.issued_at',
                    'c.expires_at',
                    'c.verification_token_hash',
                    'c.user_id',
                    'u.name AS user_name',
                    't.name AS template_name'
                ])
                ->join('users u', 'u.id = c.user_id', 'left')
                ->join('certificate_templates t', 't.id = c.template_id', 'left')
                ->where('c.id', (int) $rowByToken['id'])
                ->get()
                ->getRowArray();
            if ($fullRow) {
                return $fullRow;
            }
        }

        return null;
    }

    // =============================================================
    // View helpers — info terstruktur & aman
    // =============================================================

    /**
     * Mapping row DB → struktur siap-view publik.
     */
    private function toPublicView(array $row): array
    {
        $status = $row['status'] ?? 'unknown';

        $statusLabel = [
            'issued'  => 'Valid & Diterbitkan Resmi',
            'revoked' => 'Dicabut (Revoked)',
            'expired' => 'Masa Berlaku Berakhir',
            'pending' => 'Sedang Diproses (Pending)',
        ];

        $statusBadgeClass = [
            'issued'  => 'badge-success',
            'revoked' => 'badge-danger',
            'expired' => 'badge-warning',
            'pending' => 'badge-secondary',
        ];

        $sourceLabel = [
            'learning_enrollment' => 'Pelatihan PBJ (Arena Belajar)',
            'event_registration'  => 'Event / Seminar PBJ Lampung',
        ];

        $tokenHash = (string) ($row['verification_token_hash'] ?? '');
        $securityCode = $tokenHash !== '' ? strtoupper(substr($tokenHash, 0, 16)) : 'SEC-' . strtoupper(substr(md5((string) ($row['id'] ?? '')), 0, 12));

        $sigService   = new \App\Services\DigitalSignatureService();
        $tteTimestamp = $sigService->formatTteTimestamp($row['issued_at'] ?? null);
        $tteChecksum  = $sigService->computeDocumentChecksum(
            (string) ($row['certificate_number'] ?? ''),
            $tokenHash,
            (string) ($row['issued_at'] ?? '')
        );

        return [
            'id'                  => (int) ($row['id'] ?? 0),
            'certificate_number'  => $row['certificate_number'] ?? '—',
            'user_name'           => ! empty($row['user_name']) ? $row['user_name'] : 'Peserta PBJ Terverifikasi',
            'title'               => $row['title'] ?? 'Kegiatan Belajar Pengadaan Barang/Jasa',
            'source_type'         => $row['source_type'] ?? '',
            'source_label'        => $sourceLabel[$row['source_type'] ?? ''] ?? 'Kegiatan PBJ Resmi',
            'status'              => $status,
            'status_label'        => $statusLabel[$status] ?? 'Tidak Diketahui',
            'status_badge'        => $statusBadgeClass[$status] ?? 'badge-secondary',
            'is_valid'            => $status === 'issued',
            'is_revoked'          => $status === 'revoked',
            'is_expired'          => $status === 'expired',
            'issued_at_human'     => ! empty($row['issued_at'])
                ? date('d F Y', strtotime((string) $row['issued_at']))
                : '—',
            'expires_at_human'    => ! empty($row['expires_at'])
                ? date('d F Y', strtotime((string) $row['expires_at']))
                : 'Berlaku Seumur Hidup',
            'security_code'       => $securityCode,
            'masked_hash'         => $tokenHash !== '' ? substr($tokenHash, 0, 8) . '...' . substr($tokenHash, -8) : 'SHA256-AUTHENTIC',
            'issuer'              => 'Kepala Biro Pengadaan Barang dan Jasa Sekretariat Daerah Provinsi Lampung',
            'verify_url'          => site_url('verifikasi-sertifikat/' . rawurlencode($row['certificate_number'] ?? '')),
            'tte_signer_name'     => \App\Services\DigitalSignatureService::DEFAULT_SIGNER_NAME,
            'tte_signer_position' => \App\Services\DigitalSignatureService::DEFAULT_SIGNER_POS,
            'tte_signer_nip'      => \App\Services\DigitalSignatureService::DEFAULT_SIGNER_NIP,
            'tte_signer_rank'     => \App\Services\DigitalSignatureService::DEFAULT_SIGNER_RANK,
            'tte_timestamp'       => $tteTimestamp,
            'tte_checksum'        => 'SHA256:' . strtoupper(substr($tteChecksum, 0, 16)),
            'tte_checksum_full'   => $tteChecksum,
            'tte_status'          => $status === 'issued' ? 'DITANDATANGANI SECARA ELEKTRONIK & SAH' : 'TIDAK AKTIF',
        ];
    }

    private function renderResult(?array $result, ?string $error, string $searched = '')
    {
        return view('landing/pages/certificate_result', [
            'title'           => 'Hasil Verifikasi Keabsahan Sertifikat | BegawiPengadaan',
            'pageTitle'       => 'Hasil Verifikasi Sertifikat',
            'metaDescription' => 'Hasil verifikasi keabsahan dan otentikasi sertifikat digital resmi Biro Pengadaan Barang dan Jasa Pemerintah Provinsi Lampung.',
            'breadcrumbs'     => [
                ['label' => 'Verifikasi Sertifikat', 'url' => site_url('verifikasi-sertifikat')],
                ['label' => 'Hasil Verifikasi'],
            ],
            'result'          => $result,
            'error'           => $error,
            'searched'        => $searched,
        ]);
    }

    private function resolvePublicTitle(string $sourceType, int $certificateId): string
    {
        if ($sourceType === 'learning_enrollment') {
            $row = $this->db->table('certificates c')
                ->select('co.title AS course_title')
                ->join('enrollments e',       'e.id = c.source_id',  'left')
                ->join('learning_classes lc', 'lc.id = e.class_id',   'left')
                ->join('courses co',          'co.id = lc.course_id', 'left')
                ->where('c.id', $certificateId)
                ->get()
                ->getRowArray();
            return ! empty($row['course_title']) ? $row['course_title'] : 'Kegiatan Belajar Pengadaan Barang/Jasa';
        }

        if ($sourceType === 'event_registration') {
            $row = $this->db->table('certificates c')
                ->select('ev.title AS event_title')
                ->join('event_registrations er', 'er.id = c.source_id', 'left')
                ->join('events ev',              'ev.id = er.event_id',  'left')
                ->where('c.id', $certificateId)
                ->get()
                ->getRowArray();
            return ! empty($row['event_title']) ? $row['event_title'] : 'Seminar / Workshop Pengadaan Barang/Jasa';
        }

        return 'Sertifikasi Pengadaan Barang dan Jasa Pemerintah';
    }

    private function lookupSafeUserName(int $userId): string
    {
        $row = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
        return $row['name'] ?? 'Peserta PBJ Terverifikasi';
    }
}
