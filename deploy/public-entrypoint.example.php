<?php

/**
 * The actual HTTP entrypoint for the deploy hook — copy this file BY HAND
 * into public/ on each server (staging and live each need their own copy),
 * under a name only you know, e.g.:
 *
 *   cp deploy/public-entrypoint.example.php public/dh-<random>.php
 *
 * Do this once per server during setup, the same way you create
 * deploy/deploy-hook.config.php. This file is deliberately NOT part of the
 * git repo's public/ folder and is never touched by an automated deploy —
 * if it were redeployed every time under a fixed name, that name would
 * become guessable again even though the HMAC check still protects it.
 *
 * Why it can't live directly in deploy/: only files under public/ are
 * reachable over HTTP on a typical cPanel Laravel setup (the subdomain's
 * document root points at .../public, and deploy/ sits one level above
 * it, alongside vendor/ and app/ — not web-servable). This file is a two
 * line bridge; all the real logic — signature check, replay check, the
 * Artisan commands — lives in deploy/hook.php, outside the web root.
 */

require __DIR__.'/../deploy/hook.php';
