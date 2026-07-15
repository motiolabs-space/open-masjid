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

        // Jadwal sholat (AlAdhan) — mengikuti koordinat & zona waktu masjid.
        $prayerData = $this->_ambilJadwalSholat($masjid);
        if ($prayerData) {
            $prayerData['timings'] = $this->_terapkanKoreksi(
                $prayerData['timings'],
                json_decode($masjid['koreksi_menit'] ?? '', true) ?: []
            );
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

        // 4. Jadwal sholat hari ini — mengikuti koordinat & zona waktu masjid.
        $prayerData = $this->_ambilJadwalSholat($masjid);

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

        // 7. Koreksi menit dari pengurus diterapkan ke jadwal sebelum dipakai,
        //    agar tampilan jadwal dan pemicu layar adzan memakai angka yang sama.
        if ($prayerData) {
            $prayerData['timings'] = $this->_terapkanKoreksi(
                $prayerData['timings'],
                json_decode($masjid['koreksi_menit'] ?? '', true) ?: []
            );
        }

        // 8. Tanggal Hijriah — sudah tersedia gratis pada respons AlAdhan.
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
            'iqomahSettings'   => $this->_iqomahSettings($masjid),
            'sholatDuration'   => (int) ($masjid['sholat_duration'] ?? 10),
            // Waktu diambil dari server, bukan jam TV — jam TV kerap salah atau
            // zona waktunya keliru, yang membuat adzan tampil di saat yang salah.
            'serverEpochMs'    => (int) round(microtime(true) * 1000),
            // Zona pilihan pengurus diutamakan; bila kosong pakai hasil deteksi
            // AlAdhan dari koordinat. Harus sama dengan zona jadwal di atas.
            'timezoneMasjid'   => $this->_timezoneMasjid($masjid)
                                    ?? ($prayerData['meta']['timezone'] ?? 'Asia/Jakarta'),
            'storage'          => new \App\Libraries\Storage()
        ]);
    }

    /**
     * Menggeser jadwal sholat sesuai koreksi menit dari pengurus.
     * Nilai boleh negatif (lebih awal) maupun positif (lebih lambat).
     */
    /**
     * Zona waktu yang dipakai masjid: pilihan pengurus lebih diutamakan,
     * bila kosong ditentukan otomatis oleh AlAdhan dari koordinat.
     */
    private function _timezoneMasjid(array $masjid): ?string
    {
        $tz = trim((string) ($masjid['timezone'] ?? ''));

        return $tz !== '' ? $tz : null;
    }

    /**
     * Mengambil jadwal sholat dari AlAdhan untuk sebuah masjid.
     *
     * Dipakai bersama oleh halaman profil dan Display TV — sebelumnya kode ini
     * diduplikasi, sehingga perbaikan (mis. http -> https) harus dilakukan dua
     * kali dan rawan terlewat.
     *
     * Bila pengurus memilih zona waktu, zona itu dikirim ke AlAdhan lewat
     * 'timezonestring' agar jadwal yang kembali dinyatakan dalam zona tersebut
     * — jadwal dan jam "sekarang" wajib memakai zona yang sama, kalau tidak
     * pemicu adzan bisa meleset berjam-jam.
     */
    private function _ambilJadwalSholat(array $masjid): ?array
    {
        // Koordinat wajib; 0,0 dianggap belum diisi.
        if (empty($masjid['latitude']) || empty($masjid['longitude'])
            || ($masjid['latitude'] == 0 && $masjid['longitude'] == 0)) {
            return null;
        }

        $lat  = $masjid['latitude'];
        $long = $masjid['longitude'];
        $tz   = $this->_timezoneMasjid($masjid);
        $date = date('d-m-Y');

        // Zona ikut dalam kunci cache: mengubah zona harus langsung terasa,
        // tidak menunggu cache 24 jam kedaluwarsa.
        $cacheKey = 'prayer_aladhan_' . $masjid['id'] . '_' . $date . '_' . md5((string) $tz);
        $tersimpan = cache($cacheKey);
        if ($tersimpan) {
            return $tersimpan;
        }

        try {
            $client = \Config\Services::curlrequest();
            // Method 20 = Kementerian Agama RI.
            // WAJIB https: AlAdhan membalas 301 ke HTTPS untuk permintaan http,
            // sehingga respons bukan JSON dan jadwal sholat gagal tampil.
            $apiUrl = "https://api.aladhan.com/v1/timings/$date?latitude=$lat&longitude=$long&method=20";
            if ($tz !== null) {
                $apiUrl .= '&timezonestring=' . rawurlencode($tz);
            }

            $response = $client->request('GET', $apiUrl, ['timeout' => 5]);
            $body = json_decode($response->getBody(), true);

            if (isset($body['code']) && $body['code'] == 200) {
                cache()->save($cacheKey, $body['data'], 86400); // 24 jam
                return $body['data'];
            }
        } catch (\Exception $e) {
            log_message('error', 'AlAdhan API Error: ' . $e->getMessage());
        }

        return null;
    }

    private function _terapkanKoreksi(array $timings, array $koreksi): array
    {
        // Nama pada AlAdhan -> nama yang dipakai pengurus.
        $peta = [
            'Fajr'    => 'Subuh',
            'Dhuhr'   => 'Dzuhur',
            'Asr'     => 'Ashar',
            'Maghrib' => 'Maghrib',
            'Isha'    => 'Isya',
        ];

        foreach ($peta as $kunciApi => $namaSholat) {
            $menit = (int) ($koreksi[$namaSholat] ?? 0);
            if ($menit === 0 || empty($timings[$kunciApi])) {
                continue;
            }

            // AlAdhan dapat mengembalikan "17:52" atau "17:52 (WIB)".
            $jam = explode(' ', trim($timings[$kunciApi]))[0];
            [$j, $m] = array_map('intval', explode(':', $jam));

            // Dijaga tetap dalam satu hari agar tidak melompat ke tanggal lain.
            $total = (($j * 60) + $m + $menit + 1440) % 1440;
            $timings[$kunciApi] = sprintf('%02d:%02d', intdiv($total, 60), $total % 60);
        }

        return $timings;
    }

    /**
     * Jeda adzan->iqomah per waktu sholat (menit), dengan nilai bawaan yang
     * lazim di masjid Indonesia bila pengurus belum mengaturnya.
     */
    private function _iqomahSettings(array $masjid): array
    {
        $bawaan = ['Subuh' => 20, 'Dzuhur' => 10, 'Ashar' => 10, 'Maghrib' => 7, 'Isya' => 10];
        $tersimpan = json_decode($masjid['iqomah_settings'] ?? '', true);

        return is_array($tersimpan) ? array_merge($bawaan, $tersimpan) : $bawaan;
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

