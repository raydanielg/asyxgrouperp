#!/usr/bin/env bash
# auto_push.sh — watch repo, commit & push changes automatically (macOS/Linux)
# Usage: ./scripts/auto_push.sh [-i seconds] [-m "message"] [--once] [--skip-fetch]
set -u

INTERVAL=15
MSG="autosave"
ONCE=false
SKIP_FETCH=false
BRANCH="$(git rev-parse --abbrev-ref HEAD 2>/dev/null || echo main)"

while [ $# -gt 0 ]; do
  case "$1" in
    -i|--interval) INTERVAL="$2"; shift 2 ;;
    -m|--message)  MSG="$2"; shift 2 ;;
    --once)        ONCE=true; shift ;;
    --skip-fetch)  SKIP_FETCH=true; shift ;;
    *)             echo "Unknown option: $1"; exit 1 ;;
  esac
done

run_push() {
  if [ -z "$(git status --porcelain)" ]; then
    echo "[auto_push] No changes. Waiting..."
    return
  fi

  echo "[auto_push] Changes detected. Committing + pushing..."
  git add .

  if [ -z "$(git diff --cached --name-only)" ]; then
    echo "[auto_push] Nothing staged after git add. Skipping."
    return
  fi

  git commit -m "$MSG"

  [ "$SKIP_FETCH" = false ] && git fetch origin
  git pull --rebase "origin" "$BRANCH"
  git push origin "$BRANCH"
}

if [ "$ONCE" = true ]; then
  run_push
  exit 0
fi

echo "[auto_push] Running on branch '$BRANCH'. Poll interval: ${INTERVAL}s. Ctrl+C to stop."

while true; do
  run_push || echo "[auto_push] Error occurred — continuing..."
  sleep "$INTERVAL"
done
