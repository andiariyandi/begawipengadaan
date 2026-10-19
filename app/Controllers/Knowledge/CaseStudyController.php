<?php

namespace App\Controllers\Knowledge;

use App\Models\Knowledge\ArticleModel;
use App\Models\Knowledge\KnowledgeCategoryModel;

class CaseStudyController extends BaseKnowledgeController
{
    public function index(): string
    {
        $status   = $this->request->getGet('status') ?: null;
        $keyword  = $this->request->getGet('q') ?: null;
        $category = $this->request->getGet('category') ?: null;

        $items = (new ArticleModel())->listArticles('case_study', $status, $keyword, $category !== null ? (int) $category : null);
        $db = db_connect();
        $stats = [
            'total'     => (int) $db->table('knowledge_articles')->where('type', 'case_study')->countAllResults(),
            'published' => (int) $db->table('knowledge_articles')->where('type', 'case_study')->where('workflow_status', 'published')->countAllResults(),
            'draft'     => (int) $db->table('knowledge_articles')->where('type', 'case_study')->where('workflow_status', 'draft')->countAllResults(),
            'in_review' => (int) $db->table('knowledge_articles')->where('type', 'case_study')->whereIn('workflow_status', ['in_review', 'submitted'])->countAllResults(),
        ];

        return view('dashboard/knowledge/case_studies/index', [
            'title'         => 'Studi Kasus | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Studi Kasus PBJ',
            'activeMenu'    => 'knowledge',
            'activeSubmenu' => 'case-studies',
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
        return view('dashboard/knowledge/case_studies/form', [
            'title'        => 'Tambah Studi Kasus | Dashboard BegawiPengadaan',
            'pageTitle'    => 'Tambah Studi Kasus',
            'formMode'     => 'create',
            'activeMenu'   => 'knowledge',
            'activeSubmenu' => 'case-studies',
            'caseStudy'    => null,
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
            return redirect()->back()->withInput()->with('error', 'Periksa kembali isian formulir.');
        }
        $model = new ArticleModel();
        $title = trim((string) $this->request->getPost('title'));
        $now = date('Y-m-d H:i:s');
        $status = (string) $this->request->getPost('status');
        $payload = [
            'type'             => 'case_study',
            'title'            => $title,
            'slug'             => $model->generateUniqueSlug($title),
            'summary'          => (string) $this->request->getPost('summary'),
            'content'          => (string) $this->request->getPost('content'),
            'workflow_status'  => $status,
            'reading_time'       => (int) ($this->request->getPost('reading_time') ?: 5),
            'organization_unit'  => trim((string) $this->request->getPost('organization_unit')) ?: null,
            'procurement_stage'  => trim((string) $this->request->getPost('procurement_stage')) ?: null,
            'case_severity'      => trim((string) $this->request->getPost('case_severity')) ?: 'medium',
            'resolution_summary' => (string) $this->request->getPost('resolution_summary'),
            'featured'           => $this->request->getPost('featured') ? 1 : 0,
            'author_id'          => $this->currentUserId(),
            'created_at'         => $now,
            'updated_at'         => $now,
        ];
        if ($status === 'published') {
            $payload['published_at'] = $now;
            $payload['published_by'] = $this->currentUserId();
        }
        $file = $this->request->getFile('cover');
        $fileId = $this->uploadFileAsset($file, 'case_study_cover', 'public');
        if ($fileId !== null) {
            $payload['cover_file_id'] = $fileId;
            $payload['cover_url'] = site_url('file/' . $fileId);
        }
        $model->insert($payload);
        $id = (int) $model->getInsertID();
        $cats = array_values(array_filter(array_map('intval', (array) $this->request->getPost('categories')), static fn($v) => $v > 0));
        $model->syncCategories($id, $cats);
        return redirect()->to(route_to('dashboard.knowledge.case-studies.detail', (string) $id))->with('success', 'Studi kasus berhasil ditambahkan.');
    }

    public function detail(string $id): string
    {
        $row = (new ArticleModel())->detail((int) $id);
        if ($row === null || ($row['type'] ?? null) !== 'case_study') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Studi kasus tidak ditemukan.');
        }
        return view('dashboard/knowledge/case_studies/detail', [
            'title'        => 'Detail Studi Kasus | Dashboard BegawiPengadaan',
            'pageTitle'    => 'Detail Studi Kasus',
            'caseStudyId'  => (int) $id,
            'caseStudy'    => $row,
            'activeMenu'   => 'knowledge',
            'activeSubmenu' => 'case-studies',
        ]);
    }

    public function edit(string $id): string
    {
        $row = (new ArticleModel())->detail((int) $id);
        if ($row === null || ($row['type'] ?? null) !== 'case_study') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Studi kasus tidak ditemukan.');
        }
        return view('dashboard/knowledge/case_studies/form', [
            'title'        => 'Ubah Studi Kasus | Dashboard BegawiPengadaan',
            'pageTitle'    => 'Ubah Studi Kasus',
            'formMode'     => 'edit',
            'caseStudyId'  => (int) $id,
            'caseStudy'    => $row,
            'categories'   => (new KnowledgeCategoryModel())->activeFlat(),
            'selectedCategories' => array_map(static fn($c) => (int) $c['id'], $row['categories'] ?? []),
            'activeMenu'   => 'knowledge',
            'activeSubmenu' => 'case-studies',
        ]);
    }

    public function update(string $id)
    {
        if ($forbid = $this->assertCanManage()) {
            return $forbid;
        }
        $model = new ArticleModel();
        $row = $model->detail((int) $id);
        if ($row === null || ($row['type'] ?? null) !== 'case_study') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Studi kasus tidak ditemukan.');
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
            'title'           => $title,
            'slug'            => $model->generateUniqueSlug($title, (int) $id),
            'summary'         => (string) $this->request->getPost('summary'),
            'content'         => (string) $this->request->getPost('content'),
            'workflow_status'    => $status,
            'visibility'         => (string) ($this->request->getPost('visibility') ?: 'public'),
            'reading_time'       => (int) ($this->request->getPost('reading_time') ?: 5),
            'organization_unit'  => trim((string) $this->request->getPost('organization_unit')) ?: null,
            'procurement_stage'  => trim((string) $this->request->getPost('procurement_stage')) ?: null,
            'case_severity'      => trim((string) $this->request->getPost('case_severity')) ?: 'medium',
            'resolution_summary' => (string) $this->request->getPost('resolution_summary'),
            'featured'           => $this->request->getPost('featured') ? 1 : 0,
            'updated_at'         => $now,
        ];
        if ($status === 'published' && empty($row['published_at'])) {
            $payload['published_at'] = $now;
            $payload['published_by'] = $this->currentUserId();
        }
        $file = $this->request->getFile('cover');
        $fileId = $this->uploadFileAsset($file, 'case_study_cover', 'public');
        if ($fileId !== null) {
            $payload['cover_file_id'] = $fileId;
            $payload['cover_url'] = site_url('file/' . $fileId);
        }
        $model->update((int) $id, $payload);
        $cats = array_values(array_filter(array_map('intval', (array) $this->request->getPost('categories')), static fn($v) => $v > 0));
        $model->syncCategories((int) $id, $cats);
        return redirect()->to(route_to('dashboard.knowledge.case-studies.detail', $id))->with('success', 'Studi kasus berhasil diperbarui.');
    }

    public function delete(string $id)
    {
        if ($forbid = $this->assertCanManage()) {
            return $forbid;
        }
        $model = new ArticleModel();
        $row = $model->find((int) $id);
        if ($row === null || $row['type'] !== 'case_study') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Studi kasus tidak ditemukan.');
        }
        $model->delete((int) $id);
        return redirect()->to(route_to('dashboard.knowledge.case-studies'))->with('success', 'Studi kasus diarsipkan.');
    }
}
