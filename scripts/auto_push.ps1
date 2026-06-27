param(
  [int]$IntervalSeconds = 15,
  [string]$CommitMessage = "commit",
  [switch]$Once,
  [switch]$SkipFetch
)

$ErrorActionPreference = 'Stop'

function Test-LocalChanges {
  $status = git status --porcelain
  return -not [string]::IsNullOrWhiteSpace($status)
}

function Invoke-AutoCommitPush {
  if (-not (Test-LocalChanges)) {
    Write-Host "[auto_push] No changes. Waiting..."
    return
  }

  Write-Host "[auto_push] Changes detected. Committing + pushing..."
  git add .

  # In case there's nothing staged after add (rare), avoid failing the loop
  $cached = git diff --cached --name-only
  if ([string]::IsNullOrWhiteSpace($cached)) {
    Write-Host "[auto_push] Nothing staged after git add. Skipping."
    return
  }

  git commit -m $CommitMessage

  if (-not $SkipFetch) {
    git fetch origin
  }

  # Rebase to avoid non-fast-forward push errors
  git pull --rebase origin main
  git push origin main
}

if ($Once) {
  Invoke-AutoCommitPush
  exit 0
}

Write-Host "[auto_push] Running. Poll interval: $IntervalSeconds seconds. Press Ctrl+C to stop."

while ($true) {
  try {
    Invoke-AutoCommitPush
  } catch {
    Write-Host "[auto_push] Error: $($_.Exception.Message)"
  }

  Start-Sleep -Seconds $IntervalSeconds
}

 powershell -ExecutionPolicy Bypass -File .\scripts\auto_push.ps1 -IntervalSeconds 30 -CommitMessage "autosave"//