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

        $gapData = $this->analyticsService->getCompetencyGapAnalysis($year);

        return view('dashboard/intelligence/competency_gap/index', [
            'title'         => 'Analisis Gap Kompetensi | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Analisis Gap Kompetensi OPD',
            'activeMenu'    => 'intelligence',
            'activeSubmenu' => 'competency-gap',
            'year'          => $year,
            'summary'       => $gapData['summary'],
            'chart'         => $gapData['chart'],
            'matrix'        => $gapData['matrix'],
        ]);
    }
}
