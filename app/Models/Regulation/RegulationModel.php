<?php

namespace App\Models\Regulation;

use CodeIgniter\Model;

class RegulationModel extends Model
{
    protected $table         = 'regulations';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'category_id', 'title', 'slug', 'document_number', 'document_year',
        'issuer_name', 'official_date', 'effective_date', 'summary',
        'official_source_url', 'workflow_status', 'legal_status', 'visibility',
        'current_version_id', 'created_by', 'reviewed_by', 'reviewed_at',
        'published_by', 'published_at', 'archived_at',
    ];

    /**
     * Lightweight dropdown listing used by relation-create form & similar.
     * Returns id+title only, sorted by title, excluding soft-deleted rows.
     */
    public function listAll(): array
    {
        return $this->select('id, title, document_number')
            ->where('deleted_at', null)
            ->orderBy('title', 'ASC')
            ->findAll();
    }

    public function listForAdmin(?int $categoryId = null, ?string $keyword = null, ?string $workflowStatus = null): array
    {
        $builder = $this->select('regulations.*, regulation_categories.name AS category_name,
                                   u_creator.username AS creator_name,
                                   v.version_number AS current_version_number')
            ->join('regulation_categories', 'regulation_categories.id = regulations.category_id', 'left')
            ->join('users u_creator', 'u_creator.id = regulations.created_by', 'left')
            ->join('regulation_versions v', 'v.id = regulations.current_version_id', 'left')
            ->orderBy('regulations.created_at', 'DESC');

        if ($categoryId) {
            $builder->where('regulations.category_id', $categoryId);
        }
        if ($workflowStatus) {
            $builder->where('regulations.workflow_status', $workflowStatus);
        }
        if ($keyword) {
            $builder->groupStart()
                ->like('regulations.title', $keyword)
                ->orLike('regulations.document_number', $keyword)
                ->groupEnd();
        }

        return $builder->findAll();
    }

    public function withDetail(int $id): ?array
    {
        $row = $this->select('regulations.*, regulation_categories.name AS category_name,
                              u_creator.username AS creator_name,
                              u_reviewer.username AS reviewer_name,
                              u_publisher.username AS publisher_name,
                              v.version_number AS current_version_number,
                              v.file_id AS current_file_id')
            ->join('regulation_categories', 'regulation_categories.id = regulations.category_id', 'left')
            ->join('users u_creator', 'u_creator.id = regulations.created_by', 'left')
            ->join('users u_reviewer', 'u_reviewer.id = regulations.reviewed_by', 'left')
            ->join('users u_publisher', 'u_publisher.id = regulations.published_by', 'left')
            ->join('regulation_versions v', 'v.id = regulations.current_version_id', 'left')
            ->where('regulations.id', $id)
            ->first();

        if (! $row) {
            return null;
        }

        $row['versions'] = (new RegulationVersionModel())->historyFor($id);
        return $row;
    }
}