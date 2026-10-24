<?php

namespace App\Controllers\Admin\Landing;

use App\Controllers\BaseController;
use App\Services\AuditService;
use App\Services\LandingImageUploadService;
use CodeIgniter\Database\ConnectionInterface;

class TeamMemberController extends BaseController
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
        $members = $this->db->table('team_members')
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();

        $totalCount       = count($members);
        $activeCount      = count(array_filter($members, fn($m) => (int) ($m['is_active'] ?? 0) === 1));
        $inactiveCount    = $totalCount - $activeCount;
        $pembinaCount     = count(array_filter($members, fn($m) => ($m['category'] ?? '') === 'pembina'));
        $pengembangCount  = count(array_filter($members, fn($m) => ($m['category'] ?? '') === 'pengembang'));
        $narasumberCount  = count(array_filter($members, fn($m) => ($m['category'] ?? '') === 'narasumber'));

        $stats = [
            'total'            => $totalCount,
            'active_count'     => $activeCount,
            'inactive_count'   => $inactiveCount,
            'pembina_count'    => $pembinaCount,
            'pengembang_count' => $pengembangCount,
            'narasumber_count' => $narasumberCount,
        ];

        return view('dashboard/landing/team/index', [
            'title'         => 'Kelola Tim Inovator | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Tim Inovator & Pengembang',
            'activeMenu'    => 'landing',
            'activeSubmenu' => 'tim',
            'members'       => $members,
            'stats'         => $stats,
        ]);
    }

    public function create()
    {
        $maxOrderRow = $this->db->table('team_members')
            ->selectMax('sort_order', 'max_order')
            ->get()->getRowArray();
        $nextOrder = ((int) ($maxOrderRow['max_order'] ?? 0)) + 1;

        return view('dashboard/landing/team/form', [
            'title'         => 'Tambah Anggota Tim | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Tambah Anggota Tim',
            'activeMenu'    => 'landing',
            'activeSubmenu' => 'tim',
            'member'        => null,
            'nextOrder'     => $nextOrder,
        ]);
    }

    public function store()
    {
        $rules = [
            'name'       => 'required|max_length[150]',
            'role_title' => 'required|max_length[150]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $badge = trim((string) $this->request->getPost('badge'));
        $socialLinks = json_encode(['badge' => $badge ?: 'Kolaborator']);

        $photoUrl = trim((string) $this->request->getPost('photo_url'));
        $photoFile = $this->request->getFile('photo_file');
        $uploadedPhoto = LandingImageUploadService::upload($photoFile, 'team', $photoUrl ?: null, 'team_member');
        if ($uploadedPhoto) {
            $photoUrl = $uploadedPhoto;
        }
        if (empty($photoUrl)) {
            $photoUrl = 'assets/main/images/team/team-1-1.jpg';
        }

        $rawOrder  = $this->request->getPost('sort_order');
        $sortOrder = ($rawOrder !== null && $rawOrder !== '') ? (int) $rawOrder : 1;

        $data = [
            'name'         => trim((string) $this->request->getPost('name')),
            'role_title'   => trim((string) $this->request->getPost('role_title')),
            'nip'          => trim((string) $this->request->getPost('nip')),
            'category'     => trim((string) $this->request->getPost('category') ?: 'pembina'),
            'photo_url'    => $photoUrl,
            'bio'          => trim((string) $this->request->getPost('bio')),
            'social_links' => $socialLinks,
            'sort_order'   => $sortOrder,
            'is_active'    => $this->request->getPost('is_active') ? 1 : 0,
            'created_at'   => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s'),
        ];

        $this->db->table('team_members')->insert($data);
        $insertId = $this->db->insertID();

        $rawActorId = (int) (session()->get('user_id') ?: 0);
        $actorId    = $rawActorId > 0 ? $rawActorId : null;
        $this->auditService->log($actorId, 'landing.team.created', 'landing', 'team_members', (int) $insertId, $data);

        return redirect()->to('dashboard/landing/tim')->with('success', 'Anggota tim berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $member = $this->db->table('team_members')->where('id', $id)->get()->getRowArray();
        if (! $member) {
            return redirect()->to('dashboard/landing/tim')->with('error', 'Anggota tim tidak ditemukan.');
        }

        return view('dashboard/landing/team/form', [
            'title'         => 'Ubah Anggota Tim | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Ubah Anggota Tim',
            'activeMenu'    => 'landing',
            'activeSubmenu' => 'tim',
            'member'        => $member,
            'nextOrder'     => (int) ($member['sort_order'] ?? 1),
        ]);
    }

    public function update($id)
    {
        $member = $this->db->table('team_members')->where('id', $id)->get()->getRowArray();
        if (! $member) {
            return redirect()->to('dashboard/landing/tim')->with('error', 'Anggota tim tidak ditemukan.');
        }

        $rules = [
            'name'       => 'required|max_length[150]',
            'role_title' => 'required|max_length[150]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $badge = trim((string) $this->request->getPost('badge'));
        $socialLinks = json_encode(['badge' => $badge ?: 'Kolaborator']);

        $photoUrl = trim((string) $this->request->getPost('photo_url'));
        $photoFile = $this->request->getFile('photo_file');
        $currentPhoto = $member['photo_url'] ?? 'assets/main/images/team/team-1-1.jpg';
        $uploadedPhoto = LandingImageUploadService::upload($photoFile, 'team', $photoUrl ?: $currentPhoto, 'team_member');
        if ($uploadedPhoto) {
            $photoUrl = $uploadedPhoto;
        }
        if (empty($photoUrl)) {
            $photoUrl = $currentPhoto;
        }

        $rawOrder  = $this->request->getPost('sort_order');
        $sortOrder = ($rawOrder !== null && $rawOrder !== '') ? (int) $rawOrder : (int) ($member['sort_order'] ?? 1);

        $data = [
            'name'         => trim((string) $this->request->getPost('name')),
            'role_title'   => trim((string) $this->request->getPost('role_title')),
            'nip'          => trim((string) $this->request->getPost('nip')),
            'category'     => trim((string) $this->request->getPost('category') ?: 'pembina'),
            'photo_url'    => $photoUrl,
            'bio'          => trim((string) $this->request->getPost('bio')),
            'social_links' => $socialLinks,
            'sort_order'   => $sortOrder,
            'is_active'    => $this->request->getPost('is_active') ? 1 : 0,
            'updated_at'   => date('Y-m-d H:i:s'),
        ];

        $this->db->table('team_members')->where('id', $id)->update($data);

        $rawActorId = (int) (session()->get('user_id') ?: 0);
        $actorId    = $rawActorId > 0 ? $rawActorId : null;
        $this->auditService->log($actorId, 'landing.team.updated', 'landing', 'team_members', (int) $id, ['before' => $member, 'after' => $data]);

        return redirect()->to('dashboard/landing/tim')->with('success', 'Anggota tim berhasil diperbarui.');
    }

    public function duplicate($id)
    {
        $member = $this->db->table('team_members')->where('id', $id)->get()->getRowArray();
        if (! $member) {
            return redirect()->to('dashboard/landing/tim')->with('error', 'Anggota tim tidak ditemukan.');
        }

        $maxOrderRow = $this->db->table('team_members')->selectMax('sort_order', 'max_order')->get()->getRowArray();
        $nextOrder = ((int) ($maxOrderRow['max_order'] ?? $member['sort_order'])) + 1;

        $newData = [
            'name'         => $member['name'] . ' (Salinan)',
            'role_title'   => $member['role_title'],
            'nip'          => $member['nip'],
            'category'     => $member['category'],
            'photo_url'    => $member['photo_url'],
            'bio'          => $member['bio'],
            'social_links' => $member['social_links'],
            'sort_order'   => $nextOrder,
            'is_active'    => 0, // Duplicate starts as draft
            'created_at'   => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s'),
        ];

        $this->db->table('team_members')->insert($newData);
        $newId = $this->db->insertID();

        $rawActorId = (int) (session()->get('user_id') ?: 0);
        $actorId    = $rawActorId > 0 ? $rawActorId : null;
        $this->auditService->log($actorId, 'landing.team.duplicated', 'team_members', 'id', (int) $newId, $newData);

        return redirect()->to('dashboard/landing/tim')->with('success', 'Profil tokoh berhasil diduplikat sebagai draf.');
    }

    public function updateOrder()
    {
        $id       = (int) ($this->request->getPost('id') ?: $this->request->getVar('id') ?: ($_POST['id'] ?? 0));
        $rawOrder = $this->request->getPost('order') ?? $this->request->getVar('order') ?? ($_POST['order'] ?? null);
        $order    = ($rawOrder !== null && $rawOrder !== '') ? (int) $rawOrder : 1;

        if ($id <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID anggota tim tidak valid.'])->setStatusCode(400);
        }

        $member = $this->db->table('team_members')->where('id', $id)->get()->getRowArray();
        if (! $member) {
            return $this->response->setJSON(['success' => false, 'message' => 'Anggota tim tidak ditemukan.'])->setStatusCode(404);
        }

        $this->db->table('team_members')->where('id', $id)->update([
            'sort_order' => $order,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $rawActorId = (int) (session()->get('user_id') ?: 0);
        $actorId    = $rawActorId > 0 ? $rawActorId : null;
        $this->auditService->log($actorId, 'landing.team.reordered', 'team_members', 'sort_order', $id, ['order' => $order]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Urutan anggota tim berhasil diperbarui.',
                'order'   => $order,
            ]);
        }

        return redirect()->to('dashboard/landing/tim')->with('success', 'Urutan anggota tim berhasil diperbarui.');
    }

    public function delete($id)
    {
        if ($res = $this->validateSafeMutation()) {
            return $res;
        }

        $member = $this->db->table('team_members')->where('id', $id)->get()->getRowArray();
        if ($member) {
            $this->db->table('team_members')->where('id', $id)->delete();

            $rawActorId = (int) (session()->get('user_id') ?: 0);
            $actorId    = $rawActorId > 0 ? $rawActorId : null;
            $this->auditService->log($actorId, 'landing.team.deleted', 'landing', 'team_members', (int) $id, ['deleted' => $member]);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Anggota tim berhasil dihapus.',
                ]);
            }

            return redirect()->to('dashboard/landing/tim')->with('success', 'Anggota tim berhasil dihapus.');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Anggota tim tidak ditemukan.'])->setStatusCode(404);
        }

        return redirect()->to('dashboard/landing/tim')->with('error', 'Anggota tim tidak ditemukan.');
    }

    public function toggle($id)
    {
        if ($res = $this->validateSafeMutation()) {
            return $res;
        }

        $member = $this->db->table('team_members')->where('id', $id)->get()->getRowArray();
        if ($member) {
            $newStatus = $member['is_active'] ? 0 : 1;
            $this->db->table('team_members')->where('id', $id)->update([
                'is_active'  => $newStatus,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $rawActorId = (int) (session()->get('user_id') ?: 0);
            $actorId    = $rawActorId > 0 ? $rawActorId : null;
            $this->auditService->log($actorId, 'landing.team.toggled', 'team_members', 'is_active', (int) $id, ['new_status' => $newStatus]);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success'   => true,
                    'message'   => 'Status anggota tim berhasil diubah.',
                    'is_active' => $newStatus,
                    'id'        => (int) $id,
                ]);
            }

            return redirect()->to('dashboard/landing/tim')->with('success', 'Status anggota tim berhasil diubah.');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Anggota tim tidak ditemukan.'])->setStatusCode(404);
        }

        return redirect()->to('dashboard/landing/tim')->with('error', 'Anggota tim tidak ditemukan.');
    }

    /**
     * Pre-populate standard canonical BegawiPengadaan leadership & developer team
     */
    public function seedRecommendations()
    {
        $presets = [
            [
                'name'         => 'Dr. Ir. Fahrizal Darminto, M.A.',
                'role_title'   => 'Sekretaris Daerah Provinsi Lampung',
                'category'     => 'pembina',
                'bio'          => 'Pengarah strategis percepatan transformasi digital dan penguatan integritas tata kelola pengadaan barang dan jasa Pemerintah Provinsi Lampung.',
                'photo_url'    => 'assets/main/images/team/team-1-1.jpg',
                'social_links' => json_encode(['badge' => 'Pengarah Strategis']),
                'sort_order'   => 1,
                'is_active'    => 1,
            ],
            [
                'name'         => 'Slamet Riadi, S.Sos., M.M.',
                'role_title'   => 'Kepala Biro Pengadaan Barang & Jasa',
                'category'     => 'pembina',
                'bio'          => 'Inisiator ekosistem BegawiPengadaan, memimpin transformasi UKPBJ Lampung menuju Level 3 (Proaktif) dan pusat keunggulan PBJ.',
                'photo_url'    => 'assets/main/images/team/team-1-2.jpg',
                'social_links' => json_encode(['badge' => 'Penanggung Jawab']),
                'sort_order'   => 2,
                'is_active'    => 1,
            ],
            [
                'name'         => 'Software Engineering & UI/UX Crew',
                'role_title'   => 'Software Engineering & UX System',
                'category'     => 'pengembang',
                'bio'          => 'Merancang arsitektur MVC CodeIgniter 4 yang gesit, integrasi database MySQL teroptimasi, dan tata letak responsif modern WowDash.',
                'photo_url'    => 'assets/main/images/team/team-1-5.jpg',
                'social_links' => json_encode(['badge' => 'Tim Pengembang']),
                'sort_order'   => 3,
                'is_active'    => 1,
            ],
            [
                'name'         => 'Tim LPSE & Pengelola SPSE',
                'role_title'   => 'Infrastruktur & Layanan LPSE',
                'category'     => 'pengembang',
                'bio'          => 'Menjamin ketersediaan server 24/7 dengan standardisasi ISO 27001 dan keamanan siber pengadaan.',
                'photo_url'    => 'assets/main/images/team/team-1-4.jpg',
                'social_links' => json_encode(['badge' => 'Infrastruktur SPSE']),
                'sort_order'   => 4,
                'is_active'    => 1,
            ],
            [
                'name'         => 'Tim Pembinaan & Advokasi Hukum PBJ',
                'role_title'   => 'Advokasi & Regulasi PBJ',
                'category'     => 'narasumber',
                'bio'          => 'Harmonisasi peraturan, telaah hukum pengadaan, mitigasi sanggahan tender, dan penyusunan panduan teknis.',
                'photo_url'    => 'assets/main/images/team/team-1-3.jpg',
                'social_links' => json_encode(['badge' => 'Advokasi Hukum']),
                'sort_order'   => 5,
                'is_active'    => 1,
            ],
            [
                'name'         => 'Tim Fasilitator & Master Trainer PBJ',
                'role_title'   => 'Instruktur & Penjamin Mutu',
                'category'     => 'narasumber',
                'bio'          => 'Penyusun silabus pembelajaran LMS berbasis kompetensi LKPP dan bank soal sertifikasi level-1.',
                'photo_url'    => 'assets/main/images/team/team-1-6.jpg',
                'social_links' => json_encode(['badge' => 'Master Trainer']),
                'sort_order'   => 6,
                'is_active'    => 1,
            ],
        ];

        $inserted = 0;
        foreach ($presets as $item) {
            $exists = $this->db->table('team_members')
                ->where('name', $item['name'])
                ->countAllResults();

            if ($exists === 0) {
                $item['created_at'] = date('Y-m-d H:i:s');
                $item['updated_at'] = date('Y-m-d H:i:s');
                $this->db->table('team_members')->insert($item);
                $inserted++;
            }
        }

        $rawActorId = (int) (session()->get('user_id') ?: 0);
        $actorId    = $rawActorId > 0 ? $rawActorId : null;
        $this->auditService->log($actorId, 'landing.team.seeded', 'landing', 'team_members', 0, ['seeded_count' => $inserted]);

        return redirect()->to('dashboard/landing/tim')
            ->with('success', "Rekomendasi tim inovator berhasil disinkronisasi ({$inserted} ditambahkan).");
    }
}
