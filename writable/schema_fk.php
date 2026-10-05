<?php
$db = new mysqli('127.0.0.1','root','','db_begawipbj');
$r = $db->query("SELECT TABLE_NAME, COLUMN_NAME, REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_NAME='regulations' AND TABLE_SCHEMA='db_begawipbj'");
while ($row = $r->fetch_assoc()) {
  echo $row['TABLE_NAME'].'.'.$row['COLUMN_NAME'].' -> regulations.'.$row['REFERENCED_COLUMN_NAME'].PHP_EOL;
}
