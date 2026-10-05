<?php
$cookieJar = sys_get_temp_dir().'/manual-test-'.uniqid().'.txt';
function ui($method, $uri, $post=[], $cookieJar) {
    $ch = curl_init('http://127.0.0.1:8888' . $uri);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true, CURLOPT_HEADER => true,
        CURLOPT_FOLLOWLOCATION => false, CURLOPT_COOKIEJAR => $cookieJar,
        CURLOPT_COOKIEFILE => $cookieJar, CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTPHEADER => ['Expect:'],
    ]);
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    }
    $r = curl_exec($ch); $info = curl_getinfo($ch);
    $body = $r === false ? '' : substr($r, $info['header_size']);
    $headers = $r === false ? '' : substr($r, 0, $info['header_size']);
    curl_close($ch);
    return ['status'=>$info['http_code'], 'headers'=>$headers, 'body'=>$body];
}
function getCsrf($html) {
    if (preg_match("/name=['\\\"]csrf_test_name['\\\"]\\s+value=['\\\"]([a-f0-9]+)['\\\"]/", $html, $m)) return $m[1];
    return null;
}
$r = ui('GET', '/dashboard/masuk', [], $cookieJar);
$csrf = getCsrf($r['body']);
$r = ui('POST', '/dashboard/masuk', ['csrf_test_name'=>$csrf,'email'=>'admin@begawipbj.go.id','password'=>'TestPass123!'], $cookieJar);
echo "Login: status={$r['status']}\n";

// Find submitted regulasi
$r = ui('GET', '/dashboard/regulasi/dokumen', [], $cookieJar);
preg_match_all("/dashboard\/regulasi\/dokumen\/(\d+)/", $r['body'], $m);
$ids = array_unique($m[1] ?? []);
echo "IDs: ".implode(',', $ids)."\n";
// Try id 14 (from prior test)
$rid = 14;
$r2 = ui('GET', "/dashboard/regulasi/dokumen/$rid", [], $cookieJar);
echo "Detail: status={$r2['status']}\n";
// Check workflow_status displayed
if (preg_match("/workflow_status['\\\"]?\\s*[:=]\\s*['\\\"](\w+)['\\\"]/", $r2['body'], $m2)) {
  echo "Displayed status: ".$m2[1]."\n";
}
// Try to call approve
$csrf2 = getCsrf($r2['body']);
echo "CSRF2: ".substr($csrf2,0,16)."...\n";
$r3 = ui('POST', "/dashboard/regulasi/dokumen/$rid/approve", ['csrf_test_name'=>$csrf2,'notes'=>'OK'], $cookieJar);
echo "Approve: status={$r3['status']} Loc=".(preg_match('/Location: ([^\r\n]+)/', $r3['headers'], $m)?$m[1]:'')."\n";
$r4 = ui('GET', "/dashboard/regulasi/dokumen/$rid", [], $cookieJar);
if (preg_match("/workflow_status['\\\"]?\\s*[:=]\\s*['\\\"](\w+)['\\\"]/", $r4['body'], $m2)) {
  echo "After approve status: ".$m2[1]."\n";
}
