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
        'status',
        'visibility',
        'featured',
        'cover_file_id',
        'created_by',
        'updated_by',
        'published_by',
        'published_at',
    ];
}
