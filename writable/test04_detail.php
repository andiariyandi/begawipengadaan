<?php
$urls = ['test-perpres-12-2021', 'test-perpres-12-2021-2', 'test-perpres-12-2021-3', 'test-perpres-12-2021-4', 'regulasi-a-ui', 'regulasi-a-ui-2', 'regulasi-a-ui-3', 'nonexistent-slug-xxx'];
foreach ($urls as $slug) {
  $url = 'http://127.0.0.1:8888/index.php/regulasi/' . $slug;
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  $body = curl_exec($ch);
  $info = curl_getinfo($ch);
  curl_close($ch);
  $fname = preg_replace('/[^a-z0-9]/', '_', strtolower($slug));
  file_put_contents("writable/test04_detail_{$fname}.html", $body);
  $title = '';
  if (preg_match('/<title>([^<]+)</', $body, $m)) $title = $m[1];
  echo sprintf("slug=%-30s status=%d size=%d title=%s\n", $slug, $info['http_code'], strlen($body), $title);
}
