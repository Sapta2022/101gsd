<?php

/**
 * Copy this file to deploy-hook.config.php, next to the hook script itself,
 * directly on the server (staging and live each need their own copy with
 * their own random secret). Never commit the real file — it's already in
 * .gitignore.
 *
 * Generate a secret with:
 *   php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
 */

return [
    'secret'   => 'REPLACE-WITH-A-LONG-RANDOM-VALUE',

    // Path to this release's Laravel application root (the folder that
    // contains artisan, bootstrap/, vendor/) — usually one level up from
    // wherever you placed this deploy/ folder.
    'app_path' => __DIR__ . '/../',

    // Reject any request whose X-Deploy-Timestamp is more than this many
    // seconds away from the server's own clock.
    'max_skew' => 300,
];
