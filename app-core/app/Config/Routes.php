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
$routes->get('/auth/google', 'Auth::googleLogin');
$routes->get('/auth/google/callback', 'Auth::googleCallback');
$routes->get('/auth/check-username', 'Auth::checkUsername');
$routes->get('/auth/check-email', 'Auth::checkEmail');
$routes->post('/register/masjid', 'Auth::registerMasjid');
$routes->post('/register/jamaah', 'Auth::registerJamaah');
$routes->get('dashboard', 'Admin::index');
$routes->post('subscribe', 'Home::subscribe');
$routes->get('dashboard/followers', 'Admin::followers');
$routes->post('dashboard/followers/promote', 'Admin::promoteFollower');
$routes->get('dashboard/profil', 'Admin::profil');
$routes->post('dashboard/profil', 'Admin::updateProfile');
$routes->get('dashboard/regencies/(:num)', 'Admin::getRegencies/$1');
$routes->get('dashboard/users/search', 'Admin::searchUsers');
$routes->post('dashboard/pengurus/add', 'Admin::addPengurus');
$routes->post('dashboard/pengurus/update', 'Admin::updatePengurus');
$routes->post('dashboard/pengurus/delete', 'Admin::deletePengurus');
$routes->post('dashboard/gallery/upload', 'Admin::uploadGallery');
$routes->post('dashboard/gallery/delete', 'Admin::deleteGallery');
$routes->get('dashboard/program', 'Admin::program');
    // DKM LMS Routes
    $routes->get('dashboard/lms', 'Lms::index');
    $routes->get('dashboard/lms/module/(:segment)', 'Lms::module/$1');
    $routes->get('dashboard/lms/material/(:num)', 'Lms::material/$1');
    $routes->post('dashboard/lms/mark-completed/(:num)', 'Lms::markCompleted/$1');

    $routes->get('dashboard/settings', 'Admin::settings');

    // Virtual Auditor
    $routes->get('dashboard/auditor', 'VirtualAuditor::index');
    $routes->post('dashboard/auditor/run', 'VirtualAuditor::runAudit');

    // DKM Distribution & Mustahik Routes
    $routes->get('dashboard/distribution', 'Distribution::index');
    $routes->get('dashboard/distribution/mustahik/create', 'Distribution::createMustahik');
    $routes->get('dashboard/distribution/mustahik/edit/(:num)', 'Distribution::editMustahik/$1');
    $routes->post('dashboard/distribution/mustahik/save', 'Distribution::saveMustahik');
    $routes->post('dashboard/distribution/mustahik/delete/(:num)', 'Distribution::deleteMustahik/$1');
    $routes->post('dashboard/distribution/mustahik/rescore/(:num)', 'Distribution::generateScore/$1');

    $routes->get('dashboard/distribution/history', 'Distribution::history');
    $routes->get('dashboard/distribution/create', 'Distribution::createDistribution');
    $routes->get('dashboard/distribution/create/(:num)', 'Distribution::createDistribution/$1');
    $routes->post('dashboard/distribution/save', 'Distribution::saveDistribution');
$routes->get('dashboard/berita', 'Admin::berita');
$routes->get('dashboard/berita/create', 'Admin::createBerita');
$routes->get('dashboard/berita/edit/(:num)', 'Admin::editBerita/$1');
$routes->post('dashboard/berita/save', 'Admin::saveBerita');
$routes->post('dashboard/berita/delete', 'Admin::deleteBerita');
$routes->post('dashboard/berita/category/save', 'Admin::saveNewsCategory');
$routes->post('dashboard/berita/category/delete', 'Admin::deleteNewsCategory');
$routes->get('dashboard/program', 'Admin::program');
$routes->get('dashboard/program/create', 'Admin::createProgram');
$routes->get('dashboard/program/edit/(:num)', 'Admin::editProgram/$1');
$routes->post('dashboard/program/save', 'Admin::saveProgram');
$routes->get('dashboard/program/delete/(:num)', 'Admin::deleteProgram/$1');
$routes->post('dashboard/program/category/save', 'Admin::saveProgramCategory');
$routes->post('dashboard/program/category/delete', 'Admin::deleteProgramCategory');

