<?php

namespace App\Models;

use CodeIgniter\Model;

class TemplateModel extends Model
{
    protected $table          = 'templates';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $allowedFields  = ['category_id', 'title', 'file_path'];

    protected $validationRules = [
        'category_id' => 'required|is_natural_no_zero',
        'title'       => 'required|max_length[200]',
        'file_path'   => 'required|max_length[255]',
    ];

    public function browse(?int $categoryId = null, ?string $keyword = null): array
    {
        $builder = $this->select('templates.*, categories.name AS category_name')
            ->join('categories', 'categories.id = templates.category_id')
            ->orderBy('templates.created_at', 'DESC');

        if ($categoryId) {
            $builder->where('templates.category_id', $categoryId);
        }

        if ($keyword) {
            $builder->like('templates.title', $keyword);
        }

        return $builder->findAll();
    }
}
