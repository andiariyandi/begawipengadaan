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
        return view('dashboard/learning/programs/index', [
            'title' => 'Program | Dashboard BegawiPBJ',
            'pageTitle' => 'Program',
            'programs' => $this->programModel->orderBy('created_at', 'DESC')->findAll(),
            'activeMenu' => 'learning',
            'activeSubmenu' => 'programs',
        ]);
    }

    public function create(): string
    {
        return view('dashboard/learning/programs/form', [
            'title' => 'Tambah Program | Dashboard BegawiPBJ',
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
            'title' => 'Detail Program | Dashboard BegawiPBJ',
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
            'title' => 'Ubah Program | Dashboard BegawiPBJ',
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
        if ($this->programModel->find($id) === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Program tidak ditemukan.');
        }

        $data = $this->validatedProgramData();

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

    private function validatedProgramData(): ?array
    {
        $rules = [
            'title' => 'required',
            'slug' => 'required',
            'status' => 'permit_empty',
            'visibility' => 'permit_empty',
        ];

        if (! $this->validate($rules)) {
            return null;
        }

        return [
            'title' => trim((string) $this->request->getPost('title')),
            'slug' => trim((string) $this->request->getPost('slug')),
            'summary' => $this->request->getPost('summary'),
            'description' => $this->request->getPost('description'),
            'status' => $this->request->getPost('status') ?: 'draft',
            'visibility' => $this->request->getPost('visibility') ?: 'public',
            'cover_file_id' => $this->request->getPost('cover_file_id') ?: null,
            'featured' => $this->request->getPost('featured') ? 1 : 0,
        ];
    }
}
