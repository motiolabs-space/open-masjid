<?php

namespace App\Controllers;

use App\Models\MasjidModel;
use App\Models\MasjidWilayahModel;
use App\Models\MasjidGalleryModel;
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

        // Fetch Gallery
        $galleryModel = new MasjidGalleryModel();
        $gallery = $galleryModel->where('masjid_id', $masjidId)->findAll();
        
        // Get unique categories from gallery
        $categories = $galleryModel->where('masjid_id', $masjidId)
            ->select('category')
            ->distinct()
            ->get()
            ->getResultArray();
        $categories = array_column($categories, 'category');
        if (empty($categories)) {
            $categories = ['Umum', 'Fasilitas', 'Kegiatan']; // Default categories
        }

        return view('dashboard/profil', [
            'title'      => 'Profil Masjid - Masj.id',
            'masjid'     => $masjid,
            'pengurus'   => $pengurus,
            'storage'    => $storage,
            'percentage' => round($percentage),
            'provinces'  => $provinces,
            'wilayah'    => $wilayah,
            'gallery'    => $gallery,
            'categories' => $categories
        ]);
    }

    public function updateProfile()
    {
        $masjidModel = new \App\Models\MasjidModel();
        $masjidId = session()->get('masjid_id');

        $data = $this->request->getPost();
    $oldMasjid = $masjidModel->find($masjidId);

    // Handle Username Change
    if (isset($data['username']) && $data['username'] !== $oldMasjid['username']) {
        // 1. Check uniqueness
        $exists = $masjidModel->where('username', $data['username'])->where('id !=', $masjidId)->first();
        if ($exists) {
            return redirect()->back()->withInput()->with('error', 'Username sudah digunakan oleh masjid lain.');
        }

        // 2. Check time constraint (1 month)
        if (!empty($oldMasjid['username_updated_at'])) {
            $lastUpdate = new \DateTime($oldMasjid['username_updated_at']);
            $now = new \DateTime();
            $diff = $now->diff($lastUpdate);
            
            if ($diff->m < 1 && $diff->y == 0) {
                return redirect()->back()->withInput()->with('error', 'Username hanya dapat diubah minimal 1 bulan sekali.');
            }
        }
        
        $data['username_updated_at'] = date('Y-m-d H:i:s');
    } else {
        // Prevent accidental update of username_updated_at if username is same
        unset($data['username']);
    }
    
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

    // Handle Menu Toggles
    $data['menu_berita']  = isset($data['menu_berita']) ? 1 : 0;
    $data['menu_program'] = isset($data['menu_program']) ? 1 : 0;
    $data['menu_laporan'] = isset($data['menu_laporan']) ? 1 : 0;
    $data['menu_kontak']  = isset($data['menu_kontak']) ? 1 : 0;

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

    public function updatePengurus()
    {
        $id = $this->request->getPost('id');
        $role = $this->request->getPost('role');
        $title = $this->request->getPost('title');

        if (empty($id) || empty($role)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak lengkap.']);
        }

        $pengurusModel = new MasjidPengurusModel();
        $masjidId = session()->get('masjid_id');

        // Verify ownership
        $pengurus = $pengurusModel->where(['id' => $id, 'masjid_id' => $masjidId])->first();
        if (!$pengurus) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
        }

        if (($pengurus['is_creator'] ?? 0) == 1) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akun Admin Utama tidak dapat diubah.']);
        }

        $data = [
            'role'  => $role,
            'title' => $title
        ];

        if ($pengurusModel->update($id, $data)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Data pengurus berhasil diperbarui.']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal memperbarui data pengurus.']);
    }

    public function deletePengurus()
    {
        $id = $this->request->getPost('id');
        if (empty($id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID tidak ditemukan.']);
        }

        $pengurusModel = new MasjidPengurusModel();
        $masjidId = session()->get('masjid_id');

        // Verify ownership
        $pengurus = $pengurusModel->where(['id' => $id, 'masjid_id' => $masjidId])->first();
        if (!$pengurus) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan atau bukan milik Anda.']);
        }

        if (($pengurus['is_creator'] ?? 0) == 1) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akun Admin Utama tidak dapat dihapus.']);
        }

        if ($pengurusModel->delete($id)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Pengurus berhasil dihapus.']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menghapus pengurus.']);
    }

    public function uploadGallery()
    {
        $masjidId = session()->get('masjid_id');
        $category = $this->request->getPost('category');
        $files = $this->request->getFiles();

        if (empty($category) || empty($files['photos'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak lengkap.']);
        }

        $galleryModel = new MasjidGalleryModel();
        $storage = new Storage();
        $successCount = 0;

        foreach ($files['photos'] as $file) {
            if ($file->isValid() && !$file->hasMoved()) {
                $path = $storage->upload($file, 'images/gallery');
                if ($path) {
                    $galleryModel->insert([
                        'masjid_id'  => $masjidId,
                        'image_path' => $path,
                        'category'   => $category
                    ]);
                    $successCount++;
                }
            }
        }

        if ($successCount > 0) {
            return $this->response->setJSON(['status' => 'success', 'message' => "$successCount foto berhasil diunggah."]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal mengunggah foto.']);
    }

    public function deleteGallery()
    {
        $id = $this->request->getPost('id');
        $masjidId = session()->get('masjid_id');

        $galleryModel = new MasjidGalleryModel();
        $photo = $galleryModel->where(['id' => $id, 'masjid_id' => $masjidId])->first();

        if ($photo) {
            $storage = new Storage();
            $storage->delete($photo['image_path']);
            $galleryModel->delete($id);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Foto berhasil dihapus.']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Foto tidak ditemukan.']);
    }
}
