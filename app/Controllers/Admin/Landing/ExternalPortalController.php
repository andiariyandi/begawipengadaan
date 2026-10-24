<?php

namespace App\Controllers\Admin\Landing;

use App\Controllers\BaseController;
use App\Services\AuditService;
use App\Services\LandingImageUploadService;
use CodeIgniter\Database\ConnectionInterface;

class ExternalPortalController extends BaseController
{
    private AuditService $auditService;
    private ConnectionInterface $db;

    public function __construct()
    {
        $this->auditService = new AuditService();
        $this->db           = \Config\Database::connect();
    }

    public function index()
    {
        $portals = $this->db->table('external_portals')
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();

        $totalCount    = count($portals);
        $activeCount   = count(array_filter($portals, fn($p) => (int) ($p['is_active'] ?? 0) === 1));
        $inactiveCount = $totalCount - $activeCount;
        $nasionalCount = count(array_filter($portals, fn($p) => ($p['category'] ?? '') === 'nasional'));
        $daerahCount   = count(array_filter($portals, fn($p) => ($p['category'] ?? '') === 'daerah'));

        $stats = [
            'total'          => $totalCount,
            'active_count'   => $activeCount,
            'inactive_count' => $inactiveCount,
            'nasional_count' => $nasionalCount,
            'daerah_count'   => $daerahCount,
        ];

        return view('dashboard/landing/portal/index', [
            'title'         => 'Kelola Portal Eksternal | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Portal Ekosistem PBJ',
            'activeMenu'    => 'landing',
            'activeSubmenu' => 'portal-eksternal',
            'portals'       => $portals,
            'stats'         => $stats,
        ]);
    }

