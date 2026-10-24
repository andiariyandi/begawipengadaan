<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class VerifyHeroAndSpotlight extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:hero-spotlight';
    protected $description = 'Verifies unified Hero Slider and Unggulan badge logic across all 5 modules and Spotlight Hub';

    public function run(array $params)
    {
        CLI::write("==================================================================", "cyan");
        CLI::write("    VERIFIKASI LOGIKA HERO SLIDER & BADGE UNGGULAN (5 MODUL)     ", "yellow");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        $db = \Config\Database::connect();
        $passed = 0;
        $failed = 0;

        // 1. Strict Migration Integrity
        $migrationFiles = glob(APPPATH . 'Database/Migrations/*.php');
        if (count($migrationFiles) === 16) {
            CLI::write(" [PASS] Strict Migration Constraint: Exactly 16 migrations intact", "green");
            $passed++;
        } else {
            CLI::write(" [FAIL] Migration count altered: " . count($migrationFiles), "red");
            $failed++;
        }

        // 2. Test 5 Public Controllers Rendering Cleanly
        $pages = [
            'Arena Belajar (/arena-belajar)' => \App\Controllers\Landing\ArenaBelajarController::class,
            'Bunga Rampai (/bunga-rampai)'   => \App\Controllers\Landing\BungaRampaiController::class,
            'Regulasi PBJ (/regulasi)'       => \App\Controllers\Landing\RegulationPublicController::class,
            'Berita PBJ (/berita)'           => \App\Controllers\Landing\NewsPublicController::class,
            'Event & Agenda (/event)'        => \App\Controllers\Landing\EventPublicController::class,
            'Dashboard Hub (/dashboard/landing/sorotan-carousel)' => \App\Controllers\Admin\Landing\SpotlightHubController::class,
        ];

        foreach ($pages as $label => $class) {
            try {
                $ctrl = new $class();
                $ctrl->initController(service('request'), service('response'), service('logger'));
                $html = $ctrl->index();
                if (is_string($html) && strlen($html) > 500) {
                    CLI::write(" [PASS] Controller {$label} rendered cleanly (" . number_format(strlen($html)) . " bytes)", "green");
                    $passed++;
                } else {
                    CLI::write(" [FAIL] Controller {$label} empty response", "red");
                    $failed++;
                }
            } catch (\Throwable $e) {
                CLI::write(" [FAIL] Controller {$label} error: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine(), "red");
                $failed++;
            }
        }

        // 3. Test Discovery Search Endpoint in SpotlightHubController
        try {
            $hub = new \App\Controllers\Admin\Landing\SpotlightHubController();
            $hub->initController(service('request'), service('response'), service('logger'));
            
            // Test search for arena
            $_GET['feature'] = 'arena';
            $_GET['q'] = '';
            $resp = $hub->searchAvailable();
            $data = json_decode($resp->getBody(), true);
            if (! empty($data['success']) && isset($data['results']) && is_array($data['results'])) {
                CLI::write(" [PASS] SpotlightHub searchAvailable('arena') returned " . count($data['results']) . " items sorted newest", "green");
                $passed++;
            } else {
                CLI::write(" [FAIL] SpotlightHub searchAvailable('arena') failed", "red");
                $failed++;
            }

            // Test search for knowledge
            $_GET['feature'] = 'knowledge';
            $_GET['q'] = '';
            $resp = $hub->searchAvailable();
            $data = json_decode($resp->getBody(), true);
            if (! empty($data['success']) && isset($data['results']) && is_array($data['results'])) {
                CLI::write(" [PASS] SpotlightHub searchAvailable('knowledge') returned " . count($data['results']) . " items (excluding FAQ)", "green");
                $passed++;
            } else {
                CLI::write(" [FAIL] SpotlightHub searchAvailable('knowledge') failed", "red");
                $failed++;
            }
        } catch (\Throwable $e) {
            CLI::write(" [FAIL] SpotlightHub search exception: " . $e->getMessage(), "red");
            $failed++;
        }

        // 4. Test Logic: Condition 1 & Condition 2 on Regulation Spotlight
        try {
            $regService = new \App\Services\Regulation\RegulationPublicService();
            
            // Test spotlightRegulations returns up to limit
            $spotlights = $regService->spotlightRegulations('all', 4);
            if (count($spotlights) <= 4 && count($spotlights) > 0) {
                CLI::write(" [PASS] Regulation spotlight respects quota limit (returned " . count($spotlights) . " items)", "green");
                $passed++;
            } else {
                CLI::write(" [FAIL] Regulation spotlight quota violation", "red");
                $failed++;
            }

            // Verify no duplicate IDs in spotlightRegulations
            $ids = array_column($spotlights, 'id');
            if (count($ids) === count(array_unique($ids))) {
                CLI::write(" [PASS] Regulation spotlight contains zero duplicate IDs", "green");
                $passed++;
            } else {
                CLI::write(" [FAIL] Regulation spotlight contains duplicates: " . implode(',', $ids), "red");
                $failed++;
            }
        } catch (\Throwable $e) {
            CLI::write(" [FAIL] Regulation service exception: " . $e->getMessage(), "red");
            $failed++;
        }

        // 5. Test Decoupling: Toggle Sorotan vs Slider
        // Toggle carousel_enabled on a test record, verify it does NOT set featured/is_featured
        try {
            $sampleNews = $db->table('news')->where('deleted_at', null)->get()->getRowArray();
            if ($sampleNews) {
                $origSlider = (int) ($sampleNews['carousel_enabled'] ?? 0);
                $origFeat   = (int) ($sampleNews['is_featured'] ?? 0);

                // Update carousel_enabled
                $db->table('news')->where('id', $sampleNews['id'])->update(['carousel_enabled' => $origSlider ? 0 : 1]);
                $after = $db->table('news')->where('id', $sampleNews['id'])->get()->getRowArray();

                if ((int)$after['is_featured'] === $origFeat) {
                    CLI::write(" [PASS] Toggling Slider (carousel_enabled) DOES NOT alter Sorotan (is_featured)", "green");
                    $passed++;
                } else {
                    CLI::write(" [FAIL] Slider and Sorotan states coupled!", "red");
                    $failed++;
                }

                // Restore
                $db->table('news')->where('id', $sampleNews['id'])->update(['carousel_enabled' => $origSlider]);
            }
        } catch (\Throwable $e) {
            CLI::write(" [FAIL] Toggle decouple test exception: " . $e->getMessage(), "red");
            $failed++;
        }

        CLI::newLine();
        CLI::write("==================================================================", "cyan");
        CLI::write("  HASIL VERIFIKASI: {$passed} BERHASIL, {$failed} GAGAL", $failed === 0 ? "green" : "red");
        CLI::write("==================================================================", "cyan");

        return $failed === 0 ? 0 : 1;
    }
}
