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
        $userModel = new UserModel();
        $masjidModel = new MasjidModel();
        $pengurusModel = new MasjidPengurusModel();

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // 1. Create User (PIC)
            $userData = [
                'name'          => $this->request->getPost('nama_pic'),
                'email'         => $this->request->getPost('email_pic'),
                'phone'         => $this->request->getPost('phone_pic'),
                'password_hash' => password_hash($this->request->getPost('password_pic'), PASSWORD_DEFAULT),
                'role'          => 'user'
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
                'masjid_username' => $masjidData['username']
            ]);

            // 4. Send Telegram Notification
            try {
                $telegram = new TelegramLibrary();
                $msg = "<b>🆕 PENDAFTAR BARU!</b>\n\n";
                $msg .= "Nama Masjid: <b>{$masjidData['name']}</b>\n";
                $msg .= "Username: @{$masjidData['username']}\n";
                $msg .= "PIC: {$userData['name']} ({$userData['phone']})\n";
                $msg .= "Waktu: " . date('d M Y H:i:s') . "\n";
                $telegram->sendMessage($msg);
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
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $userModel->where('email', $email)->first();

        if ($user && password_verify($password, $user['password_hash'])) {
            return $this->processLogin($user);
        }

        return redirect()->back()->withInput()->with('error', 'Email atau password salah.');
    }

    public function registerJamaah()
    {
        $userModel = new UserModel();

        $userData = [
            'name'          => $this->request->getPost('nama_lengkap'),
            'email'         => $this->request->getPost('email'),
            'phone'         => $this->request->getPost('phone'),
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'          => 'user'
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
            'masjid_username' => $masjid['username']
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

    // TEMPORARY: Use this to promote your first account manually via URL
    // e.g., site.com/auth/promote-me?email=your@email.com
    public function promoteMe()
    {
        $userModel = new UserModel();
        
        // Security check: If a superadmin already exists, disable this backdoor
        $anySuperAdmin = $userModel->where('role', 'superadmin')->first();
        if ($anySuperAdmin) {
            return "Fitur ini dinonaktifkan demi keamanan karena Super Admin sudah terdaftar. Silakan minta akses ke Super Admin yang ada.";
        }

        $email = $this->request->getGet('email');
        if (!$email) return "Email required.";
        
        $user = $userModel->where('email', $email)->first();
        
        if ($user) {
            $updateData = ['role' => 'superadmin'];
            
            // Optional: Reset password if provided in URL
            $newPassword = $this->request->getGet('password');
            if ($newPassword) {
                $updateData['password_hash'] = password_hash($newPassword, PASSWORD_DEFAULT);
            }

            // Using direct query builder to bypass any model limitations or ENUM issues
            $db = \Config\Database::connect();
            $db->table('users')->where('id', $user['id'])->update($updateData);
            
            $msg = "User with email $email has been promoted to Super Admin.";
            if ($newPassword) $msg .= " Password has been reset to '$newPassword'.";
            return $msg . " Please re-login.";
        }
        
        return "User not found.";
    }

    private function processLogin($user)
    {
        $userModel = new \App\Models\UserModel();
        $userModel->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);

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
        $clientId = env('GOOGLE_CLIENT_ID');
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

        $clientId = env('GOOGLE_CLIENT_ID');
        $clientSecret = env('GOOGLE_CLIENT_SECRET');
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
}
