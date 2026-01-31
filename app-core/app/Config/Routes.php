<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('fitur', 'Home::fitur');
$routes->get('kebaikan', 'Home::kebaikan');
$routes->get('tentang', 'Home::tentang');
$routes->get('laporan', 'Home::laporan');
$routes->get('bantuan', 'Home::panduan');
$routes->get('kontak', 'Home::kontak');
$routes->get('privacy-policy', 'Home::privacy');
$routes->get('term', 'Home::term');
$routes->get('/login', 'Home::login');
$routes->get('/register', 'Home::register');
$routes->get('/logout', 'Auth::logout');
$routes->post('/register/masjid', 'Auth::registerMasjid');
$routes->post('/register/jamaah', 'Auth::registerJamaah');
$routes->get('dashboard', 'Admin::index');
$routes->get('dashboard/profil', 'Admin::profil');
$routes->get('dashboard/program', 'Admin::program');
$routes->get('dashboard/berita', 'Admin::berita');
$routes->get('dashboard/keuangan', 'Admin::keuangan');
