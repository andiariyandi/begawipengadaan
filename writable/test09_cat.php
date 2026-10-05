<?php
$urls = [
  'kategori/test-perpres-6ac288a0095d0',
  'kategori/test-perpres-6ac288c1c715b',
  'kategori/nonexistent-category-slug',
  'kategori',
  'kategori/',
];
foreach ($urls as $path) {
  $url = 'http://127.0.0.1:8888/index.php/regulasi/' . $path;
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  $body = curl_exec($ch);
  $info = curl_getinfo($ch);
  curl_close($ch);
  $fname = preg_replace('/[^a-z0-9]/', '_', strtolower($path));
  file_put_contents("writable/test09_cat_{$fname}.html", $body);
  $title = '';
  if (preg_match('/<title>([^<]+)</', $body, $m)) $title = $m[1];
  $articles = preg_match_all('/<article class="course-one__single/', $body);
  echo sprintf("path=%-45s status=%d size=%d title=%s articles=%d\n", $path, $info['http_code'], strlen($body), $title, $articles);
}
