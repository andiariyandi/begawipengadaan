<?php

namespace App\Controllers\Certificate;

use App\Controllers\BaseController;
use App\Models\Certificate\CertificateTemplateModel;
use App\Services\AuditService;
use App\Services\CertificatePdfGenerator;
use App\Services\CertificateQrGenerator;
use App\Services\CertificateTemplateRenderer;
use CodeIgniter\Database\ConnectionInterface;
use RuntimeException;

/**
 * CertificateTemplateController — dashboard CRUD untuk certificate_templates.
 *
 * Mendukung perancangan template sertifikat visual dengan upload file template
 * (PNG / JPG / PDF background) dan penataan placeholder sertifikat PBJ resmi.
 */
class CertificateTemplateController extends BaseController
{
    private ConnectionInterface $db;
    private CertificateTemplateModel $templateModel;
    private AuditService $auditService;

    public function __construct()
    {
        $this->db            = db_connect();
        $this->templateModel = new CertificateTemplateModel();
        $this->auditService  = new AuditService();
    }

    public function index()
    {
        $items = $this->templateModel
            ->orderBy('is_active', 'DESC')
            ->orderBy('name', 'ASC')
            ->findAll();

        return view('dashboard/certificates/templates/index', [
            'title'         => 'Template Sertifikat | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Template Sertifikat',
            'activeMenu'    => 'certificates',
            'activeSubmenu' => 'templates',
            'items'         => $items,
        ]);
    }

    public function create()
    {
        return view('dashboard/certificates/templates/form', [
            'title'                 => 'Tambah Template Sertifikat | Dashboard BegawiPengadaan',
            'pageTitle'             => 'Tambah Template Sertifikat',
            'formMode'              => 'create',
            'activeMenu'            => 'certificates',
            'activeSubmenu'         => 'templates',
            'availablePlaceholders' => CertificateTemplateRenderer::getAvailablePlaceholders(),
            'template'              => [
                'id'                 => 0,
                'name'               => '',
                'source_type'        => 'learning',
                'template_payload'   => $this->defaultPayload(),
                'background_file_id' => null,
                'is_active'          => 1,
            ],
            'backgroundAsset'       => null,
        ]);
    }

