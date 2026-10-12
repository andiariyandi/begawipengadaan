<?php

namespace App\Models\Knowledge;

use CodeIgniter\Model;

class ArticleModel extends Model
{
    protected $table         = 'knowledge_articles';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $allowedFields = [
        'type', 'title', 'slug', 'summary', 'content',
        'workflow_status', 'visibility',
        'featured', 'carousel_enabled', 'carousel_order',
        'cover_file_id',
        'reading_time', 'source_url', 'organization_unit', 'procurement_stage',
        'case_severity', 'resolution_summary', 'legal_basis', 'sort_order',
        'document_category', 'document_version',
        'author_id', 'reviewed_by', 'published_by',
        'published_at', 'archived_at',
    ];

    /**
     * List untuk dashboard: type=article dengan filter & pencarian.
     */
    public function listArticles(string $type, ?string $status = null, ?string $keyword = null, ?int $categoryId = null): array
    {
        $builder = $this->db->table('knowledge_articles ka')
            ->select('ka.*, u.name AS author_name')
            ->join('users u', 'u.id = ka.author_id', 'left')
            ->where('ka.type', $type)
            ->where('ka.deleted_at IS NULL', null, false)
            ->orderBy('ka.updated_at', 'DESC');
        if ($status !== null && $status !== '') {
            $builder->where('ka.workflow_status', $status);
        }
        if ($keyword !== null && $keyword !== '') {
            $builder->groupStart()
                ->like('ka.title', $keyword)
                ->orLike('ka.summary', $keyword)
                ->groupEnd();
        }
        if ($categoryId !== null && $categoryId > 0) {
            $builder->join('knowledge_article_categories kac', 'kac.article_id = ka.id', 'inner')
                ->where('kac.category_id', $categoryId)
                ->groupBy('ka.id');
        }
        return $builder->get()->getResultArray();
    }

    /**
     * Detail + relasi kategori, tag, dan author.
     */
    public function detail(int $id): ?array
    {
        $row = $this->db->table('knowledge_articles ka')
            ->select('ka.*, u.name AS author_name, u.email AS author_email')
            ->join('users u', 'u.id = ka.author_id', 'left')
            ->where('ka.id', $id)
            ->where('ka.deleted_at IS NULL', null, false)
            ->get()->getRowArray();
        if ($row === null) {
            return null;
        }
        $row['categories'] = $this->db->table('knowledge_article_categories k')
            ->select('kc.id, kc.name, kc.slug')
            ->join('knowledge_categories kc', 'kc.id = k.category_id')
            ->where('k.article_id', $id)
            ->get()->getResultArray();
        $row['tags'] = $this->db->table('knowledge_article_tags k')
            ->select('kt.id, kt.name, kt.slug')
            ->join('knowledge_tags kt', 'kt.id = k.tag_id')
            ->where('k.article_id', $id)
            ->get()->getResultArray();
        return $row;
    }

    /**
     * Generate slug unik.
     */
    public function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = url_title($title, '-', true);
        if ($base === '') {
            $base = 'artikel-' . substr(md5(uniqid('', true)), 0, 6);
        }
        $slug = $base;
        $i = 1;
        while ($this->slugExists($slug, $ignoreId)) {
            $i++;
            $slug = $base . '-' . $i;
        }
        return $slug;
    }

    private function slugExists(string $slug, ?int $ignoreId): bool
    {
        $builder = $this->db->table('knowledge_articles')->where('slug', $slug);
        if ($ignoreId !== null) {
            $builder->where('id !=', $ignoreId);
        }
        return $builder->countAllResults() > 0;
    }

    /**
     * Sinkron kategori (replace).
     */
    public function syncCategories(int $articleId, array $categoryIds): void
    {
        $this->db->table('knowledge_article_categories')->where('article_id', $articleId)->delete();
        $now = date('Y-m-d H:i:s');
        foreach ($categoryIds as $cid) {
            $cid = (int) $cid;
            if ($cid < 1) {
                continue;
            }
            $this->db->table('knowledge_article_categories')->insert([
                'article_id'  => $articleId,
                'category_id' => $cid,
                'created_at'  => $now,
            ]);
        }
    }

    /**
     * Sinkron tag (replace). Tag baru akan dibuat otomatis jika belum ada.
     */
    public function syncTags(int $articleId, array $tagNames, TagModel $tagModel): void
    {
        $this->db->table('knowledge_article_tags')->where('article_id', $articleId)->delete();
        $now = date('Y-m-d H:i:s');
        foreach ($tagNames as $name) {
            $name = trim((string) $name);
            if ($name === '') {
                continue;
            }
            $slug = url_title($name, '-', true);
            $tag = $tagModel->where('slug', $slug)->first();
            if ($tag === null) {
                $tagModel->insert(['name' => $name, 'slug' => $slug]);
                $tag = $tagModel->where('slug', $slug)->first();
            }
            $this->db->table('knowledge_article_tags')->insert([
                'article_id' => $articleId,
                'tag_id'     => (int) $tag['id'],
                'created_at' => $now,
            ]);
        }
    }
}