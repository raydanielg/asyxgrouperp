$files = Get-ChildItem -Path 'resources\views' -Filter '*.blade.php' -Recurse
foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw
    $original = $content

    # Pattern: @if($cond1)text1@elseif($cond2)text2@else text3@endif
    # Replace with: {{ ($cond1) ? 'text1' : ($cond2) ? 'text2' : 'text3' }}
    $regex = '@if\(([^)]+)\)(.+?)@elseif\(([^)]+)\)(.+?)@else(.+?)@endif'
    while ($content -match $regex) {
        $cond1 = $matches[1].Trim()
        $text1 = $matches[2].Trim()
        $cond2 = $matches[3].Trim()
        $text2 = $matches[4].Trim()
        $text3 = $matches[5].Trim()
        $replacement = "{{ ($cond1) ? '$text1' : ($cond2) ? '$text2' : '$text3' }}"
        $content = $content -replace [regex]::Escape($matches[0]), $replacement
    }

    # Pattern: @if($cond)text1@else text2@endif (no elseif)
    $regex2 = '@if\(([^)]+)\)(.+?)@else(.+?)@endif'
    while ($content -match $regex2) {
        $cond = $matches[1].Trim()
        $text1 = $matches[2].Trim()
        $text2 = $matches[3].Trim()
        $replacement = "{{ ($cond) ? '$text1' : '$text2' }}"
        $content = $content -replace [regex]::Escape($matches[0]), $replacement
    }

    # Pattern: @if($cond)text@endif (no else)
    $regex3 = '@if\(([^)]+)\)(.+?)@endif'
    while ($content -match $regex3) {
        $cond = $matches[1].Trim()
        $text = $matches[2].Trim()
        $replacement = "{{ ($cond) ? '$text' : '' }}"
        $content = $content -replace [regex]::Escape($matches[0]), $replacement
    }

    if ($content -ne $original) {
        Set-Content -Path $file.FullName -Value $content -NoNewline
        Write-Host "Fixed: $($file.Name)"
    }
}
Write-Host "Done!"
