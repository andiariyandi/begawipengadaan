<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * FASE 7: Add procurement_stages master + pivot tables for
 * regulation↔competency and regulation↔stage relations.
 */
class AddProcurementStagesAndPivotTables extends Migration
{
    public function up()
    {
        // ---------- procurement_stages ----------
        if (! $this->db->tableExists('procurement_stages')) {
            $this->forge->addField([
                'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'key' => ['type' => 'VARCHAR', 'constraint' => 64],
                'name' => ['type' => 'VARCHAR', 'constraint' => 255],
                'description' => ['type' => 'TEXT', 'null' => true],
                'sort_order' => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
                'is_active' => ['type' => 'TINYINT', 'default' => 1],
                'created_at' => ['type' => 'DATETIME'],
                'updated_at' => ['type' => 'DATETIME'],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('key');
            $this->forge->createTable('procurement_stages', true);

            // Seed default master
            $now = date('Y-m-d H:i:s');
            foreach ([
                ['key' => 'PLAN',      'name' => 'Perencanaan Pengadaan',     'description' => 'Identifikasi kebutuhan dan penyusunan rencana pengadaan', 'sort_order' => 1],
                ['key' => 'PREP',     'name' => 'Persiapan Pengadaan',       'description' => 'Persiapan dokumen dan strategi pengadaan',                   'sort_order' => 2],
                ['key' => 'SELECT',   'name' => 'Pemilihan Penyedia',        'description' => 'Proses pemilihan penyedia barang/jasa',                   'sort_order' => 3],
                ['key' => 'CONTRACT', 'name' => 'Pelaksanaan Kontrak',       'description' => 'Pelaksanaan dan pengendalian kontrak',                   'sort_order' => 4],
                ['key' => 'HANDOVER', 'name' => 'Serah Terima',              'description' => 'Pemeriksaan hasil dan serah terima pekerjaan',             'sort_order' => 5],
            ] as $row) {
                $this->db->table('procurement_stages')->insert($row + ['is_active' => 1, 'created_at' => $now, 'updated_at' => $now]);
            }
        }

        // ---------- regulation_competencies ----------
        if (! $this->db->tableExists('regulation_competencies')) {
            $this->forge->addField([
                'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'regulation_id' => ['type' => 'BIGINT', 'unsigned' => true],
                'competency_id' => ['type' => 'BIGINT', 'unsigned' => true],
                'relevance' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
                'created_at' => ['type' => 'DATETIME'],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['regulation_id', 'competency_id']);
            $this->forge->addForeignKey('regulation_id', 'regulations', 'id', '', 'CASCADE');
            $this->forge->addForeignKey('competency_id', 'competencies', 'id', '', 'CASCADE');
            $this->forge->createTable('regulation_competencies', true);
        }

        // ---------- regulation_procurement_stages ----------
        if (! $this->db->tableExists('regulation_procurement_stages')) {
            $this->forge->addField([
                'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                'regulation_id' => ['type' => 'BIGINT', 'unsigned' => true],
                'procurement_stage_id' => ['type' => 'BIGINT', 'unsigned' => true],
                'relevance' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
                'created_at' => ['type' => 'DATETIME'],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['regulation_id', 'procurement_stage_id']);
            $this->forge->addForeignKey('regulation_id', 'regulations', 'id', '', 'CASCADE');
            $this->forge->addForeignKey('procurement_stage_id', 'procurement_stages', 'id', '', 'CASCADE');
            $this->forge->createTable('regulation_procurement_stages', true);
        }
    }

    public function down()
    {
        foreach (['regulation_procurement_stages', 'regulation_competencies', 'procurement_stages'] as $t) {
            if ($this->db->tableExists($t)) {
                $this->forge->dropTable($t, true);
            }
        }
    }
}