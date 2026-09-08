<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\MasjidModel;
use App\Models\MasjidPengurusModel;
use App\Libraries\TelegramLibrary;
use CodeIgniter\RESTful\ResourceController;

class Auth extends BaseController
{
    public function registerMasjid()
    {
        if (! $this->lolosBatasLaju('daftar-masjid', 3, 10 * MINUTE)) {
            return redirect()->back()->withInput()->with('error',
                'Terlalu banyak pendaftaran dari perangkat ini. Silakan tunggu beberapa menit.');
        }

        $userModel = new UserModel();
        $masjidModel = new MasjidModel();
        $pengurusModel = new MasjidPengurusModel();

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // 1. Create User (PIC)
            $ip = $this->request->getIPAddress();
            $userData = [
                'name'          => $this->request->getPost('nama_pic'),
                'email'         => $this->request->getPost('email_pic'),
                'phone'         => $this->request->getPost('phone_pic'),
                'password_hash' => password_hash($this->request->getPost('password_pic'), PASSWORD_DEFAULT),
                'role'          => 'user',
                'register_ip'   => $ip,
                'register_country' => $this->_getCountryFromIp($ip),
                // Asal kunjungan diambil dari cookie sentuhan pertama, bukan
                // dari alamat halaman formulir — orang jarang mendaftar pada
                // kunjungan yang sama saat ia pertama menemukan situs ini.
                ...\App\Libraries\Acquisition::untukPendaftaran(),
            ];
            $userId = $userModel->insert($userData);

            if (!$userId) {
                $errors = $userModel->errors();
                $errorMsg = !empty($errors) ? implode(', ', $errors) : "Gagal mendaftarkan user PIC (Email mungkin sudah terdaftar).";
                throw new \Exception($errorMsg);
            }

            // 2. Create Masjid
            $masjidData = [
                'name'     => $this->request->getPost('nama_masjid'),
                'username' => $this->request->getPost('username_masjid'),
                // Asal masjid ini ikut dicatat — inilah satuan yang paling
                // menentukan bagi GTM, bukan jumlah penggunanya.
                ...\App\Libraries\Acquisition::untukPendaftaran(),
            ];
            $masjidId = $masjidModel->insert($masjidData);

            if (!$masjidId) {
                $errors = $masjidModel->errors();
                $errorMsg = !empty($errors) ? implode(', ', $errors) : "Gagal mendaftarkan masjid.";
                throw new \Exception($errorMsg);
            }

            // 3. Link User to Masjid as Pengurus
            $pengurusData = [
                'masjid_id'  => $masjidId,
                'user_id'    => $userId,
                'role'       => 'admin',
                'title'      => 'Admin Utama',
                'is_creator' => 1
            ];
            $pengurusModel->insert($pengurusData);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat pendaftaran.');
            }

            // 4. Auto Login
            $session = session();
            $session->set([
                'isLoggedIn' => true,
                'user_id'    => $userId,
                'user_name'  => $userData['name'],
                'user_email' => $userData['email'],
                'role'            => 'pengurus',
                'masjid_id'       => $masjidId,
                'masjid_name'     => $masjidData['name'],
                'masjid_username' => $masjidData['username'],
            ]);

            // Email sambutan ke PIC. Tugasnya memancing langkah pertama —
            // mayoritas masjid mendaftar lalu tak pernah mengisi apa pun.
            $this->kirimSambutan(
                'welcome_masjid',
                $userData['email'],
                $userData['name'],
                'Halaman ' . $masjidData['name'] . ' sudah aktif di Masj.id',
                [
                    'nama'         => $userData['name'],
                    'namaMasjid'   => $masjidData['name'],
                    'urlMasjid'    => base_url($masjidData['username']),
                    'urlDashboard' => base_url('dashboard/profil'),
                    'urlContoh'    => base_url(\App\Database\Seeds\DemoMasjidSeeder::USERNAME),
                ]
            );

