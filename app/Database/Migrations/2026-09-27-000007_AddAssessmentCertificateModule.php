<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAssessmentCertificateModule extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'=>['type'=>'INT','unsigned'=>true,'auto_increment'=>true],
            'course_id'=>['type'=>'INT'],
            'title'=>['type'=>'VARCHAR','constraint'=>255],
            'passing_grade'=>['type'=>'INT','default'=>70],
            'created_at'=>['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id',true);
        $this->forge->createTable('quizzes',true);

        $this->forge->addField([
            'id'=>['type'=>'INT','unsigned'=>true,'auto_increment'=>true],
            'quiz_id'=>['type'=>'INT'],
            'question'=>['type'=>'TEXT'],
            'answer_key'=>['type'=>'TEXT','null'=>true],
        ]);
        $this->forge->addKey('id',true);
        $this->forge->createTable('quiz_questions',true);

        $this->forge->addField([
            'id'=>['type'=>'INT','unsigned'=>true,'auto_increment'=>true],
            'user_id'=>['type'=>'INT'],
            'course_id'=>['type'=>'INT'],
            'final_score'=>['type'=>'DECIMAL','constraint'=>'5,2','default'=>0],
            'status'=>['type'=>'VARCHAR','constraint'=>20,'default'=>'pending'],
        ]);
        $this->forge->addKey('id',true);
        $this->forge->createTable('learning_results',true);

        $this->forge->addField([
            'id'=>['type'=>'INT','unsigned'=>true,'auto_increment'=>true],
            'user_id'=>['type'=>'INT'],
            'course_id'=>['type'=>'INT'],
            'certificate_number'=>['type'=>'VARCHAR','constraint'=>100],
            'verification_code'=>['type'=>'VARCHAR','constraint'=>100],
            'issued_at'=>['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id',true);
        $this->forge->createTable('certificates',true);
    }

    public function down()
    {
        $this->forge->dropTable('certificates',true);
        $this->forge->dropTable('learning_results',true);
        $this->forge->dropTable('quiz_questions',true);
        $this->forge->dropTable('quizzes',true);
    }
}
