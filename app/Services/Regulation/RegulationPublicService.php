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
     * Public listing with search + category filter + facets + sorting + pagination.
     */
    public function list(
        int $page = 1,
        int $perPage = 9,
        ?int $categoryId = null,
        ?string $keyword = null,
        ?string $legalStatus = null,
        ?int $year = null,
        ?int $stageId = null,
        string $sort = 'newest'
    ): array {
        $db = db_connect();
        $builder = $db->table('regulations r')
            ->select('r.id, r.title, r.slug, r.document_number, r.document_year,
                      r.issuer_name, r.official_date, r.effective_date, r.summary,
                      r.official_source_url, r.legal_status, r.workflow_status, r.visibility,
                      r.cover_file_id, r.cover_image_path, r.is_featured, r.carousel_enabled, r.carousel_order,
                      r.published_at, r.published_by, r.created_at,
                      rc.id AS category_id, rc.name AS category_name, rc.slug AS category_slug,
                      v.version_number AS current_version_number, v.file_id AS current_file_id,
                      fa.size_bytes AS file_size, fa.extension AS file_ext, fa.mime_type AS file_mime,
                      (SELECT COUNT(rd.id) FROM regulation_downloads rd WHERE rd.regulation_id = r.id) AS download_count')
            ->join('regulation_categories rc', 'rc.id = r.category_id', 'left')
            ->join('regulation_versions v', 'v.id = r.current_version_id', 'left')
            ->join('file_assets fa', 'fa.id = v.file_id', 'left')
            ->where('r.workflow_status', 'published')
            ->where('r.visibility', 'public')
            ->where('r.deleted_at', null);

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

        if ($legalStatus !== null && $legalStatus !== '') {
            if ($legalStatus === 'active') {
                $builder->whereIn('r.legal_status', ['active', 'berlaku']);
            } elseif ($legalStatus === 'amended') {
                $builder->whereIn('r.legal_status', ['amended', 'diubah']);
            } elseif ($legalStatus === 'revoked') {
                $builder->whereIn('r.legal_status', ['revoked', 'dicabut']);
            } elseif ($legalStatus === 'expired') {
                $builder->whereIn('r.legal_status', ['expired', 'kedaluwarsa']);
            } else {
                $builder->where('r.legal_status', $legalStatus);
            }
        }

        if ($year !== null && $year > 0) {
            $builder->where('r.document_year', $year);
        }

        if ($stageId !== null && $stageId > 0) {
            $builder->where("EXISTS (SELECT 1 FROM regulation_procurement_stages rps WHERE rps.regulation_id = r.id AND rps.procurement_stage_id = " . (int) $stageId . ")", null, false);
        }

        // Sorting
        match ($sort) {
            'year_desc' => $builder->orderBy('r.document_year', 'DESC')->orderBy('r.document_number', 'DESC'),
            'downloads' => $builder->orderBy('download_count', 'DESC')->orderBy('r.published_at', 'DESC'),
            'title'     => $builder->orderBy('r.title', 'ASC'),
            default     => $builder->orderBy('r.published_at', 'DESC')->orderBy('r.created_at', 'DESC'),
        };

        $total = (clone $builder)->countAllResults();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));

        $items = $builder->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();

        // Attach procurement stages to each regulation item
        if (! empty($items)) {
            $regIds = array_column($items, 'id');
            $stagesRows = $db->table('regulation_procurement_stages rps')
                ->select('rps.regulation_id, ps.id AS stage_id, ps.name AS stage_name')
                ->join('procurement_stages ps', 'ps.id = rps.procurement_stage_id')
                ->whereIn('rps.regulation_id', $regIds)
                ->get()->getResultArray();

            $stageMap = [];
            foreach ($stagesRows as $sr) {
                $stageMap[(int) $sr['regulation_id']][] = [
                    'id'   => (int) $sr['stage_id'],
                    'name' => $sr['stage_name'],
                ];
            }

            foreach ($items as &$it) {
                $it['stages'] = $stageMap[(int) $it['id']] ?? [];
            }
            unset($it);
        }

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
        $base = $db->table('regulations r')
            ->where('r.workflow_status', 'published')
            ->where('r.visibility', 'public')
            ->where('r.deleted_at', null);

        $totalPublished = (clone $base)->countAllResults();
        $activeCount = (clone $base)->whereIn('r.legal_status', ['active', 'berlaku'])->countAllResults();
        $amendedCount = (clone $base)->whereIn('r.legal_status', ['amended', 'diubah'])->countAllResults();
        $revokedCount = (clone $base)->whereIn('r.legal_status', ['revoked', 'dicabut'])->countAllResults();

        return [
            'total'         => $totalPublished,
            'categories'    => (clone $this->categories)->where('is_active', 1)->countAllResults(),
            'published'     => $totalPublished,
            'downloaded'    => (int) $db->table('regulation_downloads')->countAllResults(),
            'active_count'  => $activeCount,
            'amended_count' => $amendedCount,
            'revoked_count' => $revokedCount,
        ];
    }

    /**
     * Distinct document years for the filter dropdown.
     */
    public function distinctYears(): array
    {
        $rows = db_connect()->table('regulations r')
            ->select('r.document_year')
            ->distinct()
            ->where('r.workflow_status', 'published')
            ->where('r.visibility', 'public')
            ->where('r.deleted_at', null)
            ->where('r.document_year IS NOT NULL', null, false)
            ->orderBy('r.document_year', 'DESC')
            ->get()->getResultArray();

        return array_values(array_filter(array_column($rows, 'document_year')));
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
    public function popular(int $limit = 4): array
    {
        return db_connect()->table('regulations r')
            ->select('r.id, r.slug, r.title, r.document_number, r.document_year, r.legal_status,
                      r.issuer_name, r.official_date, r.effective_date, r.summary,
                      r.cover_file_id, r.cover_image_path, r.is_featured, r.carousel_enabled, r.carousel_order,
                      rc.name AS category_name,
                      (SELECT COUNT(rd.id) FROM regulation_downloads rd WHERE rd.regulation_id = r.id) AS download_count')
            ->join('regulation_categories rc', 'rc.id = r.category_id', 'left')
            ->where('r.workflow_status', 'published')
            ->where('r.visibility', 'public')
            ->where('r.deleted_at', null)
            ->orderBy('download_count', 'DESC')
            ->orderBy('r.published_at', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }

    /**
     * Fetch regulations for the landing page hero headline carousel based on spotlight mode & limit.
     */
    public function spotlightRegulations(string $mode = 'all', int $limit = 6): array
    {
        $limit = max(1, min(15, $limit));
        $db = db_connect();

        $builder = $db->table('regulations r')
            ->select('r.id, r.slug, r.title, r.document_number, r.document_year, r.legal_status,
                      r.issuer_name, r.official_date, r.effective_date, r.summary,
                      r.cover_file_id, r.cover_image_path, r.is_featured, r.carousel_enabled, r.carousel_order,
                      r.published_at,
                      rc.name AS category_name, rc.slug AS category_slug,
                      (SELECT COUNT(rd.id) FROM regulation_downloads rd WHERE rd.regulation_id = r.id) AS download_count')
            ->join('regulation_categories rc', 'rc.id = r.category_id', 'left')
            ->where('r.workflow_status', 'published')
            ->where('r.visibility', 'public')
            ->where('r.deleted_at', null);

        if ($mode === 'featured') {
            $builder->groupStart()
                ->where('r.carousel_enabled', 1)
                ->orWhere('r.is_featured', 1)
                ->groupEnd();
        } elseif ($mode !== 'all' && $mode !== '') {
            $builder->where('rc.slug', $mode);
        }

        // Prioritize carousel_enabled, is_featured, carousel_order, then download count & published date
        $builder->orderBy('r.carousel_enabled', 'DESC')
            ->orderBy('r.is_featured', 'DESC')
            ->orderBy('CASE WHEN r.carousel_order IS NULL THEN 1 ELSE 0 END', 'ASC', false)
            ->orderBy('r.carousel_order', 'ASC')
            ->orderBy('download_count', 'DESC')
            ->orderBy('r.published_at', 'DESC')
            ->limit($limit);

        $results = $builder->get()->getResultArray();

        // Graceful fallback to all regulations if specific filter mode returned no results
        if (empty($results) && $mode !== 'all') {
            return $this->spotlightRegulations('all', $limit);
        }

        return $results;
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

        if ($regulation) {
            $regId = (int) $regulation['id'];
            $db = db_connect();
            $regulation['stages'] = $db->table('regulation_procurement_stages rps')
                ->select('ps.id, ps.name, ps.description')
                ->join('procurement_stages ps', 'ps.id = rps.procurement_stage_id')
                ->where('rps.regulation_id', $regId)
                ->get()->getResultArray();
            $regulation['download_count'] = (int) $db->table('regulation_downloads')
                ->where('regulation_id', $regId)
                ->countAllResults();
        }

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