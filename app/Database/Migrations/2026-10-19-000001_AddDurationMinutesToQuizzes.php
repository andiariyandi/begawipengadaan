<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDurationMinutesToQuizzes extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('duration_minutes', 'quizzes')) {
            $this->forge->addColumn('quizzes', [
                'duration_minutes' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'attempt_limit',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('duration_minutes', 'quizzes')) {
            $this->forge->dropColumn('quizzes', 'duration_minutes');
        }
    }
}
