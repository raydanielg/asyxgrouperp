$files = Get-ChildItem -Path 'resources\views\roles' -Recurse -Filter '*.blade.php'
foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw
    if ($content -match "@section\('title'.*?@endsection") {
        $newContent = $content -replace "(?s)(@section\('title',[^\r\n]+)\r?\n@endsection", '$1'
        [System.IO.File]::WriteAllText($file.FullName, $newContent)
        Write-Host "Fixed: $($file.FullName)"
    }
}
