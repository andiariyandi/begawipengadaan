$BASE = 'http://begawipengadaan.test'
$TS = '20261005-224000'
$LOG = 'C:\laragon\www\begawipengadaan\_crud_log.txt'
$RESULTS = @()

function Get-Csrf($url, $j) {
    $r = [System.Net.HttpWebRequest]::Create($url); $r.CookieContainer = $j; $r.UserAgent = 'Mozilla/5.0'; $r.Timeout = 60000
    $resp = $r.GetResponse(); $rdr = New-Object System.IO.StreamReader($resp.GetResponseStream()); $h = $rdr.ReadToEnd(); $resp.Close()
    $m = [regex]::Match($h, 'name="csrf_test_name"[^>]*value="([^"]+)"'); if ($m.Success) { return $m.Groups[1].Value }; return $null
}

# LOGIN
$jar = New-Object System.Net.CookieContainer
$csrf = Get-Csrf "$BASE/dashboard/masuk" $jar
$form = 'email=' + [System.Uri]::EscapeDataString('admin@begawipbj.go.id') + '&password=' + [System.Uri]::EscapeDataString('Spandupat24') + '&csrf_test_name=' + [System.Uri]::EscapeDataString($csrf)
$req = [System.Net.HttpWebRequest]::Create("$BASE/dashboard/masuk"); $req.CookieContainer = $jar; $req.UserAgent = 'Mozilla/5.0'
$req.Method = 'POST'; $req.ContentType = 'application/x-www-form-urlencoded'; $req.AllowAutoRedirect = $false; $req.Timeout = 60000
$bytes = [System.Text.Encoding]::UTF8.GetBytes($form); $req.ContentLength = $bytes.Length
$s = $req.GetRequestStream(); $s.Write($bytes, 0, $bytes.Length); $s.Close(); $r = $req.GetResponse(); $r.Close()
Write-Host ("Logged in at: " + $TS)

# GET fresh csrf from form page, then POST
function Do-Create($postUrl, $csrfUrl, $fields) {
    if (-not $csrfUrl) { $csrfUrl = $postUrl }
    $j = $jar  # share session
    $csrf = Get-Csrf "$BASE$csrfUrl" $j
    if (-not $csrf) { return @{ ok=$false; msg="NO_CSRF"; loc=""; status=0; body="" } }
    $fields["csrf_test_name"] = $csrf
    foreach ($k in $fields.Keys) {
        $v = [string]$fields[$k]
        $pairs += ($k + '=' + [System.Uri]::EscapeDataString($v))
    }
    $encoded = ($pairs -join '&')
    $bytes = [System.Text.Encoding]::UTF8.GetBytes($encoded)
    $r = [System.Net.HttpWebRequest]::Create("$BASE$postUrl")
    $r.CookieContainer = $j; $r.UserAgent = 'Mozilla/5.0'
    $r.Method = 'POST'; $r.ContentType = 'application/x-www-form-urlencoded'
    $r.Referer = "$BASE$csrfUrl"
    $r.ContentLength = $bytes.Length
    $s = $r.GetRequestStream(); $s.Write($bytes, 0, $bytes.Length); $s.Close()
    try {
        $resp = $r.GetResponse()
        $loc = $resp.Headers['Location']
        $st = [int]$resp.StatusCode
        $rdr = New-Object System.IO.StreamReader($resp.GetResponseStream())
        $body = $rdr.ReadToEnd(); $resp.Close()
        $hasErr = $body -match 'whoops|Exception &amp; occurred|<title>Error|<title>404|<h1>404|Validation failed'
        $okRedirect = ($loc -and $loc -notmatch '/tambah$') -and ($loc -notmatch 'create$')
        return @{ ok = ((-not $hasErr) -and ($st -in 200, 302, 303)); msg = 'OK'; loc = $loc; status = $st; body = $body.Substring(0, [Math]::Min(200, $body.Length)); err = $hasErr }
    } catch {
        $code = if ($_.Exception.Response) { $_.Exception.Response.StatusCode.value__ } else { -1 }
        $errBody = ''
        try { $rdr = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream()); $errBody = $rdr.ReadToEnd() } catch {}
        return @{ ok = $false; msg = ('EXC_' + $code); loc = ''; status = [int]$code; body = $errBody.Substring(0, [Math]::Min(200, $errBody.Length)); err = $true }
    }
}

