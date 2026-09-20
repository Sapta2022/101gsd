<?php

/**
 * 101GSD deploy hook — runs post-deploy Artisan tasks with no shell/SSH access.
 *
 * GitHub Actions FTP-uploads the built release (vendor/ and node_modules build
 * output included, since composer/npm cannot run on this host) and then POSTs
 * to a small public entrypoint that requires this file (see
 * deploy/public-entrypoint.example.php — this file itself is NOT web-facing;
 * it lives outside public/, one level above the docroot). This script
 * bootstraps Laravel in-process and calls Artisan commands as PHP function
 * calls — no exec()/shell_exec() needed, so it works even when those are
 * disabled on shared hosting.
 *
 * SECURITY
 * - The public entrypoint that requires this file is created by hand under
 *   an unguessable name during setup — see public-entrypoint.example.php.
 * - Every request must carry a valid HMAC-SHA256 signature over
 *   "<timestamp>.<raw body>", keyed with the secret from
 *   deploy-hook.config.php, in the "X-Deploy-Signature" header
 *   ("sha256=<hex>"), plus an "X-Deploy-Timestamp" header within max_skew
 *   seconds of the server's clock, to stop replay of a captured request.
 * - Never build this file's paths from anything in the request. The only
 *   input trusted from the body is whether to run migrations, and even
 *   that only toggles a step in the fixed, hard-coded sequence below.
 */

declare(strict_types=1);
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Artisan;

header('Content-Type: application/json');

// ---------------------------------------------------------------------
// 1. Load secret + config. deploy-hook.config.php is NOT committed to git
//    (see .gitignore) — it is created once by hand on each server during
//    setup, sibling to this file, containing:
//      <?php return [
//          'secret'   => 'paste the long random value here',
//          'app_path' => __DIR__ . '/../',   // path to this release's Laravel root
//          'max_skew' => 300,
//      ];
// ---------------------------------------------------------------------
$configFile = __DIR__.'/deploy-hook.config.php';

if (! is_file($configFile)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'deploy-hook.config.php is missing on this server']);
    exit;
}

$config = require $configFile;

$secret = (string) ($config['secret'] ?? '');
$appPath = rtrim((string) ($config['app_path'] ?? ''), '/');
$maxSkew = (int) ($config['max_skew'] ?? 300);

if ($secret === '' || $appPath === '' || ! is_dir($appPath)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'deploy hook is misconfigured']);
    exit;
}

// ---------------------------------------------------------------------
// 2. Verify signature + freshness before touching anything else.
// ---------------------------------------------------------------------
$raw = file_get_contents('php://input') ?: '';
$sigHeader = $_SERVER['HTTP_X_DEPLOY_SIGNATURE'] ?? '';
$tsHeader = $_SERVER['HTTP_X_DEPLOY_TIMESTAMP'] ?? '';

function deploy_hook_fail(int $code, string $message): never
{
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $message]);
    exit;
}

if ($sigHeader === '' || $tsHeader === '' || ! ctype_digit($tsHeader)) {
    deploy_hook_fail(401, 'missing signature or timestamp');
}

if (abs(time() - (int) $tsHeader) > $maxSkew) {
    deploy_hook_fail(401, 'timestamp outside allowed window');
}

$expected = 'sha256='.hash_hmac('sha256', $tsHeader.'.'.$raw, $secret);

if (! hash_equals($expected, $sigHeader)) {
    deploy_hook_fail(401, 'invalid signature');
}

$payload = json_decode($raw, true);
if (! is_array($payload)) {
    $payload = [];
}

// ---------------------------------------------------------------------
// 2.5. Extract the uploaded release archive(s), if present. GitHub Actions
//      uploads one zip instead of thousands of individual files over FTP —
//      some hosts drop long FTP sessions that open too many connections in
//      quick succession. This extracts in place and removes the zip(s).
// ---------------------------------------------------------------------
function deploy_hook_extract(string $zipPath, string $destination): void
{
    if (! is_file($zipPath)) {
        return;
    }

    $zip = new ZipArchive();

    if ($zip->open($zipPath) !== true) {
        deploy_hook_fail(500, "failed to open {$zipPath}");
    }

    if (! $zip->extractTo($destination)) {
        $zip->close();
        deploy_hook_fail(500, "failed to extract {$zipPath}");
    }

    $zip->close();
    @unlink($zipPath);
}

deploy_hook_extract($appPath.'/release.zip', $appPath);

// Live only: a second archive containing just the public/ folder's
// contents, extracted to public_path instead (see deploy-hook.config.php).
$publicPath = isset($config['public_path']) ? rtrim((string) $config['public_path'], '/') : null;

if ($publicPath !== null && is_dir($publicPath)) {
    deploy_hook_extract($appPath.'/public-release.zip', $publicPath);
}

// ---------------------------------------------------------------------
// 3. Only ever run a fixed, known-safe set of Artisan commands, in a
//    fixed order. The request may only choose whether "migrate" runs
//    (staging/live both migrate by default; leave room to skip it for a
//    hotfix that touches no schema) — it can never name arbitrary
//    commands or arguments.
// ---------------------------------------------------------------------
$runMigrations = ! isset($payload['migrate']) || $payload['migrate'] !== false;

chdir($appPath);
require $appPath.'/vendor/autoload.php';

/** @var Application $app */
$app = require $appPath.'/bootstrap/app.php';

$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$steps = [];
$failed = false;

/**
 * Run one Artisan command, capture its exit code and output, and stop
 * the whole deploy (leaving the app in maintenance mode) on first failure.
 */
$run = function (string $command, array $arguments = []) use (&$steps, &$failed): void {
    if ($failed) {
        return;
    }

    $exit = Artisan::call($command, $arguments);
    $out = Artisan::output();

    $steps[] = [
        'command' => $command,
        'exit' => $exit,
        'output' => trim($out),
    ];

    if ($exit !== 0) {
        $failed = true;
    }
};

// Put the app into maintenance mode with a bypass secret so the deploy
// itself can be checked while it's running, then always try to bring it
// back up in finally — even if a step throws.
$maintenanceSecret = bin2hex(random_bytes(8));

try {
    $run('down', [
        '--secret' => $maintenanceSecret,
        '--retry' => 60,
    ]);

    if ($runMigrations) {
        $run('migrate', ['--force' => true]);
    }

    $run('config:cache');
    $run('route:cache');
    $run('view:cache');
    $run('event:cache');

    if (is_dir($appPath.'/storage/app/public') && ! is_link($appPath.'/public/storage')) {
        $run('storage:link');
    }

    $run('queue:restart');
} finally {
    // Bring the site back up no matter what happened above.
    Artisan::call('up');
    $steps[] = ['command' => 'up', 'exit' => 0, 'output' => ''];
}

http_response_code($failed ? 500 : 200);
echo json_encode([
    'ok' => ! $failed,
    'migrate' => $runMigrations,
    'steps' => $steps,
], JSON_PRETTY_PRINT);
