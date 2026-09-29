<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCommunicationEventModule extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'=>['type'=>'INT','unsigned'=>true,'auto_increment'=>true],
            'title'=>['type'=>'VARCHAR','constraint'=>255],
            'slug'=>['type'=>'VARCHAR','constraint'=>255],
            'content'=>['type'=>'LONGTEXT','null'=>true],
            'status'=>['type'=>'VARCHAR','constraint'=>20,'default'=>'draft'],
            'published_at'=>['type'=>'DATETIME','null'=>true],
            'created_at'=>['type'=>'DATETIME','null'=>true],
            'updated_at'=>['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('news', true);

        $this->forge->addField([
            'id'=>['type'=>'INT','unsigned'=>true,'auto_increment'=>true],
            'title'=>['type'=>'VARCHAR','constraint'=>255],
            'description'=>['type'=>'TEXT','null'=>true],
            'start_date'=>['type'=>'DATETIME'],
            'end_date'=>['type'=>'DATETIME','null'=>true],
            'quota'=>['type'=>'INT','default'=>0],
            'status'=>['type'=>'VARCHAR','constraint'=>30,'default'=>'draft'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('events', true);

        $this->forge->addField([
            'id'=>['type'=>'INT','unsigned'=>true,'auto_increment'=>true],
            'event_id'=>['type'=>'INT'],
            'user_id'=>['type'=>'INT'],
            'status'=>['type'=>'VARCHAR','constraint'=>20,'default'=>'registered'],
            'registered_at'=>['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('event_registrations', true);

        $this->forge->addField([
            'id'=>['type'=>'INT','unsigned'=>true,'auto_increment'=>true],
            'registration_id'=>['type'=>'INT'],
            'attendance_time'=>['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('attendance', true);
    }

    public function down()
    {
        $this->forge->dropTable('attendance', true);
        $this->forge->dropTable('event_registrations', true);
        $this->forge->dropTable('events', true);
        $this->forge->dropTable('news', true);
    }
}
