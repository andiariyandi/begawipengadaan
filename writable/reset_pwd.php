<?php
$db = new mysqli('127.0.0.1','root','','db_begawipbj');
$newHash = password_hash('TestPass123!', PASSWORD_BCRYPT);
$db->query("UPDATE users SET password_hash = '$newHash' WHERE email IN ('admin@begawipbj.go.id','superadmin@begawipbj.go.id')");
echo "Updated: ".$db->affected_rows." rows\n";
echo "New hash: $newHash\n";
echo "Verify: ".(password_verify('TestPass123!', $newHash) ? 'OK' : 'FAIL')."\n";
