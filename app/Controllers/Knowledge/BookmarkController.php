<?php

namespace App\Controllers\Knowledge;

use App\Models\Knowledge\BookmarkModel;

class BookmarkController extends BaseKnowledgeController
{
    public function index(): string
    {
        $userId = $this->currentUserId();
        $bookmarkModel = new BookmarkModel();
        $groupedBookmarks = $bookmarkModel->myBookmarks($userId);
        $totalBookmarks = 0;
        foreach ($groupedBookmarks as $grp) {
            $totalBookmarks += count($grp['items']);
        }

        return view('dashboard/knowledge/bookmarks/index', [
            'title'            => 'Bookmark Koleksi PBJ | Dashboard BegawiPengadaan',
            'pageTitle'        => 'Bookmark Saya',
            'activeMenu'       => 'knowledge',
            'activeSubmenu'    => 'bookmarks',
            'groupedBookmarks' => $groupedBookmarks,
            'totalBookmarks'   => $totalBookmarks,
        ]);
    }
}
