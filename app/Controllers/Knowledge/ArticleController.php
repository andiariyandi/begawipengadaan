<?php

namespace App\Controllers\Knowledge;

use App\Models\Knowledge\ArticleModel;
use App\Models\Knowledge\KnowledgeCategoryModel;
use App\Models\Knowledge\TagModel;

class ArticleController extends BaseKnowledgeController
{
    public function index(): string
    {
        $status   = $this->request->getGet('status') ?: null;
        $keyword  = $this->request->getGet('q') ?: null;
        $category = $this->request->getGet('category') ?: null;

        $model = new ArticleModel();
        $items = $model->listArticles('article', $status, $keyword, $category !== null ? (int) $category : null);

        $db = db_connect();
        $stats = [
            'total'     => (int) $db->table('knowledge_articles')->where('type', 'article')->countAllResults(),
            'published' => (int) $db->table('knowledge_articles')->where('type', 'article')->where('workflow_status', 'published')->countAllResults(),
            'draft'     => (int) $db->table('knowledge_articles')->where('type', 'article')->where('workflow_status', 'draft')->countAllResults(),
            'in_review' => (int) $db->table('knowledge_articles')->where('type', 'article')->whereIn('workflow_status', ['in_review', 'submitted'])->countAllResults(),
        ];

        return view('dashboard/knowledge/articles/index', [
            'title'        => 'Artikel | Dashboard BegawiPBJ',
            'pageTitle'    => 'Artikel dan Praktik Baik',
            'activeMenu'   => 'knowledge',
            'activeSubmenu'=> 'articles',
            'items'        => $items,
            'stats'        => $stats,
            'categories'   => (new KnowledgeCategoryModel())->activeFlat(),
            'filterStatus' => $status,
            'filterKeyword'=> $keyword,
            'filterCategory'=> $category,
        ]);
    }

