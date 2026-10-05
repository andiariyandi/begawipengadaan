<?php
$file = 'writable/debugbar/debugbar_1791179418.347430.json'; // approve
$d = json_decode(file_get_contents($file), true);
echo "URL: {$d['url']}\nMETHOD: {$d['method']}\n";
if (!empty($d['exceptions'])) echo "EXCEPTIONS:\n".json_encode($d['exceptions'], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)."\n";
if (!empty($d['logs'])) foreach ($d['logs']['logs'] ?? [] as $l) echo "LOG: {$l['msg']}\n";
if (!empty($d['vars'])) foreach ($d['vars']['vars'] ?? [] as $k=>$v) echo "VAR: $k\n";
if (!empty($d['queries'])) foreach ($d['queries']['queries'] ?? [] as $q) echo "QRY: ".substr($q['sql'],0,200)."\n";
