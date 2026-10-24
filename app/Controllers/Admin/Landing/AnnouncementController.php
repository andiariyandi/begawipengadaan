<?php

namespace App\Controllers\Admin\Landing;

use App\Controllers\BaseController;
use App\Services\AuditService;
use App\Services\SystemSettingService;
use App\Services\LandingImageUploadService;
use CodeIgniter\Database\ConnectionInterface;

class AnnouncementController extends BaseController
{
    private AuditService $auditService;
    private SystemSettingService $settingService;
    private ConnectionInterface $db;

    public function __construct()
    {
        $this->auditService   = new AuditService();
        $this->settingService = new SystemSettingService();
        $this->db             = \Config\Database::connect();
    }

    public function index()
    {
        $announcements = $this->db->table('site_announcements')
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'DESC')
            ->get()->getResultArray();

        $popupEnabled = (string) $this->settingService->get('announcement', 'popup_enabled', '1');
        $popupBadge   = (string) $this->settingService->get('announcement', 'popup_badge', 'PENGUMUMAN RESMI PBJ PROVINSI LAMPUNG');

        $activeCount   = count(array_filter($announcements, fn($a) => (int) ($a['is_active'] ?? 0) === 1));
        $inactiveCount = count($announcements) - $activeCount;

        $stats = [
            'total'          => count($announcements),
            'active_count'   => $activeCount,
            'inactive_count' => $inactiveCount,
            'popup_enabled'  => $popupEnabled === '1',
        ];

