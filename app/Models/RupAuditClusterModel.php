<?php

namespace App\Models;

use CodeIgniter\Model;

class RupAuditClusterModel extends Model
{
    protected $table            = 'rup_audit_clusters';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'batch_id',
        'cluster_uuid',
        'kode_satker_sipd',
        'nama_opd_clean',
        'budget_cycle',
        'nama_komoditas_klaster',
        'lokasi_klaster',
        'jenis_pengadaan',
        'metode_dominan',
        'jumlah_paket_terpecah',
        'nilai_paket_terkecil',
        'nilai_paket_terbesar',
        'total_pagu_klaster',
        'batas_regulasi_pl',
        'kategori_temuan',
        'severity',
        'daftar_kode_rup',
        'instruksi_konsolidasi',
        'created_at',
    ];

    protected $useTimestamps = false;
}
