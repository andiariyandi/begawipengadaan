<?php

namespace App\Controllers\Knowledge;

use App\Models\Knowledge\ArticleModel;
use App\Models\Knowledge\KnowledgeMediaItemModel;

/**
 * Carousel & Konten Unggulan — view gabungan artikel + media.
 * Update: toggle featured/carousel_enabled & urutan.
 */
class KnowledgeCarouselController extends BaseKnowledgeController
{
    public function index(): string
    {
        $articles = (new ArticleModel())
            ->where('carousel_enabled', 1)
            ->where('deleted_at IS NULL', null, false)
            ->orderBy('carousel_order', 'ASC')
            ->findAll();
        $media = (new KnowledgeMediaItemModel())
            ->where('carousel_enabled', 1)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
        return view('dashboard/knowledge/carousel/index', [
            'title'        => 'Carousel dan Konten Unggulan | Dashboard BegawiPengadaan',
            'pageTitle'    => 'Carousel dan Konten Unggulan',
            'activeMenu'   => 'knowledge',
            'activeSubmenu' => 'carousel',
            'articles'     => $articles,
            'media'        => $media,
        ]);
    }

    public function create(): string
    {
        return view('dashboard/knowledge/carousel/form', [
            'title'        => 'Tambah Carousel | Dashboard BegawiPengadaan',
            'pageTitle'    => 'Tambah Carousel',
            'formMode'     => 'create',
            'activeMenu'   => 'knowledge',
            'activeSubmenu' => 'carousel',
        ]);
    }

    /**
     * Pilih artikel atau media untuk diaktifkan di carousel.
     */
    public function store()
    {
        if ($forbid = $this->assertCanManage()) {
            return $forbid;
        }
        $type = (string) $this->request->getPost('target_type');
        $rawId = $this->request->getPost('target_id');
        if (empty($rawId)) {
            $rawId = $type === 'article' ? $this->request->getPost('target_id_article') : $this->request->getPost('target_id_media');
        }
        $id = (int) $rawId;

        if (empty($type) || $id <= 0) {
            return redirect()->back()->withInput()->with('error', 'Pilih target konten (artikel atau media) yang valid.');
        }
        $order = (int) ($this->request->getPost('order') ?: 0);
        $now = date('Y-m-d H:i:s');
        if ($type === 'article') {
            (new ArticleModel())->update($id, [
                'carousel_enabled' => 1,
                'carousel_order'   => $order,
                'updated_at'       => $now,
            ]);
        } else {
            (new KnowledgeMediaItemModel())->update($id, [
                'carousel_enabled' => 1,
                'sort_order'       => $order,
                'updated_at'       => $now,
            ]);
        }
        return redirect()->to(route_to('dashboard.knowledge.carousel'))->with('success', 'Carousel diaktifkan.');
    }

    public function disable(string $type, string $id = '')
    {
        if ($forbid = $this->assertCanManage()) {
            return $forbid;
        }
        $id = $id !== '' ? $id : (string) $this->request->getPost('target_id');
        $now = date('Y-m-d H:i:s');
        if ($type === 'article') {
            (new ArticleModel())->update((int) $id, [
                'carousel_enabled' => 0,
                'carousel_order'   => null,
                'updated_at'       => $now,
            ]);
        } elseif ($type === 'media') {
            (new KnowledgeMediaItemModel())->update((int) $id, [
                'carousel_enabled' => 0,
                'updated_at'       => $now,
            ]);
        }
        return redirect()->to(route_to('dashboard.knowledge.carousel'))->with('success', 'Carousel dinonaktifkan.');
    }
}
