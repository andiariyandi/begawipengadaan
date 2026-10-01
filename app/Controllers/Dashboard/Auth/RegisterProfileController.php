<?php

namespace App\Controllers\Dashboard\Auth;

use App\Controllers\BaseController;

class RegisterProfileController extends BaseController
{
    public function form(string $type): string
    {
        $views = [
            'asn' => 'register_asn',
            'provider' => 'register_provider',
            'penyedia' => 'register_provider',
            'academic' => 'register_academic',
            'akademisi' => 'register_academic',
            'public' => 'register_public',
            'masyarakat' => 'register_public',
        ];

        if (! isset($views[$type])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('dashboard/auth/' . $views[$type], [
            'title' => 'Profil Pendaftaran | BegawiPBJ',
            'pageTitle' => 'Lengkapi Profil Pendaftaran',
            'currentStep' => 3,
            'currentKey' => 'profil',
            'registrationProfile' => session('registration_profile') ?? [],
        ]);
    }

    public function store(string $type)
    {
        session()->set('registration_profile', $this->request->getPost());
        return redirect()->to(route_to('dashboard.auth.register.review'));
    }
}
