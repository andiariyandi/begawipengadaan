<?php

namespace App\Models;

use CodeIgniter\Model;

class OrganizationUnitModel extends Model
{
    protected $table = 'organization_units';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'parent_id',
        'code',
        'name',
        'type',
        'status'
    ];

    protected $useTimestamps = true;
}
