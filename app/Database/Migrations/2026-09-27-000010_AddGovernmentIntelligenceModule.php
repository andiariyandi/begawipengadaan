<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGovernmentIntelligenceModule extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'=>['type'=>'INT','unsigned'=>true,'auto_increment'=>true],
            'name'=>['type'=>'VARCHAR','constraint'=>255],
            'code'=>['type'=>'VARCHAR','constraint'=>50],
            'type'=>['type'=>'VARCHAR','constraint'=>50,'default'=>'opd'],
            'status'=>['type'=>'VARCHAR','constraint'=>20,'default'=>'active'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('organization_units', true);

        $this->forge->addField([
            'id'=>['type'=>'INT','unsigned'=>true,'auto_increment'=>true],
            'user_id'=>['type'=>'INT'],
            'organization_id'=>['type'=>'INT','null'=>true],
            'position_level'=>['type'=>'VARCHAR','constraint'=>100,'null'=>true],
            'status'=>['type'=>'VARCHAR','constraint'=>20,'default'=>'active'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('ppbj_personnel', true);

        $this->forge->addField([
            'id'=>['type'=>'INT','unsigned'=>true,'auto_increment'=>true],
            'personnel_id'=>['type'=>'INT'],
            'change_type'=>['type'=>'VARCHAR','constraint'=>50],
            'description'=>['type'=>'TEXT','null'=>true],
            'created_at'=>['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('ppbj_history', true);

        $this->forge->addField([
            'id'=>['type'=>'INT','unsigned'=>true,'auto_increment'=>true],
            'personnel_id'=>['type'=>'INT'],
            'certificate_type'=>['type'=>'VARCHAR','constraint'=>100],
            'certificate_number'=>['type'=>'VARCHAR','constraint'=>100],
            'issued_at'=>['type'=>'DATE','null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('competency_certificates', true);

        $this->forge->addField([
            'id'=>['type'=>'INT','unsigned'=>true,'auto_increment'=>true],
            'metric'=>['type'=>'VARCHAR','constraint'=>100],
            'value'=>['type'=>'DECIMAL','constraint'=>'12,2'],
            'snapshot_date'=>['type'=>'DATE'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('statistic_snapshots', true);
    }

    public function down()
    {
        $this->forge->dropTable('statistic_snapshots', true);
        $this->forge->dropTable('competency_certificates', true);
        $this->forge->dropTable('ppbj_history', true);
        $this->forge->dropTable('ppbj_personnel', true);
        $this->forge->dropTable('organization_units', true);
    }
}
