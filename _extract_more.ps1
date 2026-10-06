$BASE = 'http://begawipengadaan.test'
$jar = New-Object System.Net.CookieContainer
function Get-Csrf($url, $j) {
    $r = [System.Net.HttpWebRequest]::Create($url); $r.CookieContainer = $j; $r.UserAgent = 'Mozilla/5.0'; $r.Timeout = 60000
    $resp = $r.GetResponse(); $rdr = New-Object System.IO.StreamReader($resp.GetResponseStream()); $h = $rdr.ReadToEnd(); $resp.Close()
    $m = [regex]::Match($h, 'name="csrf_test_name"[^>]*value="([^"]+)"'); if ($m.Success) { return $m.Groups[1].Value }; return $null
}
$csrf = Get-Csrf "$BASE/dashboard/masuk" $jar
$form = 'email=' + [System.Uri]::EscapeDataString('admin@begawipbj.go.id') + '&password=' + [System.Uri]::EscapeDataString('Spandupat24') + '&csrf_test_name=' + [System.Uri]::EscapeDataString($csrf)
$req = [System.Net.HttpWebRequest]::Create("$BASE/dashboard/masuk"); $req.CookieContainer = $jar; $req.UserAgent = 'Mozilla/5.0'
$req.Method = 'POST'; $req.ContentType = 'application/x-www-form-urlencoded'; $req.AllowAutoRedirect = $false; $req.Timeout = 60000
$bytes = [System.Text.Encoding]::UTF8.GetBytes($form); $req.ContentLength = $bytes.Length
$s = $req.GetRequestStream(); $s.Write($bytes, 0, $bytes.Length); $s.Close(); $r = $req.GetResponse(); $r.Close()
Write-Host 'login ok'

$paths = @(
    '/dashboard/bunga-rampai/template-dokumen/tambah',
    '/dashboard/regulasi/dokumen/tambah',
    '/dashboard/regulasi/relasi/tambah'
)
foreach ($path in $paths) {
    try {
        $r = [System.Net.HttpWebRequest]::Create("$BASE$path")
        $r.CookieContainer = $jar; $r.UserAgent = 'Mozilla/5.0'; $r.Timeout = 60000
        $resp = $r.GetResponse(); $rdr = New-Object System.IO.StreamReader($resp.GetResponseStream())
        $h = $rdr.ReadToEnd(); $resp.Close()
        $ms = [regex]::Matches($h, '<(input|textarea|select)\s+[^>]*name="([^"]+)"[^>]*')
        $fields = @()
        foreach ($mm in $ms) { $fields += $mm.Groups[2].Value }
        $fields = ($fields | Sort-Object -Unique) | Where-Object { $_ -notmatch 'csrf_test|^_$|csrf_token' }
        Write-Host ''
        Write-Host ("=== $path ===")
        Write-Host ($fields -join ', ')
    } catch { Write-Host ("`nERR $path : $($_.Exception.Message)") }
}