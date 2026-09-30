<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProfileCategoryToUserProfiles extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('profile_category', 'user_profiles')) {
            $this->forge->addColumn('user_profiles', [
                'profile_category' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 32,
                    'null'       => true,
                    'after'      => 'user_id',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('profile_category', 'user_profiles')) {
            $this->forge->dropColumn('user_profiles', 'profile_category');
        }
    }
}
