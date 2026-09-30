<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserOrganizationRelation extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('users')) {
            $this->forge->addColumn('users', [
                'organization_id' => [
                    'type' => 'INT',
                    'unsigned' => true,
                    'null' => true,
                    'after' => 'id',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('users')) {
            $this->forge->dropColumn('users', 'organization_id');
        }
    }
}
