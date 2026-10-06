<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class CompatibilityFoundationTest extends CIUnitTestCase
{
    public function testLegacyAuthRouteAliasesRemainStable(): void
    {
        $this->assertSame('/dashboard/masuk', route_to('dashboard.auth.login'));
        $this->assertSame('/dashboard/masuk', route_to('dashboard.auth.login.attempt'));
        $this->assertSame('/dashboard/keluar', route_to('dashboard.auth.logout'));
    }

    public function testTargetLoginControllerExistsWithoutMovingLegacyControllers(): void
    {
        $this->assertTrue(class_exists(\App\Controllers\Dashboard\Auth\LoginController::class));
        $this->assertFileExists(APPPATH . 'Controllers/Admin/AdminAuthController.php');
    }

    public function testAccessCompatibilityRouteRemainsAvailable(): void
    {
        $this->assertSame('/dashboard/akses/pengguna', route_to('dashboard.access.users'));
        $this->assertSame('/dashboard/akses/role', route_to('dashboard.access.roles'));
    }
}
