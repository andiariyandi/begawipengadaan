<?php

namespace App\Controllers\Admin\Landing;

use App\Controllers\BaseController;
use App\Services\AuditService;
use App\Services\SystemSettingService;

class AnnouncementController extends BaseController
{
    private AuditService $auditService;
    private SystemSettingService $settingService;
    private $db;

    public function __construct()
    {
        $this->auditService = new AuditService();
        $this->settingService = new SystemSettingService();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $announcements = $this->db->table('site_announcements')
            ->orderBy('sort_order', 'ASC')
            ->get()->getResultArray();

        $popupEnabled = (string) $this->settingService->get('announcement', 'popup_enabled', '1');
        $popupBadge   = (string) $this->settingService->get('announcement', 'popup_badge', 'PENGUMUMAN RESMI PBJ PROVINSI LAMPUNG');

        return view('dashboard/landing/announcement/index', [
            'title'         => 'Kelola Popup Pengumuman | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Popup Promo & Pengumuman Beranda',
            'activeMenu'    => 'landing',
            'activeSubmenu' => 'pengumuman',
            'announcements' => $announcements,
            'popupEnabled'  => $popupEnabled,
            'popupBadge'    => $popupBadge,
        ]);
    }

    public function saveSettings()
    {
        $popupEnabled = $this->request->getPost('popup_enabled') ? '1' : '0';
        $popupBadge   = trim((string) $this->request->getPost('popup_badge'));

        $this->settingService->saveGroup('announcement', [
            'popup_enabled' => $popupEnabled,
            'popup_badge'   => $popupBadge,
        ]);

        return redirect()->to('dashboard/landing/pengumuman')->with('success', 'Pengaturan modal popup berhasil diperbarui.');
    }

    public function create()
    {
        return view('dashboard/landing/announcement/form', [
            'title'         => 'Tambah Pengumuman | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Tambah Slide Pengumuman',
            'activeMenu'    => 'landing',
            'activeSubmenu' => 'pengumuman',
            'announcement'  => null,
        ]);
    }

    public function store()
    {
        $rules = [
            'title' => 'required|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $imageUrl = trim((string) $this->request->getPost('image_url'));
        $file = $this->request->getFile('image_file');
        $uploaded = \App\Services\LandingImageUploadService::upload($file, 'announcements', $imageUrl, 'site_announcement');
        if (! empty($uploaded)) {
            $imageUrl = $uploaded;
        }

        $data = [
            'badge_label' => trim((string) $this->request->getPost('badge_label')),
            'title'       => trim((string) $this->request->getPost('title')),
            'description' => trim((string) $this->request->getPost('description')),
            'image_url'   => $imageUrl ?: 'assets/main/images/event/event-details-top-img-1.jpg',
            'cta_label'   => trim((string) $this->request->getPost('cta_label')),
            'cta_url'     => trim((string) $this->request->getPost('cta_url')),
            'sort_order'  => (int) ($this->request->getPost('sort_order') ?: 1),
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        $this->db->table('site_announcements')->insert($data);
        $insertId = $this->db->insertID();

        $actorId = (int) (session()->get('user_id') ?: 1);
        $this->auditService->log($actorId, 'landing.announcement.created', 'landing', 'site_announcements', (int) $insertId, $data);

        return redirect()->to('dashboard/landing/pengumuman')->with('success', 'Slide pengumuman berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $announcement = $this->db->table('site_announcements')->where('id', $id)->get()->getRowArray();
        if (! $announcement) {
            return redirect()->to('dashboard/landing/pengumuman')->with('error', 'Pengumuman tidak ditemukan.');
        }

        return view('dashboard/landing/announcement/form', [
            'title'         => 'Ubah Pengumuman | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Ubah Slide Pengumuman',
            'activeMenu'    => 'landing',
            'activeSubmenu' => 'pengumuman',
            'announcement'  => $announcement,
        ]);
    }

    public function update($id)
    {
        $announcement = $this->db->table('site_announcements')->where('id', $id)->get()->getRowArray();
        if (! $announcement) {
            return redirect()->to('dashboard/landing/pengumuman')->with('error', 'Pengumuman tidak ditemukan.');
        }

        $rules = [
            'title' => 'required|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $imageUrl = trim((string) ($this->request->getPost('image_url') ?: $announcement['image_url']));
        $file = $this->request->getFile('image_file');
        $uploaded = \App\Services\LandingImageUploadService::upload($file, 'announcements', $imageUrl, 'site_announcement');
        if (! empty($uploaded)) {
            $imageUrl = $uploaded;
        }

        $data = [
            'badge_label' => trim((string) $this->request->getPost('badge_label')),
            'title'       => trim((string) $this->request->getPost('title')),
            'description' => trim((string) $this->request->getPost('description')),
            'image_url'   => $imageUrl ?: $announcement['image_url'],
            'cta_label'   => trim((string) $this->request->getPost('cta_label')),
            'cta_url'     => trim((string) $this->request->getPost('cta_url')),
            'sort_order'  => (int) ($this->request->getPost('sort_order') ?: 1),
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        $this->db->table('site_announcements')->where('id', $id)->update($data);

        $actorId = (int) (session()->get('user_id') ?: 1);
        $this->auditService->log($actorId, 'landing.announcement.updated', 'landing', 'site_announcements', (int) $id, ['before' => $announcement, 'after' => $data]);

        return redirect()->to('dashboard/landing/pengumuman')->with('success', 'Slide pengumuman berhasil diperbarui.');
    }

    public function delete($id)
    {
        if ($res = $this->validateSafeMutation()) {
            return $res;
        }
        $announcement = $this->db->table('site_announcements')->where('id', $id)->get()->getRowArray();
        if ($announcement) {
            $this->db->table('site_announcements')->where('id', $id)->delete();
            $actorId = (int) (session()->get('user_id') ?: 1);
            $this->auditService->log($actorId, 'landing.announcement.deleted', 'landing', 'site_announcements', (int) $id, ['deleted' => $announcement]);
        }

        return redirect()->to('dashboard/landing/pengumuman')->with('success', 'Slide pengumuman berhasil dihapus.');
    }

    public function toggle($id)
    {
        if ($res = $this->validateSafeMutation()) {
            return $res;
        }
        $announcement = $this->db->table('site_announcements')->where('id', $id)->get()->getRowArray();
        if ($announcement) {
            $newStatus = $announcement['is_active'] ? 0 : 1;
            $this->db->table('site_announcements')->where('id', $id)->update([
                'is_active'  => $newStatus,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            return redirect()->to('dashboard/landing/pengumuman')->with('success', 'Status pengumuman berhasil diubah.');
        }

        return redirect()->to('dashboard/landing/pengumuman')->with('error', 'Pengumuman tidak ditemukan.');
    }
}
