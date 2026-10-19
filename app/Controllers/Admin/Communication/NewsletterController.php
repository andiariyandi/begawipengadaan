<?php

namespace App\Controllers\Admin\Communication;

use App\Controllers\BaseController;

class NewsletterController extends BaseController
{
    private $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $subscribers = $this->db->table('newsletter_subscribers')
            ->orderBy('created_at', 'DESC')
            ->get()->getResultArray();

        return view('dashboard/communication/newsletter/index', [
            'title'         => 'Pelanggan Newsletter | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Daftar Pelanggan Newsletter',
            'activeMenu'    => 'communication',
            'activeSubmenu' => 'newsletter',
            'subscribers'   => $subscribers,
        ]);
    }

    public function delete($id)
    {
        $this->db->table('newsletter_subscribers')->where('id', $id)->delete();
        return redirect()->to('dashboard/komunikasi/newsletter')->with('success', 'Pelanggan berhasil dihapus.');
    }

    public function exportCsv()
    {
        $subscribers = $this->db->table('newsletter_subscribers')
            ->orderBy('created_at', 'DESC')
            ->get()->getResultArray();

        $filename = 'subscribers_begawipbj_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Email', 'Status', 'Tanggal Berlangganan']);

        foreach ($subscribers as $row) {
            fputcsv($output, [$row['id'], $row['email'], $row['status'], $row['created_at']]);
        }

        fclose($output);
        exit;
    }
}
