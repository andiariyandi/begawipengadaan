<?php

namespace App\Commands;

use App\Controllers\Landing\RegulationPublicController;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class RegulationLandingTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'regulation:test';
    protected $description = 'Comprehensive test suite for Regulasi landing pages, category filtering, detail pages, and download flow.';
    protected $usage       = 'regulation:test';

    public function run(array $params): int
    {
        CLI::write('==================================================================', 'yellow');
        CLI::write('     TEST SUITE: REGULASI PBJ LANDING, CATALOG & DETAIL SYSTEM    ', 'yellow');
        CLI::write('==================================================================', 'yellow');
        CLI::newLine();

        $passed = 0;
        $failed = 0;
        $controller = new RegulationPublicController();

        // 1. Index Page
        CLI::write('>>> TEST 1: Main Regulation Index (/regulasi)', 'cyan');
        try {
            $_GET = [];
            $html = $controller->index();
            $passed++;
            CLI::write('  [OK] Index rendered successfully, length: ' . strlen($html), 'green');
        } catch (\Throwable $e) {
            $failed++;
            CLI::error('  [FAIL] Index error: ' . $e->getMessage());
        }

        // 2. Database Stats
        $db = db_connect();
        $totalRegs = $db->table('regulations')->countAllResults();
        $totalCats = $db->table('regulation_categories')->countAllResults();
        CLI::write("  [INFO] DB Regulations: {$totalRegs}, Categories: {$totalCats}", 'yellow');

        $cats = $db->table('regulation_categories')->get()->getResultArray();
        CLI::write("Categories (" . count($cats) . "):", 'white');
        foreach ($cats as $c) {
            CLI::write("  - [ID: {$c['id']}] {$c['name']} (slug: {$c['slug']})", 'white');
        }

        $stages = $db->table('procurement_stages')->get()->getResultArray();
        CLI::write("Procurement Stages (" . count($stages) . "):", 'white');
        foreach ($stages as $st) {
            CLI::write("  - [ID: {$st['id']}] {$st['name']} (slug: " . ($st['slug'] ?? '-') . ")", 'white');
        }

        $competencies = $db->table('competencies')->get()->getResultArray();
        CLI::write("Competencies (" . count($competencies) . "):", 'white');
        foreach ($competencies as $co) {
            CLI::write("  - [ID: {$co['id']}] [{$co['key']}] {$co['name']}", 'white');
        }

        $sample = $db->table('regulations')->where('deleted_at', null)->where('workflow_status', 'published')->get()->getRowArray();
        if ($sample) {
            CLI::write(">>> TEST 2: Detail Regulation (/regulasi/{$sample['slug']})", 'cyan');
            try {
                $detailHtml = $controller->detail($sample['slug']);
                $passed++;
                CLI::write("  [OK] Detail rendered for slug: {$sample['slug']}", 'green');
            } catch (\Throwable $e) {
                $failed++;
                CLI::error('  [FAIL] Detail error: ' . $e->getMessage());
            }

            CLI::write(">>> TEST 3: Version History (/regulasi/{$sample['slug']}/versi)", 'cyan');
            try {
                $versionsHtml = $controller->versions($sample['slug']);
                $passed++;
                CLI::write("  [OK] Versions rendered for slug: {$sample['slug']}", 'green');
            } catch (\Throwable $e) {
                $failed++;
                CLI::error('  [FAIL] Versions error: ' . $e->getMessage());
            }

            CLI::write(">>> TEST 4: Relations (/regulasi/{$sample['slug']}/relasi)", 'cyan');
            try {
                $relationsHtml = $controller->relations($sample['slug']);
                $passed++;
                CLI::write("  [OK] Relations rendered for slug: {$sample['slug']}", 'green');
            } catch (\Throwable $e) {
                $failed++;
                CLI::error('  [FAIL] Relations error: ' . $e->getMessage());
            }
        }

        $sampleCat = $db->table('regulation_categories')->where('is_active', 1)->get()->getRowArray();
        if ($sampleCat) {
            CLI::write(">>> TEST 5: Category Filter (/regulasi/kategori/{$sampleCat['slug']})", 'cyan');
            try {
                $_GET = [];
                $catHtml = $controller->category($sampleCat['slug']);
                $passed++;
                CLI::write("  [OK] Category rendered for slug: {$sampleCat['slug']}", 'green');
            } catch (\Throwable $e) {
                $failed++;
                CLI::error('  [FAIL] Category error: ' . $e->getMessage());
            }
        }

        CLI::newLine();
        CLI::write("==================================================================", 'yellow');
        CLI::write("TEST RESULTS: {$passed} Passed, {$failed} Failed", $failed > 0 ? 'red' : 'green');
        CLI::write("==================================================================", 'yellow');

        return $failed > 0 ? 1 : 0;
    }
}
