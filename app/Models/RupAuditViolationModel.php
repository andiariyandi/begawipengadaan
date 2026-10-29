<?php

namespace App\Models;

use CodeIgniter\Model;

class RupAuditViolationModel extends Model
{
    protected $table            = 'rup_audit_violations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'batch_id',
        'package_id',
        'rule_id',
        'severity',
        'kategori',
        'regulasi',
        'deskripsi_pelanggaran',
        'rekomendasi_perbaikan',
        'target_perubahan',
        'created_at',
    ];

    protected $useTimestamps = false;
}
