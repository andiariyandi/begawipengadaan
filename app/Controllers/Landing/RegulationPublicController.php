<?php

namespace App\Controllers\Landing;

use App\Controllers\BaseController;
use App\Services\AuditService;
use App\Services\Regulation\RegulationPublicService;

/**
 * Public landing controller for Regulasi PBJ.
 *
 * All public-facing queries go through RegulationPublicService so the
 * listing filter and the statistics counters stay in sync (BUG-003).
 *
 * Bug-fix summary:
 *   BUG-001  detail() crashed on CASE+? positional binding — replaced
 *             relations() with two simple direction queries.
 *   BUG-002  search now LIKE's title, document_number, summary, issuer_name
 *             and the result actually filters.
 *   BUG-003  stats derive from same scope, no more "counter=0 but list has rows".
 *   BUG-004  invalid slug / invalid category return 404 (not silent redirect).
 *   BUG-005  download() inserts regulation_downloads, validates stream path,
 *             sends Content-Type from file_assets.mime_type (octet-stream fallback).
 *   BUG-006  preview() streams the current version file inline (no DB write).
 *   BUG-007  versions() returns paginated history (delegates to service).
 *   BUG-008  relations() returns both directions with friendly label.
 *   BUG-009  slug uniqueness already enforced by regulation_categories.slug
 *             UNIQUE; regulation slug column has UNIQUE index too.
 *   BUG-010  every view receives pre-sanitized arrays; foreach loops are guarded
 *             by `empty()` checks.
 */
class RegulationPublicController extends BaseController
{
    public function __construct(
        private readonly RegulationPublicService $svc = new RegulationPublicService(),
    ) {
        $this->request = service('request');
    }

    public function index()
    {
        try {
            (new \App\Services\VisitorStatisticService())->recordHit('regulasi');
        } catch (\Throwable) {
        }

        $page        = max(1, (int) ($this->request->getGet('page') ?: 1));
        $categoryId  = (int) ($this->request->getGet('category_id') ?: 0) ?: null;
        $keyword     = trim((string) ($this->request->getGet('q') ?: '')) ?: null;
        $legalStatus = trim((string) ($this->request->getGet('status') ?: $this->request->getGet('legal_status') ?: '')) ?: null;
        $year        = (int) ($this->request->getGet('year') ?: 0) ?: null;
        $stageId     = (int) ($this->request->getGet('stage_id') ?: 0) ?: null;
        $sort        = trim((string) ($this->request->getGet('sort') ?: 'newest'));
        $activeTab   = trim((string) ($this->request->getGet('tab') ?: 'katalog'));

        $result = $this->svc->list($page, 9, $categoryId, $keyword, $legalStatus, $year, $stageId, $sort);

        return view('landing/pages/regulation', [
            'title'               => 'Pusat Regulasi & Tata Kelola PBJ Lampung | BegawiPengadaan',
            'metaDescription'     => 'Kompilasi regulasi Pengadaan Barang/Jasa Pemerintah terlengkap: Perpres 12/2021, Peraturan LKPP, Peraturan Daerah Lampung, status keberlakuan hukum, riwayat versi, dan unduh dokumen resmi.',
            'pageTitle'           => 'Pusat Regulasi PBJ',
            'regulations'         => $result['items'],
            'totalRegulations'    => $result['total'],
            'currentPage'         => $result['page'],
            'totalPages'          => $result['totalPages'],
            'perPage'             => $result['perPage'],
            'categories'          => $this->svc->categoriesWithCount(),
            'stages'              => $this->svc->stages(),
            'competencies'        => $this->svc->competencies(),
            'stats'               => $this->svc->stats(),
            'popular'             => $this->svc->popular(4),
            'years'               => $this->svc->distinctYears(),
            'keyword'             => $keyword,
            'selectedCategory'    => $categoryId,
            'selectedCategoryObj' => null,
            'selectedStatus'      => $legalStatus,
            'selectedYear'        => $year,
            'selectedStage'       => $stageId,
            'selectedSort'        => $sort,
            'activeTab'           => $activeTab,
        ]);
    }

