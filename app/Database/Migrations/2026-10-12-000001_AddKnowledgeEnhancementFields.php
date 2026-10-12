<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKnowledgeEnhancementFields extends Migration
{
    public function up()
    {
        // 1. Fields for knowledge_articles
        $articleFields = [];

        if (! $this->db->fieldExists('reading_time', 'knowledge_articles')) {
            $articleFields['reading_time'] = [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'default'    => 5,
                'null'       => true,
                'after'      => 'content',
            ];
        }

        if (! $this->db->fieldExists('source_url', 'knowledge_articles')) {
            $articleFields['source_url'] = [
                'type'       => 'VARCHAR',
                'constraint' => 1024,
                'null'       => true,
                'default'    => null,
                'after'      => 'content',
            ];
        }

        if (! $this->db->fieldExists('organization_unit', 'knowledge_articles')) {
            $articleFields['organization_unit'] = [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
                'after'      => 'content',
            ];
        }

        if (! $this->db->fieldExists('procurement_stage', 'knowledge_articles')) {
            $articleFields['procurement_stage'] = [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'after'      => 'content',
            ];
        }

        if (! $this->db->fieldExists('case_severity', 'knowledge_articles')) {
            $articleFields['case_severity'] = [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => null,
                'after'      => 'content',
            ];
        }

        if (! $this->db->fieldExists('resolution_summary', 'knowledge_articles')) {
            $articleFields['resolution_summary'] = [
                'type'    => 'TEXT',
                'null'    => true,
                'default' => null,
                'after'   => 'content',
            ];
        }

        if (! $this->db->fieldExists('legal_basis', 'knowledge_articles')) {
            $articleFields['legal_basis'] = [
                'type'    => 'TEXT',
                'null'    => true,
                'default' => null,
                'after'   => 'content',
            ];
        }

        if (! $this->db->fieldExists('sort_order', 'knowledge_articles')) {
            $articleFields['sort_order'] = [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'default'    => 0,
                'after'      => 'carousel_order',
            ];
        }

        if (! $this->db->fieldExists('document_category', 'knowledge_articles')) {
            $articleFields['document_category'] = [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'after'      => 'content',
            ];
        }

        if (! $this->db->fieldExists('document_version', 'knowledge_articles')) {
            $articleFields['document_version'] = [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => 'v1.0',
                'after'      => 'content',
            ];
        }

        if (! empty($articleFields)) {
            $this->forge->addColumn('knowledge_articles', $articleFields);
        }

        // 2. Fields for knowledge_media
        $mediaFields = [];

        if (! $this->db->fieldExists('hashtags', 'knowledge_media')) {
            $mediaFields['hashtags'] = [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
                'default'    => null,
                'after'      => 'caption',
            ];
        }

        if (! $this->db->fieldExists('likes_count', 'knowledge_media')) {
            $mediaFields['likes_count'] = [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'default'    => 0,
                'after'      => 'sort_order',
            ];
        }

        if (! $this->db->fieldExists('views_count', 'knowledge_media')) {
            $mediaFields['views_count'] = [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'default'    => 0,
                'after'      => 'likes_count',
            ];
        }

        if (! $this->db->fieldExists('shares_count', 'knowledge_media')) {
            $mediaFields['shares_count'] = [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'default'    => 0,
                'after'      => 'views_count',
            ];
        }

        if (! $this->db->fieldExists('location', 'knowledge_media')) {
            $mediaFields['location'] = [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
                'after'      => 'caption',
            ];
        }

        if (! $this->db->fieldExists('event_date', 'knowledge_media')) {
            $mediaFields['event_date'] = [
                'type'    => 'DATE',
                'null'    => true,
                'default' => null,
                'after'   => 'location',
            ];
        }

        if (! $this->db->fieldExists('duration', 'knowledge_media')) {
            $mediaFields['duration'] = [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => null,
                'after'      => 'external_url',
            ];
        }

        if (! empty($mediaFields)) {
            $this->forge->addColumn('knowledge_media', $mediaFields);
        }
    }

    public function down()
    {
        $articleCols = [
            'reading_time', 'source_url', 'organization_unit', 'procurement_stage',
            'case_severity', 'resolution_summary', 'legal_basis', 'sort_order',
            'document_category', 'document_version',
        ];
        foreach ($articleCols as $col) {
            if ($this->db->fieldExists($col, 'knowledge_articles')) {
                $this->forge->dropColumn('knowledge_articles', $col);
            }
        }

        $mediaCols = [
            'hashtags', 'likes_count', 'views_count', 'shares_count',
            'location', 'event_date', 'duration',
        ];
        foreach ($mediaCols as $col) {
            if ($this->db->fieldExists($col, 'knowledge_media')) {
                $this->forge->dropColumn('knowledge_media', $col);
            }
        }
    }
}
