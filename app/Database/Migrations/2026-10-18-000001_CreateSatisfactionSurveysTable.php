<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSatisfactionSurveysTable extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('satisfaction_surveys')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'BIGINT',
                    'constraint'     => 20,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'ticket_number' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 32,
                ],
                'rating_overall' => [
                    'type'       => 'TINYINT',
                    'constraint' => 2,
                    'default'    => 5,
                ],
                'service_access_rating' => [
                    'type'       => 'TINYINT',
                    'constraint' => 2,
                    'null'       => true,
                ],
                'service_regulation_rating' => [
                    'type'       => 'TINYINT',
                    'constraint' => 2,
                    'null'       => true,
                ],
                'service_learning_rating' => [
                    'type'       => 'TINYINT',
                    'constraint' => 2,
                    'null'       => true,
                ],
                'service_helpdesk_rating' => [
                    'type'       => 'TINYINT',
                    'constraint' => 2,
                    'null'       => true,
                ],
                'respondent_role' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'default'    => 'Masyarakat Umum / Pengguna Layanan',
                ],
                'is_anonymous' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 1,
                ],
                'respondent_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                    'null'       => true,
                ],
                'respondent_institution' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                    'null'       => true,
                ],
                'respondent_contact' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
                'feedback' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'recommendation' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'default'    => 'Pasti Merekomendasikan',
                ],
                'ip_address' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 45,
                    'null'       => true,
                ],
                'user_agent' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);

            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('ticket_number');
            $this->forge->addKey('rating_overall');
            $this->forge->addKey('respondent_role');
            $this->forge->addKey('created_at');

            $this->forge->createTable('satisfaction_surveys', true);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('satisfaction_surveys')) {
            $this->forge->dropTable('satisfaction_surveys', true);
        }
    }
}
