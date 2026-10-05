<?php
$db = new mysqli('127.0.0.1','root','','db_begawipbj');
$r = $db->query("UPDATE regulations SET workflow_status = 'approved', reviewed_by = 14 WHERE id = 14");
echo "Update result: ".var_export($r, true)."\n";
echo "Affected: ".$db->affected_rows."\n";
echo "Errno: ".$db->errno." Error: ".$db->error."\n";
$r = $db->query("SELECT workflow_status, reviewed_by FROM regulations WHERE id = 14");
$row = $r->fetch_assoc();
echo "After update: status={$row['workflow_status']} reviewed_by={$row['reviewed_by']}\n";
