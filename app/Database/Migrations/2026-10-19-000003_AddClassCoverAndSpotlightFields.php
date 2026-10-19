<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddClassCoverAndSpotlightFields extends Migration
{
    public function up()
    {
        // 1. Add cover and featured to learning_classes
        if (! $this->db->fieldExists('cover_url', 'learning_classes')) {
            $this->forge->addColumn('learning_classes', [
                'cover_file_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'description',
                ],
                'cover_url' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 500,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'cover_file_id',
                ],
                'featured' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'null'       => false,
                    'default'    => 0,
                    'after'      => 'status',
                ],
            ]);
        }

        // 2. Add featured to courses
        if (! $this->db->fieldExists('featured', 'courses')) {
            $this->forge->addColumn('courses', [
                'featured' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'null'       => false,
                    'default'    => 0,
                    'after'      => 'visibility',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('cover_url', 'learning_classes')) {
            $this->forge->dropColumn('learning_classes', [
                'cover_file_id',
                'cover_url',
                'featured',
            ]);
        }

        if ($this->db->fieldExists('featured', 'courses')) {
            $this->forge->dropColumn('courses', ['featured']);
        }
    }
}
