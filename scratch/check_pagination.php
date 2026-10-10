<?php
define('FCPATH', realpath(__DIR__ . '/../public') . DIRECTORY_SEPARATOR);
require_once __DIR__ . '/../app/Config/Paths.php';
$paths = new Config\Paths();
require_once $paths->systemDirectory . '/Boot.php';
defined('ENVIRONMENT') || define('ENVIRONMENT', 'development');
CodeIgniter\Boot::bootConsole($paths);

$db = db_connect();
$builder = $db->table('users u')
    ->select('u.id, u.username, u.email, u.name, u.phone, u.profile_type, u.account_status, u.created_at,
              GROUP_CONCAT(r.name ORDER BY r.name SEPARATOR ", ") AS roles,
              ou.name AS organization_name')
    ->join('user_roles ur', 'ur.user_id = u.id AND ur.status = "active"', 'left')
    ->join('roles r', 'r.id = ur.role_id', 'left')
    ->join('user_profiles up', 'up.user_id = u.id', 'left')
    ->join('organization_units ou', 'ou.id = up.province_code OR ou.id = up.regency_code', 'left', false)
    ->groupBy('u.id, ou.id, r.id, ur.id')
    ->orderBy('u.created_at', 'DESC');

try {
    $c = (clone $builder)->countAllResults();
    echo "Count: " . var_export($c, true) . "\n";
} catch (\Throwable $e) {
    echo "Error counting: " . $e->getMessage() . "\n";
}

$_GET['keyword'] = 'budi';
$_GET['role_id'] = '3';
$pager = service('pager');
$pager->store('users', 1, 15, 100, 0);

function preservePagerUri(string $uri, array $currentGet = []): string {
    $parsed = parse_url($uri);
    $query = [];
    if (!empty($parsed['query'])) {
        parse_str($parsed['query'], $query);
    }
    $merged = array_merge($currentGet, $query);
    return ($parsed['path'] ?? '') . (!empty($merged) ? '?' . http_build_query($merged) : '');
}

echo "Rendered Pager HTML:\n" . $pager->links('users', 'default_full') . "\n";

