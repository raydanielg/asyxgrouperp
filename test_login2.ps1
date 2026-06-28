# Test login flow
$baseUrl = 'http://localhost:8080'

# Step 1: GET /login to get CSRF token and cookies
$session = New-Object Microsoft.PowerShell.Commands.WebRequestSession
$resp1 = Invoke-WebRequest -Uri "$baseUrl/login" -UseBasicParsing -WebSession $session
Write-Host "Step 1 - GET /login: Status $($resp1.StatusCode)"

# Extract CSRF token
$html = $resp1.Content
if ($html -match 'name="csrf-token" content="([^"]+)"') {
    $csrf = $matches[1]
    Write-Host "CSRF token found: $($csrf.Substring(0,10))..."
} else {
    Write-Host "CSRF token NOT found!"
    exit
}

# Also check for hidden _token field
if ($html -match 'name="_token" value="([^"]+)"') {
    $formToken = $matches[1]
    Write-Host "Form _token found: $($formToken.Substring(0,10))..."
} else {
    $formToken = $csrf
    Write-Host "Using csrf-token as form token"
}

# Step 2: POST /login with test credentials
$headers = @{'X-CSRF-TOKEN' = $csrf; 'Referer' = "$baseUrl/login"}
$body = @{_token = $formToken; email = 'test@example.com'; password = 'wrongpassword'}
try {
    $resp2 = Invoke-WebRequest -Uri "$baseUrl/login" -Method POST -Body $body -Headers $headers -WebSession $session -MaximumRedirection 0 -ErrorAction Stop
    Write-Host "Step 2 - POST /login: Status $($resp2.StatusCode)"
} catch {
    $status = $_.Exception.Response.StatusCode
    $location = $_.Exception.Response.Headers.Location
    Write-Host "Step 2 - POST /login: Status $status"
    Write-Host "Redirect Location: $location"
}
