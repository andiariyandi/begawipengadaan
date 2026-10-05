<?php
$cmd = 'powershell.exe -Command "(Get-NetTCPConnection -LocalPort 8888 -ErrorAction SilentlyContinue).State"';
$out = shell_exec($cmd);
echo "Server status: ".(trim($out) ?: 'unknown')."\n";

// Test landing regulasi endpoint
$ch = curl_init('http://127.0.0.1:8888/index.php/regulasi');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36');
$resp = curl_exec($ch);
$info = curl_getinfo($ch);
$status = $info['http_code'];
$err = curl_error($ch);
curl_close($ch);

echo "URL: http://127.0.0.1:8888/index.php/regulasi\n";
echo "HTTP Status: $status\n";
echo "Curl Error: ".($err ?: 'none')."\n";
echo "Response size: ".strlen($resp)." bytes\n";
echo "---HEADERS---\n";
$lines = explode("\n", $resp);
$head_end = false;
foreach ($lines as $line) {
    if ($head_end) break;
    if (trim($line) === '') { $head_end = true; continue; }
    echo $line."\n";
}
