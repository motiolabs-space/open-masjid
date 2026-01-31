<?php

namespace App\Controllers;

use App\Models\MasjidModel;
use App\Models\MasjidWilayahModel;
use App\Models\MasjidPengurusModel;
use App\Models\UserModel;
use App\Models\ProvinceModel;
use App\Models\RegencyModel;
use App\Libraries\Storage;

class Admin extends BaseController
{
    public function index(): string
    {
        $name = session()->get('masjid_name') ?? session()->get('user_name') ?? 'User';
        $data = [
            'title' => 'Dashboard Utama - ' . $name
        ];
        return view('dashboard/index', $data);
    }
    public function profil(): string
    {
        $masjidModel = new \App\Models\MasjidModel();
        $masjidId = session()->get('masjid_id');
        
        $masjid = $masjidModel->find($masjidId);
        $storage = new \App\Libraries\Storage();

        // Fetch Pengurus
        $db = \Config\Database::connect();
        $pengurus = $db->table('masjid_pengurus')
            ->select('masjid_pengurus.*, users.name as user_name, users.phone as user_phone, users.email as user_email')
            ->join('users', 'users.id = masjid_pengurus.user_id')
            ->where('masjid_id', $masjidId)
            ->get()->getResultArray();

        // Calculate completion percentage
        $mandatoryFields = [
            'name', 'nama_resmi', 'jenis_masjid', 'tahun_berdiri', 
            'address', 'provinsi', 'kabupaten', 'kecamatan', 'kelurahan', 
            'visi', 'misi', 'foto_utama'
        ];
        $filledCount = 0;
        foreach ($mandatoryFields as $field) {
            if (!empty($masjid[$field])) {
                $filledCount++;
            }
        }
        $percentage = ($filledCount / count($mandatoryFields)) * 100;

        // Fetch Provinces
        $provinceModel = new ProvinceModel();
        $provinces = $provinceModel->findAll();

        // Fetch Service Areas (Wilayah Layanan)
        $wilayahModel = new MasjidWilayahModel();
        $wilayah = $wilayahModel->where('masjid_id', $masjidId)->findAll();

        return view('dashboard/profil', [
            'title'      => 'Profil Masjid - Masj.id',
            'masjid'     => $masjid,
            'pengurus'   => $pengurus,
            'storage'    => $storage,
            'percentage' => round($percentage),
            'provinces'  => $provinces,
            'wilayah'    => $wilayah
        ]);
    }

    public function updateProfile()
    {
        $masjidModel = new \App\Models\MasjidModel();
        $masjidId = session()->get('masjid_id');

        $data = $this->request->getPost();
        
        // Handle Photo Upload
        $file = $this->request->getFile('foto_utama');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $storage = new Storage();
            
            // Delete old photo if exists
            $oldMasjid = $masjidModel->find($masjidId);
            if (!empty($oldMasjid['foto_utama'])) {
                $storage->delete($oldMasjid['foto_utama']);
            }

            // Upload new photo
            $uploadPath = $storage->upload($file, 'images/masjid');
            if ($uploadPath) {
                $data['foto_utama'] = $uploadPath;
            }
        }

        // Remove ID if present to prevent primary key issues
        unset($data['id']);

        // Convert Provinsi ID to Name if it's numeric
        if (isset($data['provinsi']) && is_numeric($data['provinsi'])) {
            $provinceModel = new ProvinceModel();
            $province = $provinceModel->find($data['provinsi']);
            if ($province) {
                $data['provinsi'] = $province['name'];
            }
        }

        // Handle External Service Toggle
        $data['is_external_service'] = isset($data['is_external_service']) ? 1 : 0;

        // Handle Wilayah Layanan (Service Areas)
        $wilayahData = $this->request->getPost('wilayah') ?? [];
        unset($data['wilayah']);

        if ($masjidModel->update($masjidId, $data)) {
            // Update Wilayah Layanan
            $wilayahModel = new MasjidWilayahModel();
            $wilayahModel->where('masjid_id', $masjidId)->delete();
            
            if (!empty($wilayahData)) {
                $batchWilayah = [];
                foreach ($wilayahData as $wName) {
                    if (!empty(trim($wName))) {
                        $batchWilayah[] = [
                            'masjid_id' => $masjidId,
                            'name'      => trim($wName)
                        ];
                    }
                }
                if (!empty($batchWilayah)) {
                    $wilayahModel->insertBatch($batchWilayah);
                }
            }

            // Update session if name changed
            if (isset($data['name'])) {
                session()->set('masjid_name', $data['name']);
            }
            return redirect()->to('/dashboard/profil')->with('success', 'Profil masjid berhasil diperbarui.');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal memperbarui profil.');
    }

    public function program(): string
    {
        return view('dashboard/program', ['title' => 'Manajemen Program & Kegiatan - Masj.id']);
    }

    public function berita(): string
    {
        return view('dashboard/berita', ['title' => 'Berita & Dokumentasi - Masj.id']);
    }

    public function keuangan(): string
    {
        return view('dashboard/keuangan', ['title' => 'Manajemen Keuangan - Masj.id']);
    }

    public function getRegencies($provinceId)
    {
        $regencyModel = new RegencyModel();
        $regencies = $regencyModel->where('province_id', $provinceId)->findAll();
        return $this->response->setJSON($regencies);
    }

    public function searchUsers()
    {
        $query = $this->request->getGet('q');
        if (empty($query)) {
            return $this->response->setJSON([]);
        }

        $userModel = new UserModel();
        $users = $userModel->like('name', $query)
            ->orLike('phone', $query)
            ->limit(10)
            ->findAll();

        return $this->response->setJSON($users);
    }

    public function addPengurus()
    {
        $masjidId = session()->get('masjid_id');
        $userId = $this->request->getPost('user_id');
        $role = $this->request->getPost('role');
        $title = $this->request->getPost('title');

        if (empty($userId) || empty($role)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak lengkap.']);
        }

        $pengurusModel = new MasjidPengurusModel();
        
        // Check if already exist
        $exists = $pengurusModel->where([
            'masjid_id' => $masjidId,
            'user_id'   => $userId
        ])->first();

        if ($exists) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Pengguna ini sudah menjadi pengurus.']);
        }

        $data = [
            'masjid_id' => $masjidId,
            'user_id'   => $userId,
            'role'      => $role,
            'title'     => $title
        ];

        if ($pengurusModel->insert($data)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Pengurus berhasil ditambahkan.']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menambahkan pengurus.']);
    }
}
