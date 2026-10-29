<?php

namespace App\Models;

use CodeIgniter\Model;

class RupAuditOpdSummaryModel extends Model
{
    protected $table            = 'rup_audit_opd_summaries';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'batch_id',
        'kode_satker_sipd',
        'nama_opd_clean',
        'total_paket',
        'total_pagu',
        'pagu_penyedia',
        'pagu_swakelola',
        'pagu_dpa',
        'persen_cakupan_dpa',
        'persen_epurchasing',
        'persen_umk_penyedia',
        'persen_umk_eligible_le15m',
        'persen_umk_total_rup',
        'persen_pdn',
        'rasio_penunjukan_langsung',
        'temuan_kritis',
        'temuan_tinggi',
        'temuan_sedang',
        'temuan_rendah',
        'pagu_bermasalah_kritis_tinggi',
        'skor_rci',
        'predikat_risiko',
        'macro_flags',
        'created_at',
    ];

    protected $useTimestamps = false;
}
