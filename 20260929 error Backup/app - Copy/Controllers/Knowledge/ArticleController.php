<?php

namespace App\Controllers\Knowledge;

use App\Controllers\BaseController;
use App\Models\ArticleBookmarkModel;
use App\Models\ArticleModel;
use App\Models\CategoryModel;
use App\Services\AuditService;

class ArticleController extends BaseController
{
    /**
     * Kelola artikel (Admin/Trainer) — semua status termasuk draft.
     */
    public function index()
    {
        $keyword = trim((string) $this->request->getGet('q'));
        $status  = trim((string) $this->request->getGet('status'));
        $model   = new ArticleModel();

        if ($keyword !== '') {
            $model->groupStart()
                ->like('articles.title', $keyword)
                ->orLike('articles.content', $keyword)
                ->groupEnd();
        }

        if (in_array($status, ['draft', 'published'], true)) {
            $model->where('articles.status', $status);
        } else {
            $status = '';
        }

        return view('knowledge/articles/manage', [
            'title'    => 'Kelola Artikel',
            'subTitle' => 'Kelola Artikel',
            'articles' => $model->forAdmin(),
            'keyword'  => $keyword,
            'status'   => $status,
        ]);
    }

    public function create()
    {
        return view('knowledge/articles/form', [
            'title'      => 'Tambah Artikel',
            'subTitle'   => 'Tambah Artikel',
            'categories' => (new CategoryModel())->kmsCategories(),
        ]);
    }

    public function store()
    {
        $rules = [
            'title'       => 'required|max_length[200]',
            'category_id' => 'required|is_natural_no_zero',
            'content'     => 'required',
            'status'      => 'required|in_list[draft,published]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Judul, kategori, dan isi artikel wajib diisi.');
        }

        $articleModel = new ArticleModel();

        $articleId = $articleModel->insert([
            'category_id' => $this->request->getPost('category_id'),
            'title'       => $this->request->getPost('title'),
            'content'     => $this->request->getPost('content'),
            'author_id'   => session()->get('user_id'),
            'status'      => $this->request->getPost('status'),
        ], true);

        (new AuditService())->log(session()->get('user_id'), 'create', 'kms', 'articles', $articleId);

        return redirect()->to(route_to('knowledge.articles.manage'))->with('message', 'Artikel berhasil disimpan.');
    }

    public function edit(int $id)
    {
        $article = (new ArticleModel())->find($id);

        if (! $article) {
            return redirect()->to(route_to('knowledge.articles.manage'))->with('error', 'Artikel tidak ditemukan.');
        }

        return view('knowledge/articles/form', [
            'title'      => 'Edit Artikel',
            'subTitle'   => 'Edit Artikel',
            'article'    => $article,
            'categories' => (new CategoryModel())->kmsCategories(),
        ]);
    }

    public function update(int $id)
    {
        $rules = [
            'title'       => 'required|max_length[200]',
            'category_id' => 'required|is_natural_no_zero',
            'content'     => 'required',
            'status'      => 'required|in_list[draft,published]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Judul, kategori, dan isi artikel wajib diisi.');
        }

        $articleModel = new ArticleModel();

        if (! $articleModel->find($id)) {
            return redirect()->to(route_to('knowledge.articles.manage'))->with('error', 'Artikel tidak ditemukan.');
        }

        $articleModel->update($id, [
            'category_id' => $this->request->getPost('category_id'),
            'title'       => $this->request->getPost('title'),
            'content'     => $this->request->getPost('content'),
            'status'      => $this->request->getPost('status'),
        ]);

        (new AuditService())->log(session()->get('user_id'), 'update', 'kms', 'articles', $id);

        return redirect()->to(route_to('knowledge.articles.manage'))->with('message', 'Artikel berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $articleModel = new ArticleModel();

        if (! $articleModel->find($id)) {
            return redirect()->to(route_to('knowledge.articles.manage'))->with('error', 'Artikel tidak ditemukan.');
        }

        $articleModel->delete($id); // soft delete

        (new AuditService())->log(session()->get('user_id'), 'delete', 'kms', 'articles', $id);

        return redirect()->to(route_to('knowledge.articles.manage'))->with('message', 'Artikel berhasil dihapus.');
    }

    /**
     * Daftar artikel published — terbuka untuk pembaca publik.
     */
    public function browse()
    {
        $categoryId = $this->request->getGet('category_id') ?: null;
        $keyword    = $this->request->getGet('q') ?: null;

        return view('knowledge/articles/browse', [
            'title'      => 'Artikel PBJ',
            'subTitle'   => 'Artikel PBJ',
            'articles'   => (new ArticleModel())->browse($categoryId ? (int) $categoryId : null, $keyword),
            'categories' => (new CategoryModel())->kmsCategories(),
            'keyword'    => $keyword,
            'categoryId' => $categoryId,
        ]);
    }

    public function detail(int $id)
    {
        $articleModel = new ArticleModel();
        $article      = $articleModel->withDetail($id);

        if (! $article || $article['status'] !== 'published') {
            return redirect()->to(route_to('knowledge.articles.browse'))->with('error', 'Artikel tidak ditemukan.');
        }

        $articleModel->incrementView($id);

        $isLoggedIn = (bool) session()->get('isLoggedIn');
        $isBookmarked = $isLoggedIn
            ? (new ArticleBookmarkModel())->isBookmarked((int) session()->get('user_id'), $id)
            : false;

        return view('knowledge/articles/detail', [
            'title'         => $article['title'],
            'subTitle'      => 'Artikel PBJ',
            'article'       => $article,
            'isBookmarked'  => $isBookmarked,
        ]);
    }
}
