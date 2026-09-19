#!/usr/bin/env bash
# Signs and sends the post-deploy request to deploy/hook.php on the server.
# Usage: call-deploy-hook.sh <migrate: true|false>
# Reads DEPLOY_HOOK_URL and DEPLOY_HOOK_SECRET from the environment.
set -euo pipefail

migrate="${1:-true}"

: "${DEPLOY_HOOK_URL:?DEPLOY_HOOK_URL is not set}"
: "${DEPLOY_HOOK_SECRET:?DEPLOY_HOOK_SECRET is not set}"

body=$(printf '{"migrate":%s}' "$migrate")
timestamp=$(date +%s)
signature="sha256=$(printf '%s.%s' "$timestamp" "$body" | openssl dgst -sha256 -hmac "$DEPLOY_HOOK_SECRET" | sed 's/^.* //')"

response_file=$(mktemp)
http_status=$(curl -sS -o "$response_file" -w '%{http_code}' \
  -X POST "$DEPLOY_HOOK_URL" \
  -H "Content-Type: application/json" \
  -H "X-Deploy-Signature: $signature" \
  -H "X-Deploy-Timestamp: $timestamp" \
  -d "$body")

echo "Deploy hook responded with HTTP $http_status:"
cat "$response_file"
echo

if [ "$http_status" -ne 200 ]; then
  echo "::error::Deploy hook reported a failure (HTTP $http_status) — the app was left in maintenance mode by the hook's own error handling; check the response above and the server's storage/logs."
  exit 1
fi
