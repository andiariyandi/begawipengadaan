<?php
$url = 'http://127.0.0.1:8888/index.php/regulasi';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36');
$body = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);
file_put_contents('writable/test01_landing.html', $body);
echo "HTTP=".$info['http_code']." Size=".strlen($body)." bytes\n";
echo "FinalURL=".$info['url']."\n";
echo "Redirects=".$info['redirect_count']."\n";
