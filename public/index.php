<?php

use CodeIgniter\Boot;
use Config\Paths;

/*
 *---------------------------------------------------------------
 * POST CONTENT-LENGTH EXCEEDED GUARD
 *---------------------------------------------------------------
 * When a POST request exceeds PHP's post_max_size, PHP discards
 * $_POST & $_FILES, and outputs an internal warning at Unknown:0.
 * If output buffering is off, headers are immediately sent, causing
 * CodeIgniter's Session to fatal-crash on ini_set(). We catch this
 * condition early, clean any output buffer, and present a graceful response.
 */
if (isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') {
    $contentLength = isset($_SERVER['CONTENT_LENGTH']) ? (int) $_SERVER['CONTENT_LENGTH'] : 0;
    if ($contentLength > 0 && empty($_POST) && empty($_FILES)) {
        $postMaxSize = (string) ini_get('post_max_size');
        $multiplier  = match (strtolower(substr(trim($postMaxSize), -1))) {
            'g'     => 1024 * 1024 * 1024,
            'm'     => 1024 * 1024,
            'k'     => 1024,
            default => 1,
        };
        $maxBytes = (int) ((float) $postMaxSize * $multiplier);

        if ($maxBytes > 0 && $contentLength > $maxBytes) {
            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            if (! headers_sent()) {
                http_response_code(413);
                header('Content-Type: text/html; charset=UTF-8');
            }

            $uploadedMb = round($contentLength / (1024 * 1024), 2);
            $limitMb    = round($maxBytes / (1024 * 1024), 0);

            echo '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ukuran Berkas Melebihi Batas | BegawiPengadaan</title>
    <link rel="stylesheet" href="/assets/main/css/bootstrap.min.css">
    <style>
        body { background: #f8fafc; font-family: system-ui, -apple-system, sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px; margin: 0; }
        .error-card { max-width: 560px; background: #ffffff; border-radius: 16px; padding: 36px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; text-align: center; }
        .icon-circle { width: 72px; height: 72px; border-radius: 50%; background: #fee2e2; color: #dc2626; display: inline-flex; align-items: center; justify-content: center; font-size: 32px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="icon-circle">&#9888;</div>
        <h1 class="h4 fw-bold text-dark mb-2">Ukuran Berkas Melebihi Batas Maksimal</h1>
        <p class="text-muted small mb-3">
            Berkas atau data yang Anda kirimkan berukuran <strong>' . htmlspecialchars((string) $uploadedMb) . ' MB</strong>, melebihi kapasitas maksimal unggahan server yaitu <strong>' . htmlspecialchars((string) $limitMb) . ' MB</strong>.
        </p>
        <div class="p-3 bg-light rounded-3 text-start small text-secondary mb-4 border">
            <strong>Saran Solusi:</strong>
            <ul class="mb-0 ps-3 mt-1">
                <li>Gunakan berkas dokumen (PDF/DOC) dengan ukuran di bawah ' . htmlspecialchars((string) $limitMb) . ' MB.</li>
                <li>Lakukan kompresi berkas PDF menggunakan alat kompresi dokumen resmi.</li>
            </ul>
        </div>
        <button type="button" onclick="history.back();" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold">
            &larr; Kembali ke Formulir
        </button>
    </div>
</body>
</html>';
            exit;
        }
    }
}

/*
 *---------------------------------------------------------------
 * CHECK PHP VERSION
 *---------------------------------------------------------------
 */

$minPhpVersion = '8.2'; // If you update this, don't forget to update `spark`.
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION,
    );

    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo $message;

    exit(1);
}

/*
 *---------------------------------------------------------------
 * SET THE CURRENT DIRECTORY
 *---------------------------------------------------------------
 */

// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Ensure the current directory is pointing to the front controller's directory
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 * This process sets up the path constants, loads and registers
 * our autoloader, along with Composer's, loads our constants
 * and fires up an environment-specific bootstrapping.
 */

// LOAD OUR PATHS CONFIG FILE
// This is the line that might need to be changed, depending on your folder structure.
require FCPATH . '../app/Config/Paths.php';
// ^^^ Change this line if you move your application folder

$paths = new Paths();

// LOAD THE FRAMEWORK BOOTSTRAP FILE
require $paths->systemDirectory . '/Boot.php';

exit(Boot::bootWeb($paths));
