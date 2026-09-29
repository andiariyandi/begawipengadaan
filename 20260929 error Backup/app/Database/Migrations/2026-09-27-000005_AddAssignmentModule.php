<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAssignmentModule extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type'=>'INT','unsigned'=>true,'auto_increment'=>true],
            'course_id' => ['type'=>'INT'],
            'class_id' => ['type'=>'INT'],
            'module_id' => ['type'=>'INT','null'=>true],
            'title' => ['type'=>'VARCHAR','constraint'=>255],
            'description' => ['type'=>'TEXT','null'=>true],
            'max_score' => ['type'=>'INT','default'=>100],
            'deadline' => ['type'=>'DATETIME','null'=>true],
            'status' => ['type'=>'VARCHAR','constraint'=>20,'default'=>'draft'],
            'created_at' => ['type'=>'DATETIME','null'=>true],
            'updated_at' => ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('assignments', true);

        $this->forge->addField([
            'id' => ['type'=>'INT','unsigned'=>true,'auto_increment'=>true],
            'assignment_id' => ['type'=>'INT'],
            'user_id' => ['type'=>'INT'],
            'file' => ['type'=>'VARCHAR','constraint'=>255,'null'=>true],
            'answer' => ['type'=>'TEXT','null'=>true],
            'submitted_at' => ['type'=>'DATETIME','null'=>true],
            'status' => ['type'=>'VARCHAR','constraint'=>20,'default'=>'submitted'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('assignment_submissions', true);

        $this->forge->addField([
            'id' => ['type'=>'INT','unsigned'=>true,'auto_increment'=>true],
            'submission_id' => ['type'=>'INT'],
            'evaluator_id' => ['type'=>'INT'],
            'score' => ['type'=>'INT','default'=>0],
            'feedback' => ['type'=>'TEXT','null'=>true],
            'graded_at' => ['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('assignment_grades', true);
    }

    public function down()
    {
        $this->forge->dropTable('assignment_grades', true);
        $this->forge->dropTable('assignment_submissions', true);
        $this->forge->dropTable('assignments', true);
    }
}
