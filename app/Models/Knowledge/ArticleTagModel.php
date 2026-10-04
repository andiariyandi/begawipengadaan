<?php

namespace App\Models\Knowledge;

use CodeIgniter\Model;

class ArticleTagModel extends Model
{
    protected $table         = 'knowledge_article_tags';
    protected $primaryKey    = null;
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['article_id', 'tag_id', 'created_at'];
}