    public function create(): string
    {
        return view('dashboard/knowledge/articles/form', [
            'title'        => 'Tambah Artikel | Dashboard BegawiPBJ',
            'pageTitle'    => 'Tambah Artikel',
            'formMode'     => 'create',
            'activeMenu'   => 'knowledge',
            'activeSubmenu'=> 'articles',
            'article'      => null,
            'categories'   => (new KnowledgeCategoryModel())->activeFlat(),
            'tags'         => (new TagModel())->listAll(),
            'selectedCategories' => [],
            'selectedTags'        => [],
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
        $now = date('Y-m-d H:i:s');
        $title = trim((string) $this->request->getPost('title'));
        $status = (string) $this->request->getPost('status');
        $payload = [
            'type'             => 'article',
            'title'            => $title,
            'slug'             => $model->generateUniqueSlug($title),
            'summary'          => (string) $this->request->getPost('summary'),
            'content'          => (string) $this->request->getPost('content'),
            'workflow_status'  => $status,
            'visibility'       => (string) ($this->request->getPost('visibility') ?: 'internal'),
            'featured'         => $this->request->getPost('featured') ? 1 : 0,
            'carousel_enabled' => $this->request->getPost('carousel_enabled') ? 1 : 0,
            'carousel_order'   => $this->request->getPost('carousel_order') !== null && $this->request->getPost('carousel_order') !== ''
                ? (int) $this->request->getPost('carousel_order') : null,
            'reading_time'     => (int) ($this->request->getPost('reading_time') ?: 5),
            'source_url'       => trim((string) $this->request->getPost('source_url')) ?: null,
            'author_id'        => $this->currentUserId(),
            'created_at'       => $now,
            'updated_at'       => $now,
        ];
        if ($status === 'published') {
            $payload['published_at'] = $now;
            $payload['published_by'] = $this->currentUserId();
        }
        if ($status === 'archived') {
            $payload['archived_at'] = $now;
        }
        $file = $this->request->getFile('cover');
        $fileId = $this->uploadFileAsset($file, 'article_cover', 'public');
        if ($fileId !== null) {
            $payload['cover_file_id'] = $fileId;
        }
        $model->insert($payload);
        $articleId = (int) $model->getInsertID();
        $this->syncRelations($model, $articleId);
        return redirect()->to(route_to('dashboard.knowledge.articles'))->with('success', 'Artikel berhasil ditambahkan.');
    }

    public function detail(string $id): string
    {
        $article = (new ArticleModel())->detail((int) $id);
        if ($article === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Artikel tidak ditemukan.');
        }
        return view('dashboard/knowledge/articles/detail', [
            'title'        => 'Detail Artikel | Dashboard BegawiPBJ',
            'pageTitle'    => 'Detail Artikel',
            'articleId'    => (int) $id,
            'article'      => $article,
            'activeMenu'   => 'knowledge',
            'activeSubmenu'=> 'articles',
        ]);
    }

    public function edit(string $id): string
    {
        $article = (new ArticleModel())->detail((int) $id);
        if ($article === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Artikel tidak ditemukan.');
        }
        return view('dashboard/knowledge/articles/form', [
            'title'        => 'Ubah Artikel | Dashboard BegawiPBJ',
            'pageTitle'    => 'Ubah Artikel',
            'formMode'     => 'edit',
            'articleId'    => (int) $id,
            'article'      => $article,
            'categories'   => (new KnowledgeCategoryModel())->activeFlat(),
            'tags'         => (new TagModel())->listAll(),
            'selectedCategories' => array_map(static fn ($c) => (int) $c['id'], $article['categories'] ?? []),
            'selectedTags'        => array_map(static fn ($t) => (string) $t['name'], $article['tags'] ?? []),
            'activeMenu'   => 'knowledge',
            'activeSubmenu'=> 'articles',
        ]);
    }

    public function update(string $id)
    {
        if ($forbid = $this->assertCanManage()) {
            return $forbid;
        }
        $model = new ArticleModel();
        $article = $model->detail((int) $id);
        if ($article === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Artikel tidak ditemukan.');
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
            'title'            => $title,
            'slug'             => $model->generateUniqueSlug($title, (int) $id),
            'summary'          => (string) $this->request->getPost('summary'),
            'content'          => (string) $this->request->getPost('content'),
            'workflow_status'  => $status,
            'visibility'       => (string) ($this->request->getPost('visibility') ?: 'internal'),
            'featured'         => $this->request->getPost('featured') ? 1 : 0,
            'carousel_enabled' => $this->request->getPost('carousel_enabled') ? 1 : 0,
            'carousel_order'   => $this->request->getPost('carousel_order') !== null && $this->request->getPost('carousel_order') !== ''
                ? (int) $this->request->getPost('carousel_order') : null,
            'reading_time'     => (int) ($this->request->getPost('reading_time') ?: 5),
            'source_url'       => trim((string) $this->request->getPost('source_url')) ?: null,
            'updated_at'       => $now,
        ];
        if ($status === 'published' && empty($article['published_at'])) {
            $payload['published_at'] = $now;
            $payload['published_by'] = $this->currentUserId();
        }
        if ($status === 'archived' && empty($article['archived_at'])) {
            $payload['archived_at'] = $now;
        }
        $file = $this->request->getFile('cover');
        $fileId = $this->uploadFileAsset($file, 'article_cover', 'public');
        if ($fileId !== null) {
            $payload['cover_file_id'] = $fileId;
        }
        $model->update((int) $id, $payload);
        $this->syncRelations($model, (int) $id);
        return redirect()->to(route_to('dashboard.knowledge.articles.detail', $id))->with('success', 'Artikel berhasil diperbarui.');
    }

    public function delete(string $id)
    {
        if ($forbid = $this->assertCanManage()) {
            return $forbid;
        }
        $model = new ArticleModel();
        $article = $model->find((int) $id);
        if ($article === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Artikel tidak ditemukan.');
        }
        $model->delete((int) $id);
        return redirect()->to(route_to('dashboard.knowledge.articles'))->with('success', 'Artikel diarsipkan.');
    }

    private function syncRelations(ArticleModel $model, int $articleId): void
    {
        $categories = (array) $this->request->getPost('categories');
        $categories = array_values(array_filter(array_map('intval', $categories), static fn ($v) => $v > 0));
        $model->syncCategories($articleId, $categories);

        $tagsRaw = (string) $this->request->getPost('tags');
        $tagNames = array_values(array_filter(array_map('trim', preg_split('/[,\n]+/', $tagsRaw) ?: [])));
        $model->syncTags($articleId, $tagNames, new TagModel());
    }
}