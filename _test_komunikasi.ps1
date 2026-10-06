$BASE = 'http://begawipengadaan.test'
$EMAIL = 'admin@begawipbj.go.id'
$PASSWORD = 'Spandupat24'

function Get-Csrf($url, $jar) {
    $req = [System.Net.HttpWebRequest]::Create($url)
    $req.CookieContainer = $jar
    $req.UserAgent = 'Mozilla/5.0'
    $req.Timeout = 30000
    $r = $req.GetResponse()
    $reader = New-Object System.IO.StreamReader($r.GetResponseStream())
    $html = $reader.ReadToEnd()
    $r.Close()
    $m = [regex]::Match($html, 'name="csrf_test_name"[^>]*value="([^"]+)"')
    if ($m.Success) { return $m.Groups[1].Value }
    return $null
}

function Do-Login($email, $password) {
    $jar = New-Object System.Net.CookieContainer
    $csrf = Get-Csrf "$BASE/dashboard/masuk" $jar
    if (-not $csrf) { return @{ ok = $false; msg = 'no csrf' } }
    $form = 'email=' + [System.Uri]::EscapeDataString($email) + '&password=' + [System.Uri]::EscapeDataString($password) + '&csrf_test_name=' + [System.Uri]::EscapeDataString($csrf)
    $req = [System.Net.HttpWebRequest]::Create("$BASE/dashboard/masuk")
    $req.CookieContainer = $jar
    $req.UserAgent = 'Mozilla/5.0'
    $req.Method = 'POST'
    $req.ContentType = 'application/x-www-form-urlencoded'
    $req.Referer = "$BASE/dashboard/masuk"
    $req.Timeout = 30000
    $req.AllowAutoRedirect = $false
    $bytes = [System.Text.Encoding]::UTF8.GetBytes($form)
    $req.ContentLength = $bytes.Length
    $s = $req.GetRequestStream()
    $s.Write($bytes, 0, $bytes.Length)
    $s.Close()
    try {
        $r = $req.GetResponse()
        $r.Close()
        $loc = $r.Headers['Location']
        if ($loc -match '/dashboard(\?|/index\.php/dashboard|$)' -and $loc -notmatch '/masuk') {
            return @{ ok = $true; jar = $jar; loc = $loc }
        }
        return @{ ok = $false; msg = 'redirect=' + $loc; jar = $jar }
    } catch {
        $code = $_.Exception.Response.StatusCode.value__
        return @{ ok = $false; msg = 'EXC ' + $code + ' ' + $_.Exception.Message; jar = $jar }
    }
}

function Test-Page($jar, $path, $label) {
    $req = [System.Net.HttpWebRequest]::Create("$BASE$path")
    $req.CookieContainer = $jar
    $req.UserAgent = 'Mozilla/5.0'
    $req.Timeout = 30000
    try {
        $r = $req.GetResponse()
        $reader = New-Object System.IO.StreamReader($r.GetResponseStream())
        $body = $reader.ReadToEnd()
        $r.Close()
        $bytes = $body.Length
        $errors = ''
        if ($body -match 'Whoops|Exception|Error\s*[:.-]?\s*<|404' ) {
            $errors = ' ERR-MARKER'
        }
        if ($body -match '(?s)<title>([^<]+)') {
            $title = $matches[1]
        } else {
            $title = '(no title)'
        }
        Write-Host ("[{0,-20}] {1} bytes={2} title={3}{4}" -f $label, $r.StatusCode, $bytes, $title, $errors)
    } catch {
        $code = $_.Exception.Response.StatusCode.value__
        Write-Host ("[{0,-20}] ERR {2} path={3}" -f $label, '', $code, $path)
    }
}

Write-Host '=== Login attempts ==='
$creds = @(
    @{ e = 'admin@begawipbj.go.id'; p = 'Spandupat24' },
    @{ e = 'admin@begawipbj.go.id'; p = 'admin123' },
    @{ e = 'admin@begawipbj.go.id'; p = 'admin' },
    @{ e = 'admin@begawipbj.go.id'; p = 'BegawiPBJ2024' },
    @{ e = 'admin@begawipbj.go.id'; p = 'BegawiPBJ' },
    @{ e = 'admin@begawipbj.go.id'; p = 'begawipbj' },
    @{ e = 'admin@begawipbj.go.id'; p = 'Admin123' },
    @{ e = 'admin@begawipbj.go.id'; p = 'password' },
    @{ e = 'superadmin@begawipbj.go.id'; p = 'Spandupat24' },
    @{ e = 'superadmin@begawipbj.go.id'; p = 'superadmin' }
)
$goodJar = $null
foreach ($c in $creds) {
    $r = Do-Login $c.e $c.p
    if ($r.ok) {
        Write-Host ("GOOD: " + $c.e + " / " + $c.p)
        $goodJar = $r.jar
        break
    } else {
        Write-Host ("BAD:  " + $c.e + " / " + $c.p + " => " + $r.msg)
    }
}

if (-not $goodJar) {
    Write-Host 'NO WORKING CREDENTIALS'
    exit 1
}

Write-Host ''
Write-Host '=== Walk Komunikasi pages ==='
Test-Page $goodJar '/dashboard' 'dashboard-home'
Test-Page $goodJar '/dashboard/komunikasi/berita' 'berita-list'
Test-Page $goodJar '/dashboard/komunikasi/berita/tambah' 'berita-add'
Test-Page $goodJar '/dashboard/komunikasi/kategori-berita' 'kategori-list'
Test-Page $goodJar '/dashboard/komunikasi/kategori-berita/tambah' 'kategori-add'
Test-Page $goodJar '/dashboard/komunikasi/event-agenda' 'event-list'
Test-Page $goodJar '/dashboard/komunikasi/event-agenda/tambah' 'event-add'
Test-Page $goodJar '/dashboard/komunikasi/registrasi-event' 'reg-list'
Test-Page $goodJar '/dashboard/komunikasi/kehadiran' 'attendance-list'
Test-Page $goodJar '/dashboard/komunikasi/waitlist-pembatalan' 'waitlist-list'