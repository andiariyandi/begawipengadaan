<?php

namespace App\Controllers\Admin\Landing;

use App\Controllers\BaseController;
use App\Services\AuditService;
use App\Services\LandingImageUploadService;

class ExternalPortalController extends BaseController
{
    private AuditService $auditService;
    private $db;

    public function __construct()
    {
        $this->auditService = new AuditService();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $portals = $this->db->table('external_portals')
            ->orderBy('sort_order', 'ASC')
            ->get()->getResultArray();

        return view('dashboard/landing/portal/index', [
            'title'         => 'Kelola Portal Eksternal | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Portal Ekosistem PBJ',
            'activeMenu'    => 'landing',
            'activeSubmenu' => 'portal-eksternal',
            'portals'       => $portals,
        ]);
    }

    public function create()
    {
        return view('dashboard/landing/portal/form', [
            'title'         => 'Tambah Portal Eksternal | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Tambah Portal Mitra',
            'activeMenu'    => 'landing',
            'activeSubmenu' => 'portal-eksternal',
            'portal'        => null,
        ]);
    }

    public function store()
    {
        $rules = [
            'name' => 'required|max_length[150]',
            'url'  => 'required|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $logoUrl = trim((string) $this->request->getPost('logo_url'));
        $logoFile = $this->request->getFile('logo_file');
        $uploadedLogo = LandingImageUploadService::upload($logoFile, 'portals', $logoUrl ?: null, 'external_portal');
        if ($uploadedLogo) {
            $logoUrl = $uploadedLogo;
        }
        if (empty($logoUrl)) {
            $logoUrl = 'solar:server-square-bold';
        }

        $data = [
            'name'        => trim((string) $this->request->getPost('name')),
            'acronym'     => trim((string) $this->request->getPost('acronym')),
            'category'    => trim((string) $this->request->getPost('category') ?: 'nasional'),
            'description' => trim((string) $this->request->getPost('description')),
            'url'         => trim((string) $this->request->getPost('url')),
            'logo_url'    => $logoUrl,
            'sort_order'  => (int) ($this->request->getPost('sort_order') ?: 1),
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        $this->db->table('external_portals')->insert($data);
        $insertId = $this->db->insertID();

        $actorId = (int) (session()->get('user_id') ?: 1);
        $this->auditService->log($actorId, 'landing.portal.created', 'landing', 'external_portals', (int) $insertId, $data);

        return redirect()->to('dashboard/landing/portal-eksternal')->with('success', 'Portal eksternal berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $portal = $this->db->table('external_portals')->where('id', $id)->get()->getRowArray();
        if (! $portal) {
            return redirect()->to('dashboard/landing/portal-eksternal')->with('error', 'Portal tidak ditemukan.');
        }

        return view('dashboard/landing/portal/form', [
            'title'         => 'Ubah Portal Eksternal | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Ubah Portal Mitra',
            'activeMenu'    => 'landing',
            'activeSubmenu' => 'portal-eksternal',
            'portal'        => $portal,
        ]);
    }

    public function update($id)
    {
        $portal = $this->db->table('external_portals')->where('id', $id)->get()->getRowArray();
        if (! $portal) {
            return redirect()->to('dashboard/landing/portal-eksternal')->with('error', 'Portal tidak ditemukan.');
        }

        $rules = [
            'name' => 'required|max_length[150]',
            'url'  => 'required|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $logoUrl = trim((string) $this->request->getPost('logo_url'));
        $logoFile = $this->request->getFile('logo_file');
        $currentLogo = $portal['logo_url'] ?? 'solar:server-square-bold';
        $uploadedLogo = LandingImageUploadService::upload($logoFile, 'portals', $logoUrl ?: $currentLogo, 'external_portal');
        if ($uploadedLogo) {
            $logoUrl = $uploadedLogo;
        }
        if (empty($logoUrl)) {
            $logoUrl = $currentLogo;
        }

        $data = [
            'name'        => trim((string) $this->request->getPost('name')),
            'acronym'     => trim((string) $this->request->getPost('acronym')),
            'category'    => trim((string) $this->request->getPost('category') ?: 'nasional'),
            'description' => trim((string) $this->request->getPost('description')),
            'url'         => trim((string) $this->request->getPost('url')),
            'logo_url'    => $logoUrl,
            'sort_order'  => (int) ($this->request->getPost('sort_order') ?: 1),
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        $this->db->table('external_portals')->where('id', $id)->update($data);

        $actorId = (int) (session()->get('user_id') ?: 1);
        $this->auditService->log($actorId, 'landing.portal.updated', 'landing', 'external_portals', (int) $id, ['before' => $portal, 'after' => $data]);

        return redirect()->to('dashboard/landing/portal-eksternal')->with('success', 'Portal eksternal berhasil diperbarui.');
    }

    public function delete($id)
    {
        $portal = $this->db->table('external_portals')->where('id', $id)->get()->getRowArray();
        if ($portal) {
            $this->db->table('external_portals')->where('id', $id)->delete();
            $actorId = (int) (session()->get('user_id') ?: 1);
            $this->auditService->log($actorId, 'landing.portal.deleted', 'landing', 'external_portals', (int) $id, ['deleted' => $portal]);
        }

        return redirect()->to('dashboard/landing/portal-eksternal')->with('success', 'Portal berhasil dihapus.');
    }

    public function toggle($id)
    {
        $portal = $this->db->table('external_portals')->where('id', $id)->get()->getRowArray();
        if ($portal) {
            $newStatus = $portal['is_active'] ? 0 : 1;
            $this->db->table('external_portals')->where('id', $id)->update([
                'is_active'  => $newStatus,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            return redirect()->to('dashboard/landing/portal-eksternal')->with('success', 'Status portal berhasil diubah.');
        }

        return redirect()->to('dashboard/landing/portal-eksternal')->with('error', 'Portal tidak ditemukan.');
    }
}