# Verify by checking list for new data
function Verify-InList($listUrl, $needle) {
    try {
        $r = [System.Net.HttpWebRequest]::Create("$BASE$listUrl")
        $r.CookieContainer = $jar; $r.UserAgent = 'Mozilla/5.0'; $r.Timeout = 60000
        $resp = $r.GetResponse(); $rdr = New-Object System.IO.StreamReader($resp.GetResponseStream())
        $body = $rdr.ReadToEnd(); $resp.Close()
        return $body.Contains($needle)
    } catch { return $false }
}

# === Test sequence ===
$tests = @(

    @{name='Arena Belajar / Program';     url='/dashboard/pembelajaran/program'; csrfUrl='/dashboard/pembelajaran/program/tambah';
     fields=@{ title="QA AUDIT Program $TS"; slug="qa-audit-program-$TS"; description="qa audit"; status="draft"; visibility="public"; featured="0"; cover_file_id="" }},
    @{name='Arena Belajar / Course';      url='/dashboard/pembelajaran/course'; csrfUrl='/dashboard/pembelajaran/course/tambah';
     fields=@{ title="QA AUDIT Course $TS"; slug="qa-audit-course-$TS"; description="qa audit"; status="draft"; visibility="public"; level="pemula"; duration_minutes="60"; program_id="4"; featured="0" }},
    @{name='Arena Belajar / Kelas';       url='/dashboard/pembelajaran/kelas'; csrfUrl='/dashboard/pembelajaran/kelas/tambah';
     fields=@{ name="QA AUDIT Kelas $TS"; code="QA-CLS-$TS"; course_id="5"; start_at="2027-01-01 08:00:00"; end_at="2027-01-02 08:00:00"; status="draft"; certificate_enabled="0"; capacity="30"; passing_grade="70.00" }},
    @{name='Arena Belajar / Modul';        url='/dashboard/pembelajaran/materi-modul'; csrfUrl='/dashboard/pembelajaran/materi-modul/tambah';
     fields=@{ title="QA AUDIT Modul $TS"; class_id="4"; description="qa audit"; status="draft"; is_required="1"; sort_order="1" }},
    @{name='Bunga Rampai / Artikel';      url='/dashboard/bunga-rampai/artikel/tambah';
     fields=@{ title="QA AUDIT Artikel $TS"; content="qa audit content"; summary="qa audit"; status="draft"; visibility="public"; featured="0"; carousel_enabled="0"; carousel_order="0"; categories="3"; tags="" }},
    @{name='Bunga Rampai / FAQ';          url='/dashboard/bunga-rampai/faq/tambah';
     fields=@{ title="QA AUDIT FAQ $TS"; content="qa audit FAQ content"; status="draft"; categories="3" }},
    @{name='Bunga Rampai / Studi Kasus';  url='/dashboard/bunga-rampai/studi-kasus/tambah';
     fields=@{ title="QA AUDIT Studi Kasus $TS"; content="qa audit studi kasus content"; status="draft"; categories="3" }},
    @{name='Bunga Rampai / Template Dok'; url='/dashboard/bunga-rampai/template-dokumen/tambah';
     fields=@{ title="QA AUDIT Template $TS"; content="qa audit template content"; summary="qa audit template"; status="draft"; categories="3" }},
    @{name='Bunga Rampai / Media Galeri'; url='/dashboard/bunga-rampai/media-galeri/tambah';
     fields=@{ title="QA AUDIT Media $TS"; alt_text="qa"; type="image"; status="draft"; visibility="public"; featured="0"; carousel_enabled="0"; sort_order="1"; caption="" }},
    @{name='Bunga Rampai / Carousel';     url='/dashboard/bunga-rampai/carousel-konten-unggulan/tambah';
     fields=@{ target_id="5"; target_type="course"; order="1" }},
    @{name='Bunga Rampai / Kategori';     url='/dashboard/bunga-rampai/kategori/tambah';
     fields=@{ name="QA AUDIT Kategori $TS"; description="qa"; is_active="1"; sort_order="1"; parent_id="" }},
    @{name='Bunga Rampai / Tag';          url='/dashboard/bunga-rampai/tag/tambah';
     fields=@{ name="qa-audit-tag-$TS" }},
    @{name='Regulasi / Dokumen';          url='/dashboard/regulasi/dokumen'; csrfUrl='/dashboard/regulasi/dokumen/tambah';
     fields=@{ title="QA AUDIT Regulasi $TS"; category_id="2"; document_number="QA-AUDIT-$TS"; document_year="2027"; effective_date="2027-01-01"; issuer_name="QA Issuer"; legal_status="berlaku"; official_date=""; official_source_url=""; visibility="public"; summary="qa audit regulation" }},
    @{name='Regulasi / Kategori';         url='/dashboard/regulasi/kategori/tambah';
     fields=@{ name="QA AUDIT Kategori Reg $TS"; slug="qa-audit-kategori-reg-$TS"; description="qa audit"; is_active="1" }},
    @{name='Regulasi / Relasi';           url='/dashboard/regulasi/relasi/tambah';
     fields=@{ source_regulation_id="3"; target_regulation_id="4"; relation_type="melengkapi"; notes="qa audit" }},
    @{name='Regulasi / Tahapan PBJ';      url='/dashboard/regulasi/tahapan-pbj/tambah';
     fields=@{ name="QA AUDIT Tahapan $TS"; key="qa-audit-tahapan-$TS"; description="qa audit"; sort_order="1"; is_active="1" }},
    @{name='Regulasi / Kompetensi';       url='/dashboard/regulasi/kompetensi/tambah';
     fields=@{ name="QA AUDIT Kompetensi $TS"; key="qa-audit-kompetensi-$TS"; description="qa audit"; is_active="1" }},
    @{name='Komunikasi / Berita';         url='/dashboard/komunikasi/berita/tambah';
     fields=@{ title="QA AUDIT Berita $TS"; content="qa audit berita content"; summary="qa audit"; category_id="3"; visibility="public"; workflow_status="draft"; scheduled_at="" }},
    @{name='Komunikasi / Kategori';       url='/dashboard/komunikasi/kategori-berita/tambah';
     fields=@{ name="QA AUDIT Kategori Berita $TS"; slug="qa-audit-kategori-berita-$TS"; description="qa audit"; is_active="1" }},
    @{name='Komunikasi / Event';          url='/dashboard/komunikasi/event-agenda/tambah';
     fields=@{ title="QA AUDIT Event $TS"; summary="qa audit event"; description="qa"; type="webinar"; mode="online"; location="Online"; meeting_url="https://example.com/qa-audit"; capacity="100"; start_at="2027-01-01 09:00:00"; end_at="2027-01-01 12:00:00"; registration_open_at="2026-12-01 09:00:00"; registration_close_at="2026-12-30 17:00:00"; status="draft"; visibility="public"; waitlist_enabled="0"; certificate_enabled="0" }}
)

