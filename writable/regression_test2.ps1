$ProgressPreference = SILENTLYCONTINUE

$base = "http://127.0.0.1:8888/index.php/regulasi"
$hdr  = @{"User-Agent"="Mozilla/5.0"}

function Test-Url {
    param([string]$url, [string]$label)
    try {
        $r = Invoke-WebRequest -Uri $url -UseBasicParsing -TimeoutSec 15 -Headers $hdr
        $status = $r.StatusCode
        $size   = $r.Content.Length
        $body   = $r.Content
        $ctype  = ($r.Headers["Content-Type"] | Select-Object -First 1)
        $loc    = ($r.Headers["Location"] | Select-Object -First 1)
        $title  = ([regex]::Match($body, "<title>([^<]+)</title>")).Groups[1].Value
        $cards  = ([regex]::Matches($body, "course-one__single")).Count
        $danger = ([regex]::Matches($body, "alert alert-danger")).Count
        $line   = "{0,-50} | {1} | {2,6} | {3,4} | {4,3} | loc={5}" -f $label, $status, $size, $title, $cards, $loc
        Write-Host $line -ForegroundColor Cyan
        return @{Label=$label; Status=$status; Size=$size; Title=$title; Cards=$cards; Location=$loc; ContentType=$ctype; Body=$body}
    } catch {
        Write-Host ("{0,-50} | ERR: {1}" -f $label, $_) -ForegroundColor Red
    }
}

$r0 = Invoke-WebRequest -Uri $base -UseBasicParsing -Headers $hdr
$landing = $r0.Content
$slugs = [regex]::Matches($landing, "href=""[^""]*/regulasi/([^""/?#]+)""") | ForEach-Object { $_.Groups[1].Value } | Sort-Object -Unique
Write-Host ("SLUGS: " + ($slugs -join ", ")) -ForegroundColor Magenta

Write-Host ""
Write-Host "====== TEST 1: SEARCH ======" -ForegroundColor Yellow
foreach ($q in @("Perpres","12","2021","pengadaan","Nomor","Manajemen","tidak-ada-x","A UI")) {
    $u = $base + "?q=" + [uri]::EscapeDataString($q)
    $r = Invoke-WebRequest -Uri $u -UseBasicParsing -Headers $hdr
    $cards = ([regex]::Matches($r.Content, "course-one__single")).Count
    $empty = $r.Content -match "Belum ada regulasi"
    $title = ([regex]::Match($r.Content, "<title>([^<]+)</title>")).Groups[1].Value
    Write-Host ("q=" + ("{0,-16}" -f $q) + " | " + $r.StatusCode + " | size=" + ("{0,6}" -f $r.Content.Length) + " | cards=" + ("{0,3}" -f $cards) + " | empty=" + $empty + " | title=" + $title)
}

Write-Host ""
Write-Host "====== TEST 2: DETAIL ======" -ForegroundColor Yellow
foreach ($s in $slugs) {
    Test-Url "$base/$s" "detail/$s" | Out-Null
}
Test-Url ("$base/nonexistent-slug-zzz") "detail/nonexistent" | Out-Null

Write-Host ""
Write-Host "====== TEST 3: VERSI ======" -ForegroundColor Yellow
foreach ($s in $slugs) {
    Test-Url "$base/$s/versi" "versi/$s" | Out-Null
}

Write-Host ""
Write-Host "====== TEST 4: RELASI ======" -ForegroundColor Yellow
foreach ($s in $slugs) {
    Test-Url "$base/$s/relasi" "relasi/$s" | Out-Null
}

