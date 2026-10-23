<?php

namespace App\Controllers\Admin\Landing;

use App\Controllers\BaseController;
use App\Services\AuditService;
use App\Services\LandingImageUploadService;

class TeamMemberController extends BaseController
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
        $members = $this->db->table('team_members')
            ->orderBy('sort_order', 'ASC')
            ->get()->getResultArray();

        return view('dashboard/landing/team/index', [
            'title'         => 'Kelola Tim Inovator | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Tim Inovator & Pengembang',
            'activeMenu'    => 'landing',
            'activeSubmenu' => 'tim',
            'members'       => $members,
        ]);
    }

    public function create()
    {
        return view('dashboard/landing/team/form', [
            'title'         => 'Tambah Anggota Tim | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Tambah Anggota Tim',
            'activeMenu'    => 'landing',
            'activeSubmenu' => 'tim',
            'member'        => null,
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

        $data = [
            'name'         => trim((string) $this->request->getPost('name')),
            'role_title'   => trim((string) $this->request->getPost('role_title')),
            'nip'          => trim((string) $this->request->getPost('nip')),
            'category'     => trim((string) $this->request->getPost('category') ?: 'pembina'),
            'photo_url'    => $photoUrl,
            'bio'          => trim((string) $this->request->getPost('bio')),
            'social_links' => $socialLinks,
            'sort_order'   => (int) ($this->request->getPost('sort_order') ?: 1),
            'is_active'    => $this->request->getPost('is_active') ? 1 : 0,
            'created_at'   => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s'),
        ];

        $this->db->table('team_members')->insert($data);
        $insertId = $this->db->insertID();

        $actorId = (int) (session()->get('user_id') ?: 1);
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

        $data = [
            'name'         => trim((string) $this->request->getPost('name')),
            'role_title'   => trim((string) $this->request->getPost('role_title')),
            'nip'          => trim((string) $this->request->getPost('nip')),
            'category'     => trim((string) $this->request->getPost('category') ?: 'pembina'),
            'photo_url'    => $photoUrl,
            'bio'          => trim((string) $this->request->getPost('bio')),
            'social_links' => $socialLinks,
            'sort_order'   => (int) ($this->request->getPost('sort_order') ?: 1),
            'is_active'    => $this->request->getPost('is_active') ? 1 : 0,
            'updated_at'   => date('Y-m-d H:i:s'),
        ];

        $this->db->table('team_members')->where('id', $id)->update($data);

        $actorId = (int) (session()->get('user_id') ?: 1);
        $this->auditService->log($actorId, 'landing.team.updated', 'landing', 'team_members', (int) $id, ['before' => $member, 'after' => $data]);

        return redirect()->to('dashboard/landing/tim')->with('success', 'Anggota tim berhasil diperbarui.');
    }

    public function delete($id)
    {
        if ($res = $this->validateSafeMutation()) {
            return $res;
        }
        $member = $this->db->table('team_members')->where('id', $id)->get()->getRowArray();
        if ($member) {
            $this->db->table('team_members')->where('id', $id)->delete();
            $actorId = (int) (session()->get('user_id') ?: 1);
            $this->auditService->log($actorId, 'landing.team.deleted', 'landing', 'team_members', (int) $id, ['deleted' => $member]);
        }

        return redirect()->to('dashboard/landing/tim')->with('success', 'Anggota tim berhasil dihapus.');
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
            return redirect()->to('dashboard/landing/tim')->with('success', 'Status anggota tim berhasil diubah.');
        }

        return redirect()->to('dashboard/landing/tim')->with('error', 'Anggota tim tidak ditemukan.');
    }
}