            // 4. Send Telegram Notification
            try {
                $userModel = new \App\Models\UserModel();
                $superAdmins = $userModel->where('role', 'superadmin')
                                         ->where('telegram_chat_id IS NOT NULL')
                                         ->findAll();
                
                if (!empty($superAdmins)) {
                    $telegram = new TelegramLibrary();
                    $msg = "<b>🆕 PENDAFTAR BARU!</b>\n\n";
                    $msg .= "Nama Masjid: <b>{$masjidData['name']}</b>\n";
                    $msg .= "Username: @{$masjidData['username']}\n";
                    $msg .= "PIC: {$userData['name']} ({$userData['phone']})\n";
                    $msg .= "Waktu: " . date('d M Y H:i:s') . "\n";
                    
                    foreach ($superAdmins as $admin) {
                        if (!empty($admin['telegram_chat_id'])) {
                            $telegram->setChatId($admin['telegram_chat_id'])->sendMessage($msg);
                        }
                    }
                }
            } catch (\Exception $te) {
                log_message('error', 'Failed to send Telegram notification: ' . $te->getMessage());
            }

            return redirect()->to('dashboard')->with('success', 'Pendaftaran masjid berhasil. Selamat datang!');

        } catch (\Exception $e) {
            $db->transRollback();
            // Log the actual error for debugging
            log_message('error', '[Registration Error] ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function login()
    {
        $userModel = new UserModel();
        $email = trim((string) $this->request->getPost('email'));
        $password = $this->request->getPost('password');

        // Dua jatah sekaligus, karena keduanya menutup celah yang berbeda:
        // per-IP menahan satu mesin menyapu banyak akun, per-email menahan satu
        // akun digempur dari banyak alamat (mis. lewat proxy). Keduanya baru
        // diperiksa saat kata sandi hendak dicocokkan.
        $lolosIp    = $this->lolosBatasLaju('login-ip', 10, MINUTE);
        $lolosEmail = $this->lolosBatasLaju('login-email', 5, 5 * MINUTE, strtolower($email));
        if (! $lolosIp || ! $lolosEmail) {
            return redirect()->back()->withInput()->with('error',
                'Terlalu banyak percobaan masuk. Silakan tunggu beberapa menit lalu coba lagi.');
        }

        $user = $userModel->where('email', $email)->first();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Jatah dikembalikan setelah berhasil: pengguna sah yang sempat
            // salah ketik tak boleh ikut terkunci oleh jatah penebak.
            $this->resetBatasLaju('login-ip');
            $this->resetBatasLaju('login-email', strtolower($email));

            return $this->processLogin($user);
        }

        return redirect()->back()->withInput()->with('error', 'Email atau password salah.');
    }

    public function registerJamaah()
    {
        if (! $this->lolosBatasLaju('daftar-jamaah', 3, 10 * MINUTE)) {
            return redirect()->back()->withInput()->with('error',
                'Terlalu banyak pendaftaran dari perangkat ini. Silakan tunggu beberapa menit.');
        }

        $userModel = new UserModel();

        $ip = $this->request->getIPAddress();
        $userData = [
            'name'          => $this->request->getPost('nama_lengkap'),
            'email'         => $this->request->getPost('email'),
            'phone'         => $this->request->getPost('phone'),
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'          => 'user',
            'register_ip'   => $ip,
            'register_country' => $this->_getCountryFromIp($ip),
            ...\App\Libraries\Acquisition::untukPendaftaran(),
        ];

        if ($userId = $userModel->insert($userData)) {
            $session = session();
            $session->set([
                'isLoggedIn' => true,
                'user_id'    => $userId,
                'user_name'  => $userData['name'],
                'user_email' => $userData['email'],
                'role'       => 'jamaah'
            ]);

            $this->kirimSambutan(
                'welcome_jamaah',
                $userData['email'],
                $userData['name'],
                'Selamat bergabung di Masj.id',
                [
                    'nama'      => $userData['name'],
                    'urlCari'   => base_url('jelajah'),
                    'urlContoh' => base_url(\App\Database\Seeds\DemoMasjidSeeder::USERNAME),
                ]
            );

            return redirect()->to('dashboard')->with('success', 'Pendaftaran berhasil. Selamat datang!');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal mendaftarkan akun.');
    }

    public function selectMasjid()
    {
        if (!session()->get('isLoggedIn')) return redirect()->to('login');

        $pengurusModel = new MasjidPengurusModel();
        $masjidModel = new MasjidModel();

        $pengurus = $pengurusModel->where('user_id', session()->get('user_id'))->findAll();
        
        $masjids = [];
        foreach ($pengurus as $p) {
            $m = $masjidModel->find($p['masjid_id']);
            if ($m) $masjids[] = $m;
        }

        $data = [
            'title'   => 'Pilih Masjid - Masj.id',
            'masjids' => $masjids
        ];

        return view('auth/select_masjid', $data);
    }

    public function setMasjidContext($id)
    {
        if (!session()->get('isLoggedIn')) return redirect()->to('login');

        // Security check: Verify user actually manages this masjid
        $pengurusModel = new MasjidPengurusModel();
        $isManaged = $pengurusModel->where([
            'user_id'   => session()->get('user_id'),
            'masjid_id' => $id
        ])->first();

        if (!$isManaged && session()->get('role') !== 'superadmin') {
            return redirect()->to('login')->with('error', 'Akses ditolak.');
        }

        $masjidModel = new MasjidModel();
        $masjid = $masjidModel->find($id);

        if (!$masjid) {
            return redirect()->back()->with('error', 'Masjid tidak ditemukan.');
        }

        session()->set([
            'masjid_id'       => $masjid['id'],
            'masjid_name'     => $masjid['name'],
            'masjid_username' => $masjid['username'],
        ]);

        return redirect()->to('dashboard')->with('success', 'Selamat datang di ' . $masjid['name']);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Berhasil keluar.');
    }

    public function checkUsername()
    {
        $username = $this->request->getGet('username');
        if (empty($username)) {
            return $this->response->setJSON(['available' => false, 'message' => 'Username kosong']);
        }

        $masjidModel = new MasjidModel();
        $exists = $masjidModel->where('username', $username)->first();

        return $this->response->setJSON(['available' => !$exists]);
    }

    public function checkEmail()
    {
        $email = $this->request->getGet('email');
        if (empty($email)) {
            return $this->response->setJSON(['available' => false, 'message' => 'Email kosong']);
        }

        $userModel = new UserModel();
        $exists = $userModel->where('email', $email)->first();

        return $this->response->setJSON(['available' => !$exists]);
    }

    /**
     * Mengirim email sambutan. Sengaja TIDAK pernah menggagalkan pendaftaran.
     *
     * Pendaftaran sudah tersimpan saat method ini dipanggil. Bila layanan email
     * sedang mati atau kuncinya belum diatur, yang benar adalah pendaftar tetap
     * masuk ke dashboard — bukan melihat galat untuk sesuatu yang sudah berhasil.
     * Kegagalannya dicatat ke log supaya tetap bisa ditelusuri.
     *
     * Mailer::kirim() memang mengembalikan false alih-alih melempar, tapi
     * penyusunan view atau panggilan HTTP-nya masih bisa melempar — karena itu
     * seluruhnya dibungkus try/catch.
     */
    private function kirimSambutan(string $templat, string $email, string $nama, string $subjek, array $data): void
    {
        try {
            $mailer = new \App\Libraries\Mailer();
            if (! $mailer->siap()) {
                // Kunci email belum diatur — lazim di lingkungan pengembangan.
                // Dicatat pada tingkat info, jadi TIDAK muncul dengan setelan
                // logger.threshold = 4 di .env proyek ini (error ke atas saja).
                // Itu disengaja: keadaan ini normal, bukan kegagalan.
                log_message('info', 'Email sambutan dilewati: kunci Mailer belum diatur.');

                return;
            }

            // HTML disusun SEKARANG, selagi konteks permintaan masih utuh —
            // view() butuh itu. Yang ditunda hanya panggilan HTTP-nya.
            $html = view('emails/' . $templat, $data);
        } catch (\Throwable $e) {
            log_message('error', 'Email sambutan gagal disiapkan: ' . $e->getMessage());

            return;
        }

        // Pengiriman ditunda sampai respons selesai dikirim ke browser.
        //
        // Mailer memberi batas 20 detik untuk panggilan HTTP-nya. Bila layanan
        // email sedang lambat atau mati, tanpa penundaan ini pendaftar akan
        // menatap layar menunggu sampai 20 detik SETELAH menekan "Daftar" —
        // padahal masjidnya sudah tersimpan sejak tadi. Sebagian akan mengira
        // gagal lalu menekan ulang atau pergi, dan itu persis kebocoran
        // aktivasi yang sedang coba ditutup oleh email ini.
        //
        // fastcgi_finish_request() menutup koneksi lebih dulu bila tersedia
        // (php-fpm). Di lingkungan lain fungsi ini tidak ada, dan pengiriman
        // tetap berjalan di shutdown — setidaknya keluarannya sudah terkirim.
        register_shutdown_function(static function () use ($mailer, $email, $nama, $subjek, $html) {
            // Nama fungsinya berbeda per SAPI: php-fpm memakai yang pertama,
            // LiteSpeed yang kedua. Apache mod_php tak punya keduanya — di sana
            // koneksi tetap terbuka, tetapi pendaftarannya sendiri sudah aman
            // tersimpan sejak sebelum email disentuh.
            foreach (['fastcgi_finish_request', 'litespeed_finish_request'] as $tutup) {
                if (function_exists($tutup)) {
                    $tutup();
                    break;
                }
            }

            try {
                if (! $mailer->kirim($email, $nama, $subjek, $html)) {
                    log_message('error', 'Email sambutan gagal terkirim ke ' . $email . ': ' . $mailer->pesanGalat());
                }
            } catch (\Throwable $e) {
                log_message('error', 'Email sambutan gagal: ' . $e->getMessage());
            }
        });
    }

    private function processLogin($user)
    {
        $userModel = new \App\Models\UserModel();
        $userModel->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);

        // Riwayat login dicatat di sini karena SELURUH jalur masuk — formulir
        // biasa maupun Google — bermuara ke method ini. last_login di atas
        // hanya menyimpan yang terakhir dan selalu tertimpa; tanpa riwayat,
        // retensi dan kohort tak bisa dihitung sama sekali.
        (new \App\Models\UserLoginEventModel())->catat((int) $user['id'], $user['role'] ?? null);

        $session = session();
        
        $pengurusModel = new \App\Models\MasjidPengurusModel();
        $pengurus = $pengurusModel->where('user_id', $user['id'])->findAll();
        $pengurusCount = count($pengurus);

        $sessionData = [
            'isLoggedIn' => true,
            'user_id'    => $user['id'],
            'user_name'  => $user['name'],
            'user_email' => $user['email'],
        ];

        if ($user['role'] === 'superadmin') {
            $sessionData['role'] = 'superadmin';
            $session->set($sessionData);
            return redirect()->to('superadmin')->with('success', 'Selamat datang di Panel Kontrol Pusat, ' . $user['name'] . '!');
        }

        if ($pengurusCount > 0) {
            $sessionData['role'] = 'pengurus';
            
            if ($pengurusCount === 1) {
                $p = $pengurus[0];
                $masjid = (new \App\Models\MasjidModel())->find($p['masjid_id']);
                
                $sessionData['masjid_id'] = $p['masjid_id'];
                $sessionData['masjid_name'] = $masjid['name'] ?? 'Masjid Saya';
                $sessionData['masjid_username'] = $masjid['username'] ?? '';

                $session->set($sessionData);
                return redirect()->to('dashboard')->with('success', 'Selamat datang kembali, ' . $user['name'] . '!');
            } else {
                $session->set($sessionData);
                return redirect()->to('auth/select-masjid');
            }
        } else {
            $sessionData['role'] = 'jamaah';
            $session->set($sessionData);
            return redirect()->to('dashboard')->with('success', 'Selamat datang kembali, ' . $user['name'] . '!');
        }
    }

    public function googleLogin()
    {
        $clientId = $_ENV['GOOGLE_CLIENT_ID'] ?? getenv('GOOGLE_CLIENT_ID');
        if (!$clientId) {
            $clientId = env('GOOGLE_CLIENT_ID');
        }

        if (!$clientId) {
            return redirect()->to('login')->with('error', 'Google Client ID belum dikonfigurasi.');
        }

        $redirectUri = base_url('auth/google/callback');
        $authUrl = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'email profile',
            'access_type' => 'online',
        ]);
        return redirect()->to($authUrl);
    }

    public function googleCallback()
    {
        $code = $this->request->getGet('code');
        if (!$code) {
            return redirect()->to('login')->with('error', 'Google login dibatalkan atau gagal.');
        }

        $clientId = $_ENV['GOOGLE_CLIENT_ID'] ?? getenv('GOOGLE_CLIENT_ID');
        if (!$clientId) $clientId = env('GOOGLE_CLIENT_ID');
        
        $clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'] ?? getenv('GOOGLE_CLIENT_SECRET');
        if (!$clientSecret) $clientSecret = env('GOOGLE_CLIENT_SECRET');
        
        $redirectUri = base_url('auth/google/callback');

        // Exchange code for token
        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
            'code' => $code,
            'grant_type' => 'authorization_code',
        ]));
        $response = curl_exec($ch);
        curl_close($ch);
        $tokenData = json_decode($response, true);

        if (!isset($tokenData['access_token'])) {
            return redirect()->to('login')->with('error', 'Gagal mendapatkan token dari Google.');
        }

        // Get user info
        $chInfo = curl_init('https://www.googleapis.com/oauth2/v3/userinfo');
        curl_setopt($chInfo, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chInfo, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $tokenData['access_token']
        ]);
        $userInfoResponse = curl_exec($chInfo);
        curl_close($chInfo);
        $googleUser = json_decode($userInfoResponse, true);

        if (!isset($googleUser['email'])) {
            return redirect()->to('login')->with('error', 'Gagal mendapatkan informasi profil Google.');
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('email', $googleUser['email'])->first();

        if ($user) {
            return $this->processLogin($user);
        } else {
            $userData = [
                'name'          => $googleUser['name'],
                'email'         => $googleUser['email'],
                'phone'         => '',
                'password_hash' => password_hash(bin2hex(random_bytes(10)), PASSWORD_DEFAULT),
                'role'          => 'user'
            ];
            
            $userId = $userModel->insert($userData);
            if ($userId) {
                $user = $userModel->find($userId);
                return $this->processLogin($user);
            }
            return redirect()->to('login')->with('error', 'Gagal membuat akun.');
        }
    }

    private function _getCountryFromIp($ip)
    {
        if (empty($ip) || $ip == '::1' || $ip == '127.0.0.1') return 'Localhost';
        
        $ch = curl_init("http://ip-api.com/json/{$ip}?fields=country");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        $response = curl_exec($ch);
        curl_close($ch);
        
        if ($response) {
            $data = json_decode($response, true);
            if (isset($data['country'])) {
                return $data['country'];
            }
        }
        return 'Unknown';
    }

    // =====================================================================
    // LUPA PASSWORD
    // =====================================================================

    public function showForgotPassword()
    {
        return view('auth/forgot_password', ['title' => 'Lupa Password - Masj.id']);
    }

    /**
     * Mengirim tautan reset ke email — bila email terdaftar.
     *
     * Balasan SELALU sama ("bila terdaftar, tautan dikirim") apa pun hasilnya,
     * supaya orang luar tidak bisa memakai halaman ini untuk menebak email mana
     * yang punya akun (anti-enumerasi).
     */
    public function sendResetLink()
    {
        $email = trim((string) $this->request->getPost('email'));
        $pesanNetral = 'Bila email tersebut terdaftar, kami telah mengirim tautan reset. Silakan cek kotak masuk (dan folder spam).';

        // Tanpa pembatas, rute ini adalah alat pengirim email massal: siapa pun
        // bisa membanjiri kotak masuk orang lain dengan tautan reset.
        if (! $this->lolosBatasLaju('lupa-sandi', 3, 10 * MINUTE)
            || ! $this->lolosBatasLaju('lupa-sandi-email', 3, HOUR, strtolower($email))) {
            // Tetap pesan netral: membedakan balasan di sini akan membocorkan
            // email mana yang terdaftar — hal yang justru dijaga rute ini.
            return redirect()->back()->with('success', $pesanNetral);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withInput()->with('error', 'Masukkan alamat email yang sah.');
        }

        $user = (new UserModel())->where('email', $email)->first();

        // Hanya kirim bila user ada — tetapi balasan ke pengguna tetap netral.
        if ($user) {
            // Token mentah dikirim ke email; hanya HASH-nya disimpan.
            $tokenMentah = bin2hex(random_bytes(32));
            $db = \Config\Database::connect();
            $db->table('password_resets')->insert([
                'email'      => $email,
                'token_hash' => hash('sha256', $tokenMentah),
                'expires_at' => date('Y-m-d H:i:s', time() + 3600), // 1 jam
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $link = base_url('reset-password/' . $tokenMentah);
            $html = view('emails/reset_password', [
                'nama' => $user['name'] ?? 'Bapak/Ibu',
                'link' => $link,
            ]);

            $mailer = new \App\Libraries\Mailer();
            $terkirim = $mailer->kirim($email, $user['name'] ?? '', 'Reset Password Masj.id', $html);

            if (!$terkirim) {
                // Jujur bila layanan email gagal — jangan menyuruh pengguna
                // mengecek email yang tak akan pernah datang.
                log_message('error', 'Gagal kirim reset password ke ' . $email . ': ' . $mailer->pesanGalat());
                return redirect()->back()
                    ->with('error', 'Maaf, layanan email sedang bermasalah. Coba lagi nanti atau hubungi admin.');
            }
        }

        return redirect()->to('login')->with('success', $pesanNetral);
    }

    public function showResetPassword($token)
    {
        $baris = $this->cariTokenSah($token);
        if (!$baris) {
            return redirect()->to('forgot-password')
                ->with('error', 'Tautan reset tidak berlaku atau sudah kedaluwarsa. Silakan minta tautan baru.');
        }

        return view('auth/reset_password', [
            'title' => 'Atur Password Baru - Masj.id',
            'token' => $token,
        ]);
    }

    public function doResetPassword()
    {
        if (! $this->lolosBatasLaju('reset-sandi', 10, 10 * MINUTE)) {
            return redirect()->to('forgot-password')->with('error',
                'Terlalu banyak percobaan. Silakan tunggu beberapa menit lalu coba lagi.');
        }

        $token = (string) $this->request->getPost('token');
        $pass  = (string) $this->request->getPost('password');
        $pass2 = (string) $this->request->getPost('password_confirm');

        $baris = $this->cariTokenSah($token);
        if (!$baris) {
            return redirect()->to('forgot-password')
                ->with('error', 'Tautan reset tidak berlaku atau sudah kedaluwarsa.');
        }

        if (strlen($pass) < 8) {
            return redirect()->back()->with('error', 'Password minimal 8 karakter.');
        }
        if ($pass !== $pass2) {
            return redirect()->back()->with('error', 'Konfirmasi password tidak sama.');
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $baris['email'])->first();
        if (!$user) {
            return redirect()->to('forgot-password')->with('error', 'Akun tidak ditemukan.');
        }

        $userModel->update($user['id'], ['password_hash' => password_hash($pass, PASSWORD_DEFAULT)]);

        // Tandai token terpakai (sekali pakai) dan buang token lain milik email
        // ini agar tak ada tautan lama yang masih menganga.
        $db = \Config\Database::connect();
        $db->table('password_resets')->where('email', $baris['email'])
            ->update(['used_at' => date('Y-m-d H:i:s')]);

        return redirect()->to('login')->with('success', 'Password berhasil diubah. Silakan login dengan password baru.');
    }

    /**
     * Baris token yang sah: cocok hash, belum dipakai, belum kedaluwarsa.
     */
    private function cariTokenSah(string $tokenMentah): ?array
    {
        if ($tokenMentah === '') {
            return null;
        }

        $db = \Config\Database::connect();
        return $db->table('password_resets')
            ->where('token_hash', hash('sha256', $tokenMentah))
            ->where('used_at', null)
            ->where('expires_at >=', date('Y-m-d H:i:s'))
            ->get()->getRowArray();
    }
}
