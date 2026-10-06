$BASE = 'http://begawipengadaan.test'
$DUMP = 'C:\laragon\www\begawipengadaan\_form_attrs'
New-Item -ItemType Directory -Path $DUMP -Force | Out-Null

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
Write-Host 'logged in'

$formUrls = @(
    @{mod='Arena Belajar';name='Program';url='/dashboard/pembelajaran/program/tambah'},
    @{mod='Arena Belajar';name='Course';url='/dashboard/pembelajaran/course/tambah'},
    @{mod='Arena Belajar';name='Kelas';url='/dashboard/pembelajaran/kelas/tambah'},
    @{mod='Arena Belajar';name='Materi dan Modul';url='/dashboard/pembelajaran/materi-modul/tambah'},
    @{mod='Bunga Rampai';name='Artikel';url='/dashboard/bunga-rampai/artikel/tambah'},
    @{mod='Bunga Rampai';name='FAQ';url='/dashboard/bunga-rampai/faq/tambah'},
    @{mod='Bunga Rampai';name='Studi Kasus';url='/dashboard/bunga-rampai/studi-kasus/tambah'},
    @{mod='Bunga Rampai';name='Template Dokumen';url='/dashboard/bunga-rapai/template-dokumen/tambah'},
    @{mod='Bunga Rampai';name='Media Galeri';url='/dashboard/bunga-rampai/media-galeri/tambah'},
    @{mod='Bunga Rampai';name='Carousel';url='/dashboard/bunga-rampai/carousel-konten-unggulan/tambah'},
    @{mod='Bunga Rampai';name='Kategori';url='/dashboard/bunga-rampai/kategori/tambah'},
    @{mod='Bunga Rampai';name='Tag';url='/dashboard/bunga-rampai/tag/tambah'},
    @{mod='Regulasi';name='Regulasi';url='/dashboard/regulasi/dokumen/tambah'},
    @{mod='Regulasi';name='Kategori';url='/dashboard/regulasi/kategori/tambah'},
    @{mod='Regulasi';name='Relasi';url='/dashboard/regulasi/relasi/tambah'},
    @{mod='Regulasi';name='Tahapan';url='/dashboard/regulasi/tahapan-pbj/tambah'},
    @{mod='Regulasi';name='Kompetensi';url='/dashboard/regulasi/kompetensi/tambah'},
    @{mod='Komunikasi';name='Berita';url='/dashboard/komunikasi/berita/tambah'},
    @{mod='Komunikasi';name='Kategori';url='/dashboard/komunikasi/kategori-berita/tambah'},
    @{mod='Komunikasi';name='Event';url='/dashboard/komunikasi/event-agenda/tambah'}
)

foreach ($m in $formUrls) {
    $r = [System.Net.HttpWebRequest]::Create("$BASE$($m.url)")
    $r.CookieContainer = $jar; $r.UserAgent = 'Mozilla/5.0'; $r.Timeout = 60000
    try {
        $resp = $r.GetResponse(); $rdr = New-Object System.IO.StreamReader($resp.GetResponseStream())
        $h = $rdr.ReadToEnd(); $resp.Close()
        $namePattern = '<(input|textarea|select)\s+[^>]*name="([^"]+)"[^>]*'
        $ms = [regex]::Matches($h, $namePattern)
        $set = New-Object PSObject
        $fields = @()
        foreach ($mm in $ms) { $fields += $mm.Groups[2].Value }
        $fields = ($fields | Sort-Object -Unique) | Where-Object { $_ -notmatch 'csrf_test|^_$|csrf_token' }
        $slug = ($m.mod + '-' + $m.name) -replace ' ', '_'
        Set-Content -Path "$DUMP\$slug.txt" -Value ($fields -join "`n")
        Write-Host ("=== " + $m.mod + " / " + $m.name + " ===")
        Write-Host ($fields -join ', ')
        Write-Host ''
    } catch { Write-Host ("ERROR: " + $m.url + " " + $_.Exception.Message) }
}