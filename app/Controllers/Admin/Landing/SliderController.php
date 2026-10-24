<?php

namespace App\Controllers\Admin\Landing;

use App\Controllers\BaseController;
use App\Services\AuditService;
use App\Services\LandingImageUploadService;

class SliderController extends BaseController
{
    private AuditService $auditService;
    private $db;

    public function __construct()
    {
        $this->auditService = new AuditService();
        $this->db           = \Config\Database::connect();
    }

    public function index()
    {
        $sliders = $this->db->table('home_sliders')
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();

        $totalSlides   = count($sliders);
        $activeSlides  = 0;
        $inactiveSlides = 0;
        $latestUpdated = null;

        foreach ($sliders as $s) {
            if (! empty($s['is_active'])) {
                $activeSlides++;
            } else {
                $inactiveSlides++;
            }
            if ($latestUpdated === null || strtotime($s['updated_at'] ?? '') > strtotime($latestUpdated)) {
                $latestUpdated = $s['updated_at'] ?? null;
            }
        }

        $stats = [
            'total'          => $totalSlides,
            'active'         => $activeSlides,
            'inactive'       => $inactiveSlides,
            'latest_updated' => $latestUpdated,
        ];

        return view('dashboard/landing/slider/index', [
            'title'         => 'Kelola Slider & Hero Beranda | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Slider & Hero Beranda',
            'activeMenu'    => 'landing',
            'activeSubmenu' => 'slider',
            'sliders'       => $sliders,
            'stats'         => $stats,
        ]);
    }

    public function create()
    {
        $maxOrderRow = $this->db->table('home_sliders')
            ->selectMax('sort_order', 'max_order')
            ->get()->getRowArray();
        $nextOrder = ((int) ($maxOrderRow['max_order'] ?? 0)) + 1;

        return view('dashboard/landing/slider/form', [
            'title'         => 'Tambah Slider Hero | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Tambah Slide Hero Beranda',
            'activeMenu'    => 'landing',
            'activeSubmenu' => 'slider',
            'slider'        => null,
            'nextOrder'     => $nextOrder,
            'presets'       => $this->getSliderPresets(),
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
        $file     = $this->request->getFile('image_file');
        $uploaded = LandingImageUploadService::upload($file, 'sliders', $imageUrl, 'home_slider');
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
            'title'         => 'Ubah Slider Hero | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Ubah Slide Hero Beranda',
            'activeMenu'    => 'landing',
            'activeSubmenu' => 'slider',
            'slider'        => $slider,
            'presets'       => $this->getSliderPresets(),
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
        $file     = $this->request->getFile('image_file');
        $uploaded = LandingImageUploadService::upload($file, 'sliders', $imageUrl, 'home_slider');
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

    public function duplicate($id)
    {
        if ($res = $this->validateSafeMutation()) {
            return $res;
        }

        $slider = $this->db->table('home_sliders')->where('id', $id)->get()->getRowArray();
        if (! $slider) {
            return redirect()->to('dashboard/landing/slider')->with('error', 'Slide tidak ditemukan.');
        }

        $maxOrderRow = $this->db->table('home_sliders')
            ->selectMax('sort_order', 'max_order')
            ->get()->getRowArray();
        $nextOrder = ((int) ($maxOrderRow['max_order'] ?? 0)) + 1;

        $now = date('Y-m-d H:i:s');
        $duplicateData = [
            'badge_text'            => $slider['badge_text'],
            'title'                 => '[Salinan] ' . $slider['title'],
            'subtitle'              => $slider['subtitle'],
            'image_url'             => $slider['image_url'],
            'button_text'           => $slider['button_text'],
            'button_url'            => $slider['button_url'],
            'secondary_button_text' => $slider['secondary_button_text'],
            'secondary_button_url'  => $slider['secondary_button_url'],
            'sort_order'            => $nextOrder,
            'is_active'             => 0, // Inactive by default for review
            'created_at'            => $now,
            'updated_at'            => $now,
        ];

        $this->db->table('home_sliders')->insert($duplicateData);
        $newId = $this->db->insertID();

        $actorId = (int) (session()->get('user_id') ?: 1);
        $this->auditService->log($actorId, 'landing.slider.duplicated', 'landing', 'home_sliders', (int) $newId, [
            'original_id' => $id,
            'new_data'    => $duplicateData,
        ]);

        return redirect()->to('dashboard/landing/slider')->with('success', 'Slide berhasil diduplikasi sebagai draf (nonaktif). Silakan sesuaikan.');
    }

    public function updateOrder()
    {
        if ($res = $this->validateSafeMutation()) {
            return $res;
        }

        $json = $this->request->getJSON(true);
        $orders = $json['orders'] ?? $this->request->getPost('orders');

        if (! empty($orders) && is_array($orders)) {
            foreach ($orders as $item) {
                if (isset($item['id'], $item['sort_order'])) {
                    $this->db->table('home_sliders')
                        ->where('id', (int) $item['id'])
                        ->update([
                            'sort_order' => (int) $item['sort_order'],
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                }
            }

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Urutan slide berhasil diperbarui.',
                ]);
            }

            return redirect()->to('dashboard/landing/slider')->with('success', 'Urutan slide berhasil diperbarui.');
        }

        // Support move up / move down single slide
        $slideId = (int) ($this->request->getPost('id') ?: ($json['id'] ?? 0));
        $direction = (string) ($this->request->getPost('direction') ?: ($json['direction'] ?? ''));

        if ($slideId > 0 && in_array($direction, ['up', 'down'], true)) {
            $current = $this->db->table('home_sliders')->where('id', $slideId)->get()->getRowArray();
            if ($current) {
                $currentOrder = (int) $current['sort_order'];
                $targetQuery = $this->db->table('home_sliders');
                if ($direction === 'up') {
                    $targetQuery->where('sort_order <', $currentOrder)->orderBy('sort_order', 'DESC');
                } else {
                    $targetQuery->where('sort_order >', $currentOrder)->orderBy('sort_order', 'ASC');
                }
                $adjacent = $targetQuery->get()->getRowArray();

                if ($adjacent) {
                    $adjOrder = (int) $adjacent['sort_order'];
                    // Swap orders
                    $this->db->table('home_sliders')->where('id', $current['id'])->update(['sort_order' => $adjOrder]);
                    $this->db->table('home_sliders')->where('id', $adjacent['id'])->update(['sort_order' => $currentOrder]);
                }
            }

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Urutan slide berhasil ditukar.',
                ]);
            }

            return redirect()->to('dashboard/landing/slider')->with('success', 'Urutan slide berhasil ditukar.');
        }

