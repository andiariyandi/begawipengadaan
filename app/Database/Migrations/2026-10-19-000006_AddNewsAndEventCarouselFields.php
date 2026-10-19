<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNewsAndEventCarouselFields extends Migration
{
    public function up()
    {
        // 1. Table news
        $newsFields = [];
        if (! $this->db->fieldExists('carousel_enabled', 'news')) {
            $newsFields['carousel_enabled'] = [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 0,
                'after'      => 'is_featured',
            ];
        }
        if (! $this->db->fieldExists('carousel_order', 'news')) {
            $newsFields['carousel_order'] = [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
                'after'      => 'carousel_enabled',
            ];
        }
        if (! empty($newsFields)) {
            $this->forge->addColumn('news', $newsFields);
        }

        // 2. Table events
        $eventFields = [];
        if (! $this->db->fieldExists('is_featured', 'events')) {
            $eventFields['is_featured'] = [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 0,
                'after'      => 'cover_file_id',
            ];
        }
        if (! $this->db->fieldExists('carousel_enabled', 'events')) {
            $eventFields['carousel_enabled'] = [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 0,
                'after'      => 'is_featured',
            ];
        }
        if (! $this->db->fieldExists('carousel_order', 'events')) {
            $eventFields['carousel_order'] = [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
                'after'      => 'carousel_enabled',
            ];
        }
        if (! empty($eventFields)) {
            $this->forge->addColumn('events', $eventFields);
        }
    }

    public function down()
    {
        $newsToDrop = [];
        if ($this->db->fieldExists('carousel_order', 'news')) {
            $newsToDrop[] = 'carousel_order';
        }
        if ($this->db->fieldExists('carousel_enabled', 'news')) {
            $newsToDrop[] = 'carousel_enabled';
        }
        if (! empty($newsToDrop)) {
            $this->forge->dropColumn('news', $newsToDrop);
        }

        $eventsToDrop = [];
        if ($this->db->fieldExists('carousel_order', 'events')) {
            $eventsToDrop[] = 'carousel_order';
        }
        if ($this->db->fieldExists('carousel_enabled', 'events')) {
            $eventsToDrop[] = 'carousel_enabled';
        }
        if ($this->db->fieldExists('is_featured', 'events')) {
            $eventsToDrop[] = 'is_featured';
        }
        if (! empty($eventsToDrop)) {
            $this->forge->dropColumn('events', $eventsToDrop);
        }
    }
}
