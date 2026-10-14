<?php

namespace App\Controllers\Workspace;

use App\Controllers\BaseController;
use App\Services\NotificationService;

class NotificationController extends BaseController
{
    private NotificationService $service;

    public function __construct()
    {
        $this->service = new NotificationService();
    }

    public function index()
    {
        return $this->render('Notifikasi', 'Pusat pembaruan pembelajaran, tugas, penilaian, dan sistem.');
    }

    public function detail(string $id)
    {
        $userId = $this->userId();
        if ($userId > 0) {
            $this->service->markAsRead((int) $id, $userId);
        }

        return $this->render('Detail Notifikasi', 'Informasi rinci terkait notifikasi #' . esc($id) . '.');
    }

    public function markAsRead(string $id)
    {
        $userId = $this->userId();
        if ($userId > 0) {
            $this->service->markAsRead((int) $id, $userId);
        }

        return redirect()->back()->with('message', 'Notifikasi telah ditandai dibaca.');
    }

    public function markAllAsRead()
    {
        $userId = $this->userId();
        if ($userId > 0) {
            $this->service->markAllAsRead($userId);
        }

        return redirect()->back()->with('message', 'Semua notifikasi telah ditandai dibaca.');
    }

    private function render(string $title, string $description): string
    {
        $userName = (string) (session()->get('name') ?: session()->get('username') ?: 'Pengguna');
        $userId   = $this->userId();
        $roles    = (array) session()->get('roles');

        $primaryRole = in_array('operator', $roles, true) ? 'operator'
            : (in_array('trainer', $roles, true) ? 'trainer' : 'participant');

        $notifications = [];
        $unread = 0;
        if ($userId > 0) {
            $notifications = $this->service->getUserNotifications($userId, 30);
            $unread        = $this->service->unreadCount($userId);
        }

        $sectionContent = [
            'eyebrow'     => 'Personal',
            'title'       => $title,
            'description' => $description,
            'cards'       => [
                ['title' => 'Belum Dibaca', 'value' => sprintf('%02d', $unread), 'meta' => 'notifikasi baru'],
                ['title' => 'Total Notifikasi', 'value' => sprintf('%02d', count($notifications)), 'meta' => 'riwayat sistem'],
            ],
            'unread'        => $unread,
            'notifications' => $notifications,
        ];

        return view('main/workspace/pages/notifikasi', [
            'title'          => $title . ' | Workspace BegawiPengadaan',
            'pageTitle'      => $title,
            'userName'       => $userName,
            'roles'          => [$primaryRole],
            'primaryRole'    => $primaryRole,
            'roleLabel'      => ucfirst($primaryRole),
            'menuItems'      => $this->menuItems($primaryRole),
            'sectionContent' => $sectionContent,
        ]);
    }

    private function userId(): int
    {
        return (int) session()->get('user_id');
    }

    private function menuItems(string $role): array
    {
        $workspace = route_to('workspace.home');
        $section = static fn(string $name): string => route_to('workspace.section', $name);

        return [
            ['label' => 'Dashboard', 'items' => [
                ['label' => 'Ringkasan', 'url' => $workspace],
                ['label' => 'Aktivitas Terakhir', 'url' => $section('aktivitas')],
            ]],
            ['label' => 'Personal', 'items' => [
                ['label' => 'Notifikasi', 'url' => route_to('workspace.notifications')],
                ['label' => 'Bookmark', 'url' => route_to('workspace.bookmarks.index')],
                ['label' => 'Profil', 'url' => route_to('workspace.profile.index')],
            ]],
        ];
    }
}
