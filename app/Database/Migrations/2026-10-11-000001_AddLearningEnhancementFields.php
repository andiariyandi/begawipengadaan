<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLearningEnhancementFields extends Migration
{
    public function up()
    {
        // 1. Program Enhancement Fields
        if (! $this->db->fieldExists('target_audience', 'programs')) {
            $this->forge->addColumn('programs', [
                'target_audience' => [
                    'type'    => 'TEXT',
                    'null'    => true,
                    'default' => null,
                    'after'   => 'description',
                ],
                'prerequisites' => [
                    'type'    => 'TEXT',
                    'null'    => true,
                    'default' => null,
                    'after'   => 'target_audience',
                ],
                'learning_objectives' => [
                    'type'    => 'TEXT',
                    'null'    => true,
                    'default' => null,
                    'after'   => 'prerequisites',
                ],
                'cover_url' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 500,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'cover_file_id',
                ],
            ]);
        }

        // 2. Course Enhancement Fields
        if (! $this->db->fieldExists('target_audience', 'courses')) {
            $this->forge->addColumn('courses', [
                'target_audience' => [
                    'type'    => 'TEXT',
                    'null'    => true,
                    'default' => null,
                    'after'   => 'description',
                ],
                'prerequisites' => [
                    'type'    => 'TEXT',
                    'null'    => true,
                    'default' => null,
                    'after'   => 'target_audience',
                ],
                'learning_objectives' => [
                    'type'    => 'TEXT',
                    'null'    => true,
                    'default' => null,
                    'after'   => 'prerequisites',
                ],
                'cover_url' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 500,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'cover_file_id',
                ],
            ]);
        }

        // 3. Learning Class Enhancement Fields
        if (! $this->db->fieldExists('description', 'learning_classes')) {
            $this->forge->addColumn('learning_classes', [
                'description' => [
                    'type'    => 'TEXT',
                    'null'    => true,
                    'default' => null,
                    'after'   => 'name',
                ],
                'learning_method' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 64,
                    'null'       => false,
                    'default'    => 'online',
                    'after'      => 'status',
                ],
                'location_or_link' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 500,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'learning_method',
                ],
                'target_audience' => [
                    'type'    => 'TEXT',
                    'null'    => true,
                    'default' => null,
                    'after'   => 'location_or_link',
                ],
            ]);
        }

        // 4. Class Trainers Enhancement Fields
        if (! $this->db->fieldExists('role', 'class_trainers')) {
            $this->forge->addColumn('class_trainers', [
                'role' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 64,
                    'null'       => false,
                    'default'    => 'primary',
                    'after'      => 'status',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('target_audience', 'programs')) {
            $this->forge->dropColumn('programs', [
                'target_audience',
                'prerequisites',
                'learning_objectives',
                'cover_url',
            ]);
        }

        if ($this->db->fieldExists('target_audience', 'courses')) {
            $this->forge->dropColumn('courses', [
                'target_audience',
                'prerequisites',
                'learning_objectives',
                'cover_url',
            ]);
        }

        if ($this->db->fieldExists('description', 'learning_classes')) {
            $this->forge->dropColumn('learning_classes', [
                'description',
                'learning_method',
                'location_or_link',
                'target_audience',
            ]);
        }

        if ($this->db->fieldExists('role', 'class_trainers')) {
            $this->forge->dropColumn('class_trainers', ['role']);
        }
    }
}
