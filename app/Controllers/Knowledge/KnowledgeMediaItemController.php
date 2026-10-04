<?php

namespace App\Controllers\Knowledge;

use App\Models\Knowledge\KnowledgeMediaItemModel;

/**
 * Items view → wrapper yang me-load media yang dapat dipakai pada galeri.
 */
class KnowledgeMediaItemController extends BaseKnowledgeController
{
    public function index(): string
    {
        $items = (new KnowledgeMediaItemModel())->listMedia();
        return view('dashboard/knowledge/media/items/index', [
            'title'        => 'Item Media | Dashboard BegawiPBJ',
            'pageTitle'    => 'Item Media',
            'activeMenu'   => 'knowledge',
            'activeSubmenu'=> 'media-gallery',
            'items'        => $items,
        ]);
    }

    public function detail(string $id): string
    {
        $row = (new KnowledgeMediaItemModel())->detail((int) $id);
        if ($row === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Item media tidak ditemukan.');
        }
        return view('dashboard/knowledge/media/items/detail', [
            'title'        => 'Detail Item Media | Dashboard BegawiPBJ',
            'pageTitle'    => 'Detail Item Media',
            'mediaItemId'  => (int) $id,
            'mediaItem'    => $row,
            'activeMenu'   => 'knowledge',
            'activeSubmenu'=> 'media-gallery',
        ]);
    }

    public function create(): string
    {
        return redirect()->to(route_to('dashboard.knowledge.media-gallery.create'));
    }
}