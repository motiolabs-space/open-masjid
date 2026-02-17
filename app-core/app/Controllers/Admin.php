<?php

namespace App\Controllers;

use App\Models\MasjidModel;
use App\Models\MasjidWilayahModel;
use App\Models\MasjidGalleryModel;
use App\Models\MasjidPengurusModel;
use App\Models\UserModel;
use App\Models\ProvinceModel;
use App\Models\RegencyModel;
use App\Models\MasjidNewsModel;
use App\Models\MasjidNewsCategoryModel;
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


    public function berita(): string
    {
        $masjidId = session()->get('masjid_id');
        $masjidModel = new \App\Models\MasjidModel();
        $masjid = $masjidModel->find($masjidId);

        $newsModel = new \App\Models\MasjidNewsModel();
        $categoryModel = new \App\Models\MasjidNewsCategoryModel();

        $news = $newsModel->select('masjid_news.*, masjid_news_categories.name as category_name')
            ->join('masjid_news_categories', 'masjid_news_categories.id = masjid_news.category_id', 'left')
            ->where('masjid_news.masjid_id', $masjidId)
            ->orderBy('masjid_news.created_at', 'DESC')
            ->findAll();

        $categories = $categoryModel->where('masjid_id', $masjidId)->findAll();

        return view('dashboard/berita/index', [
            'title' => 'Berita & Dokumentasi - Masj.id',
            'news' => $news,
            'categories' => $categories,
            'masjid' => $masjid,
            'storage' => new Storage()
        ]);
    }

    public function createBerita()
    {
        $masjidId = session()->get('masjid_id');
        $categoryModel = new \App\Models\MasjidNewsCategoryModel();
        $categories = $categoryModel->where('masjid_id', $masjidId)->findAll();

        return view('dashboard/berita/form', [
            'title' => 'Tulis Berita Baru - Masj.id',
            'categories' => $categories,
            'news' => null
        ]);
    }

    public function editBerita($id)
    {
        $masjidId = session()->get('masjid_id');
        $newsModel = new \App\Models\MasjidNewsModel();
        $news = $newsModel->where(['id' => $id, 'masjid_id' => $masjidId])->first();

        if (!$news) {
            return redirect()->to('/dashboard/berita')->with('error', 'Berita tidak ditemukan.');
        }

        $categoryModel = new \App\Models\MasjidNewsCategoryModel();
        $categories = $categoryModel->where('masjid_id', $masjidId)->findAll();

        return view('dashboard/berita/form', [
            'title' => 'Edit Berita - Masj.id',
            'categories' => $categories,
            'news' => $news,
            'storage' => new Storage()
        ]);
    }

    public function saveBerita()
    {
        $masjidId = session()->get('masjid_id');
        $newsModel = new \App\Models\MasjidNewsModel();
        $newsId = $this->request->getPost('id');

        $slugPrefix = url_title($this->request->getPost('title'), '-', true);
        if (!$newsId) {
            $slug = $slugPrefix . '-' . substr(md5(uniqid()), 0, 6);
        } else {
            // Keep existing slug for SEO if updating, 
            // unless we want to regenerate it. Let's keep it unless title changed significantly.
            // For simplicity in this task, let's just use the current one if it exists or create one if not.
            $oldNews = $newsModel->find($newsId);
            $slug = $oldNews['slug'] ?? ($slugPrefix . '-' . substr(md5(uniqid()), 0, 6));
        }

        $data = [
            'masjid_id'   => $masjidId,
            'category_id' => $this->request->getPost('category_id') ?: null,
            'title'       => $this->request->getPost('title'),
            'slug'        => $slug,
            'content'     => $this->request->getPost('content'),
            'video_url'   => $this->request->getPost('video_url'),
            'status'      => $this->request->getPost('status') ?: 'published'
        ];

        // Handle Thumbnail
        $file = $this->request->getFile('thumbnail');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $storage = new Storage();
            if ($newsId) {
                $oldNews = $newsModel->find($newsId);
                if (!empty($oldNews['thumbnail'])) {
                    $storage->delete($oldNews['thumbnail']);
                }
            }
            $uploadPath = $storage->upload($file, 'images/news');
            if ($uploadPath) {
                $data['thumbnail'] = $uploadPath;
            }
        }

        if ($newsId) {
            $newsModel->update($newsId, $data);
            $msg = 'Berita berhasil diperbarui.';
        } else {
            $newsModel->insert($data);
            $msg = 'Berita berhasil diterbitkan.';
        }

        return redirect()->to('/dashboard/berita')->with('success', $msg);
    }

    public function deleteBerita()
    {
        $masjidId = session()->get('masjid_id');
        $newsId = $this->request->getPost('id');
        $newsModel = new \App\Models\MasjidNewsModel();

        $news = $newsModel->where(['id' => $newsId, 'masjid_id' => $masjidId])->first();
        if ($news) {
            if (!empty($news['thumbnail'])) {
                $st = new Storage();
                $st->delete($news['thumbnail']);
            }
            $newsModel->delete($newsId);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Berita berhasil dihapus.']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Berita tidak ditemukan.']);
    }

    public function saveNewsCategory()
    {
        $masjidId = session()->get('masjid_id');
        $categoryModel = new \App\Models\MasjidNewsCategoryModel();
        
        $data = [
            'masjid_id' => $masjidId,
            'name'      => $this->request->getPost('name'),
            'slug'      => url_title($this->request->getPost('name'), '-', true)
        ];

        $id = $this->request->getPost('id');
        if ($id) {
            $categoryModel->update($id, $data);
        } else {
            $categoryModel->insert($data);
        }

        return redirect()->back()->with('success', 'Kategori berita berhasil disimpan.');
    }

    public function deleteNewsCategory()
    {
        $masjidId = session()->get('masjid_id');
        $id = $this->request->getPost('id');
        $categoryModel = new \App\Models\MasjidNewsCategoryModel();

        $cat = $categoryModel->where(['id' => $id, 'masjid_id' => $masjidId])->first();
        if ($cat) {
            $categoryModel->delete($id);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Kategori berhasil dihapus.']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Kategori tidak ditemukan.']);
    }

    public function keuangan(): string
    {
        $masjidId = session()->get('masjid_id');
        $transactionModel = new \App\Models\MasjidFinanceTransactionModel();
        $categoryModel = new \App\Models\MasjidFinanceCategoryModel();
        $programModel = new \App\Models\MasjidProgramModel();

        $summary = $transactionModel->getSummary($masjidId);
        
        $transactions = $transactionModel->select('masjid_finance_transactions.*, masjid_finance_categories.name as category_name, masjid_programs.title as program_title')
            ->join('masjid_finance_categories', 'masjid_finance_categories.id = masjid_finance_transactions.category_id', 'left')
            ->join('masjid_programs', 'masjid_programs.id = masjid_finance_transactions.program_id', 'left')
            ->where('masjid_finance_transactions.masjid_id', $masjidId)
            ->orderBy('masjid_finance_transactions.date', 'DESC')
            ->orderBy('masjid_finance_transactions.created_at', 'DESC')
            ->findAll();

        $categories = $categoryModel->where('masjid_id', $masjidId)->findAll();
        $programs = $programModel->where('masjid_id', $masjidId)->findAll();

        return view('dashboard/keuangan/index', [
            'title'        => 'Manajemen Keuangan - Masj.id',
            'summary'      => $summary,
            'transactions' => $transactions,
            'categories'   => $categories,
            'programs'     => $programs,
            'storage'      => new Storage()
        ]);
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

    public function program(): string
    {
        $masjidId = session()->get('masjid_id');
        $programModel = new \App\Models\MasjidProgramModel();
        
        $programs = $programModel->select('masjid_programs.*, masjid_program_categories.name as category_name')
            ->join('masjid_program_categories', 'masjid_program_categories.id = masjid_programs.category_id', 'left')
            ->where('masjid_programs.masjid_id', $masjidId)
            ->orderBy('masjid_programs.date_start', 'DESC')
            ->findAll();

        $categoryModel = new \App\Models\MasjidProgramCategoryModel();
        $categories = $categoryModel->where('masjid_id', $masjidId)->findAll();

        return view('dashboard/program/index', [
            'title'      => 'Program & Kegiatan - Masj.id',
            'programs'   => $programs,
            'categories' => $categories,
            'storage'    => new Storage()
        ]);
    }

    public function createProgram()
    {
        $masjidId = session()->get('masjid_id');
        $categoryModel = new \App\Models\MasjidProgramCategoryModel();
        
        return view('dashboard/program/form', [
            'title'      => 'Buat Program Baru - Masj.id',
            'program'    => null,
            'categories' => $categoryModel->where('masjid_id', $masjidId)->findAll(),
            'storage'    => new Storage()
        ]);
    }

    public function editProgram($id)
    {
        $masjidId = session()->get('masjid_id');
        $programModel = new \App\Models\MasjidProgramModel();
        $program = $programModel->where(['id' => $id, 'masjid_id' => $masjidId])->first();

        if (!$program) {
            return redirect()->to('dashboard/program')->with('error', 'Program tidak ditemukan.');
        }

        $categoryModel = new \App\Models\MasjidProgramCategoryModel();

        return view('dashboard/program/form', [
            'title'      => 'Edit Program - Masj.id',
            'program'    => $program,
            'categories' => $categoryModel->where('masjid_id', $masjidId)->findAll(),
            'storage'    => new Storage()
        ]);
    }

    public function saveProgram()
    {
        $masjidId = session()->get('masjid_id');
        $programModel = new \App\Models\MasjidProgramModel();
        $programId = $this->request->getPost('id');

        $slugPrefix = url_title($this->request->getPost('title'), '-', true);
        if (!$programId) {
            $slug = $slugPrefix . '-' . substr(md5(uniqid()), 0, 6);
        } else {
            $oldProgram = $programModel->find($programId);
            $slug = $oldProgram['slug'] ?? ($slugPrefix . '-' . substr(md5(uniqid()), 0, 6));
        }

        $data = [
            'masjid_id'         => $masjidId,
            'category_id'       => $this->request->getPost('category_id') ?: null,
            'title'             => $this->request->getPost('title'),
            'slug'              => $slug,
            'description'       => $this->request->getPost('description'),
            'date_start'        => $this->request->getPost('date_start'),
            'date_end'          => $this->request->getPost('date_end') ?: null,
            'location'          => $this->request->getPost('location'),
            'registration_link' => $this->request->getPost('registration_link'),
            'quota'             => $this->request->getPost('quota') ?: null,
            'status'            => $this->request->getPost('status') ?: 'published'
        ];

        // Handle Thumbnail
        $file = $this->request->getFile('thumbnail');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $storage = new Storage();
            if ($programId) {
                $oldProgram = $programModel->find($programId);
                if (!empty($oldProgram['thumbnail'])) {
                    $storage->delete($oldProgram['thumbnail']);
                }
            }
            $uploadPath = $storage->upload($file, 'images/programs');
            if ($uploadPath) {
                $data['thumbnail'] = $uploadPath;
            }
        }

        if ($programId) {
            $programModel->update($programId, $data);
            $message = 'Program berhasil diperbarui.';
        } else {
            $programModel->insert($data);
            $message = 'Program baru berhasil ditambahkan.';
        }

        return redirect()->to('dashboard/program')->with('success', $message);
    }

    public function deleteProgram($id)
    {
        $masjidId = session()->get('masjid_id');
        $programModel = new \App\Models\MasjidProgramModel();
        $program = $programModel->where(['id' => $id, 'masjid_id' => $masjidId])->first();

        if ($program) {
            if (!empty($program['thumbnail'])) {
                (new Storage())->delete($program['thumbnail']);
            }
            $programModel->delete($id);
            return redirect()->to('dashboard/program')->with('success', 'Program berhasil dihapus.');
        }

        return redirect()->to('dashboard/program')->with('error', 'Program tidak ditemukan.');
    }

    public function saveProgramCategory()
    {
        $masjidId = session()->get('masjid_id');
        $categoryModel = new \App\Models\MasjidProgramCategoryModel();
        $id = $this->request->getPost('id');

        $data = [
            'masjid_id' => $masjidId,
            'name'      => $this->request->getPost('name'),
            'slug'      => url_title($this->request->getPost('name'), '-', true)
        ];

        if ($id) {
            $categoryModel->update($id, $data);
            $message = 'Kategori berhasil diperbarui.';
        } else {
            $categoryModel->insert($data);
            $message = 'Kategori baru berhasil ditambahkan.';
        }

        return redirect()->to('dashboard/program')->with('success', $message);
    }

    public function deleteProgramCategory()
    {
        $masjidId = session()->get('masjid_id');
        $id = $this->request->getPost('id');
        $categoryModel = new \App\Models\MasjidProgramCategoryModel();

        $category = $categoryModel->where(['id' => $id, 'masjid_id' => $masjidId])->first();
        if ($category) {
            // Set program category_id to NULL for programs using this category
            $programModel = new \App\Models\MasjidProgramModel();
            $programModel->where('category_id', $id)->set(['category_id' => null])->update();
            
            $categoryModel->delete($id);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Kategori berhasil dihapus.']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Kategori tidak ditemukan.']);
    }

    public function saveFinanceCategory()
    {
        $masjidId = session()->get('masjid_id');
        $categoryModel = new \App\Models\MasjidFinanceCategoryModel();
        $id = $this->request->getPost('id');

        $data = [
            'masjid_id' => $masjidId,
            'name'      => $this->request->getPost('name'),
            'type'      => $this->request->getPost('type'),
            'slug'      => url_title($this->request->getPost('name'), '-', true)
        ];

        if ($id) {
            $categoryModel->update($id, $data);
            $message = 'Kategori keuangan berhasil diperbarui.';
        } else {
            $categoryModel->insert($data);
            $message = 'Kategori keuangan baru berhasil ditambahkan.';
        }

        return redirect()->to('dashboard/keuangan')->with('success', $message);
    }

    public function deleteFinanceCategory()
    {
        $masjidId = session()->get('masjid_id');
        $id = $this->request->getPost('id');
        $categoryModel = new \App\Models\MasjidFinanceCategoryModel();

        $category = $categoryModel->where(['id' => $id, 'masjid_id' => $masjidId])->first();
        if ($category) {
            // Set transaction category_id to NULL
            $transactionModel = new \App\Models\MasjidFinanceTransactionModel();
            $transactionModel->where('category_id', $id)->set(['category_id' => null])->update();
            
            $categoryModel->delete($id);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Kategori berhasil dihapus.']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Kategori tidak ditemukan.']);
    }

    public function saveFinanceTransaction()
    {
        $masjidId = session()->get('masjid_id');
        $transactionModel = new \App\Models\MasjidFinanceTransactionModel();
        $id = $this->request->getPost('id');

        $data = [
            'masjid_id'   => $masjidId,
            'category_id' => $this->request->getPost('category_id'),
            'program_id'  => $this->request->getPost('program_id') ?: null,
            'date'        => $this->request->getPost('date'),
            'amount'      => str_replace(['.', ','], ['', '.'], $this->request->getPost('amount')),
            'type'        => $this->request->getPost('type'),
            'description' => $this->request->getPost('description'),
            'donor_name'  => $this->request->getPost('donor_name'),
            'donor_phone' => $this->request->getPost('donor_phone'),
        ];

        // Handle Attachment
        $file = $this->request->getFile('attachment');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $storage = new Storage();
            if ($id) {
                $oldTrans = $transactionModel->find($id);
                if (!empty($oldTrans['attachment'])) {
                    $storage->delete($oldTrans['attachment']);
                }
            }
            $uploadPath = $storage->upload($file, 'images/finance');
            if ($uploadPath) {
                $data['attachment'] = $uploadPath;
            }
        }

        if ($id) {
            $transactionModel->update($id, $data);
            $message = 'Transaksi berhasil diperbarui.';
        } else {
            $transactionModel->insert($data);
            $message = 'Transaksi berhasil dicatat.';
        }

        return redirect()->to('dashboard/keuangan')->with('success', $message);
    }

    public function deleteFinanceTransaction()
    {
        $masjidId = session()->get('masjid_id');
        $id = $this->request->getPost('id');
        $transactionModel = new \App\Models\MasjidFinanceTransactionModel();

        $transaction = $transactionModel->where(['id' => $id, 'masjid_id' => $masjidId])->first();
        if ($transaction) {
            if (!empty($transaction['attachment'])) {
                (new Storage())->delete($transaction['attachment']);
            }
            $transactionModel->delete($id);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Transaksi berhasil dihapus.']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Transaksi tidak ditemukan.']);
    }

    // --- Warga (Mustahik) Management ---

    public function warga(): string
    {
        $masjidId = session()->get('masjid_id');
        $wargaModel = new \App\Models\MasjidWargaModel();

        $search = $this->request->getGet('q');
        $status = $this->request->getGet('status');
        $economic = $this->request->getGet('economic');

        $query = $wargaModel->where('masjid_id', $masjidId);

        if ($search) {
            $query->groupStart()
                ->like('name', $search)
                ->orLike('nik', $search)
                ->orLike('phone', $search)
                ->groupEnd();
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($economic) {
            $query->where('economic_status', $economic);
        }

        $warga = $query->orderBy('name', 'ASC')->findAll();

        return view('dashboard/warga/index', [
            'title' => 'Data Warga & Mustahik - Masj.id',
            'warga' => $warga,
            'filters' => [
                'q' => $search,
                'status' => $status,
                'economic' => $economic
            ]
        ]);
    }

    public function createWarga()
    {
        return view('dashboard/warga/form', [
            'title' => 'Tambah Data Warga - Masj.id',
            'warga' => null
        ]);
    }

    public function editWarga($id)
    {
        $masjidId = session()->get('masjid_id');
        $wargaModel = new \App\Models\MasjidWargaModel();
        $warga = $wargaModel->where(['id' => $id, 'masjid_id' => $masjidId])->first();

        if (!$warga) {
            return redirect()->to('dashboard/warga')->with('error', 'Data warga tidak ditemukan.');
        }

        return view('dashboard/warga/form', [
            'title' => 'Edit Data Warga - Masj.id',
            'warga' => $warga
        ]);
    }

    public function saveWarga()
    {
        $masjidId = session()->get('masjid_id');
        $wargaModel = new \App\Models\MasjidWargaModel();
        $id = $this->request->getPost('id');

        $data = [
            'masjid_id'       => $masjidId,
            'name'            => $this->request->getPost('name'),
            'nik'             => $this->request->getPost('nik') ?: null,
            'kk'              => $this->request->getPost('kk') ?: null,
            'phone'           => $this->request->getPost('phone') ?: null,
            'address'         => $this->request->getPost('address'),
            'economic_status' => $this->request->getPost('economic_status'),
            'status'          => $this->request->getPost('status'),
            'notes'           => $this->request->getPost('notes'),
        ];

        if ($id) {
            // Security Check: Ensure owned by this masjid
            $oldWarga = $wargaModel->where(['id' => $id, 'masjid_id' => $masjidId])->first();
            if (!$oldWarga) {
                return redirect()->to('dashboard/warga')->with('error', 'Data tidak ditemukan atau bukan milik Anda.');
            }
            
            if (!$wargaModel->update($id, $data)) {
                return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data. Periksa inputan Anda.');
            }
            $message = 'Data warga berhasil diperbarui.';
        } else {
            if (!$wargaModel->insert($data)) {
                return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data. Periksa inputan Anda.');
            }
            $message = 'Data warga berhasil ditambahkan.';
        }

        return redirect()->to('dashboard/warga')->with('success', $message);
    }

    public function deleteWarga($id)
    {
        $masjidId = session()->get('masjid_id');
        $wargaModel = new \App\Models\MasjidWargaModel();
        $warga = $wargaModel->where(['id' => $id, 'masjid_id' => $masjidId])->first();

        if ($warga) {
            $wargaModel->delete($id);
            return redirect()->to('dashboard/warga')->with('success', 'Data warga berhasil dihapus.');
        }

        return redirect()->to('dashboard/warga')->with('error', 'Data warga tidak ditemukan.');
    }
}
