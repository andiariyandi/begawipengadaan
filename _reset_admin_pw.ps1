$PASSWORD = 'Spandupat24'
$hash = (& 'C:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe' -r "echo password_hash('$PASSWORD', PASSWORD_DEFAULT);").Trim()
Write-Host ("NEW HASH: " + $hash)
& 'C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe' -h 127.0.0.1 -u root db_begawipbj --execute ("UPDATE users SET password_hash='" + $hash + "' WHERE email='admin@begawipbj.go.id';")
& 'C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe' -h 127.0.0.1 -u root db_begawipbj --execute "SELECT id, email, LEFT(password_hash, 20) AS hash_prefix FROM users WHERE email='admin@begawipbj.go.id';"