<?php

namespace App\Services\Regulation;

use App\Models\Regulation\CompetencyModel;
use App\Models\Regulation\ProcurementStageModel;
use App\Models\Regulation\RegulationCategoryModel;
use App\Models\Regulation\RegulationModel;

/**
 * Public landing service for Regulasi PBJ.
 *
 * Single source of truth for: listing, stats counters, detail assembly,
 * search. Kept thin — every method returns either plain arrays or null so
 * views never crash on empty data.
 *
 * BUG-003 fix: stats counts come from the same filtered source as listing
 * (published + public + not soft-deleted), not a separate count query.
 */
class RegulationPublicService
{
    public function __construct(
        private readonly RegulationModel $regulations = new RegulationModel(),
        private readonly RegulationCategoryModel $categories = new RegulationCategoryModel(),
        private readonly CompetencyModel $competencies = new CompetencyModel(),
        private readonly ProcurementStageModel $stages = new ProcurementStageModel(),
    ) {}

    /**
     * Public listing with search + category filter + pagination.
     */
    public function list(int $page = 1, int $perPage = 9, ?int $categoryId = null, ?string $keyword = null): array
    {
        // Use raw query builder — RegulationModel auto-injects soft-delete
        // and other select fields via getCompiledSelect() which conflicts
        // with our explicit scope when chaining Model->select()->where().
        $db = db_connect();
        $builder = $db->table('regulations r')
            ->select('r.id, r.title, r.slug, r.document_number, r.document_year,
                      r.issuer_name, r.summary, r.workflow_status, r.visibility,
                      r.published_at, r.published_by, r.created_at,
                      rc.name AS category_name,
                      v.version_number AS current_version_number')
            ->join('regulation_categories rc', 'rc.id = r.category_id', 'left')
            ->join('regulation_versions v', 'v.id = r.current_version_id', 'left')
            ->where('r.workflow_status', 'published')
            ->where('r.visibility', 'public')
            ->where('r.deleted_at', null)
            ->orderBy('r.published_at', 'DESC');

        if ($categoryId) {
            $builder->where('r.category_id', $categoryId);
        }

        if ($keyword !== null && $keyword !== '') {
            $builder->groupStart()
                ->like('r.title', $keyword)
                ->orLike('r.document_number', $keyword)
                ->orLike('r.summary', $keyword)
                ->orLike('r.issuer_name', $keyword)
                ->groupEnd();
        }

        $total = (clone $builder)->countAllResults();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));

        $items = $builder->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();

