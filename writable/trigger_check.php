<?php
$db = new mysqli('127.0.0.1','root','','db_begawipbj');
$r = $db->query("SELECT TRIGGER_NAME, EVENT_MANIPULATION, ACTION_STATEMENT FROM INFORMATION_SCHEMA.TRIGGERS WHERE EVENT_OBJECT_SCHEMA = 'db_begawipbj' AND EVENT_OBJECT_TABLE = 'regulations'");
while ($row = $r->fetch_assoc()) {
  echo "Trigger {$row['TRIGGER_NAME']} ({$row['EVENT_MANIPULATION']}): {$row['ACTION_STATEMENT']}\n";
}
