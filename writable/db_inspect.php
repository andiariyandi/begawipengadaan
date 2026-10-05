<?php
// Use existing CI bootstrap via internal Spark-style helper
define('FCPATH', __DIR__ . '/../public/');
chdir(__DIR__ . '/../');
// Try simpler approach: hit existing URL endpoint that exposes regulation info via headers
// Instead, fetch the regulation count from the page itself via parsing odometer
$url = 'http://127.0.0.1:8888/index.php/regulasi';
$ctx = stream_context_create(['http' => ['header' => "User-Agent: Mozilla/5.0\r\n"]]);
$body = file_get_contents($url, false, $ctx);
// Extract odometer values
preg_match_all('/data-count="(\d+)">/', $body, $m);
echo "Landing odometer values: " . implode(', ', $m[1]) . "\n";
echo "Total Regulasi card count on landing: " . preg_match_all('/<article class="course-one__single h-100">/', $body) . "\n";