    public function category(string $slug)
    {
        $category = $this->svc->categoryBySlug($slug);
        if (! $category) {
            // BUG-004: invalid category → 404, NOT silent redirect.
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kategori regulasi tidak ditemukan.');
        }

        $page        = max(1, (int) ($this->request->getGet('page') ?: 1));
        $keyword     = trim((string) ($this->request->getGet('q') ?: '')) ?: null;
        $legalStatus = trim((string) ($this->request->getGet('status') ?: $this->request->getGet('legal_status') ?: '')) ?: null;
        $year        = (int) ($this->request->getGet('year') ?: 0) ?: null;
        $stageId     = (int) ($this->request->getGet('stage_id') ?: 0) ?: null;
        $sort        = trim((string) ($this->request->getGet('sort') ?: 'newest'));
        $activeTab   = trim((string) ($this->request->getGet('tab') ?: 'katalog'));

        $result = $this->svc->list($page, 9, (int) $category['id'], $keyword, $legalStatus, $year, $stageId, $sort);

        return view('landing/pages/regulation', [
            'title'               => 'Kategori ' . $category['name'] . ' | Regulasi PBJ Lampung',
            'metaDescription'     => 'Kumpulan dokumen ' . $category['name'] . ' Pengadaan Barang/Jasa Pemerintah Provinsi Lampung dengan filter status hukum dan unduh berkas.',
            'pageTitle'           => 'Regulasi · ' . $category['name'],
            'regulations'         => $result['items'],
            'totalRegulations'    => $result['total'],
            'currentPage'         => $result['page'],
            'totalPages'          => $result['totalPages'],
            'perPage'             => $result['perPage'],
            'categories'          => $this->svc->categoriesWithCount(),
            'stages'              => $this->svc->stages(),
            'competencies'        => $this->svc->competencies(),
            'stats'               => $this->svc->stats(),
            'popular'             => $this->svc->popular(4),
            'years'               => $this->svc->distinctYears(),
            'keyword'             => $keyword,
            'selectedCategory'    => (int) $category['id'],
            'selectedCategoryObj' => $category,
            'selectedStatus'      => $legalStatus,
            'selectedYear'        => $year,
            'selectedStage'       => $stageId,
            'selectedSort'        => $sort,
            'activeTab'           => $activeTab,
        ]);
    }

    public function detail(string $slug)
    {
        $regulation = $this->svc->detail($slug);
        if (! $regulation) {
            // BUG-001: invalid slug → 404, no silent redirect.
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Regulasi tidak ditemukan.');
        }

        $regId = (int) $regulation['id'];

        return view('landing/pages/regulation_detail', [
            'title'         => ($regulation['document_number'] ?? 'Detail') . ' | JDIH PBJ Lampung',
            'pageTitle'     => $regulation['document_number'] ?? 'Detail Regulasi',
            'regulation'    => $regulation,
            'versions'      => $this->svc->versions($regId),
            'relations'     => $this->svc->relations($regId),
            'competencies'  => $this->svc->competenciesForRegulation($regId),
            'related'       => $this->svc->relatedRegulations($regId, (int) $regulation['category_id']),
            'categories'    => $this->svc->categoriesWithCount(),
            'stats'         => $this->svc->stats(),
            'viewMode'      => 'detail',
        ]);
    }

    public function versions(string $slug)
    {
        $regulation = $this->svc->detail($slug);
        if (! $regulation) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Regulasi tidak ditemukan.');
        }

        $regId = (int) $regulation['id'];
        $versions = $this->svc->versions($regId);

