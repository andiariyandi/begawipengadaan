<?php
/**
 * QA Test: Admin Manajemen Akses workflows.
 * Tests: Role Matrix sync, Procurement Assignment, Profile Approval,
 *        Role Request Approval, Profile Change Approval.
 */

define('FCPATH', __DIR__ . '/public/');
chdir(__DIR__);
require __DIR__ . '/vendor/autoload.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/Boot.php';

$_SERVER['argv'] = ['test'];
$_SERVER['argc'] = 1;
CodeIgniter\Boot::bootSpark($paths);

$db = db_connect();

function ok(string $label, bool $cond, string $note = ''): void {
    global $passed, $failed;
    echo ($cond ? "[PASS] " : "[FAIL] ") . $label . ($note ? " — $note" : '') . PHP_EOL;
    $cond ? $passed++ : $failed++;
}

function cleanupByUser(int $uid): void {
    global $db;
    if ($uid <= 0) return;
    $db->table('audit_logs')->where('actor_id', $uid)->orWhere('resource_id', $uid)->delete();
    $db->table('workflow_transitions')->whereIn('resource_type', ['user','profile_verification','role_request','profile_change_request'])->groupStart()->where('resource_id', $uid)->groupEnd()->delete();
    $db->table('profile_verifications')->where('user_id', $uid)->delete();
    $db->table('profile_change_requests')->where('user_id', $uid)->delete();
    $db->table('role_requests')->where('user_id', $uid)->delete();
    foreach (['asn_profiles','provider_profiles','academic_profiles','public_profiles'] as $t) {
        $db->table($t)->where('user_id', $uid)->delete();
    }
    $db->table('user_profiles')->where('user_id', $uid)->delete();
    $db->table('user_roles')->where('user_id', $uid)->delete();
    $db->table('users')->where('id', $uid)->delete();
}

function createTestUser(string $email, string $username, string $profileType = 'asn', string $accountStatus = 'pending_verification'): int {
    global $db;
    $now = date('Y-m-d H:i:s');
    $db->table('users')->where('email', $email)->delete();
    $ok = $db->table('users')->insert([
        'name' => 'Test ' . $username,
        'username' => $username,
        'email' => $email,
        'phone' => '081',
        'password_hash' => password_hash('Test@12345Ab', PASSWORD_DEFAULT),
        'profile_type' => $profileType,
        'account_status' => $accountStatus,
        'created_at' => $now,
        'updated_at' => $now,
    ]);
    if (! $ok) {
        throw new \RuntimeException('createTestUser failed: ' . print_r($db->error(), true));
    }
    $uid = (int) $db->insertID();
    if ($uid <= 0) {
        throw new \RuntimeException('createTestUser insertID=0');
    }
    return $uid;
}

$passed = 0;
$failed = 0;
$actorId = 13; // superadmin (superadmin@begawipbj.go.id)

// ================== TEST 1: Role Matrix Sync ==================
echo "\n=== TEST 1: Role Matrix Sync ===\n";
$roleMgmt = new \App\Services\Admin\RoleManagementService();
$roles = $roleMgmt->listRoles();
$perms = $roleMgmt->listPermissions();
ok('Roles loaded', count($roles) > 0, count($roles) . ' roles');
ok('Permissions loaded', count($perms) > 0, count($perms) . ' permissions');

// Pick operator role and 2 permissions
$opRole = null;
foreach ($roles as $r) if ($r['key'] === 'operator') $opRole = $r;
ok('Operator role found', $opRole !== null);

$pickPerms = array_slice(array_column($perms, 'id'), 0, 2);
$roleMgmt->syncRolePermissions((int) $opRole['id'], $pickPerms, $actorId);

// Verify matrix
$matrix = db_connect()->table('role_permissions')->where('role_id', (int) $opRole['id'])->get()->getResultArray();
ok('Matrix sync applied', count($matrix) === 2, 'role-permissions rows: ' . count($matrix));

// Audit row
$auditRow = $db->table('audit_logs')->where('action', 'role.permission.sync')->where('resource_id', (int) $opRole['id'])->orderBy('created_at', 'DESC')->get()->getRowArray();
ok('Audit log written', $auditRow !== null);

// ================== TEST 2: Procurement Assignment ==================
echo "\n=== TEST 2: Procurement Assignment ===\n";
$email = 'qa.proc.' . uniqid() . '@x.com';
$uid = createTestUser($email, 'qaproc' . uniqid(), 'public');

