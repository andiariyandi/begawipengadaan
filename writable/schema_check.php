<?php
$db = new mysqli('127.0.0.1','root','','db_begawipbj');
$r = $db->query("SHOW CREATE TABLE regulations");
while ($row = $r->fetch_assoc()) echo $row['Create Table']."\n";
