<?php

namespace App\Models\Knowledge;

use CodeIgniter\Model;

class KnowledgeMediaItemModel extends Model
{
    protected $table         = 'knowledge_media';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'article_id', 'type', 'title', 'caption', 'alt_text',
        'file_id', 'external_url', 'thumbnail_file_id',
        'hashtags', 'likes_count', 'views_count', 'shares_count',
        'location', 'event_date', 'duration',
        'workflow_status', 'visibility', 'sort_order',
        'featured', 'carousel_enabled',
        'created_by', 'published_by', 'published_at',
    ];

    public function listMedia(?string $type = null, ?string $status = null, ?string $keyword = null): array
    {
        $builder = $this->db->table('knowledge_media km')
            ->select('km.*, fa.original_name AS file_original_name, fa.mime_type AS file_mime_type, fa.path AS file_path, fa.disk AS file_disk,
                      ft.original_name AS thumb_original_name, ft.mime_type AS thumb_mime_type, ft.path AS thumb_path, ft.disk AS thumb_disk')
            ->join('file_assets fa', 'fa.id = km.file_id', 'left')
            ->join('file_assets ft', 'ft.id = km.thumbnail_file_id', 'left')
            ->orderBy('km.updated_at', 'DESC');
        if ($type !== null && $type !== '') {
            $builder->where('km.type', $type);
        }
        if ($status !== null && $status !== '') {
            $builder->where('km.workflow_status', $status);
        }
        if ($keyword !== null && $keyword !== '') {
            $builder->groupStart()
                ->like('km.title', $keyword)
                ->orLike('km.caption', $keyword)
                ->orLike('km.alt_text', $keyword)
                ->groupEnd();
        }
        return $builder->get()->getResultArray();
    }

    public function detail(int $id): ?array
    {
        $row = $this->db->table('knowledge_media km')
            ->select('km.*, fa.original_name AS file_original_name, fa.mime_type AS file_mime_type, fa.path AS file_path, fa.disk AS file_disk, ft.original_name AS thumb_original_name, ft.mime_type AS thumb_mime_type, ft.path AS thumb_path, ft.disk AS thumb_disk')
            ->join('file_assets fa', 'fa.id = km.file_id', 'left')
            ->join('file_assets ft', 'ft.id = km.thumbnail_file_id', 'left')
            ->where('km.id', $id)
            ->get()->getRowArray();
        return $row ?: null;
    }
}