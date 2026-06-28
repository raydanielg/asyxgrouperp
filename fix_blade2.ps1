$files = Get-ChildItem -Path 'resources\views' -Filter '*.blade.php' -Recurse
$patterns = @(
    '@if\(',
    '@elseif\(',
    '@else',
    '@endif',
    '@foreach\(',
    '@endforeach',
    '@forelse\(',
    '@empty',
    '@endforelse',
    '@php',
    '@endphp',
    '@unless\(',
    '@endunless',
    '@isset\(',
    '@endisset',
    '@empty\(',
    '@endempty',
    '@auth',
    '@endauth',
    '@guest',
    '@endguest',
    '@switch\(',
    '@case\(',
    '@break',
    '@default',
    '@endswitch',
    '@once',
    '@endonce',
    '@error\(',
    '@enderror',
    '@json\(',
    '@csrf',
    '@method\(',
    '@yield\(',
    '@section\(',
    '@endsection',
    '@extends\(',
    '@include\(',
    '@stack\(',
    '@push\(',
    '@endpush',
    '@props\('
)
foreach ($file in $files) {
    $lines = Get-Content $file.FullName
    $newLines = @()
    $changed = $false
    foreach ($line in $lines) {
        $tempLine = $line
        # Skip lines that are purely a Blade directive
        $trimmed = $tempLine.Trim()
        $isPureDirective = $false
        foreach ($p in $patterns) {
            if ($trimmed -match "^$p" -and $trimmed -notmatch '<[a-zA-Z]') {
                $isPureDirective = $true
                break
            }
        }
        if ($isPureDirective) {
            $newLines += $line
            continue
        }
        # Check if line has both HTML and Blade directives
        $hasBlade = $false
        foreach ($p in $patterns) {
            if ($tempLine -match $p) { $hasBlade = $true; break }
        }
        if (-not $hasBlade) {
            $newLines += $line
            continue
        }
        # We need to split Blade directives onto separate lines
        # Replace @if( with newline + @if( etc, but be careful not to break {{ }} or @csrf in forms
        $result = $tempLine
        # Only split if there are structural directives mixed with HTML
        $structuralPatterns = @('@if\(', '@elseif\(', '@else\b', '@endif\b', '@foreach\(', '@endforeach\b', '@forelse\(', '@empty\b', '@endforelse\b', '@unless\(', '@endunless\b', '@isset\(', '@endisset\b', '@switch\(', '@case\(', '@break\b', '@default\b', '@endswitch\b', '@once\b', '@endonce\b', '@auth\b', '@endauth\b', '@guest\b', '@endguest\b', '@error\(', '@enderror\b')
        $hasStructural = $false
        foreach ($p in $structuralPatterns) {
            if ($result -match $p) { $hasStructural = $true; break }
        }
        if (-not $hasStructural) {
            $newLines += $line
            continue
        }
        # Add newline before each structural directive (except at start of line)
        foreach ($p in $structuralPatterns) {
            $result = [regex]::Replace($result, "([^\s])\s*$p", "`$1`n        " + ($p -replace '\\b', '').Replace('\(', '(').Replace('\s', ''))
        }
        # This is getting too complex. Let's use a simpler approach.
        # Just add newlines around known directives
        $result = $tempLine
        $result = $result -replace '(?<!^)(?<!\n\s*)@if\(', "`n        @if("
        $result = $result -replace '@endif(?!\s*[a-zA-Z])', "@endif`n        "
        $result = $result -replace '(?<!^)(?<!\n\s*)@foreach\(', "`n        @foreach("
        $result = $result -replace '@endforeach(?!\s*[a-zA-Z])', "@endforeach`n        "
        $result = $result -replace '(?<!^)(?<!\n\s*)@forelse\(', "`n        @forelse("
        $result = $result -replace '@endforelse(?!\s*[a-zA-Z])', "@endforelse`n        "
        $result = $result -replace '(?<!^)(?<!\n\s*)@elseif\(', "`n        @elseif("
        $result = $result -replace '(?<!^)(?<!\n\s*)@else\b', "`n        @else`n        "
        $result = $result -replace '(?<!^)(?<!\n\s*)@empty\b', "`n        @empty`n        "
        $result = $result -replace '(?<!^)(?<!\n\s*)@php\b', "`n        @php`n        "
        $result = $result -replace '@endphp\b', "`n        @endphp`n        "
        $splitLines = $result -split "`n"
        $cleanedLines = @()
        foreach ($sl in $splitLines) {
            $cleaned = $sl.TrimEnd()
            if ($cleaned -ne '') { $cleanedLines += $cleaned }
        }
        if ($cleanedLines.Count -gt 1) {
            $newLines += $cleanedLines
            $changed = $true
        } else {
            $newLines += $line
        }
    }
    if ($changed) {
        $output = $newLines -join "`n"
        Set-Content -Path $file.FullName -Value $output -NoNewline
        Write-Host "Fixed: $($file.Name)"
    }
}
Write-Host "Done!"
