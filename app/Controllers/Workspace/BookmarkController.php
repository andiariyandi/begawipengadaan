<?php

namespace App\Controllers\Workspace;

use App\Controllers\BaseController;
use App\Models\Knowledge\BookmarkModel;

class BookmarkController extends BaseController
{
    /**
     * Halaman utama bookmark user — semua tipe digabung, dikelompokkan.
     */
    public function index()
    {
        $userId = (int) session()->get('user_id');
        if ($userId < 1) {
            return redirect()->to('/dashboard/masuk')->with('error', 'Silakan masuk untuk melihat bookmark.');
        }
        $grouped = (new BookmarkModel())->myBookmarks($userId);
        return view('main/workspace/pages/bookmark', [
            'pageTitle'      => 'Bookmark Saya',
            'breadcrumbTail' => [['label' => 'Bookmark']],
            'grouped'        => $grouped,
        ]);
    }

    public function learning()
    {
        return $this->index();
    }

    public function knowledge()
    {
        return $this->index();
    }

    public function regulations()
    {
        return $this->index();
    }

    public function communication()
    {
        return $this->index();
    }

    public function certificates()
    {
        return $this->index();
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