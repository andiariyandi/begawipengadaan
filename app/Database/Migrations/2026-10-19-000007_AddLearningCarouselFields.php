<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLearningCarouselFields extends Migration
{
    public function up()
    {
        // 1. Table programs
        $programFields = [];
        if (! $this->db->fieldExists('carousel_enabled', 'programs')) {
            $programFields['carousel_enabled'] = [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 0,
                'after'      => 'featured',
            ];
        }
        if (! $this->db->fieldExists('carousel_order', 'programs')) {
            $programFields['carousel_order'] = [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
                'after'      => 'carousel_enabled',
            ];
        }
        if (! empty($programFields)) {
            $this->forge->addColumn('programs', $programFields);
        }

        // 2. Table courses
        $courseFields = [];
        if (! $this->db->fieldExists('carousel_enabled', 'courses')) {
            $courseFields['carousel_enabled'] = [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 0,
                'after'      => 'featured',
            ];
        }
        if (! $this->db->fieldExists('carousel_order', 'courses')) {
            $courseFields['carousel_order'] = [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
                'after'      => 'carousel_enabled',
            ];
        }
        if (! empty($courseFields)) {
            $this->forge->addColumn('courses', $courseFields);
        }

        // 3. Table learning_classes
        $classFields = [];
        if (! $this->db->fieldExists('carousel_enabled', 'learning_classes')) {
            $classFields['carousel_enabled'] = [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 0,
                'after'      => 'featured',
            ];
        }
        if (! $this->db->fieldExists('carousel_order', 'learning_classes')) {
            $classFields['carousel_order'] = [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
                'after'      => 'carousel_enabled',
            ];
        }
        if (! empty($classFields)) {
            $this->forge->addColumn('learning_classes', $classFields);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('carousel_order', 'programs')) {
            $this->forge->dropColumn('programs', ['carousel_enabled', 'carousel_order']);
        }
        if ($this->db->fieldExists('carousel_order', 'courses')) {
            $this->forge->dropColumn('courses', ['carousel_enabled', 'carousel_order']);
        }
        if ($this->db->fieldExists('carousel_order', 'learning_classes')) {
            $this->forge->dropColumn('learning_classes', ['carousel_enabled', 'carousel_order']);
        }
    }
}
