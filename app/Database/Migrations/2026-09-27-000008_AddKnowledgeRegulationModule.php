<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKnowledgeRegulationModule extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'=>['type'=>'INT','unsigned'=>true,'auto_increment'=>true],
            'title'=>['type'=>'VARCHAR','constraint'=>255],
            'content'=>['type'=>'LONGTEXT','null'=>true],
            'category_id'=>['type'=>'INT','null'=>true],
            'status'=>['type'=>'VARCHAR','constraint'=>20,'default'=>'draft'],
            'created_at'=>['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id',true);
        $this->forge->createTable('articles',true);

        $this->forge->addField([
            'id'=>['type'=>'INT','unsigned'=>true,'auto_increment'=>true],
            'question'=>['type'=>'TEXT'],
            'answer'=>['type'=>'TEXT'],
            'status'=>['type'=>'VARCHAR','constraint'=>20,'default'=>'publish'],
        ]);
        $this->forge->addKey('id',true);
        $this->forge->createTable('faqs',true);

        $this->forge->addField([
            'id'=>['type'=>'INT','unsigned'=>true,'auto_increment'=>true],
            'title'=>['type'=>'VARCHAR','constraint'=>255],
            'file'=>['type'=>'VARCHAR','constraint'=>255,'null'=>true],
            'keyword'=>['type'=>'TEXT','null'=>true],
        ]);
        $this->forge->addKey('id',true);
        $this->forge->createTable('regulations',true);
    }

    public function down()
    {
        $this->forge->dropTable('regulations',true);
        $this->forge->dropTable('faqs',true);
        $this->forge->dropTable('articles',true);
    }
}
