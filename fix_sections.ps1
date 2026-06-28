$files = Get-ChildItem -Path 'resources\views\roles' -Recurse -Filter '*.blade.php'
$count = 0
foreach ($file in $files) {
    $lines = Get-Content $file.FullName
    if ($lines.Count -ge 3 -and $lines[2].Trim() -eq '@endsection' -and $lines[1].Trim().StartsWith("@section('title'")) {
        $newLines = $lines[0..1]
        if ($lines.Count -gt 3) {
            $newLines += $lines[3..($lines.Count - 1)]
        }
        [System.IO.File]::WriteAllText($file.FullName, ($newLines -join "`r`n"))
        Write-Host "Fixed: $($file.Name)"
        $count++
    }
}
Write-Host "Total fixed: $count"