        return [
            'items'      => $items,
            'total'      => $total,
            'page'       => $page,
            'totalPages' => $totalPages,
            'perPage'    => $perPage,
        ];
    }

    /**
     * Statistics counters — derived from the SAME base scope as list()
     * so the counters never disagree with the listing (BUG-003).
     */
    public function stats(): array
    {
        $db = db_connect();
        $totalPublished = (int) $db->table('regulations r')
            ->where('r.workflow_status', 'published')
            ->where('r.visibility', 'public')
            ->where('r.deleted_at', null)
            ->countAllResults();

        return [
            'total'      => $totalPublished,
            'categories' => (clone $this->categories)->where('is_active', 1)->countAllResults(),
            'published'  => $totalPublished,
            'downloaded' => (int) $db->table('regulation_downloads')->countAllResults(),
        ];
    }

    /**
     * Categories with their regulation_count for the chip sidebar.
     */
    public function categoriesWithCount(): array
    {
        return $this->categories
            ->select('regulation_categories.*, COUNT(r.id) AS regulation_count')
            ->join('regulations r',
                'r.category_id = regulation_categories.id AND r.deleted_at IS NULL
                 AND r.workflow_status = "published" AND r.visibility = "public"',
                'left')
            ->where('regulation_categories.is_active', 1)
            ->groupBy('regulation_categories.id')
            ->orderBy('regulation_categories.name')
            ->findAll();
    }

    public function stages(): array
    {
        return $this->stages->listActive();
    }

    public function competencies(): array
    {
        return $this->competencies->listActive();
    }

    /**
     * Top N regulations by download count (BUG-006 supportive data).
     */
    public function popular(int $limit = 3): array
    {
        return db_connect()->table('regulations r')
            ->select('r.id, r.slug, r.title, r.document_number, r.document_year,
                      rc.name AS category_name, COUNT(rd.id) AS download_count')
            ->join('regulation_downloads rd', 'rd.regulation_id = r.id', 'left')
            ->join('regulation_categories rc', 'rc.id = r.category_id', 'left')
            ->where('r.workflow_status', 'published')
            ->where('r.visibility', 'public')
            ->where('r.deleted_at', null)
            ->groupBy('r.id')
            ->orderBy('download_count', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }

    /**
     * Single regulation detail. Returns null if not published+public.
     */
    public function detail(string $slug): ?array
    {
        $regulation = $this->regulations->select('regulations.*, rc.name AS category_name,
                                                v.version_number AS current_version_number,
                                                v.file_id AS current_file_id,
                                                v.changelog AS current_changelog,
                                                v.created_at AS current_version_created_at,
                                                fa.original_name AS current_file_name,
                                                fa.size_bytes AS current_file_size,
                                                fa.mime_type AS current_file_mime,
                                                fa.extension AS current_file_ext')
            ->join('regulation_categories rc', 'rc.id = regulations.category_id', 'left')
            ->join('regulation_versions v', 'v.id = regulations.current_version_id', 'left')
            ->join('file_assets fa', 'fa.id = v.file_id', 'left')
            ->where('regulations.slug', $slug)
            ->where('regulations.workflow_status', 'published')
            ->where('regulations.visibility', 'public')
            ->where('regulations.deleted_at', null)
            ->first();

        return $regulation ?: null;
    }

    /**
     * All versions of a regulation (history).
     */
    public function versions(int $regulationId): array
    {
        return db_connect()->table('regulation_versions v')
            ->select('v.*, v.changelog, fa.original_name, fa.size_bytes, fa.mime_type')
            ->join('file_assets fa', 'fa.id = v.file_id', 'left')
            ->where('v.regulation_id', $regulationId)
            ->orderBy('v.version_number', 'DESC')
            ->get()->getResultArray();
    }

    /**
     * All relations for a regulation (both directions).
     * Uses 2 simple queries to avoid CASE-with-? binding issue
     * (BUG-001 root cause was: BaseBuilder cannot bind ? positional inside CASE).
     */
    public function relations(int $regulationId): array
    {
        $db = db_connect();

        // Outgoing: source = $regulationId
        $out = $db->table('regulation_relations rr')
            ->select('rr.relation_type, rr.notes, "outgoing" AS direction,
                      r_other.id AS related_id, r_other.slug AS related_slug,
                      r_other.title AS related_title, r_other.document_number AS related_document_number,
                      r_other.workflow_status AS related_workflow_status')
            ->join('regulations r_other', 'r_other.id = rr.target_regulation_id', 'left')
            ->where('rr.source_regulation_id', $regulationId)
            ->where('r_other.deleted_at', null)
            ->get()->getResultArray();

        // Incoming: target = $regulationId
        $inc = $db->table('regulation_relations rr')
            ->select('rr.relation_type, rr.notes, "incoming" AS direction,
                      r_other.id AS related_id, r_other.slug AS related_slug,
                      r_other.title AS related_title, r_other.document_number AS related_document_number,
                      r_other.workflow_status AS related_workflow_status')
            ->join('regulations r_other', 'r_other.id = rr.source_regulation_id', 'left')
            ->where('rr.target_regulation_id', $regulationId)
            ->where('r_other.deleted_at', null)
            ->get()->getResultArray();

        return array_merge($out, $inc);
    }

    /**
     * Competencies attached to a regulation via pivot table.
     */
    public function competenciesForRegulation(int $regulationId): array
    {
        return db_connect()->table('regulation_competencies rc')
            ->select('c.id, c.key, c.name')
            ->join('competencies c', 'c.id = rc.competency_id')
            ->where('rc.regulation_id', $regulationId)
            ->get()->getResultArray();
    }

    /**
     * Other regulations in the same category (related sidebar).
     */
    public function relatedRegulations(int $regulationId, int $categoryId, int $limit = 3): array
    {
        return $this->regulations
            ->select('id, slug, title, document_number, document_year')
            ->where('category_id', $categoryId)
            ->where('id !=', $regulationId)
            ->where('workflow_status', 'published')
            ->where('visibility', 'public')
            ->where('deleted_at', null)
            ->orderBy('published_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Lookup the current version + its file_assets row, used by download/preview.
     */
    public function currentVersion(int $regulationId): ?array
    {
        $row = db_connect()->table('regulation_versions v')
            ->select('v.*, fa.id AS file_id, fa.path, fa.original_name, fa.size_bytes, fa.mime_type, fa.extension')
            ->join('file_assets fa', 'fa.id = v.file_id', 'left')
            ->where('v.regulation_id', $regulationId)
            ->where('v.is_current', 1)
            ->get()->getRowArray();
        return $row ?: null;
    }

    /**
     * Categories list (simple dropdown for filter).
     */
    public function categoriesActive(): array
    {
        return $this->categories
            ->where('is_active', 1)
            ->orderBy('name')
            ->findAll();
    }

    /**
     * Find category by slug — null if not found (BUG-004).
     */
    public function categoryBySlug(string $slug): ?array
    {
        return $this->categories
            ->where('slug', $slug)
            ->where('is_active', 1)
            ->first() ?: null;
    }
}