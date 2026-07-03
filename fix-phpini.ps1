$f = 'C:\Program Files\PHP\current\php.ini'
$c = [IO.File]::ReadAllText($f)
$c = $c.Replace(';extension=openssl', 'extension=openssl')
[IO.File]::WriteAllText($f, $c)
Write-Host "Done"
