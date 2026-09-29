<?php

namespace App\Models;

use CodeIgniter\Model;

class RegulationCategoryModel extends Model
{
    protected $table         = 'regulation_categories';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['name'];
}
