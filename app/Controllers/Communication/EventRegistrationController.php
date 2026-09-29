<?php

namespace App\Controllers\Communication;

use App\Controllers\BaseController;

class EventRegistrationController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/communication/registrations/index', [
            'title' => 'Registrasi Event | Dashboard BegawiPBJ',
            'pageTitle' => 'Registrasi Event',
            'activeMenu' => 'communication',
            'activeSubmenu' => 'event-registrations',
        ]);
    }

    public function detail(string $id): string
    {
        return view('dashboard/communication/registrations/detail', [
            'title' => 'Detail Registrasi Event | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Registrasi Event',
            'registrationId' => $id,
            'activeMenu' => 'communication',
            'activeSubmenu' => 'event-registrations',
        ]);
    }

    public function waitlistAndCancellation(): string
    {
        return view('dashboard/communication/waitlist_cancellations/index', [
            'title' => 'Waitlist dan Pembatalan | Dashboard BegawiPBJ',
            'pageTitle' => 'Waitlist dan Pembatalan',
            'activeMenu' => 'communication',
            'activeSubmenu' => 'waitlist-cancellations',
        ]);
    }
}
