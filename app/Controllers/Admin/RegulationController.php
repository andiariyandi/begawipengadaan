<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Regulation\CompetencyModel;
use App\Models\Regulation\ProcurementStageModel;
use App\Models\Regulation\RegulationCategoryModel;
use App\Models\Regulation\RegulationCompetencyModel;
use App\Models\Regulation\RegulationDownloadLogModel;
use App\Models\Regulation\RegulationModel;
use App\Models\Regulation\RegulationProcurementStageModel;
use App\Models\Regulation\RegulationRelationModel;
use App\Models\Regulation\RegulationVersionModel;
use App\Services\AuditService;
use App\Services\FileStorageService;
use App\Services\Regulation\RegulationService;

/**
 * Admin Manajemen Regulasi PBJ.
 *
 * Implements 7 menu from audit + workflow lifecycle (draft → submitted →
 * in_review → approved/revision_required/rejected → published → archived).
 * File upload via file_assets. Every state change writes audit_logs and
 * workflow_transitions through RegulationService.
 */
class RegulationController extends BaseController
{
    public function __construct()
    {
        helper(['form', 'text']);
    }

    // ============== MENU 1: REGULASI ==============

    public function index()
    {
        $filters = [
            'category_id'      => $this->request->getGet('category_id') ?: null,
            'keyword'          => $this->request->getGet('keyword') ?: null,
            'workflow_status'  => $this->request->getGet('workflow_status') ?: null,
            'legal_status'     => $this->request->getGet('legal_status') ?: null,
            'is_featured'      => $this->request->getGet('is_featured') !== null && $this->request->getGet('is_featured') !== '' ? (int) $this->request->getGet('is_featured') : null,
        ];

        $allRegulations = (new RegulationModel())->listForAdmin(
            $filters['category_id'] ? (int) $filters['category_id'] : null,
            $filters['keyword'],
            $filters['workflow_status'],
            $filters['legal_status'],
            $filters['is_featured']
        );

        $perPage = max(5, min(100, (int) ($this->request->getGet('per_page') ?: 15)));
        $page = max(1, (int) ($this->request->getGet('page_regulations') ?: 1));
        $total = count($allRegulations);
        $regulations = array_slice($allRegulations, ($page - 1) * $perPage, $perPage);

        $pager = service('pager');
        $pager->store('regulations', $page, $perPage, $total, 0);

        $db = db_connect();

        // Attach stages & competencies to each displayed regulation
        if (! empty($regulations)) {
            $regIds = array_column($regulations, 'id');
            $stageRows = $db->table('regulation_procurement_stages rps')
                ->select('rps.regulation_id, ps.id AS stage_id, ps.name AS stage_name, ps.key AS stage_key')
                ->join('procurement_stages ps', 'ps.id = rps.procurement_stage_id')
                ->whereIn('rps.regulation_id', $regIds)
                ->orderBy('ps.sort_order', 'ASC')
                ->get()->getResultArray();
            $stageMap = [];
            foreach ($stageRows as $sr) {
                $stageMap[(int) $sr['regulation_id']][] = $sr;
            }

            foreach ($regulations as &$r) {
                $r['stages'] = $stageMap[(int) $r['id']] ?? [];
            }
            unset($r);
        }

        $stats = [
            'total'       => (int) $db->table('regulations')->where('deleted_at', null)->countAllResults(),
            'active'      => (int) $db->table('regulations')->where('deleted_at', null)->whereIn('legal_status', ['active', 'berlaku'])->countAllResults(),
            'published'   => (int) $db->table('regulations')->where('deleted_at', null)->where('workflow_status', 'published')->countAllResults(),
            'in_review'   => (int) $db->table('regulations')->where('deleted_at', null)->whereIn('workflow_status', ['in_review', 'submitted', 'draft'])->countAllResults(),
        ];

        return view('dashboard/regulation/regulations/index', [
            'title'            => 'Regulasi PBJ | Dashboard BegawiPBJ',
            'pageTitle'        => 'Regulasi Pengadaan Barang/Jasa',
            'activeMenu'       => 'regulations',
            'activeSubmenu'    => 'regulations',
            'regulations'      => $regulations,
            'stats'            => $stats,
            'pager'            => $pager,
            'perPage'          => $perPage,
            'categories'       => (new RegulationCategoryModel())->listAll(),
            'filters'          => $filters,
            'workflowStatuses' => RegulationService::ALLOWED_WORKFLOW_STATUSES,
            'legalStatuses'    => ['active', 'amended', 'revoked', 'expired'],
        ]);
    }

    public function create()
    {
        return view('dashboard/regulation/regulations/form', [
            'title'                => 'Tambah Regulasi | Dashboard BegawiPBJ',
            'pageTitle'            => 'Tambah Regulasi',
            'formMode'             => 'create',
            'activeMenu'           => 'regulations',
            'activeSubmenu'        => 'regulations',
            'categories'           => (new RegulationCategoryModel())->listAll(),
            'stages'               => (new ProcurementStageModel())->orderBy('sort_order, name')->findAll(),
            'competencies'         => (new CompetencyModel())->orderBy('name')->findAll(),
            'selectedStages'       => [],
            'selectedCompetencies' => [],
            'workflowStatuses'     => RegulationService::ALLOWED_WORKFLOW_STATUSES,
            'legalStatuses'        => ['active', 'amended', 'revoked', 'expired'],
            'visibilities'         => ['public', 'internal', 'restricted'],
        ]);
    }

