<?php

namespace App\Controllers\Knowledge;

use App\Controllers\BaseController;
use App\Models\ArticleBookmarkModel;
use App\Models\ArticleModel;
use App\Services\AuditService;

class BookmarkController extends BaseController
{
    public function myBookmarks()
    {
        return view('landing/user/buku_pintar', [
            'title'    => 'Artikel Tersimpan',
            'subTitle' => 'Artikel Tersimpan',
            'articles' => (new ArticleBookmarkModel())->myArticles((int) session()->get('user_id')),
        ]);
    }

    public function toggle(int $articleId)
    {
        $article = (new ArticleModel())->find($articleId);

        if (! $article || $article['status'] !== 'published') {
            return redirect()->back()->with('error', 'Artikel tidak ditemukan.');
        }

        $isBookmarked = (new ArticleBookmarkModel())->toggle((int) session()->get('user_id'), $articleId);

        (new AuditService())->log(session()->get('user_id'), $isBookmarked ? 'bookmark' : 'unbookmark', 'kms', 'articles', $articleId);

        return redirect()->back()->with('message', $isBookmarked ? 'Artikel disimpan ke bookmark.' : 'Artikel dihapus dari bookmark.');
    }
}
