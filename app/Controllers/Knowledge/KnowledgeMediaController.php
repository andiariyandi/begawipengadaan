<?php

namespace App\Controllers\Knowledge;

use App\Models\Knowledge\KnowledgeMediaItemModel;

class KnowledgeMediaController extends BaseKnowledgeController
{
    public function index(): string
    {
        $type    = $this->request->getGet('type') ?: null;
        $status  = $this->request->getGet('status') ?: null;
        $keyword = $this->request->getGet('q') ?: null;
        $items   = (new KnowledgeMediaItemModel())->listMedia($type, $status, $keyword);
        return view('dashboard/knowledge/media/index', [
            'title'        => 'Media dan Galeri | Dashboard BegawiPBJ',
            'pageTitle'    => 'Media dan Galeri',
            'activeMenu'   => 'knowledge',
            'activeSubmenu'=> 'media-gallery',
            'items'        => $items,
            'filterType'   => $type,
            'filterStatus' => $status,
            'filterKeyword'=> $keyword,
        ]);
    }

    public function create(): string
    {
        return view('dashboard/knowledge/media/form', [
            'title'        => 'Tambah Media | Dashboard BegawiPBJ',
            'pageTitle'    => 'Tambah Media',
            'formMode'     => 'create',
            'activeMenu'   => 'knowledge',
            'activeSubmenu'=> 'media-gallery',
            'media'        => null,
        ]);
    }

    public function store()
    {
        if ($forbid = $this->assertCanManage()) {
            return $forbid;
        }
        $rules = [
            'type'   => 'required|in_list[image,poster,flyer,infographic,gallery,video_file,youtube,social_link,external_link]',
            'title'  => 'permit_empty|max_length[255]',
            'status' => 'required|in_list[draft,submitted,in_review,approved,published,revision_required,rejected,archived]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Tipe media dan status wajib valid.');
        }
        $model = new KnowledgeMediaItemModel();
        $now = date('Y-m-d H:i:s');
        $type = (string) $this->request->getPost('type');
        $payload = [
            'article_id'      => null,
            'type'            => $type,
            'title'           => trim((string) $this->request->getPost('title')) ?: null,
            'caption'         => (string) $this->request->getPost('caption'),
            'alt_text'        => (string) $this->request->getPost('alt_text'),
            'external_url'    => $this->request->getPost('external_url') ?: null,
            'workflow_status' => (string) $this->request->getPost('status'),
            'visibility'      => (string) ($this->request->getPost('visibility') ?: 'internal'),
            'sort_order'      => (int) ($this->request->getPost('sort_order') ?: 0),
            'featured'        => $this->request->getPost('featured') ? 1 : 0,
            'carousel_enabled'=> $this->request->getPost('carousel_enabled') ? 1 : 0,
            'created_by'      => $this->currentUserId(),
            'created_at'      => $now,
            'updated_at'      => $now,
        ];
        $file = $this->request->getFile('file');
        $fileId = $this->uploadFileAsset($file, 'media_'.$type, 'public');
        if ($fileId !== null) {
            $payload['file_id'] = $fileId;
        }
        $thumb = $this->request->getFile('thumbnail');
        $thumbId = $this->uploadFileAsset($thumb, 'media_thumbnail_'.$type, 'public');
        if ($thumbId !== null) {
            $payload['thumbnail_file_id'] = $thumbId;
        }
        $model->insert($payload);
        $id = (int) $model->db->insertID();
        return redirect()->to(route_to('dashboard.knowledge.media-gallery.detail', (string) $id))->with('success', 'Media berhasil ditambahkan.');
    }

    public function detail(string $id): string
    {
        $row = (new KnowledgeMediaItemModel())->detail((int) $id);
        if ($row === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Media tidak ditemukan.');
        }
        return view('dashboard/knowledge/media/detail', [
            'title'        => 'Detail Media | Dashboard BegawiPBJ',
            'pageTitle'    => 'Detail Media',
            'mediaId'      => (int) $id,
            'media'        => $row,
            'activeMenu'   => 'knowledge',
            'activeSubmenu'=> 'media-gallery',
        ]);
    }

    public function edit(string $id): string
    {
        $row = (new KnowledgeMediaItemModel())->detail((int) $id);
        if ($row === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Media tidak ditemukan.');
        }
        return view('dashboard/knowledge/media/form', [
            'title'        => 'Ubah Media | Dashboard BegawiPBJ',
            'pageTitle'    => 'Ubah Media',
            'formMode'     => 'edit',
            'mediaId'      => (int) $id,
            'media'        => $row,
            'activeMenu'   => 'knowledge',
            'activeSubmenu'=> 'media-gallery',
        ]);
    }

    public function update(string $id)
    {
        if ($forbid = $this->assertCanManage()) {
            return $forbid;
        }
        $model = new KnowledgeMediaItemModel();
        $row = $model->detail((int) $id);
        if ($row === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Media tidak ditemukan.');
        }
        $rules = [
            'type'   => 'required|in_list[image,poster,flyer,infographic,gallery,video_file,youtube,social_link,external_link]',
            'status' => 'required|in_list[draft,submitted,in_review,approved,published,revision_required,rejected,archived]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Periksa kembali isian formulir.');
        }
        $now = date('Y-m-d H:i:s');
        $payload = [
            'type'            => (string) $this->request->getPost('type'),
            'title'           => trim((string) $this->request->getPost('title')) ?: null,
            'caption'         => (string) $this->request->getPost('caption'),
            'alt_text'        => (string) $this->request->getPost('alt_text'),
            'external_url'    => $this->request->getPost('external_url') ?: null,
            'workflow_status' => (string) $this->request->getPost('status'),
            'visibility'      => (string) ($this->request->getPost('visibility') ?: 'internal'),
            'sort_order'      => (int) ($this->request->getPost('sort_order') ?: 0),
            'featured'        => $this->request->getPost('featured') ? 1 : 0,
            'carousel_enabled'=> $this->request->getPost('carousel_enabled') ? 1 : 0,
            'updated_at'      => $now,
        ];
        $file = $this->request->getFile('file');
        $fileId = $this->uploadFileAsset($file, 'media_'.$payload['type'], 'public');
        if ($fileId !== null) {
            $payload['file_id'] = $fileId;
        }
        $thumb = $this->request->getFile('thumbnail');
        $thumbId = $this->uploadFileAsset($thumb, 'media_thumbnail_'.$payload['type'], 'public');
        if ($thumbId !== null) {
            $payload['thumbnail_file_id'] = $thumbId;
        }
        $model->update((int) $id, $payload);
        return redirect()->to(route_to('dashboard.knowledge.media-gallery.detail', $id))->with('success', 'Media berhasil diperbarui.');
    }

    public function delete(string $id)
    {
        if ($forbid = $this->assertCanManage()) {
            return $forbid;
        }
        $model = new KnowledgeMediaItemModel();
        $row = $model->find((int) $id);
        if ($row === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Media tidak ditemukan.');
        }
        $model->delete((int) $id);
        return redirect()->to(route_to('dashboard.knowledge.media-gallery'))->with('success', 'Media dihapus.');
    }
}