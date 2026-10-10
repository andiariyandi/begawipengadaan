<?php

namespace App\Controllers\Knowledge;

use App\Models\Knowledge\ArticleModel;
use App\Models\Knowledge\KnowledgeCategoryModel;
use App\Models\Knowledge\TagModel;

class FaqController extends BaseKnowledgeController
{
    public function index(): string
    {
        $status   = $this->request->getGet('status') ?: null;
        $keyword  = $this->request->getGet('q') ?: null;
        $category = $this->request->getGet('category') ?: null;

        $items = (new ArticleModel())->listArticles('faq', $status, $keyword, $category !== null ? (int) $category : null);
        $db = db_connect();
        $stats = [
            'total'     => (int) $db->table('knowledge_articles')->where('type', 'faq')->countAllResults(),
            'published' => (int) $db->table('knowledge_articles')->where('type', 'faq')->where('workflow_status', 'published')->countAllResults(),
            'draft'     => (int) $db->table('knowledge_articles')->where('type', 'faq')->where('workflow_status', 'draft')->countAllResults(),
            'in_review' => (int) $db->table('knowledge_articles')->where('type', 'faq')->whereIn('workflow_status', ['in_review', 'submitted'])->countAllResults(),
        ];

        return view('dashboard/knowledge/faqs/index', [
            'title'         => 'FAQ | Dashboard BegawiPBJ',
            'pageTitle'     => 'Tanya Jawab (FAQ)',
            'activeMenu'    => 'knowledge',
            'activeSubmenu' => 'faqs',
            'items'         => $items,
            'stats'         => $stats,
            'categories'    => (new KnowledgeCategoryModel())->activeFlat(),
            'filterStatus'  => $status,
            'filterKeyword' => $keyword,
            'filterCategory'=> $category,
        ]);
    }

    public function create(): string
    {
        return view('dashboard/knowledge/faqs/form', [
            'title'        => 'Tambah FAQ | Dashboard BegawiPBJ',
            'pageTitle'    => 'Tambah FAQ',
            'formMode'     => 'create',
            'activeMenu'   => 'knowledge',
            'activeSubmenu'=> 'faqs',
            'faq'          => null,
            'categories'   => (new KnowledgeCategoryModel())->activeFlat(),
        ]);
    }

    public function store()
    {
        if ($forbid = $this->assertCanManage()) {
            return $forbid;
        }
        $rules = [
            'title'   => 'required|max_length[255]',
            'content' => 'required',
            'status'  => 'required|in_list[draft,submitted,in_review,approved,published,revision_required,rejected,archived]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Pertanyaan, jawaban, dan status wajib diisi.');
        }
        $model = new ArticleModel();
        $title = trim((string) $this->request->getPost('title'));
        $now = date('Y-m-d H:i:s');
        $status = (string) $this->request->getPost('status');
        $payload = [
            'type'             => 'faq',
            'title'            => $title,
            'slug'             => $model->generateUniqueSlug($title),
            'summary'          => '',
            'content'          => (string) $this->request->getPost('content'),
            'workflow_status'  => $status,
            'visibility'       => 'public',
            'author_id'        => $this->currentUserId(),
            'created_at'       => $now,
            'updated_at'       => $now,
        ];
        if ($status === 'published') {
            $payload['published_at'] = $now;
            $payload['published_by'] = $this->currentUserId();
        }
        $model->insert($payload);
        $faqId = (int) $model->getInsertID();
        $cats = (array) $this->request->getPost('categories');
        $cats = array_values(array_filter(array_map('intval', $cats), static fn ($v) => $v > 0));
        $model->syncCategories($faqId, $cats);
        return redirect()->to(route_to('dashboard.knowledge.faqs.detail', (string) $faqId))->with('success', 'FAQ berhasil ditambahkan.');
    }

    public function detail(string $id): string
    {
        $faq = (new ArticleModel())->detail((int) $id);
        if ($faq === null || ($faq['type'] ?? null) !== 'faq') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('FAQ tidak ditemukan.');
        }
        return view('dashboard/knowledge/faqs/detail', [
            'title'        => 'Detail FAQ | Dashboard BegawiPBJ',
            'pageTitle'    => 'Detail FAQ',
            'faqId'        => (int) $id,
            'faq'          => $faq,
            'activeMenu'   => 'knowledge',
            'activeSubmenu'=> 'faqs',
        ]);
    }

    public function edit(string $id): string
    {
        $faq = (new ArticleModel())->detail((int) $id);
        if ($faq === null || ($faq['type'] ?? null) !== 'faq') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('FAQ tidak ditemukan.');
        }
        return view('dashboard/knowledge/faqs/form', [
            'title'        => 'Ubah FAQ | Dashboard BegawiPBJ',
            'pageTitle'    => 'Ubah FAQ',
            'formMode'     => 'edit',
            'faqId'        => (int) $id,
            'faq'          => $faq,
            'categories'   => (new KnowledgeCategoryModel())->activeFlat(),
            'selectedCategories' => array_map(static fn ($c) => (int) $c['id'], $faq['categories'] ?? []),
            'activeMenu'   => 'knowledge',
            'activeSubmenu'=> 'faqs',
        ]);
    }

    public function update(string $id)
    {
        if ($forbid = $this->assertCanManage()) {
            return $forbid;
        }
        $model = new ArticleModel();
        $faq = $model->detail((int) $id);
        if ($faq === null || ($faq['type'] ?? null) !== 'faq') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('FAQ tidak ditemukan.');
        }
        $rules = [
            'title'   => 'required|max_length[255]',
            'content' => 'required',
            'status'  => 'required|in_list[draft,submitted,in_review,approved,published,revision_required,rejected,archived]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Periksa kembali isian formulir.');
        }
        $now = date('Y-m-d H:i:s');
        $title = trim((string) $this->request->getPost('title'));
        $status = (string) $this->request->getPost('status');
        $payload = [
            'title'            => $title,
            'slug'             => $model->generateUniqueSlug($title, (int) $id),
            'content'          => (string) $this->request->getPost('content'),
            'workflow_status'  => $status,
            'updated_at'       => $now,
        ];
        if ($status === 'published' && empty($faq['published_at'])) {
            $payload['published_at'] = $now;
            $payload['published_by'] = $this->currentUserId();
        }
        $model->update((int) $id, $payload);
        $cats = (array) $this->request->getPost('categories');
        $cats = array_values(array_filter(array_map('intval', $cats), static fn ($v) => $v > 0));
        $model->syncCategories((int) $id, $cats);
        return redirect()->to(route_to('dashboard.knowledge.faqs.detail', $id))->with('success', 'FAQ berhasil diperbarui.');
    }

    public function delete(string $id)
    {
        if ($forbid = $this->assertCanManage()) {
            return $forbid;
        }
        $model = new ArticleModel();
        $faq = $model->find((int) $id);
        if ($faq === null || $faq['type'] !== 'faq') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('FAQ tidak ditemukan.');
        }
        $model->delete((int) $id);
        return redirect()->to(route_to('dashboard.knowledge.faqs'))->with('success', 'FAQ diarsipkan.');
    }
}