    public function store()
    {
        try {
            $this->assertManageAccess();
            $actorId = (int) session()->get('user_id');

            $rules = [
                'name'        => 'required|string|max_length[255]',
                'source_type' => 'required|in_list[learning,event]',
                'is_active'   => 'permit_empty|in_list[0,1]',
            ];
            if (! $this->validateData((array) $this->request->getPost(), $rules)) {
                throw new RuntimeException('Validasi gagal: ' . implode('; ', $this->validator->getErrors()));
            }

            $payload = $this->sanitizePayload((array) $this->request->getPost('payload'));
            $now     = date('Y-m-d H:i:s');

            // Handle background file upload jika disertakan
            $bgAssetId = null;
            $bgFile    = $this->request->getFile('background_file');
            if ($bgFile && $bgFile->isValid() && ! $bgFile->hasMoved()) {
                $bgUploadResult                      = $this->handleBackgroundUpload($bgFile, $actorId, 0);
                $bgAssetId                           = $bgUploadResult['asset_id'];
                $payload['background_path']          = $bgUploadResult['relative_path'];
                $payload['background_original_name'] = $bgUploadResult['original_name'];
            }

            $data = [
                'name'               => trim((string) $this->request->getPost('name')),
                'source_type'        => (string) $this->request->getPost('source_type'),
                'template_payload'   => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'background_file_id' => $bgAssetId,
                'is_active'          => (int) ($this->request->getPost('is_active') ?? 1),
                'created_by'         => $actorId,
                'updated_by'         => $actorId,
                'created_at'         => $now,
                'updated_at'         => $now,
            ];

            $ok = $this->db->table('certificate_templates')->insert($data, true);
            if (! $ok) {
                throw new RuntimeException('Gagal menyimpan template.');
            }
            $newId = (int) $this->db->insertID();

            // Kaitkan resource_id pada file_asset
            if ($bgAssetId > 0) {
                $this->db->table('file_assets')->where('id', $bgAssetId)->update(['resource_id' => $newId]);
            }

            $this->auditService->log($actorId, 'certificate_template.created', 'certificate', 'certificate_template', $newId, [
                'name'        => $data['name'],
                'source_type' => $data['source_type'],
                'is_active'   => $data['is_active'],
            ]);

            return redirect()->to(site_url('dashboard/sertifikat/template/' . $newId))
                ->with('success', 'Template sertifikat berhasil dibuat.');
        } catch (RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function detail(string $id)
    {
        $template = $this->templateModel->find((int) $id);
        if (! $template) {
            return $this->notFoundResponse('Template tidak ditemukan.');
        }
        $template['template_payload_pretty'] = $this->prettyJson($template['template_payload'] ?? null);

        $backgroundAsset = null;
        if (! empty($template['background_file_id'])) {
            $backgroundAsset = $this->db->table('file_assets')->where('id', $template['background_file_id'])->get()->getRowArray();
        }

        return view('dashboard/certificates/templates/detail', [
            'title'                 => 'Detail Template | Dashboard BegawiPengadaan',
            'pageTitle'             => 'Detail Template Sertifikat',
            'activeMenu'            => 'certificates',
            'activeSubmenu'         => 'templates',
            'templateId'            => (int) $id,
            'template'              => $template,
            'backgroundAsset'       => $backgroundAsset,
            'availablePlaceholders' => CertificateTemplateRenderer::getAvailablePlaceholders(),
        ]);
    }

    public function edit(string $id)
    {
        $template = $this->templateModel->find((int) $id);
        if (! $template) {
            return $this->notFoundResponse('Template tidak ditemukan.');
        }
        $decoded = json_decode((string) ($template['template_payload'] ?? ''), true);
        if (! is_array($decoded)) {
            $decoded = $this->defaultPayload();
        }
        $template['template_payload'] = $decoded;

        $backgroundAsset = null;
        if (! empty($template['background_file_id'])) {
            $backgroundAsset = $this->db->table('file_assets')->where('id', $template['background_file_id'])->get()->getRowArray();
        }

        return view('dashboard/certificates/templates/form', [
            'title'                 => 'Ubah Template | Dashboard BegawiPengadaan',
            'pageTitle'             => 'Ubah Template Sertifikat',
            'formMode'              => 'edit',
            'activeMenu'            => 'certificates',
            'activeSubmenu'         => 'templates',
            'availablePlaceholders' => CertificateTemplateRenderer::getAvailablePlaceholders(),
            'template'              => $template,
            'backgroundAsset'       => $backgroundAsset,
        ]);
    }

    public function update(string $id)
    {
        try {
            $this->assertManageAccess();
            $actorId = (int) session()->get('user_id');

            $templateId = (int) $id;
            $before     = $this->templateModel->find($templateId);
            if (! $before) {
                throw new RuntimeException('Template tidak ditemukan.');
            }

            $rules = [
                'name'        => 'required|string|max_length[255]',
                'source_type' => 'required|in_list[learning,event]',
                'is_active'   => 'permit_empty|in_list[0,1]',
            ];
            if (! $this->validateData((array) $this->request->getPost(), $rules)) {
                throw new RuntimeException('Validasi gagal: ' . implode('; ', $this->validator->getErrors()));
            }

            $payload   = $this->sanitizePayload((array) $this->request->getPost('payload'));
            $now       = date('Y-m-d H:i:s');
            $bgAssetId = $before['background_file_id'] ? (int) $before['background_file_id'] : null;

            // Jika user memilih hapus gambar background lama
            if ($this->request->getPost('remove_background') == '1') {
                $bgAssetId = null;
                unset($payload['background_path'], $payload['background_original_name']);
            }

            // Jika ada unggahan gambar background baru
            $bgFile = $this->request->getFile('background_file');
            if ($bgFile && $bgFile->isValid() && ! $bgFile->hasMoved()) {
                $bgUploadResult                      = $this->handleBackgroundUpload($bgFile, $actorId, $templateId);
                $bgAssetId                           = $bgUploadResult['asset_id'];
                $payload['background_path']          = $bgUploadResult['relative_path'];
                $payload['background_original_name'] = $bgUploadResult['original_name'];
            }

            $data = [
                'name'               => trim((string) $this->request->getPost('name')),
                'source_type'        => (string) $this->request->getPost('source_type'),
                'template_payload'   => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'background_file_id' => $bgAssetId,
                'is_active'          => (int) ($this->request->getPost('is_active') ?? 0),
                'updated_by'         => $actorId,
                'updated_at'         => $now,
            ];

            $this->db->table('certificate_templates')->where('id', $templateId)->update($data);

            $this->auditService->log($actorId, 'certificate_template.updated', 'certificate', 'certificate_template', $templateId, [
                'before' => ['name' => $before['name'], 'is_active' => $before['is_active'], 'source_type' => $before['source_type']],
                'after'  => ['name' => $data['name'], 'is_active' => $data['is_active'], 'source_type' => $data['source_type']],
            ]);

            return redirect()->to(site_url('dashboard/sertifikat/template/' . $templateId))
                ->with('success', 'Template sertifikat berhasil diperbarui.');
        } catch (RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function activate(string $id)
    {
        return $this->setActive((int) $id, 1);
    }

    public function deactivate(string $id)
    {
        return $this->setActive((int) $id, 0);
    }

    private function setActive(int $id, int $active)
    {
        try {
            $this->assertManageAccess();
            $actorId  = (int) session()->get('user_id');
            $template = $this->templateModel->find($id);
            if (! $template) {
                throw new RuntimeException('Template tidak ditemukan.');
            }

            $now = date('Y-m-d H:i:s');
            $this->db->table('certificate_templates')
                ->where('id', $id)
                ->update([
                    'is_active'  => $active,
                    'updated_by' => $actorId,
                    'updated_at' => $now,
                ]);

            $action = $active === 1 ? 'certificate_template.activated' : 'certificate_template.deactivated';
            $this->auditService->log($actorId, $action, 'certificate', 'certificate_template', $id, [
                'name'      => $template['name'],
                'is_active' => $active,
            ]);

            $msg = $active === 1 ? 'Template diaktifkan.' : 'Template dinonaktifkan.';
            return redirect()->to(site_url('dashboard/sertifikat/template'))
                ->with('success', $msg);
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Pratinjau langsung template sertifikat dalam format HTML interaktif.
     */
    public function preview(string $id)
    {
        $template = $this->templateModel->find((int) $id);
        if (! $template) {
            return $this->notFoundResponse('Template tidak ditemukan.');
        }

        $mockData    = $this->buildMockData($template);
        $renderer    = new CertificateTemplateRenderer();
        $html        = $renderer->render($mockData, $template);

        // Inject sample QR Code
        $sampleQrUri = (new CertificateQrGenerator())->generate(bin2hex(random_bytes(32)));
        $qrImgTag    = '<img alt="QR Sample" src="' . htmlspecialchars($sampleQrUri, ENT_QUOTES, 'UTF-8')
            . '" style="width:140px;height:140px;border:1px solid #cbd5e1;padding:4px;background:#fff;" />';
        $html = str_replace(['<!--CERTIFICATE_QR_PLACEHOLDER-->', '__QR_BLOCK__', '{{qr_code}}', '{{qr_block}}'], $qrImgTag, $html);

        return $this->response->setHeader('Content-Type', 'text/html; charset=UTF-8')->setBody($html);
    }

    /**
     * Pratinjau langsung template sertifikat dalam format binary PDF siap cetak.
     */
    public function previewPdf(string $id)
    {
        $template = $this->templateModel->find((int) $id);
        if (! $template) {
            return $this->notFoundResponse('Template tidak ditemukan.');
        }

        $mockData    = $this->buildMockData($template);
        $renderer    = new CertificateTemplateRenderer();
        $html        = $renderer->render($mockData, $template);

        $sampleQrUri = (new CertificateQrGenerator())->generate(bin2hex(random_bytes(32)));
        $qrImgTag    = '<img alt="QR Sample" src="' . htmlspecialchars($sampleQrUri, ENT_QUOTES, 'UTF-8')
            . '" style="width:140px;height:140px;border:1px solid #cbd5e1;padding:4px;background:#fff;" />';
        $html = str_replace(['<!--CERTIFICATE_QR_PLACEHOLDER-->', '__QR_BLOCK__', '{{qr_code}}', '{{qr_block}}'], $qrImgTag, $html);

        $pdfGenerator = new CertificatePdfGenerator();
        $pdfBytes     = $pdfGenerator->generate($html);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="preview_template_' . $id . '.pdf"')
            ->setBody($pdfBytes);
    }

    // ===============================================================
    // Helpers
    // ===============================================================

    /**
     * Upload dan simpan asset gambar template ke file_assets.
     *
     * @return array{asset_id: int, relative_path: string, original_name: string}
     */
    private function handleBackgroundUpload($file, int $actorId, int $templateId): array
    {
        $ext     = strtolower($file->getClientExtension());
        $allowed = ['png', 'jpg', 'jpeg', 'pdf'];
        if (! in_array($ext, $allowed, true)) {
            throw new RuntimeException('Format file tidak didukung. Harap unggah PNG, JPG, atau PDF.');
        }

        $targetDir = WRITEPATH . 'uploads/certificates/templates';
        if (! is_dir($targetDir) && ! @mkdir($targetDir, 0755, true) && ! is_dir($targetDir)) {
            throw new RuntimeException('Direktori upload template sertifikat tidak dapat dibuat.');
        }

        $storedName = 'template_' . ($templateId ?: 'new') . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $file->move($targetDir, $storedName);

        $fullPath     = $targetDir . DIRECTORY_SEPARATOR . $storedName;
        $relativePath = 'uploads/certificates/templates/' . $storedName;
        $size         = (int) filesize($fullPath);
        $checksum     = @hash_file('sha256', $fullPath) ?: null;
        $now          = date('Y-m-d H:i:s');

        $inserted = $this->db->table('file_assets')->insert([
            'disk'             => 'local',
            'path'             => $relativePath,
            'original_name'    => $file->getClientName(),
            'stored_name'      => $storedName,
            'extension'        => $ext,
            'mime_type'        => $file->getClientMimeType(),
            'size_bytes'       => $size,
            'checksum'         => $checksum,
            'visibility'       => 'public',
            'lifecycle_status' => 'available',
            'resource_type'    => 'certificate_template',
            'resource_id'      => $templateId ?: null,
            'uploaded_by'      => $actorId,
            'available_at'     => $now,
            'created_at'       => $now,
            'updated_at'       => $now,
        ], true);

        if (! $inserted) {
            @unlink($fullPath);
            throw new RuntimeException('Gagal mencatat asset gambar background ke database.');
        }

        return [
            'asset_id'      => (int) $this->db->insertID(),
            'relative_path' => $relativePath,
            'original_name' => $file->getClientName(),
        ];
    }

    /**
     * Mock data otentik untuk pengetesan pratinjau sertifikat secara akurat.
     */
    private function buildMockData(array $template): array
    {
        $isLearning = ($template['source_type'] ?? 'learning') === 'learning';
        $year       = date('Y');

        return [
            'certificate_id'     => 999,
            'certificate_number' => $isLearning ? "SERT-00035/AB-5/05/{$year}" : "SERT-00021/WB-1/05/{$year}",
            'participant_name'   => 'Dr. Ahmad Fauzi, S.T., M.Si.',
            'nip'                => '19820415 200801 1 007',
            'institution_name'   => 'Biro Pengadaan Barang dan Jasa Sekretariat Daerah Provinsi Lampung',
            'unit_name'          => 'Bagian Pengelolaan Pengadaan Barang/Jasa',
            'position_name'      => 'Pengelola Pengadaan Barang/Jasa Ahli Muda',
            'certificate_title'  => $isLearning
                ? 'Pengantar Pengadaan Barang/Jasa Pemerintah (Tingkat Dasar)'
                : 'Webinar Nasional Strategi Pengadaan Berkelanjutan & E-Katalog V6',
            'title'              => $isLearning
                ? 'Pengantar Pengadaan Barang/Jasa Pemerintah (Tingkat Dasar)'
                : 'Webinar Nasional Strategi Pengadaan Berkelanjutan & E-Katalog V6',
            'subtitle'           => 'Batch I Tahun Anggaran ' . $year,
            'activity_type'      => $isLearning ? 'Arena Belajar PBJ' : 'Webinar PBJ',
            'tipe_kegiatan'      => $isLearning ? 'Arena Belajar' : 'Webinar',
            'kode_tipe'          => $isLearning ? 'AB' : 'WB',
            'id_kegiatan'        => $isLearning ? 5 : 1,
            'jam_pelajaran'      => $isLearning ? '24 JP' : '4 JP',
            'jp'                 => $isLearning ? '24 JP' : '4 JP',
            'final_score'        => '94.5',
            'predicate'          => 'Sangat Baik',
            'issued_at'          => date('Y-m-d H:i:s'),
            'event_start_at'     => date('Y-m-d 08:00:00', strtotime('-5 days')),
            'event_end_at'       => date('Y-m-d 16:00:00'),
            'verification_url'   => site_url('verifikasi-sertifikat/' . ($isLearning ? "SERT-00035-AB-5-05-{$year}" : "SERT-00021-WB-1-05-{$year}")),
        ];
    }

    /**
     * Sanitize payload JSON.
     */
    private function sanitizePayload(array $raw): array
    {
        $bodyHtml = isset($raw['body_html']) && is_string($raw['body_html']) ? $raw['body_html'] : '';
        $css      = isset($raw['css'])       && is_string($raw['css'])       ? $raw['css']       : '';
        $issuer   = isset($raw['issuer_name']) && is_string($raw['issuer_name']) && trim($raw['issuer_name']) !== ''
            ? trim($raw['issuer_name'])
            : 'Pemerintah Provinsi Lampung — Biro Pengadaan Barang dan Jasa';
        $qrPos    = isset($raw['qr_position']) && in_array($raw['qr_position'], ['right', 'bottom'], true)
            ? $raw['qr_position']
            : 'right';

        $signerName = isset($raw['signer_name']) && is_string($raw['signer_name']) && trim($raw['signer_name']) !== ''
            ? trim($raw['signer_name'])
            : 'Dr. H. Rahmad Hidayat, S.E., M.Si.';
        $signerPos  = isset($raw['signer_position']) && is_string($raw['signer_position']) && trim($raw['signer_position']) !== ''
            ? trim($raw['signer_position'])
            : 'Kepala Biro Pengadaan Barang dan Jasa';
        $signerNip  = isset($raw['signer_nip']) && is_string($raw['signer_nip']) && trim($raw['signer_nip']) !== ''
            ? trim($raw['signer_nip'])
            : '19750812 199903 1 004';

        $clean = [
            'body_html'       => mb_substr($bodyHtml, 0, 60000),
            'css'             => mb_substr($css, 0, 20000),
            'issuer_name'     => mb_substr($issuer, 0, 255),
            'signer_name'     => mb_substr($signerName, 0, 255),
            'signer_position' => mb_substr($signerPos, 0, 255),
            'signer_nip'      => mb_substr($signerNip, 0, 64),
            'qr_position'     => $qrPos,
        ];

        if (! empty($raw['background_path'])) {
            $clean['background_path'] = (string) $raw['background_path'];
        }
        if (! empty($raw['background_original_name'])) {
            $clean['background_original_name'] = (string) $raw['background_original_name'];
        }

        return $clean;
    }

    private function defaultPayload(): array
    {
        return [
            'body_html'       => '',
            'css'             => '',
            'issuer_name'     => 'Pemerintah Provinsi Lampung — Biro Pengadaan Barang dan Jasa',
            'signer_name'     => 'Dr. H. Rahmad Hidayat, S.E., M.Si.',
            'signer_position' => 'Kepala Biro Pengadaan Barang dan Jasa',
            'signer_nip'      => '19750812 199903 1 004',
            'qr_position'     => 'right',
        ];
    }

    private function prettyJson($raw): string
    {
        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }
        return (string) json_encode($this->defaultPayload(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    private function assertManageAccess(): void
    {
        $actorId = (int) session()->get('user_id');
        if (session()->get('isLoggedIn') !== true || $actorId < 1) {
            throw new RuntimeException('Akses ditolak.');
        }

        $actor = $this->db->table('users')->where('id', $actorId)->get()->getRowArray();
        if (! $actor || ($actor['account_status'] ?? '') !== 'active') {
            throw new RuntimeException('Akun tidak aktif.');
        }

        // Permission explicit
        $hasManage = $this->db->table('user_roles ur')
            ->join('roles r', 'r.id = ur.role_id')
            ->join('role_permissions rp', 'rp.role_id = r.id')
            ->join('permissions p', 'p.id = rp.permission_id')
            ->where([
                'ur.user_id' => $actorId,
                'ur.status'  => 'active',
                'p.key'      => 'certificate.template.manage',
            ])
            ->countAllResults() > 0;
        if ($hasManage) {
            return;
        }

        // Fallback: admin/super_admin
        $isAdmin = $this->db->table('user_roles ur')
            ->join('roles r', 'r.id = ur.role_id')
            ->where(['ur.user_id' => $actorId, 'ur.status' => 'active'])
            ->groupStart()
            ->where('r.key', 'admin')
            ->orWhere('r.key', 'super_admin')
            ->orWhere('r.name', 'admin')
            ->orWhere('r.name', 'super_admin')
            ->groupEnd()
            ->countAllResults() > 0;

        if (! $isAdmin) {
            throw new RuntimeException('Anda tidak memiliki permission untuk mengelola template sertifikat.');
        }
    }

    private function notFoundResponse(string $msg)
    {
        return view('dashboard/errors/404', [
            'title'      => 'Template tidak ditemukan',
            'pageTitle'  => 'Template tidak ditemukan',
            'activeMenu' => 'certificates',
            'message'    => $msg,
        ]);
    }
}