$routes->get('dashboard/keuangan', 'Admin::keuangan');
$routes->get('dashboard/keuangan/mutasi', 'Admin::mutasi');
$routes->post('dashboard/keuangan/mutasi/upload', 'Admin::uploadMutasi');
$routes->post('dashboard/keuangan/mutasi/map', 'Admin::mapMutasi');
$routes->post('dashboard/keuangan/save', 'Admin::saveFinanceTransaction');
$routes->post('dashboard/keuangan/delete', 'Admin::deleteFinanceTransaction');
$routes->post('dashboard/keuangan/category/save', 'Admin::saveFinanceCategory');
$routes->post('dashboard/keuangan/category/delete', 'Admin::deleteFinanceCategory');

// Finance AI Features
$routes->get('dashboard/keuangan/import-csv', 'FinanceAI::importCSV');
$routes->post('dashboard/keuangan/import-csv/process', 'FinanceAI::processCSV');
$routes->get('dashboard/keuangan/review-csv', 'FinanceAI::reviewCSV');
$routes->post('dashboard/keuangan/import-csv/save', 'FinanceAI::saveCSV');
$routes->get('dashboard/keuangan/report', 'FinanceAI::generateReport');
$routes->post('dashboard/keuangan/report', 'FinanceAI::generateReport');

$routes->get('dashboard/warga', 'Admin::warga');
$routes->get('dashboard/warga/new', 'Admin::createWarga');
$routes->get('dashboard/warga/edit/(:num)', 'Admin::editWarga/$1');
$routes->post('dashboard/warga/save', 'Admin::saveWarga');
$routes->get('dashboard/warga/delete/(:num)', 'Admin::deleteWarga/$1');
$routes->get('dashboard/volunteers', 'Admin::volunteers');

// Broadcast Newsletter
$routes->get('dashboard/subscribers', 'Admin::subscribers');
$routes->get('dashboard/subscribers/delete/(:num)', 'Admin::deleteSubscriber/$1');
$routes->get('dashboard/broadcast', 'Admin::broadcasts');
$routes->get('dashboard/broadcast/new', 'Admin::createBroadcast');
$routes->post('dashboard/broadcast/send', 'Admin::sendBroadcast');

// Aid Distribution (Penyaluran)
$routes->get('dashboard/distribution', 'Admin::distributions');
$routes->get('dashboard/distribution/new', 'Admin::createDistribution');
$routes->get('dashboard/distribution/edit/(:num)', 'Admin::editDistribution/$1');
$routes->post('dashboard/distribution/save', 'Admin::saveDistribution');
$routes->get('dashboard/distribution/delete/(:num)', 'Admin::deleteDistribution/$1');

// Reporting (Laporan)
$routes->get('dashboard/reports', 'Admin::reports');
$routes->get('dashboard/reports/finance', 'Admin::generateFinanceReport');
$routes->get('dashboard/reports/program', 'Admin::generateProgramReport');
$routes->get('dashboard/reports/inventory', 'Admin::generateInventoryReport');
$routes->get('dashboard/reports/ai-generator', 'Admin::aiReportGenerator');
$routes->post('dashboard/reports/ai-generate', 'Admin::generateAiReport');
$routes->post('dashboard/reports/ai-publish', 'Admin::publishAiReport');

// Inventory Management
$routes->get('dashboard/inventory', 'Admin::inventory');
$routes->get('dashboard/inventory/new', 'Admin::createInventory');
$routes->get('dashboard/inventory/edit/(:num)', 'Admin::editInventory/$1');
$routes->post('dashboard/inventory/save', 'Admin::saveInventory');
$routes->get('dashboard/inventory/delete/(:num)', 'Admin::deleteInventory/$1');

// Payment Settings
$routes->get('dashboard/pembayaran', 'Admin::paymentSettings');
$routes->post('dashboard/pembayaran/save', 'Admin::savePaymentSettings');

// Schedule Management
$routes->get('dashboard/schedules', 'Admin::schedules');
$routes->get('dashboard/schedules/new', 'Admin::createSchedule');
$routes->get('dashboard/schedules/edit/(:num)', 'Admin::editSchedule/$1');
$routes->post('dashboard/schedules/save', 'Admin::saveSchedule');
$routes->get('dashboard/schedules/delete/(:num)', 'Admin::deleteSchedule/$1');

