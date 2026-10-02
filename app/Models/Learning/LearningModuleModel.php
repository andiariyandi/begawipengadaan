<?php

namespace App\Models\Learning;

use CodeIgniter\Model;

class LearningModuleModel extends Model
{
    protected $table = 'learning_modules';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $allowedFields = ['class_id', 'title', 'description', 'sort_order', 'is_required', 'status'];
}
