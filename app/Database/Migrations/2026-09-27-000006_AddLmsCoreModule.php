<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLmsCoreModule extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'=>['type'=>'INT','unsigned'=>true,'auto_increment'=>true],
            'title'=>['type'=>'VARCHAR','constraint'=>255],
            'description'=>['type'=>'TEXT','null'=>true],
            'trainer_id'=>['type'=>'INT','null'=>true],
            'status'=>['type'=>'VARCHAR','constraint'=>20,'default'=>'draft'],
            'created_at'=>['type'=>'DATETIME','null'=>true],
            'updated_at'=>['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id',true);
        $this->forge->createTable('courses',true);

        $this->forge->addField([
            'id'=>['type'=>'INT','unsigned'=>true,'auto_increment'=>true],
            'course_id'=>['type'=>'INT'],
            'title'=>['type'=>'VARCHAR','constraint'=>255],
            'description'=>['type'=>'TEXT','null'=>true],
            'sort_order'=>['type'=>'INT','default'=>1],
        ]);
        $this->forge->addKey('id',true);
        $this->forge->createTable('course_modules',true);

        $this->forge->addField([
            'id'=>['type'=>'INT','unsigned'=>true,'auto_increment'=>true],
            'module_id'=>['type'=>'INT'],
            'title'=>['type'=>'VARCHAR','constraint'=>255],
            'content'=>['type'=>'LONGTEXT','null'=>true],
        ]);
        $this->forge->addKey('id',true);
        $this->forge->createTable('lessons',true);

        $this->forge->addField([
            'id'=>['type'=>'INT','unsigned'=>true,'auto_increment'=>true],
            'course_id'=>['type'=>'INT'],
            'user_id'=>['type'=>'INT'],
            'status'=>['type'=>'VARCHAR','constraint'=>20,'default'=>'active'],
        ]);
        $this->forge->addKey('id',true);
        $this->forge->createTable('enrollments',true);
    }

    public function down()
    {
        $this->forge->dropTable('enrollments',true);
        $this->forge->dropTable('lessons',true);
        $this->forge->dropTable('course_modules',true);
        $this->forge->dropTable('courses',true);
    }
}
