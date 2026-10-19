<?php

namespace App\Models\Learning;

use CodeIgniter\Model;

class ProgramModel extends Model
{
    protected $table = 'programs';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes = true;
    protected $deletedField = 'deleted_at';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $allowedFields = [
        'title',
        'slug',
        'summary',
        'description',
        'target_audience',
        'prerequisites',
        'learning_objectives',
        'status',
        'visibility',
        'featured',
        'carousel_enabled',
        'carousel_order',
        'cover_file_id',
        'cover_url',
        'created_by',
        'updated_by',
        'published_by',
        'published_at',
    ];
}
