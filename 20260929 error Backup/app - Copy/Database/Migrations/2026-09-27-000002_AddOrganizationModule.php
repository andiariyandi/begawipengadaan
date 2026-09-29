<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOrganizationModule extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'parent_id' => [
                'type' => 'INT',
                'null' => true,
            ],
            'code' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'OPD',
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'active',
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
        $this->forge->createTable('organization_units', true);
    }

    public function down()
    {
        $this->forge->dropTable('organization_units', true);
    }
}