Write-Host ""
Write-Host "====== TEST 5: PREVIEW ======" -ForegroundColor Yellow
foreach ($s in $slugs) {
    try {
        $r = Invoke-WebRequest -Uri "$base/$s/preview" -UseBasicParsing -TimeoutSec 10 -Headers $hdr
        $ctype = ($r.Headers["Content-Type"] | Select-Object -First 1)
        $body  = $r.Content
        $isPdf = ($body -match "^%PDF-") -or ($ctype -match "pdf")
        $cd    = ($r.Headers["Content-Disposition"] | Select-Object -First 1)
        Write-Host ("preview/" + ("{0,-25}" -f $s) + " | " + $r.StatusCode + " | ct=" + $ctype + " | size=" + ("{0,6}" -f $body.Length) + " | pdf=" + $isPdf) -ForegroundColor Cyan
    } catch {
        Write-Host ("preview/" + ("{0,-25}" -f $s) + " | ERR: " + $_) -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "====== TEST 6: UNDUH ======" -ForegroundColor Yellow
foreach ($s in $slugs) {
    try {
        $r = Invoke-WebRequest -Uri "$base/$s/unduh" -UseBasicParsing -TimeoutSec 10 -Headers $hdr
        $ctype = ($r.Headers["Content-Type"] | Select-Object -First 1)
        $body  = $r.Content
        $cd    = ($r.Headers["Content-Disposition"] | Select-Object -First 1)
        $isBinary = ($body -match "^%PDF-") -or ($ctype -match "pdf|octet") -or $cd
        Write-Host ("unduh/" + ("{0,-25}" -f $s) + " | " + $r.StatusCode + " | ct=" + $ctype + " | size=" + ("{0,6}" -f $body.Length) + " | cd=" + ($cd -as [string]) + " | binary=" + $isBinary) -ForegroundColor Cyan
    } catch {
        Write-Host ("unduh/" + ("{0,-25}" -f $s) + " | ERR: " + $_) -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "====== TEST 7: CATEGORY INVALID 404 ======" -ForegroundColor Yellow
Test-Url "$base/kategori/nonexistent-category-zzz-999" "cat/NONEXISTENT/nonexistent-category-zzz-999" | Out-Null
Test-Url "$base/kategori/tidak-ada-xyz" "cat/NONEXISTENT/tidak-ada-xyz" | Out-Null
$cats = [regex]::Matches($landing, "href=""[^""]*/regulasi/kategori/([^""/?#]+)""") | ForEach-Object { $_.Groups[1].Value } | Sort-Object -Unique
Write-Host ("CATS: " + ($cats -join ", ")) -ForegroundColor Magenta
foreach ($c in $cats) {
    Test-Url "$base/kategori/$c" "cat/$c" | Out-Null
}

Write-Host ""
Write-Host "====== TEST 8: STATISTICS ======" -ForegroundColor Yellow
$od = [regex]::Matches($landing, "class=""odometer""[^>]*data-count=""([^""]+)""")
Write-Host "Odometer counters:" -ForegroundColor Cyan
foreach ($o in $od) { Write-Host ("  value = " + $o.Groups[1].Value) -ForegroundColor Cyan }
$regSection = ([regex]::Match($landing, "Regulasi Terbaru([\s\S]*?)(?=<section|</body>)")).Groups[1].Value
$regInSection = ([regex]::Matches($regSection, "course-one__single")).Count
$popSection = ([regex]::Match($landing, "Regulasi Populer([\s\S]*?)(?=<section|</body>)")).Groups[1].Value
$popCards = ([regex]::Matches($popSection, "course-one__single")).Count
$popEmpty = $popSection -match "Belum ada data"
$kompetSection = ([regex]::Match($landing, "Kompetensi Terkait([\s\S]*?)(?=<section|</body>)")).Groups[1].Value
$kompetCards = ([regex]::Matches($kompetSection, "course-one__single")).Count
Write-Host ("Reg cards in Regulasi Terbaru section: " + $regInSection) -ForegroundColor Cyan
Write-Host ("Reg cards in Regulasi Populer section: " + $popCards + " (empty=" + $popEmpty + ")") -ForegroundColor Cyan
Write-Host ("Komp cards in Kompetensi Terkait section: " + $kompetCards) -ForegroundColor Cyan

Write-Host ""
Write-Host "====== DONE ======" -ForegroundColor Yellow
