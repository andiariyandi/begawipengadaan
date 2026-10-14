<?php

namespace App\Models\Communication;

use CodeIgniter\Model;

class NewsModel extends Model
{
    protected $table         = 'news';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'category_id', 'title', 'slug', 'summary', 'content',
        'cover_file_id', 'cover_caption', 'is_featured',
        'reading_time', 'tags', 'source_name', 'source_url',
        'workflow_status', 'visibility',
        'published_at', 'scheduled_at', 'view_count',
        'author_id', 'reviewed_by', 'published_by',
    ];

    public function listNews(?string $status = null, ?int $categoryId = null, ?string $keyword = null): array
    {
        $builder = $this->db->table('news n')
            ->select('n.*, nc.name AS category_name, u.username AS author_name,
                      fa.path AS cover_path, fa.disk AS cover_disk,
                      fa.mime_type AS cover_mime_type')
            ->join('news_categories nc', 'nc.id = n.category_id', 'left')
            ->join('users u', 'u.id = n.author_id', 'left')
            ->join('file_assets fa', 'fa.id = n.cover_file_id', 'left')
            ->where('n.deleted_at', null)
            ->orderBy('n.published_at', 'DESC')
            ->orderBy('n.created_at', 'DESC');

        if ($status !== null && $status !== '') {
            $builder->where('n.workflow_status', $status);
        }
        if ($categoryId !== null) {
            $builder->where('n.category_id', $categoryId);
        }
        if ($keyword !== null && $keyword !== '') {
            $builder->groupStart()
                ->like('n.title', $keyword)
                ->orLike('n.slug', $keyword)
                ->orLike('n.summary', $keyword)
                ->groupEnd();
        }

        return $builder->get()->getResultArray();
    }

    public function listNewsPaginated(
        ?string $status = null,
        ?int $categoryId = null,
        ?string $keyword = null,
        ?int $isFeatured = null,
        int $perPage = 9,
        int $page = 1,
        string $sort = 'newest'
    ): array {
        $builder = $this->db->table('news n')
            ->select('n.*, nc.name AS category_name, nc.slug AS category_slug, 
                      COALESCE(u.name, u.username, "Redaksi Begawi") AS author_name,
                      fa.path AS cover_path, fa.disk AS cover_disk,
                      fa.mime_type AS cover_mime_type, fa.original_name AS cover_original_name')
            ->join('news_categories nc', 'nc.id = n.category_id', 'left')
            ->join('users u', 'u.id = n.author_id', 'left')
            ->join('file_assets fa', 'fa.id = n.cover_file_id', 'left')
            ->where('n.deleted_at', null);

        if ($status !== null && $status !== '' && $status !== 'all') {
            $builder->where('n.workflow_status', $status);
        }
        if ($categoryId !== null && $categoryId > 0) {
            $builder->where('n.category_id', $categoryId);
        }
        if ($isFeatured !== null) {
            $builder->where('n.is_featured', $isFeatured);
        }
        if ($keyword !== null && $keyword !== '') {
            $builder->groupStart()
                ->like('n.title', $keyword)
                ->orLike('n.slug', $keyword)
                ->orLike('n.summary', $keyword)
                ->orLike('n.tags', $keyword)
                ->orLike('nc.name', $keyword)
                ->groupEnd();
        }

        $countBuilder = clone $builder;
        $totalRows = (int) $countBuilder->countAllResults(false);
        $totalPages = max(1, (int) ceil($totalRows / $perPage));
        $currentPage = min(max(1, $page), $totalPages);
        $offset = ($currentPage - 1) * $perPage;

        match ($sort) {
            'popular' => $builder->orderBy('n.view_count', 'DESC')->orderBy('n.published_at', 'DESC'),
            'oldest'  => $builder->orderBy('n.published_at', 'ASC')->orderBy('n.id', 'ASC'),
            default   => $builder->orderBy('n.is_featured', 'DESC')->orderBy('n.published_at', 'DESC')->orderBy('n.id', 'DESC'),
        };

        $items = $builder->limit($perPage, $offset)->get()->getResultArray();

        return [
            'items'       => $items,
            'totalRows'   => $totalRows,
            'page'        => $currentPage,
            'perPage'     => $perPage,
            'totalPages'  => $totalPages,
            'hasPrev'     => $currentPage > 1,
            'hasNext'     => $currentPage < $totalPages,
            'prevPage'    => $currentPage - 1,
            'nextPage'    => $currentPage + 1,
        ];
    }

    public function detail(int $id): ?array
    {
        $row = $this->db->table('news n')
            ->select('n.*, nc.name AS category_name, u.username AS author_name,
                      fa.path AS cover_path, fa.disk AS cover_disk,
                      fa.mime_type AS cover_mime_type, fa.original_name AS cover_original_name')
            ->join('news_categories nc', 'nc.id = n.category_id', 'left')
            ->join('users u', 'u.id = n.author_id', 'left')
            ->join('file_assets fa', 'fa.id = n.cover_file_id', 'left')
            ->where('n.id', $id)
            ->get()->getRowArray();
        return $row ?: null;
    }

    public function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');
        if ($slug === '') {
            $slug = 'news-' . date('YmdHis');
        }
        $base = $slug;
        $i = 1;
        while (true) {
            $check = $this->withDeleted()->where('slug', $slug)->first();
            if ($check === null || ($ignoreId !== null && (int) $check['id'] === $ignoreId)) {
                return $slug;
            }
            $slug = $base . '-' . (++$i);
        }
    }
}