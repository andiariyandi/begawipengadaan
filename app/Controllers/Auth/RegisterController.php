<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Services\Identity\RegistrationService;
use Config\ProfileOptions;
use App\Models\WilayahModel;

class RegisterController extends BaseController
{
    public function index()
    {
        return view('authentication/signup', [
            'options' => new ProfileOptions(),
            'provinces' => (new WilayahModel())->provinces(),
        ]);
    }

    public function attempt()
    {
        $data = $this->request->getPost();

        $rules = [
            'username' => 'required|min_length[3]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'full_name' => 'required',
            'phone' => 'required',
            'profile_type' => 'required',
            'province_code' => 'required',
            'regency_code' => 'required',
            'district_code' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            (new RegistrationService())->register([
                'name' => trim($data['full_name']),
                'username' => trim($data['username']),
                'email' => trim($data['email']),
                'phone' => trim($data['phone']),
                'password' => $data['password'],
                'profile_type' => $data['profile_type'],
                'address_line' => $data['address_line'] ?? '',
                'province_code' => $data['province_code'],
                'regency_code' => $data['regency_code'],
                'district_code' => $data['district_code'],
                'village_code' => $data['village_code'] ?? null,
                'profile' => $data['profile'] ?? [],
            ]);

            return redirect()->to(route_to('signin'))
                ->with('message', 'Registrasi berhasil dan menunggu verifikasi.');
        } catch (\Throwable $e) {
            log_message('error', $e->getMessage());
            return redirect()->back()->withInput()
                ->with('error', 'Registrasi gagal diproses.');
        }
    }
}
