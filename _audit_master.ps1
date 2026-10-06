$BASE = 'http://begawipengadaan.test'
$LOG = 'C:\laragon\www\begawipengadaan\_audit_log.txt'
$RESULTS = @()

function Get-Csrf($url, $jar) {
    $req = [System.Net.HttpWebRequest]::Create($url)
    $req.CookieContainer = $jar
    $req.UserAgent = 'Mozilla/5.0'
    $req.Timeout = 60000
    $r = $req.GetResponse()
    $reader = New-Object System.IO.StreamReader($r.GetResponseStream())
    $html = $reader.ReadToEnd()
    $r.Close()
    $m = [regex]::Match($html, 'name="csrf_test_name"[^>]*value="([^"]+)"')
    if ($m.Success) { return $m.Groups[1].Value }
    return $null
}

function Do-Get($jar, $path) {
    $url = "$BASE$path"
    $req = [System.Net.HttpWebRequest]::Create($url)
    $req.CookieContainer = $jar
    $req.UserAgent = 'Mozilla/5.0'
    $req.Timeout = 60000
    try {
        $r = $req.GetResponse()
        $reader = New-Object System.IO.StreamReader($r.GetResponseStream())
        $body = $reader.ReadToEnd()
        $r.Close()
        $status = [int]$r.StatusCode
        $len = $body.Length
        $is404 = $body -match '404 Page' -or $body -match 'Whoops' -or $body -match 'Exception &amp; occurred'
        $hasTitle = ($body -match '(?s)<title>([^<]+)</title>')
        $title = if ($hasTitle) { ($matches[1].Trim()) } else { '' }
        $hasTable = ($body -match '<table') -or ($body -match 'data-table')
        $hasForm = ($body -match '<form') -and ($body -match 'csrf_test_name')
        $hasContent = ($body -match 'class="container"|class="page-content"|data-sveltekit|<main')
        $pass = ($status -eq 200) -and (-not $is404)
        return [pscustomobject]@{
            Path = $path; Status = $status; Len = $len; Pass = $pass
            Is404 = $is404; Title = $title.Substring(0, [Math]::Min(80, $title.Length))
            HasTable = $hasTable; HasForm = $hasForm; HasContent = $hasContent
        }
    } catch {
        $code = if ($_.Exception.Response) { $_.Exception.Response.StatusCode.value__ } else { -1 }
        return [pscustomobject]@{
            Path = $path; Status = [int]$code; Len = 0; Pass = $false
            Is404 = $true; Title = 'ERR:' + $_.Exception.Message.Substring(0, [Math]::Min(60, $_.Exception.Message.Length))
            HasTable = $false; HasForm = $false; HasContent = $false
        }
    }
}

