<?php

namespace App\Controllers\Organization;

use App\Controllers\BaseController;
use App\Services\OrganizationService;

class OrganizationController extends BaseController
{
    protected $service;

    public function __construct()
    {
        $this->service = new OrganizationService();
    }

    public function index()
    {
        return view('dashboard/organization/index', [
            'organizations' => $this->service->getAll()
        ]);
    }

    public function store()
    {
        $this->service->create($this->request->getPost());
        return redirect()->back();
    }
}