        return redirect()->to('dashboard/landing/slider')->with('error', 'Data urutan tidak valid.');
    }

    public function delete($id)
    {
        if ($res = $this->validateSafeMutation()) {
            return $res;
        }

        $slider = $this->db->table('home_sliders')->where('id', $id)->get()->getRowArray();
        if ($slider) {
            $this->db->table('home_sliders')->where('id', $id)->delete();
            $actorId = (int) (session()->get('user_id') ?: 1);
            $this->auditService->log($actorId, 'landing.slider.deleted', 'landing', 'home_sliders', (int) $id, ['deleted' => $slider]);
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Slide hero beranda berhasil dihapus.',
            ]);
        }

        return redirect()->to('dashboard/landing/slider')->with('success', 'Slide hero beranda berhasil dihapus.');
    }

    public function toggle($id)
    {
        if ($res = $this->validateSafeMutation()) {
            return $res;
        }

        $slider = $this->db->table('home_sliders')->where('id', $id)->get()->getRowArray();
        if ($slider) {
            $newStatus = $slider['is_active'] ? 0 : 1;
            $this->db->table('home_sliders')->where('id', $id)->update([
                'is_active'  => $newStatus,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $actorId = (int) (session()->get('user_id') ?: 1);
            $this->auditService->log($actorId, 'landing.slider.toggle_active', 'landing', 'home_sliders', (int) $id, [
                'previous' => $slider['is_active'],
                'current'  => $newStatus,
            ]);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status'     => 'success',
                    'new_status' => $newStatus,
                    'message'    => 'Status slide berhasil diubah menjadi ' . ($newStatus ? 'Aktif' : 'Nonaktif') . '.',
                ]);
            }

            return redirect()->to('dashboard/landing/slider')->with('success', 'Status slide berhasil diubah.');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Slide tidak ditemukan.',
            ])->setStatusCode(404);
        }

        return redirect()->to('dashboard/landing/slider')->with('error', 'Slide tidak ditemukan.');
    }

    /**
     * Presets helper for form
     */
    private function getSliderPresets(): array
    {
        return [
            'routes' => [
                ['label' => 'Regulasi PBJ', 'url' => 'regulasi', 'btn' => 'Pusat Regulasi PBJ'],
                ['label' => 'Arena Belajar LMS', 'url' => 'arena-belajar', 'btn' => 'Arena Belajar'],
                ['label' => 'Bunga Rampai', 'url' => 'bunga-rampai', 'btn' => 'Bunga Rampai PBJ'],
                ['label' => 'Event & Bimtek', 'url' => 'event', 'btn' => 'Lihat Agenda Bimtek'],
                ['label' => 'Warta Berita', 'url' => 'berita', 'btn' => 'Berita & Warta'],
                ['label' => 'Verifikasi Sertifikat', 'url' => 'verifikasi-sertifikat', 'btn' => 'Verifikasi Sertifikat'],
                ['label' => 'Kontak & Helpdesk', 'url' => 'kontak', 'btn' => 'Konsultasi Pengadaan'],
                ['label' => 'Survei Kepuasan', 'url' => 'survei-kepuasan', 'btn' => 'Isi Survei Kepuasan'],
            ],
            'backgrounds' => [
                ['label' => 'Hero Utama (Gedung & Lanskap)', 'path' => 'assets/main/images/backgrounds/slider-1-1.png'],
                ['label' => 'Hero Banner 2 (Aparatur & Layanan)', 'path' => 'assets/main/images/backgrounds/slider-1-2.png'],
                ['label' => 'Hero Event Banner', 'path' => 'assets/main/images/event/event-details-top-img-1.jpg'],
            ],
            'headlines' => [
                'Transformasi Digital Terpadu <span>Pengadaan Barang & Jasa</span>',
                'Pusat Edukasi &amp; Peningkatan <span>Kompetensi SDM Pengadaan</span>',
                'Transparansi, Efisiensi &amp; Akuntabilitas <span>UKPBJ Proaktif Lampung</span>',
            ],
        ];
    }
}
