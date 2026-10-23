<?php

namespace App\Controllers\Admin\Landing;

use App\Controllers\BaseController;
use App\Services\AuditService;

class TestimonialController extends BaseController
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
        $testimonials = $this->db->table('testimonials')
            ->orderBy('sort_order', 'ASC')
            ->get()->getResultArray();

        return view('dashboard/landing/testimonial/index', [
            'title'         => 'Kelola Testimoni | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Testimoni Pengguna & Mitra',
            'activeMenu'    => 'landing',
            'activeSubmenu' => 'testimoni',
            'testimonials'  => $testimonials,
        ]);
    }

    public function create()
    {
        return view('dashboard/landing/testimonial/form', [
            'title'         => 'Tambah Testimoni | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Tambah Testimoni',
            'activeMenu'    => 'landing',
            'activeSubmenu' => 'testimoni',
            'testimonial'   => null,
        ]);
    }

    public function store()
    {
        $rules = [
            'name'  => 'required|max_length[150]',
            'quote' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $avatarUrl = trim((string) $this->request->getPost('avatar_url'));
        $file = $this->request->getFile('avatar_file');
        $uploaded = \App\Services\LandingImageUploadService::upload($file, 'testimonials', $avatarUrl, 'user_testimonial');
        if (! empty($uploaded)) {
            $avatarUrl = $uploaded;
        }

        $data = [
            'name'             => trim((string) $this->request->getPost('name')),
            'role_title'       => trim((string) $this->request->getPost('role_title')),
            'organization'     => trim((string) $this->request->getPost('organization')),
            'avatar_url'       => $avatarUrl ?: 'assets/main/images/testimonial/testimonial-1-1.jpg',
            'rating'           => (int) ($this->request->getPost('rating') ?: 5),
            'quote'            => trim((string) $this->request->getPost('quote')),
            'impact_statement' => trim((string) $this->request->getPost('impact_statement')),
            'category'         => trim((string) $this->request->getPost('category') ?: 'opd'),
            'sort_order'       => (int) ($this->request->getPost('sort_order') ?: 1),
            'is_active'        => $this->request->getPost('is_active') ? 1 : 0,
            'created_at'       => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s'),
        ];

        $this->db->table('testimonials')->insert($data);
        $insertId = $this->db->insertID();

        $actorId = (int) (session()->get('user_id') ?: 1);
        $this->auditService->log($actorId, 'landing.testimonial.created', 'landing', 'testimonials', (int) $insertId, $data);

        return redirect()->to('dashboard/landing/testimoni')->with('success', 'Testimoni berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $testimonial = $this->db->table('testimonials')->where('id', $id)->get()->getRowArray();
        if (! $testimonial) {
            return redirect()->to('dashboard/landing/testimoni')->with('error', 'Testimoni tidak ditemukan.');
        }

        return view('dashboard/landing/testimonial/form', [
            'title'         => 'Ubah Testimoni | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Ubah Testimoni',
            'activeMenu'    => 'landing',
            'activeSubmenu' => 'testimoni',
            'testimonial'   => $testimonial,
        ]);
    }

    public function update($id)
    {
        $testimonial = $this->db->table('testimonials')->where('id', $id)->get()->getRowArray();
        if (! $testimonial) {
            return redirect()->to('dashboard/landing/testimoni')->with('error', 'Testimoni tidak ditemukan.');
        }

        $rules = [
            'name'  => 'required|max_length[150]',
            'quote' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $avatarUrl = trim((string) ($this->request->getPost('avatar_url') ?: $testimonial['avatar_url']));
        $file = $this->request->getFile('avatar_file');
        $uploaded = \App\Services\LandingImageUploadService::upload($file, 'testimonials', $avatarUrl, 'user_testimonial');
        if (! empty($uploaded)) {
            $avatarUrl = $uploaded;
        }

        $data = [
            'name'             => trim((string) $this->request->getPost('name')),
            'role_title'       => trim((string) $this->request->getPost('role_title')),
            'organization'     => trim((string) $this->request->getPost('organization')),
            'avatar_url'       => $avatarUrl ?: $testimonial['avatar_url'],
            'rating'           => (int) ($this->request->getPost('rating') ?: 5),
            'quote'            => trim((string) $this->request->getPost('quote')),
            'impact_statement' => trim((string) $this->request->getPost('impact_statement')),
            'category'         => trim((string) $this->request->getPost('category') ?: 'opd'),
            'sort_order'       => (int) ($this->request->getPost('sort_order') ?: 1),
            'is_active'        => $this->request->getPost('is_active') ? 1 : 0,
            'updated_at'       => date('Y-m-d H:i:s'),
        ];

        $this->db->table('testimonials')->where('id', $id)->update($data);

        $actorId = (int) (session()->get('user_id') ?: 1);
        $this->auditService->log($actorId, 'landing.testimonial.updated', 'landing', 'testimonials', (int) $id, ['before' => $testimonial, 'after' => $data]);

        return redirect()->to('dashboard/landing/testimoni')->with('success', 'Testimoni berhasil diperbarui.');
    }

    public function delete($id)
    {
        if ($res = $this->validateSafeMutation()) {
            return $res;
        }
        $testimonial = $this->db->table('testimonials')->where('id', $id)->get()->getRowArray();
        if ($testimonial) {
            $this->db->table('testimonials')->where('id', $id)->delete();
            $actorId = (int) (session()->get('user_id') ?: 1);
            $this->auditService->log($actorId, 'landing.testimonial.deleted', 'landing', 'testimonials', (int) $id, ['deleted' => $testimonial]);
        }

        return redirect()->to('dashboard/landing/testimoni')->with('success', 'Testimoni berhasil dihapus.');
    }

    public function toggle($id)
    {
        if ($res = $this->validateSafeMutation()) {
            return $res;
        }
        $testimonial = $this->db->table('testimonials')->where('id', $id)->get()->getRowArray();
        if ($testimonial) {
            $newStatus = $testimonial['is_active'] ? 0 : 1;
            $this->db->table('testimonials')->where('id', $id)->update([
                'is_active'  => $newStatus,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            return redirect()->to('dashboard/landing/testimoni')->with('success', 'Status testimoni berhasil diubah.');
        }

        return redirect()->to('dashboard/landing/testimoni')->with('error', 'Testimoni tidak ditemukan.');
    }
}
