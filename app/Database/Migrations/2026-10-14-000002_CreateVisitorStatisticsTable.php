<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVisitorStatisticsTable extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('visitor_statistics')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'BIGINT',
                    'constraint'     => 20,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'feature' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'default'    => 'home',
                ],
                'visit_date' => [
                    'type' => 'DATE',
                ],
                'hit_count' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'default'    => 1,
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
            $this->forge->addUniqueKey(['feature', 'visit_date']);
            $this->forge->addKey('visit_date');
            $this->forge->addKey('feature');

            $this->forge->createTable('visitor_statistics', true);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('visitor_statistics')) {
            $this->forge->dropTable('visitor_statistics', true);
        }
    }
}
