<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIntegrationFoundation extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'=>['type'=>'INT','unsigned'=>true,'auto_increment'=>true],
            'action'=>['type'=>'VARCHAR','constraint'=>100],
            'module'=>['type'=>'VARCHAR','constraint'=>100],
            'user_id'=>['type'=>'INT','null'=>true],
            'status'=>['type'=>'VARCHAR','constraint'=>30,'default'=>'pending'],
            'created_at'=>['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('approval_logs', true);

        $this->forge->addField([
            'id'=>['type'=>'INT','unsigned'=>true,'auto_increment'=>true],
            'title'=>['type'=>'VARCHAR','constraint'=>255],
            'message'=>['type'=>'TEXT'],
            'user_id'=>['type'=>'INT','null'=>true],
            'is_read'=>['type'=>'BOOLEAN','default'=>false],
            'created_at'=>['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('notifications', true);
    }

    public function down()
    {
        $this->forge->dropTable('notifications', true);
        $this->forge->dropTable('approval_logs', true);
    }
}
