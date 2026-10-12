<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRegulationEnhancementFields extends Migration
{
    public function up()
    {
        // 1. Fields for regulations table
        $regulationFields = [];

        if (! $this->db->fieldExists('cover_file_id', 'regulations')) {
            $regulationFields['cover_file_id'] = [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
                'after'      => 'current_version_id',
            ];
        }

        if (! $this->db->fieldExists('cover_image_path', 'regulations')) {
            $regulationFields['cover_image_path'] = [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
                'default'    => null,
                'after'      => 'cover_file_id',
            ];
        }

        if (! $this->db->fieldExists('is_featured', 'regulations')) {
            $regulationFields['is_featured'] = [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'visibility',
            ];
        }

        if (! $this->db->fieldExists('keywords', 'regulations')) {
            $regulationFields['keywords'] = [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
                'default'    => null,
                'after'      => 'summary',
            ];
        }

        if (! empty($regulationFields)) {
            $this->forge->addColumn('regulations', $regulationFields);
        }

        // 2. Fields for regulation_categories table
        $categoryFields = [];

        if (! $this->db->fieldExists('icon_class', 'regulation_categories')) {
            $categoryFields['icon_class'] = [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => 'solar:document-outline',
                'after'      => 'slug',
            ];
        }

        if (! $this->db->fieldExists('icon_file_id', 'regulation_categories')) {
            $categoryFields['icon_file_id'] = [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
                'after'      => 'icon_class',
            ];
        }

        if (! empty($categoryFields)) {
            $this->forge->addColumn('regulation_categories', $categoryFields);
        }
    }

    public function down()
    {
        $regCols = ['cover_file_id', 'cover_image_path', 'is_featured', 'keywords'];
        foreach ($regCols as $col) {
            if ($this->db->fieldExists($col, 'regulations')) {
                $this->forge->dropColumn('regulations', $col);
            }
        }

        $catCols = ['icon_class', 'icon_file_id'];
        foreach ($catCols as $col) {
            if ($this->db->fieldExists($col, 'regulation_categories')) {
                $this->forge->dropColumn('regulation_categories', $col);
            }
        }
    }
}
