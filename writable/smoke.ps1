$ErrorActionPreference = "Stop"  
$base = "http://begawipengadaan.test"  
$r0 = Invoke-WebRequest -Uri "$base/dashboard/masuk" -UseBasicParsing -SessionVariable sv  
Write-Host "LOGIN-PAGE: HTTP" $r0.StatusCode  
