<?php

namespace App\Controllers\Admin\Survey;

use App\Controllers\BaseController;
use App\Services\AuditService;

class SatisfactionSurveyAdminController extends BaseController
{
    private $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        // 1. All responses
        $responses = $this->db->table('satisfaction_surveys')
            ->orderBy('created_at', 'DESC')
            ->get()->getResultArray();

        $totalResponses = count($responses);

        // 2. Fetch survey aspects & dynamic weights
        $aspects = $this->db->table('survey_aspects')
            ->orderBy('sort_order', 'ASC')
            ->get()->getResultArray();

        $wMap = [];
        foreach ($aspects as $asp) {
            $wMap[$asp['aspect_key']] = (float) ($asp['weight'] ?? 25.0);
        }
        $wPersyaratan = $wMap['kesesuaian_persyaratan'] ?? 25.0;
        $wSistem      = $wMap['kemudahan_sistem'] ?? 25.0;
        $wLayanan     = $wMap['kecepatan_layanan'] ?? 25.0;
        $wMateri      = $wMap['kualitas_materi'] ?? 25.0;
        $totalWeight  = ($wPersyaratan + $wSistem + $wLayanan + $wMateri) ?: 100.0;

        // 3. Average calculations
        $avgTotal = 0;
        $avgPersyaratan = 0;
        $avgSistem = 0;
        $avgLayanan = 0;
        $avgMateri = 0;

        if ($totalResponses > 0) {
            $sumPersyaratan = array_sum(array_map(static fn($r) => (int) ($r['service_regulation_rating'] ?? $r['rating_overall'] ?? 5), $responses));
            $sumSistem      = array_sum(array_map(static fn($r) => (int) ($r['service_access_rating'] ?? $r['rating_overall'] ?? 5), $responses));
            $sumLayanan     = array_sum(array_map(static fn($r) => (int) ($r['service_helpdesk_rating'] ?? $r['rating_overall'] ?? 5), $responses));
            $sumMateri      = array_sum(array_map(static fn($r) => (int) ($r['service_learning_rating'] ?? $r['rating_overall'] ?? 5), $responses));

            $avgPersyaratan = round($sumPersyaratan / $totalResponses, 2);
            $avgSistem      = round($sumSistem / $totalResponses, 2);
            $avgLayanan     = round($sumLayanan / $totalResponses, 2);
            $avgMateri      = round($sumMateri / $totalResponses, 2);

            // Pembobotan dinamis dari konfigurasi tabel survey_aspects
            $weightedSum = ($avgPersyaratan * $wPersyaratan)
                + ($avgSistem * $wSistem)
                + ($avgLayanan * $wLayanan)
                + ($avgMateri * $wMateri);
            $avgTotal = round($weightedSum / $totalWeight, 2);
        }

        // 4. Standarisasi IKM PermenPAN-RB No. 14 Tahun 2017
        $ikmScale4 = $totalResponses > 0 ? (1 + (($avgTotal - 1) / 4) * 3) : 0;
        $ikmScore  = $totalResponses > 0 ? round($ikmScale4 * 25, 2) : 0;

        if ($ikmScore >= 88.31) {
            $mutuKategori     = 'A';
            $kinerjaPelayanan = 'Sangat Baik';
            $mutuBadgeClass   = 'bg-success-50 text-success-600 border border-success-200';
        } elseif ($ikmScore >= 76.61) {
            $mutuKategori     = 'B';
            $kinerjaPelayanan = 'Baik';
            $mutuBadgeClass   = 'bg-primary-50 text-primary-600 border border-primary-200';
        } elseif ($ikmScore >= 65.00) {
            $mutuKategori     = 'C';
            $kinerjaPelayanan = 'Kurang Baik';
            $mutuBadgeClass   = 'bg-warning-50 text-warning-600 border border-warning-200';
        } else {
            $mutuKategori     = 'D';
            $kinerjaPelayanan = 'Tidak Baik';
            $mutuBadgeClass   = 'bg-danger-50 text-danger-600 border border-danger-200';
        }

