<?php

namespace App\Models\Learning;

use CodeIgniter\Model;

class LessonModel extends Model
{
    protected $table = 'lessons';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $allowedFields = [
        'module_id',
        'title',
        'slug',
        'content_type',
        'content',
        'file_id',
        'external_url',
        'sort_order',
        'is_required',
        'status',
        'visibility',
        'created_by',
        'updated_by',
    ];
}
