<?php
$db = new mysqli('127.0.0.1','root','','db_begawipbj');
if (!$db->query("ALTER TABLE regulations ADD COLUMN reviewed_at DATETIME NULL AFTER reviewed_by")) {
  echo "ALTER failed: ".$db->error."\n";
} else {
  echo "Added reviewed_at column to regulations\n";
}
$r = $db->query("DESCRIBE regulations");
echo "Current schema:\n";
while ($row = $r->fetch_assoc()) {
  if (in_array($row['Field'], ['reviewed_by','reviewed_at','published_by','published_at'])) {
    echo "  {$row['Field']} ({$row['Type']}) null={$row['Null']}\n";
  }
}
