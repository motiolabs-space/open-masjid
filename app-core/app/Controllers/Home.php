<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $masjidModel = new \App\Models\MasjidModel();
        $wargaModel = new \App\Models\MasjidWargaModel();
        $financeModel = new \App\Models\MasjidFinanceTransactionModel();

        // 1. Total Masjid (Count all active mosques)
        // Assuming all in 'masjid' table are valid/active for now.
        $totalMasjid = $masjidModel->countAll();

        // 2. Total Dana Terkelola (Sum of all 'pemasukan')
        $totalDanaResult = $financeModel->selectSum('amount')->where('type', 'pemasukan')->first();
        $totalDana = $totalDanaResult['amount'] ?? 0;

        // 3. Total Jamaah (Count all warga)
        $totalJamaah = $wargaModel->countAll();

        return view('landing', [
            'title'       => 'Masj.id - Manajemen Masjid Modern & Transparan',
            'stats'       => [
                'masjid' => $totalMasjid,
                'dana'   => $totalDana,
                'jamaah' => $totalJamaah
            ]
        ]);
    }

    public function fitur(): string
    {
        $masjidModel = new \App\Models\MasjidModel();
        $wargaModel = new \App\Models\MasjidWargaModel();
        $financeModel = new \App\Models\MasjidFinanceTransactionModel();
        $programModel = new \App\Models\MasjidProgramModel();

        // 1. Total Masjid
        $totalMasjid = $masjidModel->countAll();

        // 2. Total Donasi (Sum of all 'pemasukan')
        $totalDonasiResult = $financeModel->selectSum('amount')->where('type', 'pemasukan')->first();
        $totalDonasi = $totalDonasiResult['amount'] ?? 0;

        // 3. Jamaah Aktif
        $totalJamaah = $wargaModel->countAll();

        // 4. Provinsi Terjangkau (Unique provinces)
        $totalProvinsi = $masjidModel->select('provinsi')->distinct()->countAllResults();

        // 5. Program Aktif (Published programs)
        $totalProgramAktif = $programModel->where('status', 'published')->countAllResults();

        return view('fitur', [
            'title' => 'Fitur Lengkap Platform Masj.id',
            'stats' => [
                'masjid'        => $totalMasjid,
                'donasi'        => $totalDonasi,
                'jamaah'        => $totalJamaah,
                'provinsi'      => $totalProvinsi,
                'program_aktif' => $totalProgramAktif
            ]
        ]);
    }

    public function kebaikan(): string
    {
        $masjidModel = new \App\Models\MasjidModel();
        $wargaModel = new \App\Models\MasjidWargaModel();
        $financeModel = new \App\Models\MasjidFinanceTransactionModel();
        $programModel = new \App\Models\MasjidProgramModel();

        // 1. Total Masjid
        $totalMasjid = $masjidModel->countAll();

        // 2. Total Dana (managed funds)
        $totalDanaResult = $financeModel->selectSum('amount')->where('type', 'pemasukan')->first();
        $totalDana = $totalDanaResult['amount'] ?? 0;

        // 3. Beneficiaries (Jamaah/Warga)
        $totalJamaah = $wargaModel->countAll();

        // 4. Active Programs
        $totalProgram = $programModel->where('status', 'published')->countAllResults();

        return view('program_kebaikan', [
            'title' => 'Statistik Dampak & Program - Masj.id',
            'stats' => [
                'masjid' => $totalMasjid,
                'dana'   => $totalDana,
                'jamaah' => $totalJamaah,
                'program'=> $totalProgram
            ]
        ]);
    }

    public function tentang(): string
    {
        return view('tentang_kami', ['title' => 'Tentang Kami - Yayasan Masjid Digital Indonesia']);
    }

    public function laporan(): string
    {
        return view('laporan_transparansi', ['title' => 'Laporan Transparansi Donasi - Masj.id']);
    }

    public function panduan(): string
    {
        return view('panduan', ['title' => 'Pusat Bantuan & Tutorial - Masj.id']);
    }

    public function kontak(): string
    {
        return view('kontak', ['title' => 'Kontak Kami - Masj.id']);
    }

    public function privacy(): string
    {
        return "Halaman Kebijakan Privasi (Privacy Policy)";
    }

    public function term(): string
    {
        return "Halaman Syarat & Ketentuan (Terms & Conditions)";
    }

    public function login(): string
    {
        return view('login');
    }

    public function register(): string
    {
        return view('register');
    }

    public function masjid($username): string
    {
        $masjidId = null;
        $masjidModel = new \App\Models\MasjidModel();
        $masjid = $masjidModel->where('username', $username)->first();

        if (!$masjid) {
            // Rute catch-all '(:any)' membuat setiap URL tak dikenal mendarat di
            // sini, jadi tampilkan halaman ramah + ajakan mendaftar bagi pengurus,
            // bukan 404 mentah. Status tetap 404 agar tidak terindeks mesin pencari.
            $this->response->setStatusCode(404);
            return view('public/masjid_not_found', [
                'title'    => 'Masjid Tidak Ditemukan - Masj.id',
                'username' => $username,
            ]);
        }

        $masjidId = $masjid['id'];
        $db = \Config\Database::connect();
        
        // Fetch Pengurus
        $pengurus = $db->table('masjid_pengurus')
            ->select('masjid_pengurus.*, users.name as user_name, users.phone as user_phone, users.email as user_email')
            ->join('users', 'users.id = masjid_pengurus.user_id')
            ->where('masjid_id', $masjidId)
            ->get()
            ->getResultArray();

        // Fetch Gallery
        $galleryModel = new \App\Models\MasjidGalleryModel();
        $gallery = $galleryModel->where('masjid_id', $masjidId)->findAll();

        // Fetch Service Areas
        $wilayahModel = new \App\Models\MasjidWilayahModel();
        $wilayah = $wilayahModel->where('masjid_id', $masjid['id'])->findAll();

        $newsModel = new \App\Models\MasjidNewsModel();
        $news = $newsModel->select('masjid_news.*, masjid_news_categories.name as category_name')
            ->join('masjid_news_categories', 'masjid_news_categories.id = masjid_news.category_id', 'left')
            ->where(['masjid_news.masjid_id' => $masjid['id'], 'masjid_news.status' => 'published'])
            ->orderBy('masjid_news.created_at', 'DESC')
            ->limit(3)
            ->findAll();

        $programModel = new \App\Models\MasjidProgramModel();
        $programs = $programModel->where(['masjid_id' => $masjid['id'], 'status' => 'published'])
            ->orderBy('date_start', 'ASC')
            ->limit(3)
            ->findAll();

        $financeModel = new \App\Models\MasjidFinanceTransactionModel();
        $financeSummary = $financeModel->getSummary($masjid['id']);

        // Fetch Worship Schedules
        $schedModel = new \App\Models\MasjidScheduleModel();
        $todaySchedules = $schedModel->where('masjid_id', $masjidId)
            ->where('date', date('Y-m-d'))
            ->orderBy('prayer_type', 'ASC')
            ->findAll();
        
        // Next Friday
        $nextFriday = date('Y-m-d', strtotime('next Friday'));
        if (date('l') === 'Friday') $nextFriday = date('Y-m-d'); // If today is Friday
        
        $fridaySchedule = $schedModel->where('masjid_id', $masjidId)
            ->where('date', $nextFriday)
            ->where('prayer_type', 'jumat')
            ->first();

        // Fetch Prayer Times from AlAdhan API (Coordinate Based)
        $prayerData = null;
        // Verify coordinates exist and are not default 0
        if (!empty($masjid['latitude']) && !empty($masjid['longitude']) && ($masjid['latitude'] != 0 || $masjid['longitude'] != 0)) {
            $lat = $masjid['latitude'];
            $long = $masjid['longitude'];
            $date = date('d-m-Y');
            
            $cacheKey = "prayer_aladhan_{$masjid['id']}_{$date}";
            $prayerData = cache($cacheKey);

            if (!$prayerData) {
                try {
                    $client = \Config\Services::curlrequest();
                    // Method 20 is Ministry of Religious Affairs (Kemenag)
                    // WAJIB https: AlAdhan membalas 301 ke HTTPS untuk permintaan http,
                    // sehingga respons bukan JSON dan jadwal sholat gagal tampil.
                    $apiUrl = "https://api.aladhan.com/v1/timings/$date?latitude=$lat&longitude=$long&method=20";
                    
                    $response = $client->request('GET', $apiUrl, [
                        'timeout' => 5
                    ]);
                    
                    $body = json_decode($response->getBody(), true);
                    if (isset($body['code']) && $body['code'] == 200) {
                        $prayerData = $body['data'];
                        cache()->save($cacheKey, $prayerData, 86400); // 24 hours
                    }
                } catch (\Exception $e) {
                    log_message('error', 'AlAdhan API Error: ' . $e->getMessage());
                }
            }
        }

        $storage = new \App\Libraries\Storage();

        return view('public/masjid_profile', [
            'title'          => esc($masjid['name']),
            'masjid'         => $masjid,
            'pengurus'       => $pengurus,
            'gallery'        => $gallery,
            'service_areas'  => $wilayah,
            'news'           => $news,
            'programs'       => $programs,
            'financeSummary' => $financeSummary,
            'todaySchedules' => $todaySchedules,
            'fridaySchedule' => $fridaySchedule,
            'prayerData'     => $prayerData,
            'storage'        => $storage
        ]);
    }

    public function newsDetail($username, $slug): string
    {
        $masjidModel = new \App\Models\MasjidModel();
        $masjid = $masjidModel->where('username', $username)->first();

        if (!$masjid) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Masjid tidak ditemukan.");
        }

        $newsModel = new \App\Models\MasjidNewsModel();
        $news = $newsModel->select('masjid_news.*, masjid_news_categories.name as category_name')
            ->join('masjid_news_categories', 'masjid_news_categories.id = masjid_news.category_id', 'left')
            ->where(['masjid_news.slug' => $slug, 'masjid_news.masjid_id' => $masjid['id']])
            ->first();

        if (!$news) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Berita tidak ditemukan.");
        }

        // Increment views
        $newsModel->update($news['id'], ['views' => $news['views'] + 1]);

        return view('public/news_detail', [
            'title'   => esc($news['title']) . ' - ' . esc($masjid['name']),
            'masjid'  => $masjid,
            'news'    => $news,
            'storage' => new \App\Libraries\Storage()
        ]);
    }

    public function newsList($username): string
    {
        $masjidModel = new \App\Models\MasjidModel();
        $masjid = $masjidModel->where('username', $username)->first();

        if (!$masjid) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Masjid tidak ditemukan.");
        }

        $newsModel = new \App\Models\MasjidNewsModel();
        $categoryModel = new \App\Models\MasjidNewsCategoryModel();

        // Get filter from GET
        $catSlug = $this->request->getGet('category');
        
        $query = $newsModel->select('masjid_news.*, masjid_news_categories.name as category_name')
            ->join('masjid_news_categories', 'masjid_news_categories.id = masjid_news.category_id', 'left')
            ->where(['masjid_news.masjid_id' => $masjid['id'], 'masjid_news.status' => 'published'])
            ->orderBy('masjid_news.created_at', 'DESC');

        if ($catSlug) {
            $query->where('masjid_news_categories.slug', $catSlug);
        }

        $news = $query->findAll();
        $categories = $categoryModel->where('masjid_id', $masjid['id'])->findAll();

        return view('public/news_list', [
            'title'      => 'Berita & Kegiatan - ' . esc($masjid['name']),
            'masjid'     => $masjid,
            'news'       => $news,
            'categories' => $categories,
            'activeCat'  => $catSlug,
            'storage'    => new \App\Libraries\Storage()
        ]);
    }

    public function programList($username): string
    {
        $masjidModel = new \App\Models\MasjidModel();
        $masjid = $masjidModel->where('username', $username)->first();

        if (!$masjid) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Masjid tidak ditemukan.");
        }

        $programModel = new \App\Models\MasjidProgramModel();
        $query = $programModel->select('masjid_programs.*, masjid_program_categories.name as category_name')
            ->join('masjid_program_categories', 'masjid_program_categories.id = masjid_programs.category_id', 'left')
            ->where(['masjid_programs.masjid_id' => $masjid['id'], 'masjid_programs.status' => 'published']);

        $catSlug = $this->request->getGet('category');
        if ($catSlug) {
            $categoryModel = new \App\Models\MasjidProgramCategoryModel();
            $category = $categoryModel->where(['slug' => $catSlug, 'masjid_id' => $masjid['id']])->first();
            if ($category) {
                $query->where('masjid_programs.category_id', $category['id']);
            }
        }

        $programs = $query->orderBy('date_start', 'ASC')->findAll();

        $categoryModel = new \App\Models\MasjidProgramCategoryModel();
        $categories = $categoryModel->where('masjid_id', $masjid['id'])->findAll();

        return view('public/program_list', [
            'title'      => 'Program & Kegiatan - ' . esc($masjid['name']),
            'masjid'     => $masjid,
            'programs'   => $programs,
            'categories' => $categories,
            'activeCat'  => $catSlug,
            'storage'    => new \App\Libraries\Storage()
        ]);
    }

    public function programDetail($username, $slug): string
    {
        $masjidModel = new \App\Models\MasjidModel();
        $masjid = $masjidModel->where('username', $username)->first();

        if (!$masjid) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Masjid tidak ditemukan.");
        }

        $programModel = new \App\Models\MasjidProgramModel();
        $program = $programModel->select('masjid_programs.*, masjid_program_categories.name as category_name')
            ->join('masjid_program_categories', 'masjid_program_categories.id = masjid_programs.category_id', 'left')
            ->where(['masjid_programs.masjid_id' => $masjid['id'], 'masjid_programs.slug' => $slug])
            ->first();

        if (!$program) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Program tidak ditemukan.");
        }

        return view('public/program_detail', [
            'title'   => esc($program['title']) . ' - ' . esc($masjid['name']),
            'masjid'  => $masjid,
            'program' => $program,
            'storage' => new \App\Libraries\Storage()
        ]);
    }

    public function publicReport($username): string
    {
        $masjidModel = new \App\Models\MasjidModel();
        $masjid = $masjidModel->where('username', $username)->first();

        if (!$masjid || ($masjid['menu_laporan'] ?? 1) == 0) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Laporan tidak tersedia atau dinonaktifkan.");
        }

        $financeModel = new \App\Models\MasjidFinanceTransactionModel();
        $programModel = new \App\Models\MasjidProgramModel();
        
        // Get filters from GET
        $start = $this->request->getGet('start_date') ?: date('Y-m-01');
        $end = $this->request->getGet('end_date') ?: date('Y-m-d');

        $query = $financeModel->select('masjid_finance_transactions.*, masjid_finance_categories.name as category_name, masjid_programs.title as program_title')
            ->join('masjid_finance_categories', 'masjid_finance_categories.id = masjid_finance_transactions.category_id', 'left')
            ->join('masjid_programs', 'masjid_programs.id = masjid_finance_transactions.program_id', 'left')
            ->where('masjid_finance_transactions.masjid_id', $masjid['id'])
            ->where('date >=', $start)
            ->where('date <=', $end)
            ->orderBy('date', 'DESC');

        $transactions = $query->findAll();
        $summary = $financeModel->getSummary($masjid['id']);

        // Impact Analysis: Group expenses by category
        $expenditureByCat = $financeModel->select('masjid_finance_categories.name, SUM(amount) as total')
            ->join('masjid_finance_categories', 'masjid_finance_categories.id = masjid_finance_transactions.category_id')
            ->where([
                'masjid_finance_transactions.masjid_id' => $masjid['id'],
                // Wajib diberi prefix: kolom 'type' ada di kedua tabel yang di-join.
                'masjid_finance_transactions.type'      => 'pengeluaran',
            ])
            ->where('date >=', $start)
            ->where('date <=', $end)
            ->groupBy('category_id')
            ->get()
            ->getResultArray();

        return view('public/finance_report', [
            'title'            => 'Laporan Amanah - ' . esc($masjid['name']),
            'masjid'           => $masjid,
            'transactions'     => $transactions,
            'summary'          => $summary,
            'expenditureByCat' => $expenditureByCat,
            'filters'          => ['start' => $start, 'end' => $end],
            'storage'          => new \App\Libraries\Storage()
        ]);
    }

    public function display($username): string
    {
        $masjidModel = new \App\Models\MasjidModel();
        $masjid = $masjidModel->where('username', $username)->first();

        if (!$masjid) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Masjid tidak ditemukan.");
        }

        $masjidId = $masjid['id'];
        $financeModel = new \App\Models\MasjidFinanceTransactionModel();
        $programModel = new \App\Models\MasjidProgramModel();
        $newsModel = new \App\Models\MasjidNewsModel();
        $schedModel = new \App\Models\MasjidScheduleModel();

        // 1. Finance Summary
        $financeSummary = $financeModel->getSummary($masjidId);

        // 2. Active Programs
        $programs = $programModel->where(['masjid_id' => $masjidId, 'status' => 'published'])
            ->orderBy('date_start', 'ASC')
            ->limit(5)
            ->findAll();

        // 3. Recent News
        $news = $newsModel->where(['masjid_id' => $masjidId, 'status' => 'published'])
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->findAll();

        // 4. Prayer Times (Today)
        $prayerData = null;
        if (!empty($masjid['latitude']) && !empty($masjid['longitude'])) {
            $lat = $masjid['latitude'];
            $long = $masjid['longitude'];
            $date = date('d-m-Y');
            
            $cacheKey = "prayer_aladhan_{$masjid['id']}_{$date}";
            $prayerData = cache($cacheKey);

            if (!$prayerData) {
                try {
                    $client = \Config\Services::curlrequest();
                    // WAJIB https: AlAdhan membalas 301 ke HTTPS untuk permintaan http,
                    // sehingga respons bukan JSON dan jadwal sholat gagal tampil.
                    $apiUrl = "https://api.aladhan.com/v1/timings/$date?latitude=$lat&longitude=$long&method=20";
                    $response = $client->request('GET', $apiUrl, ['timeout' => 5]);
                    $body = json_decode($response->getBody(), true);
                    if (isset($body['code']) && $body['code'] == 200) {
                        $prayerData = $body['data'];
                        cache()->save($cacheKey, $prayerData, 86400);
                    }
                } catch (\Exception $e) {}
            }
        }

        // 5. Social Impact Highlights (Recent Pengeluaran with non-empty descriptions)
        $impactHighlights = $financeModel->select('masjid_finance_transactions.*, masjid_finance_categories.name as category_name')
            ->join('masjid_finance_categories', 'masjid_finance_categories.id = masjid_finance_transactions.category_id', 'left')
            ->where([
                'masjid_finance_transactions.masjid_id' => $masjidId,
                // Wajib diberi prefix: kolom 'type' ada di kedua tabel yang di-join.
                'masjid_finance_transactions.type'      => 'pengeluaran',
            ])
            ->orderBy('date', 'DESC')
            ->limit(5)
            ->findAll();

        // 6. Donasi terbaru yang berhasil — ditampilkan sebagai apresiasi donatur.
        $db = \Config\Database::connect();
        $recentDonations = $db->table('masjid_donations')
            ->select('donor_name, amount, paid_at, created_at')
            ->where('masjid_id', $masjidId)
            ->where('status', 'success')
            ->orderBy('paid_at', 'DESC')
            ->limit(8)
            ->get()->getResultArray();

        // 7. Tanggal Hijriah — sudah tersedia gratis pada respons AlAdhan.
        $hijriDate = null;
        if (!empty($prayerData['date']['hijri'])) {
            $h = $prayerData['date']['hijri'];
            $hijriDate = trim(sprintf(
                '%s %s %s H',
                $h['day'] ?? '',
                $h['month']['en'] ?? '',
                $h['year'] ?? ''
            ));
        }

        return view('public/display_tv', [
            'title'            => 'Display TV - ' . esc($masjid['name']),
            'masjid'           => $masjid,
            'financeSummary'   => $financeSummary,
            'programs'         => $programs,
            'news'             => $news,
            'impactHighlights' => $impactHighlights,
            'recentDonations'  => $recentDonations,
            'prayerData'       => $prayerData,
            'hijriDate'        => $hijriDate,
            'runningText'      => $this->_buildRunningText($masjid, $programs, $news),
            'storage'          => new \App\Libraries\Storage()
        ]);
    }

    /**
     * Teks berjalan untuk Display TV.
     *
     * Memakai teks yang diisi pengurus. Bila kosong, dirangkai otomatis dari
     * agenda & berita terbaru agar layar tidak pernah tampil hampa.
     */
    private function _buildRunningText(array $masjid, array $programs, array $news): string
    {
        $manual = trim((string) ($masjid['running_text'] ?? ''));
        if ($manual !== '') {
            return $manual;
        }

        $bagian = [];
        foreach (array_slice($programs, 0, 3) as $p) {
            $bagian[] = 'Program: ' . $p['title'];
        }
        foreach (array_slice($news, 0, 3) as $n) {
            $bagian[] = 'Kabar: ' . $n['title'];
        }

        return empty($bagian)
            ? 'Selamat datang di ' . $masjid['name'] . '. Semoga Allah menerima amal ibadah kita.'
            : implode('   •   ', $bagian);
    }

    public function subscribe()
    {
        $masjidUsername = $this->request->getPost('masjid_username');
        $email = $this->request->getPost('email');
        $name = $this->request->getPost('name');

        if (!$masjidUsername || !$email) {
            return redirect()->back()->with('error', 'Email wajib diisi.');
        }

        $masjidModel = new \App\Models\MasjidModel();
        $masjid = $masjidModel->where('username', $masjidUsername)->first();

        if (!$masjid) {
            return redirect()->back()->with('error', 'Masjid tidak ditemukan.');
        }

        $subscriberModel = new \App\Models\MasjidSubscriberModel();
        
        // Check if already subscribed
        $exists = $subscriberModel->where(['masjid_id' => $masjid['id'], 'email' => $email])->first();
        if ($exists) {
            return redirect()->back()->with('error', 'Email ini sudah terdaftar.');
        }

        $subscriberModel->insert([
            'masjid_id' => $masjid['id'],
            'email'     => $email,
            'name'      => $name,
            'is_active' => 1
        ]);

        return redirect()->back()->with('success', 'Terima kasih telah berlangganan info masjid!');
    }

}

