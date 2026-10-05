<?php
$queries = ['Perpres', '12', '2021', 'pengadaan', 'Nomor'];
foreach ($queries as $q) {
  $url = 'http://127.0.0.1:8888/index.php/regulasi?q=' . urlencode($q);
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
  $body = curl_exec($ch);
  $info = curl_getinfo($ch);
  curl_close($ch);
  $count = preg_match_all('/<a href="http:\/\/begawipengadaan\.test\/index\.php\/regulasi\/[^"]*">/', $body);
  $total = preg_match_all('/<article class="course-one__single/', $body);
  $slug = preg_replace('/[^a-z0-9]/', '_', strtolower($q));
  file_put_contents("writable/test03_search_{$slug}.html", $body);
  echo sprintf("q=%-12s status=%d size=%d articles=%d\n", $q, $info['http_code'], strlen($body), $total);
}
