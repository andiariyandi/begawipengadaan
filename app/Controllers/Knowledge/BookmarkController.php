<?php

namespace App\Controllers\Knowledge;

use App\Models\Knowledge\BookmarkModel;

class BookmarkController extends BaseKnowledgeController
{
    public function index(): string
    {
        $userId = $this->currentUserId();
        $items = (new BookmarkModel())
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
        return view('dashboard/knowledge/bookmarks/index', [
            'title'        => 'Bookmark | Dashboard BegawiPBJ',
            'pageTitle'    => 'Bookmark',
            'activeMenu'   => 'knowledge',
            'activeSubmenu'=> 'bookmarks',
            'items'        => $items,
        ]);
    }
}