<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class DatabaseCleanAudit extends BaseCommand
{
    protected $group       = 'Maintenance';
    protected $name        = 'db:clean:audit';
    protected $description = 'Inspects tables and users to prepare for clean database wipe for release v.1';

    public function run(array $params): int
    {
        $db = \Config\Database::connect();

        CLI::write("=== DATABASE TABLES & ROW COUNTS ===", "cyan");
        $tables = $db->listTables();
        foreach ($tables as $t) {
            $cnt = $db->table($t)->countAllResults();
            CLI::write(sprintf("%-35s: %d rows", $t, $cnt));
        }

        CLI::newLine();
        CLI::write("=== ROLES & ADMIN / SUPERADMIN ACCOUNTS ===", "cyan");
        $roles = $db->table('roles')->get()->getResultArray();
        foreach ($roles as $r) {
            CLI::write("Role: ID={$r['id']}, Name={$r['name']}, Key={$r['key']}");
        }

        CLI::newLine();
        CLI::write("=== USERS WITH ADMIN / SUPERADMIN ROLES ===", "yellow");
        $adminRoles = ['super_administrator', 'administrator'];
        $admins = $db->table('users u')
            ->select('u.id, u.username, u.name, u.email, u.account_status, r.name AS role_name, r.key AS role_key')
            ->join('user_roles ur', 'ur.user_id = u.id', 'left')
            ->join('roles r', 'r.id = ur.role_id', 'left')
            ->whereIn('r.key', $adminRoles)
            ->get()->getResultArray();

        $u13 = $db->table('users')->where('id', 13)->get()->getRowArray();
        $u14 = $db->table('users')->where('id', 14)->get()->getRowArray();
        CLI::write("User 13: " . json_encode($u13));
        CLI::write("User 14: " . json_encode($u14));
        CLI::write("Roles 13: " . json_encode($db->table('user_roles')->where('user_id', 13)->get()->getResultArray()));
        CLI::write("Roles 14: " . json_encode($db->table('user_roles')->where('user_id', 14)->get()->getResultArray()));
        CLI::write("Profile 13: " . json_encode($db->table('user_profiles')->where('user_id', 13)->get()->getRowArray()));
        CLI::write("Profile 14: " . json_encode($db->table('user_profiles')->where('user_id', 14)->get()->getRowArray()));
        CLI::write("ASN 13: " . json_encode($db->table('asn_profiles')->where('user_id', 13)->get()->getRowArray()));
        CLI::write("ASN 14: " . json_encode($db->table('asn_profiles')->where('user_id', 14)->get()->getRowArray()));

        CLI::newLine();
        CLI::write("=== CATEGORIES & CMS CHECK ===", "cyan");
        CLI::write("--- Knowledge Categories ---");
        foreach ($db->table('knowledge_categories')->get()->getResultArray() as $kc) {
            CLI::write("  ID={$kc['id']} | Name={$kc['name']} | Slug={$kc['slug']}");
        }
        CLI::write("--- Knowledge Article Categories (Pivot) ---");
        CLI::write("  Count: " . $db->table('knowledge_article_categories')->countAllResults());
        CLI::write("--- News Categories ---");
        foreach ($db->table('news_categories')->get()->getResultArray() as $nc) {
            CLI::write("  ID={$nc['id']} | Name={$nc['name']} | Slug={$nc['slug']}");
        }
        $faTypes = $db->table('file_assets')->select('resource_type, COUNT(*) as cnt')->groupBy('resource_type')->get()->getResultArray();
        foreach ($faTypes as $fat) {
            CLI::write("  Resource Type: {$fat['resource_type']} => {$fat['cnt']} assets");
        }

        return 0;
    }
}
