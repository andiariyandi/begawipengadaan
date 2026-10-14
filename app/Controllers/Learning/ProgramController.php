<?php

namespace App\Controllers\Learning;

use App\Controllers\BaseController;
use App\Models\Learning\ProgramModel;

class ProgramController extends BaseController
{
    protected ProgramModel $programModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        $this->programModel = new ProgramModel();
    }

    public function index(): string
    {
        $keyword = trim((string) $this->request->getGet('keyword'));
        $status = trim((string) $this->request->getGet('status'));

        $builder = $this->programModel
            ->select('programs.*,
                      (SELECT COUNT(1) FROM courses c WHERE c.program_id = programs.id) AS course_count,
                      (SELECT COUNT(1) FROM learning_classes lc JOIN courses c ON c.id = lc.course_id WHERE c.program_id = programs.id) AS class_count')
            ->orderBy('programs.created_at', 'DESC');
        if ($keyword !== '') {
            $builder->groupStart()
                ->like('programs.title', $keyword)
                ->orLike('programs.slug', $keyword)
                ->orLike('programs.summary', $keyword)
                ->groupEnd();
        }
        if ($status !== '') {
            $builder->where('programs.status', $status);
        }

        $perPage = max(5, min(100, (int) ($this->request->getGet('per_page') ?: 15)));
        $page = max(1, (int) ($this->request->getGet('page_programs') ?: 1));
        $total = $this->programModel->countAllResults(false);
        $programs = $this->programModel->findAll($perPage, ($page - 1) * $perPage);

        $pager = service('pager');
        $pager->store('programs', $page, $perPage, $total, 0);

        $db = db_connect();
        $stats = [
            'total'     => (int) $db->table('programs')->countAllResults(),
            'published' => (int) $db->table('programs')->where('status', 'published')->countAllResults(),
            'draft'     => (int) $db->table('programs')->where('status', 'draft')->countAllResults(),
            'archived'  => (int) $db->table('programs')->where('status', 'archived')->countAllResults(),
        ];

        return view('dashboard/learning/programs/index', [
            'title'         => 'Program | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Program Pembelajaran',
            'programs'      => $programs,
            'stats'         => $stats,
            'pager'         => $pager,
            'filters'       => [
                'keyword' => $keyword,
                'status'  => $status,
            ],
            'activeMenu'    => 'learning',
            'activeSubmenu' => 'programs',
        ]);
    }

    public function create(): string
    {
        return view('dashboard/learning/programs/form', [
            'title' => 'Tambah Program | Dashboard BegawiPengadaan',
            'pageTitle' => 'Tambah Program',
            'formMode' => 'create',
            'program' => null,
            'activeMenu' => 'learning',
            'activeSubmenu' => 'programs',
        ]);
    }

    public function store()
    {
        $data = $this->validatedProgramData();

        if ($data === null) {
            return redirect()->back()->withInput();
        }

        $userId = session()->get('user_id');
        if ($userId !== null) {
            $data['created_by'] = (int) $userId;
        }

        $this->programModel->insert($data);

        return redirect()->to(route_to('dashboard.learning.programs'))->with('success', 'Program berhasil disimpan.');
    }

    public function detail(string $id): string
    {
        $program = $this->programModel->find($id);
        if ($program === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Program tidak ditemukan.');
        }

        return view('dashboard/learning/programs/detail', [
            'title' => 'Detail Program | Dashboard BegawiPengadaan',
            'pageTitle' => 'Detail Program',
            'programId' => $id,
            'program' => $program,
            'activeMenu' => 'learning',
            'activeSubmenu' => 'programs',
        ]);
    }

    public function edit(string $id): string
    {
        $program = $this->programModel->find($id);
        if ($program === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Program tidak ditemukan.');
        }

        return view('dashboard/learning/programs/form', [
            'title' => 'Ubah Program | Dashboard BegawiPengadaan',
            'pageTitle' => 'Ubah Program',
            'formMode' => 'edit',
            'programId' => $id,
            'program' => $program,
            'activeMenu' => 'learning',
            'activeSubmenu' => 'programs',
        ]);
    }

    public function update(string $id)
    {
        $existing = $this->programModel->find($id);
        if ($existing === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Program tidak ditemukan.');
        }

        $data = $this->validatedProgramData($existing['cover_url'] ?? null, $existing['cover_file_id'] ?? null);

        if ($data === null) {
            return redirect()->back()->withInput();
        }

        $userId = session()->get('user_id');
        if ($userId !== null) {
            $data['updated_by'] = (int) $userId;
        }

        $this->programModel->update($id, $data);

        return redirect()->to(route_to('dashboard.learning.programs'))->with('success', 'Program berhasil diperbarui.');
    }

    public function delete(string $id)
    {
        $program = $this->programModel->find($id);
        if ($program === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Program tidak ditemukan.');
        }

        $db = db_connect();
        $courseCount = (int) $db->table('courses')->where('program_id', $id)->countAllResults();
        if ($courseCount > 0) {
            return redirect()->back()->with('error', "Tidak dapat menghapus program: Masih terdapat {$courseCount} course terkait pada program ini.");
        }

        $this->programModel->delete($id);

        return redirect()->to(route_to('dashboard.learning.programs'))->with('success', 'Program berhasil dihapus.');
    }

    private function validatedProgramData(?string $existingCoverUrl = null, ?int $existingCoverFileId = null): ?array
    {
        $rules = [
            'title'      => 'required',
            'slug'       => 'required',
            'status'     => 'permit_empty',
            'visibility' => 'permit_empty',
        ];

        if (! $this->validate($rules)) {
            return null;
        }

        // Handle uploaded cover image file
        $file = $this->request->getFile('cover_image');
        $uploadedUrl = null;
        $uploadedFileId = null;

        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
            $mime = $file->getMimeType();
            if (! in_array($mime, $allowedMimes, true)) {
                session()->setFlashdata('error', 'Format file gambar harus JPG, PNG, atau WEBP.');
                return null;
            }
            if ($file->getSize() > 2097152) {
                session()->setFlashdata('error', 'Ukuran file gambar maksimal 2 MB.');
                return null;
            }

            $targetDir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'learning' . DIRECTORY_SEPARATOR . 'programs';
            if (! is_dir($targetDir)) {
                @mkdir($targetDir, 0755, true);
            }

            $storedName = $file->getRandomName();
            if ($file->move($targetDir, $storedName)) {
                $uploadedUrl = 'uploads/learning/programs/' . $storedName;

                try {
                    $now = date('Y-m-d H:i:s');
                    $db = db_connect();
                    $db->table('file_assets')->insert([
                        'disk'             => 'local',
                        'path'             => $uploadedUrl,
                        'original_name'    => $file->getClientName() ?: $storedName,
                        'stored_name'      => $storedName,
                        'extension'        => $file->getClientExtension() ?: 'jpg',
                        'mime_type'        => $mime,
                        'size_bytes'       => (int) ($file->getSize() ?: 0),
                        'visibility'       => 'public',
                        'lifecycle_status' => 'available',
                        'resource_type'    => 'program_cover',
                        'resource_id'      => null,
                        'uploaded_by'      => (int) session()->get('user_id'),
                        'available_at'     => $now,
                        'created_at'       => $now,
                        'updated_at'       => $now,
                    ]);
                    $uploadedFileId = (int) $db->insertID();
                } catch (\Throwable $e) {
                    log_message('error', 'File asset registration failed: ' . $e->getMessage());
                }
            }
        }

        $urlPost = $this->request->getPost('cover_url');
        $coverUrl = $uploadedUrl ?: (trim((string) $urlPost) ?: $existingCoverUrl);
        $coverFileId = $uploadedFileId ?: ($this->request->getPost('cover_file_id') ? (int) $this->request->getPost('cover_file_id') : $existingCoverFileId);

        return [
            'title'               => trim((string) $this->request->getPost('title')),
            'slug'                => trim((string) $this->request->getPost('slug')),
            'summary'             => $this->request->getPost('summary'),
            'description'         => $this->request->getPost('description'),
            'target_audience'     => $this->request->getPost('target_audience'),
            'prerequisites'       => $this->request->getPost('prerequisites'),
            'learning_objectives' => $this->request->getPost('learning_objectives'),
            'status'              => $this->request->getPost('status') ?: 'draft',
            'visibility'          => $this->request->getPost('visibility') ?: 'public',
            'cover_file_id'       => $coverFileId,
            'cover_url'           => $coverUrl,
            'featured'            => $this->request->getPost('featured') ? 1 : 0,
        ];
    }
}
