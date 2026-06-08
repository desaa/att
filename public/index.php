<?php

use CodeIgniter\Boot;
use Config\Paths;

/*
 *---------------------------------------------------------------
 * CHECK PHP VERSION
 *---------------------------------------------------------------
 */
$minPhpVersion = '8.2';
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
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

/*
 *---------------------------------------------------------------
 * FORCE SESSION & ENVIRONMENT BEFORE BOOT
 * Override .env values yang bermasalah di shared hosting
 *---------------------------------------------------------------
 */
$SESSION_PATH = '/home/d1sk0m1nv0/public_html/maincek/writable/session';

// Pastikan folder ada
if (!is_dir($SESSION_PATH)) {
    mkdir($SESSION_PATH, 0755, true);
}

// Override via semua cara yang mungkin dibaca CI4
putenv('session.savePath=' . $SESSION_PATH);
$_ENV['session.savePath']    = $SESSION_PATH;
$_SERVER['session.savePath'] = $SESSION_PATH;

// Override CI_ENVIRONMENT agar debugbar tidak aktif
putenv('CI_ENVIRONMENT=production');
$_ENV['CI_ENVIRONMENT']    = 'production';
$_SERVER['CI_ENVIRONMENT'] = 'production';

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 */
require FCPATH . '../app/Config/Paths.php';
$paths = new Paths();

require $paths->systemDirectory . '/Boot.php';

exit(Boot::bootWeb($paths));