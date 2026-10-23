<?php

namespace App\Controllers\Admin\Landing;

use App\Controllers\BaseController;
use App\Services\AuditService;

class HighlightFeatureController extends BaseController
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
        $section = $this->request->getGet('section') ?: 'pilar_begawi';

        $validSections = [
            'pilar_begawi'        => '5 Pilar Ekosistem Begawi',
            'budaya_patik'        => '5 Budaya Kerja PATIK',
            'hierarki_hukum'      => '4 Tingkat Hierarki Hukum PBJ',
            'roadmap_lkpp'        => '4 Level Roadmap Kompetensi LKPP',
            'keamanan_sertifikat' => '4 Pilar Kriptografi & Keamanan Sertifikat',
        ];

        if (! array_key_exists($section, $validSections)) {
            $section = 'pilar_begawi';
        }

        $items = $this->db->table('feature_highlights')
            ->where('section_key', $section)
            ->orderBy('sort_order', 'ASC')
            ->get()->getResultArray();

        return view('dashboard/landing/highlight/index', [
            'title'         => 'Kelola Pilar & Hierarki | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Pilar, Budaya & Hierarki Regulasi',
            'activeMenu'    => 'landing',
            'activeSubmenu' => 'pilar-hierarki',
            'items'         => $items,
            'currentSection'=> $section,
            'validSections' => $validSections,
        ]);
    }

    public function create()
    {
        $section = $this->request->getGet('section') ?: 'pilar_begawi';

        return view('dashboard/landing/highlight/form', [
            'title'          => 'Tambah Item Highlight | Dashboard BegawiPengadaan',
            'pageTitle'      => 'Tambah Highlight Fitur',
            'activeMenu'     => 'landing',
            'activeSubmenu'  => 'pilar-hierarki',
            'item'           => null,
            'currentSection' => $section,
        ]);
    }

    public function store()
    {
        $rules = [
            'section_key' => 'required',
            'title'       => 'required|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $sectionKey = trim((string) $this->request->getPost('section_key'));

        $data = [
            'section_key' => $sectionKey,
            'code'        => trim((string) $this->request->getPost('code')),
            'badge_text'  => trim((string) $this->request->getPost('badge_text')),
            'title'       => trim((string) $this->request->getPost('title')),
            'subtitle'    => trim((string) $this->request->getPost('subtitle')),
            'description' => trim((string) $this->request->getPost('description')),
            'icon_class'  => trim((string) $this->request->getPost('icon_class')),
            'color_hex'   => trim((string) $this->request->getPost('color_hex')),
            'link_url'    => trim((string) $this->request->getPost('link_url')),
            'sort_order'  => (int) ($this->request->getPost('sort_order') ?: 1),
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        $this->db->table('feature_highlights')->insert($data);
        $insertId = $this->db->insertID();

        $actorId = (int) (session()->get('user_id') ?: 1);
        $this->auditService->log($actorId, 'landing.highlight.created', 'landing', 'feature_highlights', (int) $insertId, $data);

        return redirect()->to('dashboard/landing/pilar-hierarki?section=' . urlencode($sectionKey))
            ->with('success', 'Item berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $item = $this->db->table('feature_highlights')->where('id', $id)->get()->getRowArray();
        if (! $item) {
            return redirect()->to('dashboard/landing/pilar-hierarki')->with('error', 'Item tidak ditemukan.');
        }

        return view('dashboard/landing/highlight/form', [
            'title'          => 'Ubah Item Highlight | Dashboard BegawiPengadaan',
            'pageTitle'      => 'Ubah Highlight Fitur',
            'activeMenu'     => 'landing',
            'activeSubmenu'  => 'pilar-hierarki',
            'item'           => $item,
            'currentSection' => $item['section_key'],
        ]);
    }

    public function update($id)
    {
        $item = $this->db->table('feature_highlights')->where('id', $id)->get()->getRowArray();
        if (! $item) {
            return redirect()->to('dashboard/landing/pilar-hierarki')->with('error', 'Item tidak ditemukan.');
        }

        $rules = [
            'title' => 'required|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'code'        => trim((string) $this->request->getPost('code')),
            'badge_text'  => trim((string) $this->request->getPost('badge_text')),
            'title'       => trim((string) $this->request->getPost('title')),
            'subtitle'    => trim((string) $this->request->getPost('subtitle')),
            'description' => trim((string) $this->request->getPost('description')),
            'icon_class'  => trim((string) $this->request->getPost('icon_class')),
            'color_hex'   => trim((string) $this->request->getPost('color_hex')),
            'link_url'    => trim((string) $this->request->getPost('link_url')),
            'sort_order'  => (int) ($this->request->getPost('sort_order') ?: 1),
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        $this->db->table('feature_highlights')->where('id', $id)->update($data);

        $actorId = (int) (session()->get('user_id') ?: 1);
        $this->auditService->log($actorId, 'landing.highlight.updated', 'landing', 'feature_highlights', (int) $id, ['before' => $item, 'after' => $data]);

        return redirect()->to('dashboard/landing/pilar-hierarki?section=' . urlencode($item['section_key']))
            ->with('success', 'Item berhasil diperbarui.');
    }

    public function delete($id)
    {
        if ($res = $this->validateSafeMutation()) {
            return $res;
        }
        $item = $this->db->table('feature_highlights')->where('id', $id)->get()->getRowArray();
        if ($item) {
            $this->db->table('feature_highlights')->where('id', $id)->delete();
            $actorId = (int) (session()->get('user_id') ?: 1);
            $this->auditService->log($actorId, 'landing.highlight.deleted', 'landing', 'feature_highlights', (int) $id, ['deleted' => $item]);
            return redirect()->to('dashboard/landing/pilar-hierarki?section=' . urlencode($item['section_key']))
                ->with('success', 'Item berhasil dihapus.');
        }

        return redirect()->to('dashboard/landing/pilar-hierarki')->with('error', 'Item tidak ditemukan.');
    }

    public function toggle($id)
    {
        if ($res = $this->validateSafeMutation()) {
            return $res;
        }
        $item = $this->db->table('feature_highlights')->where('id', $id)->get()->getRowArray();
        if ($item) {
            $newStatus = $item['is_active'] ? 0 : 1;
            $this->db->table('feature_highlights')->where('id', $id)->update([
                'is_active'  => $newStatus,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            return redirect()->to('dashboard/landing/pilar-hierarki?section=' . urlencode($item['section_key']))
                ->with('success', 'Status item berhasil diubah.');
        }

        return redirect()->to('dashboard/landing/pilar-hierarki')->with('error', 'Item tidak ditemukan.');
    }
}
