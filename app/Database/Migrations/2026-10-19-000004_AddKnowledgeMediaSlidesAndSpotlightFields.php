<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKnowledgeMediaSlidesAndSpotlightFields extends Migration
{
    public function up()
    {
        // 1. Fields for knowledge_media
        $mediaFields = [];
        if (! $this->db->fieldExists('slides_data', 'knowledge_media')) {
            $mediaFields['slides_data'] = [
                'type' => 'LONGTEXT',
                'null' => true,
                'default' => null,
                'after' => 'file_id',
            ];
        }
        if (! $this->db->fieldExists('cover_url', 'knowledge_media')) {
            $mediaFields['cover_url'] = [
                'type' => 'VARCHAR',
                'constraint' => 1024,
                'null' => true,
                'default' => null,
                'after' => 'slides_data',
            ];
        }
        if (! empty($mediaFields)) {
            $this->forge->addColumn('knowledge_media', $mediaFields);
        }

        // 2. Fields for knowledge_articles
        $articleFields = [];
        if (! $this->db->fieldExists('cover_url', 'knowledge_articles')) {
            $articleFields['cover_url'] = [
                'type' => 'VARCHAR',
                'constraint' => 1024,
                'null' => true,
                'default' => null,
                'after' => 'cover_file_id',
            ];
        }
        if (! $this->db->fieldExists('attachment_file_id', 'knowledge_articles')) {
            $articleFields['attachment_file_id'] = [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => true,
                'default' => null,
                'after' => 'cover_url',
            ];
        }
        if (! empty($articleFields)) {
            $this->forge->addColumn('knowledge_articles', $articleFields);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('slides_data', 'knowledge_media')) {
            $this->forge->dropColumn('knowledge_media', 'slides_data');
        }
        if ($this->db->fieldExists('cover_url', 'knowledge_media')) {
            $this->forge->dropColumn('knowledge_media', 'cover_url');
        }
        if ($this->db->fieldExists('cover_url', 'knowledge_articles')) {
            $this->forge->dropColumn('knowledge_articles', 'cover_url');
        }
        if ($this->db->fieldExists('attachment_file_id', 'knowledge_articles')) {
            $this->forge->dropColumn('knowledge_articles', 'attachment_file_id');
        }
    }
}
