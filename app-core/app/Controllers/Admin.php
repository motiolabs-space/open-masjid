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

        // Jamaah tidak terikat satu masjid — tampilkan dashboard khusus jamaah
        // dengan data pribadi (donasi, masjid diikuti), bukan data manajemen.
        $role = session()->get('role');
        if ($role !== 'pengurus' && $role !== 'superadmin') {
            return $this->jamaahDashboard();
        }

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

        // Fetch Global Settings
        $settingModel = new \App\Models\PlatformSettingModel();
        $waLinkSetting = $settingModel->where('setting_key', 'community_wa_link')->first();
        $communityWaLink = $waLinkSetting ? $waLinkSetting['setting_value'] : '';

        $data = [
            'title' => 'Dashboard Utama - ' . $name,
            'stats' => [
                'total_warga' => $totalWarga,
                'total_assets' => $totalAssetItems,
                'finance' => $financeSummary,
                'active_programs' => $activeProgramsCount,
                'social_alert' => $socialAlertCount,
            ],
            'recentPrograms' => $recentPrograms,
            'recentNews' => $recentNews,
            'upcomingSchedules' => $upcomingSchedules,
            'community_wa_link' => $communityWaLink,
            'statusSetup' => $this->_statusSetup(
                (new \App\Models\MasjidModel())->find($masjidId),
                $masjidId
            ),
        ];

        return view('dashboard/index', $data);
    }

    /**
     * Status kelengkapan data masjid untuk kartu notifikasi di dashboard.
     *
     * Hanya memuat hal yang benar-benar menghambat fitur bila kosong — bukan
     * sekadar kolom yang belum diisi. Koordinat ditandai penting karena tanpa
     * itu jadwal sholat dan Display TV mati tanpa pesan error apa pun.
     */
    private function _statusSetup(?array $masjid, $masjidId): array
    {
        if (!$masjid) {
            return [];
        }

        $adaKoordinat = !empty($masjid['latitude']) && !empty($masjid['longitude'])
            && ($masjid['latitude'] != 0 || $masjid['longitude'] != 0);

        // Donasi dianggap siap bila ada rekening bank atau QRIS.
        $bayar = (new \App\Models\MasjidPaymentModel())->where('masjid_id', $masjidId)->first();
        $adaPembayaran = $bayar && (!empty($bayar['bank_account_number']) || !empty($bayar['qris_image']));

        return [
            [
                'label'   => 'Titik koordinat masjid',
                'alasan'  => 'Tanpa ini jadwal sholat dan Display TV tidak akan tampil sama sekali.',
                'selesai' => $adaKoordinat,
                'penting' => true,
                'url'     => base_url('dashboard/profil'),
                'aksi'    => 'Atur Koordinat',
            ],
            [
                'label'   => 'Logo masjid',
                'alasan'  => 'Tampil di halaman profil publik dan Display TV.',
                'selesai' => !empty($masjid['logo']),
                'penting' => false,
                'url'     => base_url('dashboard/profil'),
                'aksi'    => 'Unggah Logo',
            ],
            [
                'label'   => 'Alamat masjid',
                'alasan'  => 'Membantu jamaah menemukan lokasi masjid.',
                'selesai' => !empty($masjid['address']),
                'penting' => false,
                'url'     => base_url('dashboard/profil'),
                'aksi'    => 'Isi Alamat',
            ],
            [
                'label'   => 'Rekening atau QRIS',
                'alasan'  => 'Diperlukan agar jamaah dapat berdonasi secara online.',
                'selesai' => $adaPembayaran,
                'penting' => false,
                'url'     => base_url('dashboard/pembayaran'),
                'aksi'    => 'Atur Pembayaran',
            ],
        ];
    }

    /** Dashboard khusus jamaah: data pribadi lintas-masjid (donasi, follow, program). */
    private function jamaahDashboard(): string
    {
        $userId = session()->get('user_id');
        $email  = session()->get('user_email');
        $db = \Config\Database::connect();

        $followerModel = new \App\Models\MasjidFollowerModel();
        $followedIds = array_map(
            'intval',
            array_column($followerModel->where('user_id', $userId)->findAll(), 'masjid_id')
        );

        // Total donasi berhasil (dicocokkan lewat email pendonor).
        $totalDonasi = 0;
        if ($email) {
            $row = $db->table('masjid_donations')
                ->selectSum('amount')
                ->where('donor_email', $email)
                ->where('status', 'success')
                ->get()->getRowArray();
            $totalDonasi = $row['amount'] ?? 0;
        }

        // Program aktif & berita dari masjid yang diikuti.
        $jumlahProgram = 0;
        $recentNews = [];
        if (!empty($followedIds)) {
            $jumlahProgram = $db->table('masjid_programs')
                ->whereIn('masjid_id', $followedIds)
                ->where('status', 'published')
                ->countAllResults();

            $recentNews = $db->table('masjid_news n')
                ->select('n.*, masjid.name as masjid_name, masjid.username as masjid_username')
                ->join('masjid', 'masjid.id = n.masjid_id')
                ->whereIn('n.masjid_id', $followedIds)
                ->where('n.status', 'published')
                ->orderBy('n.created_at', 'DESC')
                ->limit(5)
                ->get()->getResultArray();
        }

        // Jadwal ibadah terdekat dari masjid yang diikuti.
        $agenda = [];
        if (!empty($followedIds)) {
            $agenda = $db->table('masjid_schedules s')
                ->select('s.*, masjid.name as masjid_name')
                ->join('masjid', 'masjid.id = s.masjid_id')
                ->whereIn('s.masjid_id', $followedIds)
                ->where('s.date >=', date('Y-m-d'))
                ->orderBy('s.date', 'ASC')
                ->limit(3)
                ->get()->getResultArray();
        }

        // Modul LMS yang sedang dipelajari (sudah mulai, belum selesai).
        $lmsModule = $this->lmsInProgress($userId);

        return view('dashboard/index_jamaah', [
            'title'         => 'Dashboard Jamaah - Masj.id',
            'totalDonasi'   => $totalDonasi,
            'jumlahDiikuti' => count($followedIds),
            'jumlahProgram' => $jumlahProgram,
            'recentNews'    => $recentNews,
            'agenda'        => $agenda,
            'lmsModule'     => $lmsModule,
        ]);
    }

    /**
     * Modul LMS pertama yang sedang dikerjakan user (ada materi selesai tapi
     * belum semua). Mengembalikan null bila belum ada modul yang dimulai.
     */
    private function lmsInProgress($userId): ?array
    {
        $progressModel = new \App\Models\LmsProgressModel();
        $completedIds = array_column(
            $progressModel->where('user_id', $userId)->findAll(),
            'material_id'
        );
        if (empty($completedIds)) {
            return null;
        }

        $moduleModel   = new \App\Models\LmsModuleModel();
        $materialModel = new \App\Models\LmsMaterialModel();
        $masjidModel   = new \App\Models\MasjidModel();

        foreach ($moduleModel->where('status', 'published')->findAll() as $mod) {
            $materialIds = array_column(
                $materialModel->where('module_id', $mod['id'])->findAll(),
                'id'
            );
            $total = count($materialIds);
            if ($total === 0) {
                continue;
            }

            $done = count(array_intersect($materialIds, $completedIds));
            if ($done > 0 && $done < $total) {
                // lembaga_pemateri bisa berisi id masjid atau nama bebas.
                $lembaga = $mod['lembaga_pemateri'];
                if (is_numeric($lembaga)) {
                    $m = $masjidModel->find($lembaga);
                    $lembaga = $m['name'] ?? $lembaga;
                }

                return [
                    'title'   => $mod['title'],
                    'slug'    => $mod['slug'],
                    'lembaga' => $lembaga,
                    'done'    => $done,
                    'total'   => $total,
                    'pct'     => (int) round($done / $total * 100),
                ];
            }
        }

        return null;
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

    // Jeda iqomah dikirim sebagai 5 input terpisah (iqomah[Subuh] dst),
    // disatukan menjadi satu kolom JSON. Nilai di luar 0-60 menit diabaikan.
    if (isset($data['iqomah']) && is_array($data['iqomah'])) {
        $iqomah = [];
        foreach (['Subuh', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'] as $sholat) {
            $menit = (int) ($data['iqomah'][$sholat] ?? 0);
            if ($menit >= 0 && $menit <= 60) {
                $iqomah[$sholat] = $menit;
            }
        }
        $data['iqomah_settings'] = json_encode($iqomah);
        unset($data['iqomah']);
    }

    // Koreksi menit jadwal sholat — boleh negatif (lebih awal) atau positif
    // (lebih lambat). Dibatasi -30..+30 menit agar tidak mengubah jadwal terlalu jauh.
    if (isset($data['koreksi']) && is_array($data['koreksi'])) {
        $koreksi = [];
        foreach (['Subuh', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'] as $sholat) {
            $menit = (int) ($data['koreksi'][$sholat] ?? 0);
            if ($menit >= -30 && $menit <= 30) {
                $koreksi[$sholat] = $menit;
            }
        }
        $data['koreksi_menit'] = json_encode($koreksi);
        unset($data['koreksi']);
    }

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

        // category_id dari POST: pastikan kategorinya milik masjid ini, bukan
        // milik masjid lain.
        $categoryId = $this->request->getPost('category_id') ?: null;
        if ($categoryId !== null) {
            $kategori = (new \App\Models\MasjidNewsCategoryModel())
                ->where(['id' => $categoryId, 'masjid_id' => $masjidId])->first();

            if (!$kategori) {
                return redirect()->back()->withInput()->with('error', 'Kategori berita tidak ditemukan.');
            }
        }

        $data = [
            'masjid_id'   => $masjidId,
            'category_id' => $categoryId,
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

        // Hasil simpan diperiksa. Model mensyaratkan judul minimal 5 huruf dan
        // isi tidak kosong; tanpa ini kegagalannya dijawab 'Berita berhasil
        // diterbitkan.' sementara tulisan pengurus hilang begitu saja.
        $tersimpan = $newsId
            ? $newsModel->update($newsId, $data)
            : $newsModel->insert($data);

        if (!$tersimpan) {
            return redirect()->back()->withInput()
                ->with('error', 'Gagal menyimpan berita: ' . implode(' ', $newsModel->errors()));
        }

        $msg = $newsId ? 'Berita berhasil diperbarui.' : 'Berita berhasil diterbitkan.';

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

        // masjid_pengurus.role di produksi bertipe varchar(50) DEFAULT 'pengurus'
        // dan hanya berisi 'admin' atau 'pengurus'. Karena varchar menerima nilai
        // apa pun, pembatasan dilakukan di sini agar tidak muncul jabatan asing.
        if (!in_array($role, ['admin', 'pengurus'], true)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Jabatan tidak valid.']);
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

        // Sama seperti addPengurus: hanya 'admin' atau 'pengurus' (lihat catatan di sana).
        if (!in_array($role, ['admin', 'pengurus'], true)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Jabatan tidak valid.']);
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
                    // Dulu $successCount naik tanpa melihat hasil insert, jadi
                    // foto yang gagal masuk basis data tetap dihitung 'berhasil
                    // diunggah' — berkasnya ada di penyimpanan tetapi tidak
                    // pernah muncul di galeri, tanpa satu pun pesan.
                    if (!$galleryModel->insert([
                        'masjid_id'  => $masjidId,
                        'image_path' => $path,
                        'category'   => $category
                    ])) {
                        return $this->response->setJSON([
                            'status'  => 'error',
                            'message' => 'Gagal menyimpan foto: ' . implode(' ', $galleryModel->errors()),
                        ]);
                    }
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

        // category_id dari POST: pastikan kategorinya milik masjid ini.
        $categoryId = $this->request->getPost('category_id') ?: null;
        if ($categoryId !== null) {
            $kategori = (new \App\Models\MasjidProgramCategoryModel())
                ->where(['id' => $categoryId, 'masjid_id' => $masjidId])->first();

            if (!$kategori) {
                return redirect()->back()->withInput()->with('error', 'Kategori program tidak ditemukan.');
            }
        }

        $data = [
            'masjid_id'         => $masjidId,
            'category_id'       => $categoryId,
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
            } else {
                // Sebelumnya kegagalan unggah dilewati diam-diam: programnya
                // tersimpan tanpa gambar dan pengurus mengira fotonya masuk.
                return redirect()->back()->withInput()
                    ->with('error', 'Format gambar tidak didukung atau file berbahaya.');
            }
        }

        // Lihat catatan pada saveBerita: hasil simpan wajib diperiksa.
        $tersimpan = $programId
            ? $programModel->update($programId, $data)
            : $programModel->insert($data);

        if (!$tersimpan) {
            return redirect()->back()->withInput()
                ->with('error', 'Gagal menyimpan program: ' . implode(' ', $programModel->errors()));
        }

        $message = $programId ? 'Program berhasil diperbarui.' : 'Program baru berhasil ditambahkan.';

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
            
            // Alasannya ikut disebut: 'Periksa inputan Anda' memaksa pengurus
            // menebak kolom mana yang salah dari tujuh kolom yang ada.
            if (!$wargaModel->update($id, $data)) {
                return redirect()->back()->withInput()
                    ->with('error', 'Gagal memperbarui data warga: ' . implode(' ', $wargaModel->errors()));
            }
            $message = 'Data warga berhasil diperbarui.';
        } else {
            if (!$wargaModel->insert($data)) {
                return redirect()->back()->withInput()
                    ->with('error', 'Gagal menyimpan data warga: ' . implode(' ', $wargaModel->errors()));
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
            'storage'   => new Storage(),
            // View memakai $request untuk mengisi ulang kotak pencarian & filter.
            // Tanpa ini halaman gagal dengan "Undefined variable $request".
            'request'   => $this->request,
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
            // Kolom api_key/api_secret/merchant_id sengaja TIDAK ditulis:
            // ketiganya tidak ada di basis data dan tidak pernah dibaca kode mana
            // pun — gateway membaca multipay_api_key & multipay_secret_key
            // (lihat Donation::store). Menuliskannya membuat seluruh penyimpanan
            // Pengaturan Pembayaran gagal: "Unknown column 'api_key'".
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

        return redirect()->to('dashboard/pembayaran')->with('success', 'Pengaturan pembayaran berhasil disimpan.');
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
        $masjidId = session()->get('masjid_id');
        $masjid   = (new \App\Models\MasjidModel())->find($masjidId);

        return view('dashboard/broadcast/form', [
            'title'  => 'Buat Siaran Baru - Masj.id',
            'groups' => (new \App\Models\MasjidGroupModel())->aktifMilik($masjidId),
            // Dipakai formulir untuk memberi tahu lebih dulu bila kanalnya belum
            // siap, alih-alih membiarkan pengurus menulis panjang-panjang lalu
            // menekan kirim dan gagal.
            'telegramSiap' => !empty($masjid['telegram_bot_token']),
            'whatsappSiap' => !empty($masjid['whatsapp_api_key']),
        ]);
    }

    /**
     * Menyusun draf pengumuman dari poin-poin singkat pengurus (dibantu AI).
     *
     * Dijawab sebagai JSON untuk fetch(); pengurus tetap menyunting dan
     * menyetujui hasilnya sebelum benar-benar dikirim — draf ini bukan siaran.
     */
    public function draftBroadcast()
    {
        $poin = trim((string) $this->request->getPost('poin'));
        if ($poin === '') {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => 'error',
                'message' => 'Tuliskan dulu poin-poin pengumumannya.',
            ]);
        }

        $masjidId = session()->get('masjid_id');
        $masjid   = (new \App\Models\MasjidModel())->find($masjidId);

        // Nada disediakan sebagai pilihan supaya draf sesuai konteks (kabar duka
        // tidak boleh terdengar ceria). Nilai di luar daftar diabaikan.
        $nada = $this->request->getPost('nada');
        $nadaTeks = [
            'resmi'    => 'resmi dan sopan',
            'hangat'   => 'hangat dan mengajak',
            'ringkas'  => 'sangat ringkas dan padat',
            'duka'     => 'penuh empati dan berbelasungkawa',
        ][$nada] ?? 'resmi dan sopan';

        $prompt  = "Susun sebuah pengumuman resmi dari '{$masjid['name']}' untuk dikirim ke grup jamaah "
                 . "(Telegram/WhatsApp), berdasarkan poin-poin dari pengurus di bawah.\n\n";
        $prompt .= "Poin-poin dari pengurus:\n{$poin}\n\n";
        $prompt .= "Aturan:\n";
        $prompt .= "1. Bahasa Indonesia yang {$nadaTeks}, bernuansa Islami secukupnya.\n";
        // Pagar terpenting: model TIDAK boleh menambah tanggal, nominal, atau
        // detail yang tidak ada di poin — pengumuman salah yang sudah terkirim
        // ke jamaah tidak bisa ditarik.
        $prompt .= "2. JANGAN menambahkan informasi, tanggal, angka, atau nama yang tidak ada di poin. "
                 . "Bila sebuah detail tidak disebutkan, jangan mengarangnya.\n";
        $prompt .= "3. Panjang wajar untuk pesan grup, tidak bertele-tele.\n";
        $prompt .= "4. Output HANYA teks pengumuman yang siap dikirim — tanpa judul 'Pengumuman:', "
                 . "tanpa tanda kutip pembungkus, tanpa penjelasan apa pun di luar teksnya.";

        // Berkualitas: pengumuman ini dibaca seluruh jamaah. Volume rendah
        // (pengurus menyusun sesekali), jadi biaya bukan pertimbangan utama.
        $ai    = new \App\Libraries\SumoPodAI((int) $masjidId);
        $draft = $ai->chatCompletion($prompt, [
            'temperature' => 0.7,
            'max_tokens'  => 1500,
            'tier'        => 'berat',
            'feature'     => 'draft_pengumuman',
        ]);

        if (!$draft) {
            // 503, bukan 200 dengan draf kosong: pengurus harus tahu ini gagal,
            // bukan mengira AI tak punya saran.
            return $this->response->setStatusCode(503)->setJSON([
                'status'  => 'error',
                'message' => 'AI sedang tidak dapat dihubungi. Coba lagi sebentar lagi, atau tulis manual.',
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'draft'  => trim($draft),
        ]);
    }

    /**
     * Meringkas obrolan sebuah grup jamaah untuk pengurus (dibantu AI).
     *
     * Dijawab JSON untuk fetch(). Hanya grup milik masjid ini yang boleh
     * diringkas, dan hanya dari pesan yang sudah tersimpan (retensi pendek —
     * lihat MasjidGroupMessageModel).
     */
    public function summarizeGroup($groupId)
    {
        $masjidId = session()->get('masjid_id');

        // group_id dari URL WAJIB milik masjid ini.
        $grup = (new \App\Models\MasjidGroupModel())
            ->where(['id' => $groupId, 'masjid_id' => $masjidId])->first();
        if (!$grup) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error', 'message' => 'Grup tidak ditemukan.',
            ]);
        }

        $pesan = (new \App\Models\MasjidGroupMessageModel())->terbaru((int) $groupId, 150);
        if (count($pesan) < 3) {
            return $this->response->setJSON([
                'status'  => 'kosong',
                'message' => 'Belum cukup obrolan untuk diringkas. '
                           . 'Pastikan bot ada di grup dan privacy mode-nya dimatikan.',
            ]);
        }

        $percakapan = '';
        foreach ($pesan as $p) {
            $percakapan .= '[' . ($p['sender_name'] ?: 'Anonim') . '] ' . $p['text'] . "\n";
        }

        $prompt  = "Berikut cuplikan obrolan grup jamaah masjid. Sebagai asisten pengurus, "
                 . "buat ringkasan SINGKAT dalam bahasa Indonesia yang menyorot hal-hal yang perlu "
                 . "PERHATIAN atau TINDAKAN pengurus: pertanyaan yang belum terjawab, usulan, keluhan, "
                 . "atau kesepakatan penting. Abaikan obrolan basa-basi. Bila tidak ada yang penting, "
                 . "katakan begitu secara singkat.\n\n"
                 . "Obrolan:\n{$percakapan}\n\n"
                 . "Format: poin-poin ringkas, maksimal 5 poin.";

        // Berkualitas: ringkasan ini dasar pengurus mengambil tindakan; volume
        // rendah (diminta sesekali).
        $ai      = new \App\Libraries\SumoPodAI((int) $masjidId);
        $ringkas = $ai->chatCompletion($prompt, [
            'temperature' => 0.4,
            'max_tokens'  => 700,
            'tier'        => 'berat',
            'feature'     => 'ringkas_grup',
        ]);

        if (!$ringkas) {
            return $this->response->setStatusCode(503)->setJSON([
                'status'  => 'error',
                'message' => 'AI sedang tidak dapat dihubungi. Coba lagi sebentar lagi.',
            ]);
        }

        return $this->response->setJSON([
            'status'      => 'success',
            'ringkasan'   => trim($ringkas),
            'jml_pesan'   => count($pesan),
        ]);
    }

    /**
     * Halaman kelola pengingat terjadwal ke grup jamaah.
     */
    public function reminders()
    {
        $masjidId = session()->get('masjid_id');

        return view('dashboard/broadcast/reminders', [
            'title'     => 'Pengingat Terjadwal - Masj.id',
            'reminders' => (new \App\Models\MasjidReminderModel())->aktifMilik($masjidId),
            // Hanya grup aktif yang boleh jadi tujuan; tanpa grup, tak ada
            // yang bisa dikirimi.
            'groups'    => (new \App\Models\MasjidGroupModel())->aktifMilik($masjidId),
        ]);
    }

    public function saveReminder()
    {
        $masjidId = session()->get('masjid_id');
        $model    = new \App\Models\MasjidReminderModel();
        $id       = $this->request->getPost('id');

        // 'id' dari POST diperiksa kepemilikannya sebelum diperbarui.
        if ($id) {
            $milik = $model->where(['id' => $id, 'masjid_id' => $masjidId])->first();
            if (!$milik) {
                return redirect()->to('dashboard/broadcast/reminders')->with('error', 'Pengingat tidak ditemukan.');
            }
        }

        // group_id dari POST WAJIB milik masjid ini: tanpa cek ini pengingat
        // bisa diarahkan mengirim ke grup masjid lain.
        $groupId = (int) $this->request->getPost('group_id');
        $grup    = (new \App\Models\MasjidGroupModel())
            ->where(['id' => $groupId, 'masjid_id' => $masjidId])->first();
        if (!$grup) {
            return redirect()->back()->withInput()->with('error', 'Grup tujuan tidak ditemukan.');
        }

        $frekuensi = $this->request->getPost('frequency');
        $data = [
            'masjid_id'    => $masjidId,
            'group_id'     => $groupId,
            'type'         => $this->request->getPost('type'),
            'frequency'    => $frekuensi,
            // Hari hanya relevan sesuai frekuensinya; sisanya dikosongkan agar
            // tidak menyimpan angka yang menyesatkan.
            'day_of_week'  => $frekuensi === 'mingguan' ? (int) $this->request->getPost('day_of_week') : null,
            'day_of_month' => $frekuensi === 'bulanan' ? (int) $this->request->getPost('day_of_month') : null,
            'time'         => $this->request->getPost('time') . ':00',
            'is_active'    => 1,
        ];

        $tersimpan = $id ? $model->update($id, $data) : $model->insert($data);
        if (!$tersimpan) {
            return redirect()->back()->withInput()
                ->with('error', 'Gagal menyimpan pengingat: ' . implode(' ', $model->errors()));
        }

        return redirect()->to('dashboard/broadcast/reminders')
            ->with('success', $id ? 'Pengingat diperbarui.' : 'Pengingat ditambahkan.');
    }

    public function toggleReminder($id)
    {
        $masjidId = session()->get('masjid_id');
        $model    = new \App\Models\MasjidReminderModel();
        $r = $model->where(['id' => $id, 'masjid_id' => $masjidId])->first();
        if (!$r) {
            return redirect()->to('dashboard/broadcast/reminders')->with('error', 'Pengingat tidak ditemukan.');
        }
        $model->update($id, ['is_active' => (int) $r['is_active'] === 1 ? 0 : 1]);

        return redirect()->to('dashboard/broadcast/reminders')->with('success', 'Status pengingat diperbarui.');
    }

    public function deleteReminder($id)
    {
        $masjidId = session()->get('masjid_id');
        $model    = new \App\Models\MasjidReminderModel();
        if (!$model->where(['id' => $id, 'masjid_id' => $masjidId])->first()) {
            return redirect()->to('dashboard/broadcast/reminders')->with('error', 'Pengingat tidak ditemukan.');
        }
        $model->delete($id);

        return redirect()->to('dashboard/broadcast/reminders')->with('success', 'Pengingat dihapus.');
    }

    /**
     * Halaman kelola grup jamaah tujuan siaran.
     */
    public function groups()
    {
        $masjidId = session()->get('masjid_id');
        $masjid   = (new \App\Models\MasjidModel())->find($masjidId);

        return view('dashboard/broadcast/groups', [
            'title'        => 'Grup Jamaah - Masj.id',
            'groups'       => (new \App\Models\MasjidGroupModel())
                                ->where('masjid_id', $masjidId)->orderBy('name')->findAll(),
            'telegramSiap' => !empty($masjid['telegram_bot_token']),
            'whatsappSiap' => !empty($masjid['whatsapp_api_key']),
        ]);
    }

    public function saveGroup()
    {
        $masjidId   = session()->get('masjid_id');
        $groupModel = new \App\Models\MasjidGroupModel();
        $id         = $this->request->getPost('id');

        // 'id' dari POST tidak dipercaya: tanpa pemeriksaan ini, mengirim id
        // milik masjid lain akan menimpa datanya sekaligus memindahkan
        // kepemilikannya, karena masjid_id di bawah ditulis dari session.
        if ($id) {
            $milik = $groupModel->where(['id' => $id, 'masjid_id' => $masjidId])->first();

            if (!$milik) {
                return redirect()->to('dashboard/broadcast/groups')
                    ->with('error', 'Grup tidak ditemukan.');
            }
        }

        $data = [
            'masjid_id' => $masjidId,
            'channel'   => $this->request->getPost('channel'),
            'group_id'  => trim((string) $this->request->getPost('group_id')),
            'name'      => trim((string) $this->request->getPost('name')),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];

        $tersimpan = $id ? $groupModel->update($id, $data) : $groupModel->insert($data);

        if (!$tersimpan) {
            return redirect()->back()->withInput()
                ->with('error', 'Gagal menyimpan grup: ' . implode(' ', $groupModel->errors()));
        }

        return redirect()->to('dashboard/broadcast/groups')
            ->with('success', $id ? 'Grup diperbarui.' : 'Grup berhasil didaftarkan.');
    }

    public function deleteGroup($id)
    {
        $masjidId   = session()->get('masjid_id');
        $groupModel = new \App\Models\MasjidGroupModel();

        if (!$groupModel->where(['id' => $id, 'masjid_id' => $masjidId])->first()) {
            return redirect()->to('dashboard/broadcast/groups')->with('error', 'Grup tidak ditemukan.');
        }

        $groupModel->delete($id);

        return redirect()->to('dashboard/broadcast/groups')->with('success', 'Grup dihapus.');
    }

    /**
     * Menyalakan/mematikan sebuah grup.
     *
     * Aktivasi adalah persetujuan pengurus atas grup yang dicatat webhook: sejak
     * aktif, bot melayani grup itu dan grup itu menerima siaran. Karena itu
     * dibatasi Admin Masjid, seperti mendaftarkan grup.
     */
    public function toggleGroup($id)
    {
        $masjidId   = session()->get('masjid_id');
        $groupModel = new \App\Models\MasjidGroupModel();

        $grup = $groupModel->where(['id' => $id, 'masjid_id' => $masjidId])->first();
        if (!$grup) {
            return redirect()->to('dashboard/broadcast/groups')->with('error', 'Grup tidak ditemukan.');
        }

        $aktif = (int) $grup['is_active'] === 1 ? 0 : 1;
        $groupModel->update($id, ['is_active' => $aktif]);

        return redirect()->to('dashboard/broadcast/groups')
            ->with('success', $aktif ? 'Grup "' . $grup['name'] . '" diaktifkan.' : 'Grup "' . $grup['name'] . '" dinonaktifkan.');
    }

    /**
     * Uji kirim ke satu grup, supaya pengurus tahu grupnya benar-benar
     * terjangkau sebelum menyiarkan pengumuman sungguhan ke jamaah.
     */
    public function testGroup($id)
    {
        $masjidId = session()->get('masjid_id');
        $masjid   = (new \App\Models\MasjidModel())->find($masjidId);

        $grup = (new \App\Models\MasjidGroupModel())
            ->where(['id' => $id, 'masjid_id' => $masjidId])->first();

        if (!$grup) {
            return redirect()->to('dashboard/broadcast/groups')->with('error', 'Grup tidak ditemukan.');
        }

        $kanal = \App\Libraries\Channel\ChannelFactory::untuk($grup['channel'], $masjid);
        $pesan = $grup['channel'] === 'telegram'
            ? '<b>Uji koneksi</b>' . "\n\n" . 'Grup ini sudah terhubung dengan ' . esc($masjid['name']) . '.'
            : '*Uji koneksi*' . "\n\n" . 'Grup ini sudah terhubung dengan ' . $masjid['name'] . '.';

        if (!$kanal->kirim($grup['group_id'], $pesan)) {
            return redirect()->to('dashboard/broadcast/groups')
                ->with('error', 'Uji kirim gagal: ' . $kanal->pesanGalat());
        }

        return redirect()->to('dashboard/broadcast/groups')
            ->with('success', 'Uji kirim berhasil. Cek pesan di grup ' . $grup['name'] . '.');
    }

    /**
     * Mengirim siaran ke grup jamaah yang dipilih (Telegram / WhatsApp).
     *
     * Sebelumnya method ini mengirim EMAIL satu per satu ke subscriber — bukan
     * ke grup — dan tidak pernah dipakai: subscriber di produksi nol. Selain
     * itu ia melapor "berhasil dikirim ke 0 subscriber" ketika setiap
     * pengirimannya gagal, karena hasil $email->send() hanya dihitung, tidak
     * pernah dijadikan sebab kegagalan.
     */
    public function sendBroadcast()
    {
        $masjidId = session()->get('masjid_id');
        $subject  = $this->request->getPost('subject');
        $content  = $this->request->getPost('content');
        $groupIds = $this->request->getPost('group_ids');

        if (empty($groupIds) || !is_array($groupIds)) {
            return redirect()->back()->withInput()
                ->with('error', 'Pilih setidaknya satu grup tujuan.');
        }

        // group_ids datang dari POST: hanya grup milik masjid ini yang boleh
        // dituju. Tanpa ini pengurus satu masjid bisa menyiarkan pengumuman ke
        // grup jamaah masjid lain — dan pesan yang sudah terkirim tidak bisa
        // ditarik kembali.
        $groupModel = new \App\Models\MasjidGroupModel();
        $grup = $groupModel->whereIn('id', $groupIds)
            ->where(['masjid_id' => $masjidId, 'is_active' => 1])
            ->findAll();

        if (empty($grup)) {
            return redirect()->back()->withInput()
                ->with('error', 'Grup tujuan tidak ditemukan.');
        }

        $masjid = (new \App\Models\MasjidModel())->find($masjidId);

        $broadcastModel = new \App\Models\MasjidBroadcastModel();
        $berhasil = [];
        $gagal    = [];

        foreach ($grup as $g) {
            $kanal = \App\Libraries\Channel\ChannelFactory::untuk($g['channel'], $masjid);
            // Teksnya disusun per kanal: Telegram dan WhatsApp memakai penanda
            // tebal yang berbeda, dan HTML yang dikirim ke WhatsApp akan terbaca
            // mentah sebagai '<b>' oleh jamaah.
            $terkirim = $kanal->kirim(
                $g['group_id'],
                $this->susunPesanSiaran($masjid, $subject, $content, $g['channel'])
            );

            // Tiap grup dicatat sendiri: satu grup gagal tidak boleh membuat
            // yang lain ikut dianggap gagal, dan pengurus perlu tahu persis
            // grup mana yang perlu diulang.
            $broadcastModel->insert([
                'masjid_id'       => $masjidId,
                'subject'         => $subject,
                'content'         => $content,
                'type'            => $g['channel'],
                'group_id'        => $g['group_id'],
                'status'          => $terkirim ? 'sent' : 'failed',
                'recipient_count' => $terkirim ? 1 : 0,
                'sent_at'         => $terkirim ? date('Y-m-d H:i:s') : null,
            ]);

            if ($terkirim) {
                $berhasil[] = $g['name'];
            } else {
                $gagal[] = $g['name'] . ' (' . $kanal->pesanGalat() . ')';
            }
        }

        if (empty($berhasil)) {
            return redirect()->back()->withInput()
                ->with('error', 'Siaran tidak terkirim ke satu grup pun. ' . implode(' ', $gagal));
        }

        $kabar = 'Siaran terkirim ke ' . count($berhasil) . ' grup: ' . implode(', ', $berhasil) . '.';
        if (!empty($gagal)) {
            $kabar .= ' Gagal: ' . implode(' ', $gagal);
        }

        return redirect()->to('dashboard/broadcast')->with('success', $kabar);
    }

    /**
     * Menyusun teks siaran sesuai kanalnya.
     *
     * Telegram hanya menerima HTML terbatas (b, i, a, code, pre); tag di luar
     * itu membuat Telegram MENOLAK seluruh pesannya, jadi isi dari editor
     * dibersihkan dulu. WhatsApp tidak mengenal HTML sama sekali — mengirim
     * '<b>' ke sana membuat jamaah membaca tanda kurung sikunya apa adanya.
     */
    private function susunPesanSiaran(array $masjid, string $subject, string $content, string $channel): string
    {
        if ($channel === 'telegram') {
            return '<b>' . esc($subject) . '</b>' . "\n\n"
                . trim(strip_tags($content, '<b><i><a><code><pre>')) . "\n\n"
                . '— ' . esc($masjid['name']);
        }

        // WhatsApp: teks polos, tebal ditandai *bintang*. Entitas HTML dari
        // editor ikut dikembalikan agar '&amp;' tidak terbaca mentah.
        $isi = html_entity_decode(strip_tags($content), ENT_QUOTES, 'UTF-8');

        return '*' . $subject . '*' . "\n\n"
            . trim($isi) . "\n\n"
            . '— ' . $masjid['name'];
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
        if (!$item) return redirect()->to('dashboard/warga')->with('error', 'Data tidak ditemukan.');

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

        // warga_id dan program_id datang dari POST. Tanpa pemeriksaan ini,
        // bantuan bisa ditempelkan ke warga atau program milik masjid lain —
        // dan karena daftar penyaluran menggabungkan tabel warga, nama warga
        // masjid lain ikut tampil di layar masjid ini.
        $wargaId = $this->request->getPost('warga_id') ?: null;
        if ($wargaId !== null) {
            $warga = (new \App\Models\MasjidWargaModel())
                ->where(['id' => $wargaId, 'masjid_id' => $masjidId])->first();

            if (!$warga) {
                return redirect()->back()->withInput()->with('error', 'Data warga tidak ditemukan.');
            }
        }

        $programId = $this->request->getPost('program_id') ?: null;
        if ($programId !== null) {
            $program = (new \App\Models\MasjidProgramModel())
                ->where(['id' => $programId, 'masjid_id' => $masjidId])->first();

            if (!$program) {
                return redirect()->back()->withInput()->with('error', 'Program tidak ditemukan.');
            }
        }

        $data = [
            'masjid_id'   => $masjidId,
            'warga_id'    => $wargaId,
            'program_id'  => $programId,
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

            if (!$distModel->update($id, $data)) {
                return redirect()->back()->withInput()
                    ->with('error', 'Gagal memperbarui penyaluran: ' . implode(' ', $distModel->errors()));
            }
            $message = 'Data penyaluran diperbarui.';
        } else {
            if (!$distModel->insert($data)) {
                return redirect()->back()->withInput()
                    ->with('error', 'Gagal menyimpan penyaluran: ' . implode(' ', $distModel->errors()));
            }
            $message = 'Data penyaluran berhasil disimpan.';

            // OPTIONAL: Auto-create Expense Transaction
            if ($this->request->getPost('create_expense') == 1 && $data['type'] == 'money') {
                $financeModel = new \App\Models\MasjidFinanceTransactionModel();

                // Kategori WAJIB ada: MasjidFinanceTransactionModel mensyaratkan
                // category_id, sehingga mengirim null membuat insert selalu
                // ditolak validasi. Karena hasilnya dulu tidak diperiksa,
                // pengurus tetap dibalas '(Transaksi pengeluaran otomatis
                // dibuat)' padahal kas masjid tidak pernah mencatat apa pun —
                // uang bantuan keluar tanpa jejak di pembukuan.
                $catModel = new \App\Models\MasjidFinanceCategoryModel();
                $kategori = $catModel->where([
                    'masjid_id' => $masjidId,
                    'slug'      => 'penyaluran-bantuan',
                ])->first();

                $catId = $kategori['id'] ?? $catModel->insert([
                    'masjid_id' => $masjidId,
                    'name'      => 'Penyaluran Bantuan Warga',
                    'type'      => 'pengeluaran',
                    'slug'      => 'penyaluran-bantuan',
                ]);

                $tercatat = $catId && $financeModel->insert([
                    'masjid_id'   => $masjidId,
                    'type'        => 'pengeluaran',
                    'category_id' => $catId,
                    'amount'      => $data['amount'],
                    'date'        => $data['date'],
                    'description' => 'Penyaluran bantuan: ' . ($data['description'] ?: '-'),
                ]);

                // Penyalurannya sendiri sudah tersimpan, jadi kegagalan di sini
                // dilaporkan apa adanya — bukan diam-diam diakui berhasil.
                $message .= $tercatat
                    ? ' Pengeluaran otomatis dicatat di Keuangan.'
                    : ' Namun pencatatan otomatis ke Keuangan gagal — silakan catat manual.';
            }
        }

        return redirect()->to('dashboard/warga')->with('success', $message);
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
        $types = $this->request->getPost('type');
        $programIds = $this->request->getPost('program_id');
        $selected = $this->request->getPost('selected');

        if (empty($selected)) {
            return redirect()->back()->with('error', 'Pilih setidaknya satu transaksi untuk dipetakan.');
        }

        // program_id datang dari POST, jadi tidak boleh dipercaya: tanpa
        // penyaringan ini pengurus bisa menempelkan transaksi ke program milik
        // masjid lain. Hanya program masjid sendiri yang boleh dipakai.
        // intval() wajib: basis data mengembalikan id sebagai string, sehingga
        // perbandingan ketat di bawah tidak akan pernah cocok tanpa ini.
        $programSah = array_map('intval', array_column(
            (new \App\Models\MasjidProgramModel())->select('id')->where('masjid_id', $masjidId)->findAll(),
            'id'
        ));

        // Mutasi bank berisi uang masuk DAN uang keluar, sehingga dua kategori
        // dibutuhkan — kategori pemasukan tidak sah menampung pengeluaran.
        $kategori = function (string $slug, string $nama, string $tipe) use ($catModel, $masjidId) {
            $ada = $catModel->where(['masjid_id' => $masjidId, 'slug' => $slug])->first();

            return $ada ? $ada['id'] : $catModel->insert([
                'masjid_id' => $masjidId,
                'name'      => $nama,
                'type'      => $tipe,
                'slug'      => $slug,
            ]);
        };
        $catMasuk  = $kategori('donasi-terikat', 'Donasi Terikat Program', 'pemasukan');
        $catKeluar = $kategori('pengeluaran-program', 'Pengeluaran Program', 'pengeluaran');

        $successCount = 0;
        $dilewati = 0;
        foreach ($selected as $index) {
            if (empty($programIds[$index]) || !in_array((int) $programIds[$index], $programSah, true)) {
                $dilewati++;
                continue;
            }

            // Tanggal sudah dibakukan ke 'Y-m-d' oleh BankMutationParser.
            $tanggal = parse_tanggal($dates[$index] ?? '');
            if ($tanggal === null) {
                $dilewati++;
                continue;
            }

            $keluar = ($types[$index] ?? 'CR') === 'DB';

            $financeModel->insert([
                'masjid_id'   => $masjidId,
                'category_id' => $keluar ? $catKeluar : $catMasuk,
                'program_id'  => $programIds[$index],
                'date'        => $tanggal,
                'amount'      => $amounts[$index],
                'type'        => $keluar ? 'pengeluaran' : 'pemasukan',
                'description' => 'Mutasi Bank: ' . $descs[$index]
            ]);
            $successCount++;
        }

        $pesan = $successCount . ' transaksi berhasil dipetakan ke laporan keuangan.';
        if ($dilewati > 0) {
            // Sebelumnya baris tanpa program dibuang tanpa sepatah kata pun,
            // sehingga pengurus mengira semuanya tersimpan.
            $pesan .= ' ' . $dilewati . ' transaksi dilewati karena belum dipilih programnya.';
        }

        return redirect()->to(base_url('dashboard/keuangan'))->with('success', $pesan);
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

    public function followers(): string
    {
        $masjidId = session()->get('masjid_id');
        $db = \Config\Database::connect();
        
        // Use Query Builder instead of model to join with users table
        $followers = $db->table('masjid_followers mf')
            ->select('mf.id as follow_id, mf.created_at, u.id as user_id, u.name, u.email, u.phone, u.avatar')
            ->join('users u', 'u.id = mf.user_id')
            ->where('mf.masjid_id', $masjidId)
            ->orderBy('mf.created_at', 'DESC')
            ->get()->getResultArray();

        // Pass pengurus info to check if a follower is already a pengurus
        $pengurusIds = $db->table('masjid_pengurus')
            ->select('user_id')
            ->where('masjid_id', $masjidId)
            ->get()->getResultArray();
            
        $pengurusIds = array_column($pengurusIds, 'user_id');

        return view('dashboard/followers/index', [
            'title'       => 'Daftar Jamaah (Follower) - Masj.id',
            'followers'   => $followers,
            'pengurusIds' => $pengurusIds
        ]);
    }

    public function promoteFollower()
    {
        $masjidId = session()->get('masjid_id');
        $userId = $this->request->getPost('user_id');
        $role = $this->request->getPost('role') ?? 'admin';
        $title = $this->request->getPost('title') ?? 'Pengurus';

        if (empty($userId)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User tidak ditemukan.']);
        }

        $pengurusModel = new \App\Models\MasjidPengurusModel();
        
        $exists = $pengurusModel->where(['masjid_id' => $masjidId, 'user_id' => $userId])->first();
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
            return $this->response->setJSON(['status' => 'success', 'message' => 'Jamaah berhasil diangkat menjadi pengurus.']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal mengangkat pengurus.']);
    }
}
