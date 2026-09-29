<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Services\ApprovalService;
use RuntimeException;

class UserController extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();
        $keyword   = trim((string) $this->request->getGet('q'));
        $status    = trim((string) $this->request->getGet('status'));
        $userType  = trim((string) $this->request->getGet('user_type'));

        if ($keyword !== '') {
            $userModel->groupStart()
                ->like('username', $keyword)
                ->orLike('email', $keyword)
                ->orLike('instansi', $keyword)
                ->groupEnd();
        }

        if (in_array($status, ['active', 'pending', 'inactive', 'rejected'], true)) {
            $userModel->where('status', $status);
        } else {
            $status = '';
        }

        if (in_array($userType, ['asn', 'provider', 'academic_practitioner', 'public'], true)) {
            $userModel->where('user_type', $userType);
        } else {
            $userType = '';
        }

        return view('users/usersList', [
            'title'    => 'Users List',
            'subTitle' => 'Users List',
            'users'    => $userModel->orderBy('created_at', 'DESC')->findAll(),
            'keyword'  => $keyword,
            'status'   => $status,
            'userType' => $userType,
        ]);
    }

    public function verify(int $userId)
    {
        $decision  = $this->request->getPost('decision');
        $userModel = new UserModel();
        $actedBy   = session()->get('user_id');

        if (! in_array($decision, ['approve', 'reject'], true)) {
            return redirect()->back()->with('error', 'Keputusan tidak valid.');
        }

        $service = new ApprovalService();

        try {
            if ($decision === 'approve') {
                $service->approve('user_registration', $userId, $actedBy, null, function (int $id) use ($userModel) {
                    $userModel->update($id, ['status' => 'active']);
                });
                $message = 'Registrasi user berhasil disetujui.';
            } else {
                $service->reject('user_registration', $userId, $actedBy, null, function (int $id) use ($userModel) {
                    $userModel->update($id, ['status' => 'rejected']);
                });
                $message = 'Registrasi user ditolak.';
            }
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->to(route_to('usersList'))->with('message', $message);
    }

    public function updateStatus(int $userId)
    {
        $status = $this->request->getPost('status');

        if (! in_array($status, ['active', 'inactive'], true)) {
            return redirect()->back()->with('error', 'Status user tidak valid.');
        }

        if ($userId === (int) session()->get('user_id')) {
            return redirect()->back()->with('error', 'Status akun sendiri tidak dapat diubah dari halaman ini.');
        }

        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (! $user) {
            return redirect()->back()->with('error', 'User tidak ditemukan.');
        }

        $userModel->update($userId, ['status' => $status]);
        (new \App\Services\AuditService())->log(
            session()->get('user_id'),
            'update',
            'user',
            'users',
            $userId,
            ['status' => $status]
        );

        return redirect()->back()->with('message', 'Status user berhasil diperbarui.');
    }
}
