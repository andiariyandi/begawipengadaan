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
        CLI::write("PASS: Legacy array targetId handled gracefully without TypeError! Snapshots captured.", 'green');

        // 3. Test TeamMemberController update execution with real database row
        CLI::write("[TEST 3] Testing TeamMemberController update simulated request...");
        $member = $db->table('team_members')->orderBy('id', 'ASC')->get()->getRowArray();
        if (! $member) {
            $db->table('team_members')->insert([
                'name'         => 'Test Member',
                'role_title'   => 'Test Role',
                'nip'          => '12345',
                'category'     => 'pembina',
                'photo_url'    => 'assets/main/images/team/team-1-1.jpg',
                'bio'          => 'Test Bio',
                'social_links' => json_encode(['badge' => 'Test']),
                'sort_order'   => 1,
                'is_active'    => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ]);
            $memberId = $db->insertID();
            $member = $db->table('team_members')->where('id', $memberId)->get()->getRowArray();
        } else {
            $memberId = (int) $member['id'];
        }

        // Simulate POST request
        $_POST = [
            'name'       => $member['name'] . ' Updated',
            'role_title' => $member['role_title'],
            'nip'        => $member['nip'] ?? '',
            'category'   => $member['category'] ?? 'pembina',
            'photo_url'  => $member['photo_url'] ?? '',
            'bio'        => $member['bio'] ?? '',
            'badge'      => 'Pembina Ahli',
            'sort_order' => 1,
            'is_active'  => 1,
        ];

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'dashboard/landing/tim/update/' . $memberId);
        $incomingRequest = new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent());
        $incomingRequest = $incomingRequest->withMethod('POST');
        \Config\Services::injectMock('request', $incomingRequest);

        $controller = new \App\Controllers\Admin\Landing\TeamMemberController();
        $controller->initController($incomingRequest, service('response'), service('logger'));
        $response = $controller->update($memberId);

        CLI::write("PASS: TeamMemberController::update({$memberId}) completed without error! Response class: " . get_class($response), 'green');

        // 4. Test AnnouncementController update
        CLI::write("[TEST 4] Testing AnnouncementController update...");
        $announcement = $db->table('site_announcements')->orderBy('id', 'ASC')->get()->getRowArray();
        if ($announcement) {
            $_POST = [
                'badge_label' => 'Info',
                'title'       => $announcement['title'] . ' Updated',
                'description' => 'Test Desc',
                'image_url'   => $announcement['image_url'],
                'cta_label'   => 'Lihat',
                'cta_url'     => '#',
                'sort_order'  => 1,
                'is_active'   => 1,
            ];
            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'dashboard/landing/pengumuman/update/' . $announcement['id']);
            $req = (new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent()))->withMethod('POST');
            \Config\Services::injectMock('request', $req);
            $c = new \App\Controllers\Admin\Landing\AnnouncementController();
            $c->initController($req, service('response'), service('logger'));
            $res = $c->update((int) $announcement['id']);
            CLI::write("PASS: AnnouncementController::update completed!", 'green');
        }

        // 5. Test ExternalPortalController update
        CLI::write("[TEST 5] Testing ExternalPortalController update...");
        $portal = $db->table('external_portals')->orderBy('id', 'ASC')->get()->getRowArray();
        if ($portal) {
            $_POST = [
                'name'        => $portal['name'] . ' Updated',
                'acronym'     => $portal['acronym'] ?? 'TEST',
                'category'    => 'nasional',
                'description' => 'Portal Test',
                'url'         => 'https://example.com',
                'logo_url'    => $portal['logo_url'],
                'sort_order'  => 1,
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

        // 6. Test HighlightFeatureController update
        CLI::write("[TEST 6] Testing HighlightFeatureController update...");
        $item = $db->table('feature_highlights')->orderBy('id', 'ASC')->get()->getRowArray();
        if ($item) {
            $_POST = [
                'code'        => $item['code'] ?? 'TEST',
                'badge_text'  => 'Pilar',
                'title'       => $item['title'] . ' Updated',
                'subtitle'    => 'Subtitle',
                'description' => 'Desc',
                'icon_class'  => 'solar:star-bold',
                'color_hex'   => '#2563eb',
                'link_url'    => '#',
                'sort_order'  => 1,
                'is_active'   => 1,
            ];
            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'dashboard/landing/pilar-hierarki/update/' . $item['id']);
            $req = (new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent()))->withMethod('POST');
            \Config\Services::injectMock('request', $req);
            $c = new \App\Controllers\Admin\Landing\HighlightFeatureController();
            $c->initController($req, service('response'), service('logger'));
            $res = $c->update((int) $item['id']);
            CLI::write("PASS: HighlightFeatureController::update completed!", 'green');
        }

        // 7. Test PageBannerController update
        CLI::write("[TEST 7] Testing PageBannerController update...");
        $banner = $db->table('page_banners')->orderBy('id', 'ASC')->get()->getRowArray();
        if ($banner) {
            $_POST = [
                'hero_badge'             => 'Badge',
                'hero_title'             => ($banner['hero_title'] ?? 'Title') . ' Updated',
                'hero_subtitle'          => 'Subtitle',
                'cta_badge'              => 'Badge',
                'cta_title'              => 'CTA Title',
                'cta_lead'               => 'Lead',
                'cta_primary_btn_text'   => 'Btn',
                'cta_primary_btn_url'    => '#',
                'cta_secondary_btn_text' => 'Btn2',
                'cta_secondary_btn_url'  => '#',
            ];
            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'dashboard/landing/banner-halaman/update/' . $banner['page_key']);
            $req = (new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent()))->withMethod('POST');
            \Config\Services::injectMock('request', $req);
            $c = new \App\Controllers\Admin\Landing\PageBannerController();
            $c->initController($req, service('response'), service('logger'));
            $res = $c->update($banner['page_key']);
            CLI::write("PASS: PageBannerController::update completed!", 'green');
        }

        // 8. Test ServiceCategoryController update
        CLI::write("[TEST 8] Testing ServiceCategoryController update...");
        $cat = $db->table('service_categories')->orderBy('id', 'ASC')->get()->getRowArray();
        if ($cat) {
            $_POST = [
                'name'        => $cat['name'] . ' Updated',
                'code'        => $cat['code'],
                'description' => 'Desc',
                'sort_order'  => 1,
                'is_active'   => 1,
            ];
            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'dashboard/landing/kategori-layanan/update/' . $cat['id']);
            $req = (new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent()))->withMethod('POST');
            \Config\Services::injectMock('request', $req);
            $c = new \App\Controllers\Admin\Landing\ServiceCategoryController();
            $c->initController($req, service('response'), service('logger'));
            $res = $c->update((int) $cat['id']);
            CLI::write("PASS: ServiceCategoryController::update completed!", 'green');
        }

        // 9. Test SliderController update
        CLI::write("[TEST 9] Testing SliderController update...");
        $slider = $db->table('home_sliders')->orderBy('id', 'ASC')->get()->getRowArray();
        if ($slider) {
            $_POST = [
                'badge_text'            => 'Badge',
                'title'                 => $slider['title'] . ' Updated',
                'subtitle'              => 'Subtitle',
                'image_url'             => $slider['image_url'],
                'button_text'           => 'Btn',
                'button_url'            => '#',
                'secondary_button_text' => 'Btn2',
                'secondary_button_url'  => '#',
                'sort_order'            => 1,
                'is_active'             => 1,
            ];
            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'dashboard/landing/slider/update/' . $slider['id']);
            $req = (new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent()))->withMethod('POST');
            \Config\Services::injectMock('request', $req);
            $c = new \App\Controllers\Admin\Landing\SliderController();
            $c->initController($req, service('response'), service('logger'));
            $res = $c->update((int) $slider['id']);
            CLI::write("PASS: SliderController::update completed!", 'green');
        }

        // 10. Test TestimonialController update
        CLI::write("[TEST 10] Testing TestimonialController update...");
        $testi = $db->table('testimonials')->orderBy('id', 'ASC')->get()->getRowArray();
        if ($testi) {
            $_POST = [
                'name'             => $testi['name'] . ' Updated',
                'role_title'       => $testi['role_title'] ?? 'Staff',
                'organization'     => $testi['organization'] ?? 'Instansi',
                'avatar_url'       => $testi['avatar_url'] ?? '',
                'rating'           => 5,
                'quote'            => 'Sangat baik',
                'impact_statement' => 'Berdampak',
                'category'         => 'opd',
                'sort_order'       => 1,
                'is_active'        => 1,
            ];
            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'dashboard/landing/testimoni/update/' . $testi['id']);
            $req = (new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent()))->withMethod('POST');
            \Config\Services::injectMock('request', $req);
            $c = new \App\Controllers\Admin\Landing\TestimonialController();
            $c->initController($req, service('response'), service('logger'));
            $res = $c->update((int) $testi['id']);
            CLI::write("PASS: TestimonialController::update completed!", 'green');
        }

        // 11. Test ContactMessageController updateStatus
        CLI::write("[TEST 11] Testing ContactMessageController updateStatus...");
        $msg = $db->table('contact_messages')->orderBy('id', 'ASC')->get()->getRowArray();
        if ($msg) {
            $_POST = [
                'status'     => 'in_progress',
                'admin_note' => 'Sedang dicek',
            ];
            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'dashboard/komunikasi/pesan-masuk/update/' . $msg['id']);
            $req = (new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent()))->withMethod('POST');
            \Config\Services::injectMock('request', $req);
            $c = new \App\Controllers\Admin\Communication\ContactMessageController();
            $c->initController($req, service('response'), service('logger'));
            $res = $c->updateStatus((int) $msg['id']);
            CLI::write("PASS: ContactMessageController::updateStatus completed!", 'green');
        }

        // Clean up test audit logs
        $db->table('audit_logs')->whereIn('resource_id', [9999, 8888])->delete();

        CLI::write("=== All Audit Tests Passed Successfully! ===", 'green');
    }
}
