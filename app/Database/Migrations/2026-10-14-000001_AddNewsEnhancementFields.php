<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNewsEnhancementFields extends Migration
{
    public function up()
    {
        $fields = [];

        if (! $this->db->fieldExists('is_featured', 'news')) {
            $fields['is_featured'] = [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'after'      => 'cover_file_id',
            ];
        }

        if (! $this->db->fieldExists('cover_caption', 'news')) {
            $fields['cover_caption'] = [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
                'after'      => 'cover_file_id',
            ];
        }

        if (! $this->db->fieldExists('reading_time', 'news')) {
            $fields['reading_time'] = [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => null,
                'after'      => 'summary',
            ];
        }

        if (! $this->db->fieldExists('tags', 'news')) {
            $fields['tags'] = [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
                'after'      => 'reading_time',
            ];
        }

        if (! $this->db->fieldExists('source_name', 'news')) {
            $fields['source_name'] = [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'default'    => null,
                'after'      => 'content',
            ];
        }

        if (! $this->db->fieldExists('source_url', 'news')) {
            $fields['source_url'] = [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
                'default'    => null,
                'after'      => 'source_name',
            ];
        }

        if (! empty($fields)) {
            $this->forge->addColumn('news', $fields);
        }
    }

    public function down()
    {
        $cols = ['is_featured', 'cover_caption', 'reading_time', 'tags', 'source_name', 'source_url'];
        foreach ($cols as $col) {
            if ($this->db->fieldExists($col, 'news')) {
                $this->forge->dropColumn('news', $col);
            }
        }
    }
}
