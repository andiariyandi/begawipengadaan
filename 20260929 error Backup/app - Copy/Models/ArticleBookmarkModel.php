<?php

namespace App\Models;

use CodeIgniter\Model;

class ArticleBookmarkModel extends Model
{
    protected $table         = 'article_bookmarks';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false; // hanya created_at, dikelola manual saat insert
    protected $allowedFields = ['user_id', 'article_id', 'created_at'];

    public function isBookmarked(int $userId, int $articleId): bool
    {
        return (bool) $this->where('user_id', $userId)->where('article_id', $articleId)->first();
    }

    /**
     * Toggle bookmark. Mengembalikan status baru (true = sekarang di-bookmark).
     */
    public function toggle(int $userId, int $articleId): bool
    {
        $existing = $this->where('user_id', $userId)->where('article_id', $articleId)->first();

        if ($existing) {
            $this->delete($existing['id']);
            return false;
        }

        $this->insert([
            'user_id'    => $userId,
            'article_id' => $articleId,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return true;
    }

    public function myArticles(int $userId): array
    {
        return $this->select('articles.*, categories.name AS category_name, article_bookmarks.created_at AS bookmarked_at')
            ->join('articles', 'articles.id = article_bookmarks.article_id')
            ->join('categories', 'categories.id = articles.category_id')
            ->where('article_bookmarks.user_id', $userId)
            ->orderBy('article_bookmarks.created_at', 'DESC')
            ->findAll();
    }
}
