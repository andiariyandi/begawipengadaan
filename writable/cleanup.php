<?php
// Clean up all test regulations from prior runs first
$db = new mysqli('127.0.0.1','root','','db_begawipbj');
// First delete the relations of these regulations
$db->query("DELETE FROM regulation_relations WHERE source_regulation_id IN (SELECT id FROM regulations WHERE document_number='12/2021-UI') OR target_regulation_id IN (SELECT id FROM regulations WHERE document_number='12/2021-UI')");
$db->query("DELETE FROM regulation_downloads WHERE regulation_id IN (SELECT id FROM regulations WHERE document_number='12/2021-UI')");
$db->query("DELETE FROM regulation_competencies WHERE regulation_id IN (SELECT id FROM regulations WHERE document_number='12/2021-UI')");
$db->query("DELETE FROM file_assets WHERE resource_type='regulations' AND resource_id IN (SELECT id FROM regulations WHERE document_number='12/2021-UI')");
$db->query("DELETE FROM regulation_versions WHERE regulation_id IN (SELECT id FROM regulations WHERE document_number='12/2021-UI')");
$db->query("DELETE FROM workflow_transitions WHERE resource_id IN (SELECT id FROM regulations WHERE document_number='12/2021-UI')");
$db->query("DELETE FROM audit_logs WHERE resource_id IN (SELECT id FROM regulations WHERE document_number='12/2021-UI')");
$db->query("DELETE FROM regulations WHERE document_number='12/2021-UI'");
echo "Cleanup done. 12/2021-UI rows: ".$db->affected_rows."\n";
$r = $db->query("SELECT id, workflow_status, document_number FROM regulations ORDER BY id DESC LIMIT 5");
while ($row = $r->fetch_assoc()) echo "ID={$row['id']} num={$row['document_number']} status={$row['workflow_status']}\n";