function Do-Post($jar, $path, $form) {
    $url = "$BASE$path"
    $csrf = Get-Csrf $url $jar
    $boundary = '----FormBoundary' + (Get-Random)
    $body = New-Object System.IO.MemoryStream
    $writer = New-Object System.IO.StreamWriter($body)
    foreach ($k in $form.Keys) {
        $v = $form[$k]
        $writer.Write("--$boundary`r`nContent-Disposition: form-data; name=`"$k`"`r`n`r`n$v$this--`r`n")
    }
    $writer.Close()
    $bodyBytes = $body.ToArray()
    $body.Close()

    # url-encoded simpler approach
    $enc = New-Object System.Text.UTF8Encoding
    $pairs = @()
    foreach ($k in $form.Keys) {
        $pairs += ($k + '=' + [System.Uri]::EscapeDataString([string]$form[$k]))
    }
    $encoded = ($pairs -join '&')
    $bytes = $enc.GetBytes($encoded)
    $req = [System.Net.HttpWebRequest]::Create($url)
    $req.CookieContainer = $jar
    $req.UserAgent = 'Mozilla/5.0'
    $req.Method = 'POST'
    $req.ContentType = 'application/x-www-form-urlencoded'
    $req.Referer = $url
    $req.Timeout = 60000
    $req.AllowAutoRedirect = $false
    $req.ContentLength = $bytes.Length
    $s = $req.GetRequestStream()
    $s.Write($bytes, 0, $bytes.Length)
    $s.Close()
    try {
        $r = $req.GetResponse()
        $loc = $r.Headers['Location']
        $status = [int]$r.StatusCode
        $reader = New-Object System.IO.StreamReader($r.GetResponseStream())
        $bodyText = $reader.ReadToEnd()
        $r.Close()
        $isError = $bodyText -match 'whoops|Exception &amp; occurred|Error:|<title>Error|<title>404'
        $okRedirect = ($loc -and ($loc -match '/(dashboard|admin)')) -and ($loc -notmatch '/tambah$|/create$|/create$')
        return [pscustomobject]@{
            Path = $path; Status = $status; Loc = $loc; Len = $bodyText.Length
            Pass = ($status -in 200, 302, 303) -and (-not $isError) -and (-not $isError)
            HasError = $isError
            BodyHead = $bodyText.Substring(0, [Math]::Min(120, $bodyText.Length))
        }
    } catch {
        $code = if ($_.Exception.Response) { $_.Exception.Response.StatusCode.value__ } else { -1 }
        return [pscustomobject]@{
            Path = $path; Status = [int]$code; Loc = ''; Len = 0
            Pass = $false; HasError = $true; BodyHead = $_.Exception.Message
        }
    }
}

# ---------- LOGIN ----------
Write-Host '=== LOGIN ==='
$jar = New-Object System.Net.CookieContainer
$csrf = Get-Csrf "$BASE/dashboard/masuk" $jar
if (-not $csrf) { Write-Host 'NO CSRF'; exit 1 }
$form = 'email=' + [System.Uri]::EscapeDataString('admin@begawipbj.go.id') + '&password=' + [System.Uri]::EscapeDataString('Spandupat24') + '&csrf_test_name=' + [System.Uri]::EscapeDataString($csrf)
$req = [System.Net.HttpWebRequest]::Create("$BASE/dashboard/masuk")
$req.CookieContainer = $jar; $req.UserAgent = 'Mozilla/5.0'; $req.Method = 'POST'; $req.ContentType = 'application/x-www-form-urlencoded'
$req.Referer = "$BASE/dashboard/masuk"; $req.Timeout = 60000; $req.AllowAutoRedirect = $false
$bytes = [System.Text.Encoding]::UTF8.GetBytes($form)
$req.ContentLength = $bytes.Length
$s = $req.GetRequestStream(); $s.Write($bytes, 0, $bytes.Length); $s.Close()
$r = $req.GetResponse(); $loc = $r.Headers['Location']; $r.Close()
Write-Host ("login redirect: $loc")
if ($loc -match '/masuk') { Write-Host 'LOGIN FAILED'; exit 1 }
Write-Host 'LOGIN OK'
Write-Host ''

# ---------- MENU LIST ----------
$menus = @(
    # modul,menu,url,tipe(CRUD|READ),POST_NAME,fields
    @{mod='Arena Belajar';name='Program';m='+';url='/dashboard/pembelajaran/program';formUrl='/dashboard/pembelajaran/program/tambah';fields=@{}},
    @{mod='Arena Belajar';name='Course';m='+';url='/dashboard/pembelajaran/course';formUrl='/dashboard/pembelajaran/course/tambah';fields=@{}},
    @{mod='Arena Belajar';name='Kelas';m='+';url='/dashboard/pembelajaran/kelas';formUrl='/dashboard/pembelajaran/kelas/tambah';fields=@{}},
    @{mod='Arena Belajar';name='Materi dan Modul';m='+';url='/dashboard/pembelajaran/materi-modul';formUrl='/dashboard/pembelajaran/materi-modul/tambah';fields=@{}},
    @{mod='Arena Belajar';name='Assessment dan Bank Soal';m='R';url='/dashboard/pembelajaran/assessment';formUrl='';fields=@{}},
    @{mod='Arena Belajar';name='Quiz';m='R';url='/dashboard/pembelajaran/assessment/quiz';formUrl='';fields=@{}},
    @{mod='Arena Belajar';name='Bank Soal';m='R';url='/dashboard/pembelajaran/assessment/bank-soal';formUrl='';fields=@{}},
    @{mod='Arena Belajar';name='Enrollment';m='R';url='/dashboard/pembelajaran/enrollment';formUrl='';fields=@{}},
    @{mod='Arena Belajar';name='Penilaian';m='R';url='/dashboard/pembelajaran/penilaian';formUrl='';fields=@{}},
    @{mod='Arena Belajar';name='Progres dan Kelulusan';m='R';url='/dashboard/pembelajaran/progres-kelulusan';formUrl='';fields=@{}},
    @{mod='Arena Belajar';name='Feedback Peserta';m='R';url='/dashboard/pembelajaran/feedback-peserta';formUrl='';fields=@{}},

    @{mod='Bunga Rampai';name='Artikel';m='+';url='/dashboard/bunga-rampai/artikel';formUrl='/dashboard/bunga-rampai/artikel/tambah';fields=@{}},
    @{mod='Bunga Rampai';name='FAQ';m='+';url='/dashboard/bunga-rampai/faq';formUrl='/dashboard/bunga-rampai/faq/tambah';fields=@{}},
    @{mod='Bunga Rampai';name='Studi Kasus';m='+';url='/dashboard/bunga-rampai/studi-kasus';formUrl='/dashboard/bunga-rampai/studi-kasus/tambah';fields=@{}},
    @{mod='Bunga Rampai';name='Template Dokumen';m='+';url='/dashboard/bunga-rampai/template-dokumen';formUrl='/dashboard/bunga-rampai/template-dokumen/tambah';fields=@{}},
    @{mod='Bunga Rampai';name='Media dan Galeri';m='+';url='/dashboard/bunga-rampai/media-galeri';formUrl='/dashboard/bunga-rampai/media-galeri/tambah';fields=@{}},
    @{mod='Bunga Rampai';name='Carousel Konten Unggulan';m='+';url='/dashboard/bunga-rampai/carousel-konten-unggulan';formUrl='/dashboard/bunga-rampai/carousel-konten-unggulan/tambah';fields=@{}},
    @{mod='Bunga Rampai';name='Kategori';m='+';url='/dashboard/bunga-rampai/kategori';formUrl='/dashboard/bunga-rampai/kategori/tambah';fields=@{}},
    @{mod='Bunga Rampai';name='Tag';m='+';url='/dashboard/bunga-rampai/tag';formUrl='/dashboard/bunga-rampai/tag/tambah';fields=@{}},

    @{mod='Regulasi PBJ';name='Regulasi';m='+';url='/dashboard/regulasi/dokumen';formUrl='/dashboard/regulasi/dokumen/tambah';fields=@{}},
    @{mod='Regulasi PBJ';name='Kategori Regulasi';m='+';url='/dashboard/regulasi/kategori';formUrl='/dashboard/regulasi/kategori/tambah';fields=@{}},
    @{mod='Regulasi PBJ';name='Versi Dokumen';m='R';url='/dashboard/regulasi/versi';formUrl='';fields=@{}},
    @{mod='Regulasi PBJ';name='Relasi Regulasi';m='+';url='/dashboard/regulasi/relasi';formUrl='/dashboard/regulasi/relasi/tambah';fields=@{}},
    @{mod='Regulasi PBJ';name='Tahapan PBJ';m='+';url='/dashboard/regulasi/tahapan-pbj';formUrl='/dashboard/regulasi/tahapan-pbj/tambah';fields=@{}},
    @{mod='Regulasi PBJ';name='Kompetensi Terkait';m='+';url='/dashboard/regulasi/kompetensi-terkait';formUrl='/dashboard/regulasi/kompetensi/tambah';fields=@{}},
    @{mod='Regulasi PBJ';name='Log Download';m='R';url='/dashboard/regulasi/log-download';formUrl='';fields=@{}},

    @{mod='Komunikasi';name='Berita';m='+';url='/dashboard/komunikasi/berita';formUrl='/dashboard/komunikasi/berita/tambah';fields=@{}},
    @{mod='Komunikasi';name='Kategori Berita';m='+';url='/dashboard/komunikasi/kategori-berita';formUrl='/dashboard/komunikasi/kategori-berita/tambah';fields=@{}},
    @{mod='Komunikasi';name='Event dan Agenda';m='+';url='/dashboard/komunikasi/event-agenda';formUrl='/dashboard/komunikasi/event-agenda/tambah';fields=@{}},
    @{mod='Komunikasi';name='Registrasi Event';m='R';url='/dashboard/komunikasi/registrasi-event';formUrl='';fields=@{}},
    @{mod='Komunikasi';name='Kehadiran';m='R';url='/dashboard/komunikasi/kehadiran';formUrl='';fields=@{}},
    @{mod='Komunikasi';name='Waitlist dan Pembatalan';m='R';url='/dashboard/komunikasi/waitlist-pembatalan';formUrl='';fields=@{}}
)

$allPages += $menus | ForEach-Object { Do-Get $jar $_.url }

Write-Host '=== ACCESS TEST (list pages) ==='
foreach ($m in $menus) {
    $r = ($allPages | Where-Object { $_.Path -eq $m.url })[0]
    $status = if ($r.Pass) { 'PASS' } else { 'FAIL' }
    Write-Host ("[{0,-6}] {1,-12} {2,-35} -> {3} ({4}) [{5}]" -f $status, $m.mod, $m.name, $r.Status, $r.Len, $r.Title)
}

Write-Host ''
Write-Host '=== FORM PAGE ACCESS (create/get) ==='
$formPages = @()
foreach ($m in $menus) {
    if ($m.formUrl) {
        $r = Do-Get $jar $m.formUrl
        $formPages += $r
        $status = if ($r.Pass) { 'PASS' } else { 'FAIL' }
        Write-Host ("[{0,-6}] {1,-12} {2,-35} -> {3} ({4}) [{5}]" -f $status, $m.mod, $m.name + ' (form)', $r.Status, $r.Len, $r.Title)
    }
}

Write-Host ''
Write-Host '=== TEST DATA PREFIX ==='
$TS = Get-Date -Format 'yyyyMMdd-HHmmss'
Write-Host ("Timestamp: $TS")

# ---------- ACCESS SUMMARY ----------
$total = $menus.Count
$pass = ($allPages | Where-Object { $_.Pass }).Count
$fail = $total - $pass
Write-Host ''
Write-Host ("LIST PAGES: total=$total pass=$pass fail=$fail")
$totalForms = $formPages.Count
$passForms = ($formPages | Where-Object { $_.Pass }).Count
$failForms = $totalForms - $passForms
Write-Host ("FORM PAGES: total=$totalForms pass=$passForms fail=$failForms")

# ---------- SAVE FULL HTML for FAIL pages (for bug analysis) ----------
$dumpDir = 'C:\laragon\www\begawipengadaan\_audit_dump'
New-Item -ItemType Directory -Path $dumpDir -Force | Out-Null
$failList = $allPages | Where-Object { -not $_.Pass }
foreach ($r in $failList) {
    $slug = $r.Path -replace '/', '_'
    $req = [System.Net.HttpWebRequest]::Create("$BASE$($r.Path)")
    $req.CookieContainer = $jar; $req.UserAgent = 'Mozilla/5.0'; $req.Timeout = 30000
    try {
        $rr = $req.GetResponse()
        $rdr = New-Object System.IO.StreamReader($rr.GetResponseStream())
        $body = $rdr.ReadToEnd(); $rr.Close()
        Set-Content -Path "$dumpDir$slug.html" -Value $body
    } catch { }
}

# ---------- SUMMARY as JSON for downstream ----------
$report = [pscustomobject]@{
    timestamp = $TS
    total = $total
    pass = $pass
    fail = $fail
    total_forms = $totalForms
    pass_forms = $passForms
    fail_forms = $failForms
    list_results = $allPages
    form_results = $formPages
}
$report | ConvertTo-Json -Depth 3 | Out-File -FilePath $LOG
Write-Host "Report saved: $LOG"