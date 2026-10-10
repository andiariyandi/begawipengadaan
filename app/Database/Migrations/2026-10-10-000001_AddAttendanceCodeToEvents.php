<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAttendanceCodeToEvents extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('attendance_code', 'events')) {
            return;
        }

        $fields = [
            'attendance_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => true,
                'default'    => null,
                'after'      => 'certificate_enabled',
            ],
            'attendance_code_hash' => [
                'type'       => 'CHAR',
                'constraint' => 64,
                'null'       => true,
                'default'    => null,
                'after'      => 'attendance_code',
            ],
            'attendance_open_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
                'after'   => 'attendance_code_hash',
            ],
            'attendance_close_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
                'after'   => 'attendance_open_at',
            ],
        ];

        $this->forge->addColumn('events', $fields);
        $this->db->query('ALTER TABLE `events` ADD INDEX `events_attendance_code_idx` (`attendance_code`)');
    }

    public function down()
    {
        if (! $this->db->fieldExists('attendance_code', 'events')) {
            return;
        }

        $this->forge->dropColumn('events', [
            'attendance_code',
            'attendance_code_hash',
            'attendance_open_at',
            'attendance_close_at',
        ]);
    }
}
