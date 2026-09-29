<?php

namespace App\Controllers\Knowledge;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\FaqModel;
use App\Services\AuditService;

class FaqController extends BaseController
{
    /**
     * Satu halaman untuk kelola (Admin/Trainer, lewat form tambah) sekaligus
     * tampil publik (termasuk pembaca anonim) — FAQ tidak punya status
     * draft/published seperti artikel, jadi tidak perlu dipisah dua controller.
     */
    public function index()
    {
        $categoryId = $this->request->getGet('category_id') ?: null;
        $keyword    = $this->request->getGet('q') ?: null;

        return view('knowledge/faq/index', [
            'title'      => 'FAQ PBJ',
            'subTitle'   => 'FAQ PBJ',
            'faqs'       => (new FaqModel())->browse($categoryId ? (int) $categoryId : null, $keyword),
            'categories' => (new CategoryModel())->kmsCategories(),
            'keyword'    => $keyword,
            'categoryId' => $categoryId,
        ]);
    }

    public function store()
    {
        $rules = [
            'category_id' => 'required|is_natural_no_zero',
            'question'    => 'required|max_length[255]',
            'answer'      => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Kategori, pertanyaan, dan jawaban wajib diisi.');
        }

        $faqModel = new FaqModel();

        $faqId = $faqModel->insert([
            'category_id' => $this->request->getPost('category_id'),
            'question'    => $this->request->getPost('question'),
            'answer'      => $this->request->getPost('answer'),
        ], true);

        (new AuditService())->log(session()->get('user_id'), 'create', 'kms', 'faq', $faqId);

        return redirect()->to(route_to('knowledge.faq'))->with('message', 'FAQ berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $faq = (new FaqModel())->find($id);

        if (! $faq) {
            return redirect()->to(route_to('knowledge.faq'))->with('error', 'FAQ tidak ditemukan.');
        }

        return view('knowledge/faq/form', [
            'title'      => 'Edit FAQ',
            'subTitle'   => 'Edit FAQ',
            'faq'        => $faq,
            'categories' => (new CategoryModel())->kmsCategories(),
        ]);
    }

    public function update(int $id)
    {
        $rules = [
            'category_id' => 'required|is_natural_no_zero',
            'question'    => 'required|max_length[255]',
            'answer'      => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Kategori, pertanyaan, dan jawaban wajib diisi.');
        }

        $faqModel = new FaqModel();

        if (! $faqModel->find($id)) {
            return redirect()->to(route_to('knowledge.faq'))->with('error', 'FAQ tidak ditemukan.');
        }

        $faqModel->update($id, [
            'category_id' => $this->request->getPost('category_id'),
            'question'    => $this->request->getPost('question'),
            'answer'      => $this->request->getPost('answer'),
        ]);

        (new AuditService())->log(session()->get('user_id'), 'update', 'kms', 'faq', $id);

        return redirect()->to(route_to('knowledge.faq'))->with('message', 'FAQ berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $faqModel = new FaqModel();

        if (! $faqModel->find($id)) {
            return redirect()->to(route_to('knowledge.faq'))->with('error', 'FAQ tidak ditemukan.');
        }

        $faqModel->delete($id);

        (new AuditService())->log(session()->get('user_id'), 'delete', 'kms', 'faq', $id);

        return redirect()->to(route_to('knowledge.faq'))->with('message', 'FAQ berhasil dihapus.');
    }
}
