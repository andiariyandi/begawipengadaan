<?php

namespace App\Controllers\Admin\Survey;

use App\Controllers\BaseController;

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

        // 2. Average calculations
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

            $avgTotal = round(($avgPersyaratan + $avgSistem + $avgLayanan + $avgMateri) / 4, 2);
        }

        // 3. Survey aspects
        $aspects = $this->db->table('survey_aspects')
            ->orderBy('sort_order', 'ASC')
            ->get()->getResultArray();

        // 4. Role distribution
        $rolesCount = [];
        foreach ($responses as $r) {
            $role = $r['respondent_role'] ?: 'Lainnya';
            $rolesCount[$role] = ($rolesCount[$role] ?? 0) + 1;
        }

        return view('dashboard/survey/index', [
            'title'          => 'Survei Kepuasan (IKM) | Dashboard BegawiPengadaan',
            'pageTitle'      => 'Survei Kepuasan Masyarakat (IKM)',
            'activeMenu'     => 'survey',
            'activeSubmenu'  => 'ikm',
            'responses'      => $responses,
            'totalResponses' => $totalResponses,
            'avgTotal'       => $avgTotal,
            'avgPersyaratan' => $avgPersyaratan,
            'avgSistem'      => $avgSistem,
            'avgLayanan'     => $avgLayanan,
            'avgMateri'      => $avgMateri,
            'aspects'        => $aspects,
            'rolesCount'     => $rolesCount,
        ]);
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

        return redirect()->to('dashboard/survei-kepuasan')->with('success', 'Dimensi aspek survei berhasil diperbarui.');
    }

    public function deleteResponse($id)
    {
        $this->db->table('satisfaction_surveys')->where('id', $id)->delete();
        return redirect()->to('dashboard/survei-kepuasan')->with('success', 'Data responden berhasil dihapus.');
    }
}
