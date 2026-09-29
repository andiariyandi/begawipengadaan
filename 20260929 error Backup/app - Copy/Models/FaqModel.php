<?php

namespace App\Models;

use CodeIgniter\Model;

class FaqModel extends Model
{
    protected $table         = 'faq';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['category_id', 'question', 'answer'];

    protected $validationRules = [
        'question' => 'required|max_length[255]',
        'answer'   => 'required',
    ];

    public function browse(?int $categoryId = null, ?string $keyword = null): array
    {
        $builder = $this->select('faq.*, categories.name AS category_name')
            ->join('categories', 'categories.id = faq.category_id')
            ->orderBy('faq.created_at', 'DESC');

        if ($categoryId) {
            $builder->where('faq.category_id', $categoryId);
        }

        if ($keyword) {
            $builder->groupStart()
                ->like('faq.question', $keyword)
                ->orLike('faq.answer', $keyword)
                ->groupEnd();
        }

        return $builder->findAll();
    }
}
