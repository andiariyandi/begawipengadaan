<?php
$db = new mysqli('127.0.0.1','root','','db_begawipbj');
$r = $db->query("SELECT email, password_hash FROM users WHERE email IN ('admin@begawipbj.go.id','superadmin@begawipbj.go.id')");
while ($row = $r->fetch_assoc()) {
  echo "email={$row['email']} full_hash={$row['password_hash']}\n";
}
