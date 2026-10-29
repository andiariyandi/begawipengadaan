<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRupAuditTables extends Migration
{
    public function up(): void
    {
        // 1. Tabel rup_audit_batches
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'batch_uuid' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'filename' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'file_size' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'default'    => 0,
            ],
            'total_rows_raw' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'total_packages' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'total_pagu' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0.00,
            ],
            'total_pagu_penyedia' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0.00,
            ],
            'total_pagu_swakelola' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0.00,
            ],
            'total_opd' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'avg_rci_score' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['processing', 'completed', 'failed'],
                'default'    => 'processing',
            ],
            'previous_batch_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'macro_summary' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'schema_mode' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'CORE_12_COLS',
            ],
            'tahun_anggaran' => [
                'type'       => 'INT',
                'constraint' => 4,
                'default'    => 2026,
            ],
            'nama_instansi' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'default'    => 'Pemerintah Provinsi Lampung',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('batch_uuid', 'uk_rup_batch_uuid');
        $this->forge->addKey('user_id', false, false, 'idx_rup_batch_user');
        $this->forge->addKey('created_at', false, false, 'idx_rup_batch_created');
        $this->forge->createTable('rup_audit_batches', true);

        // 2. Tabel rup_audit_opd_summaries
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'batch_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'kode_satker_sipd' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => '',
            ],
            'nama_opd_clean' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'total_paket' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'total_pagu' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0.00,
            ],
            'pagu_penyedia' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0.00,
            ],
            'pagu_swakelola' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0.00,
            ],
            'pagu_dpa' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0.00,
            ],
            'persen_cakupan_dpa' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 100.00,
            ],
            'persen_epurchasing' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
            ],
            'persen_umk_penyedia' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
            ],
            'persen_umk_eligible_le15m' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
            ],
            'persen_umk_total_rup' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
            ],
            'persen_pdn' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
            ],
            'rasio_penunjukan_langsung' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
            ],
            'temuan_kritis' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'temuan_tinggi' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'temuan_sedang' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'temuan_rendah' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'pagu_bermasalah_kritis_tinggi' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0.00,
            ],
            'skor_rci' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
            ],
            'predikat_risiko' => [
                'type'       => 'ENUM',
                'constraint' => ['HIJAU', 'KUNING', 'ORANYE', 'MERAH'],
                'default'    => 'HIJAU',
            ],
            'macro_flags' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['batch_id', 'kode_satker_sipd'], false, false, 'idx_opd_batch_satker');
        $this->forge->addKey(['batch_id', 'skor_rci'], false, false, 'idx_opd_batch_rci');
        $this->forge->addKey(['batch_id', 'predikat_risiko'], false, false, 'idx_opd_batch_predikat');
        $this->forge->addForeignKey('batch_id', 'rup_audit_batches', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('rup_audit_opd_summaries', true);

        // 3. Tabel rup_audit_packages
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'batch_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'opd_summary_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'null'       => true,
            ],
            'row_index_csv' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'kode_rup' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => '',
            ],
            'nama_instansi' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'default'    => '',
            ],
            'nama_satuan_kerja' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'default'    => '',
            ],
            'nama_opd_clean' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'default'    => '',
            ],
            'kode_satker_sipd' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => '',
            ],
            'tahun_anggaran' => [
                'type'       => 'INT',
                'constraint' => 4,
                'default'    => 2026,
            ],
            'cara_pengadaan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'Penyedia',
            ],
            'metode_pengadaan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => '-',
            ],
            'metode_arr' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'jenis_pengadaan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => '-',
            ],
            'jenis_arr' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'nama_paket' => [
                'type' => 'TEXT',
            ],
            'nama_paket_clean' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'nama_paket_objek' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'nama_paket_core' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'lokasi_extracted' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'sumber_dana' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'default'    => 'APBD',
            ],
            'sumber_dana_arr' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'budget_cycle' => [
                'type'       => 'ENUM',
                'constraint' => ['MURNI', 'PERUBAHAN'],
                'default'    => 'MURNI',
            ],
            'is_blud' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'is_dak' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'is_multiyears' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'is_scientific_truncated' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'is_integrated_package' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'produk_dalam_negeri' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'Ya',
            ],
            'status_umk' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'Ya',
            ],
            'total_nilai' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0.00,
            ],
            'cluster_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'max_severity' => [
                'type'       => 'ENUM',
                'constraint' => ['AMAN', 'RENDAH', 'SEDANG', 'TINGGI', 'KRITIS'],
                'default'    => 'AMAN',
            ],
            'violation_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'diff_status' => [
                'type'       => 'ENUM',
                'constraint' => ['NEW', 'PERSISTENT', 'FIXED', 'DELETED'],
                'default'    => 'NEW',
            ],
            // Kolom Ekstensi Opsional (Mode 20 Kolom)
            'tanggal_umumkan' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'jadwal_pemilihan_awal' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'jadwal_kontrak_awal' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'jadwal_kontrak_akhir' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'tipe_swakelola' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'lokasi_pekerjaan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'kode_rekening_mak' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'pagu_pengadaan_dpa' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['batch_id', 'kode_rup'], false, false, 'idx_pkg_batch_koderup');
        $this->forge->addKey(['batch_id', 'row_index_csv'], false, false, 'idx_pkg_batch_row');
        $this->forge->addKey(['batch_id', 'nama_opd_clean'], false, false, 'idx_pkg_batch_opd');
        $this->forge->addKey(['batch_id', 'max_severity'], false, false, 'idx_pkg_batch_severity');
        $this->forge->addKey(['batch_id', 'diff_status'], false, false, 'idx_pkg_batch_diff');
        $this->forge->addKey('cluster_id', false, false, 'idx_pkg_cluster');
        $this->forge->addForeignKey('batch_id', 'rup_audit_batches', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('rup_audit_packages', true);

        // 4. Tabel rup_audit_violations
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'batch_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'package_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
            ],
            'rule_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'severity' => [
                'type'       => 'ENUM',
                'constraint' => ['KRITIS', 'TINGGI', 'SEDANG', 'RENDAH'],
                'default'    => 'SEDANG',
            ],
            'kategori' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => '',
            ],
            'regulasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'default'    => '',
            ],
            'deskripsi_pelanggaran' => [
                'type' => 'TEXT',
            ],
            'rekomendasi_perbaikan' => [
                'type' => 'TEXT',
            ],
            'target_perubahan' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['batch_id', 'rule_id'], false, false, 'idx_viol_batch_rule');
        $this->forge->addKey(['package_id', 'severity'], false, false, 'idx_viol_pkg_severity');
        $this->forge->addForeignKey('batch_id', 'rup_audit_batches', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('package_id', 'rup_audit_packages', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('rup_audit_violations', true);

        // 5. Tabel rup_audit_clusters
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'batch_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'cluster_uuid' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'kode_satker_sipd' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => '',
            ],
            'nama_opd_clean' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'budget_cycle' => [
                'type'       => 'ENUM',
                'constraint' => ['MURNI', 'PERUBAHAN'],
                'default'    => 'MURNI',
            ],
            'nama_komoditas_klaster' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'lokasi_klaster' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'default'    => 'ALL',
            ],
            'jenis_pengadaan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'Barang',
            ],
            'metode_dominan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'Pengadaan Langsung',
            ],
            'jumlah_paket_terpecah' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'nilai_paket_terkecil' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0.00,
            ],
            'nilai_paket_terbesar' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0.00,
            ],
            'total_pagu_klaster' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0.00,
            ],
            'batas_regulasi_pl' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 200000000.00,
            ],
            'kategori_temuan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'severity' => [
                'type'       => 'ENUM',
                'constraint' => ['KRITIS', 'TINGGI', 'SEDANG'],
                'default'    => 'TINGGI',
            ],
            'daftar_kode_rup' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'instruksi_konsolidasi' => [
                'type' => 'TEXT',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['batch_id', 'cluster_uuid'], false, false, 'idx_cls_batch_uuid');
        $this->forge->addKey(['batch_id', 'nama_opd_clean'], false, false, 'idx_cls_batch_opd');
        $this->forge->addKey(['batch_id', 'severity'], false, false, 'idx_cls_batch_severity');
        $this->forge->addForeignKey('batch_id', 'rup_audit_batches', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('rup_audit_clusters', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('rup_audit_violations', true);
        $this->forge->dropTable('rup_audit_clusters', true);
        $this->forge->dropTable('rup_audit_packages', true);
        $this->forge->dropTable('rup_audit_opd_summaries', true);
        $this->forge->dropTable('rup_audit_batches', true);
    }
}
