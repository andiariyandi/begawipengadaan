<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Services\DashboardService;
use App\Libraries\PdfGenerator;

class DashboardController extends BaseController
{
    public function index()
    {
        $from = $this->request->getGet('from');
        $to   = $this->request->getGet('to');
        $service = new DashboardService();

        return view('dashboard/home/index', [
            'title'       => 'Dashboard PBJ',
            'subTitle'    => 'Dashboard PBJ',
            'from'        => $from,
            'to'          => $to,
            'userStats'   => $service->userStats(),
            'training'    => $service->trainingStats($from, $to),
            'certificate' => $service->certificateStats(),
            'regulation'  => $service->regulationStats(),
        ]);
    }

    public function internalKpi(): string
    {
        return view('dashboard/home/summary', [
            'title' => 'KPI Internal | BegawiPBJ',
            'pageTitle' => 'KPI Internal',
        ]);
    }

    public function approvalQueue(): string
    {
        return view('dashboard/home/summary', [
            'title' => 'Antrean Persetujuan | BegawiPBJ',
            'pageTitle' => 'Antrean Persetujuan',
        ]);
    }

    public function recentActivity(): string
    {
        return view('dashboard/home/summary', [
            'title' => 'Aktivitas Terbaru | BegawiPBJ',
            'pageTitle' => 'Aktivitas Terbaru',
        ]);
    }

    public function systemIndicators(): string
    {
        return view('dashboard/home/summary', [
            'title' => 'Indikator Sistem | BegawiPBJ',
            'pageTitle' => 'Indikator Sistem',
        ]);
    }

    public function exportPdf()
    {
        $service = new DashboardService();
        $from    = $this->request->getGet('from');
        $to      = $this->request->getGet('to');

        $html = view('dashboard_report/pdf_report', [
            'userStats'   => $service->userStats(),
            'training'    => $service->trainingStats($from, $to),
            'certificate' => $service->certificateStats(),
            'regulation'  => $service->regulationStats(),
            'generatedAt' => date('d F Y H:i'),
        ]);

        $fileName = 'laporan-pbj-' . date('Y-m-d') . '.pdf';
        $fullPath = WRITEPATH . 'uploads/' . $fileName;

        (new PdfGenerator())->generateToFile($html, $fullPath, 'portrait');

        return $this->response->download($fullPath, null)->setFileName($fileName);
    }

    public function exportExcel()
    {
        $service    = new DashboardService();
        $from       = $this->request->getGet('from');
        $to         = $this->request->getGet('to');
        $training   = $service->trainingStats($from, $to);
        $userStats  = $service->userStats();
        $cert       = $service->certificateStats();
        $regulation = $service->regulationStats();

        $fileName = 'laporan-pbj-' . date('Y-m-d') . '.xls';

        $this->response->setHeader('Content-Type', 'application/vnd.ms-excel; charset=UTF-8');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        $rows = [
            ['Kategori', 'Metrik', 'Nilai'],
            ['User', 'Total Pengguna', $userStats['total']],
            ['User', 'ASN', $userStats['asn']],
            ['User', 'Umum', $userStats['umum']],
            ['Pelatihan', 'Total Program', $training['total_courses']],
            ['Pelatihan', 'Total Kelas', $training['total_classes']],
            ['Pelatihan', 'Peserta Aktif', $training['active_participants']],
            ['Pelatihan', 'Tingkat Kelulusan (%)', $training['pass_rate']],
            ['Pelatihan', 'Nilai Rata-rata', $training['average_score']],
            ['Sertifikat', 'Total', $cert['total']],
            ['Sertifikat', 'Aktif', $cert['active']],
            ['Sertifikat', 'Tervalidasi', $cert['verified']],
            ['Regulasi', 'Total Dokumen', $regulation['total']],
        ];

        $table = '<table><thead><tr>';
        foreach ($rows[0] as $heading) {
            $table .= '<th>' . esc($heading) . '</th>';
        }
        $table .= '</tr></thead><tbody>';
        foreach (array_slice($rows, 1) as $row) {
            $table .= '<tr>';
            foreach ($row as $value) {
                $table .= '<td>' . esc((string) $value) . '</td>';
            }
            $table .= '</tr>';
        }
        $table .= '</tbody></table>';

        return $this->response->setBody('<html><head><meta charset="UTF-8"></head><body>' . $table . '</body></html>');
    }
}
