<?php

namespace App\Models\Communication;

use CodeIgniter\Model;

class NewsCategoryModel extends Model
{
    protected $table         = 'news_categories';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'name', 'slug', 'description', 'is_active',
    ];

    public function listActive(): array
    {
        return $this->where('is_active', 1)->orderBy('name')->findAll();
    }

    public function listAllWithCount(): array
    {
        $rows = $this->db->table('news_categories nc')
            ->select('nc.*, COUNT(n.id) AS news_count')
            ->join('news n', 'n.category_id = nc.id AND n.deleted_at IS NULL', 'left')
            ->groupBy('nc.id')
            ->orderBy('nc.name')
            ->get()->getResultArray();
        foreach ($rows as &$r) {
            $r['news_count'] = (int) ($r['news_count'] ?? 0);
        }
        return $rows;
    }

    public function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');
        if ($slug === '') {
            $slug = 'category-' . date('YmdHis');
        }
        $base = $slug;
        $i = 1;
        while (true) {
            $check = $this->where('slug', $slug)->first();
            if ($check === null || ($ignoreId !== null && (int) $check['id'] === $ignoreId)) {
                return $slug;
            }
            $slug = $base . '-' . (++$i);
        }
    }
}