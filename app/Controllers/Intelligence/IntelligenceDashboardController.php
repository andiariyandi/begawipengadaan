<?php

namespace App\Controllers\Intelligence;

use App\Controllers\BaseController;
use App\Services\Admin\IntelligenceAnalyticsService;

class IntelligenceDashboardController extends BaseController
{
    protected IntelligenceAnalyticsService $analyticsService;

    public function __construct(?IntelligenceAnalyticsService $service = null)
    {
        $this->analyticsService = $service ?? new IntelligenceAnalyticsService();
    }

    public function index(): string
    {
        $kpis = $this->analyticsService->getOverviewKpi();

        return view('dashboard/intelligence/home/index', [
            'title'                  => 'Dashboard Intelligence | Dashboard BegawiPengadaan',
            'pageTitle'              => 'Dashboard Government Intelligence',
            'activeMenu'             => 'intelligence',
            'activeSubmenu'          => 'dashboard',
            'kpis'                   => $kpis,
            'personnelChart'         => $kpis['personnel_chart'],
            'competencyChart'        => $kpis['competency_chart'],
            'competencyPercentages'  => $kpis['competency_percentages'],
            'topOpdGaps'             => $kpis['top_opd_gaps'],
            'certificateStatus'      => $kpis['certificate_status'],
        ]);
    }

    public function competencyGap(): string
    {
        $req  = $this->request ?? service('request');
        $year = (int) ($req->getGet('year') ?: 2026);

        $gapData   = $this->analyticsService->getCompetencyGapAnalysis($year);
        $snapshots = db_connect()->table('competency_gap_snapshots s')
            ->select('s.*, ou.name AS opd_name, ou.code AS opd_code, c.name AS competency_name')
            ->join('organization_units ou', 'ou.id = s.organization_unit_id', 'left')
            ->join('competencies c', 'c.id = s.competency_id', 'left')
            ->orderBy('s.period_date', 'DESC')
            ->orderBy('s.gap_count', 'DESC')
            ->limit(25)
            ->get()
            ->getResultArray();

        return view('dashboard/intelligence/competency_gap/index', [
            'title'         => 'Analisis Gap Kompetensi | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Analisis Gap Kompetensi OPD',
            'activeMenu'    => 'intelligence',
            'activeSubmenu' => 'competency-gap',
            'year'          => $year,
            'summary'       => $gapData['summary'],
            'chart'         => $gapData['chart'],
            'matrix'        => $gapData['matrix'],
            'snapshots'     => $snapshots,
        ]);
    }

    public function generateSnapshot()
    {
        $req     = $this->request ?? service('request');
        $year    = (int) ($req->getPost('year') ?: date('Y'));
        $gapData = $this->analyticsService->getCompetencyGapAnalysis($year);
        $matrix  = $gapData['matrix'] ?? [];

        $db           = db_connect();
        $competency   = $db->table('competencies')->orderBy('id', 'ASC')->limit(1)->get()->getRowArray();
        $competencyId = (int) ($competency['id'] ?? 1);
        $actorId      = (int) (session()->get('user_id') ?: 13);
        $periodDate   = date('Y-m-d');
        $now          = date('Y-m-d H:i:s');

        $count = 0;
        foreach ($matrix as $row) {
            $opdId = (int) ($row['opd_id'] ?? $row['id'] ?? 0);
            if ($opdId < 1) {
                continue;
            }
            $db->table('competency_gap_snapshots')->insert([
                'organization_unit_id' => $opdId,
                'competency_id'        => $competencyId,
                'period_date'          => $periodDate,
                'required_count'       => (int) ($row['need'] ?? $row['required_count'] ?? 0),
                'available_count'      => (int) ($row['available'] ?? $row['available_count'] ?? 0),
                'gap_count'            => (int) ($row['gap'] ?? $row['gap_count'] ?? 0),
                'metadata'             => json_encode([
                    'year'          => $year,
                    'coverage_pct'  => $row['coverage_pct'] ?? 0,
                    'priority_rank' => $row['priority'] ?? 'Sedang',
                ], JSON_UNESCAPED_UNICODE),
                'generated_by'         => $actorId,
                'created_at'           => $now,
            ]);
            $count++;
        }

        return redirect()->to(site_url('dashboard/intelligence/gap-kompetensi?year=' . $year))
            ->with('message', "Snapshot analisis gap kompetensi untuk {$count} OPD (Periode {$periodDate}) berhasil direkam.");
    }
}