        return view('landing/pages/regulation_detail', [
            'title'         => 'Riwayat Versi ' . ($regulation['document_number'] ?? '') . ' | BegawiPengadaan',
            'pageTitle'     => 'Riwayat Versi · ' . ($regulation['document_number'] ?? ''),
            'regulation'    => $regulation,
            'versions'      => $versions,
            'relations'     => $this->svc->relations($regId),
            'competencies'  => $this->svc->competenciesForRegulation($regId),
            'related'       => $this->svc->relatedRegulations($regId, (int) $regulation['category_id']),
            'categories'    => $this->svc->categoriesWithCount(),
            'stats'         => $this->svc->stats(),
            'viewMode'      => 'versions',
        ]);
    }

    public function relations(string $slug)
    {
        $regulation = $this->svc->detail($slug);
        if (! $regulation) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Regulasi tidak ditemukan.');
        }

        $regId = (int) $regulation['id'];
        $relations = $this->svc->relations($regId);

        return view('landing/pages/regulation_detail', [
            'title'         => 'Relasi & Keterkaitan ' . ($regulation['document_number'] ?? '') . ' | BegawiPengadaan',
            'pageTitle'     => 'Matriks Relasi · ' . ($regulation['document_number'] ?? ''),
            'regulation'    => $regulation,
            'versions'      => $this->svc->versions($regId),
            'relations'     => $relations,
            'competencies'  => $this->svc->competenciesForRegulation($regId),
            'related'       => $this->svc->relatedRegulations($regId, (int) $regulation['category_id']),
            'categories'    => $this->svc->categoriesWithCount(),
            'stats'         => $this->svc->stats(),
            'viewMode'      => 'relations',
        ]);
    }

    public function preview(string $slug)
    {
        $regulation = $this->svc->detail($slug);
        if (! $regulation) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Regulasi tidak ditemukan.');
        }

        $version = $this->svc->currentVersion((int) $regulation['id']);
        if (! $version || empty($version['path'])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File pratinjau tidak tersedia.');
        }

        // BUG-006: stream file inline, no DB write (preview ≠ download).
        $fullPath = $this->resolveSafePath((string) $version['path']);
        if ($fullPath === null || ! is_file($fullPath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File tidak ditemukan di server.');
        }

        $mime = ! empty($version['mime_type']) ? $version['mime_type'] : 'application/octet-stream';
        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', 'inline; filename="' . ($regulation['document_number'] ?? 'preview') . '"')
            ->setBody(file_get_contents($fullPath));
    }

    public function download(string $slug)
    {
        $regulation = $this->svc->detail($slug);
        if (! $regulation) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Regulasi tidak ditemukan.');
        }

        $version = $this->svc->currentVersion((int) $regulation['id']);
        if (! $version || empty($version['path'])) {
            return redirect()->back()->with('error', 'File tidak tersedia untuk regulasi ini.');
        }

        $fullPath = $this->resolveSafePath((string) $version['path']);
        if ($fullPath === null || ! is_file($fullPath)) {
            return redirect()->back()->with('error', 'File tidak ditemukan di server.');
        }

        // BUG-005: log to regulation_downloads table.
        $userId = session()->get('user_id');
        $downloadLog = [
            'regulation_id' => (int) $regulation['id'],
            'version_id'    => (int) ($version['id'] ?? 0),
            'file_id'       => (int) ($version['file_id'] ?? 0),
            'access_type'   => 'public_download',
            'created_at'    => date('Y-m-d H:i:s'),
        ];
        if (! empty($userId) && (int) $userId > 0) {
            $downloadLog['user_id'] = (int) $userId;
        }
        db_connect()->table('regulation_downloads')->insert($downloadLog);

        try {
            (new AuditService())->log(
                (int) (session()->get('user_id') ?: 0) ?: null,
                'public.regulation.download',
                'regulations',
                'regulation',
                (int) $regulation['id'],
            );
        } catch (\Throwable $e) {
            log_message('warning', '[RegulationPublic] audit log failed: ' . $e->getMessage());
        }

        // BUG-005: send Content-Type from mime_type (application/pdf or
        // application/octet-stream fallback) and stream the file as attachment.
        $mime = ! empty($version['mime_type']) ? $version['mime_type'] : 'application/octet-stream';
        $ext  = ! empty($version['extension']) ? $version['extension'] : pathinfo($fullPath, PATHINFO_EXTENSION);
        $filename = ($regulation['document_number'] ?? 'regulasi') . '-v' . ($version['version_number'] ?? '1') . '.' . $ext;

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody(file_get_contents($fullPath));
    }

    /**
     * Validate that an upload path resolves under WRITEPATH/uploads to prevent
     * path traversal. Returns the real absolute path or null if unsafe.
     */
    private function resolveSafePath(string $relativePath): ?string
    {
        $basePath = realpath(WRITEPATH . 'uploads/regulations');
        $candidate = realpath(WRITEPATH . ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath), DIRECTORY_SEPARATOR));

        if ($basePath === false || $candidate === false) {
            return null;
        }

        $baseReal = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (strncmp($candidate, $baseReal, strlen($baseReal)) !== 0) {
            return null;
        }

        return $candidate;
    }
}
