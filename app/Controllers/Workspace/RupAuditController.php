<?php

namespace App\Controllers\Workspace;

use App\Controllers\BaseController;
use App\Services\RupAudit\RupAccessHelper;
use App\Services\RupAudit\RupAuditService;
use App\Services\RupAudit\RupExportService;
use CodeIgniter\HTTP\ResponseInterface;

class RupAuditController extends BaseController
{
    protected RupAuditService $auditService;
    protected RupExportService $exportService;

    public function __construct()
    {
        $this->auditService  = new RupAuditService();
        $this->exportService = new RupExportService();
    }

    /**
     * Memeriksa otorisasi akses pengguna.
     */
    private function checkAccess(): ?ResponseInterface
    {
        if (! RupAccessHelper::canAccess()) {
            return redirect()->to(route_to('workspace.home'))
                ->with('error', RupAccessHelper::getAccessDeniedMessage());
        }
        return null;
    }

    /**
     * Halaman Beranda Modul: KPI Eksekutif, Form Unggah CSV & Riwayat Batch.
     */
    public function index()
    {
        if ($res = $this->checkAccess()) {
            return $res;
        }

        $batches = $this->auditService->getBatches(20);
        $latestBatch = ! empty($batches) ? $batches[0] : null;

        $kpi = [
            'total_opd'              => 0,
            'total_paket'            => 0,
            'total_pagu'             => 0.0,
            'persen_epurchasing'     => 0.0,
            'persen_umk_penyedia'    => 0.0,
            'persen_umk_eligible'    => 0.0,
            'pagu_kritis_tinggi'     => 0.0,
            'avg_rci_score'          => 0.0,
            'batch_uuid'             => '-',
        ];

        if ($latestBatch) {
            $macro = json_decode((string) ($latestBatch['macro_summary'] ?? '{}'), true);
            $dist = $macro['distribusi_temuan_unik'] ?? [];
            $paguBermasalah = (float) ($dist['kritis']['total_pagu_terdampak'] ?? 0.0) + (float) ($dist['tinggi']['total_pagu_terdampak'] ?? 0.0);

            $kpi = [
                'total_opd'           => (int) $latestBatch['total_opd'],
                'total_paket'         => (int) $latestBatch['total_packages'],
                'total_pagu'          => (float) $latestBatch['total_pagu'],
                'persen_epurchasing'  => (float) ($macro['persen_e_purchasing_penyedia'] ?? 0.0),
                'persen_umk_penyedia' => (float) ($macro['persen_umk_terhadap_penyedia_non_dikecualikan'] ?? 0.0),
                'persen_umk_eligible' => (float) ($macro['persen_umk_eligible_le15m'] ?? 0.0),
                'pagu_kritis_tinggi'  => $paguBermasalah,
                'avg_rci_score'       => (float) $latestBatch['avg_rci_score'],
                'batch_uuid'          => $latestBatch['batch_uuid'],
            ];
        }

        return view('main/workspace/pages/rup_audit/index', [
            'pageTitle'   => 'Sistem Audit Otomatis RUP (Universal SIRUP Compliance Engine v3.1)',
            'batches'     => $batches,
            'latestBatch' => $latestBatch,
            'kpi'         => $kpi,
        ]);
    }

    /**
     * Memproses unggahan berkas CSV RUP.
     */
    public function upload()
    {
        if ($res = $this->checkAccess()) {
            return $res;
        }

        $file = $this->request->getFile('csv_file');
        if (! $file || ! $file->isValid()) {
            return redirect()->back()->with('error', 'Silakan pilih berkas CSV RUP yang valid.');
        }

        $ext = strtolower($file->getClientExtension());
        if (! in_array($ext, ['csv', 'txt'], true)) {
            return redirect()->back()->with('error', 'Format berkas tidak valid. Harap unggah berkas berekstensi .csv hasil ekspor SIRUP.');
        }

        $targetDir = WRITEPATH . 'uploads/rup_audit/';
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }

        $newFileName = 'RUP_' . date('Ymd_His') . '_' . substr(bin2hex(random_bytes(4)), 0, 6) . '.csv';
        $file->move($targetDir, $newFileName);
        $fullPath = $targetDir . $newFileName;

        $userId = (int) (session()->get('user_id') ?: session()->get('id') ?: 0);
        $prevBatchId = (int) $this->request->getPost('previous_batch_id') ?: null;
        $notes = trim((string) $this->request->getPost('notes'));

        $meta = [
            'user_id'           => $userId,
            'filename'          => $file->getClientName(),
            'file_size'         => filesize($fullPath),
            'previous_batch_id' => $prevBatchId,
            'notes'             => $notes,
        ];

        $auditResult = $this->auditService->processUpload($fullPath, $meta);

