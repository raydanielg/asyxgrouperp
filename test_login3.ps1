# Test login flow with .NET HttpClient
Add-Type -AssemblyName System.Net.Http
$client = New-Object System.Net.Http.HttpClient
$handler = New-Object System.Net.Http.HttpClientHandler
$handler.AllowAutoRedirect = $false
$client = New-Object System.Net.Http.HttpClient($handler)

$baseUrl = 'http://localhost:8080'

# Step 1: GET /login
$resp1 = $client.GetAsync("$baseUrl/login").Result
Write-Host "Step 1 - GET /login: $([int]$resp1.StatusCode)"
$html = $resp1.Content.ReadAsStringAsync().Result

# Extract CSRF token
if ($html -match 'name="csrf-token" content="([^"]+)"') {
    $csrf = $matches[1]
    Write-Host "CSRF: $($csrf.Substring(0,10))..."
}

# Step 2: POST /login with wrong credentials
$content = New-Object System.Net.Http.FormUrlEncodedContent(@([System.Collections.Generic.KeyValuePair[string,string]]::new('_token', $csrf), [System.Collections.Generic.KeyValuePair[string,string]]::new('email', 'test@example.com'), [System.Collections.Generic.KeyValuePair[string,string]]::new('password', 'wrongpass')))
$req = New-Object System.Net.Http.HttpRequestMessage('POST', "$baseUrl/login")
$req.Content = $content
$req.Headers.Referrer = New-Object System.Uri("$baseUrl/login")
$resp2 = $client.SendAsync($req).Result
Write-Host "Step 2 - POST /login: $([int]$resp2.StatusCode)"
$locHeader = $resp2.Headers.Location
if ($locHeader) { Write-Host "Location: $locHeader" } else { Write-Host "No Location header" }

# Step 3: Try with correct admin credentials from seeder
$content2 = New-Object System.Net.Http.FormUrlEncodedContent(@([System.Collections.Generic.KeyValuePair[string,string]]::new('_token', $csrf), [System.Collections.Generic.KeyValuePair[string,string]]::new('email', 'admin@asyxgroup.com'), [System.Collections.Generic.KeyValuePair[string,string]]::new('password', 'password')))
$req2 = New-Object System.Net.Http.HttpRequestMessage('POST', "$baseUrl/login")
$req2.Content = $content2
$req2.Headers.Referrer = New-Object System.Uri("$baseUrl/login")
$resp3 = $client.SendAsync($req2).Result
Write-Host "Step 3 - POST /login (admin): $([int]$resp3.StatusCode)"
$locHeader2 = $resp3.Headers.Location
if ($locHeader2) { Write-Host "Location: $locHeader2" } else { Write-Host "No Location header" }
