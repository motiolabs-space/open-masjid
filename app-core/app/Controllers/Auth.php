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
                throw new \Exception("Gagal mendaftarkan user PIC.");
            }

            // 2. Create Masjid
            $masjidData = [
                'name'     => $this->request->getPost('nama_masjid'),
                'username' => $this->request->getPost('username_masjid'),
            ];
            $masjidId = $masjidModel->insert($masjidData);

            if (!$masjidId) {
                throw new \Exception("Gagal mendaftarkan masjid.");
            }

            // 3. Link User to Masjid as Pengurus
            $pengurusData = [
                'masjid_id' => $masjidId,
                'user_id'   => $userId,
                'role'      => 'pengurus'
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
                'role'       => 'pengurus',
                'masjid_id'  => $masjidId,
                'masjid_name'=> $masjidData['name']
            ]);

            return redirect()->to('/dashboard')->with('success', 'Pendaftaran masjid berhasil. Selamat datang!');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
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
}