$tests = $tests | ForEach-Object {
    Write-Host ""
    Write-Host ("=== CREATE: " + $_.name + " ===")
    $r = Do-Create $_.url $_.csrfUrl $_.fields
    Write-Host ("  http_status=" + $r.status + " loc=" + $r.loc + " err=" + $r.err)
    Write-Host ("  body=" + $r.body.Substring(0, [Math]::Min(200, $r.body.Length)))
    Write-Host ("  body=" + $r.body.Substring(0, [Math]::Min(800, $r.body.Length)))
    }
    $_.createResult = $r
    $_
}

Write-Host ""
Write-Host "=== SUMMARY ==="
$pass = 0; $fail = 0; $list = @()
foreach ($t in $tests) {
    $line = "{0,-40} status={1} loc={3} err={4}" -f $t.name, $t.createResult.status, '', $t.createResult.loc, $t.createResult.err
    Write-Host $line
    if ($t.createResult.ok) { $pass++ } else { $fail++ }
    $list += [pscustomobject]@{ name=$t.name; url=$t.url; status=$t.createResult.status; loc=$t.createResult.loc; err=$t.createResult.err; ok=$t.createResult.ok }
}
Write-Host ""
Write-Host ("CREATE pass=$pass fail=$fail of " + $tests.Count)

# Persist
$list | ConvertTo-Json -Depth 3 | Out-File -FilePath $LOG
Write-Host "Saved: $LOG"
