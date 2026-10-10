<?php

namespace App\Controllers\Certificate;

use App\Controllers\BaseController;
use App\Models\Certificate\CertificateTemplateModel;
use App\Services\AuditService;
use CodeIgniter\Database\ConnectionInterface;
use RuntimeException;

/**
 * CertificateTemplateController — dashboard CRUD untuk certificate_templates.
 *
 * Skema v4:
 *   name, source_type ('learning'|'event'),
 *   template_payload (JSON), background_file_id (file_assets.id),
 *   is_active, created_by, updated_by
 *
 * Endpoint (sesuai routes):
 *   GET    /dashboard/sertifikat/template                          index
 *   GET    /dashboard/sertifikat/template/tambah                   create
 *   POST   /dashboard/sertifikat/template/simpan                   store
 *   GET    /dashboard/sertifikat/template/{id}/ubah                edit
 *   POST   /dashboard/sertifikat/template/{id}/ubah                update
 *   POST   /dashboard/sertifikat/template/{id}/aktifkan           activate
 *   POST   /dashboard/sertifikat/template/{id}/nonaktifkan        deactivate
 *   GET    /dashboard/sertifikat/template/{id}                     detail
 *
 * Authorization: sertifikat.view (index/detail) + sertifikat.template.manage
 * (create/update/activate). Permission check mengikuti policy yang umum
 * dipakai di modul lain.
 */
class CertificateTemplateController extends BaseController
{
    private ConnectionInterface $db;
    private CertificateTemplateModel $templateModel;
    private AuditService $auditService;

    public function __construct()
    {
        $this->db = db_connect();
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
            'title'         => 'Template Sertifikat | Dashboard BegawiPBJ',
            'pageTitle'     => 'Template Sertifikat',
            'activeMenu'    => 'certificates',
            'activeSubmenu' => 'templates',
            'items'         => $items,
        ]);
    }

    public function create()
    {
        return view('dashboard/certificates/templates/form', [
            'title'         => 'Tambah Template Sertifikat | Dashboard BegawiPBJ',
            'pageTitle'     => 'Tambah Template',
            'formMode'      => 'create',
            'activeMenu'    => 'certificates',
            'activeSubmenu' => 'templates',
            'template'      => [
                'id'         => 0,
                'name'       => '',
                'source_type'=> 'learning',
                'template_payload' => $this->defaultPayload(),
                'is_active'  => 1,
            ],
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
            $now = date('Y-m-d H:i:s');

            $data = [
                'name'             => trim((string) $this->request->getPost('name')),
                'source_type'      => (string) $this->request->getPost('source_type'),
                'template_payload' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'is_active'        => (int) ($this->request->getPost('is_active') ?? 1),
                'created_by'       => $actorId,
                'updated_by'       => $actorId,
                'created_at'       => $now,
                'updated_at'       => $now,
            ];

            $ok = $this->db->table('certificate_templates')->insert($data, true);
            if (! $ok) {
                throw new RuntimeException('Gagal menyimpan template.');
            }
            $newId = (int) $this->db->insertID();

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

        return view('dashboard/certificates/templates/detail', [
            'title'         => 'Detail Template | Dashboard BegawiPBJ',
            'pageTitle'     => 'Detail Template',
            'activeMenu'    => 'certificates',
            'activeSubmenu' => 'templates',
            'templateId'    => (int) $id,
            'template'      => $template,
        ]);
    }

    public function edit(string $id)
    {
        $template = $this->templateModel->find((int) $id);
        if (! $template) {
            return $this->notFoundResponse('Template tidak ditemukan.');
        }
        // Decode payload agar bisa diedit sebagai array
        $decoded = json_decode((string) ($template['template_payload'] ?? ''), true);
        if (! is_array($decoded)) {
            $decoded = $this->defaultPayload();
        }
        $template['template_payload'] = $decoded;

        return view('dashboard/certificates/templates/form', [
            'title'         => 'Ubah Template | Dashboard BegawiPBJ',
            'pageTitle'     => 'Ubah Template',
            'formMode'      => 'edit',
            'activeMenu'    => 'certificates',
            'activeSubmenu' => 'templates',
            'template'      => $template,
        ]);
    }

    public function update(string $id)
    {
        try {
            $this->assertManageAccess();
            $actorId = (int) session()->get('user_id');

            $templateId = (int) $id;
            $before = $this->templateModel->find($templateId);
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

            $payload = $this->sanitizePayload((array) $this->request->getPost('payload'));
            $now = date('Y-m-d H:i:s');

            $data = [
                'name'             => trim((string) $this->request->getPost('name')),
                'source_type'      => (string) $this->request->getPost('source_type'),
                'template_payload' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'is_active'        => (int) ($this->request->getPost('is_active') ?? 0),
                'updated_by'       => $actorId,
                'updated_at'       => $now,
            ];

            $this->db->table('certificate_templates')->where('id', $templateId)->update($data);

            $this->auditService->log($actorId, 'certificate_template.updated', 'certificate', 'certificate_template', $templateId, [
                'before' => ['name' => $before['name'], 'is_active' => $before['is_active'], 'source_type' => $before['source_type']],
                'after'  => ['name' => $data['name'], 'is_active' => $data['is_active'], 'source_type' => $data['source_type']],
            ]);

            return redirect()->to(site_url('dashboard/sertifikat/template/' . $templateId))
                ->with('success', 'Template berhasil diperbarui.');
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
            $actorId = (int) session()->get('user_id');
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

    // ===============================================================
    // Helpers
    // ===============================================================

    /**
     * Sanitize payload JSON — hanya terima whitelisted keys.
     */
    private function sanitizePayload(array $raw): array
    {
        $bodyHtml = isset($raw['body_html']) && is_string($raw['body_html']) ? $raw['body_html'] : '';
        $css      = isset($raw['css'])      && is_string($raw['css'])      ? $raw['css']      : '';
        $issuer   = isset($raw['issuer_name']) && is_string($raw['issuer_name']) && trim($raw['issuer_name']) !== ''
            ? trim($raw['issuer_name'])
            : 'BegawiPBJ — Pemerintah Provinsi Lampung';
        $qrPos    = isset($raw['qr_position']) && in_array($raw['qr_position'], ['right', 'bottom'], true)
            ? $raw['qr_position']
            : 'right';

        // Lebas maksimal CSS+HTML agar tidak ada upload size abuse di fase dev.
        return [
            'body_html'   => mb_substr($bodyHtml, 0, 60000),
            'css'         => mb_substr($css, 0, 20000),
            'issuer_name' => mb_substr($issuer, 0, 200),
            'qr_position' => $qrPos,
        ];
    }

    private function defaultPayload(): array
    {
        return [
            'body_html'   => '',
            'css'         => '',
            'issuer_name' => 'BegawiPBJ — Pemerintah Provinsi Lampung',
            'qr_position' => 'right',
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
        // Permission sederhana: admin/super_admin atau permission 'certificate.template.manage'
        $actor = $this->db->table('users')->where('id', $actorId)->get()->getRowArray();
        if (! $actor || ($actor['account_status'] ?? '') !== 'active') {
            throw new RuntimeException('Akun tidak aktif.');
        }

        // permission explicit (preferred)
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

        // fallback: admin/super_admin implicit (r.key sesuai schema v4)
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
