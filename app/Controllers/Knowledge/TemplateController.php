<?php

namespace App\Controllers\Knowledge;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\TemplateModel;
use App\Services\AuditService;

class TemplateController extends BaseController
{
    private const UPLOAD_DIRECTORY = 'uploads/templates';

    private const ALLOWED_MIMES = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];

    public function browse()
    {
        $categoryId = $this->request->getGet('category_id') ?: null;
        $keyword    = $this->request->getGet('q') ?: null;
        $templateModel = new TemplateModel();

        return view('knowledge/templates/browse', [
            'title'      => 'Template Dokumen PBJ',
            'subTitle'   => 'Template Dokumen PBJ',
            'templates'  => $templateModel->browse($categoryId ? (int) $categoryId : null, $keyword),
            'categories' => (new CategoryModel())->kmsCategories(),
            'keyword'    => $keyword,
            'categoryId' => $categoryId,
        ]);
    }

    public function create()
    {
        return view('knowledge/templates/form', [
            'title'      => 'Tambah Template',
            'subTitle'   => 'Tambah Template',
            'categories' => (new CategoryModel())->kmsCategories(),
        ]);
    }

    public function store()
    {
        $rules = [
            'title'       => 'required|max_length[200]',
            'category_id' => 'required|is_natural_no_zero',
            'file'        => 'uploaded[file]|max_size[file,10240]', // 10 MB
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Judul, kategori, dan file template wajib diisi.');
        }

        $file = $this->request->getFile('file');

        if (! $file || ! $file->isValid() || ! in_array($file->getMimeType(), self::ALLOWED_MIMES, true)) {
            return redirect()->back()->withInput()->with('error', 'Tipe file tidak diizinkan (hanya PDF/Word/Excel).');
        }

        $uploadPath = WRITEPATH . self::UPLOAD_DIRECTORY;

        if (! is_dir($uploadPath) && ! mkdir($uploadPath, 0750, true)) {
            return redirect()->back()->withInput()->with('error', 'Direktori penyimpanan template tidak tersedia.');
        }

        if (! is_writable($uploadPath)) {
            return redirect()->back()->withInput()->with('error', 'Direktori penyimpanan template tidak dapat ditulis.');
        }

        $newName = $file->getRandomName();
        $file->move($uploadPath, $newName);

        $templateModel = new TemplateModel();

        $templateId = $templateModel->insert([
            'category_id' => $this->request->getPost('category_id'),
            'title'       => $this->request->getPost('title'),
            'file_path'   => self::UPLOAD_DIRECTORY . '/' . $newName,
        ], true);

        if (! $templateId) {
            unlink($uploadPath . DIRECTORY_SEPARATOR . $newName);

            return redirect()->back()->withInput()->with('error', 'Template gagal disimpan.');
        }

        (new AuditService())->log(session()->get('user_id'), 'create', 'kms', 'templates', $templateId);

        return redirect()->to(route_to('knowledge.templates.browse'))->with('message', 'Template berhasil ditambahkan.');
    }

    public function delete(int $id)
    {
        $templateModel = new TemplateModel();
        $template = $templateModel->find($id);

        if (! $template) {
            return redirect()->to(route_to('knowledge.templates.browse'))->with('error', 'Template tidak ditemukan.');
        }

        $templateModel->delete($id);

        $filePath = $this->storedFilePath($template);

        if ($filePath !== null && is_file($filePath) && ! unlink($filePath)) {
            return redirect()->to(route_to('knowledge.templates.browse'))->with('error', 'Template terhapus, tetapi file fisiknya gagal dibersihkan.');
        }

        (new AuditService())->log(session()->get('user_id'), 'delete', 'kms', 'templates', $id);

        return redirect()->to(route_to('knowledge.templates.browse'))->with('message', 'Template berhasil dihapus.');
    }

    public function download(int $id)
    {
        $template = (new TemplateModel())->find($id);

        if (! $template) {
            return redirect()->to(route_to('knowledge.templates.browse'))->with('error', 'Template tidak ditemukan.');
        }

        $fullPath = $this->storedFilePath($template);

        if ($fullPath === null) {
            return redirect()->to(route_to('knowledge.templates.browse'))->with('error', 'File template tidak ditemukan di server.');
        }

        (new AuditService())->log(session()->get('user_id'), 'download', 'kms', 'templates', $id);

        $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
        $fileName  = trim((string) preg_replace('/[^\pL\pN._-]+/u', '-', $template['title']), '-.');
        $fileName  = $fileName !== '' ? $fileName : 'template';

        return $this->response->download($fullPath, null)->setFileName($fileName . '.' . $extension);
    }

    private function storedFilePath(array $template): ?string
    {
        $basePath = realpath(WRITEPATH . self::UPLOAD_DIRECTORY);
        $filePath = realpath(WRITEPATH . ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $template['file_path']), DIRECTORY_SEPARATOR));

        if ($basePath === false || $filePath === false || ! is_file($filePath)) {
            return null;
        }

        $basePath = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        return strncmp($filePath, $basePath, strlen($basePath)) === 0 ? $filePath : null;
    }
}
