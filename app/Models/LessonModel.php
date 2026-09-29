<?php

namespace App\Models;

use CodeIgniter\Model;

class LessonModel extends Model
{
    protected $table         = 'lessons';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['module_id', 'title', 'content_type', 'file_path', 'content_text', 'sequence_order'];

    protected $validationRules = [
        'title'        => 'required|max_length[200]',
        'content_type' => 'required|in_list[document,video,text]',
    ];

    public function forModule(int $moduleId): array
    {
        return $this->where('module_id', $moduleId)->orderBy('sequence_order', 'ASC')->findAll();
    }
}
