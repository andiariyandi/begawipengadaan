<?php

namespace App\Controllers\Dashboard\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;

/**
 * Account status page (per CLAUDE.md §9 / route-contract.md §Auth status).
 *
 * Renders the appropriate view based on the active user's account_status.
 * Used after login attempts when account is non-active, and any time the
 * user navigates here directly. View per status:
 *   - pending_verification / submitted / draft      -> account_pending
 *   - revision_required                            -> account_revision_required
 *   - rejected / inactive / suspended / archived   -> account_rejected
 */
class AccountStatusController extends BaseController
{
    public function show()
    {
        $user = null;

        if (session()->get('isLoggedIn')) {
            $userId = (int) session()->get('user_id');
            if ($userId > 0) {
                $user = (new UserModel())->find($userId);
            }
        }

        if (! $user) {
            return redirect()->to(site_url('dashboard/masuk'))
                ->with('error', 'Silakan masuk terlebih dahulu.');
        }

        $status = $user['account_status'] ?? 'draft';

        $view = match ($status) {
            'revision_required' => 'dashboard/auth/account_revision_required',
            'rejected', 'inactive', 'suspended', 'archived' => 'dashboard/auth/account_rejected',
            default => 'dashboard/auth/account_pending',
        };

        return view($view, [
            'title'         => 'Status Akun | BegawiPBJ',
            'pageTitle'     => 'Status Akun Anda',
            'pageDescription' => 'Ringkasan status akun dan langkah selanjutnya.',
            'user'          => $user,
            'accountStatus' => $status,
        ]);
    }
}