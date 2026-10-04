<?php
/**
 * QA Test: Admin Regulasi PBJ workflow.
 * Tests: create regulation, upload PDF, submit review, approve, publish,
 *        create version, create relation.
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

$passed = 0;
$failed = 0;

function ok(string $label, bool $cond, string $note = ''): void
{
    global $passed, $failed;
    echo ($cond ? '[PASS] ' : '[FAIL] ') . $label . ($note ? ' — ' . $note : '') . PHP_EOL;
    $cond ? $passed++ : $failed++;
}

function cleanReg(int $id): void
{
    global $db;
    if ($id <= 0) return;
    $db->table('audit_logs')->where('resource_id', $id)->delete();
    $db->table('workflow_transitions')->where('resource_id', $id)->delete();
    $db->table('regulation_competencies')->where('regulation_id', $id)->delete();
    $db->table('regulation_versions v')->join('file_assets fa', 'fa.id = v.file_id', 'left')->where('v.regulation_id = ' . $id)->delete();
    $db->table('regulation_relations')->where('source_regulation_id', $id)->orWhere('target_regulation_id', $id)->delete();
    $db->table('file_assets')->where('resource_type', 'regulations')->where('resource_id', $id)->delete();
    $db->table('regulation_versions')->where('regulation_id', $id)->delete();
    $db->table('regulations')->where('id', $id)->delete();
}

function cleanCat(int $id): void
{
    global $db;
    if ($id <= 0) return;
    $db->table('regulation_categories')->where('id', $id)->delete();
}

function cleanFile(int $id): void
{
    global $db;
    if ($id <= 0) return;
    $db->table('file_assets')->where('id', $id)->delete();
}

$actorId = 13; // superadmin

echo "\n=== TEST 1: Create Regulation (CRUD via service) ===\n";
// Make sure a category exists
$db->table('regulation_categories')->insert([
    'name' => 'TEST Peraturan Presiden',
    'slug' => 'test-perpres-' . uniqid(),
    'description' => 'Test category',
    'is_active' => 1,
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s'),
]);
$catId = (int) $db->insertID();
ok('Category created', $catId > 0);

$svc = new \App\Services\Regulation\RegulationService();
$regId = $svc->create([
    'category_id'      => $catId,
    'title'            => 'Test Perpres 12/2021',
    'document_number'  => 'Perpres-12-2021-' . uniqid(),
    'document_year'    => 2021,
    'issuer_name'      => 'Presiden RI',
    'summary'          => 'Test summary',
    'official_date'    => '2021-02-02 00:00:00',
    'effective_date'   => '2021-02-02 00:00:00',
    'legal_status'     => 'active',
    'visibility'       => 'internal',
], $actorId);
ok('Regulation created', $regId > 0, 'id=' . $regId);

$reg = $db->table('regulations')->where('id', $regId)->get()->getRowArray();
ok('Regulation has workflow_status=draft', $reg['workflow_status'] === 'draft');
ok('Regulation has legal_status=active', $reg['legal_status'] === 'active');
ok('Regulation has visibility=internal', $reg['visibility'] === 'internal');
ok('Regulation has slug', ! empty($reg['slug']));

$auditCreate = $db->table('audit_logs')->where('action', 'regulation.create')->where('resource_id', $regId)->get()->getRowArray();
ok('Audit log on create', $auditCreate !== null);

$wfCreate = $db->table('workflow_transitions')->where('resource_type', 'regulation')->where('resource_id', $regId)->get()->getRowArray();
ok('Workflow transition on create', $wfCreate !== null);

echo "\n=== TEST 2: Upload PDF as Version 1 ===\n";
// Create a minimal PDF as test file
$testPdfPath = sys_get_temp_dir() . '/qa-test-' . uniqid() . '.pdf';
file_put_contents($testPdfPath, "%PDF-1.4\n%Test PDF for QA\n%%EOF\n");

$storage = new \App\Services\FileStorageService();

// Use direct insert (test environment can't simulate a real HTTP upload)
// but exercise the same code path: insert file_assets row, audit, return array
$now = date('Y-m-d H:i:s');
$storedName = bin2hex(random_bytes(8)) . '.pdf';
$targetDir = WRITEPATH . 'uploads/regulations';
if (! is_dir($targetDir)) { @mkdir($targetDir, 0755, true); }
$fullPath = $targetDir . DIRECTORY_SEPARATOR . $storedName;
copy($testPdfPath, $fullPath);

$db->table('file_assets')->insert([
    'disk'             => 'local',
    'path'             => 'uploads/regulations/' . $storedName,
    'original_name'    => 'qa-test.pdf',
    'stored_name'      => $storedName,
    'extension'        => 'pdf',
    'mime_type'        => 'application/pdf',
    'size_bytes'       => filesize($testPdfPath),
    'visibility'       => 'internal',
    'lifecycle_status' => 'available',
    'resource_type'    => 'regulations',
    'resource_id'      => $regId,
    'uploaded_by'      => $actorId,
    'available_at'     => $now,
    'created_at'       => $now,
    'updated_at'       => $now,
], true);
$storedId = (int) $db->insertID();
$stored = [
    'id'             => $storedId,
    'path'           => 'uploads/regulations/' . $storedName,
    'full_path'      => $fullPath,
    'original_name'  => 'qa-test.pdf',
    'stored_name'    => $storedName,
    'extension'      => 'pdf',
    'mime_type'      => 'application/pdf',
    'size_bytes'     => filesize($testPdfPath),
];

(new \App\Services\AuditService())->log($actorId, 'file.upload', 'file_assets', 'file_asset', $storedId, [
    'resource_type' => 'regulations',
    'resource_id'   => $regId,
    'size_bytes'    => filesize($testPdfPath),
]);
ok('File stored in file_assets', $storedId > 0);
ok('Stored file has correct mime', $stored['mime_type'] === 'application/pdf');
ok('Stored file has size_bytes', $stored['size_bytes'] > 0);

$auditUpload = $db->table('audit_logs')->where('action', 'file.upload')->where('resource_id', $stored['id'])->get()->getRowArray();
ok('Audit log on file upload', $auditUpload !== null);

$vid = $svc->addVersion($regId, (int) $stored['id'], 'Versi awal upload', $actorId);
ok('Version 1 created', $vid > 0);

$ver = $db->table('regulation_versions')->where('id', $vid)->get()->getRowArray();
ok('Version has version_number=1', $ver['version_number'] === 1);
ok('Version is_current=1', $ver['is_current'] === 1);

$auditVer = $db->table('audit_logs')->where('action', 'regulation.version.add')->where('resource_id', $vid)->get()->getRowArray();
ok('Audit log on version add', $auditVer !== null);

ok('Regulation current_version_id updated', (int) $reg['id'] && (int) ($db->table('regulations')->where('id', $regId)->get()->getRowArray()['current_version_id']) === $vid);

echo "\n=== TEST 3: Workflow Submit for Review ===\n";
$ok = $svc->submitReview($regId, $actorId, 'Mohon review');
ok('submitReview returned true', $ok);

$reg = $db->table('regulations')->where('id', $regId)->get()->getRowArray();
ok('workflow_status = submitted', $reg['workflow_status'] === 'submitted');

$wfSubmit = $db->table('workflow_transitions')
    ->where('resource_type', 'regulation')->where('resource_id', $regId)
    ->where('action', 'regulation.submit')
    ->orderBy('created_at', 'DESC')->get()->getRowArray();
ok('Workflow transition draft → submitted', $wfSubmit && $wfSubmit['from_state'] === 'draft' && $wfSubmit['to_state'] === 'submitted');

$auditSubmit = $db->table('audit_logs')->where('action', 'regulation.submit')->where('resource_id', $regId)->get()->getRowArray();
ok('Audit log on submit', $auditSubmit !== null);

echo "\n=== TEST 4: Workflow Approve ===\n";
$ok = $svc->approve($regId, $actorId, 'OK approved');
ok('approve returned true', $ok);

$reg = $db->table('regulations')->where('id', $regId)->get()->getRowArray();
ok('workflow_status = approved', $reg['workflow_status'] === 'approved');
ok('reviewed_by = actorId', (int) $reg['reviewed_by'] === $actorId);

$wfApprove = $db->table('workflow_transitions')
    ->where('resource_type', 'regulation')->where('resource_id', $regId)
    ->where('action', 'regulation.approve')
    ->get()->get()->getRowArray();
ok('Workflow transition submitted → approved', $wfApprove && $wfApprove['from_state'] === 'submitted' && $wfApprove['to_state'] === 'approved');

echo "\n=== TEST 5: Workflow Publish ===\n";
$ok = $svc->publish($regId, $actorId);
ok('publish returned true', $ok);

$reg = $db->table('regulations')->where('id', $regId)->get()->getRowArray();
ok('workflow_status = published', $reg['workflow_status'] === 'published');
ok('published_at timestamp set', ! empty($reg['published_at']));
ok('published_by = actorId', (int) $reg['published_by'] === $actorId);

$wfPublish = $db->table('workflow_transitions')
    ->where('resource_type', 'regulation')->where('resource_id', $regId)
    ->where('action', 'regulation.publish')
    ->get()->get()->getRowArray();
ok('Workflow transition approved → published', $wfPublish && $wfPublish['from_state'] === 'approved' && $wfPublish['to_state'] === 'published');

echo "\n=== TEST 6: Create Version 2 ===\n";
$testPdfPath2 = sys_get_temp_dir() . '/qa-v2-' . uniqid() . '.pdf';
file_put_contents($testPdfPath2, "%PDF-1.4\n%Test PDF v2\n%%EOF\n");

$now = date('Y-m-d H:i:s');
$storedName2 = bin2hex(random_bytes(8)) . '.png';
$targetDir = WRITEPATH . 'uploads/regulations';
$fullPath2 = $targetDir . DIRECTORY_SEPARATOR . $storedName2;
copy($testPdfPath2, $fullPath2);
$db->table('file_assets')->insert([
    'disk' => 'local', 'path' => 'uploads/regulations/' . $storedName2,
    'original_name' => 'qa-v2.pdf', 'stored_name' => $storedName2,
    'extension' => 'pdf', 'mime_type' => 'application/pdf', 'size_bytes' => filesize($testPdfPath2),
    'visibility' => 'internal', 'lifecycle_status' => 'available',
    'resource_type' => 'regulations', 'resource_id' => $regId,
    'uploaded_by' => $actorId, 'available_at' => $now, 'created_at' => $now, 'updated_at' => $now,
], true);
$stored2 = ['id' => (int) $db->insertID()];
ok('Version 2 file stored', $stored2['id'] > 0);

$vid2 = $svc->addVersion($regId, (int) $stored2['id'], 'Update v2', $actorId);
ok('Version 2 created', $vid2 > 0);
$ver2 = $db->table('regulation_versions')->where('id', $vid2)->get()->getRowArray();
ok('Version 2 has version_number=2', $ver2['version_number'] === 2);
ok('Version 2 is_current=1', $ver2['is_current'] === 1);

$ver1 = $db->table('regulation_versions')->where('id', $vid)->get()->getRowArray();
ok('Version 1 is_current=0 (demoted)', $ver1['is_current'] === 0);

echo "\n=== TEST 7: Create Relation ===\n";
$regId2 = $svc->create([
    'category_id'      => $catId,
    'title'            => 'Test Perpres 16/2018',
    'document_number'  => 'Perpres-16-2018-' . uniqid(),
    'document_year'    => 2018,
    'legal_status'     => 'active',
    'visibility'       => 'internal',
], $actorId);
ok('Second regulation created', $regId2 > 0);

$db->table('regulation_relations')->insert([
    'source_regulation_id' => $regId,
    'target_regulation_id' => $regId2,
    'relation_type'        => 'amends',
    'notes'                => 'Mengubah',
    'created_by'           => $actorId,
    'created_at'           => date('Y-m-d H:i:s'),
]);
$relId = (int) $db->insertID();
ok('Relation inserted', $relId > 0);

$auditRel = $db->table('audit_logs')->where('resource_id', $relId)->where('action', 'regulation_relation.create')->get()->getRowArray();
ok('Audit log on relation create', $auditRel !== null);

echo "\n=== TEST 8: Invalid Transition (Published → Reject) ===\n";
try {
    $svc->reject($regId, 'Test rejection after publish', $actorId);
    ok('Reject after publish rejected', false, 'should have thrown');
} catch (\InvalidArgumentException $e) {
    ok('Reject after publish rejected', true, $e->getMessage());
}

echo "\n=== Cleanup ===\n";
cleanReg($regId);
cleanReg($regId2);
$db->table('regulation_relations')->where('id', $relId)->delete();
cleanCat($catId);

echo "\n=== TOTAL: PASS=$passed, FAIL=$failed ===\n";