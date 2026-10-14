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

        $db = $this->db;
        $stats = [
            'total'        => (int) $db->table('knowledge_media')->countAllResults(),
            'infographics' => (int) $db->table('knowledge_media')->whereIn('type', ['infographic', 'image', 'poster', 'flyer'])->countAllResults(),
            'videos'       => (int) $db->table('knowledge_media')->whereIn('type', ['youtube', 'video_file', 'video'])->countAllResults(),
            'gallery'      => (int) $db->table('knowledge_media')->where('type', 'gallery')->countAllResults(),
            'published'    => (int) $db->table('knowledge_media')->where('workflow_status', 'published')->countAllResults(),
        ];

        return view('dashboard/knowledge/media/index', [
            'title'        => 'Semua Media & Galeri | Dashboard BegawiPengadaan',
            'pageTitle'    => 'Media & Galeri PBJ',
            'activeMenu'   => 'knowledge',
            'activeSubmenu' => 'media-gallery',
            'items'        => $items,
            'stats'        => $stats,
            'filterType'   => $type,
            'filterStatus' => $status,
            'filterKeyword' => $keyword,
            'scope'        => 'all',
        ]);
    }

    public function infographics(): string
    {
        $status  = $this->request->getGet('status') ?: null;
        $keyword = $this->request->getGet('q') ?: null;

        $builder = $this->db->table('knowledge_media km')
            ->select('km.*, fa.original_name AS file_original_name, fa.mime_type AS file_mime_type, fa.path AS file_path, fa.disk AS file_disk,
                      ft.original_name AS thumb_original_name, ft.mime_type AS thumb_mime_type, ft.path AS thumb_path, ft.disk AS thumb_disk')
            ->join('file_assets fa', 'fa.id = km.file_id', 'left')
            ->join('file_assets ft', 'ft.id = km.thumbnail_file_id', 'left')
            ->whereIn('km.type', ['infographic', 'image', 'poster', 'flyer'])
            ->orderBy('km.updated_at', 'DESC');

        if ($status !== null && $status !== '') {
            $builder->where('km.workflow_status', $status);
        }
        if ($keyword !== null && $keyword !== '') {
            $builder->groupStart()
                ->like('km.title', $keyword)
                ->orLike('km.caption', $keyword)
                ->orLike('km.alt_text', $keyword)
                ->groupEnd();
        }

        $items = $builder->get()->getResultArray();
        $db = $this->db;
        $stats = [
            'total'     => (int) $db->table('knowledge_media')->whereIn('type', ['infographic', 'image', 'poster', 'flyer'])->countAllResults(),
            'published' => (int) $db->table('knowledge_media')->whereIn('type', ['infographic', 'image', 'poster', 'flyer'])->where('workflow_status', 'published')->countAllResults(),
            'draft'     => (int) $db->table('knowledge_media')->whereIn('type', ['infographic', 'image', 'poster', 'flyer'])->where('workflow_status', 'draft')->countAllResults(),
        ];

        return view('dashboard/knowledge/media/index', [
            'title'        => 'Media & Infografik | Dashboard BegawiPengadaan',
            'pageTitle'    => 'Media & Infografik PBJ',
            'activeMenu'   => 'knowledge',
            'activeSubmenu' => 'media',
            'items'        => $items,
            'stats'        => $stats,
            'filterType'   => 'infographic',
            'filterStatus' => $status,
            'filterKeyword' => $keyword,
            'scope'        => 'infographics',
        ]);
    }

    public function videos(): string
    {
        $status  = $this->request->getGet('status') ?: null;
        $keyword = $this->request->getGet('q') ?: null;

        $builder = $this->db->table('knowledge_media km')
            ->select('km.*, fa.original_name AS file_original_name, fa.mime_type AS file_mime_type, fa.path AS file_path, fa.disk AS file_disk,
                      ft.original_name AS thumb_original_name, ft.mime_type AS thumb_mime_type, ft.path AS thumb_path, ft.disk AS thumb_disk')
            ->join('file_assets fa', 'fa.id = km.file_id', 'left')
            ->join('file_assets ft', 'ft.id = km.thumbnail_file_id', 'left')
            ->whereIn('km.type', ['youtube', 'video_file', 'video'])
            ->orderBy('km.updated_at', 'DESC');

        if ($status !== null && $status !== '') {
            $builder->where('km.workflow_status', $status);
        }
        if ($keyword !== null && $keyword !== '') {
            $builder->groupStart()
                ->like('km.title', $keyword)
                ->orLike('km.caption', $keyword)
                ->orLike('km.external_url', $keyword)
                ->groupEnd();
        }

        $items = $builder->get()->getResultArray();
        $db = $this->db;
        $stats = [
            'total'     => (int) $db->table('knowledge_media')->whereIn('type', ['youtube', 'video_file', 'video'])->countAllResults(),
            'published' => (int) $db->table('knowledge_media')->whereIn('type', ['youtube', 'video_file', 'video'])->where('workflow_status', 'published')->countAllResults(),
            'draft'     => (int) $db->table('knowledge_media')->whereIn('type', ['youtube', 'video_file', 'video'])->where('workflow_status', 'draft')->countAllResults(),
        ];

        return view('dashboard/knowledge/media/index', [
            'title'        => 'Video PBJ | Dashboard BegawiPengadaan',
            'pageTitle'    => 'Video Edukasi & Podcast PBJ',
            'activeMenu'   => 'knowledge',
            'activeSubmenu' => 'videos',
            'items'        => $items,
            'stats'        => $stats,
            'filterType'   => 'youtube',
            'filterStatus' => $status,
            'filterKeyword' => $keyword,
            'scope'        => 'videos',
        ]);
    }

    public function gallery(): string
    {
        $status  = $this->request->getGet('status') ?: null;
        $keyword = $this->request->getGet('q') ?: null;

        $builder = $this->db->table('knowledge_media km')
            ->select('km.*, fa.original_name AS file_original_name, fa.mime_type AS file_mime_type, fa.path AS file_path, fa.disk AS file_disk,
                      ft.original_name AS thumb_original_name, ft.mime_type AS thumb_mime_type, ft.path AS thumb_path, ft.disk AS thumb_disk')
            ->join('file_assets fa', 'fa.id = km.file_id', 'left')
            ->join('file_assets ft', 'ft.id = km.thumbnail_file_id', 'left')
            ->where('km.type', 'gallery')
            ->orderBy('km.updated_at', 'DESC');

        if ($status !== null && $status !== '') {
            $builder->where('km.workflow_status', $status);
        }
        if ($keyword !== null && $keyword !== '') {
            $builder->groupStart()
                ->like('km.title', $keyword)
                ->orLike('km.caption', $keyword)
                ->orLike('km.location', $keyword)
                ->groupEnd();
        }

        $items = $builder->get()->getResultArray();
        $db = $this->db;
        $stats = [
            'total'     => (int) $db->table('knowledge_media')->where('type', 'gallery')->countAllResults(),
            'published' => (int) $db->table('knowledge_media')->where('type', 'gallery')->where('workflow_status', 'published')->countAllResults(),
            'draft'     => (int) $db->table('knowledge_media')->where('type', 'gallery')->where('workflow_status', 'draft')->countAllResults(),
        ];

        return view('dashboard/knowledge/media/index', [
            'title'        => 'Galeri Kegiatan | Dashboard BegawiPengadaan',
            'pageTitle'    => 'Galeri Kegiatan & Dokumentasi PBJ',
            'activeMenu'   => 'knowledge',
            'activeSubmenu' => 'gallery',
            'items'        => $items,
            'stats'        => $stats,
            'filterType'   => 'gallery',
            'filterStatus' => $status,
            'filterKeyword' => $keyword,
            'scope'        => 'gallery',
        ]);
    }

    public function create(): string
    {
        $defaultType = $this->request->getGet('type') ?: 'infographic';
        $activeSubmenu = match ($defaultType) {
            'youtube', 'video_file', 'video' => 'videos',
            'gallery'                        => 'gallery',
            'infographic', 'image', 'poster', 'flyer' => 'media',
            default                          => 'media-gallery',
        };

        return view('dashboard/knowledge/media/form', [
            'title'        => 'Tambah Media | Dashboard BegawiPengadaan',
            'pageTitle'    => 'Tambah Media & Galeri',
            'formMode'     => 'create',
            'activeMenu'   => 'knowledge',
            'activeSubmenu' => $activeSubmenu,
            'media'        => ['type' => $defaultType],
            'preselectedType' => $defaultType,
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
            'hashtags'        => trim((string) $this->request->getPost('hashtags')) ?: null,
            'likes_count'     => (int) ($this->request->getPost('likes_count') ?: 0),
            'views_count'     => (int) ($this->request->getPost('views_count') ?: 0),
            'shares_count'    => (int) ($this->request->getPost('shares_count') ?: 0),
            'location'        => trim((string) $this->request->getPost('location')) ?: null,
            'event_date'      => $this->request->getPost('event_date') ?: null,
            'duration'        => trim((string) $this->request->getPost('duration')) ?: null,
            'external_url'    => $this->request->getPost('external_url') ?: null,
            'workflow_status' => (string) $this->request->getPost('status'),
            'visibility'      => (string) ($this->request->getPost('visibility') ?: 'public'),
            'sort_order'      => (int) ($this->request->getPost('sort_order') ?: 0),
            'featured'        => $this->request->getPost('featured') ? 1 : 0,
            'carousel_enabled' => $this->request->getPost('carousel_enabled') ? 1 : 0,
            'created_by'      => $this->currentUserId(),
            'created_at'      => $now,
            'updated_at'      => $now,
        ];

        if ($payload['workflow_status'] === 'published') {
            $payload['published_at'] = $now;
            $payload['published_by'] = $this->currentUserId();
        }

        $file = $this->request->getFile('file');
        $fileId = $this->uploadFileAsset($file, 'media_' . $type, 'public');
        if ($fileId !== null) {
            $payload['file_id'] = $fileId;
        }

        $thumb = $this->request->getFile('thumbnail');
        $thumbId = $this->uploadFileAsset($thumb, 'media_thumbnail_' . $type, 'public');
        if ($thumbId !== null) {
            $payload['thumbnail_file_id'] = $thumbId;
        }

        $model->insert($payload);
        $id = (int) $model->getInsertID();

        $redirectRoute = match ($type) {
            'youtube', 'video_file', 'video' => route_to('dashboard.knowledge.videos'),
            'gallery'                        => route_to('dashboard.knowledge.gallery'),
            'infographic', 'image', 'poster', 'flyer' => route_to('dashboard.knowledge.media'),
            default                          => route_to('dashboard.knowledge.media-gallery'),
        };

        return redirect()->to($redirectRoute)->with('success', 'Media berhasil ditambahkan.');
    }

    public function detail(string $id): string
    {
        $row = (new KnowledgeMediaItemModel())->detail((int) $id);
        if ($row === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Media tidak ditemukan.');
        }

        $type = $row['type'] ?? 'image';
        $activeSubmenu = match ($type) {
            'youtube', 'video_file', 'video' => 'videos',
            'gallery'                        => 'gallery',
            'infographic', 'image', 'poster', 'flyer' => 'media',
            default                          => 'media-gallery',
        };

        return view('dashboard/knowledge/media/detail', [
            'title'        => 'Detail Media | Dashboard BegawiPengadaan',
            'pageTitle'    => 'Detail Media & Galeri',
            'mediaId'      => (int) $id,
            'media'        => $row,
            'activeMenu'   => 'knowledge',
            'activeSubmenu' => $activeSubmenu,
        ]);
    }

    public function edit(string $id): string
    {
        $row = (new KnowledgeMediaItemModel())->detail((int) $id);
        if ($row === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Media tidak ditemukan.');
        }

        $type = $row['type'] ?? 'image';
        $activeSubmenu = match ($type) {
            'youtube', 'video_file', 'video' => 'videos',
            'gallery'                        => 'gallery',
            'infographic', 'image', 'poster', 'flyer' => 'media',
            default                          => 'media-gallery',
        };

        return view('dashboard/knowledge/media/form', [
            'title'        => 'Ubah Media | Dashboard BegawiPengadaan',
            'pageTitle'    => 'Ubah Media & Galeri',
            'formMode'     => 'edit',
            'mediaId'      => (int) $id,
            'media'        => $row,
            'activeMenu'   => 'knowledge',
            'activeSubmenu' => $activeSubmenu,
            'preselectedType' => $type,
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
        $type = (string) $this->request->getPost('type');

        $payload = [
            'type'            => $type,
            'title'           => trim((string) $this->request->getPost('title')) ?: null,
            'caption'         => (string) $this->request->getPost('caption'),
            'alt_text'        => (string) $this->request->getPost('alt_text'),
            'hashtags'        => trim((string) $this->request->getPost('hashtags')) ?: null,
            'likes_count'     => (int) ($this->request->getPost('likes_count') ?: 0),
            'views_count'     => (int) ($this->request->getPost('views_count') ?: 0),
            'shares_count'    => (int) ($this->request->getPost('shares_count') ?: 0),
            'location'        => trim((string) $this->request->getPost('location')) ?: null,
            'event_date'      => $this->request->getPost('event_date') ?: null,
            'duration'        => trim((string) $this->request->getPost('duration')) ?: null,
            'external_url'    => $this->request->getPost('external_url') ?: null,
            'workflow_status' => (string) $this->request->getPost('status'),
            'visibility'      => (string) ($this->request->getPost('visibility') ?: 'public'),
            'sort_order'      => (int) ($this->request->getPost('sort_order') ?: 0),
            'featured'        => $this->request->getPost('featured') ? 1 : 0,
            'carousel_enabled' => $this->request->getPost('carousel_enabled') ? 1 : 0,
            'updated_at'      => $now,
        ];

        if ($payload['workflow_status'] === 'published' && empty($row['published_at'])) {
            $payload['published_at'] = $now;
            $payload['published_by'] = $this->currentUserId();
        }

        $file = $this->request->getFile('file');
        $fileId = $this->uploadFileAsset($file, 'media_' . $payload['type'], 'public');
        if ($fileId !== null) {
            $payload['file_id'] = $fileId;
        }

        $thumb = $this->request->getFile('thumbnail');
        $thumbId = $this->uploadFileAsset($thumb, 'media_thumbnail_' . $payload['type'], 'public');
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
