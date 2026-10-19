<?php

namespace App\Commands;

use App\Controllers\Admin\Survey\SatisfactionSurveyAdminController;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SurveyAdminTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'survey:admin:test';
    protected $description = 'Test survey admin dashboard controller and view';

    public function run(array $params): int
    {
        CLI::write("Testing Survey Admin Dashboard...", "cyan");

        $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), '');
        $incomingRequest = new \CodeIgniter\HTTP\IncomingRequest(
            config('App'),
            $siteUri,
            null,
            new \CodeIgniter\HTTP\UserAgent()
        );
        \Config\Services::injectMock('request', $incomingRequest);

        $controller = new SatisfactionSurveyAdminController();
        $controller->initController($incomingRequest, service('response'), service('logger'));

        $html = $controller->index();
        if (is_string($html) && strlen($html) > 0) {
            CLI::write("[PASS] SatisfactionSurveyAdminController::index rendered successfully (" . strlen($html) . " bytes)", "green");
            return 0;
        }

        CLI::error("[FAIL] Index did not render string");
        return 1;
    }
}
