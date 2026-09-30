<?php

namespace App\Controllers\Communication;

use App\Controllers\BaseController;

class EventController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/communication/events/index', [
            'title' => 'Event dan Agenda | Dashboard BegawiPBJ',
            'pageTitle' => 'Event dan Agenda',
            'activeMenu' => 'communication',
            'activeSubmenu' => 'events',
        ]);
    }

    public function create(): string
    {
        return view('dashboard/communication/events/form', [
            'title' => 'Tambah Event | Dashboard BegawiPBJ',
            'pageTitle' => 'Tambah Event',
            'formMode' => 'create',
            'activeMenu' => 'communication',
            'activeSubmenu' => 'events',
        ]);
    }

    public function detail(string $id): string
    {
        return view('dashboard/communication/events/detail', [
            'title' => 'Detail Event | Dashboard BegawiPBJ',
            'pageTitle' => 'Detail Event',
            'eventId' => $id,
            'activeMenu' => 'communication',
            'activeSubmenu' => 'events',
        ]);
    }

    public function edit(string $id): string
    {
        return view('dashboard/communication/events/form', [
            'title' => 'Ubah Event | Dashboard BegawiPBJ',
            'pageTitle' => 'Ubah Event',
            'formMode' => 'edit',
            'eventId' => $id,
            'activeMenu' => 'communication',
            'activeSubmenu' => 'events',
        ]);
    }
}
