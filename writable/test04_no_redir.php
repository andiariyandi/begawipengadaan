<?php
$cookie = tempnam(sys_get_temp_dir(), 'ck');
$url = 'http://127.0.0.1:8888/index.php/regulasi/test-perpres-12-2021';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
$resp = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);
echo "===== HEADERS (no redirect) =====\n";
echo $resp;
echo "\n===== Final URL: ".$info['url']." =====\n";
echo "Status: ".$info['http_code']."\n";
echo "Redirect URL: ".($info['redirect_url'] ?? 'none')."\n";