    public function store()
    {
        $rules = [
            'title'           => 'required|max_length[255]',
            'category_id'     => 'required|is_natural_no_zero',
            'document_number' => 'required|max_length[255]',
            'document_year'   => 'required|numeric|exact_length[4]',
            'issuer_name'     => 'permit_empty|max_length[255]',
            'summary'         => 'permit_empty',
            'cover_image'     => 'permit_empty|is_image[cover_image]|max_size[cover_image,3072]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $svc = new RegulationService();
        $post = $this->request->getPost();
        
        $coverFile = $this->request->getFile('cover_image');
        $coverFileId = $this->uploadCoverImage($coverFile);

        try {
            $id = $svc->create([
                'category_id'         => (int) $post['category_id'],
                'title'               => $post['title'],
                'document_number'     => $post['document_number'],
                'document_year'       => (int) $post['document_year'],
                'issuer_name'         => $post['issuer_name'] ?? null,
                'official_date'       => $this->parseDate($post['official_date'] ?? null),
                'effective_date'      => $this->parseDate($post['effective_date'] ?? null),
                'summary'             => $post['summary'] ?? null,
                'keywords'            => $post['keywords'] ?? null,
                'official_source_url' => $post['official_source_url'] ?? null,
                'workflow_status'     => 'draft',
                'legal_status'        => $post['legal_status'] ?? 'active',
                'visibility'          => $post['visibility'] ?? 'internal',
                'is_featured'         => isset($post['is_featured']) && $post['is_featured'] ? 1 : 0,
                'cover_file_id'       => $coverFileId,
            ], (int) session()->get('user_id'));
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal membuat regulasi: ' . $e->getMessage());
        }

        // Sync procurement stages & competencies
        $this->syncRegulationStages($id, (array) ($this->request->getPost('stages') ?? []));
        $this->syncRegulationCompetencies($id, (array) ($this->request->getPost('competencies') ?? []));

        // Attach optional file as version 1
        $file = $this->request->getFile('file');
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            try {
                $stored = (new FileStorageService())->storeUpload($file, 'regulations', $id, (int) session()->get('user_id'), 'public');
                if ($stored) {
                    $svc->addVersion($id, (int) $stored['id'], 'Unggahan naskah awal resmi.', (int) session()->get('user_id'));
                }
            } catch (\Throwable $e) {
                return redirect()->to('dashboard/regulasi/dokumen/' . $id)->with('warning', 'Regulasi dibuat tetapi file berkas gagal diunggah: ' . $e->getMessage());
            }
        }

        return redirect()->to('dashboard/regulasi/dokumen/' . $id)->with('success', 'Regulasi berhasil dibuat.');
    }

    public function show(string $id)
    {
        $regId = ctype_digit($id) ? (int) $id : 0;
        $regulation = (new RegulationModel())->withDetail($regId);
        if (! $regulation) {
            return redirect()->to('dashboard/regulasi/dokumen')->with('error', 'Regulasi tidak ditemukan.');
        }

        $relations = (new RegulationRelationModel())
            ->groupStart()
                ->where('source_regulation_id', $regId)
                ->orWhere('target_regulation_id', $regId)
            ->groupEnd()
            ->listForAdmin();

        $competencies = db_connect()->table('regulation_competencies rc')
            ->select('rc.*, c.name, c.key')
            ->join('competencies c', 'c.id = rc.competency_id', 'left')
            ->where('rc.regulation_id', $regId)
            ->get()->getResultArray();

        $transitions = db_connect()->table('workflow_transitions')
            ->where('resource_type', 'regulation')
            ->where('resource_id', $regId)
            ->orderBy('created_at', 'DESC')->get()->getResultArray();

        return view('dashboard/regulation/regulations/detail', [
            'title'            => 'Detail Regulasi | Dashboard BegawiPBJ',
            'pageTitle'        => 'Detail Regulasi',
            'activeMenu'       => 'regulations',
            'activeSubmenu'    => 'regulations',
            'regulation'       => $regulation,
            'relations'        => $relations,
            'competencies'     => $competencies,
            'transitions'      => $transitions,
            'workflowStatuses' => RegulationService::ALLOWED_WORKFLOW_STATUSES,
        ]);
    }

    public function edit(string $id)
    {
        $regId = ctype_digit($id) ? (int) $id : 0;
        $regulation = (new RegulationModel())->find($regId);
        if (! $regulation) {
            return redirect()->to('dashboard/regulasi/dokumen')->with('error', 'Regulasi tidak ditemukan.');
        }

        $selectedStages = array_column(
            db_connect()->table('regulation_procurement_stages')->where('regulation_id', $regId)->get()->getResultArray(),
            'procurement_stage_id'
        );
        $selectedCompetencies = array_column(
            db_connect()->table('regulation_competencies')->where('regulation_id', $regId)->get()->getResultArray(),
            'competency_id'
        );
        $coverImgUrl = ! empty($regulation['cover_file_id']) ? base_url('file/' . (int) $regulation['cover_file_id']) : null;

        return view('dashboard/regulation/regulations/form', [
            'title'                => 'Ubah Regulasi | Dashboard BegawiPBJ',
            'pageTitle'            => 'Ubah Regulasi',
            'formMode'             => 'edit',
            'activeMenu'           => 'regulations',
            'activeSubmenu'        => 'regulations',
            'regulation'           => $regulation,
            'regId'                => $regId,
            'categories'           => (new RegulationCategoryModel())->listAll(),
            'stages'               => (new ProcurementStageModel())->orderBy('sort_order, name')->findAll(),
            'competencies'         => (new CompetencyModel())->orderBy('name')->findAll(),
            'selectedStages'       => $selectedStages,
            'selectedCompetencies' => $selectedCompetencies,
            'coverImgUrl'          => $coverImgUrl,
            'workflowStatuses'     => RegulationService::ALLOWED_WORKFLOW_STATUSES,
            'legalStatuses'        => ['active', 'amended', 'revoked', 'expired'],
            'visibilities'         => ['public', 'internal', 'restricted'],
        ]);
    }

