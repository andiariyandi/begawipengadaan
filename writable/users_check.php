<?php
$db = new mysqli('127.0.0.1','root','','db_begawipbj');
$r = $db->query("SELECT id, email, username, account_status, deleted_at FROM users ORDER BY id LIMIT 5");
while ($row = $r->fetch_assoc()) {
  echo "ID={$row['id']} email={$row['email']} username={$row['username']} status={$row['account_status']} deleted={$row['deleted_at']}\n";
}
