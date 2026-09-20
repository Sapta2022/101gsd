#!/usr/bin/env bash
# Signs and sends one request to the deploy hook.
# Usage: call-deploy-hook.sh <migrate: true|false> <step: extract|artisan|all>
set -euo pipefail

migrate="${1:-true}"
step="${2:-all}"

body=$(printf '{"migrate":%s,"step":"%s"}' "$migrate" "$step")
timestamp=$(date +%s)
signature="sha256=$(printf '%s.%s' "$timestamp" "$body" | openssl dgst -sha256 -hmac "$DEPLOY_HOOK_SECRET" | sed 's/^.* //')"

response=$(curl -sS -w '\n%{http_code}' -X POST "$DEPLOY_HOOK_URL" \
  -H "Content-Type: application/json" \
  -H "X-Deploy-Timestamp: $timestamp" \
  -H "X-Deploy-Signature: $signature" \
  -d "$body")

http_code=$(echo "$response" | tail -n1)
body_out=$(echo "$response" | sed '$d')

echo "Deploy hook ($step) responded with HTTP $http_code:"
echo "$body_out"

if [ "$http_code" != "200" ]; then
  echo "::error::Deploy hook step '$step' reported a failure (HTTP $http_code) — check the response above and the server's storage/logs."
  exit 1
fi