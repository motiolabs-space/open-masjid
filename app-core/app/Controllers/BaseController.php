<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = ['custom'];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = service('session');
    }

    /**
     * Pembatas laju berbasis `Throttler` CI4 (penyimpanan: cache berkas).
     *
     * Dipakai rute yang bisa disalahgunakan berulang-ulang tanpa biaya bagi
     * penyerang: form publik tanpa login, percobaan kata sandi, dan API
     * ber-token. Satu jatah per `$aksi` + `$identitas`, sehingga membatasi satu
     * hal tak ikut mengunci yang lain.
     *
     * @param  string $aksi      Nama jatah, mis. 'login' atau 'rsvp'.
     * @param  string $identitas Pembeda pemakai; kosong = alamat IP pemanggil.
     * @return bool              false bila jatah sudah habis.
     */
    protected function lolosBatasLaju(string $aksi, int $maks = 5, int $detik = MINUTE, string $identitas = ''): bool
    {
        return service('throttler')->check($this->kunciBatasLaju($aksi, $identitas), $maks, $detik) !== false;
    }

    /**
     * Kembalikan jatah sebuah aksi ke penuh.
     *
     * Dipakai setelah percobaan yang SAH berhasil (mis. login benar), supaya
     * pengguna yang sekadar salah ketik beberapa kali tidak ikut terkunci oleh
     * jatah yang sebetulnya ditujukan untuk penebak kata sandi.
     */
    protected function resetBatasLaju(string $aksi, string $identitas = ''): void
    {
        service('throttler')->remove($this->kunciBatasLaju($aksi, $identitas));
    }

    private function kunciBatasLaju(string $aksi, string $identitas): string
    {
        // Di-hash: identitas bisa berupa email atau token, dan keduanya tak
        // pantas tersimpan apa adanya sebagai nama berkas cache.
        return 'batas-' . $aksi . '-' . md5($identitas !== '' ? $identitas : (string) $this->request->getIPAddress());
    }
}