        $wf1 = $wPersyaratan / $totalWeight;
        $wf2 = $wSistem / $totalWeight;
        $wf3 = $wLayanan / $totalWeight;
        $wf4 = $wMateri / $totalWeight;

        $panrbElements = [
            'u1' => [
                'code'           => 'U1',
                'name'           => 'Kesesuaian Persyaratan & Regulasi PBJ',
                'weight_pct'     => $wPersyaratan,
                'nrr'            => $avgPersyaratan,
                'nrr_4'          => $totalResponses > 0 ? round(1 + (($avgPersyaratan - 1) / 4) * 3, 2) : 0,
                'nrr_tertimbang' => $totalResponses > 0 ? round((1 + (($avgPersyaratan - 1) / 4) * 3) * $wf1, 3) : 0,
            ],
            'u2' => [
                'code'           => 'U2',
                'name'           => 'Kemudahan Akses & Keandalan Sistem Portal',
                'weight_pct'     => $wSistem,
                'nrr'            => $avgSistem,
                'nrr_4'          => $totalResponses > 0 ? round(1 + (($avgSistem - 1) / 4) * 3, 2) : 0,
                'nrr_tertimbang' => $totalResponses > 0 ? round((1 + (($avgSistem - 1) / 4) * 3) * $wf2, 3) : 0,
            ],
            'u3' => [
                'code'           => 'U3',
                'name'           => 'Kecepatan Respon Helpdesk & Konsultasi',
                'weight_pct'     => $wLayanan,
                'nrr'            => $avgLayanan,
                'nrr_4'          => $totalResponses > 0 ? round(1 + (($avgLayanan - 1) / 4) * 3, 2) : 0,
                'nrr_tertimbang' => $totalResponses > 0 ? round((1 + (($avgLayanan - 1) / 4) * 3) * $wf3, 3) : 0,
            ],
            'u4' => [
                'code'           => 'U4',
                'name'           => 'Kualitas Materi, Pembelajaran & Transparansi',
                'weight_pct'     => $wMateri,
                'nrr'            => $avgMateri,
                'nrr_4'          => $totalResponses > 0 ? round(1 + (($avgMateri - 1) / 4) * 3, 2) : 0,
                'nrr_tertimbang' => $totalResponses > 0 ? round((1 + (($avgMateri - 1) / 4) * 3) * $wf4, 3) : 0,
            ],
        ];

        // 5. Role distribution
        $rolesCount = [];
        foreach ($responses as $r) {
            $role = $r['respondent_role'] ?: 'Lainnya';
            $rolesCount[$role] = ($rolesCount[$role] ?? 0) + 1;
        }

