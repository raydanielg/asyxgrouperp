$files = Get-ChildItem -Path 'resources\views' -Filter '*.blade.php' -Recurse
foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw
    $original = $content
    # Add newline before structural Blade directives that follow HTML
    $content = $content -replace '>(\s*)@if\(', ">`n        @if("
    $content = $content -replace '>(\s*)@foreach\(', ">`n        @foreach("
    $content = $content -replace '>(\s*)@forelse\(', ">`n        @forelse("
    $content = $content -replace '>(\s*)@elseif\(', ">`n        @elseif("
    $content = $content -replace '>(\s*)@else\b', ">`n        @else"
    $content = $content -replace '>(\s*)@empty\b', ">`n        @empty"
    $content = $content -replace '>(\s*)@endif\b', ">`n        @endif"
    $content = $content -replace '>(\s*)@endforeach\b', ">`n        @endforeach"
    $content = $content -replace '>(\s*)@endforelse\b', ">`n        @endforelse"
    # Add newline after structural Blade directives that are followed by HTML
    $content = $content -replace '@endif\)(\s*)<', "@endif)`n        <"
    $content = $content -replace '@endforeach(\s*)<', "@endforeach`n        <"
    $content = $content -replace '@endforelse(\s*)<', "@endforelse`n        <"
    $content = $content -replace '@else(\s*)<', "@else`n        <"
    $content = $content -replace '@empty(\s*)<', "@empty`n        <"
    # Fix @if(...) right before < tag
    $content = $content -replace '@if\(([^)]+)\)(\s*)<', "@if(`$1)`n        <"
    $content = $content -replace '@foreach\(([^)]+)\)(\s*)<', "@foreach(`$1)`n        <"
    $content = $content -replace '@forelse\(([^)]+)\)(\s*)<', "@forelse(`$1)`n        <"
    $content = $content -replace '@elseif\(([^)]+)\)(\s*)<', "@elseif(`$1)`n        <"
    # Clean up extra blank lines
    $content = $content -replace "(\r?\n){3,}", "`n`n"
    if ($content -ne $original) {
        Set-Content -Path $file.FullName -Value $content -NoNewline
        Write-Host "Fixed: $($file.Name)"
    }
}
Write-Host "Done!"
