<?php

namespace App\Models\Intelligence;

use CodeIgniter\Model;

class CompetencyCertificateModel extends Model
{
    protected $table            = 'competency_certificates';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'jf_ppbj_record_id',
        'certificate_type_id',
        'certificate_number',
        'issued_at',
        'expires_at',
        'status',
        'evidence_file_id',
        'verified_by',
        'verified_at',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
