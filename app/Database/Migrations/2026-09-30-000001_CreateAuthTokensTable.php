<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAuthTokensTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'email' => ['type' => 'VARCHAR', 'constraint' => 150],
            'token_type' => ['type' => 'VARCHAR', 'constraint' => 32],
            'token_hash' => ['type' => 'CHAR', 'constraint' => 64],
            'payload' => ['type' => 'JSON', 'null' => true],
            'expires_at' => ['type' => 'DATETIME'],
            'used_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['token_type', 'token_hash']);
        $this->forge->addKey(['email', 'token_type']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('auth_tokens');
    }

    public function down()
    {
        $this->forge->dropTable('auth_tokens', true);
    }
}
