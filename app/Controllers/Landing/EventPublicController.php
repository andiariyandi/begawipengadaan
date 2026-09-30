<?php

namespace App\Controllers\Landing;

use App\Controllers\BaseController;

class EventPublicController extends BaseController
{
    public function index(): string { return $this->catalog('Event dan Agenda', 'events', 'Agenda pembelajaran, forum, dan kegiatan ekosistem PBJ Lampung.'); }
    public function agenda(): string { return $this->catalog('Agenda PBJ', 'events', 'Jadwal kegiatan PBJ yang akan datang dan sedang berlangsung.'); }
    public function calendar(): string { return $this->catalog('Kalender Kegiatan', 'events', 'Kalender kegiatan pembelajaran dan kolaborasi publik.'); }
    public function detail(string $slug): string { return view('landing/pages/public_detail', ['title' => 'Detail Event | BegawiPBJ', 'pageTitle' => 'Detail Event', 'itemTitle' => $this->label($slug), 'description' => 'Informasi waktu, lokasi, narahubung, dan agenda kegiatan BegawiPBJ.', 'backUrl' => 'event']); }
    private function catalog(string $pageTitle, string $type, string $description): string { return view('landing/pages/public_catalog', ['title' => $pageTitle . ' | BegawiPBJ', 'pageTitle' => $pageTitle, 'catalogType' => $type, 'description' => $description]); }
    private function label(string $slug): string { return ucwords(str_replace(['-', '_'], ' ', $slug)); }
}
