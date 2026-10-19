<?php

namespace App\Controllers\Admin\Communication;

use App\Controllers\BaseController;
use App\Services\AuditService;
use App\Services\SystemSettingService;

class HelpdeskWidgetController extends BaseController
{
    private AuditService $auditService;
    private SystemSettingService $settingService;
    private $db;

    public function __construct()
    {
        $this->auditService = new AuditService();
        $this->settingService = new SystemSettingService();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $topics = $this->db->table('helpdesk_topics')
            ->orderBy('sort_order', 'ASC')
            ->get()->getResultArray();

        $settings = $this->settingService->getGroup('helpdesk');

        return view('dashboard/communication/helpdesk/index', [
            'title'         => 'Kelola Widget WhatsApp | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Widget WhatsApp Helpdesk',
            'activeMenu'    => 'communication',
            'activeSubmenu' => 'whatsapp-widget',
            'topics'        => $topics,
            'settings'      => $settings,
        ]);
    }

    public function saveSettings()
    {
        $data = [
            'wa_enabled'      => $this->request->getPost('wa_enabled') ? '1' : '0',
            'wa_phone'        => preg_replace('/[^0-9]/', '', (string) $this->request->getPost('wa_phone')),
            'wa_title'        => trim((string) $this->request->getPost('wa_title')),
            'wa_subtitle'     => trim((string) $this->request->getPost('wa_subtitle')),
            'wa_welcome_text' => trim((string) $this->request->getPost('wa_welcome_text')),
            'wa_teaser_text'  => trim((string) $this->request->getPost('wa_teaser_text')),
        ];

        $this->settingService->saveGroup('helpdesk', $data);

        return redirect()->to('dashboard/komunikasi/whatsapp-widget')->with('success', 'Pengaturan widget WhatsApp berhasil diperbarui.');
    }

    public function storeTopic()
    {
        $rules = [
            'topic_title'     => 'required|max_length[150]',
            'default_message' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'topic_title'     => trim((string) $this->request->getPost('topic_title')),
            'default_message' => trim((string) $this->request->getPost('default_message')),
            'icon_class'      => trim((string) $this->request->getPost('icon_class') ?: 'solar:chat-round-dots-bold'),
            'sort_order'      => (int) ($this->request->getPost('sort_order') ?: 1),
            'is_active'       => $this->request->getPost('is_active') ? 1 : 0,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ];

        $this->db->table('helpdesk_topics')->insert($data);

        return redirect()->to('dashboard/komunikasi/whatsapp-widget')->with('success', 'Topik cepat WhatsApp berhasil ditambahkan.');
    }

    public function updateTopic($id)
    {
        $topic = $this->db->table('helpdesk_topics')->where('id', $id)->get()->getRowArray();
        if (! $topic) {
            return redirect()->to('dashboard/komunikasi/whatsapp-widget')->with('error', 'Topik tidak ditemukan.');
        }

        $rules = [
            'topic_title'     => 'required|max_length[150]',
            'default_message' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'topic_title'     => trim((string) $this->request->getPost('topic_title')),
            'default_message' => trim((string) $this->request->getPost('default_message')),
            'icon_class'      => trim((string) $this->request->getPost('icon_class') ?: 'solar:chat-round-dots-bold'),
            'sort_order'      => (int) ($this->request->getPost('sort_order') ?: 1),
            'is_active'       => $this->request->getPost('is_active') ? 1 : 0,
            'updated_at'      => date('Y-m-d H:i:s'),
        ];

        $this->db->table('helpdesk_topics')->where('id', $id)->update($data);

        return redirect()->to('dashboard/komunikasi/whatsapp-widget')->with('success', 'Topik cepat WhatsApp berhasil diperbarui.');
    }

    public function deleteTopic($id)
    {
        $this->db->table('helpdesk_topics')->where('id', $id)->delete();
        return redirect()->to('dashboard/komunikasi/whatsapp-widget')->with('success', 'Topik cepat berhasil dihapus.');
    }

    public function toggleTopic($id)
    {
        $topic = $this->db->table('helpdesk_topics')->where('id', $id)->get()->getRowArray();
        if ($topic) {
            $newStatus = $topic['is_active'] ? 0 : 1;
            $this->db->table('helpdesk_topics')->where('id', $id)->update([
                'is_active'  => $newStatus,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            return redirect()->to('dashboard/komunikasi/whatsapp-widget')->with('success', 'Status topik berhasil diubah.');
        }

        return redirect()->to('dashboard/komunikasi/whatsapp-widget')->with('error', 'Topik tidak ditemukan.');
    }
}
