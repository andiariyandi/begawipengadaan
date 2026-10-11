<?php

namespace App\Models\Learning;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table = 'courses';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes = true;
    protected $deletedField = 'deleted_at';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $allowedFields = [
        'program_id',
        'title',
        'slug',
        'summary',
        'description',
        'target_audience',
        'prerequisites',
        'learning_objectives',
        'level',
        'duration_minutes',
        'status',
        'visibility',
        'cover_file_id',
        'cover_url',
        'created_by',
        'updated_by',
        'published_by',
        'published_at',
    ];
}
