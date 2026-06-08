<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get('assets/(:any)', static function($path) {
    $filePath = FCPATH . 'assets/' . $path;
    if (!file_exists($filePath)) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException();
    }
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $mimes = [
        'css'   => 'text/css',
        'js'    => 'application/javascript',
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'gif'   => 'image/gif',
        'svg'   => 'image/svg+xml',
        'ico'   => 'image/x-icon',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'   => 'font/ttf',
        'eot'   => 'application/vnd.ms-fontobject',
        'webp'  => 'image/webp',
        'mp4'   => 'video/mp4',
        'pdf'   => 'application/pdf',
    ];
    $mime = $mimes[$ext] ?? mime_content_type($filePath);
    header('Content-Type: ' . $mime);
    header('Content-Length: ' . filesize($filePath));
    header('Cache-Control: public, max-age=86400');
    readfile($filePath);
    exit;
});

$routes->get('flogin/(:any)', static function($path) {
    $filePath = FCPATH . 'flogin/' . $path;
    if (!file_exists($filePath)) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException();
    }
    $mime = mime_content_type($filePath);
    header('Content-Type: ' . $mime);
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit;
});

$routes->get('favicon.ico', static function() {
    $filePath = FCPATH . 'favicon.ico';
    if (!file_exists($filePath)) exit;
    header('Content-Type: image/x-icon');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit;
});



// Public routes for Shield Auth
service('auth')->routes($routes);

// Default redirect
$routes->get('/', 'Home::index');

// PUBLIC GUEST REGISTRATION
$routes->group('tamu', static function ($routes) {
    $routes->get('agenda/(:any)', 'Tamu::selfService/$1');
    $routes->post('agenda/(:any)/store', 'Tamu::storeSelfService/$1');
    $routes->get('register-umum', 'Tamu::registerUmum');
    $routes->post('register-umum/store', 'Tamu::storeRegisterUmum');
    $routes->get('register-umum/(:any)/(:any)', 'Tamu::registerUmum/$1/$2');
    $routes->post('register-umum/(:any)/(:any)/store', 'Tamu::storeRegisterUmum/$1/$2');
    $routes->get('konfirmasi/(:any)', 'Tamu::konfirmasi/$1');
    $routes->get('uploads/(:segment)/(:segment)/(:segment)/(:segment)', 'Tamu::serveUpload/$1/$2/$3/$4');
});

// Pegawai Portal - Public routes
$routes->get('pegawai-portal/login', 'PegawaiAuth::login');
$routes->post('pegawai-portal/login', 'PegawaiAuth::attemptLogin');
$routes->get('pegawai-portal/logout', 'PegawaiAuth::logout');

// AJAX APIs
$routes->get('api/bagian/(:any)', 'Api::getBagian/$1');
$routes->get('api/subbagian/(:any)/(:any)', 'Api::getSubbagian/$1/$2');
$routes->get('api/pegawai/(:any)', 'Api::getPegawaiByOpd/$1');
$routes->get('api/pegawai/(:any)/(:any)', 'Api::getPegawai/$1/$2');
$routes->get('api/pegawai/(:any)/(:any)/(:any)', 'Api::getPegawaiBySubbagian/$1/$2/$3');
$routes->get('api/pegawai-filtered', 'Api::getPegawaiFiltered');
$routes->get('api/pegawai-all', 'Api::getPegawaiAll');
$routes->get('api/pegawai-by-nip/(:any)', 'Api::getPegawaiByNip/$1');

