<?php

namespace App\Models;

use CodeIgniter\Model;

class RupAuditBatchModel extends Model
{
    protected $table            = 'rup_audit_batches';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'batch_uuid',
        'user_id',
        'filename',
        'file_size',
        'total_rows_raw',
        'total_packages',
        'total_pagu',
        'total_pagu_penyedia',
        'total_pagu_swakelola',
        'total_opd',
        'avg_rci_score',
        'status',
        'previous_batch_id',
        'macro_summary',
        'schema_mode',
        'tahun_anggaran',
        'nama_instansi',
        'notes',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
