<?php

namespace App\Models;

use CodeIgniter\Model;

class AssessmentModel extends Model
{
    protected $table         = 'assessments';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['module_id', 'type', 'title', 'passing_score'];

    protected $validationRules = [
        'title'         => 'required|max_length[200]',
        'type'          => 'required|in_list[quiz,exam,assignment]',
        'passing_score' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
    ];

    public function forModule(int $moduleId): array
    {
        return $this->where('module_id', $moduleId)->orderBy('id', 'ASC')->findAll();
    }

    /**
     * Assessment tipe quiz saja yang punya alur pengerjaan otomatis saat ini.
     */
    public function quizzesForModule(int $moduleId): array
    {
        return $this->where('module_id', $moduleId)->where('type', 'quiz')->findAll();
    }
}
