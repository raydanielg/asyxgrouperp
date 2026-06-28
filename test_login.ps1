try {
    $r = Invoke-WebRequest -Uri 'http://localhost:8080/login' -UseBasicParsing -MaximumRedirection 0 -ErrorAction Stop
    Write-Host "Status: $($r.StatusCode)"
    Write-Host "ContentLength: $($r.Content.Length)"
    Write-Host "First 200 chars:"
    Write-Host $r.Content.Substring(0, [Math]::Min(200, $r.Content.Length))
} catch {
    $status = $_.Exception.Response.StatusCode
    Write-Host "Status: $status"
    $location = $_.Exception.Response.Headers.Location
    Write-Host "Location: $location"
}
