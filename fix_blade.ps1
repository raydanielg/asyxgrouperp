$files = Get-ChildItem -Path 'resources\views' -Filter '*.blade.php' -Recurse
foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw
    if ($content -notmatch '@empty') { continue }
    $changed = $false
    # Fix </tr>@empty pattern
    if ($content -match '</tr>@empty') {
        $content = $content -replace '</tr>@empty', "`n        </tr>`n        @empty`n        "
        $changed = $true
    }
    # Fix </div>@empty pattern
    if ($content -match '</div>@empty') {
        $content = $content -replace '</div>@empty', "`n        </div>`n        @empty`n        "
        $changed = $true
    }
    # Fix </p>@empty pattern
    if ($content -match '</p>@empty') {
        $content = $content -replace '</p>@empty', "`n        </p>`n        @empty`n        "
        $changed = $true
    }
    # Fix @endforelse</tbody> pattern
    if ($content -match '@endforelse</tbody>') {
        $content = $content -replace '@endforelse</tbody>', "`n        @endforelse`n        </tbody>"
        $changed = $true
    }
    # Fix @endforelse</div> pattern
    if ($content -match '@endforelse</div>') {
        $content = $content -replace '@endforelse</div>', "`n        @endforelse`n        </div>"
        $changed = $true
    }
    # Fix <tr> right after @empty on same line
    if ($content -match '@empty\s*<tr>') {
        $content = $content -replace '@empty\s*<tr>', "@empty`n        <tr>"
        $changed = $true
    }
    # Fix <p> right after @empty on same line
    if ($content -match '@empty\s*<p>') {
        $content = $content -replace '@empty\s*<p>', "@empty`n        <p>"
        $changed = $true
    }
    # Fix </tr>@endforelse pattern (without @empty case)
    if ($content -match '</tr>@endforelse') {
        $content = $content -replace '</tr>@endforelse', "</tr>`n        @endforelse"
        $changed = $true
    }
    # Fix </div>@endforelse pattern
    if ($content -match '</div>@endforelse') {
        $content = $content -replace '</div>@endforelse', "</div>`n        @endforelse"
        $changed = $true
    }
    # Fix </p>@endforelse pattern
    if ($content -match '</p>@endforelse') {
        $content = $content -replace '</p>@endforelse', "</p>`n        @endforelse"
        $changed = $true
    }
    if ($changed) {
        Set-Content -Path $file.FullName -Value $content -NoNewline
        Write-Host "Fixed: $($file.FullName)"
    }
}
Write-Host "Done!"
