<?php
$queries = ['', 'Perpres', '12', '2021', 'pengadaan', 'Nomor', 'Regulasi', 'A%20UI', 'tidak-ada', 'TEST'];
foreach ($queries as $q) {
  $url = 'http://127.0.0.1:8888/index.php/regulasi';
  if ($q !== '') $url .= '?q=' . $q;
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  $body = curl_exec($ch);
  $info = curl_getinfo($ch);
  curl_close($ch);
  $articles = preg_match_all('/<article class="course-one__single/', $body);
  $titles = [];
  if (preg_match_all('/<p class="course-one__text">([^<]+)<\/p>/', $body, $m)) {
    foreach ($m[1] as $t) $titles[] = trim($t);
  }
  echo sprintf("q=%-12s status=%d articles=%d titles: %s\n", $q, $info['http_code'], $articles, implode('|', array_slice(array_unique($titles),0,5)));
}
