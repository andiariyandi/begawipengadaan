<?php

namespace App\Controllers\Knowledge;

use App\Controllers\BaseController;
use App\Models\CaseStudyModel;
use App\Models\CategoryModel;
use App\Services\AuditService;

class CaseStudyController extends BaseController
{
    /**
     * Case study tidak punya status draft/published di skema (beda dari
     * articles) — begitu dibuat langsung tampil untuk pembaca publik.
     * Kalau nanti perlu alur review sebelum tayang, tambah kolom status
     * seperti di articles. Kontennya dapat dibaca publik.
     */
    public function browse()
    {
        $categoryId = $this->request->getGet('category_id') ?: null;
        $keyword    = $this->request->getGet('q') ?: null;

        return view('knowledge/case_studies/browse', [
            'title'      => 'Studi Kasus PBJ',
            'subTitle'   => 'Studi Kasus PBJ',
            'caseStudies' => (new CaseStudyModel())->browse($categoryId ? (int) $categoryId : null, $keyword),
            'categories' => (new CategoryModel())->kmsCategories(),
            'keyword'    => $keyword,
            'categoryId' => $categoryId,
        ]);
    }

    public function detail(int $id)
    {
        $caseStudy = (new CaseStudyModel())->withDetail($id);

        if (! $caseStudy) {
            return redirect()->to(route_to('knowledge.caseStudies.browse'))->with('error', 'Studi kasus tidak ditemukan.');
        }

        return view('knowledge/case_studies/detail', [
            'title'     => $caseStudy['title'],
            'subTitle'  => 'Studi Kasus PBJ',
            'caseStudy' => $caseStudy,
        ]);
    }

    public function create()
    {
        return view('knowledge/case_studies/form', [
            'title'      => 'Tambah Studi Kasus',
            'subTitle'   => 'Tambah Studi Kasus',
            'categories' => (new CategoryModel())->kmsCategories(),
        ]);
    }

    public function store()
    {
        $rules = [
            'title'       => 'required|max_length[200]',
            'category_id' => 'required|is_natural_no_zero',
            'content'     => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Judul, kategori, dan isi wajib diisi.');
        }

        $caseStudyModel = new CaseStudyModel();

        $id = $caseStudyModel->insert([
            'category_id' => $this->request->getPost('category_id'),
            'title'       => $this->request->getPost('title'),
            'content'     => $this->request->getPost('content'),
            'author_id'   => session()->get('user_id'),
        ], true);

        (new AuditService())->log(session()->get('user_id'), 'create', 'kms', 'case_studies', $id);

        return redirect()->to(route_to('knowledge.caseStudies.browse'))->with('message', 'Studi kasus berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $caseStudy = (new CaseStudyModel())->find($id);

        if (! $caseStudy) {
            return redirect()->to(route_to('knowledge.caseStudies.browse'))->with('error', 'Studi kasus tidak ditemukan.');
        }

        return view('knowledge/case_studies/form', [
            'title'      => 'Edit Studi Kasus',
            'subTitle'   => 'Edit Studi Kasus',
            'caseStudy'  => $caseStudy,
            'categories' => (new CategoryModel())->kmsCategories(),
        ]);
    }

    public function update(int $id)
    {
        $rules = [
            'title'       => 'required|max_length[200]',
            'category_id' => 'required|is_natural_no_zero',
            'content'     => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Judul, kategori, dan isi wajib diisi.');
        }

        $caseStudyModel = new CaseStudyModel();

        if (! $caseStudyModel->find($id)) {
            return redirect()->to(route_to('knowledge.caseStudies.browse'))->with('error', 'Studi kasus tidak ditemukan.');
        }

        $caseStudyModel->update($id, [
            'category_id' => $this->request->getPost('category_id'),
            'title'       => $this->request->getPost('title'),
            'content'     => $this->request->getPost('content'),
        ]);

        (new AuditService())->log(session()->get('user_id'), 'update', 'kms', 'case_studies', $id);

        return redirect()->to(route_to('knowledge.caseStudies.browse'))->with('message', 'Studi kasus berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $caseStudyModel = new CaseStudyModel();

        if (! $caseStudyModel->find($id)) {
            return redirect()->to(route_to('knowledge.caseStudies.browse'))->with('error', 'Studi kasus tidak ditemukan.');
        }

        $caseStudyModel->delete($id);

        (new AuditService())->log(session()->get('user_id'), 'delete', 'kms', 'case_studies', $id);

        return redirect()->to(route_to('knowledge.caseStudies.browse'))->with('message', 'Studi kasus berhasil dihapus.');
    }
}
