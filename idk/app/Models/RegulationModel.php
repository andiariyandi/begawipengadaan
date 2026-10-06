<?php

namespace App\Models;

use CodeIgniter\Model;

class RegulationModel extends Model
{
    protected $table          = 'regulations';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $allowedFields  = ['regulation_category_id', 'title', 'nomor_regulasi', 'tahun', 'status', 'uploaded_by'];

    protected $validationRules = [
        'title'          => 'required|max_length[255]',
        'nomor_regulasi' => 'required|max_length[100]|is_unique[regulations.nomor_regulasi,id,{id}]',
        'tahun'          => 'required|numeric',
    ];

    public function browse(?int $categoryId = null, ?string $keyword = null): array
    {
        $builder = $this->select('regulations.*, regulation_categories.name AS category_name')
            ->join('regulation_categories', 'regulation_categories.id = regulations.regulation_category_id')
            ->orderBy('regulations.created_at', 'DESC');

        if ($categoryId) {
            $builder->where('regulations.regulation_category_id', $categoryId);
        }

        if ($keyword) {
            $builder->groupStart()
                ->like('regulations.title', $keyword)
                ->orLike('regulations.nomor_regulasi', $keyword)
                ->groupEnd();
        }

        return $builder->findAll();
    }

    public function withDetail(int $id): ?array
    {
        return $this->select('regulations.*, regulation_categories.name AS category_name, users.username AS uploader_name')
            ->join('regulation_categories', 'regulation_categories.id = regulations.regulation_category_id')
            ->join('users', 'users.id = regulations.uploaded_by')
            ->where('regulations.id', $id)
            ->first();
    }
}
