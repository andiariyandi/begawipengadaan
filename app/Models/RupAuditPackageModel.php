<?php

namespace App\Models;

use CodeIgniter\Model;

class RupAuditPackageModel extends Model
{
    protected $table            = 'rup_audit_packages';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'batch_id',
        'opd_summary_id',
        'row_index_csv',
        'kode_rup',
        'nama_instansi',
        'nama_satuan_kerja',
        'nama_opd_clean',
        'kode_satker_sipd',
        'tahun_anggaran',
        'cara_pengadaan',
        'metode_pengadaan',
        'metode_arr',
        'jenis_pengadaan',
        'jenis_arr',
        'nama_paket',
        'nama_paket_clean',
        'nama_paket_objek',
        'nama_paket_core',
        'lokasi_extracted',
        'sumber_dana',
        'sumber_dana_arr',
        'budget_cycle',
        'is_blud',
        'is_dak',
        'is_multiyears',
        'is_scientific_truncated',
        'is_integrated_package',
        'produk_dalam_negeri',
        'status_umk',
        'total_nilai',
        'cluster_id',
        'max_severity',
        'violation_count',
        'diff_status',
        'tanggal_umumkan',
        'jadwal_pemilihan_awal',
        'jadwal_kontrak_awal',
        'jadwal_kontrak_akhir',
        'tipe_swakelola',
        'lokasi_pekerjaan',
        'kode_rekening_mak',
        'pagu_pengadaan_dpa',
        'created_at',
    ];

    protected $useTimestamps = false;
}
