<?php
$urls = [
  'regulasi/test-perpres-12-2021/preview',
  'regulasi/test-perpres-12-2021/unduh',
  'regulasi/test-perpres-12-2021/versi',
  'regulasi/test-perpres-12-2021/relasi',
  'regulasi/regulasi-a-ui/preview',
  'regulasi/regulasi-a-ui/unduh',
  'regulasi/regulasi-a-ui/versi',
  'regulasi/regulasi-a-ui/relasi',
  'regulasi/nonexistent/preview',
  'regulasi/nonexistent/unduh',
];
foreach ($urls as $path) {
  $url = 'http://127.0.0.1:8888/index.php/' . $path;
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  $body = curl_exec($ch);
  $info = curl_getinfo($ch);
  curl_close($ch);
  $fname = preg_replace('/[^a-z0-9]/', '_', strtolower($path));
  file_put_contents("writable/test_{$fname}.html", $body);
  $title = '';
  if (preg_match('/<title>([^<]+)</', $body, $m)) $title = $m[1];
  $ctype = $info['content_type'] ?? '';
  $isPdf = strpos($ctype, 'pdf') !== false || substr($body, 0, 4) === '%PDF';
  $articles = preg_match_all('/<article class="course-one__single/', $body);
  echo sprintf("path=%-55s status=%d size=%d ctype=%s pdf=%d articles=%d title=%s\n", $path, $info['http_code'], strlen($body), $ctype, $isPdf?1:0, $articles, $title);
}
