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
        $masjidId = session()->get('masjid_id');

        // Models
        $wargaModel = new \App\Models\MasjidWargaModel();
        $inventoryModel = new \App\Models\MasjidInventoryModel();
        $financeModel = new \App\Models\MasjidFinanceTransactionModel();
        $programModel = new \App\Models\MasjidProgramModel();
        $newsModel = new \App\Models\MasjidNewsModel();
        $scheduleModel = new \App\Models\MasjidScheduleModel();

        // 1. Stats
        $totalWarga = $wargaModel->where('masjid_id', $masjidId)->whereIn('status', ['active', 'pindah', 'meninggal'])->countAllResults();
        $totalAssetItems = $inventoryModel->where('masjid_id', $masjidId)->countAllResults();
        
        // Finance Summary
        $financeSummary = $financeModel->getSummary($masjidId);
        
        // Active Programs Count
        $activeProgramsCount = $programModel->where('masjid_id', $masjidId)
            ->where('date_end >=', date('Y-m-d'))
            ->countAllResults();

        // Social Alert (Warga Kurang Mampu)
        $socialAlertCount = $wargaModel->where('masjid_id', $masjidId)
            ->whereIn('economic_status', ['fakir', 'miskin', 'yatim', 'kurang_mampu'])
            ->countAllResults();

        // 2. Lists
        // Recent Programs with Funding Progress
        $recentPrograms = $programModel->where('masjid_id', $masjidId)
            ->where('status', 'published')
            ->orderBy('created_at', 'DESC')
            ->findAll(5);
        
        // Calculate progress for each program
        foreach ($recentPrograms as &$prog) {
            $prog['collected'] = 0;
            if (!empty($prog['target_donation']) && $prog['target_donation'] > 0) {
                $prog['collected'] = $financeModel->where('masjid_id', $masjidId)
                    ->where('program_id', $prog['id'])
                    ->where('type', 'pemasukan')
                    ->selectSum('amount')
                    ->first()['amount'] ?? 0;
                $prog['percentage'] = ($prog['collected'] / $prog['target_donation']) * 100;
            } else {
                $prog['percentage'] = 0;
            }
        }

        // Recent News
        $recentNews = $newsModel->select('masjid_news.*, masjid_news_categories.name as category_name')
            ->join('masjid_news_categories', 'masjid_news_categories.id = masjid_news.category_id', 'left')
            ->where('masjid_news.masjid_id', $masjidId)
            ->where('masjid_news.status', 'published')
            ->orderBy('masjid_news.created_at', 'DESC')
            ->findAll(3);

        // Upcoming Schedules
        $upcomingSchedules = $scheduleModel->where('masjid_id', $masjidId)
            ->where('date >=', date('Y-m-d'))
            ->orderBy('date', 'ASC')
            ->findAll(3);

        // Todo List (Mocked for now based on logic)
        $todoList = [];
        // Check financial report for current month
        $currentMonth = date('Y-m');
        $hasTransaction = $financeModel->where('masjid_id', $masjidId)->like('date', $currentMonth)->countAllResults();
        if ($hasTransaction > 0) {
            // Placeholder logic: In real app, check if 'report_generated' flag exists
        }

        $data = [
            'title' => 'Dashboard Utama - ' . $name,
            'stats' => [
                'total_warga' => $totalWarga,
                'total_assets' => $totalAssetItems,
                'finance' => $financeSummary,
                'active_programs' => $activeProgramsCount,
                'social_alert' => $socialAlertCount
            ],
            'recentPrograms' => $recentPrograms,
            'recentNews' => $recentNews,
            'upcomingSchedules' => $upcomingSchedules
        ];
        
        if (session()->get('role') !== 'pengurus' && session()->get('role') !== 'superadmin') {
            // Jamaah Dashboard View
            return view('dashboard/index_jamaah', $data);
        }

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
            'visi', 'misi', 'foto_utama', 'phone', 'about_us'
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
        $provinces = $provinceModel->orderBy('name', 'ASC')->findAll();

        // Fetch Service Areas (Wilayah Layanan)
        $wilayahModel = new MasjidWilayahModel();
        $wilayah = $wilayahModel->where('masjid_id', $masjidId)->findAll();

        // Fetch Social Media
        $socialModel = new \App\Models\MasjidSocialModel();
        $socials = $socialModel->where('masjid_id', $masjidId)->findAll();

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
            'socials'    => $socials,
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
            $uploadPath = $storage->upload($file, 'profil');
            if ($uploadPath) {
                $data['foto_utama'] = $uploadPath;
            } else {
                return redirect()->back()->withInput()->with('error', 'Format foto utama tidak didukung atau file berbahaya.');
            }
        }

        // Handle Logo Upload
        $logoFile = $this->request->getFile('logo');
        if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
            $storage = new Storage();
            
            // Delete old logo if exists
            $oldMasjid = $masjidModel->find($masjidId);
            if (!empty($oldMasjid['logo'])) {
                $storage->delete($oldMasjid['logo']);
            }

            // Upload new logo
            $logoPath = $storage->upload($logoFile, 'logo');
            if ($logoPath) {
                $data['logo'] = $logoPath;
            } else {
                return redirect()->back()->withInput()->with('error', 'Format logo tidak didukung atau file berbahaya.');
            }
        }

        // Handle Social Media
        $socials = $this->request->getPost('socials');
        $socialModel = new \App\Models\MasjidSocialModel();
        
        // Delete all existing socials for this masjid (simple overwrite strategy)
        $socialModel->where('masjid_id', $masjidId)->delete();
        
        if (!empty($socials) && is_array($socials)) {
            $socialData = [];
            foreach ($socials as $s) {
                if (!empty($s['platform']) && !empty($s['url'])) {
                    $socialData[] = [
                        'masjid_id' => $masjidId,
                        'platform'  => $s['platform'],
                        'url'       => $s['url']
                    ];
                }
            }
            if (!empty($socialData)) {
                $socialModel->insertBatch($socialData);
            }
        }

        // Handle Social Media
        $socials = $this->request->getPost('socials');
        $socialModel = new \App\Models\MasjidSocialModel();
        
        // Delete all existing socials for this masjid (simple overwrite strategy)
        $socialModel->where('masjid_id', $masjidId)->delete();
        
        if (!empty($socials) && is_array($socials)) {
            $socialData = [];
            foreach ($socials as $s) {
                if (!empty($s['platform']) && !empty($s['url'])) {
                    $socialData[] = [
                        'masjid_id' => $masjidId,
                        'platform'  => $s['platform'],
                        'url'       => $s['url']
                    ];
                }
            }
            if (!empty($socialData)) {
                $socialModel->insertBatch($socialData);
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

        // Convert Kabupaten ID to Name if it's alphanumeric/numeric
        if (isset($data['kabupaten'])) {
            $regencyModel = new RegencyModel();
            
            // Save the ID to regency_id column (for prayer times API)
            // We assume the input 'kabupaten' IS the ID from the dropdown
            $data['regency_id'] = $data['kabupaten'];

            // Check if it's an ID (try to find it)
            $regency = $regencyModel->find($data['kabupaten']);
            if ($regency) {
                // Overwrite 'kabupaten' with the Name for display purposes
                $data['kabupaten'] = $regency['name'];
            }
        }

        // Handle External Service Toggle
        $data['is_external_service'] = isset($data['is_external_service']) ? 1 : 0;

    // Handle Menu Toggles
    $data['menu_berita']  = isset($data['menu_berita']) ? 1 : 0;
    $data['menu_program'] = isset($data['menu_program']) ? 1 : 0;
    $data['menu_laporan'] = isset($data['menu_laporan']) ? 1 : 0;
    $data['menu_kontak']  = isset($data['menu_kontak']) ? 1 : 0;

    // Handle Action Button
    $data['action_button_active'] = isset($data['action_button_active']) ? 1 : 0;

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
            // SECURITY CHECK
            $oldNews = $newsModel->where(['id' => $newsId, 'masjid_id' => $masjidId])->first();
            if (!$oldNews) {
                return redirect()->to('/dashboard/berita')->with('error', 'Data tidak ditemukan atau akses ditolak.');
            }
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
            $uploadPath = $storage->upload($file, 'berita');
            if ($uploadPath) {
                $data['thumbnail'] = $uploadPath;
            } else {
                return redirect()->back()->withInput()->with('error', 'Format thumbnail tidak didukung atau file berbahaya.');
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
            // SECURITY CHECK
            $exists = $categoryModel->where(['id' => $id, 'masjid_id' => $masjidId])->first();
            if (!$exists) return redirect()->back()->with('error', 'Akses ditolak.');

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
        $regencies = $regencyModel->where('province_id', $provinceId)->orderBy('name', 'ASC')->findAll();
        return $this->response->setJSON($regencies);
    }

    public function searchUsers()
    {
        $term = $this->request->getGet('q') ?? $this->request->getGet('term');
        $masjidId = session()->get('masjid_id');

        if (empty($term)) return $this->response->setJSON([]);

        $db = \Config\Database::connect();
        
        // Get generic users NOT already in this masjid committee
        $builder = $db->table('users');
        $builder->select('id, name, email, phone, avatar')
                ->groupStart()
                    ->like('name', $term)
                    ->orLike('email', $term)
                    ->orLike('phone', $term)
                ->groupEnd()
                ->whereNotIn('id', function($subquery) use ($masjidId) {
                    $subquery->select('user_id')->from('masjid_pengurus')->where('masjid_id', $masjidId);
                })
                ->limit(10);
        
        $results = $builder->get()->getResultArray();

        return $this->response->setJSON($results);
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
                $path = $storage->upload($file, 'galeri');
                if ($path) {
                    $galleryModel->insert([
                        'masjid_id'  => $masjidId,
                        'image_path' => $path,
                        'category'   => $category
                    ]);
                    $successCount++;
                } else {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'Salah satu file foto tidak didukung atau berbahaya.']);
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
            // SECURITY CHECK
            $oldProgram = $programModel->where(['id' => $programId, 'masjid_id' => $masjidId])->first();
            if (!$oldProgram) {
                return redirect()->back()->with('error', 'Data tidak ditemukan atau akses ditolak.');
            }
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
            'target_donation'   => $this->request->getPost('target_donation') ? str_replace(['.', ','], ['', '.'], $this->request->getPost('target_donation')) : null,
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
            $uploadPath = $storage->upload($file, 'program');
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
            // SECURITY CHECK
            $exists = $categoryModel->where(['id' => $id, 'masjid_id' => $masjidId])->first();
            if (!$exists) return redirect()->back()->with('error', 'Akses ditolak.');

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
            // SECURITY CHECK
            $exists = $categoryModel->where(['id' => $id, 'masjid_id' => $masjidId])->first();
            if (!$exists) return redirect()->back()->with('error', 'Akses ditolak.');

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
            $uploadPath = $storage->upload($file, 'keuangan', ['jpg', 'jpeg', 'png', 'webp', 'gif', 'pdf']);
            if ($uploadPath) {
                $data['attachment'] = $uploadPath;
            }
        }

        if ($id) {
            // SECURITY CHECK
            $exists = $transactionModel->where(['id' => $id, 'masjid_id' => $masjidId])->first();
            if (!$exists) return redirect()->back()->with('error', 'Akses ditolak.');

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

        // Include Last Aid Date
        $warga = $query->select('masjid_warga.*, (SELECT MAX(date) FROM masjid_distributions WHERE warga_id = masjid_warga.id) as last_aid_date')
            ->orderBy('name', 'ASC')
            ->findAll();

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

    public function volunteers()
    {
        $masjidId = session()->get('masjid_id');
        $wargaModel = new \App\Models\MasjidWargaModel();

        // Search for volunteers (tagged in notes or special status)
        $volunteers = $wargaModel->where('masjid_id', $masjidId)
            ->groupStart()
                ->like('notes', '#relawan')
                ->orLike('notes', '#volunteer')
            ->groupEnd()
            ->orderBy('name', 'ASC')
            ->findAll();

        return view('dashboard/volunteers/index', [
            'title'      => 'Relawan & Piket - Masj.id',
            'volunteers' => $volunteers
        ]);
    }

    // --------------------------------------------------------------------
    // INVENTORY MANAGEMENT
    // --------------------------------------------------------------------

    public function inventory()
    {
        $masjidId = session()->get('masjid_id');
        $invModel = new \App\Models\MasjidInventoryModel();

        $search = $this->request->getGet('q');
        $condition = $this->request->getGet('condition');

        $query = $invModel->where('masjid_id', $masjidId);

        if ($search) {
            $query->like('name', $search);
        }

        if ($condition) {
            $query->where('condition', $condition);
        }

        return view('dashboard/inventory/index', [
            'title'     => 'Inventaris Masjid - Masj.id',
            'inventory' => $query->orderBy('name', 'ASC')->findAll(),
            'storage'   => new Storage()
        ]);
    }

    public function createInventory()
    {
        return view('dashboard/inventory/form', [
            'title'   => 'Tambah Aset - Masj.id',
            'item'    => null,
            'storage' => new Storage()
        ]);
    }

    public function editInventory($id)
    {
        $masjidId = session()->get('masjid_id');
        $invModel = new \App\Models\MasjidInventoryModel();
        $item = $invModel->where(['id' => $id, 'masjid_id' => $masjidId])->first();

        if (!$item) {
            return redirect()->to('dashboard/inventory')->with('error', 'Aset tidak ditemukan.');
        }

        return view('dashboard/inventory/form', [
            'title'   => 'Edit Aset - Masj.id',
            'item'    => $item,
            'storage' => new Storage()
        ]);
    }

    public function saveInventory()
    {
        $masjidId = session()->get('masjid_id');
        $invModel = new \App\Models\MasjidInventoryModel();
        $id = $this->request->getPost('id');

        $data = [
            'masjid_id'      => $masjidId,
            'name'           => $this->request->getPost('name'),
            'brand'          => $this->request->getPost('brand'),
            'quantity'       => $this->request->getPost('quantity'),
            'unit'           => $this->request->getPost('unit'),
            'condition'      => $this->request->getPost('condition'),
            'purchase_date'  => $this->request->getPost('purchase_date') ?: null,
            'purchase_price' => $this->request->getPost('purchase_price') ? str_replace(['.', ','], ['', '.'], $this->request->getPost('purchase_price')) : null,
            'description'    => $this->request->getPost('description'),
        ];

        // Handle Photo
        $file = $this->request->getFile('photo');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $storage = new Storage();
            if ($id) {
                $oldItem = $invModel->find($id);
                if (!empty($oldItem['photo'])) {
                    $storage->delete($oldItem['photo']);
                }
            }
            $fileName = $storage->upload($file, 'inventaris');
            if ($fileName) {
                $data['photo'] = $fileName;
            } else {
                return redirect()->back()->withInput()->with('error', 'Format foto aset tidak didukung atau file berbahaya.');
            }
        }

        if ($id) {
             // Security Check
             $oldItem = $invModel->where(['id' => $id, 'masjid_id' => $masjidId])->first();
             if (!$oldItem) {
                 return redirect()->to('dashboard/inventory')->with('error', 'Aset tidak ditemukan.');
             }

            if (!$invModel->update($id, $data)) {
                return redirect()->back()->withInput()->with('error', 'Gagal update.');
            }
            $message = 'Aset berhasil diperbarui.';
        } else {
            if (!$invModel->insert($data)) {
                return redirect()->back()->withInput()->with('error', 'Gagal menyimpan.');
            }
            $message = 'Aset berhasil ditambahkan.';
        }

        return redirect()->to('dashboard/inventory')->with('success', $message);
    }

    public function deleteInventory($id)
    {
        $masjidId = session()->get('masjid_id');
        $invModel = new \App\Models\MasjidInventoryModel();

        $item = $invModel->where(['id' => $id, 'masjid_id' => $masjidId])->first();
        if ($item) {
            if (!empty($item['photo'])) {
                (new Storage())->delete($item['photo']);
            }
            $invModel->delete($id);
            return redirect()->to('dashboard/inventory')->with('success', 'Aset berhasil dihapus.');
        }

        return redirect()->to('dashboard/inventory')->with('error', 'Aset tidak ditemukan.');
    }

    // --------------------------------------------------------------------
    // PAYMENT SETTINGS
    // --------------------------------------------------------------------

    public function paymentSettings()
    {
        $masjidId = session()->get('masjid_id');
        $payModel = new \App\Models\MasjidPaymentModel();
        
        $settings = $payModel->where('masjid_id', $masjidId)->first();

        return view('dashboard/settings/payment', [
            'title'    => 'Pengaturan Pembayaran - Masj.id',
            'settings' => $settings,
            'storage'  => new Storage()
        ]);
    }

    public function savePaymentSettings()
    {
        $masjidId = session()->get('masjid_id');
        $payModel = new \App\Models\MasjidPaymentModel();
        
        $currentSettings = $payModel->where('masjid_id', $masjidId)->first();
        $id = $currentSettings['id'] ?? null;

        $data = [
            'masjid_id'           => $masjidId,
            'payment_mode'        => $this->request->getPost('payment_mode'),
            'bank_name'           => $this->request->getPost('bank_name'),
            'bank_account_name'   => $this->request->getPost('bank_account_name'),
            'bank_account_number' => $this->request->getPost('bank_account_number'),
            'multipay_api_key'    => $this->request->getPost('multipay_api_key'),
            'multipay_secret_key' => $this->request->getPost('multipay_secret_key'),
            'api_key'             => $this->request->getPost('multipay_api_key'), // Generic alias
            'api_secret'          => $this->request->getPost('multipay_secret_key'), // Generic alias
            'merchant_id'         => $this->request->getPost('merchant_id'),
        ];

        // Handle QRIS Image
        $file = $this->request->getFile('qris_image');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $storage = new Storage();
            if ($id && !empty($currentSettings['qris_image'])) {
                $storage->delete($currentSettings['qris_image']);
            }
            $fileName = $storage->upload($file, 'qris');
            if ($fileName) {
                $data['qris_image'] = $fileName;
            } else {
                return redirect()->back()->withInput()->with('error', 'Format gambar QRIS tidak didukung atau file berbahaya.');
            }
        }

        if ($id) {
            $payModel->update($id, $data);
        } else {
            $payModel->insert($data);
        }

        return redirect()->to('dashboard/settings/payment')->with('success', 'Pengaturan pembayaran berhasil disimpan.');
    }

    // --------------------------------------------------------------------
    // SCHEDULE MANAGEMENT (Jadwal Peribadatan)
    // --------------------------------------------------------------------

    public function schedules()
    {
        $masjidId = session()->get('masjid_id');
        $schedModel = new \App\Models\MasjidScheduleModel();
        
        $month = $this->request->getGet('month') ?: date('m');
        $year  = $this->request->getGet('year') ?: date('Y');

        $start = "$year-$month-01";
        $end   = date("Y-m-t", strtotime($start));

        $schedules = $schedModel->where('masjid_id', $masjidId)
                                ->where('date >=', $start)
                                ->where('date <=', $end)
                                ->orderBy('date', 'ASC')
                                ->orderBy('prayer_type', 'ASC') // Rough ordering, might need custom sort
                                ->findAll();

        // Group by Date for Calendar/List View
        $grouped = [];
        foreach ($schedules as $s) {
            $grouped[$s['date']][] = $s;
        }

        return view('dashboard/schedule/index', [
            'title'     => 'Jadwal Peribadatan - Masj.id',
            'schedules' => $grouped,
            'month'     => $month,
            'year'      => $year
        ]);
    }

    public function createSchedule()
    {
        return view('dashboard/schedule/form', [
            'title' => 'Tambah Jadwal - Masj.id',
            'data'  => []
        ]);
    }

    public function editSchedule($id)
    {
        $masjidId = session()->get('masjid_id');
        $schedModel = new \App\Models\MasjidScheduleModel();
        $data = $schedModel->where(['id' => $id, 'masjid_id' => $masjidId])->first();

        if (!$data) {
            return redirect()->to('dashboard/schedules')->with('error', 'Jadwal tidak ditemukan.');
        }

        return view('dashboard/schedule/form', [
            'title' => 'Edit Jadwal - Masj.id',
            'data'  => $data
        ]);
    }

    public function saveSchedule()
    {
        $masjidId = session()->get('masjid_id');
        $schedModel = new \App\Models\MasjidScheduleModel();
        $id = $this->request->getPost('id');

        $data = [
            'masjid_id'    => $masjidId,
            'date'         => $this->request->getPost('date'),
            'prayer_type'  => $this->request->getPost('prayer_type'),
            'imam_name'    => $this->request->getPost('imam_name'),
            'khatib_name'  => $this->request->getPost('khatib_name'),
            'muadzin_name' => $this->request->getPost('muadzin_name'),
            'bilal_name'   => $this->request->getPost('bilal_name'),
        ];

        if ($id) {
            // SECURITY CHECK
            $exists = $schedModel->where(['id' => $id, 'masjid_id' => $masjidId])->first();
            if (!$exists) return redirect()->back()->with('error', 'Akses ditolak.');

            $schedModel->update($id, $data);
        } else {
            $schedModel->insert($data);
        }

        return redirect()->to('dashboard/schedules')->with('success', 'Jadwal berhasil disimpan.');
    }

    public function deleteSchedule($id)
    {
        $masjidId = session()->get('masjid_id');
        $schedModel = new \App\Models\MasjidScheduleModel();
        
        $item = $schedModel->where(['id' => $id, 'masjid_id' => $masjidId])->first();
        if ($item) {
            $schedModel->delete($id);
            return redirect()->to('dashboard/schedules')->with('success', 'Jadwal berhasil dihapus.');
        }

        return redirect()->to('dashboard/schedules')->with('error', 'Jadwal tidak ditemukan.');
    }

    // --------------------------------------------------------------------
    // MOSQUE COMMITTEE MANAGEMENT (Pengurus Masjid)
    // --------------------------------------------------------------------

    public function pengurus()
    {
        $masjidId = session()->get('masjid_id');
        $pengurusModel = new \App\Models\MasjidPengurusModel();

        // Fetch pengurus with user details
        $pengurus = $pengurusModel->select('masjid_pengurus.*, users.name, users.email, users.phone, users.avatar')
            ->join('users', 'users.id = masjid_pengurus.user_id')
            ->where('masjid_id', $masjidId)
            ->findAll();

        return view('dashboard/pengurus/index', [
            'title'    => 'Pengurus Masjid - Masj.id',
            'pengurus' => $pengurus
        ]);
    }

    // BROADCAST NEWSLETTER MODULE
    // --------------------------------------------------------------------

    public function subscribers()
    {
        $masjidId = session()->get('masjid_id');
        $subscriberModel = new \App\Models\MasjidSubscriberModel();

        $subscribers = $subscriberModel->where('masjid_id', $masjidId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('dashboard/broadcast/subscribers', [
            'title'       => 'Subscriber Newsletter - Masj.id',
            'subscribers' => $subscribers
        ]);
    }

    public function deleteSubscriber($id) 
    {
        $masjidId = session()->get('masjid_id');
        $subscriberModel = new \App\Models\MasjidSubscriberModel();
        
        $item = $subscriberModel->where(['id' => $id, 'masjid_id' => $masjidId])->first();
        if ($item) {
            $subscriberModel->delete($id);
            return redirect()->back()->with('success', 'Subscriber dihapus.');
        }
        return redirect()->back()->with('error', 'Data tidak ditemukan.');
    }

    public function broadcasts()
    {
        $masjidId = session()->get('masjid_id');
        $broadcastModel = new \App\Models\MasjidBroadcastModel();

        $broadcasts = $broadcastModel->where('masjid_id', $masjidId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('dashboard/broadcast/index', [
            'title'      => 'Riwayat Siaran/Broadcast - Masj.id',
            'broadcasts' => $broadcasts
        ]);
    }

    public function createBroadcast()
    {
        return view('dashboard/broadcast/form', [
            'title' => 'Buat Siaran Baru - Masj.id'
        ]);
    }

    public function sendBroadcast()
    {
        $masjidId = session()->get('masjid_id');
        $subject = $this->request->getPost('subject');
        $content = $this->request->getPost('content');
        
        // 1. Save to Database
        $broadcastModel = new \App\Models\MasjidBroadcastModel();
        $broadcastId = $broadcastModel->insert([
            'masjid_id' => $masjidId,
            'subject'   => $subject,
            'content'   => $content,
            'type'      => 'email',
            'status'    => 'draft',
            'recipient_count' => 0
        ]);

        // 2. Get Active Subscribers
        $subscriberModel = new \App\Models\MasjidSubscriberModel();
        $subscribers = $subscriberModel->where(['masjid_id' => $masjidId, 'is_active' => 1])->findAll();
        
        if (empty($subscribers)) {
             return redirect()->to('dashboard/broadcast')->with('error', 'Belum ada subscriber aktif.');
        }

        // 3. Send Emails (Looping - MVP Approach)
        $email = \Config\Services::email();
        $masjidModel = new \App\Models\MasjidModel();
        $masjid = $masjidModel->find($masjidId);
        $count = 0;

        foreach ($subscribers as $sub) {
            $email->clear();
            $email->setTo($sub['email']);
            $email->setSubject($subject);
            
            // Simple Template
            $body = "<h2>Berita dari " . esc($masjid['name']) . "</h2>";
            $body .= "<hr>";
            $body .= $content;
            $body .= "<br><br><small>Anda menerima email ini karena berlangganan update dari " . esc($masjid['name']) . " via Masj.id.</small>";

            $email->setMessage($body);

            if ($email->send()) {
                $count++;
            }
        }

        // 4. Update Status
        $broadcastModel->update($broadcastId, [
            'status' => 'sent', 
            'recipient_count' => $count,
            'sent_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('dashboard/broadcast')->with('success', "Siaran berhasil dikirim ke $count subscriber.");
    }

    // AID DISTRIBUTION (PENYALURAN) MODULE
    // --------------------------------------------------------------------

    public function distributions()
    {
        $masjidId = session()->get('masjid_id');
        $distModel = new \App\Models\MasjidDistributionModel();

        // Join Warga & Program names
        $distributions = $distModel->select('masjid_distributions.*, masjid_warga.name as warga_name, masjid_warga.phone as warga_phone, masjid_programs.title as program_name')
            ->join('masjid_warga', 'masjid_warga.id = masjid_distributions.warga_id', 'left')
            ->join('masjid_programs', 'masjid_programs.id = masjid_distributions.program_id', 'left')
            ->where('masjid_distributions.masjid_id', $masjidId)
            ->orderBy('date', 'DESC')
            ->findAll();

        return view('dashboard/distribution/index', [
            'title'         => 'Penyaluran Bantuan - Masj.id',
            'distributions' => $distributions
        ]);
    }

    public function createDistribution()
    {
        $masjidId = session()->get('masjid_id');
        $wargaModel = new \App\Models\MasjidWargaModel();
        $programModel = new \App\Models\MasjidProgramModel();

        $warga = $wargaModel->where('masjid_id', $masjidId)->findAll();
        $programs = $programModel->where('masjid_id', $masjidId)->orderBy('date_start', 'DESC')->findAll();

        return view('dashboard/distribution/form', [
            'title'    => 'Input Penyaluran - Masj.id',
            'warga'    => $warga,
            'programs' => $programs,
            'selectedWargaId' => $this->request->getGet('warga_id')
        ]);
    }

    public function editDistribution($id)
    {
        $masjidId = session()->get('masjid_id');
        $distModel = new \App\Models\MasjidDistributionModel();
        $wargaModel = new \App\Models\MasjidWargaModel();
        $programModel = new \App\Models\MasjidProgramModel();

        $item = $distModel->where(['id' => $id, 'masjid_id' => $masjidId])->first();
        if (!$item) return redirect()->to('dashboard/distribution')->with('error', 'Data tidak ditemukan.');

        $warga = $wargaModel->where('masjid_id', $masjidId)->findAll();
        $programs = $programModel->where('masjid_id', $masjidId)->orderBy('date_start', 'DESC')->findAll();

        return view('dashboard/distribution/form', [
            'title'    => 'Edit Penyaluran - Masj.id',
            'item'     => $item,
            'warga'    => $warga,
            'programs' => $programs
        ]);
    }

    public function saveDistribution()
    {
        $masjidId = session()->get('masjid_id');
        $distModel = new \App\Models\MasjidDistributionModel();
        
        $id = $this->request->getPost('id');
        $data = [
            'masjid_id'   => $masjidId,
            'warga_id'    => $this->request->getPost('warga_id') ?: null,
            'program_id'  => $this->request->getPost('program_id') ?: null,
            'date'        => $this->request->getPost('date'),
            'type'        => $this->request->getPost('type'),
            'amount'      => str_replace('.', '', $this->request->getPost('amount') ?? '0'),
            'items'       => $this->request->getPost('items'),
            'description' => $this->request->getPost('description'),
        ];

        // Handle Photo Upload
        $file = $this->request->getFile('evidence_photo');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $storage = new \App\Libraries\Storage();
            if ($id) {
                // Delete old photo if exists
                $oldItem = $distModel->find($id);
                if ($oldItem && !empty($oldItem['evidence_photo'])) {
                    $storage->delete($oldItem['evidence_photo']);
                }
            }
            $filename = $file->getRandomName(); // getRandomName is also called inside upload, but passing it explicitly is fine or we can just let it be
            $path = $storage->upload($file, 'penyaluran');
            if ($path) {
                $data['evidence_photo'] = $path;
            } else {
                return redirect()->back()->withInput()->with('error', 'Format foto bukti tidak didukung atau file berbahaya.');
            }
        }

        if ($id) {
            // SECURITY CHECK
            $exists = $distModel->where(['id' => $id, 'masjid_id' => $masjidId])->first();
            if (!$exists) return redirect()->back()->with('error', 'Akses ditolak.');

            $distModel->update($id, $data);
            $message = 'Data penyaluran diperbarui.';
        } else {
            $distModel->insert($data);
            $message = 'Data penyaluran berhasil disimpan.';

            // OPTIONAL: Auto-create Expense Transaction
            if ($this->request->getPost('create_expense') == 1 && $data['type'] == 'money') {
                $financeModel = new \App\Models\MasjidFinanceTransactionModel();
                $financeModel->insert([
                    'masjid_id' => $masjidId,
                    'type'      => 'expense',
                    'category_id' => null, // Or a default "Penyaluran" category if exists
                    'amount'    => $data['amount'],
                    'date'      => $data['date'],
                    'description' => 'Penyaluran via Modul: ' . ($data['description'] ?? '-'),
                ]);
                $message .= ' (Transaksi pengeluaran otomatis dibuat).';
            }
        }

        return redirect()->to('dashboard/distribution')->with('success', $message);
    }

    public function deleteDistribution($id)
    {
        $masjidId = session()->get('masjid_id');
        $distModel = new \App\Models\MasjidDistributionModel();
        
        $item = $distModel->where(['id' => $id, 'masjid_id' => $masjidId])->first();
        if ($item) {
            // Delete photo
            if (!empty($item['evidence_photo'])) {
                $storage = new \App\Libraries\Storage();
                $storage->delete($item['evidence_photo']);
            }

            $distModel->delete($id);
            return redirect()->back()->with('success', 'Data penyaluran dihapus.');
        }
        return redirect()->back()->with('error', 'Data tidak ditemukan.');
    }

    // REPORTING (LAPORAN) MODULE
    // --------------------------------------------------------------------

    public function reports()
    {
        return view('dashboard/reports/index', [
            'title' => 'Laporan & Rekap - Masj.id'
        ]);
    }

    public function generateFinanceReport()
    {
        $masjidId = session()->get('masjid_id');
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');

        $financeModel = new \App\Models\MasjidFinanceTransactionModel();
        
        // 1. Get Opening Balance (Saldo Awal) before Start Date
        $prevTransactions = $financeModel->where('masjid_id', $masjidId)
            ->where('date <', $startDate)
            ->findAll();
        
        $openingBalance = 0;
        foreach ($prevTransactions as $t) {
            if ($t['type'] == 'income') $openingBalance += $t['amount'];
            else $openingBalance -= $t['amount'];
        }

        // 2. Get Transactions in Range
        $transactions = $financeModel->select('masjid_finance_transactions.*, masjid_finance_categories.name as category_name')
            ->join('masjid_finance_categories', 'masjid_finance_categories.id = masjid_finance_transactions.category_id', 'left')
            ->where('masjid_finance_transactions.masjid_id', $masjidId)
            ->where('date >=', $startDate)
            ->where('date <=', $endDate)
            ->orderBy('date', 'ASC')
            ->findAll();

        // 3. Calculate Summary
        $totalIncome = 0;
        $totalExpense = 0;
        foreach ($transactions as $t) {
            if ($t['type'] == 'income') $totalIncome += $t['amount'];
            else $totalExpense += $t['amount'];
        }

        return view('dashboard/reports/finance_view', [
            'masjid' => (new \App\Models\MasjidModel())->find($masjidId),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'openingBalance' => $openingBalance,
            'transactions' => $transactions,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'closingBalance' => $openingBalance + $totalIncome - $totalExpense
        ]);
    }

    public function generateProgramReport()
    {
        $masjidId = session()->get('masjid_id');
        $month = $this->request->getGet('month') ?? date('Y-m');
        
        $programModel = new \App\Models\MasjidProgramModel();
        
        // Get programs starting in that month
        $programs = $programModel->select('masjid_programs.*, masjid_program_categories.name as category_name')
            ->join('masjid_program_categories', 'masjid_program_categories.id = masjid_programs.category_id', 'left')
            ->where('masjid_programs.masjid_id', $masjidId)
            ->like('date_start', $month, 'after')
            ->orderBy('date_start', 'ASC')
            ->findAll();

        return view('dashboard/reports/program_view', [
             'masjid' => (new \App\Models\MasjidModel())->find($masjidId),
             'month' => $month,
             'programs' => $programs
        ]);
    }

    public function generateInventoryReport()
    {
         $masjidId = session()->get('masjid_id');
         $status = $this->request->getGet('condition') ?? 'all';

         $inventoryModel = new \App\Models\MasjidInventoryModel();
         $builder = $inventoryModel->where('masjid_id', $masjidId);
         
         if ($status != 'all') {
             $builder->where('condition', $status);
         }

         $items = $builder->orderBy('name', 'ASC')->findAll();

         return view('dashboard/reports/inventory_view', [
             'masjid' => (new \App\Models\MasjidModel())->find($masjidId),
             'items' => $items,
             'filterCondition' => $status
         ]);
    }

    public function mutasi()
    {
        $masjidId = session()->get('masjid_id');
        $programModel = new \App\Models\MasjidProgramModel();
        $programs = $programModel->where('masjid_id', $masjidId)->findAll();
        
        return view('dashboard/keuangan/mutasi', [
            'title'    => 'Impor Mutasi Bank - Masj.id',
            'programs' => $programs,
            'mutations' => session()->getFlashdata('mutations') ?? []
        ]);
    }

    public function uploadMutasi()
    {
        $file = $this->request->getFile('csv_file');
        $bankType = $this->request->getPost('bank_type');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $parser = new \App\Libraries\BankMutationParser();
            $tempPath = $file->getTempName();
            
            try {
                $rows = $parser->parse($tempPath, $bankType);
                if (empty($rows)) {
                    return redirect()->back()->with('error', 'Format file tidak sesuai atau data kosong.');
                }
                
                // --- AI Auto-Categorization Integration ---
                $masjidId = session()->get('masjid_id');
                $programModel = new \App\Models\MasjidProgramModel();
                $programs = $programModel->select('id, title')->where('masjid_id', $masjidId)->findAll();
                
                if (!empty($programs)) {
                    $ai = new \App\Libraries\SumoPodAI();
                    
                    // Prepare data for AI
                    $programListText = json_encode($programs);
                    $transactionsText = json_encode(array_map(function($r) {
                        return ['description' => $r['description']];
                    }, $rows));

                    $prompt = "Sebagai asisten keuangan masjid, tugas Anda adalah memetakan transaksi bank berikut ke dalam ID program masjid yang paling relevan berdasarkan keterangan transaksinya.\n\n"
                            . "Daftar Program (JSON):\n{$programListText}\n\n"
                            . "Daftar Transaksi (JSON):\n{$transactionsText}\n\n"
                            . "Tolong kembalikan respons dalam format JSON murni (tanpa markdown), berupa array yang berisi daftar objek dengan key 'description' dan 'suggested_program_id'. Jika tidak yakin, isi null.";

                    $aiResponse = $ai->chatCompletion($prompt, ['temperature' => 0.1]);
                    
                    if ($aiResponse) {
                        // Clean up potential markdown formatting from AI output
                        $aiResponse = trim(str_replace(['```json', '```'], '', $aiResponse));
                        $suggestions = json_decode($aiResponse, true);
                        
                        if (is_array($suggestions)) {
                            foreach ($rows as &$row) {
                                $row['suggested_program_id'] = null;
                                foreach ($suggestions as $suggestion) {
                                    if (isset($suggestion['description'], $suggestion['suggested_program_id']) && $suggestion['description'] === $row['description']) {
                                        $row['suggested_program_id'] = $suggestion['suggested_program_id'];
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
                // --- End AI Integration ---

                return redirect()->back()->with('mutations', $rows)->with('success', count($rows) . ' transaksi ditemukan. Silakan petakan ke program.');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal membaca file: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('error', 'Pilih file CSV terlebih dahulu.');
    }

    public function mapMutasi()
    {
        $masjidId = session()->get('masjid_id');
        $financeModel = new \App\Models\MasjidFinanceTransactionModel();
        $catModel = new \App\Models\MasjidFinanceCategoryModel();
        
        $dates = $this->request->getPost('date');
        $descs = $this->request->getPost('description');
        $amounts = $this->request->getPost('amount');
        $programIds = $this->request->getPost('program_id');
        $selected = $this->request->getPost('selected');

        if (empty($selected)) {
            return redirect()->back()->with('error', 'Pilih setidaknya satu transaksi untuk dipetakan.');
        }

        // Ensure 'Donasi Terikat' category exists
        $category = $catModel->where(['masjid_id' => $masjidId, 'slug' => 'donasi-terikat'])->first();
        if (!$category) {
            $catId = $catModel->insert([
                'masjid_id' => $masjidId,
                'name' => 'Donasi Terikat Program',
                'type' => 'pemasukan',
                'slug' => 'donasi-terikat'
            ]);
        } else {
            $catId = $category['id'];
        }

        $successCount = 0;
        foreach ($selected as $index) {
            if (empty($programIds[$index])) continue;

            $financeModel->insert([
                'masjid_id'   => $masjidId,
                'category_id' => $catId,
                'program_id'  => $programIds[$index],
                'date'        => date('Y-m-d', strtotime($dates[$index])),
                'amount'      => $amounts[$index],
                'type'        => 'pemasukan',
                'description' => 'Mutasi Bank: ' . $descs[$index]
            ]);
            $successCount++;
        }

        return redirect()->to(base_url('dashboard/keuangan'))->with('success', $successCount . ' transaksi berhasil dipetakan ke laporan keuangan.');
    }

    // --------------------------------------------------------------------
    // AI REPORT GENERATOR
    // --------------------------------------------------------------------

    public function aiReportGenerator()
    {
        $masjidId = session()->get('masjid_id');
        $month = $this->request->getGet('month') ?? date('Y-m');

        return view('dashboard/reports/ai_report_generator', [
            'title' => 'AI Report Generator - Masj.id',
            'month' => $month,
            'generatedText' => session()->getFlashdata('generatedText') ?? ''
        ]);
    }

    public function generateAiReport()
    {
        $masjidId = session()->get('masjid_id');
        $month = $this->request->getPost('month') ?? date('Y-m');
        $startDate = $month . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));

        $financeModel = new \App\Models\MasjidFinanceTransactionModel();
        $programModel = new \App\Models\MasjidProgramModel();

        // 1. Get Opening Balance (Saldo Awal) before Start Date
        $prevTransactions = $financeModel->where('masjid_id', $masjidId)
            ->where('date <', $startDate)
            ->findAll();
        
        $openingBalance = 0;
        foreach ($prevTransactions as $t) {
            if ($t['type'] == 'income' || $t['type'] == 'pemasukan') $openingBalance += $t['amount'];
            else $openingBalance -= $t['amount'];
        }

        // 2. Get Transactions in Range
        $transactions = $financeModel->where('masjid_id', $masjidId)
            ->where('date >=', $startDate)
            ->where('date <=', $endDate)
            ->findAll();

        $totalIncome = 0;
        $totalExpense = 0;
        foreach ($transactions as $t) {
            if ($t['type'] == 'income' || $t['type'] == 'pemasukan') $totalIncome += $t['amount'];
            else $totalExpense += $t['amount'];
        }
        $closingBalance = $openingBalance + $totalIncome - $totalExpense;

        // 3. Get Programs in Range
        $programs = $programModel->where('masjid_id', $masjidId)
            ->like('date_start', $month, 'after')
            ->findAll();
        
        $programCount = count($programs);
        $programNames = array_map(function($p) { return $p['title']; }, $programs);

        // 4. Construct Prompt
        $masjid = (new \App\Models\MasjidModel())->find($masjidId);
        $masjidName = $masjid ? $masjid['name'] : 'Masjid';

        $prompt = "Anda adalah Sekretaris Masjid Profesional untuk {$masjidName}. Buatkan narasi laporan bulanan (copywriting) untuk bulan " . date('F Y', strtotime($startDate)) . " yang hangat, transparan, dan mudah dibaca oleh jamaah awam.\n\n";
        $prompt .= "Gunakan data berikut:\n";
        $prompt .= "- Saldo Awal: Rp " . number_format($openingBalance, 0, ',', '.') . "\n";
        $prompt .= "- Total Pemasukan: Rp " . number_format($totalIncome, 0, ',', '.') . "\n";
        $prompt .= "- Total Pengeluaran: Rp " . number_format($totalExpense, 0, ',', '.') . "\n";
        $prompt .= "- Saldo Akhir: Rp " . number_format($closingBalance, 0, ',', '.') . "\n";
        $prompt .= "- Jumlah Kegiatan/Program Bulan Ini: {$programCount}\n";
        if ($programCount > 0) {
            $prompt .= "- Daftar Kegiatan: " . implode(', ', $programNames) . "\n";
        }
        $prompt .= "\nBuatkan teks yang menarik, mengucapkan terima kasih kepada donatur, merangkum kegiatan, dan menginformasikan saldo keuangan. Format dalam Markdown. Jangan gunakan kalimat pengantar AI seperti 'Tentu, ini laporannya', langsung berikan teksnya.";

        // 5. Call SumoPod AI
        $ai = new \App\Libraries\SumoPodAI();
        $aiResponse = $ai->chatCompletion($prompt, ['temperature' => 0.6]);

        if ($aiResponse) {
            return redirect()->to(base_url('dashboard/reports/ai-generator?month=' . $month))
                             ->with('generatedText', trim($aiResponse))
                             ->with('success', 'Berhasil membuat laporan AI.');
        } else {
            return redirect()->back()->with('error', 'Gagal menghasilkan laporan dari AI.');
        }
    }

    public function publishAiReport()
    {
        $masjidId = session()->get('masjid_id');
        $month = $this->request->getPost('month');
        $content = $this->request->getPost('content');
        $action = $this->request->getPost('action'); // 'news' or 'broadcast'

        if (empty($content)) {
            return redirect()->back()->with('error', 'Konten laporan kosong.');
        }

        $title = "Laporan Kegiatan & Keuangan Bulan " . date('F Y', strtotime($month . '-01'));

        if ($action === 'news') {
            $newsModel = new \App\Models\MasjidNewsModel();
            
            // Create a slug
            $slug = url_title($title, '-', true) . '-' . time();
            
            $newsModel->insert([
                'masjid_id' => $masjidId,
                'category_id' => null, // Optional, could map to a default
                'title' => $title,
                'slug' => $slug,
                'content' => $content,
                'status' => 'published',
                'published_at' => date('Y-m-d H:i:s')
            ]);

            return redirect()->to(base_url('dashboard/berita'))->with('success', 'Laporan berhasil dipublikasikan ke Berita Masjid.');
        } elseif ($action === 'broadcast') {
            $broadcastModel = new \App\Models\MasjidBroadcastModel();
            
            $broadcastModel->insert([
                'masjid_id' => $masjidId,
                'subject'   => $title,
                'content'   => $content,
                'type'      => 'email',
                'status'    => 'draft',
                'recipient_count' => 0
            ]);

            return redirect()->to(base_url('dashboard/broadcast'))->with('success', 'Laporan berhasil disimpan sebagai draf Broadcast.');
        }

        return redirect()->back()->with('error', 'Aksi tidak valid.');
    }
}
