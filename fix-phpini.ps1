$f = 'C:\Program Files\PHP\current\php.ini'
$c = [IO.File]::ReadAllText($f)
$c = $c.Replace(';extension=openssl', 'extension=openssl')
$c = $c.Replace(';extension=sqlite3', 'extension=sqlite3')
$c = $c.Replace(';extension=pdo_sqlite', 'extension=pdo_sqlite')
$c = $c.Replace(';extension=fileinfo', 'extension=fileinfo')
$c = $c.Replace(';extension=mbstring', 'extension=mbstring')
$c = $c.Replace(';extension=curl', 'extension=curl')
[IO.File]::WriteAllText($f, $c)
Write-Host "Done"