// PROTECTED ADMIN ROUTES
$routes->group('', ['filter' => 'session'], static function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('dashboard/sync-simpelgan', 'Dashboard::syncSimpelgan');
    $routes->get('ganti-password', 'Dashboard::changePassword');
    $routes->post('ganti-password', 'Dashboard::saveChangePassword');

    $routes->group('tamu', static function ($routes) {
        $routes->get('', 'Tamu::index');
        $routes->get('detail/(:any)', 'Tamu::detail/$1');
        $routes->post('update-status/(:any)', 'Tamu::updateStatus/$1');
        $routes->get('qr-umum', 'Tamu::qrUmum');
        $routes->get('input', 'Tamu::inputManual');
        $routes->post('input', 'Tamu::storeManual');
    });

    $routes->group('agenda', static function ($routes) {
        $routes->get('', 'Agenda::index');
        $routes->get('create', 'Agenda::create');
        $routes->post('store', 'Agenda::store');
        $routes->get('edit/(:any)', 'Agenda::edit/$1');
        $routes->post('update/(:any)', 'Agenda::update/$1');
        $routes->post('delete/(:any)', 'Agenda::delete/$1');
        $routes->get('complete/(:any)', 'Agenda::complete/$1');
    });

    $routes->group('laporan', static function ($routes) {
        $routes->get('', 'Laporan::index');
        $routes->get('pdf', 'Laporan::exportPdf');
        $routes->get('excel', 'Laporan::exportExcel');
    });

    $routes->group('pegawai', static function ($routes) {
        $routes->get('', 'Pegawai::index');
        $routes->get('create', 'Pegawai::create');
        $routes->post('store', 'Pegawai::store');
        $routes->get('edit/(:any)', 'Pegawai::edit/$1');
        $routes->post('update/(:any)', 'Pegawai::update/$1');
        $routes->post('delete/(:any)', 'Pegawai::delete/$1');
        $routes->get('set-password/(:any)', 'Pegawai::setPassword/$1');
        $routes->post('save-password/(:any)', 'Pegawai::savePassword/$1');
    });

    $routes->group('', ['filter' => 'group:superadmin'], static function ($routes) {
        $routes->group('users', static function ($routes) {
            $routes->get('', 'Users::index');
            $routes->get('create', 'Users::create');
            $routes->post('store', 'Users::store');
            $routes->get('edit/(:any)', 'Users::edit/$1');
            $routes->post('update/(:any)', 'Users::update/$1');
            $routes->get('reset-password/(:any)', 'Users::resetPassword/$1');
            $routes->post('reset-password/(:any)', 'Users::saveResetPassword/$1');
            $routes->get('toggle-status/(:any)', 'Users::toggleStatus/$1');
        });

        $routes->group('master', static function ($routes) {
            $routes->get('opd', 'Master::opdIndex');
            $routes->post('opd/store', 'Master::opdStore');
            $routes->post('opd/update/(:any)', 'Master::opdUpdate/$1');
            $routes->post('opd/delete/(:any)', 'Master::opdDelete/$1');
            $routes->get('bagian', 'Master::bagianIndex');
            $routes->post('bagian/store', 'Master::bagianStore');
            $routes->post('bagian/update/(:any)/(:any)', 'Master::bagianUpdate/$1/$2');
            $routes->post('bagian/delete/(:any)/(:any)', 'Master::bagianDelete/$1/$2');
            $routes->get('subbagian', 'Master::subbagianIndex');
            $routes->post('subbagian/store', 'Master::subbagianStore');
            $routes->post('subbagian/update/(:any)/(:any)/(:any)', 'Master::subbagianUpdate/$1/$2/$3');
            $routes->post('subbagian/delete/(:any)/(:any)/(:any)', 'Master::subbagianDelete/$1/$2/$3');
        });

        $routes->get('audit', 'Audit::index');
    });
});

$routes->group('pegawai-portal', ['filter' => 'pegawai-auth'], static function ($routes) {
    $routes->get('dashboard', 'PegawaiPortal::dashboard');
    $routes->get('ganti-password', 'PegawaiPortal::changePassword');
    $routes->post('ganti-password', 'PegawaiPortal::saveChangePassword');
    $routes->get('tamu', 'PegawaiPortal::tamu');
    $routes->get('tamu/detail/(:any)', 'PegawaiPortal::detail/$1');
    $routes->post('tamu/konfirmasi/(:any)', 'PegawaiPortal::konfirmasiTamu/$1');
    $routes->post('tamu/update-status/(:any)', 'PegawaiPortal::updateStatus/$1');
});