        return view('dashboard/landing/announcement/index', [
            'title'         => 'Kelola Popup Pengumuman & Promo | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Popup Promo & Pengumuman Beranda',
            'activeMenu'    => 'landing',
            'activeSubmenu' => 'pengumuman',
            'announcements' => $announcements,
            'popupEnabled'  => $popupEnabled,
            'popupBadge'    => $popupBadge,
            'stats'         => $stats,
        ]);
    }

    public function saveSettings()
    {
        $popupEnabled = $this->request->getPost('popup_enabled') ? '1' : '0';
        $popupBadge   = trim((string) $this->request->getPost('popup_badge'));

        $data = [
            'popup_enabled' => $popupEnabled,
            'popup_badge'   => $popupBadge !== '' ? $popupBadge : 'PENGUMUMAN RESMI PBJ PROVINSI LAMPUNG',
        ];

        $this->settingService->saveGroup('announcement', $data);

        $rawActorId = (int) (session()->get('user_id') ?: 0);
        $actorId    = $rawActorId > 0 ? $rawActorId : null;
        $this->auditService->log($actorId, 'landing.announcement.settings_updated', 'site_settings', 'announcement', null, $data);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pengaturan modal popup berhasil diperbarui.',
                'data'    => $data,
            ]);
        }

        return redirect()->to('dashboard/landing/pengumuman')->with('success', 'Pengaturan modal popup berhasil diperbarui.');
    }

    public function create()
    {
        return view('dashboard/landing/announcement/form', [
            'title'         => 'Tambah Slide Pengumuman | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Tambah Slide Pengumuman Popup',
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
        $uploaded = LandingImageUploadService::upload($file, 'announcements', $imageUrl, 'site_announcement');
        if (! empty($uploaded)) {
            $imageUrl = $uploaded;
        }

        $startDate = $this->request->getPost('start_date');
        $endDate   = $this->request->getPost('end_date');

        $data = [
            'badge_label' => trim((string) ($this->request->getPost('badge_label') ?: 'PENGUMUMAN RESMI')),
            'title'       => trim((string) $this->request->getPost('title')),
            'description' => trim((string) $this->request->getPost('description')),
            'image_url'   => $imageUrl ?: 'assets/main/images/event/event-details-top-img-1.jpg',
            'cta_label'   => trim((string) ($this->request->getPost('cta_label') ?: 'Lihat Detail')),
            'cta_url'     => trim((string) ($this->request->getPost('cta_url') ?: 'event')),
            'start_date'  => ! empty($startDate) ? $startDate : null,
            'end_date'    => ! empty($endDate) ? $endDate : null,
            'sort_order'  => (int) ($this->request->getPost('sort_order') ?: 1),
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        $this->db->table('site_announcements')->insert($data);
        $insertId = $this->db->insertID();

        $rawActorId = (int) (session()->get('user_id') ?: 0);
        $actorId    = $rawActorId > 0 ? $rawActorId : null;
        $this->auditService->log($actorId, 'landing.announcement.created', 'site_announcements', 'id', (int) $insertId, $data);

        return redirect()->to('dashboard/landing/pengumuman')->with('success', 'Slide pengumuman berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $announcement = $this->db->table('site_announcements')->where('id', $id)->get()->getRowArray();
        if (! $announcement) {
            return redirect()->to('dashboard/landing/pengumuman')->with('error', 'Slide pengumuman tidak ditemukan.');
        }

        return view('dashboard/landing/announcement/form', [
            'title'         => 'Ubah Slide Pengumuman | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Ubah Slide Pengumuman Popup',
            'activeMenu'    => 'landing',
            'activeSubmenu' => 'pengumuman',
            'announcement'  => $announcement,
        ]);
    }

    public function update($id)
    {
        $announcement = $this->db->table('site_announcements')->where('id', $id)->get()->getRowArray();
        if (! $announcement) {
            return redirect()->to('dashboard/landing/pengumuman')->with('error', 'Slide pengumuman tidak ditemukan.');
        }

        $rules = [
            'title' => 'required|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $imageUrl = trim((string) ($this->request->getPost('image_url') ?: $announcement['image_url']));
        $file = $this->request->getFile('image_file');
        $uploaded = LandingImageUploadService::upload($file, 'announcements', $imageUrl, 'site_announcement');
        if (! empty($uploaded)) {
            $imageUrl = $uploaded;
        }

        $startDate = $this->request->getPost('start_date');
        $endDate   = $this->request->getPost('end_date');

        $data = [
            'badge_label' => trim((string) ($this->request->getPost('badge_label') ?: 'PENGUMUMAN RESMI')),
            'title'       => trim((string) $this->request->getPost('title')),
            'description' => trim((string) $this->request->getPost('description')),
            'image_url'   => $imageUrl ?: $announcement['image_url'],
            'cta_label'   => trim((string) ($this->request->getPost('cta_label') ?: 'Lihat Detail')),
            'cta_url'     => trim((string) ($this->request->getPost('cta_url') ?: 'event')),
            'start_date'  => ! empty($startDate) ? $startDate : null,
            'end_date'    => ! empty($endDate) ? $endDate : null,
            'sort_order'  => (int) ($this->request->getPost('sort_order') ?: 1),
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        $this->db->table('site_announcements')->where('id', $id)->update($data);

        $rawActorId = (int) (session()->get('user_id') ?: 0);
        $actorId    = $rawActorId > 0 ? $rawActorId : null;
        $this->auditService->log($actorId, 'landing.announcement.updated', 'site_announcements', 'id', (int) $id, ['before' => $announcement, 'after' => $data]);

        return redirect()->to('dashboard/landing/pengumuman')->with('success', 'Slide pengumuman berhasil diperbarui.');
    }

    public function duplicate($id)
    {
        $announcement = $this->db->table('site_announcements')->where('id', $id)->get()->getRowArray();
        if (! $announcement) {
            return redirect()->to('dashboard/landing/pengumuman')->with('error', 'Slide pengumuman tidak ditemukan.');
        }

        $newData = [
            'badge_label' => $announcement['badge_label'],
            'title'       => $announcement['title'] . ' (Salinan)',
            'description' => $announcement['description'],
            'image_url'   => $announcement['image_url'],
            'cta_label'   => $announcement['cta_label'],
            'cta_url'     => $announcement['cta_url'],
            'start_date'  => $announcement['start_date'] ?? null,
            'end_date'    => $announcement['end_date'] ?? null,
            'sort_order'  => ((int) ($announcement['sort_order'] ?? 1)) + 1,
            'is_active'   => 0, // Duplicate starts as inactive draft
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        $this->db->table('site_announcements')->insert($newData);
        $newId = $this->db->insertID();

        $rawActorId = (int) (session()->get('user_id') ?: 0);
        $actorId    = $rawActorId > 0 ? $rawActorId : null;
        $this->auditService->log($actorId, 'landing.announcement.duplicated', 'site_announcements', 'id', (int) $newId, $newData);

        return redirect()->to('dashboard/landing/pengumuman')->with('success', 'Slide pengumuman berhasil diduplikat sebagai draf.');
    }

    public function updateOrder()
    {
        $id       = (int) ($this->request->getPost('id') ?: $this->request->getVar('id') ?: ($_POST['id'] ?? 0));
        $rawOrder = $this->request->getPost('order') ?? $this->request->getVar('order') ?? ($_POST['order'] ?? null);
        $order    = ($rawOrder !== null && $rawOrder !== '') ? (int) $rawOrder : 1;

        if ($id <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID slide tidak valid.'])->setStatusCode(400);
        }

        $this->db->table('site_announcements')->where('id', $id)->update([
            'sort_order' => $order,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $rawActorId = (int) (session()->get('user_id') ?: 0);
        $actorId    = $rawActorId > 0 ? $rawActorId : null;
        $this->auditService->log($actorId, 'landing.announcement.reordered', 'site_announcements', 'sort_order', $id, ['order' => $order]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Urutan slide berhasil diperbarui.',
                'order'   => $order,
            ]);
        }

        return redirect()->to('dashboard/landing/pengumuman')->with('success', 'Urutan slide berhasil diperbarui.');
    }

    public function delete($id)
    {
        if ($res = $this->validateSafeMutation()) {
            return $res;
        }

        $announcement = $this->db->table('site_announcements')->where('id', $id)->get()->getRowArray();
        if ($announcement) {
            $this->db->table('site_announcements')->where('id', $id)->delete();
            $rawActorId = (int) (session()->get('user_id') ?: 0);
            $actorId    = $rawActorId > 0 ? $rawActorId : null;
            $this->auditService->log($actorId, 'landing.announcement.deleted', 'site_announcements', 'id', (int) $id, ['deleted' => $announcement]);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => true, 'message' => 'Slide pengumuman berhasil dihapus.']);
            }
            return redirect()->to('dashboard/landing/pengumuman')->with('success', 'Slide pengumuman berhasil dihapus.');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Pengumuman tidak ditemukan.'])->setStatusCode(404);
        }
        return redirect()->to('dashboard/landing/pengumuman')->with('error', 'Slide pengumuman tidak ditemukan.');
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

            $rawActorId = (int) (session()->get('user_id') ?: 0);
            $actorId    = $rawActorId > 0 ? $rawActorId : null;
            $this->auditService->log($actorId, 'landing.announcement.toggled', 'site_announcements', 'is_active', (int) $id, ['new_status' => $newStatus]);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success'   => true,
                    'message'   => 'Status pengumuman berhasil diubah.',
                    'is_active' => $newStatus,
                    'id'        => (int) $id,
                ]);
            }

            return redirect()->to('dashboard/landing/pengumuman')->with('success', 'Status pengumuman berhasil diubah.');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Pengumuman tidak ditemukan.'])->setStatusCode(404);
        }
        return redirect()->to('dashboard/landing/pengumuman')->with('error', 'Pengumuman tidak ditemukan.');
    }
}
