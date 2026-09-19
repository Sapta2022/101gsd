#!/usr/bin/env bash
# Prints one random 64-char hex secret — use it as a GitHub secret value
# (STAGING_DEPLOY_HOOK_SECRET / LIVE_DEPLOY_HOOK_SECRET) and paste the same
# value into that server's deploy/deploy-hook.config.php. Run it twice for
# two different secrets (staging and live must NOT share one).
set -euo pipefail
php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
