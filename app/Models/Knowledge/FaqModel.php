<?php

namespace App\Models\Knowledge;

use CodeIgniter\Model;

/**
 * FAQ diimplementasikan sebagai knowledge_articles.type='faq'
 * sesuai spek. Wrapper ini hanya convenience di atas ArticleModel.
 */
class FaqModel extends Model
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