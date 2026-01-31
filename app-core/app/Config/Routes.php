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
$routes->post('/login', 'Auth::login');
$routes->get('/register', 'Home::register');
$routes->get('/logout', 'Auth::logout');
$routes->post('/register/masjid', 'Auth::registerMasjid');
$routes->post('/register/jamaah', 'Auth::registerJamaah');
$routes->get('dashboard', 'Admin::index');
$routes->get('dashboard/profil', 'Admin::profil');
$routes->post('dashboard/profil', 'Admin::updateProfile');
$routes->get('dashboard/regencies/(:num)', 'Admin::getRegencies/$1');
$routes->get('dashboard/users/search', 'Admin::searchUsers');
$routes->post('dashboard/pengurus/add', 'Admin::addPengurus');
$routes->post('dashboard/pengurus/update', 'Admin::updatePengurus');
$routes->post('dashboard/pengurus/delete', 'Admin::deletePengurus');
$routes->get('dashboard/program', 'Admin::program');
$routes->get('dashboard/berita', 'Admin::berita');
$routes->get('dashboard/keuangan', 'Admin::keuangan');