    public function update(string $id)
    {
        $regId = ctype_digit($id) ? (int) $id : 0;
        $svc   = new RegulationService();
        $regulation = (new RegulationModel())->find($regId);
        if (! $regulation) {
            return redirect()->to('dashboard/regulasi/dokumen')->with('error', 'Regulasi tidak ditemukan.');
        }

        $rules = [
            'title'           => 'required|max_length[255]',
            'category_id'     => 'required|is_natural_no_zero',
            'document_number' => 'required|max_length[255]',
            'document_year'   => 'required|numeric|exact_length[4]',
            'cover_image'     => 'permit_empty|is_image[cover_image]|max_size[cover_image,3072]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post = $this->request->getPost();
        
        $updateData = [
            'category_id'         => (int) $post['category_id'],
            'title'               => $post['title'],
            'document_number'     => $post['document_number'],
            'document_year'       => (int) $post['document_year'],
            'issuer_name'         => $post['issuer_name'] ?? null,
            'official_date'       => $this->parseDate($post['official_date'] ?? null),
            'effective_date'      => $this->parseDate($post['effective_date'] ?? null),
            'summary'             => $post['summary'] ?? null,
            'keywords'            => $post['keywords'] ?? null,
            'official_source_url' => $post['official_source_url'] ?? null,
            'legal_status'        => $post['legal_status'] ?? 'active',
            'visibility'          => $post['visibility'] ?? 'internal',
            'is_featured'         => isset($post['is_featured']) && $post['is_featured'] ? 1 : 0,
        ];

        // Handle cover image
        if ($this->request->getPost('remove_cover')) {
            $updateData['cover_file_id'] = null;
        } else {
            $coverFile = $this->request->getFile('cover_image');
            $newCoverId = $this->uploadCoverImage($coverFile, $regId);
            if ($newCoverId !== null) {
                $updateData['cover_file_id'] = $newCoverId;
            }
        }

        try {
            $svc->update($regId, $updateData, (int) session()->get('user_id'));
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengubah regulasi: ' . $e->getMessage());
        }

        // Sync stages & competencies
        $this->syncRegulationStages($regId, (array) ($this->request->getPost('stages') ?? []));
        $this->syncRegulationCompetencies($regId, (array) ($this->request->getPost('competencies') ?? []));

        // Optional new version file upload
        $docFile = $this->request->getFile('file');
        if ($docFile && $docFile->isValid() && ! $docFile->hasMoved()) {
            try {
                $stored = (new FileStorageService())->storeUpload($docFile, 'regulations', $regId, (int) session()->get('user_id'), 'public');
                if ($stored) {
                    $changelog = trim((string) $this->request->getPost('changelog')) ?: 'Pembaruan naskah dokumen resmi.';
                    $svc->addVersion($regId, (int) $stored['id'], $changelog, (int) session()->get('user_id'));
                }
            } catch (\Throwable $e) {
                return redirect()->to('dashboard/regulasi/dokumen/' . $regId)->with('warning', 'Data disimpan tetapi berkas versi gagal diunggah: ' . $e->getMessage());
            }
        }

        return redirect()->to('dashboard/regulasi/dokumen/' . $regId)->with('success', 'Regulasi berhasil diperbarui.');
    }

    public function delete(string $id)
    {
        $regId = ctype_digit($id) ? (int) $id : 0;
        try {
            (new RegulationService())->delete($regId, (int) session()->get('user_id'));
            return redirect()->to('dashboard/regulasi/dokumen')->with('success', 'Regulasi dihapus (soft).');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // ============== WORKFLOW ACTIONS ==============

    public function submitReview(string $id)
    {
        $regId = ctype_digit($id) ? (int) $id : 0;
        $notes = (string) $this->request->getPost('notes');
        try {
            (new RegulationService())->submitReview($regId, (int) session()->get('user_id'), $notes);
            return redirect()->to('dashboard/regulasi/dokumen/' . $regId)->with('success', 'Regulasi diajukan untuk review.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function approve(string $id)
    {
        $regId = ctype_digit($id) ? (int) $id : 0;
        $notes = (string) $this->request->getPost('notes');
        try {
            (new RegulationService())->approve($regId, (int) session()->get('user_id'), $notes);
            return redirect()->to('dashboard/regulasi/dokumen/' . $regId)->with('success', 'Regulasi disetujui.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function reject(string $id)
    {
        $regId = ctype_digit($id) ? (int) $id : 0;
        $reason = trim((string) $this->request->getPost('reason'));
        try {
            (new RegulationService())->reject($regId, $reason, (int) session()->get('user_id'));
            return redirect()->to('dashboard/regulasi/dokumen/' . $regId)->with('success', 'Regulasi ditolak.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function requestRevision(string $id)
    {
        $regId = ctype_digit($id) ? (int) $id : 0;
        $note = trim((string) $this->request->getPost('note'));
        try {
            (new RegulationService())->requestRevision($regId, $note, (int) session()->get('user_id'));
            return redirect()->to('dashboard/regulasi/dokumen/' . $regId)->with('success', 'Permintaan revisi dikirim.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function publish(string $id)
    {
        $regId = ctype_digit($id) ? (int) $id : 0;
        try {
            (new RegulationService())->publish($regId, (int) session()->get('user_id'));
            return redirect()->to('dashboard/regulasi/dokumen/' . $regId)->with('success', 'Regulasi dipublikasikan.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function archive(string $id)
    {
        $regId = ctype_digit($id) ? (int) $id : 0;
        try {
            $reg = (new RegulationModel())->find($regId);
            if ($reg) {
                (new RegulationService())->update($regId, [
                    'workflow_status' => 'archived',
                    'archived_at'     => date('Y-m-d H:i:s'),
                ], (int) session()->get('user_id'));
            }
            return redirect()->to('dashboard/regulasi/dokumen/' . $regId)->with('success', 'Regulasi diarsipkan.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // ============== VERSION ==============

    public function addVersion(string $id)
    {
        $regId = ctype_digit($id) ? (int) $id : 0;
        $file  = $this->request->getFile('file');
        $notes = (string) $this->request->getPost('changelog');

        if (! $file || ! $file->isValid()) {
            return redirect()->back()->with('error', 'File versi wajib diunggah.');
        }

        try {
            $stored = (new FileStorageService())->storeUpload($file, 'regulations', $regId, (int) session()->get('user_id'));
            if (! $stored) {
                return redirect()->back()->with('error', 'Upload file gagal.');
            }
            $vid = (new RegulationService())->addVersion($regId, (int) $stored['id'], $notes, (int) session()->get('user_id'));
            if ($vid <= 0) {
                return redirect()->back()->with('error', 'Versi regulasi gagal disimpan.');
            }
            return redirect()->to('dashboard/regulasi/dokumen/' . $regId)->with('success', 'Versi baru berhasil diunggah.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function setCurrentVersion(string $id)
    {
        $regId = ctype_digit($id) ? (int) $id : 0;
        $versionId = (int) $this->request->getPost('version_id');
        $ok = (new RegulationService())->setCurrentVersion($regId, $versionId, (int) session()->get('user_id'));
        return redirect()->to('dashboard/regulasi/dokumen/' . $regId)
            ->with($ok ? 'success' : 'error', $ok ? 'Versi aktif diperbarui.' : 'Gagal mengubah versi aktif.');
    }

    // ============== CATEGORY ==============

    public function categories()
    {
        $categories = (new RegulationCategoryModel())->listAll();
        return view('dashboard/regulation/categories/index', [
            'title'         => 'Kategori Regulasi | Dashboard BegawiPBJ',
            'pageTitle'     => 'Kategori Regulasi',
            'activeMenu'    => 'regulations',
            'activeSubmenu' => 'categories',
            'categories'    => $categories,
        ]);
    }

    public function categoryCreate()
    {
        return view('dashboard/regulation/categories/form', [
            'title'         => 'Tambah Kategori | Dashboard BegawiPBJ',
            'pageTitle'     => 'Tambah Kategori',
            'formMode'      => 'create',
            'activeMenu'    => 'regulations',
            'activeSubmenu' => 'categories',
        ]);
    }

    public function categoryStore()
    {
        $rules = [
            'name'        => 'required|max_length[255]',
            'slug'        => 'required|max_length[255]|is_unique[regulation_categories.slug]',
            'description' => 'permit_empty',
            'icon_class'  => 'permit_empty|max_length[100]',
            'icon_image'  => 'permit_empty|is_image[icon_image]|max_size[icon_image,2048]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post = $this->request->getPost();
        $iconFile = $this->request->getFile('icon_image');
        $iconFileId = $this->uploadCategoryIcon($iconFile);

        try {
            $id = (new RegulationCategoryModel())->insert([
                'name'         => $post['name'],
                'slug'         => $post['slug'],
                'description'  => $post['description'] ?? null,
                'icon_class'   => ! empty($post['icon_class']) ? $post['icon_class'] : 'solar:document-outline',
                'icon_file_id' => $iconFileId,
                'is_active'    => isset($post['is_active']) ? 1 : 1,
            ], true);
            (new AuditService())->log((int) session()->get('user_id'), 'regulation_category.create', 'regulation_categories', 'regulation_category', $id);
            return redirect()->to('dashboard/regulasi/kategori/' . $id)->with('success', 'Kategori dibuat.');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function categoryShow(string $id)
    {
        $catId = ctype_digit($id) ? (int) $id : 0;
        $category = (new RegulationCategoryModel())->find($catId);
        if (! $category) {
            return redirect()->to('dashboard/regulasi/kategori')->with('error', 'Kategori tidak ditemukan.');
        }
        $regulations = db_connect()->table('regulations')->where('category_id', $catId)->where('deleted_at', null)->orderBy('created_at', 'DESC')->get()->getResultArray();
        return view('dashboard/regulation/categories/detail', [
            'title'         => 'Detail Kategori | Dashboard BegawiPBJ',
            'pageTitle'     => 'Detail Kategori',
            'activeMenu'    => 'regulations',
            'activeSubmenu' => 'categories',
            'category'      => $category,
            'regulations'   => $regulations,
        ]);
    }

    public function categoryEdit(string $id)
    {
        $catId = ctype_digit($id) ? (int) $id : 0;
        $category = (new RegulationCategoryModel())->find($catId);
        if (! $category) {
            return redirect()->to('dashboard/regulasi/kategori')->with('error', 'Kategori tidak ditemukan.');
        }
        $iconUrl = ! empty($category['icon_file_id']) ? base_url('file/' . (int) $category['icon_file_id']) : null;
        return view('dashboard/regulation/categories/form', [
            'title'         => 'Ubah Kategori | Dashboard BegawiPBJ',
            'pageTitle'     => 'Ubah Kategori',
            'formMode'      => 'edit',
            'activeMenu'    => 'regulations',
            'activeSubmenu' => 'categories',
            'category'      => $category,
            'catId'         => $catId,
            'iconUrl'       => $iconUrl,
        ]);
    }

    public function categoryUpdate(string $id)
    {
        $catId = ctype_digit($id) ? (int) $id : 0;
        $category = (new RegulationCategoryModel())->find($catId);
        if (! $category) {
            return redirect()->to('dashboard/regulasi/kategori')->with('error', 'Kategori tidak ditemukan.');
        }

        $rules = [
            'name'        => 'required|max_length[255]',
            'slug'        => "required|max_length[255]|is_unique[regulation_categories.slug,id,{$catId}]",
            'description' => 'permit_empty',
            'icon_class'  => 'permit_empty|max_length[100]',
            'icon_image'  => 'permit_empty|is_image[icon_image]|max_size[icon_image,2048]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post = $this->request->getPost();
        $iconFile = $this->request->getFile('icon_image');
        $newIconId = $this->uploadCategoryIcon($iconFile, $catId);

        $updateData = [
            'name'        => $post['name'],
            'slug'        => $post['slug'],
            'description' => $post['description'] ?? null,
            'icon_class'  => ! empty($post['icon_class']) ? $post['icon_class'] : ($category['icon_class'] ?? 'solar:document-outline'),
            'is_active'   => isset($post['is_active']) ? 1 : 0,
        ];

        if ($this->request->getPost('remove_icon')) {
            $updateData['icon_file_id'] = null;
        } elseif ($newIconId !== null) {
            $updateData['icon_file_id'] = $newIconId;
        }

        try {
            (new RegulationCategoryModel())->update($catId, $updateData);
            (new AuditService())->log((int) session()->get('user_id'), 'regulation_category.update', 'regulation_categories', 'regulation_category', $catId);
            return redirect()->to('dashboard/regulasi/kategori/' . $catId)->with('success', 'Kategori diperbarui.');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function categoryDelete(string $id)
    {
        $catId = ctype_digit($id) ? (int) $id : 0;
        try {
            (new RegulationCategoryModel())->delete($catId);
            (new AuditService())->log((int) session()->get('user_id'), 'regulation_category.delete', 'regulation_categories', 'regulation_category', $catId);
            return redirect()->to('dashboard/regulasi/kategori')->with('success', 'Kategori dihapus.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // ============== RELATIONS ==============

    public function relations()
    {
        $relations = (new RegulationRelationModel())->listForAdmin();
        return view('dashboard/regulation/relations/index', [
            'title'         => 'Relasi Regulasi | Dashboard BegawiPBJ',
            'pageTitle'     => 'Relasi Regulasi',
            'activeMenu'    => 'regulations',
            'activeSubmenu' => 'relations',
            'relations'     => $relations,
            'relationTypes'  => ['amends', 'amended_by', 'revokes', 'revoked_by', 'replaces', 'replaced_by', 'implements', 'implemented_by', 'references', 'referenced_by'],
        ]);
    }

    public function relationCreate()
    {
        return view('dashboard/regulation/relations/form', [
            'title'         => 'Tambah Relasi | Dashboard BegawiPBJ',
            'pageTitle'     => 'Tambah Relasi',
            'formMode'      => 'create',
            'activeMenu'    => 'regulations',
            'activeSubmenu' => 'relations',
            'regulations'   => (new RegulationModel())->listAll(),
            'relationTypes'  => ['amends', 'amended_by', 'revokes', 'revoked_by', 'replaces', 'replaced_by', 'implements', 'implemented_by', 'references', 'referenced_by'],
        ]);
    }

    public function relationStore()
    {
        $rules = [
            'source_regulation_id' => 'required|is_natural_no_zero',
            'target_regulation_id' => 'required|is_natural_no_zero',
            'relation_type'        => 'required',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $post = $this->request->getPost();
        if ((int) $post['source_regulation_id'] === (int) $post['target_regulation_id']) {
            return redirect()->back()->withInput()->with('error', 'Regulasi sumber dan target tidak boleh sama.');
        }

        $now = date('Y-m-d H:i:s');
        try {
            $id = (new RegulationRelationModel())->insert([
                'source_regulation_id' => (int) $post['source_regulation_id'],
                'target_regulation_id' => (int) $post['target_regulation_id'],
                'relation_type'        => $post['relation_type'],
                'notes'                => $post['notes'] ?? null,
                'created_by'           => (int) session()->get('user_id'),
                'created_at'           => $now,
            ], true);
            (new AuditService())->log((int) session()->get('user_id'), 'regulation_relation.create', 'regulation_relations', 'regulation_relation', $id);
            return redirect()->to('dashboard/regulasi/relasi/' . $id)->with('success', 'Relasi dibuat.');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function relationShow(string $id)
    {
        $relId = ctype_digit($id) ? (int) $id : 0;
        $row = db_connect()->table('regulation_relations rr')
            ->select('rr.*, r_src.title AS source_title, r_src.document_number AS source_number,
                      r_tgt.title AS target_title, r_tgt.document_number AS target_number,
                      u.username AS creator_username')
            ->join('regulations r_src', 'r_src.id = rr.source_regulation_id', 'left')
            ->join('regulations r_tgt', 'r_tgt.id = rr.target_regulation_id', 'left')
            ->join('users u', 'u.id = rr.created_by', 'left')
            ->where('rr.id', $relId)
            ->get()->getRowArray();
        if (! $row) {
            return redirect()->to('dashboard/regulasi/relasi')->with('error', 'Relasi tidak ditemukan.');
        }
        return view('dashboard/regulation/relations/detail', [
            'title'         => 'Detail Relasi | Dashboard BegawiPBJ',
            'pageTitle'     => 'Detail Relasi',
            'activeMenu'    => 'regulations',
            'activeSubmenu' => 'relations',
            'relation'      => $row,
            'relationTypes'  => ['amends', 'amended_by', 'revokes', 'revoked_by', 'replaces', 'replaced_by', 'implements', 'implemented_by', 'references', 'referenced_by'],
        ]);
    }

    public function relationDelete(string $id)
    {
        $relId = ctype_digit($id) ? (int) $id : 0;
        try {
            (new RegulationRelationModel())->delete($relId);
            (new AuditService())->log((int) session()->get('user_id'), 'regulation_relation.delete', 'regulation_relations', 'regulation_relation', $relId);
            return redirect()->to('dashboard/regulasi/relasi')->with('success', 'Relasi dihapus.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // ============== VERSIONS VIEW ==============

    public function versions()
    {
        $versions = db_connect()->table('regulation_versions v')
            ->select('v.*, r.title AS regulation_title, r.document_number, u.username AS uploader_name,
                      fa.original_name AS file_original_name, fa.size_bytes AS file_size')
            ->join('regulations r', 'r.id = v.regulation_id', 'left')
            ->join('users u', 'u.id = v.uploaded_by', 'left')
            ->join('file_assets fa', 'fa.id = v.file_id', 'left')
            ->orderBy('v.created_at', 'DESC')
            ->get()->getResultArray();
        return view('dashboard/regulation/versions/index', [
            'title'         => 'Versi Dokumen | Dashboard BegawiPBJ',
            'pageTitle'     => 'Versi Dokumen',
            'activeMenu'    => 'regulations',
            'activeSubmenu' => 'versions',
            'versions'      => $versions,
        ]);
    }

    public function versionShow(string $id)
    {
        $verId = ctype_digit($id) ? (int) $id : 0;
        $row = db_connect()->table('regulation_versions v')
            ->select('v.*, r.title AS regulation_title, r.document_number, u.username AS uploader_name,
                      fa.original_name AS file_original_name, fa.size_bytes AS file_size, fa.mime_type')
            ->join('regulations r', 'r.id = v.regulation_id', 'left')
            ->join('users u', 'u.id = v.uploaded_by', 'left')
            ->join('file_assets fa', 'fa.id = v.file_id', 'left')
            ->where('v.id', $verId)
            ->get()->getRowArray();
        if (! $row) {
            return redirect()->to('dashboard/regulasi/versi')->with('error', 'Versi tidak ditemukan.');
        }
        return view('dashboard/regulation/versions/detail', [
            'title'         => 'Detail Versi | Dashboard BegawiPBJ',
            'pageTitle'     => 'Detail Versi',
            'activeMenu'    => 'regulations',
            'activeSubmenu' => 'versions',
            'version'       => $row,
        ]);
    }

    // ============== DOWNLOAD LOGS ==============

    public function downloadLogs()
    {
        $filters = [
            'user_id'       => $this->request->getGet('user_id') ?: null,
            'regulation_id' => $this->request->getGet('regulation_id') ?: null,
            'from'          => $this->request->getGet('from') ?: null,
            'to'            => $this->request->getGet('to') ?: null,
        ];

        $logs = (new RegulationDownloadLogModel())->browse($filters);
        $pager = service('pager');
        $page = max(1, (int) ($this->request->getGet('page_logs') ?: 1));
        $perPage = 25;
        $pager->store('logs', $page, $perPage, count($logs), 0);

        return view('dashboard/regulation/download_logs/index', [
            'title'         => 'Log Download Regulasi | Dashboard BegawiPBJ',
            'pageTitle'     => 'Log Download',
            'activeMenu'    => 'regulations',
            'activeSubmenu' => 'download-logs',
            'logs'          => $logs,
            'filters'       => $filters,
            'pager'         => $pager,
        ]);
    }

    // ============== TAHAPAN PBJ ==============

    public function procurementStages()
    {
        $stages = (new ProcurementStageModel())->orderBy('sort_order, name')->findAll();
        return view('dashboard/regulation/procurement_stages/index', [
            'title'         => 'Tahapan PBJ | Dashboard BegawiPBJ',
            'pageTitle'     => 'Tahapan PBJ',
            'activeMenu'    => 'regulations',
            'activeSubmenu' => 'procurement-stages',
            'stages'        => $stages,
        ]);
    }

    public function procurementStageCreate()
    {
        return view('dashboard/regulation/procurement_stages/form', [
            'title'         => 'Tambah Tahapan PBJ | Dashboard BegawiPBJ',
            'pageTitle'     => 'Tambah Tahapan',
            'formMode'      => 'create',
            'activeMenu'    => 'regulations',
            'activeSubmenu' => 'procurement-stages',
        ]);
    }

    public function procurementStageStore()
    {
        $rules = [
            'key'        => 'required|max_length[64]|is_unique[procurement_stages.key]',
            'name'       => 'required|max_length[255]',
            'sort_order' => 'permit_empty|numeric',
            'description' => 'permit_empty',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $post = $this->request->getPost();
        try {
            $id = (new ProcurementStageModel())->insert([
                'key'         => $post['key'],
                'name'        => $post['name'],
                'description' => $post['description'] ?? null,
                'sort_order'  => (int) ($post['sort_order'] ?? 0),
                'is_active'   => isset($post['is_active']) ? 1 : 1,
            ], true);
            (new AuditService())->log((int) session()->get('user_id'), 'procurement_stage.create', 'procurement_stages', 'procurement_stage', $id);
            return redirect()->to('dashboard/regulasi/tahapan-pbj')->with('success', 'Tahapan dibuat.');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function procurementStageEdit(string $id)
    {
        $stgId = ctype_digit($id) ? (int) $id : 0;
        $stage = (new ProcurementStageModel())->find($stgId);
        if (! $stage) {
            return redirect()->to('dashboard/regulasi/tahapan-pbj')->with('error', 'Tahapan tidak ditemukan.');
        }
        return view('dashboard/regulation/procurement_stages/form', [
            'title'         => 'Ubah Tahapan PBJ | Dashboard BegawiPBJ',
            'pageTitle'     => 'Ubah Tahapan',
            'formMode'      => 'edit',
            'activeMenu'    => 'regulations',
            'activeSubmenu' => 'procurement-stages',
            'stage'         => $stage,
            'stgId'         => $stgId,
        ]);
    }

    public function procurementStageUpdate(string $id)
    {
        $stgId = ctype_digit($id) ? (int) $id : 0;
        $rules = [
            'name'       => 'required|max_length[255]',
            'sort_order' => 'permit_empty|numeric',
            'description' => 'permit_empty',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $post = $this->request->getPost();
        try {
            (new ProcurementStageModel())->update($stgId, [
                'name'        => $post['name'],
                'description' => $post['description'] ?? null,
                'sort_order'  => (int) ($post['sort_order'] ?? 0),
                'is_active'   => isset($post['is_active']) ? 1 : 1,
            ]);
            (new AuditService())->log((int) session()->get('user_id'), 'procurement_stage.update', 'procurement_stages', 'procurement_stage', $stgId);
            return redirect()->to('dashboard/regulasi/tahapan-pbj')->with('success', 'Tahapan diperbarui.');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function procurementStageDelete(string $id)
    {
        $stgId = ctype_digit($id) ? (int) $id : 0;
        try {
            (new ProcurementStageModel())->delete($stgId);
            (new AuditService())->log((int) session()->get('user_id'), 'procurement_stage.delete', 'procurement_stages', 'procurement_stage', $stgId);
            return redirect()->to('dashboard/regulasi/tahapan-pbj')->with('success', 'Tahapan dihapus.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // ============== COMPETENCIES ==============

    public function competencies()
    {
        $competencies = (new CompetencyModel())->orderBy('name')->findAll();
        $pivots = db_connect()->table('regulation_competencies rc')
            ->select('rc.regulation_id, COUNT(*) AS total')
            ->groupBy('rc.regulation_id')
            ->get()->getResultArray();
        $pivotMap = [];
        foreach ($pivots as $p) { $pivotMap[(int) $p['regulation_id']] = (int) $p['total']; }
        foreach ($competencies as &$c) {
            $c['regulation_count'] = 0;
            foreach ($pivots as $p) {
                if ((int) $p['regulation_id'] === (int) $c['id']) {
                    $c['regulation_count'] = (int) $p['total'];
                }
            }
        }
        return view('dashboard/regulation/competencies/index', [
            'title'         => 'Kompetensi Terkait | Dashboard BegawiPBJ',
            'pageTitle'     => 'Kompetensi Terkait',
            'activeMenu'    => 'regulations',
            'activeSubmenu' => 'competencies',
            'competencies'  => $competencies,
        ]);
    }

    public function competencyCreate()
    {
        return view('dashboard/regulation/competencies/form', [
            'title'         => 'Tambah Kompetensi | Dashboard BegawiPBJ',
            'pageTitle'     => 'Tambah Kompetensi',
            'formMode'      => 'create',
            'activeMenu'    => 'regulations',
            'activeSubmenu' => 'competencies',
        ]);
    }

    public function competencyStore()
    {
        $rules = [
            'key'         => 'required|max_length[64]|is_unique[competencies.key]',
            'name'        => 'required|max_length[255]',
            'description' => 'permit_empty',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $post = $this->request->getPost();
        try {
            $id = (new CompetencyModel())->insert([
                'key'         => $post['key'],
                'name'        => $post['name'],
                'description' => $post['description'] ?? null,
                'is_active'   => isset($post['is_active']) ? 1 : 1,
            ], true);
            (new AuditService())->log((int) session()->get('user_id'), 'competency.create', 'competencies', 'competency', $id);
            return redirect()->to('dashboard/regulasi/kompetensi-terkait')->with('success', 'Kompetensi dibuat.');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function competencyEdit(string $id)
    {
        $compId = ctype_digit($id) ? (int) $id : 0;
        $competency = (new CompetencyModel())->find($compId);
        if (! $competency) {
            return redirect()->to('dashboard/regulasi/kompetensi-terkait')->with('error', 'Kompetensi tidak ditemukan.');
        }
        return view('dashboard/regulation/competencies/form', [
            'title'         => 'Ubah Kompetensi | Dashboard BegawiPBJ',
            'pageTitle'     => 'Ubah Kompetensi',
            'formMode'      => 'edit',
            'activeMenu'    => 'regulations',
            'activeSubmenu' => 'competencies',
            'competency'    => $competency,
            'compId'        => $compId,
        ]);
    }

    public function competencyUpdate(string $id)
    {
        $compId = ctype_digit($id) ? (int) $id : 0;
        $rules = [
            'name'        => 'required|max_length[255]',
            'description' => 'permit_empty',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $post = $this->request->getPost();
        try {
            (new CompetencyModel())->update($compId, [
                'name'        => $post['name'],
                'description' => $post['description'] ?? null,
                'is_active'   => isset($post['is_active']) ? 1 : 1,
            ]);
            (new AuditService())->log((int) session()->get('user_id'), 'competency.update', 'competencies', 'competency', $compId);
            return redirect()->to('dashboard/regulasi/kompetensi-terkait')->with('success', 'Kompetensi diperbarui.');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function competencyDelete(string $id)
    {
        $compId = ctype_digit($id) ? (int) $id : 0;
        try {
            (new CompetencyModel())->delete($compId);
            (new AuditService())->log((int) session()->get('user_id'), 'competency.delete', 'competencies', 'competency', $compId);
            return redirect()->to('dashboard/regulasi/kompetensi-terkait')->with('success', 'Kompetensi dihapus.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function attachCompetency(string $regId)
    {
        $regId = ctype_digit($regId) ? (int) $regId : 0;
        $compId = (int) $this->request->getPost('competency_id');
        if ($regId <= 0 || $compId <= 0) {
            return redirect()->back()->with('error', 'Regulation ID dan Competency ID wajib diisi.');
        }
        $now = date('Y-m-d H:i:s');
        try {
            db_connect()->table('regulation_competencies')->insert([
                'regulation_id' => $regId,
                'competency_id'  => $compId,
                'created_at'     => $now,
            ]);
            (new AuditService())->log((int) session()->get('user_id'), 'regulation.competency.attach', 'regulation_competencies', 'regulation_competency', null, [
                'regulation_id' => $regId, 'competency_id' => $compId,
            ]);
            return redirect()->to('dashboard/regulasi/dokumen/' . $regId)->with('success', 'Kompetensi terkait ditambah.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // ============== HELPERS ==============

    protected function uploadCoverImage(?\CodeIgniter\HTTP\Files\UploadedFile $file, ?int $regulationId = null): ?int
    {
        if ($file === null || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }
        $mime = $file->getMimeType();
        if (! in_array($mime, ['image/jpeg', 'image/png', 'image/webp', 'image/gif'], true)) {
            return null;
        }
        $uploadDir = WRITEPATH . 'uploads/regulations/covers';
        if (! is_dir($uploadDir) && ! @mkdir($uploadDir, 0755, true)) {
            return null;
        }
        $storedName = $file->getRandomName();
        $file->move($uploadDir, $storedName);
        $relativePath = 'uploads/regulations/covers/' . $storedName;
        $now = date('Y-m-d H:i:s');
        $db = db_connect();
        $db->table('file_assets')->insert([
            'disk'             => 'local',
            'path'             => $relativePath,
            'original_name'    => $file->getClientName() ?: $storedName,
            'stored_name'      => $storedName,
            'extension'        => $file->getClientExtension() ?: 'jpg',
            'mime_type'        => $mime,
            'size_bytes'       => (int) ($file->getSize() ?: 0),
            'visibility'       => 'public',
            'lifecycle_status' => 'available',
            'resource_type'    => 'regulation_cover',
            'resource_id'      => $regulationId,
            'uploaded_by'      => (int) session()->get('user_id'),
            'available_at'     => $now,
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);
        return (int) $db->insertID();
    }

    protected function uploadCategoryIcon(?\CodeIgniter\HTTP\Files\UploadedFile $file, ?int $categoryId = null): ?int
    {
        if ($file === null || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }
        $mime = $file->getMimeType();
        if (! in_array($mime, ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'], true)) {
            return null;
        }
        $uploadDir = WRITEPATH . 'uploads/regulations/categories';
        if (! is_dir($uploadDir) && ! @mkdir($uploadDir, 0755, true)) {
            return null;
        }
        $storedName = $file->getRandomName();
        $file->move($uploadDir, $storedName);
        $relativePath = 'uploads/regulations/categories/' . $storedName;
        $now = date('Y-m-d H:i:s');
        $db = db_connect();
        $db->table('file_assets')->insert([
            'disk'             => 'local',
            'path'             => $relativePath,
            'original_name'    => $file->getClientName() ?: $storedName,
            'stored_name'      => $storedName,
            'extension'        => $file->getClientExtension() ?: 'png',
            'mime_type'        => $mime,
            'size_bytes'       => (int) ($file->getSize() ?: 0),
            'visibility'       => 'public',
            'lifecycle_status' => 'available',
            'resource_type'    => 'regulation_category_icon',
            'resource_id'      => $categoryId,
            'uploaded_by'      => (int) session()->get('user_id'),
            'available_at'     => $now,
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);
        return (int) $db->insertID();
    }

    protected function syncRegulationStages(int $regulationId, array $stageIds): void
    {
        $db = db_connect();
        $db->table('regulation_procurement_stages')->where('regulation_id', $regulationId)->delete();
        $now = date('Y-m-d H:i:s');
        foreach ($stageIds as $stgId) {
            $sid = (int) $stgId;
            if ($sid > 0) {
                $db->table('regulation_procurement_stages')->insert([
                    'regulation_id'        => $regulationId,
                    'procurement_stage_id' => $sid,
                    'relevance'            => 'high',
                    'created_at'           => $now,
                ]);
            }
        }
    }

    protected function syncRegulationCompetencies(int $regulationId, array $competencyIds): void
    {
        $db = db_connect();
        $db->table('regulation_competencies')->where('regulation_id', $regulationId)->delete();
        $now = date('Y-m-d H:i:s');
        foreach ($competencyIds as $cId) {
            $cid = (int) $cId;
            if ($cid > 0) {
                $db->table('regulation_competencies')->insert([
                    'regulation_id' => $regulationId,
                    'competency_id' => $cid,
                    'relevance'     => 'high',
                    'created_at'    => $now,
                ]);
            }
        }
    }

    private function parseDate(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        $ts = strtotime($value);
        return $ts === false ? null : date('Y-m-d H:i:s', $ts);
    }
}