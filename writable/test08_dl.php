<?php
// Try download endpoints
$endpoints = [
  'regulasi/download',
  'regulasi/download/1',
  'regulasi/download/preview/1',
  'regulasi/preview/1',
  'regulasi/1/preview',
  'regulasi/1/download',
  'regulasi/file/1',
  'regulasi/files/preview/1',
  'regulasi/preview-document/1',
  'storage/regulations/1.pdf',
  'uploads/regulations/1.pdf',
];
foreach ($endpoints as $ep) {
  $url = 'http://127.0.0.1:8888/index.php/' . $ep;
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_NOBODY, true);
  curl_exec($ch);
  $info = curl_getinfo($ch);
  curl_close($ch);
  echo sprintf("ep=%-45s status=%d size=%d ctype=%s\n", $ep, $info['http_code'], $info['size_download'] ?? 0, $info['content_type'] ?? '');
}
