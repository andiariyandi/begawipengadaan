<?php
$db = new mysqli('127.0.0.1','root','','db_begawipbj');
$r = $db->query("SELECT id, document_number, workflow_status, reviewed_by, published_by, published_at FROM regulations WHERE id IN (13,14,15)");
while ($row = $r->fetch_assoc()) {
  echo "ID={$row['id']} num={$row['document_number']} status={$row['workflow_status']} reviewed_by={$row['reviewed_by']} published_by={$row['published_by']} published_at={$row['published_at']}\n";
}
