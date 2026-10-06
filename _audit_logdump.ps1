$lines = Get-Content writable\logs\log-2026-10-05.log
$current = ''
foreach ($l in $lines) {
  if ($l -match '^[A-Z]+ - \d') {
    if ($current -match '15:2[23]' -and $current -match 'ERROR|CRITICAL|Exception' -and $current -notmatch 'FileHandler|Session:') {
      Write-Host ($current.Substring(0, [Math]::Min(500, $current.Length)))
      Write-Host '---'
    }
    $current = $l
  } else {
    $current = $current + ' ' + $l.Trim()
  }
}
