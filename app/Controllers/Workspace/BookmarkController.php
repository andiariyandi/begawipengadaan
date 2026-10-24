<?php

namespace App\Controllers\Workspace;

use App\Controllers\BaseController;
use App\Models\Knowledge\BookmarkModel;

class BookmarkController extends BaseController
{
    /**
     * Halaman utama bookmark user — semua tipe digabung atau difilter per kategori.
     */
    public function index(string $categoryFilter = 'all')
    {
        $userId = (int) session()->get('user_id');
        if ($userId < 1) {
            return redirect()->to('/dashboard/masuk')->with('error', 'Silakan masuk untuk melihat bookmark.');
        }

        $activeFilter = trim((string) ($this->request->getGet('kategori') ?: $categoryFilter ?: 'all'));
        $allGrouped   = (new BookmarkModel())->myBookmarks($userId);

        $categoryMap = [
            'learning'      => ['program', 'course', 'learning_class'],
            'knowledge'     => ['knowledge_article', 'knowledge_faq', 'knowledge_case', 'knowledge_template', 'knowledge_media'],
            'regulations'   => ['regulation'],
            'communication' => ['news', 'event'],
        ];

        $grouped = $allGrouped;
        if (isset($categoryMap[$activeFilter])) {
            $allowedTypes = $categoryMap[$activeFilter];
            $grouped = array_intersect_key($allGrouped, array_flip($allowedTypes));
        }

        return view('main/workspace/pages/bookmark', [
            'pageTitle'      => 'Bookmark Saya',
            'breadcrumbTail' => [['label' => 'Bookmark']],
            'grouped'        => $grouped,
            'allGrouped'     => $allGrouped,
            'activeFilter'   => $activeFilter,
        ]);
    }

    public function learning()
    {
        return $this->index('learning');
    }

    public function knowledge()
    {
        return $this->index('knowledge');
    }

    public function regulations()
    {
        return $this->index('regulations');
    }

    public function communication()
    {
        return $this->index('communication');
    }

    public function certificates()
    {
        return $this->index('all');
    }

    /**
     * Hapus bookmark tertentu (POST) — dari halaman workspace.
     */
    public function remove()
    {
        $userId = (int) session()->get('user_id');
        if ($userId < 1) {
            return redirect()->to('/dashboard/masuk')->with('error', 'Silakan masuk.');
        }
        $type = (string) $this->request->getPost('type');
        $id   = (int) $this->request->getPost('id');
        if ($id > 0 && array_key_exists($type, BookmarkModel::SUPPORTED_TYPES)) {
            (new BookmarkModel())->remove($userId, $type, $id);
        }
        return redirect()->to(route_to('workspace.bookmarks.index'))->with('success', 'Bookmark dihapus.');
    }
}
