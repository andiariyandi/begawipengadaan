<?php

namespace App\Controllers\Reference;

use App\Controllers\BaseController;
use App\Models\WilayahModel;
use CodeIgniter\HTTP\ResponseInterface;

class WilayahController extends BaseController
{
    public function children(string $code): ResponseInterface
    {
        if (! preg_match('/^\d{2}(?:\.\d{2}){0,2}$/', $code)) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Kode wilayah tidak valid.']);
        }

        return $this->response->setJSON((new WilayahModel())->children($code));
    }
}