// Super Admin Dashboard
$routes->group('superadmin', ['filter' => 'dashboardGuard'], function($routes) {
    $routes->get('', 'SuperAdmin::index');
    $routes->get('/', 'SuperAdmin::index');
    $routes->get('masjid', 'SuperAdmin::masjid');
    $routes->get('users', 'SuperAdmin::users');
    $routes->get('users/analytics/(:num)', 'SuperAdmin::userAnalytics/$1');
    $routes->get('users/create', 'SuperAdmin::createUser');
    $routes->post('users/save', 'SuperAdmin::saveUser');
    $routes->get('users/edit/(:num)', 'SuperAdmin::editUser/$1');
    $routes->post('users/update/(:num)', 'SuperAdmin::updateUser/$1');
    $routes->post('users/delete/(:num)', 'SuperAdmin::deleteUser/$1');
    $routes->get('manage-masjid/(:num)', 'SuperAdmin::manageMasjid/$1');
    $routes->get('masjid/manage/(:num)', 'SuperAdmin::manageMasjid/$1'); // Backward compatibility / Alias
    $routes->get('masjid/edit/(:num)', 'SuperAdmin::editMasjid/$1');
    $routes->post('masjid/update/(:num)', 'SuperAdmin::updateMasjid/$1');
    $routes->post('masjid/delete/(:num)', 'SuperAdmin::deleteMasjid/$1');
    $routes->get('profile', 'SuperAdmin::profile');
    $routes->post('profile/password', 'SuperAdmin::updatePassword');
    
    // Superadmin Settings
    $routes->get('settings', 'SuperAdmin::settings');
    $routes->post('settings/save', 'SuperAdmin::saveSettings');

    // Superadmin LMS Routes
    $routes->get('lms', 'SuperAdmin::lmsModules');
    $routes->get('lms/create', 'SuperAdmin::createLmsModule');
    $routes->get('lms/edit/(:num)', 'SuperAdmin::editLmsModule/$1');
    $routes->post('lms/save', 'SuperAdmin::saveLmsModule');
    $routes->post('lms/delete/(:num)', 'SuperAdmin::deleteLmsModule/$1');
    
    $routes->get('lms/(:num)/materials', 'SuperAdmin::lmsMaterials/$1');
    $routes->get('lms/(:num)/materials/create', 'SuperAdmin::createLmsMaterial/$1');
    $routes->get('lms/materials/edit/(:num)', 'SuperAdmin::editLmsMaterial/$1');
    $routes->post('lms/materials/save', 'SuperAdmin::saveLmsMaterial');
    $routes->post('lms/materials/delete/(:num)', 'SuperAdmin::deleteLmsMaterial/$1');
});
$routes->get('auth/promote-me', 'Auth::promoteMe');
$routes->get('auth/select-masjid', 'Auth::selectMasjid');
$routes->get('auth/set-masjid/(:num)', 'Auth::setMasjidContext/$1');

// Donation & Payment
$routes->get('donation/(:segment)/form', 'Donation::create/$1');
$routes->get('donation/(:segment)/form/(:segment)', 'Donation::create/$1/$2'); // With Program Slug
$routes->post('donation/process', 'Donation::store');
$routes->get('donation/manual/(:segment)', 'Donation::manual/$1'); // Manual Payment Instruction

// Payment Simulation (Dummy)
$routes->get('payment/simulation/(:segment)', 'Payment::simulation/$1');
$routes->post('payment/callback', 'Payment::callback');
$routes->get('payment/success/(:segment)', 'Payment::success/$1');

// Telegram Webhook
$routes->post('api/telegram/webhook/(:any)', 'Api\Telegram::webhook/$1');

// Prevent asset paths from being captured by wildcard
$routes->get('(images|assets|uploads|css|js|fonts)/(:any)', function() {
    throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
});

// Public Profile (Catch-all)
$routes->get('(:any)/berita', 'Home::newsList/$1');
$routes->get('(:any)/berita/(:any)', 'Home::newsDetail/$1/$2');
$routes->get('(:any)/program', 'Home::programList/$1');
$routes->get('(:any)/program/(:any)', 'Home::programDetail/$1/$2');
$routes->get('(:any)/laporan', 'Home::publicReport/$1');
$routes->get('(:any)/display', 'Home::display/$1');
$routes->get('(:any)', 'Home::masjid/$1');
