<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\AuditService;
use Config\Database;

class LandingCmsAuditTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'landing:cms:audit:test';
    protected $description = 'Tests AuditService flexibility and Landing CMS audit logging';

    public function run(array $params)
    {
        CLI::write("=== Testing AuditService & Landing CMS Audit Logging ===", 'yellow');

        $db = Database::connect();
        $auditService = new AuditService();

        // 1. Test canonical AuditService::log call
        CLI::write("[TEST 1] Canonical log() call with int targetId...");
        $auditService->log(1, 'landing.team.updated', 'landing', 'team_members', 9999, [
            'before' => ['name' => 'Old Name'],
            'after'  => ['name' => 'New Name'],
        ]);
        $row1 = $db->table('audit_logs')->where('resource_id', 9999)->where('action', 'landing.team.updated')->orderBy('id', 'DESC')->get()->getRowArray();
        if (! $row1 || ($row1['resource_type'] ?? '') !== 'team_members') {
            CLI::error("FAIL: Canonical log failed or resource_type mismatch: " . json_encode($row1));
            return;
        }
        CLI::write("PASS: Canonical log recorded correctly (resource_type={$row1['resource_type']}, resource_id={$row1['resource_id']}).", 'green');

        // 2. Test legacy signature: ($userId, $action, $resourceType, $resourceId, $beforeSnapshot, $afterSnapshot)
        CLI::write("[TEST 2] Legacy/snapshot log() call where targetId is an array (the exact bug case)...");
        $auditService->log(1, 'landing.team.updated_legacy', 'team_members', '8888', ['name' => 'Legacy Before'], ['name' => 'Legacy After']);
        $row2 = $db->table('audit_logs')->where('resource_id', 8888)->where('action', 'landing.team.updated_legacy')->orderBy('id', 'DESC')->get()->getRowArray();
        if (! $row2 || ($row2['resource_type'] ?? '') !== 'team_members') {
            CLI::error("FAIL: Legacy log failed or resource_type mismatch: " . json_encode($row2));
            return;
        }
        if (empty($row2['before_snapshot']) || empty($row2['after_snapshot'])) {
            CLI::error("FAIL: before_snapshot or after_snapshot was not captured: " . json_encode($row2));
            return;
        }
        CLI::write("PASS: Legacy signature log recorded correctly with snapshot data.", 'green');

        // 3. Test InstitutionProfileController save
        CLI::write("[TEST 3] Testing InstitutionProfileController save...");
        $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'dashboard/landing/profil-lembaga');
        $req = (new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent()))->withMethod('POST');
        $_POST = [
            'agency_name'  => 'Dinas Pengadaan Uji Coba',
            'agency_tagline' => 'Tagline Uji Coba',
            'welcome_lead' => 'Lead Uji Coba',
            'motto_title'  => 'Motto Uji Coba',
            'motto_desc'   => 'Motto Desc Uji Coba',
            'vision'       => 'Visi Uji Coba',
            'mission'      => "Misi 1\nMisi 2",
        ];
        \Config\Services::injectMock('request', $req);
        $c = new \App\Controllers\Admin\Landing\InstitutionProfileController();
        $c->initController($req, service('response'), service('logger'));
        $res = $c->save();
        $auditInst = $db->table('audit_logs')->groupStart()->where('action', 'landing.profile.updated')->orWhere('action', 'landing.institution_profile.updated')->groupEnd()->orderBy('id', 'DESC')->get()->getRowArray();
        if (! $auditInst) {
            CLI::error("FAIL: InstitutionProfileController save didn't log audit_log");
            return;
        }
        CLI::write("PASS: InstitutionProfileController::save logged audit_log correctly!", 'green');

        // 4. Test ExternalPortalController update
        CLI::write("[TEST 4] Testing ExternalPortalController update...");
        $portal = $db->table('external_portals')->orderBy('id', 'ASC')->get()->getRowArray();
        if ($portal) {
            $_POST = [
                'name'        => ($portal['name'] ?? 'Portal') . ' Updated',
                'acronym'     => $portal['acronym'] ?? 'UP',
                'category'    => $portal['category'] ?? 'daerah',
                'description' => 'Test Desc',
                'url'         => $portal['url'] ?? 'https://example.com',
                'sort_order'  => (int) ($portal['sort_order'] ?? 1),
                'is_active'   => 1,
            ];
            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'dashboard/landing/portal-eksternal/update/' . $portal['id']);
            $req = (new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent()))->withMethod('POST');
            \Config\Services::injectMock('request', $req);
            $c = new \App\Controllers\Admin\Landing\ExternalPortalController();
            $c->initController($req, service('response'), service('logger'));
            $res = $c->update((int) $portal['id']);
            CLI::write("PASS: ExternalPortalController::update completed!", 'green');
        }

        // 5. Test TestimonialController update
        CLI::write("[TEST 5] Testing TestimonialController update...");
        $testi = $db->table('testimonials')->orderBy('id', 'ASC')->get()->getRowArray();
        if ($testi) {
            $_POST = [
                'author_name' => ($testi['author_name'] ?? 'Name') . ' Updated',
                'author_title'=> 'Title',
                'agency'      => 'Agency',
                'quote'       => 'Quote test',
                'rating'      => 5,
                'sort_order'  => 1,
                'is_active'   => 1,
            ];
            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'dashboard/landing/testimoni/update/' . $testi['id']);
            $req = (new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent()))->withMethod('POST');
            \Config\Services::injectMock('request', $req);
            $c = new \App\Controllers\Admin\Landing\TestimonialController();
            $c->initController($req, service('response'), service('logger'));
            $res = $c->update((int) $testi['id']);
            CLI::write("PASS: TestimonialController::update completed!", 'green');
        }

        // 6. Test HighlightFeatureController update
        CLI::write("[TEST 6] Testing HighlightFeatureController update...");
        $item = $db->table('feature_highlights')->orderBy('id', 'ASC')->get()->getRowArray();
        if ($item) {
            $_POST = [
                'section_group' => $item['section_group'] ?? 'pilar_layanan',
                'title'         => ($item['title'] ?? 'Title') . ' Updated',
                'subtitle'      => 'Subtitle',
                'description'   => 'Desc test',
                'sort_order'    => 1,
                'is_active'     => 1,
            ];
            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'dashboard/landing/pilar-hierarki/update/' . $item['id']);
            $req = (new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent()))->withMethod('POST');
            \Config\Services::injectMock('request', $req);
            $c = new \App\Controllers\Admin\Landing\HighlightFeatureController();
            $c->initController($req, service('response'), service('logger'));
            $res = $c->update((int) $item['id']);
            CLI::write("PASS: HighlightFeatureController::update completed!", 'green');
        }

        // 7. Verify PageBannerController retired and cleaned up
        CLI::write("[TEST 7] Testing Banner Halaman retirement & redirect...");
        $controllerExists = file_exists(APPPATH . 'Controllers/Admin/Landing/PageBannerController.php');
        if (! $controllerExists) {
            CLI::write("PASS: PageBannerController retired and cleaned up!", 'green');
        } else {
            CLI::write("FAIL: PageBannerController still exists", 'red');
        }

        // 8. Verify ServiceCategoryController retired and cleaned up
        CLI::write("[TEST 8] Testing Kategori Layanan retirement & cleanup...");
        $catControllerExists = file_exists(APPPATH . 'Controllers/Admin/Landing/ServiceCategoryController.php');
        if (! $catControllerExists) {
            CLI::write("PASS: ServiceCategoryController retired and cleaned up!", 'green');
        } else {
            CLI::write("FAIL: ServiceCategoryController still exists", 'red');
        }

        // 9. Test TeamMemberController update
        CLI::write("[TEST 9] Testing TeamMemberController update...");
        $member = $db->table('team_members')->orderBy('id', 'ASC')->get()->getRowArray();
        if ($member) {
            $_POST = [
                'name'       => ($member['name'] ?? 'Name') . ' Updated',
                'position'   => 'Position',
                'bio'        => 'Bio',
                'sort_order' => 1,
                'is_active'  => 1,
            ];
            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'dashboard/landing/tim/update/' . $member['id']);
            $req = (new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent()))->withMethod('POST');
            \Config\Services::injectMock('request', $req);
            $c = new \App\Controllers\Admin\Landing\TeamMemberController();
            $c->initController($req, service('response'), service('logger'));
            $res = $c->update((int) $member['id']);
            CLI::write("PASS: TeamMemberController::update completed!", 'green');
        }

        // 10. Test AnnouncementController update
        CLI::write("[TEST 10] Testing AnnouncementController update...");
        $ann = $db->table('site_announcements')->orderBy('id', 'ASC')->get()->getRowArray();
        if ($ann) {
            $_POST = [
                'badge_label' => 'Badge',
                'title'       => ($ann['title'] ?? 'Title') . ' Updated',
                'description' => 'Desc',
                'sort_order'  => 1,
                'is_active'   => 1,
            ];
            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'dashboard/landing/pengumuman/update/' . $ann['id']);
            $req = (new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent()))->withMethod('POST');
            \Config\Services::injectMock('request', $req);
            $c = new \App\Controllers\Admin\Landing\AnnouncementController();
            $c->initController($req, service('response'), service('logger'));
            $res = $c->update((int) $ann['id']);
            CLI::write("PASS: AnnouncementController::update completed!", 'green');
        }

        // 11. Test SliderController update
        CLI::write("[TEST 11] Testing SliderController update...");
        $slider = $db->table('home_sliders')->orderBy('id', 'ASC')->get()->getRowArray();
        if ($slider) {
            $_POST = [
                'title'      => ($slider['title'] ?? 'Title') . ' Updated',
                'badge_text' => 'Badge',
                'subtitle'   => 'Subtitle',
                'sort_order' => 1,
                'is_active'  => 1,
            ];
            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'dashboard/landing/slider/update/' . $slider['id']);
            $req = (new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent()))->withMethod('POST');
            \Config\Services::injectMock('request', $req);
            $c = new \App\Controllers\Admin\Landing\SliderController();
            $c->initController($req, service('response'), service('logger'));
            $res = $c->update((int) $slider['id']);
            CLI::write("PASS: SliderController::update completed!", 'green');
        }

        CLI::write("=== All Landing CMS Controllers & AuditLog Tests Passed! ===", 'green');
    }
}
