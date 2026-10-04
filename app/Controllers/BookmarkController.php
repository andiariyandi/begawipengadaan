<?php

namespace App\Controllers;

use App\Models\Knowledge\BookmarkModel;

class BookmarkController extends BaseController
{
    /**
     * POST /bookmark/toggle
     * Field form: type (bookmarkable_type), id (bookmarkable_id).
     * Guest: redirect ke login dengan intended URL.
     * Login: toggle insert/delete, redirect back dengan flash.
     */
    public function toggle()
    {
        $type = (string) $this->request->getPost('type');
        $id   = (int) $this->request->getPost('id');
        $redirect = $this->safeRedirectBack();

        if (! session()->get('isLoggedIn') === true) {
            return redirect()->to(route_to('dashboard.auth.login'))
                ->with('error', 'Silakan masuk untuk menyimpan bookmark.');
        }
        $userId = (int) session()->get('user_id');
        if ($userId < 1) {
            return redirect()->to(route_to('dashboard.auth.login'))
                ->with('error', 'Sesi tidak valid, silakan masuk ulang.');
        }
        if ($id < 1 || ! array_key_exists($type, BookmarkModel::SUPPORTED_TYPES)) {
            return $redirect->with('error', 'Tipe bookmark tidak dikenali.');
        }

        try {
            $state = (new BookmarkModel())->toggle($userId, $type, $id);
        } catch (\Throwable $e) {
            return $redirect->with('error', 'Gagal menyimpan bookmark: ' . $e->getMessage());
        }
        $label = BookmarkModel::SUPPORTED_TYPES[$type];
        $msg = $state === 'added'
            ? "Ditambahkan ke bookmark: {$label}."
            : "Dihapus dari bookmark: {$label}.";
        return $redirect->with($state === 'added' ? 'success' : 'info', $msg);
    }

    /**
     * Pastikan URL redirect aman (open redirect guard).
     */
    private function safeRedirectBack()
    {
        $fallback = site_url('/');
        $ref = $this->request->getHeaderLine('Referer');
        if ($ref !== '' && str_starts_with($ref, base_url())) {
            return redirect()->to($ref);
        }
        return redirect()->to($fallback);
    }
}