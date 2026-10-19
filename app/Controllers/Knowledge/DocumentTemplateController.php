<?php

namespace App\Controllers\Knowledge;

use App\Models\Knowledge\ArticleModel;
use App\Models\Knowledge\KnowledgeCategoryModel;

class DocumentTemplateController extends BaseKnowledgeController
{
    public function index(): string
    {
        $status   = $this->request->getGet('status') ?: null;
        $keyword  = $this->request->getGet('q') ?: null;
        $category = $this->request->getGet('category') ?: null;

        $items = (new ArticleModel())->listArticles('template', $status, $keyword, $category !== null ? (int) $category : null);
        $db = db_connect();
        $stats = [
            'total'     => (int) $db->table('knowledge_articles')->where('type', 'template')->countAllResults(),
            'published' => (int) $db->table('knowledge_articles')->where('type', 'template')->where('workflow_status', 'published')->countAllResults(),
            'draft'     => (int) $db->table('knowledge_articles')->where('type', 'template')->where('workflow_status', 'draft')->countAllResults(),
            'in_review' => (int) $db->table('knowledge_articles')->where('type', 'template')->whereIn('workflow_status', ['in_review', 'submitted'])->countAllResults(),
        ];

        return view('dashboard/knowledge/templates/index', [
            'title'         => 'Template Dokumen | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Template Dokumen PBJ',
            'activeMenu'    => 'knowledge',
            'activeSubmenu' => 'document-templates',
            'items'         => $items,
            'stats'         => $stats,
            'categories'    => (new KnowledgeCategoryModel())->activeFlat(),
            'filterStatus'  => $status,
            'filterKeyword' => $keyword,
            'filterCategory' => $category,
        ]);
    }

    public function create(): string
    {
        return view('dashboard/knowledge/templates/form', [
            'title'        => 'Tambah Template Dokumen | Dashboard BegawiPengadaan',
            'pageTitle'    => 'Tambah Template Dokumen',
            'formMode'     => 'create',
            'activeMenu'   => 'knowledge',
            'activeSubmenu' => 'document-templates',
            'template'     => null,
            'categories'   => (new KnowledgeCategoryModel())->activeFlat(),
        ]);
    }

    public function store()
    {
        if ($forbid = $this->assertCanManage()) {
            return $forbid;
        }
        $rules = [
            'title'  => 'required|max_length[255]',
            'status' => 'required|in_list[draft,submitted,in_review,approved,published,revision_required,rejected,archived]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Nama template dan status wajib diisi.');
        }
        $model = new ArticleModel();
        $title = trim((string) $this->request->getPost('title'));
        $now = date('Y-m-d H:i:s');
        $status = (string) $this->request->getPost('status');
        $payload = [
            'type'             => 'template',
            'title'            => $title,
            'slug'             => $model->generateUniqueSlug($title),
            'summary'           => (string) $this->request->getPost('summary'),
            'content'           => (string) $this->request->getPost('content'),
            'document_category' => trim((string) $this->request->getPost('document_category')) ?: 'kak',
            'document_version'  => trim((string) $this->request->getPost('document_version')) ?: 'v1.0',
            'workflow_status'   => $status,
            'visibility'        => 'public',
            'featured'          => $this->request->getPost('featured') ? 1 : 0,
            'carousel_enabled'  => $this->request->getPost('carousel_enabled') ? 1 : 0,
            'author_id'         => $this->currentUserId(),
            'created_at'        => $now,
            'updated_at'        => $now,
        ];
        if ($status === 'published') {
            $payload['published_at'] = $now;
            $payload['published_by'] = $this->currentUserId();
        }

        // 1. Cover Image Upload
        $coverFile = $this->request->getFile('cover');
        $coverId = $this->uploadFileAsset($coverFile, 'template_cover', 'public');
        if ($coverId !== null) {
            $payload['cover_file_id'] = $coverId;
            $payload['cover_url'] = site_url('file/' . $coverId);
        }

