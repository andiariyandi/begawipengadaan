<?php

namespace App\Commands;

use App\Database\Seeds\PermissionCatalogSeeder;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class PermissionSync extends BaseCommand
{
    protected $group       = 'Security';
    protected $name        = 'permission:sync';
    protected $description = 'Synchronize system permissions catalogue and auto-grant permissions to canonical roles';

    public function run(array $params): int
    {
        CLI::write("==================================================================", "yellow");
        CLI::write("     BEGAWIPENGADAAN MODULAR PERMISSION CATALOG SYNC ENGINE       ", "yellow");
        CLI::write("==================================================================", "yellow");

        $db = \Config\Database::connect();
        $seeder = new PermissionCatalogSeeder(new \Config\Database());

        $catalog = $seeder->getCatalog();
        CLI::write("Synchronizing " . count($catalog) . " permissions across 12 module domains...", "cyan");

        $seeder->run();

        $totalPerms = $db->table('permissions')->countAllResults();
        $totalGrants = $db->table('role_permissions')->countAllResults();

        CLI::write("Synchronization complete!", "green");
        CLI::write(" - Total permissions in database: " . $totalPerms, "white");
        CLI::write(" - Total role-permission grants : " . $totalGrants, "white");

        // Breakdown per domain
        $domains = $db->table('permissions')
            ->select('domain, COUNT(id) as total')
            ->groupBy('domain')
            ->get()
            ->getResultArray();

        CLI::write("\nPermissions breakdown per domain:", "cyan");
        foreach ($domains as $d) {
            CLI::write("   * " . str_pad($d['domain'], 16) . ": " . $d['total'] . " permissions");
        }

        CLI::write("\nCanonical roles permissions count:", "cyan");
        $roles = $db->table('roles')->orderBy('id', 'ASC')->get()->getResultArray();
        foreach ($roles as $r) {
            $grants = $db->table('role_permissions')->where('role_id', $r['id'])->countAllResults();
            CLI::write("   * [ID: {$r['id']}] " . str_pad($r['key'], 20) . " ({$r['name']}): " . $grants . " permissions");
        }

        CLI::write("==================================================================", "yellow");
        return 0;
    }
}
