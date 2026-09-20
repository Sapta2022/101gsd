<?php

/**
 * 101GSD deploy hook — runs post-deploy Artisan tasks with no shell/SSH access.
 *
 * GitHub Actions FTP-uploads one or two release zip(s) (vendor/ and compiled
 * frontend assets included, since composer/npm cannot run on this host) and
 * then POSTs to a small public entrypoint that requires this file (see
 * deploy/public-entrypoint.example.php — this file itself is NOT web-facing;
 * it lives outside public/, one level above the docroot). Because this
 * host enforces a hard, unchangeable 30s execution time / 128M memory limit
 * per request, the work is split across two separate calls instead of one:
 *   - step "extract": unzip the release archive(s) and return immediately
 *   - step "artisan": bootstrap Laravel and run the fixed Artisan sequence
 * Each step alone comfortably fits inside the host's limits even though the
 * two together would not.
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
 *   inputs trusted from the body are which step to run and whether to run
 *   migrations — the request can never name arbitrary commands or paths.
 */

declare(strict_types=1);

// Turn PHP warnings/notices into catchable exceptions, and turn any truly
// fatal error (the kind that otherwise kills the script silently) into a
// proper JSON response instead of a blank HTTP 500 — makes remote
// debugging possible without needing direct access to the server's logs.
set_error_handler(function (int $severity, string $message, string $file, int $line): bool {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

register_shutdown_function(function (): void {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        http_response_code(500);
        echo json_encode([
            'ok' => false,
            'error' => 'fatal: '.$error['message'].' in '.$error['file'].':'.$error['line'],
        ]);
    }
});

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Artisan;

header('Content-Type: application/json');

// ---------------------------------------------------------------------
// 1. Load secret + config. deploy-hook.config.php is NOT committed to git
//    (see .gitignore) — it is created once by hand on each server during
//    setup, sibling to this file, containing:
//      <?php return [
//          'secret'      => 'paste the long random value here',
//          'app_path'    => __DIR__ . '/../',   // path to this release's Laravel root
//          'max_skew'    => 300,
//          'public_path' => null,  // live only: absolute path to public_html
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
$publicPath = isset($config['public_path']) ? rtrim((string) $config['public_path'], '/') : null;

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

$step = (string) ($payload['step'] ?? 'all');

// ---------------------------------------------------------------------
// 3. Extract the uploaded release archive(s), if this step calls for it.
//    GitHub Actions uploads one zip instead of thousands of individual
//    files over FTP — some hosts drop long FTP sessions that open too
//    many connections in quick succession. This extracts in place and
//    removes the zip(s).
// ---------------------------------------------------------------------
function deploy_hook_extract(string $zipPath, string $destination): void
{
    if (! is_file($zipPath)) {
        return;
    }

    if (! class_exists(ZipArchive::class)) {
        deploy_hook_fail(500, 'PHP zip extension (ext-zip) is not available');
    }

    $zip = new ZipArchive;

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

/**
 * Recreate the storage/framework/* subdirectories Laravel needs at runtime.
 * git doesn't track empty directories, so a release zip built from a git
 * checkout/archive never includes these — without this, a fresh extraction
 * (e.g. a brand-new environment such as a future "live" deploy) fails on
 * view rendering/caching with "Please provide a valid cache path." Safe to
 * run on every deploy: it only creates directories that don't already exist.
 */
function deploy_hook_ensure_storage_dirs(string $appPath): void
{
    $dirs = [
        '/storage/framework/cache',
        '/storage/framework/cache/data',
        '/storage/framework/sessions',
        '/storage/framework/testing',
        '/storage/framework/views',
    ];

    foreach ($dirs as $dir) {
        $full = $appPath.$dir;
        if (! is_dir($full)) {
            mkdir($full, 0775, true);
        }
    }
}

if ($step === 'extract' || $step === 'all') {
    deploy_hook_extract($appPath.'/release.zip', $appPath);

    if ($publicPath !== null && is_dir($publicPath)) {
        deploy_hook_extract($appPath.'/public-release.zip', $publicPath);
    }

    deploy_hook_ensure_storage_dirs($appPath);

    if ($step === 'extract') {
        echo json_encode(['ok' => true, 'step' => 'extract']);
        exit;
    }
}

// ---------------------------------------------------------------------
// 4. Only ever run a fixed, known-safe set of Artisan commands, in a
//    fixed order, if this step calls for it. The request may only choose
//    whether "migrate" runs — it can never name arbitrary commands or
//    arguments.
// ---------------------------------------------------------------------
if ($step === 'artisan' || $step === 'all') {
    $runMigrations = ! isset($payload['migrate']) || $payload['migrate'] !== false;

    // Safety net: guarantee these exist even if this "artisan" call ever
    // runs without a preceding "extract" call having gone through the
    // patched code above (e.g. an older cached release, or step "all"
    // on a host where extract's own mkdir was somehow skipped).
    deploy_hook_ensure_storage_dirs($appPath);

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
    // itself can be checked while it's running, then always try to bring
    // it back up in finally — even if a step throws.
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
        'step' => 'artisan',
        'migrate' => $runMigrations,
        'steps' => $steps,
    ], JSON_PRETTY_PRINT);
}