        // 2. Downloadable Document File
        $file = $this->request->getFile('template_file');
        $fileId = $this->uploadFileAsset($file, 'template_file', 'public');
        if ($fileId !== null) {
            $payload['attachment_file_id'] = $fileId;
            if (empty($payload['cover_file_id'])) {
                $payload['cover_file_id'] = $fileId;
            }
        }
        $model->insert($payload);
        $id = (int) $model->getInsertID();
        $cats = array_values(array_filter(array_map('intval', (array) $this->request->getPost('categories')), static fn($v) => $v > 0));
        $model->syncCategories($id, $cats);
        return redirect()->to(route_to('dashboard.knowledge.document-templates.detail', (string) $id))->with('success', 'Template berhasil ditambahkan.');
    }

    public function detail(string $id): string
    {
        $row = (new ArticleModel())->detail((int) $id);
        if ($row === null || ($row['type'] ?? null) !== 'template') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Template tidak ditemukan.');
        }
        $fileMeta = null;
        if (! empty($row['cover_file_id'])) {
            $fileMeta = $this->db->table('file_assets')->where('id', (int) $row['cover_file_id'])->get()->getRowArray();
        }
        return view('dashboard/knowledge/templates/detail', [
            'title'        => 'Detail Template Dokumen | Dashboard BegawiPengadaan',
            'pageTitle'    => 'Detail Template Dokumen',
            'templateId'   => (int) $id,
            'template'     => $row,
            'fileMeta'     => $fileMeta,
            'activeMenu'   => 'knowledge',
            'activeSubmenu' => 'document-templates',
        ]);
    }

    public function edit(string $id): string
    {
        $row = (new ArticleModel())->detail((int) $id);
        if ($row === null || ($row['type'] ?? null) !== 'template') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Template tidak ditemukan.');
        }
        return view('dashboard/knowledge/templates/form', [
            'title'        => 'Ubah Template Dokumen | Dashboard BegawiPengadaan',
            'pageTitle'    => 'Ubah Template Dokumen',
            'formMode'     => 'edit',
            'templateId'   => (int) $id,
            'template'     => $row,
            'categories'   => (new KnowledgeCategoryModel())->activeFlat(),
            'selectedCategories' => array_map(static fn($c) => (int) $c['id'], $row['categories'] ?? []),
            'activeMenu'   => 'knowledge',
            'activeSubmenu' => 'document-templates',
        ]);
    }

    public function update(string $id)
    {
        if ($forbid = $this->assertCanManage()) {
            return $forbid;
        }
        $model = new ArticleModel();
        $row = $model->detail((int) $id);
        if ($row === null || ($row['type'] ?? null) !== 'template') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Template tidak ditemukan.');
        }
        $rules = [
            'title'  => 'required|max_length[255]',
            'status' => 'required|in_list[draft,submitted,in_review,approved,published,revision_required,rejected,archived]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Periksa kembali isian formulir.');
        }
        $now = date('Y-m-d H:i:s');
        $title = trim((string) $this->request->getPost('title'));
        $status = (string) $this->request->getPost('status');
        $payload = [
            'title'             => $title,
            'slug'              => $model->generateUniqueSlug($title, (int) $id),
            'summary'           => (string) $this->request->getPost('summary'),
            'content'           => (string) $this->request->getPost('content'),
            'document_category' => trim((string) $this->request->getPost('document_category')) ?: 'kak',
            'document_version'  => trim((string) $this->request->getPost('document_version')) ?: 'v1.0',
            'workflow_status'   => $status,
            'featured'          => $this->request->getPost('featured') ? 1 : 0,
            'carousel_enabled'  => $this->request->getPost('carousel_enabled') ? 1 : 0,
            'updated_at'        => $now,
        ];
        if ($status === 'published' && empty($row['published_at'])) {
            $payload['published_at'] = $now;
            $payload['published_by'] = $this->currentUserId();
        }

        // 1. Cover Image Upload
        $coverFile = $this->request->getFile('cover');
        $coverId = $this->uploadFileAsset($coverFile, 'template_cover', 'public');
        if ($coverId !== null) {
            $payload['cover_file_id'] = $coverId;
            $payload['cover_url'] = site_url('file/' . $coverId);
        }

        // 2. Downloadable Document File
        $file = $this->request->getFile('template_file');
        $fileId = $this->uploadFileAsset($file, 'template_file', 'public');
        if ($fileId !== null) {
            $payload['attachment_file_id'] = $fileId;
            if (empty($payload['cover_file_id']) && empty($row['cover_file_id'])) {
                $payload['cover_file_id'] = $fileId;
            }
        }
        $model->update((int) $id, $payload);
        $cats = array_values(array_filter(array_map('intval', (array) $this->request->getPost('categories')), static fn($v) => $v > 0));
        $model->syncCategories((int) $id, $cats);
        return redirect()->to(route_to('dashboard.knowledge.document-templates.detail', $id))->with('success', 'Template berhasil diperbarui.');
    }

    public function delete(string $id)
    {
        if ($forbid = $this->assertCanManage()) {
            return $forbid;
        }
        $model = new ArticleModel();
        $row = $model->find((int) $id);
        if ($row === null || $row['type'] !== 'template') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Template tidak ditemukan.');
        }
        $model->delete((int) $id);
        return redirect()->to(route_to('dashboard.knowledge.document-templates'))->with('success', 'Template diarsipkan.');
    }
}