$procSvc = new \App\Services\Admin\ProcurementRoleService();
$roleId = 0;
$ok = $db->table('procurement_roles')->insert([
    'key' => 'PA_TEST_' . uniqid(),
    'name' => 'Pengguna Anggaran (Test)',
    'description' => 'Test role',
    'is_active' => 1,
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s'),
], true);
if ($ok) {
    $roleId = (int) $db->insertID();
}
ok('Procurement role created', $roleId > 0);

$assignmentId = $procSvc->assign($uid, $roleId, null, $actorId);
ok('Assignment created', $assignmentId > 0);

$okAssign = $procSvc->verify($assignmentId, 'verified', $actorId);
ok('Verification applied', $okAssign, $okAssign ? '' : 'service returned false');

$final = $db->table('user_procurement_roles')->where('id', $assignmentId)->get()->getRowArray();
ok('verification_status = verified', ($final['verification_status'] ?? null) === 'verified');

$auditAssign = $db->table('audit_logs')->where('action', 'procurement_role.verify')->where('resource_id', $assignmentId)->get()->getRowArray();
ok('Audit log written for verify', $auditAssign !== null);

$db->table('audit_logs')->where('resource_id', $assignmentId)->delete();
$db->table('user_procurement_roles')->where('id', $assignmentId)->delete();
cleanupByUser($uid);
$db->table('procurement_roles')->where('id', $roleId)->delete();

// ================== TEST 3: Profile Verification Approval ==================
echo "\n=== TEST 3: Profile Verification Approval ===\n";
$email = 'qa.profile.' . uniqid() . '@x.com';
$uid = createTestUser($email, 'qaprof' . uniqid(), 'asn', 'pending_verification');

$db->table('user_profiles')->insert([
    'user_id' => $uid,
    'address_line' => 'Jl Test',
    'province_code' => '18',
    'regency_code' => '18.71',
    'district_code' => '18.71.01',
    'village_code' => null,
    'profile_completion_status' => 'draft',
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s'),
]);
$db->table('asn_profiles')->insert([
    'user_id' => $uid,
    'employee_identifier' => '199001012020011001',
    'employment_status' => 'pns',
    'origin_type' => 'pemprov_lampung',
    'institution_name' => 'Test',
    'unit_name' => 'Test Unit',
    'position_name' => 'Analis',
    'rank_grade' => 'III/c',
    'official_email' => 'test@l.go.id',
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s'),
]);
$verificationId = 0;
$ok = $db->table('profile_verifications')->insert([
    'user_id' => $uid,
    'verification_type' => 'profile_registration',
    'status' => 'pending',
    'requested_by' => $uid,
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s'),
], true);
if ($ok) {
    $verificationId = (int) $db->insertID();
}
ok('Profile verification pending created', $verificationId > 0);

$profSvc = new \App\Services\Admin\ProfileVerificationService();
$approveOk = $profSvc->approve($verificationId, $actorId, 'Test approval');
ok('Profile verification approved', $approveOk);

$userAfter = $db->table('users')->where('id', $uid)->get()->getRowArray();
ok('users.account_status = active', $userAfter['account_status'] === 'active');

$verAfter = $db->table('profile_verifications')->where('id', $verificationId)->get()->getRowArray();
ok('profile_verifications.status = approved', $verAfter['status'] === 'approved');

$wfAfter = $db->table('workflow_transitions')->where('resource_type', 'profile_verification')->where('resource_id', $verificationId)->get()->getRowArray();
ok('Workflow transition recorded', $wfAfter !== null);

$auditApprove = $db->table('audit_logs')->where('action', 'profile_verification.approve')->where('resource_id', $verificationId)->get()->getRowArray();
ok('Audit log written', $auditApprove !== null);

// Test reject flow on different verification
$verificationId2 = 0;
$ok = $db->table('profile_verifications')->insert([
    'user_id' => $uid,
    'verification_type' => 'profile_registration',
    'status' => 'pending',
    'requested_by' => $uid,
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s'),
], true);
if ($ok) {
    $verificationId2 = (int) $db->insertID();
}
try {
    $profSvc->reject($verificationId2, 'Test rejection', $actorId, null);
    ok('Profile reject with reason', true);
} catch (\Throwable $e) {
    ok('Profile reject with reason', false, $e->getMessage());
}