    public function create()
    {
        $maxOrderRow = $this->db->table('external_portals')
            ->selectMax('sort_order', 'max_order')
            ->get()->getRowArray();
        $nextOrder = ((int) ($maxOrderRow['max_order'] ?? 0)) + 1;

        return view('dashboard/landing/portal/form', [
            'title'         => 'Tambah Portal Eksternal | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Tambah Portal Mitra',
            'activeMenu'    => 'landing',
            'activeSubmenu' => 'portal-eksternal',
            'portal'        => null,
            'nextOrder'     => $nextOrder,
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

        $logoUrl      = trim((string) $this->request->getPost('logo_url'));
        $logoFile     = $this->request->getFile('logo_file');
        $uploadedLogo = LandingImageUploadService::upload($logoFile, 'portals', $logoUrl ?: null, 'external_portal');
        if ($uploadedLogo) {
            $logoUrl = $uploadedLogo;
        }
        if (empty($logoUrl)) {
            $logoUrl = 'solar:server-square-bold';
        }

        $rawOrder  = $this->request->getPost('sort_order');
        $sortOrder = ($rawOrder !== null && $rawOrder !== '') ? (int) $rawOrder : 1;

        $data = [
            'name'        => trim((string) $this->request->getPost('name')),
            'acronym'     => trim((string) $this->request->getPost('acronym')),
            'category'    => trim((string) $this->request->getPost('category') ?: 'nasional'),
            'description' => trim((string) $this->request->getPost('description')),
            'url'         => trim((string) $this->request->getPost('url')),
            'logo_url'    => $logoUrl,
            'sort_order'  => $sortOrder,
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        $this->db->table('external_portals')->insert($data);
        $insertId = $this->db->insertID();

        $rawActorId = (int) (session()->get('user_id') ?: 0);
        $actorId    = $rawActorId > 0 ? $rawActorId : null;
        $this->auditService->log($actorId, 'landing.portal.created', 'landing', 'external_portals', (int) $insertId, $data);

        return redirect()->to('dashboard/landing/portal-eksternal')
            ->with('success', 'Portal eksternal berhasil ditambahkan.');
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
            'nextOrder'     => (int) ($portal['sort_order'] ?? 1),
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

        $logoUrl      = trim((string) $this->request->getPost('logo_url'));
        $logoFile     = $this->request->getFile('logo_file');
        $currentLogo  = $portal['logo_url'] ?? 'solar:server-square-bold';
        $uploadedLogo = LandingImageUploadService::upload($logoFile, 'portals', $logoUrl ?: $currentLogo, 'external_portal');
        if ($uploadedLogo) {
            $logoUrl = $uploadedLogo;
        }
        if (empty($logoUrl)) {
            $logoUrl = $currentLogo;
        }

        $rawOrder  = $this->request->getPost('sort_order');
        $sortOrder = ($rawOrder !== null && $rawOrder !== '') ? (int) $rawOrder : (int) ($portal['sort_order'] ?? 1);

        $data = [
            'name'        => trim((string) $this->request->getPost('name')),
            'acronym'     => trim((string) $this->request->getPost('acronym')),
            'category'    => trim((string) $this->request->getPost('category') ?: 'nasional'),
            'description' => trim((string) $this->request->getPost('description')),
            'url'         => trim((string) $this->request->getPost('url')),
            'logo_url'    => $logoUrl,
            'sort_order'  => $sortOrder,
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        $this->db->table('external_portals')->where('id', $id)->update($data);

        $rawActorId = (int) (session()->get('user_id') ?: 0);
        $actorId    = $rawActorId > 0 ? $rawActorId : null;
        $this->auditService->log($actorId, 'landing.portal.updated', 'landing', 'external_portals', (int) $id, ['before' => $portal, 'after' => $data]);

        return redirect()->to('dashboard/landing/portal-eksternal')
            ->with('success', 'Portal eksternal berhasil diperbarui.');
    }

    public function duplicate($id)
    {
        $portal = $this->db->table('external_portals')->where('id', $id)->get()->getRowArray();
        if (! $portal) {
            return redirect()->to('dashboard/landing/portal-eksternal')->with('error', 'Portal tidak ditemukan.');
        }

        $maxOrderRow = $this->db->table('external_portals')->selectMax('sort_order', 'max_order')->get()->getRowArray();
        $nextOrder = ((int) ($maxOrderRow['max_order'] ?? $portal['sort_order'])) + 1;

        $newData = [
            'name'        => $portal['name'] . ' (Salinan)',
            'acronym'     => $portal['acronym'] ? ($portal['acronym'] . '-COPY') : '',
            'url'         => $portal['url'],
            'logo_url'    => $portal['logo_url'],
            'description' => $portal['description'],
            'category'    => $portal['category'],
            'sort_order'  => $nextOrder,
            'is_active'   => 0, // Duplicate starts as draft
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        $this->db->table('external_portals')->insert($newData);
        $newId = $this->db->insertID();

        $rawActorId = (int) (session()->get('user_id') ?: 0);
        $actorId    = $rawActorId > 0 ? $rawActorId : null;
        $this->auditService->log($actorId, 'landing.portal.duplicated', 'external_portals', 'id', (int) $newId, $newData);

        return redirect()->to('dashboard/landing/portal-eksternal')
            ->with('success', 'Portal berhasil diduplikat sebagai draf.');
    }

    public function updateOrder()
    {
        $id       = (int) ($this->request->getPost('id') ?: $this->request->getVar('id') ?: ($_POST['id'] ?? 0));
        $rawOrder = $this->request->getPost('order') ?? $this->request->getVar('order') ?? ($_POST['order'] ?? null);
        $order    = ($rawOrder !== null && $rawOrder !== '') ? (int) $rawOrder : 1;

        if ($id <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID portal tidak valid.'])->setStatusCode(400);
        }

        $portal = $this->db->table('external_portals')->where('id', $id)->get()->getRowArray();
        if (! $portal) {
            return $this->response->setJSON(['success' => false, 'message' => 'Portal tidak ditemukan.'])->setStatusCode(404);
        }

        $this->db->table('external_portals')->where('id', $id)->update([
            'sort_order' => $order,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $rawActorId = (int) (session()->get('user_id') ?: 0);
        $actorId    = $rawActorId > 0 ? $rawActorId : null;
        $this->auditService->log($actorId, 'landing.portal.reordered', 'external_portals', 'sort_order', $id, ['order' => $order]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Urutan portal berhasil diperbarui.',
                'order'   => $order,
            ]);
        }

        return redirect()->to('dashboard/landing/portal-eksternal')
            ->with('success', 'Urutan portal berhasil diperbarui.');
    }

    public function delete($id)
    {
        if ($res = $this->validateSafeMutation()) {
            return $res;
        }

        $portal = $this->db->table('external_portals')->where('id', $id)->get()->getRowArray();
        if ($portal) {
            $this->db->table('external_portals')->where('id', $id)->delete();

            $rawActorId = (int) (session()->get('user_id') ?: 0);
            $actorId    = $rawActorId > 0 ? $rawActorId : null;
            $this->auditService->log($actorId, 'landing.portal.deleted', 'landing', 'external_portals', (int) $id, ['deleted' => $portal]);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Portal berhasil dihapus.',
                ]);
            }

            return redirect()->to('dashboard/landing/portal-eksternal')
                ->with('success', 'Portal berhasil dihapus.');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Portal tidak ditemukan.'])->setStatusCode(404);
        }

        return redirect()->to('dashboard/landing/portal-eksternal')->with('error', 'Portal tidak ditemukan.');
    }

    public function toggle($id)
    {
        if ($res = $this->validateSafeMutation()) {
            return $res;
        }

        $portal = $this->db->table('external_portals')->where('id', $id)->get()->getRowArray();
        if ($portal) {
            $newStatus = $portal['is_active'] ? 0 : 1;
            $this->db->table('external_portals')->where('id', $id)->update([
                'is_active'  => $newStatus,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $rawActorId = (int) (session()->get('user_id') ?: 0);
            $actorId    = $rawActorId > 0 ? $rawActorId : null;
            $this->auditService->log($actorId, 'landing.portal.toggled', 'external_portals', 'is_active', (int) $id, ['new_status' => $newStatus]);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success'   => true,
                    'message'   => 'Status portal berhasil diubah.',
                    'is_active' => $newStatus,
                    'id'        => (int) $id,
                ]);
            }

            return redirect()->to('dashboard/landing/portal-eksternal')
                ->with('success', 'Status portal berhasil diubah.');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Portal tidak ditemukan.'])->setStatusCode(404);
        }

        return redirect()->to('dashboard/landing/portal-eksternal')->with('error', 'Portal tidak ditemukan.');
    }

    /**
     * Pre-populate standard canonical PBJ portals (Nasional & Daerah Lampung)
     */
    public function seedRecommendations()
    {
        $presets = [
            [
                'name'        => 'LPSE Provinsi Lampung',
                'acronym'     => 'SPSE',
                'category'    => 'daerah',
                'description' => 'Sistem Pengadaan Secara Elektronik (SPSE) tender, seleksi, dan non-tender Pemprov Lampung.',
                'url'         => 'https://lpse.lampungprov.go.id',
                'logo_url'    => 'solar:server-square-bold',
                'sort_order'  => 1,
                'is_active'   => 1,
            ],
            [
                'name'        => 'SiRUP LKPP',
                'acronym'     => 'SiRUP',
                'category'    => 'nasional',
                'description' => 'Sistem Informasi Rencana Umum Pengadaan seluruh paket belanja anggaran Pemprov Lampung.',
                'url'         => 'https://sirup.lkpp.go.id',
                'logo_url'    => 'solar:document-add-bold',
                'sort_order'  => 2,
                'is_active'   => 1,
            ],
            [
                'name'        => 'Katalog Elektronik (E-Katalog LKPP)',
                'acronym'     => 'E-Katalog',
                'category'    => 'nasional',
                'description' => 'Aplikasi belanja e-purchasing komoditas nasional, sektoral, dan etalase lokal Lampung.',
                'url'         => 'https://e-katalog.lkpp.go.id',
                'logo_url'    => 'solar:cart-large-4-bold',
                'sort_order'  => 3,
                'is_active'   => 1,
            ],
            [
                'name'        => 'Bela Pengadaan & Toko Daring',
                'acronym'     => 'Bela',
                'category'    => 'nasional',
                'description' => 'Pasar daring pengadaan langsung kebutuhan operasional pemerintah bersama mitra PPMSE terverifikasi.',
                'url'         => 'https://belapengadaan.lkpp.go.id',
                'logo_url'    => 'solar:shop-2-bold',
                'sort_order'  => 4,
                'is_active'   => 1,
            ],
            [
                'name'        => 'Portal Resmi LKPP RI',
                'acronym'     => 'LKPP',
                'category'    => 'nasional',
                'description' => 'Lembaga Kebijakan Pengadaan Barang/Jasa Pemerintah selaku instansi pembina PBJ nasional.',
                'url'         => 'https://www.lkpp.go.id',
                'logo_url'    => 'solar:city-bold',
                'sort_order'  => 5,
                'is_active'   => 1,
            ],
            [
                'name'        => 'JDIH Pemerintah Provinsi Lampung',
                'acronym'     => 'JDIH',
                'category'    => 'daerah',
                'description' => 'Jaringan Dokumentasi dan Informasi Hukum Produk Hukum Daerah Pemprov Lampung.',
                'url'         => 'https://jdih.lampungprov.go.id',
                'logo_url'    => 'solar:book-bookmark-bold',
                'sort_order'  => 6,
                'is_active'   => 1,
            ],
        ];

        $insertedCount = 0;
        foreach ($presets as $p) {
            $exists = $this->db->table('external_portals')
                ->where('name', $p['name'])
                ->countAllResults();

            if ($exists === 0) {
                $p['created_at'] = date('Y-m-d H:i:s');
                $p['updated_at'] = date('Y-m-d H:i:s');
                $this->db->table('external_portals')->insert($p);
                $insertedCount++;
            }
        }

        $rawActorId = (int) (session()->get('user_id') ?: 0);
        $actorId    = $rawActorId > 0 ? $rawActorId : null;
        $this->auditService->log($actorId, 'landing.portal.seeded_presets', 'external_portals', 'all', null, ['count' => $insertedCount]);

        return redirect()->to('dashboard/landing/portal-eksternal')
            ->with('success', "Berhasil menambahkan {$insertedCount} portal mitra ekosistem rekomendasi.");
    }
}
