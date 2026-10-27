<?php

namespace App\Controllers\Certificate;

use App\Controllers\BaseController;
use App\Models\Certificate\CertificateTemplateModel;
use App\Services\AuditService;
use App\Services\CertificatePdfGenerator;
use App\Services\CertificatePdfPlaceholderService;
use App\Services\CertificateQrGenerator;
use App\Services\CertificateTemplateRenderer;
use CodeIgniter\Database\ConnectionInterface;
use RuntimeException;

/**
 * CertificateTemplateController — dashboard CRUD untuk certificate_templates.
 *
 * Mendukung perancangan template sertifikat dengan:
 * - Penggantian otomatis {{placeholder}} yang diketik langsung di dalam file PDF
 * - Editor visual posisi & gaya placeholder di atas kanvas PDF
 * - Upload file template (PDF / PNG / JPG background)
 *
 * Mengakomodasi:
 * 1. Arena Belajar: Tingkat Program (mengunci course & kelas di bawahnya),
 *    Tingkat Course (mengunci kelas di bawahnya), atau Khusus Kelas (hanya jika program & course kosong).
 * 2. Event & Agenda: Unik 1:1 per acara, tidak dapat dibuat sama antar acara.
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
        $rawItems = $this->templateModel
            ->orderBy('is_active', 'DESC')
            ->orderBy('name', 'ASC')
            ->findAll();

        $items = [];
        foreach ($rawItems as $row) {
            $payload = json_decode((string) ($row['template_payload'] ?? ''), true);
            if (! is_array($payload)) {
                $payload = [];
            }

            $scope     = $payload['target_scope'] ?? 'global';
            $metaTitle = '';
            $lockDesc  = '';

            if ($row['source_type'] === 'event') {
                $eventId = (int) ($payload['event_id'] ?? 0);
                if ($eventId > 0) {
                    $ev = $this->db->table('events')->select('title')->where('id', $eventId)->get()->getRowArray();
                    $metaTitle = $ev ? $ev['title'] : "Event #{$eventId}";
                }
                $lockDesc = 'Template Unik 1:1 Khusus Event';
            } else {
                if ($scope === 'program') {
                    $progId = (int) ($payload['program_id'] ?? 0);
                    if ($progId > 0) {
                        $p = $this->db->table('programs')->select('title')->where('id', $progId)->get()->getRowArray();
                        $metaTitle = $p ? $p['title'] : "Program #{$progId}";
                    }
                    $lockDesc = 'Mengunci Seluruh Course & Kelas di Bawahnya';
                } elseif ($scope === 'course') {
                    $courseId = (int) ($payload['course_id'] ?? 0);
                    if ($courseId > 0) {
                        $co = $this->db->table('courses')->select('title')->where('id', $courseId)->get()->getRowArray();
                        $metaTitle = $co ? $co['title'] : "Course #{$courseId}";
                    }
                    $lockDesc = 'Mengunci Seluruh Kelas di Bawahnya';
                } elseif ($scope === 'class') {
                    $classId = (int) ($payload['class_id'] ?? 0);
                    if ($classId > 0) {
                        $cl = $this->db->table('learning_classes')->select('name')->where('id', $classId)->get()->getRowArray();
                        $metaTitle = $cl ? $cl['name'] : "Kelas #{$classId}";
                    }
                    $lockDesc = 'Khusus 1 Kelas Pembelajaran';
                } else {
                    $scope     = 'global';
                    $metaTitle = 'Seluruh Arena Belajar (Default)';
                    $lockDesc  = 'Fallback Default Pembelajaran';
                }
            }

            $row['target_scope'] = $scope;
            $row['target_title'] = $metaTitle;
            $row['lock_desc']    = $lockDesc;
            $items[] = $row;
        }

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
        $options = $this->loadHierarchyOptions();

        $sourceTypeParam = (string) $this->request->getGet('source_type');
        $eventIdParam    = (int)    $this->request->getGet('event_id');

        $initialSourceType = in_array($sourceTypeParam, ['learning', 'event'], true) ? $sourceTypeParam : 'learning';
        $initialName       = '';
        $initialPayload    = $this->defaultPayload();

        $selectedEvent = null;
        if ($eventIdParam > 0) {
            $selectedEvent = $this->db->table('events')->where('id', $eventIdParam)->where('deleted_at IS NULL')->get()->getRowArray();
            if ($selectedEvent) {
                $initialSourceType                = 'event';
                $initialName                      = 'Template Sertifikat - ' . $selectedEvent['title'];
                $initialPayload['target_scope']   = 'event';
                $initialPayload['event_id']       = $eventIdParam;
                $initialPayload['event_title']    = $selectedEvent['title'];
            }
        }

        $mockTemplate = [
            'id'                 => 0,
            'name'               => $initialName,
            'source_type'        => $initialSourceType,
            'template_payload'   => $initialPayload,
            'background_file_id' => null,
            'is_active'          => 1,
        ];

        $sampleQrUri = (new CertificateQrGenerator())->generate(bin2hex(random_bytes(16)));

        return view('dashboard/certificates/templates/form', [
            'title'                 => 'Tambah Template Sertifikat | Dashboard BegawiPengadaan',
            'pageTitle'             => 'Tambah Template Sertifikat',
            'formMode'              => 'create',
            'activeMenu'            => 'certificates',
            'activeSubmenu'         => 'templates',
            'availablePlaceholders' => CertificateTemplateRenderer::getAvailablePlaceholders(),
            'sampleValues'          => $this->buildSamplePlaceholderMap($mockTemplate),
            'sampleQrUri'           => $sampleQrUri,
            'template'              => $mockTemplate,
            'backgroundAsset'       => null,
            'hierarchyOptions'      => $options,
            'selectedEvent'         => $selectedEvent,
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

            $sourceType = (string) $this->request->getPost('source_type');
            $payload    = $this->sanitizePayload((array) $this->request->getPost('payload'));

            // Validasi & Proteksi Anti-Tumpang-Tindih & Unik 1:1 Event
            $this->validateScopeAndLock($sourceType, $payload);

            $now = date('Y-m-d H:i:s');

            // Handle background file upload jika disertakan
            $bgAssetId = null;
            $bgFile    = $this->request->getFile('background_file');
            if ($bgFile && $bgFile->isValid() && ! $bgFile->hasMoved()) {
                $bgUploadResult                      = $this->handleBackgroundUpload($bgFile, $actorId, 0);
                $bgAssetId                           = $bgUploadResult['asset_id'];
                $payload['background_path']          = $bgUploadResult['relative_path'];
                $payload['background_original_name'] = $bgUploadResult['original_name'];

                // Jika file adalah PDF dan pdf_placeholders belum terisi, deteksi otomatis dari PDF
                if ($bgUploadResult['extension'] === 'pdf') {
                    $inspection = (new CertificatePdfPlaceholderService())->inspectPdf($bgUploadResult['full_path']);
                    if (! empty($inspection['page_width_mm'])) {
                        $payload['page_width_mm']  = $inspection['page_width_mm'];
                        $payload['page_height_mm'] = $inspection['page_height_mm'];
                    }
                    if (empty($payload['pdf_placeholders']) && ! empty($inspection['placeholders'])) {
                        $payload['pdf_placeholders'] = $inspection['placeholders'];
                        $payload['layout_mode']      = 'pdf_placeholders';
                    }
                }
            }

            $data = [
                'name'               => trim((string) $this->request->getPost('name')),
                'source_type'        => $sourceType,
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
                'name'             => $data['name'],
                'source_type'      => $data['source_type'],
                'target_scope'     => $payload['target_scope'] ?? 'global',
                'layout_mode'      => $payload['layout_mode'] ?? 'pdf_placeholders',
                'placeholder_cnt'  => count($payload['pdf_placeholders'] ?? []),
                'is_active'        => $data['is_active'],
            ]);

            return redirect()->to(site_url('dashboard/sertifikat/template/' . $newId))
                ->with('success', 'Template sertifikat berhasil dibuat dan placeholder PDF telah dikonfigurasi.');
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

        $payload = json_decode((string) ($template['template_payload'] ?? ''), true);
        if (! is_array($payload)) {
            $payload = [];
        }

        $scopeDetails = [
            'scope'       => $payload['target_scope'] ?? 'global',
            'entity_type' => '',
            'title'       => '',
            'lock_info'   => '',
        ];

        if ($template['source_type'] === 'event') {
            $eventId = (int) ($payload['event_id'] ?? 0);
            $ev = $this->db->table('events')->where('id', $eventId)->get()->getRowArray();
            $scopeDetails['entity_type'] = 'Event / Agenda PBJ';
            $scopeDetails['title']       = $ev ? $ev['title'] : "Event #{$eventId}";
            $scopeDetails['lock_info']   = 'Khusus dan unik untuk acara ini.';
        } else {
            $scope = $payload['target_scope'] ?? 'global';
            if ($scope === 'program') {
                $progId = (int) ($payload['program_id'] ?? 0);
                $p = $this->db->table('programs')->where('id', $progId)->get()->getRowArray();
                $scopeDetails['entity_type'] = 'Program Pembelajaran';
                $scopeDetails['title']       = $p ? $p['title'] : "Program #{$progId}";
                $scopeDetails['lock_info']   = 'Mengunci seluruh Course dan Kelas di bawah Program ini.';
            } elseif ($scope === 'course') {
                $courseId = (int) ($payload['course_id'] ?? 0);
                $co = $this->db->table('courses')->where('id', $courseId)->get()->getRowArray();
                $scopeDetails['entity_type'] = 'Course PBJ';
                $scopeDetails['title']       = $co ? $co['title'] : "Course #{$courseId}";
                $scopeDetails['lock_info']   = 'Mengunci seluruh Kelas di bawah Course ini.';
            } elseif ($scope === 'class') {
                $classId = (int) ($payload['class_id'] ?? 0);
                $cl = $this->db->table('learning_classes')->where('id', $classId)->get()->getRowArray();
                $scopeDetails['entity_type'] = 'Kelas Pembelajaran Spesifik';
                $scopeDetails['title']       = $cl ? $cl['name'] : "Kelas #{$classId}";
                $scopeDetails['lock_info']   = 'Hanya berlaku untuk kelas ini.';
            } else {
                $scopeDetails['entity_type'] = 'Standar Arena Belajar (Global)';
                $scopeDetails['title']       = 'Seluruh Pembelajaran PBJ';
                $scopeDetails['lock_info']   = 'Berlaku jika Program, Course, dan Kelas belum diset khusus.';
            }
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
            'scopeDetails'          => $scopeDetails,
        ]);
    }

    public function edit(string $id)
    {
        $templateId = (int) $id;
        $template   = $this->templateModel->find($templateId);
        if (! $template) {
            return $this->notFoundResponse('Template tidak ditemukan.');
        }
        $decoded = json_decode((string) ($template['template_payload'] ?? ''), true);
        if (! is_array($decoded)) {
            $decoded = $this->defaultPayload();
        }

        // Jika template memiliki file PDF tetapi belum punya daftar pdf_placeholders, deteksi otomatis
        $renderer = new CertificateTemplateRenderer();
        if (empty($decoded['pdf_placeholders']) && $renderer->isPdfBackground($template)) {
            $pdfPath = $renderer->getBackgroundPhysicalPath($template);
            if ($pdfPath && is_file($pdfPath)) {
                $inspection = (new CertificatePdfPlaceholderService())->inspectPdf($pdfPath);
                if (! empty($inspection['placeholders'])) {
                    $decoded['pdf_placeholders'] = $inspection['placeholders'];
                    $decoded['page_width_mm']    = $inspection['page_width_mm'];
                    $decoded['page_height_mm']   = $inspection['page_height_mm'];
                    $decoded['layout_mode']      = 'pdf_placeholders';
                }
            }
        }

        $template['template_payload'] = $decoded;

        $backgroundAsset = null;
        if (! empty($template['background_file_id'])) {
            $backgroundAsset = $this->db->table('file_assets')->where('id', $template['background_file_id'])->get()->getRowArray();
        }

        $options = $this->loadHierarchyOptions($templateId);

        $selectedEvent = null;
        if ($template['source_type'] === 'event' && ! empty($decoded['event_id'])) {
            $selectedEvent = $this->db->table('events')->where('id', (int) $decoded['event_id'])->get()->getRowArray();
        }

        $sampleQrUri = (new CertificateQrGenerator())->generate(bin2hex(random_bytes(16)));

        return view('dashboard/certificates/templates/form', [
            'title'                 => 'Ubah Template | Dashboard BegawiPengadaan',
            'pageTitle'             => 'Ubah Template Sertifikat',
            'formMode'              => 'edit',
            'activeMenu'            => 'certificates',
            'activeSubmenu'         => 'templates',
            'availablePlaceholders' => CertificateTemplateRenderer::getAvailablePlaceholders(),
            'sampleValues'          => $this->buildSamplePlaceholderMap($template),
            'sampleQrUri'           => $sampleQrUri,
            'template'              => $template,
            'backgroundAsset'       => $backgroundAsset,
            'hierarchyOptions'      => $options,
            'selectedEvent'         => $selectedEvent,
        ]);
    }

    public function update(string $id)
    {
        try {
            $this->assertManageAccess();
            $actorId    = (int) session()->get('user_id');
            $templateId = (int) $id;
            $before     = $this->templateModel->find($templateId);
            if (! $before) {
                throw new RuntimeException('Template tidak ditemukan.');
            }

            $beforePayload = json_decode((string) ($before['template_payload'] ?? ''), true);
            if (! is_array($beforePayload)) {
                $beforePayload = [];
            }

            $rules = [
                'name'        => 'required|string|max_length[255]',
                'source_type' => 'required|in_list[learning,event]',
                'is_active'   => 'permit_empty|in_list[0,1]',
            ];
            if (! $this->validateData((array) $this->request->getPost(), $rules)) {
                throw new RuntimeException('Validasi gagal: ' . implode('; ', $this->validator->getErrors()));
            }

            $sourceType = (string) $this->request->getPost('source_type');
            $payload    = $this->sanitizePayload((array) $this->request->getPost('payload'));

            // Pertahankan info background lama kecuali dihapus atau diganti
            if (empty($payload['background_path']) && ! empty($beforePayload['background_path'])) {
                $payload['background_path'] = $beforePayload['background_path'];
            }
            if (empty($payload['background_original_name']) && ! empty($beforePayload['background_original_name'])) {
                $payload['background_original_name'] = $beforePayload['background_original_name'];
            }

            // Validasi & Proteksi Anti-Tumpang-Tindih & Unik 1:1 Event
            $this->validateScopeAndLock($sourceType, $payload, $templateId);

            $now       = date('Y-m-d H:i:s');
            $bgAssetId = $before['background_file_id'] ? (int) $before['background_file_id'] : null;

            // Jika user memilih hapus gambar background lama
            if ($this->request->getPost('remove_background') == '1') {
                $bgAssetId = null;
                unset($payload['background_path'], $payload['background_original_name']);
            }

            // Jika ada unggahan file background baru
            $bgFile = $this->request->getFile('background_file');
            if ($bgFile && $bgFile->isValid() && ! $bgFile->hasMoved()) {
                $bgUploadResult                      = $this->handleBackgroundUpload($bgFile, $actorId, $templateId);
                $bgAssetId                           = $bgUploadResult['asset_id'];
                $payload['background_path']          = $bgUploadResult['relative_path'];
                $payload['background_original_name'] = $bgUploadResult['original_name'];

                if ($bgUploadResult['extension'] === 'pdf') {
                    $inspection = (new CertificatePdfPlaceholderService())->inspectPdf($bgUploadResult['full_path']);
                    if (! empty($inspection['page_width_mm'])) {
                        $payload['page_width_mm']  = $inspection['page_width_mm'];
                        $payload['page_height_mm'] = $inspection['page_height_mm'];
                    }
                    if (empty($payload['pdf_placeholders']) && ! empty($inspection['placeholders'])) {
                        $payload['pdf_placeholders'] = $inspection['placeholders'];
                        $payload['layout_mode']      = 'pdf_placeholders';
                    }
                }
            }

            $data = [
                'name'               => trim((string) $this->request->getPost('name')),
                'source_type'        => $sourceType,
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

    /**
     * Endpoint AJAX untuk menginspeksi file PDF yang baru dipilih/diunggah
     * dan mendeteksi seluruh {{placeholder}} beserta koordinatnya.
     *
     * POST /dashboard/sertifikat/template/inspect-pdf
     */
    public function inspectPdf()
    {
        try {
            $this->assertManageAccess();

            $file       = $this->request->getFile('background_file');
            $templateId = (int) $this->request->getPost('template_id');
            $tempPath   = null;
            $cleanup    = false;

            if ($file && $file->isValid() && ! $file->hasMoved()) {
                $ext = strtolower($file->getClientExtension());
                if ($ext !== 'pdf') {
                    return $this->response->setStatusCode(400)->setJSON([
                        'status'  => 'error',
                        'message' => 'File yang diinspeksi harus berformat PDF.',
                    ]);
                }
                $tempPath = $file->getTempName();
            } elseif ($templateId > 0) {
                $tpl = $this->templateModel->find($templateId);
                if ($tpl) {
                    $tempPath = (new CertificateTemplateRenderer())->getBackgroundPhysicalPath($tpl);
                }
            }

            if (! $tempPath || ! is_file($tempPath)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'File PDF tidak ditemukan untuk diinspeksi.',
                ]);
            }

            $service    = new CertificatePdfPlaceholderService();
            $inspection = $service->inspectPdf($tempPath);

            if ($cleanup && is_file($tempPath)) {
                @unlink($tempPath);
            }

            return $this->response->setJSON([
                'status'         => 'ok',
                'page_width_pt'  => $inspection['page_width_pt'],
                'page_height_pt' => $inspection['page_height_pt'],
                'page_width_mm'  => $inspection['page_width_mm'],
                'page_height_mm' => $inspection['page_height_mm'],
                'orientation'    => $inspection['orientation'],
                'placeholders'   => $inspection['placeholders'],
                'count'          => count($inspection['placeholders']),
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Gagal menginspeksi PDF: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Endpoint untuk merender pratinjau PDF secara langsung dari halaman tambah/ubah
     * sebelum atau sesudah disimpan ke database.
     *
     * POST /dashboard/sertifikat/template/live-preview-pdf
     */
    public function livePreviewPdf()
    {
        try {
            $this->assertManageAccess();

            $sourceType = (string) ($this->request->getPost('source_type') ?: 'learning');
            $templateId = (int) $this->request->getPost('template_id');
            $payload    = $this->sanitizePayload((array) $this->request->getPost('payload'));

            $tempPdfPath = null;
            $cleanupTemp = false;

            $bgFile = $this->request->getFile('background_file');
            if ($bgFile && $bgFile->isValid() && ! $bgFile->hasMoved()) {
                $ext      = strtolower($bgFile->getClientExtension());
                $tempDir  = WRITEPATH . 'uploads/certificates/tmp';
                if (! is_dir($tempDir)) {
                    @mkdir($tempDir, 0755, true);
                }
                $tmpName     = 'live_preview_' . bin2hex(random_bytes(6)) . '.' . $ext;
                $bgFile->move($tempDir, $tmpName);
                $tempPdfPath = $tempDir . DIRECTORY_SEPARATOR . $tmpName;
                $cleanupTemp = true;
                $payload['background_path'] = 'uploads/certificates/tmp/' . $tmpName;
            } elseif ($templateId > 0) {
                $existing = $this->templateModel->find($templateId);
                if ($existing) {
                    $existingPayload = json_decode((string) ($existing['template_payload'] ?? ''), true);
                    if (is_array($existingPayload) && ! empty($existingPayload['background_path'])) {
                        $payload['background_path'] = $existingPayload['background_path'];
                    }
                    $tempPdfPath = (new CertificateTemplateRenderer())->getBackgroundPhysicalPath($existing);
                }
            }

            $mockTemplate = [
                'id'                 => $templateId ?: 999,
                'name'               => (string) ($this->request->getPost('name') ?: 'Pratinjau Template Sertifikat'),
                'source_type'        => $sourceType,
                'template_payload'   => $payload,
                'background_file_id' => null,
                'is_active'          => 1,
            ];

            $mockData    = $this->buildMockData($mockTemplate);
            $renderer    = new CertificateTemplateRenderer();
            $html        = $renderer->render($mockData, $mockTemplate);

            $sampleQrUri = (new CertificateQrGenerator())->generate(bin2hex(random_bytes(32)));
            $qrImgTag    = '<img alt="QR Sample" src="' . htmlspecialchars($sampleQrUri, ENT_QUOTES, 'UTF-8')
                . '" style="width:140px;height:140px;border:1px solid #cbd5e1;padding:4px;background:#fff;" />';
            $html = str_replace(['<!--CERTIFICATE_QR_PLACEHOLDER-->', '__QR_BLOCK__', '{{qr_code}}', '{{qr_block}}'], $qrImgTag, $html);

            $pdfGenerator = new CertificatePdfGenerator();
            $pdfBytes     = $pdfGenerator->generate($html, $tempPdfPath ?: $mockTemplate);

            if ($cleanupTemp && $tempPdfPath && is_file($tempPdfPath)) {
                @unlink($tempPdfPath);
            }

            return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'inline; filename="live_preview_sertifikat.pdf"')
                ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->setHeader('Pragma', 'no-cache')
                ->setHeader('Expires', '0')
                ->setBody($pdfBytes);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setBody('Gagal membuat pratinjau PDF: ' . $e->getMessage());
        }
    }

    /**
     * Stream file background template (PDF / PNG / JPG) dari WRITEPATH untuk kanvas editor & detail.
     *
     * GET /dashboard/sertifikat/template/{id}/background-file
     */
    public function serveBackgroundFile(string $id)
    {
        $template = $this->templateModel->find((int) $id);
        if (! $template) {
            return $this->response->setStatusCode(404)->setBody('Template tidak ditemukan.');
        }

        $renderer = new CertificateTemplateRenderer();
        $filePath = $renderer->getBackgroundPhysicalPath($template);
        if (! $filePath || ! is_file($filePath)) {
            return $this->response->setStatusCode(404)->setBody('File background tidak ditemukan.');
        }

        $ext  = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'pdf'         => 'application/pdf',
            'png'         => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'webp'        => 'image/webp',
            default       => 'application/octet-stream',
        };

        $bytes = @file_get_contents($filePath);
        if ($bytes === false) {
            return $this->response->setStatusCode(500)->setBody('Gagal membaca file background.');
        }

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', 'inline; filename="' . basename($filePath) . '"')
            ->setHeader('Cache-Control', 'private, max-age=3600')
            ->setBody($bytes);
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

        $renderer = new CertificateTemplateRenderer();

        // Bila template menggunakan background PDF, arahkan langsung ke pratinjau PDF utuh
        // agar latar belakang vektor dan penggantian placeholder tampil menyatu secara presisi.
        if ($renderer->isPdfBackground($template)) {
            return redirect()->to(site_url('dashboard/sertifikat/template/' . (int) $id . '/preview-pdf'));
        }

        $mockData    = $this->buildMockData($template);
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
        $pdfBytes     = $pdfGenerator->generate($html, $template);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="preview_template_' . $id . '.pdf"')
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', '0')
            ->setBody($pdfBytes);
    }

    // ===============================================================
    // Hierarchy Options & Scope Validation Helpers
    // ===============================================================

    private function loadHierarchyOptions(?int $excludeTemplateId = null): array
    {
        $programs = $this->db->table('programs')
            ->select('id, title')
            ->where('deleted_at IS NULL')
            ->orderBy('title', 'ASC')
            ->get()
            ->getResultArray();

        $courses = $this->db->table('courses')
            ->select('id, program_id, title')
            ->where('deleted_at IS NULL')
            ->orderBy('title', 'ASC')
            ->get()
            ->getResultArray();

        $classes = $this->db->table('learning_classes lc')
            ->select('lc.id, lc.course_id, lc.name, co.program_id, co.title AS course_title')
            ->join('courses co', 'co.id = lc.course_id', 'left')
            ->where('lc.deleted_at IS NULL')
            ->orderBy('lc.name', 'ASC')
            ->get()
            ->getResultArray();

        $events = $this->db->table('events')
            ->select('id, title, type, mode, start_at')
            ->where('deleted_at IS NULL')
            ->orderBy('start_at', 'DESC')
            ->get()
            ->getResultArray();

        $lockInfo       = $this->templateModel->getLearningHierarchyLockInfo($excludeTemplateId);
        $assignedEvents = $this->templateModel->getAssignedEventMap($excludeTemplateId);

        return [
            'programs'       => $programs,
            'courses'        => $courses,
            'classes'        => $classes,
            'events'         => $events,
            'lockInfo'       => $lockInfo,
            'assignedEvents' => $assignedEvents,
        ];
    }

    private function validateScopeAndLock(string $sourceType, array &$payload, ?int $templateId = null): void
    {
        if ($sourceType === 'event') {
            $eventId = (int) ($payload['event_id'] ?? $this->request->getPost('event_id'));
            if ($eventId < 1) {
                throw new RuntimeException('Harap pilih Event / Agenda untuk template sertifikat ini.');
            }
            $event = $this->db->table('events')->where('id', $eventId)->where('deleted_at IS NULL')->get()->getRowArray();
            if (! $event) {
                throw new RuntimeException('Event yang dipilih tidak ditemukan.');
            }

            // Validasi Unik 1:1 antar acara
            $assigned = $this->templateModel->getAssignedEventMap($templateId);
            if (isset($assigned[$eventId])) {
                throw new RuntimeException(
                    "Event \"{$event['title']}\" sudah memiliki template sertifikat aktif (\"{$assigned[$eventId]['template_name']}\"). " .
                        "Setiap event dan agenda selalu berbeda dan tidak dapat dibuat sama antar acara."
                );
            }

            $payload['target_scope'] = 'event';
            $payload['event_id']     = $eventId;
            $payload['event_title']  = $event['title'];
            $payload['event_type']   = $event['type'];
            return;
        }

        // Learning
        $scope = (string) ($payload['target_scope'] ?? $this->request->getPost('target_scope') ?? 'global');
        if (! in_array($scope, ['program', 'course', 'class', 'global'], true)) {
            $scope = 'global';
        }

        $lockInfo = $this->templateModel->getLearningHierarchyLockInfo($templateId);

        if ($scope === 'program') {
            $programId = (int) ($payload['program_id'] ?? $this->request->getPost('program_id'));
            if ($programId < 1) {
                throw new RuntimeException('Harap pilih Program pembelajaran untuk template tingkat program ini.');
            }
            $prog = $this->db->table('programs')->where('id', $programId)->where('deleted_at IS NULL')->get()->getRowArray();
            if (! $prog) {
                throw new RuntimeException('Program pembelajaran tidak ditemukan.');
            }

            if (isset($lockInfo['locked_programs'][$programId])) {
                throw new RuntimeException(
                    "Program \"{$prog['title']}\" sudah memiliki template aktif (\"{$lockInfo['locked_programs'][$programId]['template_name']}\")."
                );
            }

            $payload['target_scope']  = 'program';
            $payload['program_id']    = $programId;
            $payload['program_title'] = $prog['title'];
            $payload['course_id']     = 0;
            $payload['class_id']      = 0;
            return;
        }

        if ($scope === 'course') {
            $courseId = (int) ($payload['course_id'] ?? $this->request->getPost('course_id'));
            if ($courseId < 1) {
                throw new RuntimeException('Harap pilih Course pembelajaran untuk template tingkat course ini.');
            }
            $course = $this->db->table('courses')->where('id', $courseId)->where('deleted_at IS NULL')->get()->getRowArray();
            if (! $course) {
                throw new RuntimeException('Course pembelajaran tidak ditemukan.');
            }

            // Cek apakah Program induk sudah memiliki template (jika ya, course ini terkunci!)
            $progId = (int) ($course['program_id'] ?? 0);
            if ($progId > 0 && isset($lockInfo['locked_programs'][$progId])) {
                throw new RuntimeException(
                    "Course \"{$course['title']}\" terkunci karena Program induknya (\"{$lockInfo['locked_programs'][$progId]['template_name']}\") " .
                        "sudah memiliki template sertifikat aktif yang berlaku untuk seluruh course dan kelas di bawahnya."
                );
            }

            if (isset($lockInfo['locked_courses'][$courseId])) {
                throw new RuntimeException(
                    "Course \"{$course['title']}\" sudah memiliki template aktif (\"{$lockInfo['locked_courses'][$courseId]['template_name']}\")."
                );
            }

            $payload['target_scope']  = 'course';
            $payload['course_id']     = $courseId;
            $payload['course_title']  = $course['title'];
            $payload['program_id']    = $progId;
            $payload['class_id']      = 0;
            return;
        }

        if ($scope === 'class') {
            $classId = (int) ($payload['class_id'] ?? $this->request->getPost('class_id'));
            if ($classId < 1) {
                throw new RuntimeException('Harap pilih Kelas pembelajaran untuk template khusus kelas ini.');
            }
            $class = $this->db->table('learning_classes lc')
                ->select('lc.id, lc.course_id, lc.name, co.program_id, co.title AS course_title')
                ->join('courses co', 'co.id = lc.course_id', 'left')
                ->where('lc.id', $classId)
                ->where('lc.deleted_at IS NULL')
                ->get()
                ->getRowArray();
            if (! $class) {
                throw new RuntimeException('Kelas pembelajaran tidak ditemukan.');
            }

            $progId   = (int) ($class['program_id'] ?? 0);
            $courseId = (int) ($class['course_id'] ?? 0);

            if ($progId > 0 && isset($lockInfo['locked_programs'][$progId])) {
                throw new RuntimeException(
                    "Kelas \"{$class['name']}\" terkunci karena Program induknya sudah memiliki template sertifikat aktif. " .
                        "Semua kelas di bawah program tersebut otomatis menggunakan template tingkat program."
                );
            }

            if ($courseId > 0 && isset($lockInfo['locked_courses'][$courseId])) {
                throw new RuntimeException(
                    "Kelas \"{$class['name']}\" terkunci karena Course induknya (\"{$class['course_title']}\") sudah memiliki template aktif. " .
                        "Semua kelas di bawah course tersebut otomatis menggunakan template tingkat course."
                );
            }

            if (isset($lockInfo['locked_classes'][$classId])) {
                throw new RuntimeException(
                    "Kelas \"{$class['name']}\" sudah memiliki template khusus aktif (\"{$lockInfo['locked_classes'][$classId]['template_name']}\")."
                );
            }

            $payload['target_scope'] = 'class';
            $payload['class_id']     = $classId;
            $payload['class_name']   = $class['name'];
            $payload['course_id']    = $courseId;
            $payload['program_id']   = $progId;
            return;
        }

        // Global fallback
        $payload['target_scope'] = 'global';
        $payload['program_id']   = 0;
        $payload['course_id']    = 0;
        $payload['class_id']     = 0;
    }

    /**
     * Upload dan simpan asset gambar/PDF template ke file_assets.
     *
     * @return array{asset_id: int, relative_path: string, full_path: string, original_name: string, extension: string}
     */
    private function handleBackgroundUpload($file, int $actorId, int $templateId): array
    {
        $ext     = strtolower($file->getClientExtension());
        $allowed = ['png', 'jpg', 'jpeg', 'pdf'];
        if (! in_array($ext, $allowed, true)) {
            throw new RuntimeException('Format file tidak didukung. Harap unggah PDF, PNG, atau JPG.');
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
            throw new RuntimeException('Gagal mencatat asset template ke database.');
        }

        return [
            'asset_id'      => (int) $this->db->insertID(),
            'relative_path' => $relativePath,
            'full_path'     => $fullPath,
            'original_name' => $file->getClientName(),
            'extension'     => $ext,
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
            'participant_name'   => 'Ir. Rizky Yulitanto, S.T., M.M.',
            'user_name'          => 'Ir. Rizky Yulitanto, S.T., M.M.',
            'user_email'         => 'lutfi@lampungprov.go.id',
            'user_phone'         => '081234567890',
            'identity_label'     => 'NIP',
            'identity_number'    => '20000315 202321 1 002',
            'nip'                => '20000315 202321 1 002',
            'ni_ppk'             => '20000315 202321 1 002',
            'nib'                => '0220101234567',
            'nidn'               => '0012058001',
            'nik'                => '1871010101850001',
            'institution_label'  => 'Instansi',
            'institution_name'   => 'Biro Pengadaan Barang dan Jasa Sekretariat Daerah Provinsi Lampung',
            'instansi'           => 'Biro Pengadaan Barang dan Jasa Sekretariat Daerah Provinsi Lampung',
            'nama_usaha'         => 'PT Mahakarya Konstruksi Lampung',
            'perguruan_tinggi'   => 'Universitas Lampung',
            'unit_name'          => 'Bagian Pengelolaan Pengadaan Barang/Jasa',
            'unit_kerja'         => 'Bagian Pengelolaan Pengadaan Barang/Jasa',
            'position_name'      => 'Pengelola Pengadaan Barang/Jasa Ahli Muda',
            'jabatan'            => 'Pengelola Pengadaan Barang/Jasa Ahli Muda',
            'rank_grade'         => 'Pembina / IV.a',
            'pangkat_golongan'   => 'Pembina / IV.a',
            'tipe_peserta'       => 'Aparatur Sipil Negara (PNS)',
            'certificate_title'  => $isLearning
                ? 'Pengantar Pengadaan Barang/Jasa Pemerintah (Tingkat Dasar)'
                : 'Webinar Nasional Strategi Pengadaan Berkelanjutan & E-Katalog V6',
            'title'              => $isLearning
                ? 'Pengantar Pengadaan Barang/Jasa Pemerintah (Tingkat Dasar)'
                : 'Webinar Nasional Strategi Pengadaan Berkelanjutan & E-Katalog V6',
            'subtitle'           => 'Batch I Tahun Anggaran ' . $year,
            'nama_kelas'         => 'Batch I Tahun Anggaran ' . $year,
            'kode_kelas'         => 'KLS-PBJ-' . $year . '-001',
            'nama_program'       => 'Program Pengembangan Kompetensi Pengadaan Berkelanjutan',
            'tingkat_kursus'     => 'Tingkat Dasar (Level 1)',
            'lokasi_kegiatan'    => $isLearning ? 'Portal LMS BegawiPengadaan' : 'Hotel Novotel Bandar Lampung',
            'metode_kegiatan'    => $isLearning ? 'Daring' : 'Hybrid',
            'activity_type'      => $isLearning ? 'Arena Belajar PBJ' : 'Webinar PBJ',
            'tipe_kegiatan'      => $isLearning ? 'Arena Belajar' : 'Webinar',
            'kode_tipe'          => $isLearning ? 'AB' : 'WB',
            'id_kegiatan'        => $isLearning ? 5 : 1,
            'jam_pelajaran'      => $isLearning ? '24 JP' : '4 JP',
            'jp'                 => $isLearning ? '24 JP' : '4 JP',
            'duration_minutes'   => $isLearning ? 1440 : 240,
            'durasi'             => $isLearning ? '1440 Menit' : '240 Menit',
            'final_score'        => '94.5',
            'predicate'          => 'Sangat Baik',
            'issued_at'          => date('Y-m-d H:i:s'),
            'city'               => 'Bandar Lampung',
            'kota_terbit'        => 'Bandar Lampung',
            'signer_rank'        => 'Pembina Utama Madya (IV/d)',
            'penandatangan_pangkat' => 'Pembina Utama Madya (IV/d)',
            'event_start_at'     => date('Y-m-d 08:00:00', strtotime('-5 days')),
            'event_end_at'       => date('Y-m-d 16:00:00'),
            'verification_url'   => site_url('verifikasi-sertifikat/' . ($isLearning ? "SERT-00035-AB-5-05-{$year}" : "SERT-00021-WB-1-05-{$year}")),
        ];
    }

    /**
     * Peta nilai sampel (tag => nilai) untuk pratinjau interaktif di halaman form.
     *
     * @return array<string, string>
     */
    private function buildSamplePlaceholderMap(array $template): array
    {
        $renderer = new CertificateTemplateRenderer();
        $payload  = $renderer->decodePayload($template['template_payload'] ?? null);
        $mockData = $this->buildMockData($template);
        $rawMap   = $renderer->buildPlaceholderMap($mockData, $template, $payload);

        $cleanMap = [];
        foreach ($rawMap as $tag => $val) {
            $cleanMap[$tag] = html_entity_decode($val, ENT_QUOTES, 'UTF-8');
        }
        return $cleanMap;
    }

    /**
     * Sanitize payload JSON (termasuk konfigurasi pdf_placeholders).
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

        $tteMode = isset($raw['tte_mode']) && in_array($raw['tte_mode'], ['visual_qr', 'cryptographic_pades'], true)
            ? (string) $raw['tte_mode']
            : 'visual_qr';

        $layoutMode = isset($raw['layout_mode']) && in_array($raw['layout_mode'], ['pdf_placeholders', 'auto_overlay', 'custom_html'], true)
            ? (string) $raw['layout_mode']
            : 'pdf_placeholders';

        // Parse daftar pdf_placeholders (dari JSON string atau array)
        $rawPlaceholders = [];
        if (! empty($raw['pdf_placeholders_json']) && is_string($raw['pdf_placeholders_json'])) {
            $decodedPh = json_decode($raw['pdf_placeholders_json'], true);
            if (is_array($decodedPh)) {
                $rawPlaceholders = $decodedPh;
            }
        } elseif (! empty($raw['pdf_placeholders']) && is_array($raw['pdf_placeholders'])) {
            $rawPlaceholders = $raw['pdf_placeholders'];
        }

        $cleanPlaceholders = [];
        foreach ($rawPlaceholders as $idx => $ph) {
            if (! is_array($ph) || empty($ph['tag'])) {
                continue;
            }
            $tag = trim((string) $ph['tag']);
            if (! str_starts_with($tag, '{{') && $tag !== '__QR_BLOCK__') {
                $tag = '{{' . trim($tag, '{} ') . '}}';
            }
            $key  = trim($tag, '{} ');
            $type = (in_array($tag, ['{{qr_code}}', '{{qr_block}}', '__QR_BLOCK__'], true) || ($ph['type'] ?? '') === 'qr')
                ? 'qr'
                : 'text';

            $cleanPlaceholders[] = [
                'id'          => (string) ($ph['id'] ?? ('ph_' . ($idx + 1))),
                'tag'         => $tag,
                'key'         => $key,
                'label'       => mb_substr((string) ($ph['label'] ?? $key), 0, 120),
                'type'        => $type,
                'x_pct'       => round(max(0.0, min(100.0, (float) ($ph['x_pct'] ?? 50.0))), 2),
                'y_pct'       => round(max(0.0, min(100.0, (float) ($ph['y_pct'] ?? 50.0))), 2),
                'orig_x_pct'  => round(max(0.0, min(100.0, (float) ($ph['orig_x_pct'] ?? ($ph['x_pct'] ?? 40.0)))), 2),
                'orig_y_pct'  => round(max(0.0, min(100.0, (float) ($ph['orig_y_pct'] ?? ($ph['y_pct'] ?? 50.0)))), 2),
                'orig_w_pct'  => round(max(1.0, min(100.0, (float) ($ph['orig_w_pct'] ?? 20.0))), 2),
                'orig_h_pct'  => round(max(1.0, min(50.0, (float) ($ph['orig_h_pct'] ?? 4.5))), 2),
                'font_size'   => max(6, min(96, (int) ($ph['font_size'] ?? 16))),
                'font_weight' => (($ph['font_weight'] ?? 'normal') === 'bold') ? 'bold' : 'normal',
                'color'       => preg_match('/^#[0-9a-fA-F]{3,6}$/', (string) ($ph['color'] ?? '')) ? (string) $ph['color'] : '#0f172a',
                'align'       => in_array($ph['align'] ?? 'center', ['left', 'center', 'right'], true) ? (string) $ph['align'] : 'center',
                'mask_bg'     => ! empty($ph['mask_bg']),
                'mask_color'  => preg_match('/^#[0-9a-fA-F]{3,6}$/', (string) ($ph['mask_color'] ?? '')) ? (string) $ph['mask_color'] : '#ffffff',
                'qr_size'     => max(40, min(320, (int) ($ph['qr_size'] ?? 110))),
            ];
        }

        $pageWidthMm  = (float) ($raw['page_width_mm'] ?? 297.0);
        $pageHeightMm = (float) ($raw['page_height_mm'] ?? 210.0);

        $clean = [
            'layout_mode'      => $layoutMode,
            'pdf_placeholders' => $cleanPlaceholders,
            'page_width_mm'    => $pageWidthMm > 50 ? round($pageWidthMm, 2) : 297.0,
            'page_height_mm'   => $pageHeightMm > 50 ? round($pageHeightMm, 2) : 210.0,
            'body_html'        => mb_substr($bodyHtml, 0, 60000),
            'css'              => mb_substr($css, 0, 20000),
            'issuer_name'      => mb_substr($issuer, 0, 255),
            'signer_name'      => mb_substr($signerName, 0, 255),
            'signer_position'  => mb_substr($signerPos, 0, 255),
            'signer_nip'       => mb_substr($signerNip, 0, 64),
            'tte_mode'         => $tteMode,
            'qr_position'      => $qrPos,
            'target_scope'     => $raw['target_scope'] ?? 'global',
            'program_id'       => (int) ($raw['program_id'] ?? 0),
            'course_id'        => (int) ($raw['course_id'] ?? 0),
            'class_id'         => (int) ($raw['class_id'] ?? 0),
            'event_id'         => (int) ($raw['event_id'] ?? 0),
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
            'layout_mode'      => 'pdf_placeholders',
            'pdf_placeholders' => [],
            'page_width_mm'    => 297.0,
            'page_height_mm'   => 210.0,
            'body_html'        => '',
            'css'              => '',
            'issuer_name'      => 'Pemerintah Provinsi Lampung — Biro Pengadaan Barang dan Jasa',
            'signer_name'      => 'Dr. H. Rahmad Hidayat, S.E., M.Si.',
            'signer_position'  => 'Kepala Biro Pengadaan Barang dan Jasa',
            'signer_nip'       => '19750812 199903 1 004',
            'tte_mode'         => 'visual_qr',
            'qr_position'      => 'right',
            'target_scope'     => 'global',
            'program_id'       => 0,
            'course_id'        => 0,
            'class_id'         => 0,
            'event_id'         => 0,
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

        // Fallback: administrator/super_administrator
        $isAdmin = $this->db->table('user_roles ur')
            ->join('roles r', 'r.id = ur.role_id')
            ->where(['ur.user_id' => $actorId, 'ur.status' => 'active'])
            ->groupStart()
            ->where('r.key', 'administrator')
            ->orWhere('r.key', 'super_administrator')
            ->orWhere('r.name', 'Administrator')
            ->orWhere('r.name', 'Super Administrator')
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