        // Hapus file sementara setelah diproses
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }

        if (! $auditResult['success']) {
            return redirect()->back()->with('error', $auditResult['error'] ?? 'Gagal memproses audit RUP.');
        }

        return redirect()->to(site_url('workspace/audit-rup/detail/' . $auditResult['batch_uuid']))
            ->with('message', 'Audit RUP berhasil diselesaikan! Seluruh data kepatuhan telah dihitung sesuai Perpres 46/2025.');
    }

    /**
     * Dashboard 5 Tab Analitik Audit RUP.
     */
    public function detail(string $batchUuid)
    {
        if ($res = $this->checkAccess()) {
            return $res;
        }

        $batch = $this->auditService->getBatch($batchUuid);
        if (! $batch) {
            return redirect()->to(site_url('workspace/audit-rup'))
                ->with('error', 'Data batch audit tidak ditemukan.');
        }

        $batchId = (int) $batch['id'];
        $macroSummary = json_decode((string) ($batch['macro_summary'] ?? '{}'), true);

        // Filter parameters
        $activeTab   = $this->request->getGet('tab') ?: 'leaderboard';
        $opdFilter   = trim((string) $this->request->getGet('opd'));
        $severity    = trim((string) $this->request->getGet('severity')) ?: 'ALL';
        $budgetCycle = trim((string) $this->request->getGet('cycle')) ?: 'ALL';
        $diffStatus  = trim((string) $this->request->getGet('diff')) ?: 'ALL';
        $searchTerm  = trim((string) $this->request->getGet('q'));

        $page   = max(1, (int) $this->request->getGet('page'));
        $limit  = 50;
        $offset = ($page - 1) * $limit;

        $filters = [
            'opd'          => $opdFilter,
            'severity'     => $severity,
            'budget_cycle' => $budgetCycle,
            'diff_status'  => $diffStatus,
            'search'       => $searchTerm,
        ];

        $opdLeaderboard = $this->auditService->getOpdLeaderboard($batchId);
        $packages       = $this->auditService->getPackagesFiltered($batchId, $filters, $limit, $offset);
        $totalPackagesFiltered = $this->auditService->countPackagesFiltered($batchId, $filters);
        $totalPages     = (int) ceil($totalPackagesFiltered / $limit);

        $clusters       = $this->auditService->getClusters($batchId, $opdFilter ?: null);

        // Daftar unik nama OPD untuk dropdown filter
        $opdList = array_column($opdLeaderboard, 'nama_opd_clean');

        return view('main/workspace/pages/rup_audit/detail', [
            'pageTitle'             => 'Hasil Audit RUP: ' . $batch['batch_uuid'],
            'batch'                 => $batch,
            'macroSummary'          => $macroSummary,
            'activeTab'             => $activeTab,
            'filters'               => $filters,
            'opdLeaderboard'        => $opdLeaderboard,
            'packages'              => $packages,
            'totalPackagesFiltered' => $totalPackagesFiltered,
            'currentPage'           => $page,
            'totalPages'            => $totalPages,
            'clusters'              => $clusters,
            'opdList'               => $opdList,
        ]);
    }

    /**
     * Endpoint AJAX untuk mengambil rincian temuan pelanggaran per paket.
     */
    public function apiRowDetail(int $packageId): ResponseInterface
    {
        if (! RupAccessHelper::canAccess()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(403);
        }

        $detail = $this->auditService->getPackageDetail($packageId);
        if (! $detail) {
            return $this->response->setJSON(['success' => false, 'message' => 'Paket tidak ditemukan'])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'success' => true,
            'data'    => $detail,
        ]);
    }

    /**
     * Unduh berkas kerja Excel 5-Sheet (.xlsx).
     */
    public function exportExcel(string $batchUuid)
    {
        if ($res = $this->checkAccess()) {
            return $res;
        }

        $batch = $this->auditService->getBatch($batchUuid);
        if (! $batch) {
            return redirect()->back()->with('error', 'Batch audit tidak ditemukan.');
        }

        try {
            $excelPath = $this->exportService->generateExcel5Sheet((int) $batch['id']);
            $downloadName = 'Hasil_Audit_RUP_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $batch['batch_uuid']) . '.xlsx';
            return $this->response->download($excelPath, null)->setFileName($downloadName);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menghasilkan berkas Excel: ' . $e->getMessage());
        }
    }

    /**
     * Cetak Berita Acara Reviu RUP (.pdf).
     */
    public function exportPdf(string $batchUuid, string $kodeSatkerOrOpd)
    {
        if ($res = $this->checkAccess()) {
            return $res;
        }

        $batch = $this->auditService->getBatch($batchUuid);
        if (! $batch) {
            return redirect()->back()->with('error', 'Batch audit tidak ditemukan.');
        }

        try {
            $pdfBytes = $this->exportService->generateBeritaAcaraPdf((int) $batch['id'], urldecode($kodeSatkerOrOpd));
            $filename = 'Berita_Acara_Audit_RUP_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $kodeSatkerOrOpd) . '.pdf';

            return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
                ->setBody($pdfBytes);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menghasilkan Berita Acara PDF: ' . $e->getMessage());
        }
    }

    /**
     * Hapus riwayat batch audit.
     */
    public function deleteBatch(string $batchUuid)
    {
        if ($res = $this->checkAccess()) {
            return $res;
        }

        $batch = $this->auditService->getBatch($batchUuid);
        if (! $batch) {
            return redirect()->back()->with('error', 'Batch audit tidak ditemukan.');
        }

        $this->auditService->deleteBatch((int) $batch['id']);

        return redirect()->to(site_url('workspace/audit-rup'))
            ->with('message', 'Riwayat batch audit ' . $batch['batch_uuid'] . ' berhasil dihapus.');
    }
}
