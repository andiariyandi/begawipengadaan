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
        return $this->render('Notifikasi', 'Pusat pembaruan pembelajaran, tugas, konsultasi, penilaian, dan sistem.');
    }

    public function detail(string $id)
    {
        $userId  = $this->userId();
        $notifId = (int) $id;
        $roles   = (array) session()->get('roles');
        $visibleChannels = $this->service->getVisibleChannelsForUser($userId, $roles);

        $builder = db_connect()->table('notifications')->where('id', $notifId);
        if (empty($visibleChannels)) {
            $builder->where('user_id', $userId);
        } else {
            $builder->groupStart()
                ->where('user_id', $userId);
            foreach ($visibleChannels as $chan) {
                if (str_ends_with($chan, '%')) {
                    $builder->orLike('channel', substr($chan, 0, -1), 'after');
                } else {
                    $builder->orWhere('channel', $chan);
                }
            }
            $builder->groupEnd();
        }
        $notif = $builder->get()->getRowArray();

        if (! $notif) {
            return redirect()->to(site_url('workspace/notifikasi'))
                ->with('error', 'Notifikasi tidak ditemukan atau tidak dapat diakses.');
        }

        $this->service->markAsRead($notifId, $userId);

        $data = !empty($notif['data']) ? (is_array($notif['data']) ? $notif['data'] : json_decode($notif['data'], true)) : [];
        if (!empty($data['url'])) {
            return redirect()->to($data['url']);
        }

        return $this->render('Detail Notifikasi', 'Informasi rinci terkait notifikasi #' . esc($id) . '.');
    }

    public function markAsRead(string $id)
    {
        $userId  = $this->userId();
        $notifId = (int) $id;
        $roles   = (array) session()->get('roles');
        $visibleChannels = $this->service->getVisibleChannelsForUser($userId, $roles);

        $builder = db_connect()->table('notifications')->where('id', $notifId);
        if (empty($visibleChannels)) {
            $builder->where('user_id', $userId);
        } else {
            $builder->groupStart()
                ->where('user_id', $userId);
            foreach ($visibleChannels as $chan) {
                if (str_ends_with($chan, '%')) {
                    $builder->orLike('channel', substr($chan, 0, -1), 'after');
                } else {
                    $builder->orWhere('channel', $chan);
                }
            }
            $builder->groupEnd();
        }
        $notif = $builder->get()->getRowArray();

        if (! $notif) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Notifikasi tidak ditemukan.']);
            }
            return redirect()->back()->with('error', 'Notifikasi tidak ditemukan atau tidak dapat diakses.');
        }

        $this->service->markAsRead($notifId, $userId);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'message' => 'Notifikasi telah ditandai dibaca.']);
        }

        return redirect()->back()->with('message', 'Notifikasi telah ditandai dibaca.');
    }

    public function markAllAsRead()
    {
        $userId = $this->userId();
        $roles  = (array) session()->get('roles');
        if ($userId > 0) {
            $this->service->markAllAsRead($userId, $roles);
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'message' => 'Semua notifikasi telah ditandai dibaca.']);
        }

        return redirect()->back()->with('message', 'Semua notifikasi telah ditandai dibaca.');
    }

    public function feed()
    {
        $userId = $this->userId();
        if ($userId <= 0) {
            return $this->response->setJSON(['success' => false, 'unread' => 0, 'items' => []]);
        }

        $roles  = (array) session()->get('roles');
        $items  = $this->service->getUserNotifications($userId, 5, true, $roles);
        $unread = $this->service->unreadCount($userId, $roles);

        $formattedItems = [];
        foreach ($items as $item) {
            $catInfo   = NotificationService::formatCategory($item['type'] ?? 'system');
            $timeAgo   = NotificationService::timeAgo($item['created_at'] ?? null);
            $itemData  = !empty($item['data']) ? (is_array($item['data']) ? $item['data'] : json_decode($item['data'], true)) : [];
            $targetUrl = !empty($itemData['url']) ? $itemData['url'] : route_to('workspace.notifications');

            $formattedItems[] = [
                'id'         => (int) $item['id'],
                'title'      => (string) ($item['title'] ?? '-'),
                'body'       => (string) ($item['body'] ?? ''),
                'type'       => (string) ($item['type'] ?? 'system'),
                'channel'    => (string) ($item['channel'] ?? 'database'),
                'is_read'    => !empty($item['read_at']),
                'read_at'    => $item['read_at'] ?? null,
                'created_at' => $item['created_at'] ?? null,
                'time_ago'   => $timeAgo,
                'badge'      => $catInfo['badge'] ?? 'bg-primary-subtle text-primary',
                'fa'         => $catInfo['fa'] ?? 'fas fa-bell text-primary',
                'icon'       => $catInfo['icon'] ?? 'solar:bell-bing-bold',
                'url'        => $targetUrl,
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'unread'  => (int) $unread,
            'items'   => $formattedItems,
        ]);
    }

    private function render(string $title, string $description): string
    {
        $userName = (string) (session()->get('name') ?: session()->get('username') ?: 'Pengguna');
        $userId   = $this->userId();
        $roles    = (array) session()->get('roles');

        $primaryRole = in_array('operator', $roles, true) ? 'operator'
            : (in_array('trainer', $roles, true) ? 'trainer' : 'participant');

        $category = trim((string) $this->request->getGet('category'));
        $status   = trim((string) $this->request->getGet('status'));

        $notifications = [];
        $unread = 0;
        $totalUserNotifs = 0;

        if ($userId > 0) {
            $filters = [];
            if ($category !== '') {
                $filters['category'] = $category;
            }
            if ($status !== '') {
                $filters['status'] = $status;
            }

            $notifications   = $this->service->getUserNotificationsFiltered($userId, $filters, 50, 0, $roles);
            $unread          = $this->service->unreadCount($userId, $roles);

            $countBuilder = db_connect()->table('notifications');
            $this->service->applyUserScope($countBuilder, $userId, $roles);
            $totalUserNotifs = (int) $countBuilder->countAllResults();
        }

        $categoryCounts = [
            'all'          => $totalUserNotifs,
            'unread'       => $unread,
            'learning'     => 0,
            'consultation' => 0,
            'certificate'  => 0,
            'system'       => 0,
        ];

        if ($userId > 0) {
            $db = db_connect();

            $bLearn = $db->table('notifications');
            $this->service->applyUserScope($bLearn, $userId, $roles);
            $categoryCounts['learning'] = (int) $bLearn
                ->whereIn('notifications.type', ['class_enrolled', 'class_reminder', 'lesson_completed', 'assignment_graded', 'quiz_graded', 'class_enrollment_requested', 'assignment_submitted', 'class_material_added'])
                ->countAllResults();

            $bConsult = $db->table('notifications');
            $this->service->applyUserScope($bConsult, $userId, $roles);
            $categoryCounts['consultation'] = (int) $bConsult
                ->whereIn('notifications.type', ['ticket_reply', 'ticket_created', 'ticket_closed', 'contact_inquiry_received'])
                ->countAllResults();

            $bCert = $db->table('notifications');
            $this->service->applyUserScope($bCert, $userId, $roles);
            $categoryCounts['certificate'] = (int) $bCert
                ->whereIn('notifications.type', ['certificate_issued', 'certificate_eligible', 'certificate_revoked'])
                ->countAllResults();

            $bSys = $db->table('notifications');
            $this->service->applyUserScope($bSys, $userId, $roles);
            $categoryCounts['system'] = (int) $bSys
                ->whereIn('notifications.type', ['broadcast', 'system', 'system_announcement', 'account_approved', 'account_rejected', 'account_registered', 'profile_change_request', 'profile_change_approved', 'profile_change_rejected', 'profile_change_revision', 'trainer_verification_request', 'role_upgrade_request'])
                ->countAllResults();
        }

        $sectionContent = [
            'eyebrow'     => 'Personal',
            'title'       => $title,
            'description' => $description,
            'cards'       => [
                ['title' => 'Belum Dibaca', 'value' => sprintf('%02d', $unread), 'meta' => 'notifikasi baru'],
                ['title' => 'Total Notifikasi', 'value' => sprintf('%02d', $totalUserNotifs), 'meta' => 'riwayat sistem'],
            ],
            'unread'        => $unread,
            'notifications' => $notifications,
        ];

        return view('main/workspace/pages/notifikasi', [
            'title'           => $title . ' | Workspace BegawiPengadaan',
            'pageTitle'       => $title,
            'userName'        => $userName,
            'roles'           => [$primaryRole],
            'primaryRole'     => $primaryRole,
            'roleLabel'       => ucfirst($primaryRole),
            'menuItems'       => $this->menuItems($primaryRole),
            'sectionContent'  => $sectionContent,
            'currentCategory' => $category,
            'currentStatus'   => $status,
            'categoryCounts'  => $categoryCounts,
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

        return match ($role) {
            'trainer' => [
                ['Kelas Saya', route_to('workspace.trainer.classes'), 'fas fa-graduation-cap'],
                ['Materi Pembelajaran', route_to('workspace.trainer.all-materials'), 'fas fa-book'],
                ['Assessment', route_to('workspace.trainer.all-quizzes'), 'fas fa-clipboard-check'],
                ['Peserta', route_to('workspace.trainer.all-participants'), 'fas fa-users'],
                ['Progress Peserta', route_to('workspace.trainer.all-progress'), 'fas fa-chart-line'],
            ],
            'operator' => [
                ['Approval', route_to('workspace.operator.approvals'), 'fas fa-stamp'],
                ['Verifikasi User', route_to('workspace.operator.profile-verifications'), 'fas fa-id-card'],
                ['Kelas & Trainer', route_to('workspace.operator.class-trainers'), 'fas fa-chalkboard-teacher'],
                ['Operasional Event', route_to('workspace.operator.events'), 'fas fa-calendar-alt'],
                ['Presensi', route_to('workspace.operator.attendance'), 'fas fa-user-check'],
                ['Monitoring', route_to('workspace.operator.learning-monitoring'), 'fas fa-chart-line'],
                ['Sertifikat', route_to('workspace.operator.certificates'), 'fas fa-award'],
                ['Evaluasi', route_to('workspace.operator.evaluations'), 'fas fa-clipboard-check'],
            ],
            default => [
                ['Pembelajaran Saya', route_to('workspace.participant.classes'), 'fas fa-graduation-cap'],
                ['Tugas', route_to('workspace.participant.all-assignments'), 'fas fa-tasks'],
                ['Assessment', route_to('workspace.participant.all-assessments'), 'fas fa-clipboard-check'],
                ['Event Saya', route_to('workspace.participant.events'), 'fas fa-calendar-alt'],
                ['Presensi Saya', route_to('workspace.participant.attendance'), 'fas fa-user-check'],
                ['Sertifikat Saya', route_to('workspace.participant.certificates'), 'fas fa-award'],
                ['Konsultasi & Tiket', route_to('workspace.consultation.index'), 'fas fa-comments'],
                ['Bookmark', route_to('workspace.bookmarks.index'), 'fas fa-bookmark'],
                ['Notifikasi', route_to('workspace.notifications'), 'fas fa-bell'],
                ['Profil', route_to('workspace.profile.index'), 'fas fa-user-circle'],
            ],
        };
    }
}
