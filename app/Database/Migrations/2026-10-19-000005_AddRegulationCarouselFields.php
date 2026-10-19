<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRegulationCarouselFields extends Migration
{
    public function up()
    {
        $fields = [];

        if (! $this->db->fieldExists('carousel_enabled', 'regulations')) {
            $fields['carousel_enabled'] = [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 0,
                'after'      => 'is_featured',
            ];
        }

        if (! $this->db->fieldExists('carousel_order', 'regulations')) {
            $fields['carousel_order'] = [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
                'after'      => 'carousel_enabled',
            ];
        }

        if (! empty($fields)) {
            $this->forge->addColumn('regulations', $fields);
        }
    }

    public function down()
    {
        $fieldsToDrop = [];

        if ($this->db->fieldExists('carousel_order', 'regulations')) {
            $fieldsToDrop[] = 'carousel_order';
        }

        if ($this->db->fieldExists('carousel_enabled', 'regulations')) {
            $fieldsToDrop[] = 'carousel_enabled';
        }

        if (! empty($fieldsToDrop)) {
            $this->forge->dropColumn('regulations', $fieldsToDrop);
        }
    }
}
