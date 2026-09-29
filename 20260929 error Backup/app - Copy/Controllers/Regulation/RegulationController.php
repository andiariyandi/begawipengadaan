<?php

namespace App\Controllers\Regulation;

use App\Controllers\BaseController;
use App\Models\RegulationModel;
use App\Models\RegulationCategoryModel;
use App\Models\RegulationVersionModel;
use App\Services\AuditService;

class RegulationController extends BaseController
{
    private array $allowedMimes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];

    public function browse()
    {
        $categoryId = $this->request->getGet('category_id') ?: null;
        $keyword    = $this->request->getGet('q') ?: null;

        $view = $this->request->getUri()->getPath() === 'regulasi-saya'
            ? 'landing/user/regulations'
            : 'regulation/browse';

        return view($view, [
            'title'       => 'Repository Regulasi PBJ',
            'subTitle'    => 'Repository Regulasi PBJ',
            'regulations' => (new RegulationModel())->browse($categoryId ? (int) $categoryId : null, $keyword),
            'categories'  => (new RegulationCategoryModel())->findAll(),
            'keyword'     => $keyword,
            'categoryId'  => $categoryId,
        ]);
    }


    public function myRegulations()
    {
        $categoryId = $this->request->getGet('category_id') ?: null;
        $keyword    = $this->request->getGet('q') ?: null;

        return view('landing/user/regulations', [
            'title'       => 'Regulasi Saya',
            'subTitle'    => 'Referensi PBJ',
            'regulations' => (new RegulationModel())->browse($categoryId ? (int) $categoryId : null, $keyword),
            'categories'  => (new RegulationCategoryModel())->findAll(),
            'keyword'     => $keyword,
            'categoryId'  => $categoryId,
        ]);
    }

    public function detail(int $id)
    {
        $regulation = (new RegulationModel())->withDetail($id);

        if (! $regulation) {
            return redirect()->to(route_to('regulation.browse'))->with('error', 'Regulasi tidak ditemukan.');
        }

        return view('regulation/detail', [
            'title'      => $regulation['title'],
            'subTitle'   => 'Repository Regulasi PBJ',
            'regulation' => $regulation,
            'versions'   => (new RegulationVersionModel())->historyFor($id),
        ]);
    }

    public function create()
    {
        return view('regulation/form', [
            'title'      => 'Tambah Regulasi',
            'subTitle'   => 'Tambah Regulasi',
            'categories' => (new RegulationCategoryModel())->findAll(),
        ]);
    }

    public function store()
    {
        $rules = [
            'regulation_category_id' => 'required|is_natural_no_zero',
            'title'                  => 'required|max_length[255]',
            'nomor_regulasi'         => 'required|max_length[100]|is_unique[regulations.nomor_regulasi]',
            'tahun'                  => 'required|numeric',
            'file'                   => 'uploaded[file]|max_size[file,10240]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Semua field wajib diisi dengan benar, nomor regulasi harus unik.');
        }

        $file = $this->request->getFile('file');

        if (! $file->isValid() || ! in_array($file->getMimeType(), $this->allowedMimes, true)) {
            return redirect()->back()->withInput()->with('error', 'Tipe file tidak diizinkan (hanya PDF/Word).');
        }

        $regulationModel = new RegulationModel();
        $userId          = session()->get('user_id');
        $uploadDirectory = WRITEPATH . 'uploads/regulations';

        if (! is_dir($uploadDirectory) && ! mkdir($uploadDirectory, 0755, true) && ! is_dir($uploadDirectory)) {
            return redirect()->back()->withInput()->with('error', 'Direktori upload regulasi tidak dapat dibuat.');
        }

        if (! is_writable($uploadDirectory)) {
            return redirect()->back()->withInput()->with('error', 'Direktori upload regulasi tidak dapat ditulis.');
        }

        $regulationId = $regulationModel->insert([
            'regulation_category_id' => $this->request->getPost('regulation_category_id'),
            'title'                  => $this->request->getPost('title'),
            'nomor_regulasi'         => $this->request->getPost('nomor_regulasi'),
            'tahun'                  => $this->request->getPost('tahun'),
            'status'                 => 'berlaku',
            'uploaded_by'            => $userId,
        ], true);

        if (! $regulationId) {
            return redirect()->back()->withInput()->with('error', 'Data regulasi gagal disimpan.');
        }

        $newName = $file->getRandomName();
        $file->move($uploadDirectory, $newName);

        $versionId = (new RegulationVersionModel())->insert([
            'regulation_id'  => $regulationId,
            'version_number' => 1,
            'file_path'      => 'uploads/regulations/' . $newName,
            'change_notes'   => 'Unggahan awal.',
            'uploaded_by'    => $userId,
            'created_at'     => date('Y-m-d H:i:s'),
        ], true);

        if (! $versionId) {
            @unlink($uploadDirectory . DIRECTORY_SEPARATOR . $newName);
            $regulationModel->delete($regulationId);

            return redirect()->back()->withInput()->with('error', 'Versi regulasi gagal disimpan.');
        }

        (new AuditService())->log($userId, 'create', 'regulation', 'regulations', $regulationId);

        return redirect()->to(route_to('regulation.detail', $regulationId))->with('message', 'Regulasi berhasil diunggah.');
    }

    public function storeVersion(int $regulationId)
    {
        $regulationModel = new RegulationModel();
        $versionModel    = new RegulationVersionModel();

        if (! $regulationModel->find($regulationId)) {
            return redirect()->to(route_to('regulation.browse'))->with('error', 'Regulasi tidak ditemukan.');
        }

        $rules = [
            'change_notes' => 'required',
            'file'         => 'uploaded[file]|max_size[file,10240]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('error', 'Catatan perubahan dan file wajib diisi.');
        }

        $file = $this->request->getFile('file');

        if (! $file->isValid() || ! in_array($file->getMimeType(), $this->allowedMimes, true)) {
            return redirect()->back()->with('error', 'Tipe file tidak diizinkan (hanya PDF/Word).');
        }

        $uploadDirectory = WRITEPATH . 'uploads/regulations';

        if (! is_dir($uploadDirectory) && ! mkdir($uploadDirectory, 0755, true) && ! is_dir($uploadDirectory)) {
            return redirect()->back()->withInput()->with('error', 'Direktori upload regulasi tidak dapat dibuat.');
        }

        if (! is_writable($uploadDirectory)) {
            return redirect()->back()->withInput()->with('error', 'Direktori upload regulasi tidak dapat ditulis.');
        }

        $newName = $file->getRandomName();
        $file->move($uploadDirectory, $newName);

        $userId     = session()->get('user_id');
        $nextNumber = $versionModel->latestVersionNumber($regulationId) + 1;

        $versionId = $versionModel->insert([
            'regulation_id'  => $regulationId,
            'version_number' => $nextNumber,
            'file_path'      => 'uploads/regulations/' . $newName,
            'change_notes'   => $this->request->getPost('change_notes'),
            'uploaded_by'    => $userId,
            'created_at'     => date('Y-m-d H:i:s'),
        ], true);

        if (! $versionId) {
            @unlink($uploadDirectory . DIRECTORY_SEPARATOR . $newName);

            return redirect()->back()->withInput()->with('error', 'Versi regulasi gagal disimpan.');
        }

        (new AuditService())->log($userId, 'create', 'regulation', 'regulation_versions', $regulationId, ['version' => $nextNumber]);

        return redirect()->to(route_to('regulation.detail', $regulationId))->with('message', "Versi {$nextNumber} berhasil ditambahkan.");
    }

    public function toggleStatus(int $id)
    {
        $regulationModel = new RegulationModel();
        $regulation      = $regulationModel->find($id);

        if (! $regulation) {
            return redirect()->to(route_to('regulation.browse'))->with('error', 'Regulasi tidak ditemukan.');
        }

        $newStatus = $regulation['status'] === 'berlaku' ? 'tidak_berlaku' : 'berlaku';
        $regulationModel->update($id, ['status' => $newStatus]);

        (new AuditService())->log(session()->get('user_id'), 'update', 'regulation', 'regulations', $id, ['status' => $newStatus]);

        return redirect()->to(route_to('regulation.detail', $id))->with('message', "Status diubah menjadi {$newStatus}.");
    }

    /**
     * File disimpan di WRITEPATH (bukan public) — pola sama seperti download
     * Template KMS, supaya konsisten & aman (tidak bisa diakses langsung via URL).
     */
    public function download(int $versionId)
    {
        $version = (new RegulationVersionModel())->find($versionId);

        if (! $version) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        $basePath = realpath(WRITEPATH . 'uploads/regulations');
        $fullPath = realpath(WRITEPATH . ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $version['file_path']), DIRECTORY_SEPARATOR));

        if ($basePath === false || $fullPath === false || ! is_file($fullPath)) {
            return redirect()->back()->with('error', 'File tidak ditemukan di server.');
        }

        $basePath = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (strncmp($fullPath, $basePath, strlen($basePath)) !== 0) {
            return redirect()->back()->with('error', 'File tidak ditemukan di server.');
        }

        (new AuditService())->log(session()->get('user_id'), 'download', 'regulation', 'regulation_versions', $versionId);

        return $this->response->download($fullPath, null)->setFileName('regulasi-v' . $version['version_number'] . '.' . pathinfo($fullPath, PATHINFO_EXTENSION));
    }
}
