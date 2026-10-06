<?php

namespace App\Models;

use CodeIgniter\Model;

class NewsModel extends Model
{
    protected $table = 'news';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'title',
        'slug',
        'content',
        'status',
        'published_at'
    ];
}
