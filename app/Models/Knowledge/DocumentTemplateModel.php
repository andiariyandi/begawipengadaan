<?php

namespace App\Models\Knowledge;

use CodeIgniter\Model;

/**
 * Template dokumen: knowledge_articles.type='template' dengan file_id.
 * (Schema knowledge_articles hanya punya cover_file_id, jadi template
 * menggunakan field cover_file_id sebagai penampung lampiran file.
 * Sesuai spec: file_assets dipakai untuk seluruh upload.)
 */
class DocumentTemplateModel extends Model
{
    protected $table         = 'knowledge_articles';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $allowedFields = [
        'type', 'title', 'slug', 'summary', 'content',
        'workflow_status', 'visibility',
        'featured', 'carousel_enabled', 'carousel_order',
        'cover_file_id',
        'author_id', 'reviewed_by', 'published_by',
        'published_at', 'archived_at',
    ];
}