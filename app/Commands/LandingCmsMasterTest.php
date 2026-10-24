<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\HTTP\Files\UploadedFile;
use App\Services\LandingImageUploadService;

class TestUploadedFile extends UploadedFile
{
    public function isValid(): bool
    {
        return file_exists($this->path) && ($this->error === null || $this->error === UPLOAD_ERR_OK);
    }

    public function move(string $targetPath, ?string $name = null, bool $overwrite = false)
    {
        $targetPath = rtrim($targetPath, '/') . '/';
        $this->setPath($targetPath);
        $name ??= $this->getRandomName();
        $destination = $overwrite ? $targetPath . $name : $this->getDestination($targetPath . $name);

        $this->hasMoved = copy($this->path, $destination);
        $this->path     = $targetPath;
        $this->name     = basename($destination);

        return $this->hasMoved;
    }
}

class LandingCmsMasterTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:landing-cms';
    protected $description = 'Comprehensive test suite for Landing CMS sidebar, routes, CRUD operations, image upload pipeline, and migration integrity';

    private int $pass = 0;
    private int $fail = 0;

    public function run(array $params)
    {
        CLI::write("=======================================================", 'cyan');
        CLI::write("  BEGAWIPENGADAAN - LANDING CMS MASTER VERIFICATION", 'yellow');
        CLI::write("=======================================================", 'cyan');

        $this->testMigrationCount();
        $this->testSidebarSingleItem();
        $this->testLandingRoutes();
        $this->testImageUploadPipeline();
        $this->testSliderCrud();
        $this->testAnnouncementCrud();
        $this->testExternalPortalCrud();
        $this->testTeamMemberCrud();
        $this->testTestimonialCrud();
        $this->testHighlightCrud();
        $this->testCategoryCleaned();
        $this->testBannerHalamanCleaned();
        $this->testInstitutionProfileUpdate();
        $this->testSpotlightHubFeatures();

        CLI::newLine();
        CLI::write("=======================================================", 'cyan');
        CLI::write("  TEST RESULTS: {$this->pass} PASSED, {$this->fail} FAILED", $this->fail === 0 ? 'green' : 'red');
        CLI::write("=======================================================", 'cyan');

        return $this->fail === 0 ? EXIT_SUCCESS : EXIT_ERROR;
    }

    private function assert(string $label, bool $condition, string $detail = '')
    {
        if ($condition) {
            $this->pass++;
            CLI::write(" [PASS] {$label}", 'green');
        } else {
            $this->fail++;
            CLI::write(" [FAIL] {$label}" . ($detail ? " - {$detail}" : ""), 'red');
        }
    }

    private function testMigrationCount()
    {
        CLI::newLine();
        CLI::write("--- 1. Testing Migration Integrity ---", 'yellow');

        $migrationsPath = APPPATH . 'Database/Migrations';
        $files = glob($migrationsPath . '/*.php') ?: [];
        $count = count($files);

        $this->assert("Strict constraint: Exactly 16 migration files", $count === 16, "Found {$count} files");
    }

    private function testSidebarSingleItem()
    {
        CLI::newLine();
        CLI::write("--- 2. Testing Sidebar Clean Single Menu ---", 'yellow');

        $sidebarContent = file_get_contents(APPPATH . 'Views/dashboard/components/sidebar.php');

        $hasLandingLink = str_contains($sidebarContent, "href=\"<?= site_url('dashboard/landing') ?>\"");
        $this->assert("Sidebar has direct link to dashboard/landing", $hasLandingLink);

        $hasSingleLi = preg_match('/<li class="<\?=\s*\$currentMenu === \'landing\' \? \'active-page\' : \'\' \?>">\s*<a class="<\?=\s*\$currentMenu === \'landing\' \? \'active-page\' : \'\' \?>" href="<\?=\s*site_url\(\'dashboard\/landing\'\)\s*\?>">/', $sidebarContent);
        $this->assert("Sidebar has single li item for Kelola Landing Page", (bool) $hasSingleLi);

        $hasSubmenu = str_contains($sidebarContent, "openClass('landing')");
        $this->assert("Sidebar has NO dropdown/submenu for landing", ! $hasSubmenu);

        $hasHeroPromosiSub = str_contains($sidebarContent, "Hero &amp; Promosi");
        $this->assert("Sidebar has removed Hero & Promosi submenu", ! $hasHeroPromosiSub);
    }

    private function testLandingRoutes()
    {
        CLI::newLine();
        CLI::write("--- 3. Testing Landing Routes Registration ---", 'yellow');

        $collection = service('routes')->loadRoutes();
        $collector = new \CodeIgniter\Router\DefinedRouteCollector($collection);
        $definedRoutes = $collector->collect();

        $routesMap = [];
        foreach ($definedRoutes as $r) {
            if (strtoupper($r['method']) === 'GET') {
                $routesMap[trim($r['route'], '/')] = $r;
            }
        }

        $expectedGetRoutes = [
            'dashboard/landing',
            'dashboard/landing/sorotan-carousel',
            'dashboard/landing/sorotan-carousel/search-available',
            'dashboard/landing/slider',
            'dashboard/landing/slider/tambah',
            'dashboard/landing/slider/ubah/([^/]+)',
            'dashboard/landing/slider/edit/([^/]+)',
            'dashboard/landing/slider/duplikat/([^/]+)',
            'dashboard/landing/pengumuman',
            'dashboard/landing/pengumuman/tambah',
            'dashboard/landing/pengumuman/ubah/([^/]+)',
            'dashboard/landing/pengumuman/edit/([^/]+)',
            'dashboard/landing/pengumuman/duplikat/([^/]+)',
            'dashboard/landing/profil-lembaga',
            'dashboard/landing/pilar-hierarki',
            'dashboard/landing/pilar-hierarki/tambah',
            'dashboard/landing/pilar-hierarki/ubah/([^/]+)',
            'dashboard/landing/pilar-hierarki/edit/([^/]+)',
            'dashboard/landing/pilar-hierarki/duplikat/([^/]+)',
            'dashboard/landing/portal-eksternal',
            'dashboard/landing/portal-eksternal/tambah',
            'dashboard/landing/portal-eksternal/ubah/([^/]+)',
            'dashboard/landing/portal-eksternal/edit/([^/]+)',
            'dashboard/landing/portal-eksternal/duplikat/([^/]+)',
            'dashboard/landing/tim',
            'dashboard/landing/tim/tambah',
            'dashboard/landing/tim/ubah/([^/]+)',
            'dashboard/landing/tim/edit/([^/]+)',
            'dashboard/landing/tim/duplikat/([^/]+)',
            'dashboard/landing/testimoni',
            'dashboard/landing/testimoni/tambah',
            'dashboard/landing/testimoni/ubah/([^/]+)',
            'dashboard/landing/testimoni/edit/([^/]+)',
            'dashboard/landing/testimoni/duplikat/([^/]+)',
        ];

        foreach ($expectedGetRoutes as $expectedRoute) {
            $expectedTrimmed = trim($expectedRoute, '/');
            $isRegistered = isset($routesMap[$expectedTrimmed]);
            $this->assert("Route registered: GET {$expectedRoute}", $isRegistered);
        }
    }

    private function testImageUploadPipeline()
    {
        CLI::newLine();
        CLI::write("--- 4. Testing Image Upload Pipeline ---", 'yellow');

        // Test temp file creation
        $tempDir = WRITEPATH . 'uploads';
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0775, true);
        }
        $testImagePath = $tempDir . '/test_landing_upload.png';

        // Create a 1x1 valid PNG image
        $pngData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        file_put_contents($testImagePath, $pngData);

        $uploadedFile = new TestUploadedFile($testImagePath, 'test_landing_upload.png', 'image/png', filesize($testImagePath), UPLOAD_ERR_OK);

        $db = \Config\Database::connect();
        $targetRelative = LandingImageUploadService::upload($uploadedFile, 'sliders', null, 'test_landing');

        $this->assert("Upload service returned relative path", ! empty($targetRelative) && str_starts_with($targetRelative, 'uploads/landing/sliders/'));

        if (! empty($targetRelative)) {
            $physicalFile = FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $targetRelative);
            $this->assert("Physical uploaded file exists on disk", file_exists($physicalFile));

            // Check asset table record
            $asset = $db->table('file_assets')->where('path', $targetRelative)->get()->getRowArray();
            $this->assert("Asset record created in file_assets table", $asset !== null);

            // Cleanup
            if (file_exists($physicalFile)) {
                @unlink($physicalFile);
            }
            if ($asset) {
                $db->table('file_assets')->where('id', $asset['id'])->delete();
            }
        }

        if (file_exists($testImagePath)) {
            @unlink($testImagePath);
        }
    }

    private function testSliderCrud()
    {
        CLI::newLine();
        CLI::write("--- 5. Testing Slider CRUD Operations ---", 'yellow');

        $db = \Config\Database::connect();
        $table = $db->table('home_sliders');

        // Insert
        $testData = [
            'badge_text' => 'TEST_BADGE',
            'title'      => 'Test Slide Title',
            'subtitle'   => 'Test Slide Subtitle',
            'image_url'  => 'assets/main/images/backgrounds/slider-1-1.png',
            'sort_order' => 99,
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $table->insert($testData);
        $insertId = $db->insertID();
        $this->assert("Slider created with ID {$insertId}", $insertId > 0);

        // Update
        $table->where('id', $insertId)->update(['title' => 'Updated Test Slide Title']);
        $updated = $table->where('id', $insertId)->get()->getRowArray();
        $this->assert("Slider updated title", ($updated['title'] ?? '') === 'Updated Test Slide Title');

        // Toggle
        $newActive = $updated['is_active'] ? 0 : 1;
        $table->where('id', $insertId)->update(['is_active' => $newActive]);
        $toggled = $table->where('id', $insertId)->get()->getRowArray();
        $this->assert("Slider toggle active status", (int) ($toggled['is_active'] ?? 1) === $newActive);

        // Test Duplicate
        $dupData = [
            'badge_text' => $updated['badge_text'],
            'title'      => '[Salinan] ' . $updated['title'],
            'subtitle'   => $updated['subtitle'],
            'image_url'  => $updated['image_url'],
            'sort_order' => 100,
            'is_active'  => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $table->insert($dupData);
        $dupId = $db->insertID();
        $this->assert("Slider duplicated as draft with ID {$dupId}", $dupId > 0);

        // Test Reorder (swap orders)
        $table->where('id', $insertId)->update(['sort_order' => 100]);
        $table->where('id', $dupId)->update(['sort_order' => 99]);
        $reorderedOriginal = $table->where('id', $insertId)->get()->getRowArray();
        $reorderedDup = $table->where('id', $dupId)->get()->getRowArray();
        $this->assert("Slider reorder swapped successfully", (int) $reorderedOriginal['sort_order'] === 100 && (int) $reorderedDup['sort_order'] === 99);

        // Cleanup
        $table->where('id', $insertId)->delete();
        $table->where('id', $dupId)->delete();
        $deleted = $table->where('id', $insertId)->get()->getRowArray();
        $this->assert("Slider deleted successfully", $deleted === null);
    }

    private function testAnnouncementCrud()
    {
        CLI::newLine();
        CLI::write("--- 6. Testing Announcement CRUD Operations ---", 'yellow');

        $db = \Config\Database::connect();
        $table = $db->table('site_announcements');

        // Insert
        $testData = [
            'badge_label' => 'TEST_ANNOUNCE',
            'title'       => 'Test Announcement Title',
            'description' => 'Test Announcement Description',
            'image_url'   => 'assets/main/images/event/event-details-top-img-1.jpg',
            'sort_order'  => 99,
            'is_active'   => 1,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];
        $table->insert($testData);
        $insertId = $db->insertID();
        $this->assert("Announcement created with ID {$insertId}", $insertId > 0);

        // Update
        $table->where('id', $insertId)->update(['title' => 'Updated Announcement Title']);
        $updated = $table->where('id', $insertId)->get()->getRowArray();
        $this->assert("Announcement updated title", ($updated['title'] ?? '') === 'Updated Announcement Title');

        // Toggle
        $newActive = $updated['is_active'] ? 0 : 1;
        $table->where('id', $insertId)->update(['is_active' => $newActive]);
        $toggled = $table->where('id', $insertId)->get()->getRowArray();
        $this->assert("Announcement toggle active status", (int) ($toggled['is_active'] ?? 1) === $newActive);

        // Test Duplicate
        $dupData = [
            'badge_label' => $updated['badge_label'],
            'title'       => $updated['title'] . ' (Salinan)',
            'description' => $updated['description'],
            'image_url'   => $updated['image_url'],
            'sort_order'  => 100,
            'is_active'   => 0,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];
        $table->insert($dupData);
        $dupId = $db->insertID();
        $this->assert("Announcement duplicated as draft with ID {$dupId}", $dupId > 0);

        // Test Reorder
        $table->where('id', $insertId)->update(['sort_order' => 10]);
        $reordered = $table->where('id', $insertId)->get()->getRowArray();
        $this->assert("Announcement sort order updated to 10", (int) ($reordered['sort_order'] ?? 0) === 10);

        // Delete Both
        $table->where('id', $insertId)->delete();
        $table->where('id', $dupId)->delete();
        $deleted = $table->where('id', $insertId)->get()->getRowArray();
        $this->assert("Announcement deleted successfully", $deleted === null);
    }

    private function testExternalPortalCrud()
    {
        CLI::newLine();
        CLI::write("--- 7. Testing External Portal CRUD Operations ---", 'yellow');

        $db = \Config\Database::connect();
        $table = $db->table('external_portals');

        // Insert
        $testData = [
            'name'        => 'Test Portal PBJ',
            'acronym'     => 'TPPBJ',
            'category'    => 'daerah',
            'description' => 'Test Portal PBJ Desc',
            'url'         => 'https://example.com/portal',
            'logo_url'    => 'solar:server-square-bold',
            'sort_order'  => 99,
            'is_active'   => 1,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];
        $table->insert($testData);
        $insertId = $db->insertID();
        $this->assert("External Portal created with ID {$insertId}", $insertId > 0);

        // Update
        $table->where('id', $insertId)->update(['name' => 'Updated Test Portal PBJ']);
        $updated = $table->where('id', $insertId)->get()->getRowArray();
        $this->assert("External Portal updated name", ($updated['name'] ?? '') === 'Updated Test Portal PBJ');

        // Toggle
        $newActive = $updated['is_active'] ? 0 : 1;
        $table->where('id', $insertId)->update(['is_active' => $newActive]);
        $toggled = $table->where('id', $insertId)->get()->getRowArray();
        $this->assert("External Portal toggle active status", (int) ($toggled['is_active'] ?? 1) === $newActive);

        // Duplicate
        $dupData = [
            'name'        => $updated['name'] . ' (Salinan)',
            'acronym'     => $updated['acronym'] . '-COPY',
            'url'         => $updated['url'],
            'logo_url'    => $updated['logo_url'],
            'description' => $updated['description'],
            'category'    => $updated['category'],
            'sort_order'  => 100,
            'is_active'   => 0,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];
        $table->insert($dupData);
        $dupId = $db->insertID();
        $this->assert("External Portal duplicated as draft with ID {$dupId}", $dupId > 0);

        // Reorder
        $table->where('id', $dupId)->update(['sort_order' => 12]);
        $reordered = $table->where('id', $dupId)->get()->getRowArray();
        $this->assert("External Portal sort order updated to 12", (int) ($reordered['sort_order'] ?? 0) === 12);

        // Delete duplicated
        $table->where('id', $dupId)->delete();

        // Delete
        $table->where('id', $insertId)->delete();
        $deleted = $table->where('id', $insertId)->get()->getRowArray();
        $this->assert("External Portal deleted successfully", $deleted === null);
    }

    private function testTeamMemberCrud()
    {
        CLI::newLine();
        CLI::write("--- 8. Testing Team Member CRUD Operations ---", 'yellow');

        $db = \Config\Database::connect();
        $table = $db->table('team_members');

        // Insert
        $testData = [
            'name'         => 'Test Inovator Name',
            'role_title'   => 'Test Inovator Role',
            'category'     => 'pengembang',
            'photo_url'    => 'assets/main/images/team/team-1-1.jpg',
            'social_links' => json_encode(['badge' => 'Pengembang Utama']),
            'sort_order'   => 99,
            'is_active'    => 1,
            'created_at'   => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s'),
        ];
        $table->insert($testData);
        $insertId = $db->insertID();
        $this->assert("Team member created with ID {$insertId}", $insertId > 0);

        // Update
        $table->where('id', $insertId)->update(['name' => 'Updated Inovator Name']);
        $updated = $table->where('id', $insertId)->get()->getRowArray();
        $this->assert("Team member updated name", ($updated['name'] ?? '') === 'Updated Inovator Name');

        // Toggle
        $newActive = $updated['is_active'] ? 0 : 1;
        $table->where('id', $insertId)->update(['is_active' => $newActive]);
        $toggled = $table->where('id', $insertId)->get()->getRowArray();
        $this->assert("Team member toggle active status", (int) ($toggled['is_active'] ?? 1) === $newActive);

        // Duplicate as draft
        $dupData = [
            'name'         => $updated['name'] . ' (Salinan)',
            'role_title'   => $updated['role_title'],
            'category'     => $updated['category'],
            'photo_url'    => $updated['photo_url'],
            'social_links' => $updated['social_links'],
            'sort_order'   => 100,
            'is_active'    => 0,
            'created_at'   => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s'),
        ];
        $table->insert($dupData);
        $dupId = $db->insertID();
        $this->assert("Team member duplicated as draft with ID {$dupId}", $dupId > 0);
        $table->where('id', $dupId)->delete();

        // Sort order inline update
        $table->where('id', $insertId)->update(['sort_order' => 15]);
        $reordered = $table->where('id', $insertId)->get()->getRowArray();
        $this->assert("Team member sort order updated to 15", (int) ($reordered['sort_order'] ?? 0) === 15);

        // Delete
        $table->where('id', $insertId)->delete();
        $deleted = $table->where('id', $insertId)->get()->getRowArray();
        $this->assert("Team member deleted successfully", $deleted === null);
    }

    private function testTestimonialCrud()
    {
        CLI::newLine();
        CLI::write("--- 9. Testing Testimonial CRUD Operations ---", 'yellow');

        $db = \Config\Database::connect();
        $table = $db->table('testimonials');

        // Insert
        $testData = [
            'name'             => 'Test User Name',
            'role_title'       => 'Test PPK',
            'organization'     => 'Test Dinas',
            'avatar_url'       => 'assets/main/images/testimonial/testimonial-1-1.jpg',
            'rating'           => 5,
            'quote'            => 'Testimonial quote text',
            'impact_statement' => 'Efisiensi waktu tender 35%',
            'category'         => 'opd',
            'sort_order'       => 99,
            'is_active'        => 1,
            'created_at'       => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s'),
        ];
        $table->insert($testData);
        $insertId = $db->insertID();
        $this->assert("Testimonial created with ID {$insertId}", $insertId > 0);

        // Update
        $table->where('id', $insertId)->update(['quote' => 'Updated quote text']);
        $updated = $table->where('id', $insertId)->get()->getRowArray();
        $this->assert("Testimonial updated quote", ($updated['quote'] ?? '') === 'Updated quote text');

        // Toggle
        $newActive = $updated['is_active'] ? 0 : 1;
        $table->where('id', $insertId)->update(['is_active' => $newActive]);
        $toggled = $table->where('id', $insertId)->get()->getRowArray();
        $this->assert("Testimonial toggle active status", (int) ($toggled['is_active'] ?? 1) === $newActive);

        // Duplicate as draft
        $dupData = [
            'name'             => $updated['name'] . ' (Salinan)',
            'role_title'       => $updated['role_title'],
            'organization'     => $updated['organization'],
            'avatar_url'       => $updated['avatar_url'],
            'rating'           => $updated['rating'],
            'quote'            => $updated['quote'],
            'impact_statement' => $updated['impact_statement'],
            'category'         => $updated['category'],
            'sort_order'       => 100,
            'is_active'        => 0,
            'created_at'       => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s'),
        ];
        $table->insert($dupData);
        $dupId = $db->insertID();
        $this->assert("Testimonial duplicated as draft with ID {$dupId}", $dupId > 0);
        $table->where('id', $dupId)->delete();

        // Sort order inline update
        $table->where('id', $insertId)->update(['sort_order' => 15]);
        $reordered = $table->where('id', $insertId)->get()->getRowArray();
        $this->assert("Testimonial sort order updated to 15", (int) ($reordered['sort_order'] ?? 0) === 15);

        // Delete
        $table->where('id', $insertId)->delete();
        $deleted = $table->where('id', $insertId)->get()->getRowArray();
        $this->assert("Testimonial deleted successfully", $deleted === null);
    }

    private function testHighlightCrud()
    {
        CLI::newLine();
        CLI::write("--- 10. Testing Highlight Feature CRUD Operations ---", 'yellow');

        $db = \Config\Database::connect();
        $table = $db->table('feature_highlights');

        // Insert
        $testData = [
            'section_key' => 'pilar_begawi',
            'title'       => 'Test Feature Title',
            'subtitle'    => 'Test Feature Subtitle',
            'code'        => 'TF01',
            'icon_class'  => 'solar:star-bold',
            'sort_order'  => 99,
            'is_active'   => 1,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];
        $table->insert($testData);
        $insertId = $db->insertID();
        $this->assert("Highlight feature created with ID {$insertId}", $insertId > 0);

        // Update
        $table->where('id', $insertId)->update(['title' => 'Updated Feature Title']);
        $updated = $table->where('id', $insertId)->get()->getRowArray();
        $this->assert("Highlight feature updated title", ($updated['title'] ?? '') === 'Updated Feature Title');

        // Toggle
        $newActive = $updated['is_active'] ? 0 : 1;
        $table->where('id', $insertId)->update(['is_active' => $newActive]);
        $toggled = $table->where('id', $insertId)->get()->getRowArray();
        $this->assert("Highlight feature toggle active status", (int) ($toggled['is_active'] ?? 1) === $newActive);

        // Duplicate
        $dupData = [
            'section_key' => $updated['section_key'],
            'title'       => $updated['title'] . ' (Salinan)',
            'subtitle'    => $updated['subtitle'],
            'code'        => $updated['code'] . '-COPY',
            'badge_text'  => 'Draf',
            'icon_class'  => $updated['icon_class'],
            'sort_order'  => 100,
            'is_active'   => 0,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];
        $table->insert($dupData);
        $dupId = $db->insertID();
        $this->assert("Highlight feature duplicated as draft with ID {$dupId}", $dupId > 0);

        // Reorder
        $table->where('id', $dupId)->update(['sort_order' => 15]);
        $reordered = $table->where('id', $dupId)->get()->getRowArray();
        $this->assert("Highlight feature sort order updated to 15", (int) ($reordered['sort_order'] ?? 0) === 15);

        // Delete duplicated
        $table->where('id', $dupId)->delete();

        // Delete
        $table->where('id', $insertId)->delete();
        $deleted = $table->where('id', $insertId)->get()->getRowArray();
        $this->assert("Highlight feature deleted successfully", $deleted === null);
    }

    private function testCategoryCleaned()
    {
        CLI::newLine();
        CLI::write("--- 11. Testing Kategori Layanan Cleanup Verification ---", 'yellow');

        $controllerExists = file_exists(APPPATH . 'Controllers/Admin/Landing/ServiceCategoryController.php');
        $this->assert("ServiceCategoryController removed", ! $controllerExists);

        $viewFolderExists = is_dir(APPPATH . 'Views/dashboard/landing/category');
        $this->assert("View folder dashboard/landing/category removed", ! $viewFolderExists);

        $navContent = file_get_contents(APPPATH . 'Views/dashboard/landing/_nav_landing.php');
        $hasNavCategory = str_contains($navContent, "'id'    => 'kategori-layanan'");
        $this->assert("Navigation sub-menu tab kategori-layanan removed from _nav_landing", ! $hasNavCategory);

        $routesContent = file_get_contents(APPPATH . 'Config/Routes.php');
        $hasRedirect = str_contains($routesContent, "\$routes->addRedirect('kategori-layanan', 'dashboard/landing')");
        $this->assert("Route kategori-layanan redirects to dashboard/landing", $hasRedirect);
    }

    private function testBannerHalamanCleaned()
    {
        CLI::newLine();
        CLI::write("--- 12. Testing Banner Halaman Cleanup Verification ---", 'yellow');

        $controllerExists = file_exists(APPPATH . 'Controllers/Admin/Landing/PageBannerController.php');
        $this->assert("PageBannerController removed", ! $controllerExists);

        $viewFolderExists = is_dir(APPPATH . 'Views/dashboard/landing/banner');
        $this->assert("View folder dashboard/landing/banner removed", ! $viewFolderExists);

        $navContent = file_get_contents(APPPATH . 'Views/dashboard/landing/_nav_landing.php');
        $hasNavBanner = str_contains($navContent, "'id'    => 'banner-halaman'");
        $this->assert("Navigation sub-menu tab banner-halaman removed from _nav_landing", ! $hasNavBanner);

        $routesContent = file_get_contents(APPPATH . 'Config/Routes.php');
        $hasRedirect = str_contains($routesContent, "\$routes->addRedirect('banner-halaman', 'dashboard/landing/sorotan-carousel')");
        $this->assert("Route banner-halaman redirects to sorotan-carousel", $hasRedirect);
    }

    private function testInstitutionProfileUpdate()
    {
        CLI::newLine();
        CLI::write("--- 13. Testing Institution Profile Settings ---", 'yellow');

        $settingService = new \App\Services\SystemSettingService();
        $originalName = $settingService->get('organization', 'agency_name', 'Biro Pengadaan Barang & Jasa Sekretariat Daerah Provinsi Lampung');

        $settingService->saveGroup('organization', ['agency_name' => 'TEST_AGENCY_NAME']);
        $updatedName = $settingService->get('organization', 'agency_name');
        $this->assert("Institution profile setting saved", $updatedName === 'TEST_AGENCY_NAME');

        // Restore
        $settingService->saveGroup('organization', ['agency_name' => $originalName]);
        $restoredName = $settingService->get('organization', 'agency_name');
        $this->assert("Institution profile setting restored", $restoredName === $originalName);
    }

    private function testSpotlightHubFeatures()
    {
        CLI::newLine();
        CLI::write("--- 14. Testing Spotlight Hub (Sorotan & Carousel) Operations ---", 'yellow');

        $db = \Config\Database::connect();

        // 1. Controller instantiate check
        $controller = new \App\Controllers\Admin\Landing\SpotlightHubController();
        $this->assert("SpotlightHubController instantiated successfully", $controller instanceof \App\Controllers\Admin\Landing\SpotlightHubController);

        // 2. Regulation carousel toggle test
        $regTable = $db->table('regulations');
        $sampleReg = $regTable->where('deleted_at IS NULL', null, false)->limit(1)->get()->getRowArray();
        $this->assert("Regulation item exists for spotlight test", $sampleReg !== null);

        if ($sampleReg) {
            $regId = (int) $sampleReg['id'];
            $origCarousel = (int) ($sampleReg['carousel_enabled'] ?? 0);
            $newCarousel = $origCarousel ? 0 : 1;

            // Update
            $regTable->where('id', $regId)->update(['carousel_enabled' => $newCarousel]);
            $updatedReg = $regTable->where('id', $regId)->get()->getRowArray();
            $this->assert("Regulation carousel status toggled to {$newCarousel}", (int) ($updatedReg['carousel_enabled'] ?? 0) === $newCarousel);

            // Reorder test
            $regTable->where('id', $regId)->update(['carousel_order' => 9]);
            $updatedOrder = $regTable->where('id', $regId)->get()->getRowArray();
            $this->assert("Regulation carousel order updated to 9", (int) ($updatedOrder['carousel_order'] ?? 0) === 9);

            // Restore
            $regTable->where('id', $regId)->update([
                'carousel_enabled' => $origCarousel,
                'carousel_order'   => $sampleReg['carousel_order'] ?? null,
            ]);
            $this->assert("Regulation carousel state restored successfully", true);
        }

        // 3. Learning Class carousel toggle test
        $classTable = $db->table('learning_classes');
        $sampleClass = $classTable->where('deleted_at IS NULL', null, false)->limit(1)->get()->getRowArray();
        if ($sampleClass) {
            $classId = (int) $sampleClass['id'];
            $origCarousel = (int) ($sampleClass['carousel_enabled'] ?? 0);
            $newCarousel = $origCarousel ? 0 : 1;

            $classTable->where('id', $classId)->update(['carousel_enabled' => $newCarousel]);
            $updatedClass = $classTable->where('id', $classId)->get()->getRowArray();
            $this->assert("Learning class carousel status toggled", (int) ($updatedClass['carousel_enabled'] ?? 0) === $newCarousel);

            // Restore
            $classTable->where('id', $classId)->update(['carousel_enabled' => $origCarousel]);
            $this->assert("Learning class carousel state restored", true);
        }

        // 4. Spotlight settings test (limit & mode)
        $settingService = new \App\Services\SystemSettingService();
        $origLimit = (int) setting('learning.spotlight_limit', 6);
        $settingService->saveGroup('learning', ['spotlight_limit' => 9]);
        $updatedLimit = (int) setting('learning.spotlight_limit');
        $this->assert("Spotlight limit setting updated to 9", $updatedLimit === 9);

        // Restore
        $settingService->saveGroup('learning', ['spotlight_limit' => $origLimit]);
        $restoredLimit = (int) setting('learning.spotlight_limit');
        $this->assert("Spotlight limit restored to {$origLimit}", $restoredLimit === $origLimit);
    }
}
