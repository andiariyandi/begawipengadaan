<?php

namespace App\Controllers\Admin\Landing;

use App\Controllers\BaseController;
use App\Services\AuditService;

class SliderController extends BaseController
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
        $sliders = $this->db->table('home_sliders')
            ->orderBy('sort_order', 'ASC')
            ->get()->getResultArray();

        return view('dashboard/landing/slider/index', [
            'title'         => 'Kelola Slider & Hero | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Slider & Hero Beranda',
            'activeMenu'    => 'landing',
            'activeSubmenu' => 'slider',
            'sliders'       => $sliders,
        ]);
    }

    public function create()
    {
        return view('dashboard/landing/slider/form', [
            'title'         => 'Tambah Slider | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Tambah Slide Hero',
            'activeMenu'    => 'landing',
            'activeSubmenu' => 'slider',
            'slider'        => null,
        ]);
    }

    public function store()
    {
        $rules = [
            'title'       => 'required|max_length[255]',
            'button_text' => 'permit_empty|max_length[100]',
            'sort_order'  => 'permit_empty|integer',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $imageUrl = trim((string) $this->request->getPost('image_url'));
        $file = $this->request->getFile('image_file');
        $uploaded = \App\Services\LandingImageUploadService::upload($file, 'sliders', $imageUrl, 'home_slider');
        if (! empty($uploaded)) {
            $imageUrl = $uploaded;
        }

        $data = [
            'badge_text'            => trim((string) $this->request->getPost('badge_text')),
            'title'                 => trim((string) $this->request->getPost('title')),
            'subtitle'              => trim((string) $this->request->getPost('subtitle')),
            'image_url'             => $imageUrl ?: 'assets/main/images/backgrounds/slider-1-1.png',
            'button_text'           => trim((string) $this->request->getPost('button_text')),
            'button_url'            => trim((string) $this->request->getPost('button_url')),
            'secondary_button_text' => trim((string) $this->request->getPost('secondary_button_text')),
            'secondary_button_url'  => trim((string) $this->request->getPost('secondary_button_url')),
            'sort_order'            => (int) ($this->request->getPost('sort_order') ?: 1),
            'is_active'             => $this->request->getPost('is_active') ? 1 : 0,
            'created_at'            => date('Y-m-d H:i:s'),
            'updated_at'            => date('Y-m-d H:i:s'),
        ];

        $this->db->table('home_sliders')->insert($data);
        $insertId = $this->db->insertID();

        $actorId = (int) (session()->get('user_id') ?: 1);
        $this->auditService->log($actorId, 'landing.slider.created', 'landing', 'home_sliders', (int) $insertId, $data);

        return redirect()->to('dashboard/landing/slider')->with('success', 'Slide hero beranda berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $slider = $this->db->table('home_sliders')->where('id', $id)->get()->getRowArray();
        if (! $slider) {
            return redirect()->to('dashboard/landing/slider')->with('error', 'Slide tidak ditemukan.');
        }

        return view('dashboard/landing/slider/form', [
            'title'         => 'Ubah Slider | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Ubah Slide Hero',
            'activeMenu'    => 'landing',
            'activeSubmenu' => 'slider',
            'slider'        => $slider,
        ]);
    }

    public function update($id)
    {
        $slider = $this->db->table('home_sliders')->where('id', $id)->get()->getRowArray();
        if (! $slider) {
            return redirect()->to('dashboard/landing/slider')->with('error', 'Slide tidak ditemukan.');
        }

        $rules = [
            'title' => 'required|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $imageUrl = trim((string) ($this->request->getPost('image_url') ?: $slider['image_url']));
        $file = $this->request->getFile('image_file');
        $uploaded = \App\Services\LandingImageUploadService::upload($file, 'sliders', $imageUrl, 'home_slider');
        if (! empty($uploaded)) {
            $imageUrl = $uploaded;
        }

        $data = [
            'badge_text'            => trim((string) $this->request->getPost('badge_text')),
            'title'                 => trim((string) $this->request->getPost('title')),
            'subtitle'              => trim((string) $this->request->getPost('subtitle')),
            'image_url'             => $imageUrl ?: $slider['image_url'],
            'button_text'           => trim((string) $this->request->getPost('button_text')),
            'button_url'            => trim((string) $this->request->getPost('button_url')),
            'secondary_button_text' => trim((string) $this->request->getPost('secondary_button_text')),
            'secondary_button_url'  => trim((string) $this->request->getPost('secondary_button_url')),
            'sort_order'            => (int) ($this->request->getPost('sort_order') ?: 1),
            'is_active'             => $this->request->getPost('is_active') ? 1 : 0,
            'updated_at'            => date('Y-m-d H:i:s'),
        ];

        $this->db->table('home_sliders')->where('id', $id)->update($data);

        $actorId = (int) (session()->get('user_id') ?: 1);
        $this->auditService->log($actorId, 'landing.slider.updated', 'landing', 'home_sliders', (int) $id, ['before' => $slider, 'after' => $data]);

        return redirect()->to('dashboard/landing/slider')->with('success', 'Slide hero beranda berhasil diperbarui.');
    }

    public function delete($id)
    {
        $slider = $this->db->table('home_sliders')->where('id', $id)->get()->getRowArray();
        if ($slider) {
            $this->db->table('home_sliders')->where('id', $id)->delete();
            $actorId = (int) (session()->get('user_id') ?: 1);
            $this->auditService->log($actorId, 'landing.slider.deleted', 'landing', 'home_sliders', (int) $id, ['deleted' => $slider]);
        }

        return redirect()->to('dashboard/landing/slider')->with('success', 'Slide hero beranda berhasil dihapus.');
    }

    public function toggle($id)
    {
        $slider = $this->db->table('home_sliders')->where('id', $id)->get()->getRowArray();
        if ($slider) {
            $newStatus = $slider['is_active'] ? 0 : 1;
            $this->db->table('home_sliders')->where('id', $id)->update([
                'is_active'  => $newStatus,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            return redirect()->to('dashboard/landing/slider')->with('success', 'Status slide berhasil diubah.');
        }

        return redirect()->to('dashboard/landing/slider')->with('error', 'Slide tidak ditemukan.');
    }
}
