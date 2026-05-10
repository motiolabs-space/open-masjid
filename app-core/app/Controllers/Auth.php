<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\MasjidModel;
use App\Models\MasjidPengurusModel;
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

            return redirect()->to('/dashboard')->with('success', 'Pendaftaran masjid berhasil. Selamat datang!');

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
            $session = session();
            
            // Check for Masjid Pengurus role
            $pengurusModel = new MasjidPengurusModel();
            $pengurus = $pengurusModel->where('user_id', $user['id'])->first();

            $sessionData = [
                'isLoggedIn' => true,
                'user_id'    => $user['id'],
                'user_name'  => $user['name'],
                'user_email' => $user['email'],
            ];

            if ($user['role'] === 'superadmin') {
                $sessionData['role'] = 'superadmin';
                $session->set($sessionData);
                return redirect()->to('/superadmin')->with('success', 'Selamat datang di Panel Kontrol Pusat, ' . $user['name'] . '!');
            }

            if ($pengurus) {
                // Determine Masjid details
                $masjidModel = new MasjidModel();
                $masjid = $masjidModel->find($pengurus['masjid_id']);
                
                $sessionData['role'] = 'pengurus';
                $sessionData['masjid_id'] = $pengurus['masjid_id'];
                $sessionData['masjid_name'] = $masjid['name'] ?? 'Masjid Saya';
                $sessionData['masjid_username'] = $masjid['username'] ?? '';
            } else {
                $sessionData['role'] = 'jamaah';
            }

            $session->set($sessionData);
            return redirect()->to('/dashboard')->with('success', 'Selamat datang kembali, ' . $user['name'] . '!');
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

            return redirect()->to('/dashboard')->with('success', 'Pendaftaran berhasil. Selamat datang!');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal mendaftarkan akun.');
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
        $email = $this->request->getGet('email');
        if (!$email) return "Email required.";
        
        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();
        
        if ($user) {
            $userModel->update($user['id'], ['role' => 'superadmin']);
            return "User with email $email has been promoted to Super Admin. Please re-login.";
        }
        
        return "User not found.";
    }
}