        return view('dashboard/survey/index', [
            'title'            => 'Survei Kepuasan (IKM) | Dashboard BegawiPengadaan',
            'pageTitle'        => 'Survei Kepuasan Masyarakat (IKM)',
            'activeMenu'       => 'survey',
            'activeSubmenu'    => 'index',
            'responses'        => $responses,
            'totalResponses'   => $totalResponses,
            'avgTotal'         => $avgTotal,
            'avgPersyaratan'   => $avgPersyaratan,
            'avgSistem'        => $avgSistem,
            'avgLayanan'       => $avgLayanan,
            'avgMateri'        => $avgMateri,
            'ikmScore'         => $ikmScore,
            'ikmScale4'        => $ikmScale4,
            'mutuKategori'     => $mutuKategori,
            'kinerjaPelayanan' => $kinerjaPelayanan,
            'mutuBadgeClass'   => $mutuBadgeClass,
            'panrbElements'    => $panrbElements,
            'aspects'          => $aspects,
            'rolesCount'       => $rolesCount,
        ]);
    }

    public function exportCsv()
    {
        $responses = $this->db->table('satisfaction_surveys')
            ->orderBy('created_at', 'DESC')
            ->get()->getResultArray();

        $filename = 'laporan_ikm_begawipbj_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        // Add UTF-8 BOM for Microsoft Excel compatibility
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, [
            'ID',
            'No. Registrasi Tiket',
            'Rating Keseluruhan (1-5)',
            'Nilai Akses Portal (1-5)',
            'Nilai Regulasi Dokumen (1-5)',
            'Nilai Pembelajaran (1-5)',
            'Nilai Helpdesk (1-5)',
            'Peran Responden',
            'Status Anonim',
            'Nama Responden',
            'Instansi / Perusahaan',
            'Kontak Responden',
            'Kritik & Masukan Saran',
            'Rekomendasi',
            'Alamat IP',
            'Perangkat / User Agent',
            'Waktu Pengisian',
        ]);

        foreach ($responses as $row) {
            fputcsv($output, [
                $row['id'],
                $row['ticket_number'] ?? '',
                $row['rating_overall'] ?? 5,
                $row['service_access_rating'] ?? $row['rating_overall'] ?? 5,
                $row['service_regulation_rating'] ?? $row['rating_overall'] ?? 5,
                $row['service_learning_rating'] ?? $row['rating_overall'] ?? 5,
                $row['service_helpdesk_rating'] ?? $row['rating_overall'] ?? 5,
                $row['respondent_role'] ?? '',
                ($row['is_anonymous'] ?? 1) ? 'Ya (Anonim)' : 'Tidak',
                ($row['is_anonymous'] ?? 1) ? 'Anonim' : ($row['respondent_name'] ?? ''),
                ($row['is_anonymous'] ?? 1) ? '-' : ($row['respondent_institution'] ?? ''),
                ($row['is_anonymous'] ?? 1) ? '-' : ($row['respondent_contact'] ?? ''),
                $row['feedback'] ?? '',
                $row['recommendation'] ?? '',
                $row['ip_address'] ?? '',
                $row['user_agent'] ?? '',
                $row['created_at'] ?? '',
            ]);
        }

        fclose($output);

        // Audit Trail
        try {
            $actorId = (int) (session()->get('user_id') ?: 0);
            (new AuditService())->log(
                $actorId ?: null,
                'survey.exported_csv',
                'survey',
                'satisfaction_surveys',
                0,
                ['total_exported' => count($responses), 'filename' => $filename]
            );
        } catch (\Throwable) {
        }

        exit;
    }

    public function updateAspect($id)
    {
        $aspect = $this->db->table('survey_aspects')->where('id', $id)->get()->getRowArray();
        if (! $aspect) {
            return redirect()->to('dashboard/survei-kepuasan')->with('error', 'Dimensi aspek tidak ditemukan.');
        }

        $data = [
            'title'       => trim((string) $this->request->getPost('title')),
            'description' => trim((string) $this->request->getPost('description')),
            'weight'      => (float) ($this->request->getPost('weight') ?: 25.00),
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        $this->db->table('survey_aspects')->where('id', $id)->update($data);

        // Audit Trail
        try {
            $actorId = (int) (session()->get('user_id') ?: 0);
            (new AuditService())->log(
                $actorId ?: null,
                'survey.aspect_updated',
                'survey',
                'survey_aspects',
                (int) $id,
                ['aspect_key' => $aspect['aspect_key'], 'title' => $data['title'], 'weight' => $data['weight']]
            );
        } catch (\Throwable) {
        }

        return redirect()->to('dashboard/survei-kepuasan')->with('success', 'Dimensi aspek survei berhasil diperbarui.');
    }

    public function deleteResponse($id)
    {
        $survey = $this->db->table('satisfaction_surveys')->where('id', $id)->get()->getRowArray();
        if (! $survey) {
            return redirect()->to('dashboard/survei-kepuasan')->with('error', 'Data responden tidak ditemukan.');
        }

        $this->db->table('satisfaction_surveys')->where('id', $id)->delete();

        // Audit Trail
        try {
            $actorId = (int) (session()->get('user_id') ?: 0);
            (new AuditService())->log(
                $actorId ?: null,
                'survey.response_deleted',
                'survey',
                'satisfaction_surveys',
                (int) $id,
                ['ticket_number' => $survey['ticket_number'] ?? '', 'respondent' => $survey['respondent_name'] ?? 'Anonim']
            );
        } catch (\Throwable) {
        }

        return redirect()->to('dashboard/survei-kepuasan')->with('success', 'Data responden berhasil dihapus.');
    }
}
