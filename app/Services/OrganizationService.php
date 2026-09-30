<?php

namespace App\Services;

use App\Models\OrganizationUnitModel;

class OrganizationService
{
    protected $model;

    public function __construct()
    {
        $this->model = new OrganizationUnitModel();
    }

    public function getAll()
    {
        return $this->model->findAll();
    }

    public function create(array $data)
    {
        return $this->model->insert($data);
    }

    public function update($id, array $data)
    {
        return $this->model->update($id, $data);
    }

    public function delete($id)
    {
        return $this->model->delete($id);
    }
}
