<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Public routes for Shield Auth
service('auth')->routes($routes);

// Default redirect
$routes->get('/', 'Home::index');

// Protected admin routes
$routes->group('', ['filter' => 'session'], static function ($routes) {
    // Dashboard
    $routes->get('dashboard', 'Dashboard::index');
    
    // Data Tamu CRUD and tracking
    $routes->group('tamu', static function ($routes) {
        $routes->get('', 'Tamu::index');
        $routes->get('detail/(:num)', 'Tamu::detail/$1');
        $routes->post('update-status/(:num)', 'Tamu::updateStatus/$1');
        
        // Manual input form for Admin
        $routes->get('input', 'Tamu::inputManual');
        $routes->post('input', 'Tamu::storeManual');
    });

    // Agenda CRUD
    $routes->group('agenda', static function ($routes) {
        $routes->get('', 'Agenda::index');
        $routes->get('create', 'Agenda::create');
        $routes->post('store', 'Agenda::store');
        $routes->get('edit/(:num)', 'Agenda::edit/$1');
        $routes->post('update/(:num)', 'Agenda::update/$1');
        $routes->get('delete/(:num)', 'Agenda::delete/$1');
    });

    // Laporan & Export
    $routes->group('laporan', static function ($routes) {
        $routes->get('', 'Laporan::index');
        $routes->get('pdf', 'Laporan::exportPdf');
        $routes->get('excel', 'Laporan::exportExcel');
    });

    // Master Pegawai (Superadmin & Admin)
    $routes->group('pegawai', static function ($routes) {
        $routes->get('', 'Pegawai::index');
        $routes->get('create', 'Pegawai::create');
        $routes->post('store', 'Pegawai::store');
        $routes->get('edit/(:num)', 'Pegawai::edit/$1');
        $routes->post('update/(:num)', 'Pegawai::update/$1');
        $routes->post('delete/(:num)', 'Pegawai::delete/$1');
    });

    // Superadmin-only routes
    $routes->group('', ['filter' => 'group:superadmin'], static function ($routes) {
        // Manajemen User
        $routes->group('users', static function ($routes) {
            $routes->get('', 'Users::index');
            $routes->get('create', 'Users::create');
            $routes->post('store', 'Users::store');
            $routes->get('edit/(:num)', 'Users::edit/$1');
            $routes->post('update/(:num)', 'Users::update/$1');
            $routes->get('reset-password/(:num)', 'Users::resetPassword/$1');
            $routes->post('reset-password/(:num)', 'Users::saveResetPassword/$1');
            $routes->get('toggle-status/(:num)', 'Users::toggleStatus/$1');
        });

        // Master Data OPD, Bagian, Subbagian
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

        // Audit Log
        $routes->get('audit', 'Audit::index');
    });
});

// Public Guest Registration (via QR Code)
$routes->group('tamu', static function ($routes) {
    $routes->get('agenda/(:any)', 'Tamu::selfService/$1');
    $routes->post('agenda/(:any)/store', 'Tamu::storeSelfService/$1');
    $routes->get('konfirmasi/(:any)', 'Tamu::konfirmasi/$1');
});

// AJAX APIs (Searchable selects for employees/sections)
$routes->get('api/bagian/(:any)', 'Api::getBagian/$1');
$routes->get('api/subbagian/(:any)/(:any)', 'Api::getSubbagian/$1/$2');
$routes->get('api/pegawai/(:any)/(:any)', 'Api::getPegawai/$1/$2');
$routes->get('api/pegawai-all', 'Api::getPegawaiAll');
