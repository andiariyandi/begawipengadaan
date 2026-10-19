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
        'issuer_name', 'official_date', 'effective_date', 'summary', 'keywords',
        'official_source_url', 'workflow_status', 'legal_status', 'visibility',
        'is_featured', 'carousel_enabled', 'carousel_order', 'current_version_id', 'cover_file_id', 'cover_image_path',
        'created_by', 'reviewed_by', 'reviewed_at',
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

    public function listForAdmin(?int $categoryId = null, ?string $keyword = null, ?string $workflowStatus = null, ?string $legalStatus = null, ?int $isFeatured = null): array
    {
        $builder = $this->select('regulations.*, regulation_categories.name AS category_name,
                                   u_creator.username AS creator_name,
                                   v.version_number AS current_version_number,
                                   fa_cover.path AS cover_path,
                                   fa_cover.stored_name AS cover_stored_name')
            ->join('regulation_categories', 'regulation_categories.id = regulations.category_id', 'left')
            ->join('users u_creator', 'u_creator.id = regulations.created_by', 'left')
            ->join('regulation_versions v', 'v.id = regulations.current_version_id', 'left')
            ->join('file_assets fa_cover', 'fa_cover.id = regulations.cover_file_id', 'left')
            ->orderBy('regulations.created_at', 'DESC');

        if ($categoryId) {
            $builder->where('regulations.category_id', $categoryId);
        }
        if ($workflowStatus) {
            $builder->where('regulations.workflow_status', $workflowStatus);
        }
        if ($legalStatus) {
            $builder->where('regulations.legal_status', $legalStatus);
        }
        if ($isFeatured !== null) {
            $builder->where('regulations.is_featured', $isFeatured);
        }
        if ($keyword) {
            $builder->groupStart()
                ->like('regulations.title', $keyword)
                ->orLike('regulations.document_number', $keyword)
                ->orLike('regulations.summary', $keyword)
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
                              v.file_id AS current_file_id,
                              fa_cover.path AS cover_path,
                              fa_cover.stored_name AS cover_stored_name')
            ->join('regulation_categories', 'regulation_categories.id = regulations.category_id', 'left')
            ->join('users u_creator', 'u_creator.id = regulations.created_by', 'left')
            ->join('users u_reviewer', 'u_reviewer.id = regulations.reviewed_by', 'left')
            ->join('users u_publisher', 'u_publisher.id = regulations.published_by', 'left')
            ->join('regulation_versions v', 'v.id = regulations.current_version_id', 'left')
            ->join('file_assets fa_cover', 'fa_cover.id = regulations.cover_file_id', 'left')
            ->where('regulations.id', $id)
            ->first();

        if (! $row) {
            return null;
        }

        $row['versions'] = (new RegulationVersionModel())->historyFor($id);
        
        // Attached procurement stages
        $row['stages'] = db_connect()->table('regulation_procurement_stages rps')
            ->select('rps.*, ps.name, ps.key')
            ->join('procurement_stages ps', 'ps.id = rps.procurement_stage_id', 'left')
            ->where('rps.regulation_id', $id)
            ->orderBy('ps.sort_order', 'ASC')
            ->get()->getResultArray();

        return $row;
    }
}