<?php
$db = new mysqli('127.0.0.1','root','','db_begawipbj');
$r = $db->query("DESCRIBE audit_logs");
while ($row = $r->fetch_assoc()) echo "  {$row['Field']} ({$row['Type']})\n";
