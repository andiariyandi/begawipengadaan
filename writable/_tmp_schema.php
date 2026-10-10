<?php
chdir(__DIR__ . "/..");
define("FCPATH", __DIR__ . "/../public/");
require FCPATH . "../app/Config/Paths.php";
$paths = new Config\Paths();
require $paths->systemDirectory . "/Boot.php";
require App\Config\Paths::appDirectory() . "/Config/Database.php";
$db = \Config\Database::connect();
$tables = ["events","event_registrations","attendances","approvals","approval_histories","certificate_templates","certificates","enrollments","learning_classes"];
foreach ($tables as $t) {
    echo "=== $t ===\n";
    try {
        $rows = $db->query("DESCRIBE `$t`")->getResultArray();
        foreach ($rows as $r) { echo $r["Field"]." ".$r["Type"]." ".($r["Null"]==="YES"?"NULL":"NOT NULL")."\n"; }
    } catch (\Throwable $e) { echo "ERR: ".$e->getMessage()."\n"; }
    echo "\n";
}