cleanupByUser($uid);

// ================== TEST 4: Role Request Approval ==================
echo "\n=== TEST 4: Role Request Approval ===\n";
$email = 'qa.role.' . uniqid() . '@x.com';
$uid = createTestUser($email, 'qarole' . uniqid(), 'asn', 'active');

$trainerRole = $db->table('roles')->where('key', 'trainer')->get()->getRowArray();
$requestId = 0;
$ok = $db->table('role_requests')->insert([
    'user_id' => $uid,
    'requested_role_id' => (int) $trainerRole['id'],
    'status' => 'submitted',
    'reason' => 'Test permohonan trainer',
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s'),
], true);
if ($ok) {
    $requestId = (int) $db->insertID();
}
ok('Role request created', $requestId > 0);

$roleReqSvc = new \App\Services\Admin\RoleRequestService();
$approveRoleOk = $roleReqSvc->approve($requestId, $actorId, 'OK');
ok('Role request approved', $approveRoleOk);

$reqAfter = $db->table('role_requests')->where('id', $requestId)->get()->getRowArray();
ok('role_requests.status = approved', $reqAfter['status'] === 'approved');

$userRoleAfter = $db->table('user_roles')->where('user_id', $uid)->where('role_id', (int) $trainerRole['id'])->where('status', 'active')->get()->getRowArray();
ok('user_roles row created (trainer granted)', $userRoleAfter !== null);

$auditRoleReq = $db->table('audit_logs')->where('action', 'role_request.approve')->where('resource_id', $requestId)->get()->getRowArray();
ok('Audit log written', $auditRoleReq !== null);

cleanupByUser($uid);

// ================== TEST 5: Profile Change Approval ==================
echo "\n=== TEST 5: Profile Change Approval ===\n";
$email = 'qa.change.' . uniqid() . '@x.com';
$uid = createTestUser($email, 'qachange' . uniqid(), 'public', 'active');
$db->table('user_profiles')->insert([
    'user_id' => $uid,
    'address_line' => 'Old address',
    'province_code' => '18',
    'regency_code' => '18.71',
    'district_code' => '18.71.01',
    'village_code' => null,
    'profile_completion_status' => 'complete',
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s'),
]);
$db->table('public_profiles')->insert([
    'user_id' => $uid,
    'occupation_status' => 'mahasiswa',
    'organization_name' => 'Old Univ',
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s'),
]);

$payload = ['organization_name' => 'New University', 'occupation_status' => 'pegawai_swasta'];
$changeId = 0;
$ok = $db->table('profile_change_requests')->insert([
    'user_id' => $uid,
    'status' => 'submitted',
    'requested_payload' => json_encode($payload, JSON_THROW_ON_ERROR),
    'reason' => 'Update data pribadi',
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s'),
], true);
if ($ok) {
    $changeId = (int) $db->insertID();
}
ok('Profile change request created', $changeId > 0);

$changeSvc = new \App\Services\Admin\ProfileChangeRequestService();
$approveChangeOk = $changeSvc->approve($changeId, $actorId, 'OK');
ok('Profile change approved & applied', $approveChangeOk);

$changed = $db->table('public_profiles')->where('user_id', $uid)->get()->getRowArray();
ok('public_profiles.organization_name updated to "New University"',
    ($changed['organization_name'] ?? null) === 'New University');
ok('public_profiles.occupation_status updated',
    ($changed['occupation_status'] ?? null) === 'pegawai_swasta');

$changeAfter = $db->table('profile_change_requests')->where('id', $changeId)->get()->getRowArray();
ok('profile_change_requests.status = approved', $changeAfter['status'] === 'approved');
ok('applied_at timestamp set', ! empty($changeAfter['applied_at']));

$wfChange = $db->table('workflow_transitions')->where('resource_type', 'profile_change_request')->where('resource_id', $changeId)->get()->getRowArray();
ok('Workflow transition recorded', $wfChange !== null);

$auditChange = $db->table('audit_logs')->where('action', 'profile_change.approve')->where('resource_id', $changeId)->get()->getRowArray();
ok('Audit log written', $auditChange !== null);

cleanupByUser($uid);

// Summary
echo "\n=== TOTAL: PASS=$passed, FAIL=$failed ===\n";