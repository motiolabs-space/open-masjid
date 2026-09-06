<?php

namespace App\Controllers;

class Home extends BaseController
{
    /** Kartu masjid per halaman di direktori Jelajah. */
    private const JELAJAH_PER_HALAMAN = 24;

    /** Batas atas jumlah orang per satu konfirmasi kehadiran (RSVP). */
    private const RSVP_TAMU_MAKS = 50;

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
            'jadwalKosong'   => $this->_alasanJadwalKosong($masjid, $prayerData),
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
            // Sama seperti program: daftar sudah menyaring 'published', tapi
            // halaman detailnya dulu tidak — berita draf terbaca publik lewat
            // slug, dan pembacaannya bahkan ikut menaikkan penghitung views.
            ->where([
                'masjid_news.slug'      => $slug,
                'masjid_news.masjid_id' => $masjid['id'],
                'masjid_news.status'    => 'published',
            ])
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
            'storage' => new \App\Libraries\Storage(),
            // Dibaca di sini, bukan di view: $this->include() hanya meneruskan
            // data dari controller, sehingga variabel yang dibuat di dalam view
            // tidak akan pernah sampai ke partials/embed.
            'embed'   => \App\Libraries\Embed::baca($news['video_url'] ?? null),
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

        // Dana terkumpul per program dalam SATU query (bukan per kartu di view),
        // supaya kartu kampanye bisa menampilkan progress tanpa N kueri.
        $ids = array_column($programs, 'id');
        $terkumpulPer = [];
        if (! empty($ids)) {
            $rows = \Config\Database::connect()->table('masjid_donations')
                ->select('program_id, COALESCE(SUM(amount),0) AS total')
                ->where('masjid_id', $masjid['id'])
                ->where('status', 'success')
                ->whereIn('program_id', $ids)
                ->groupBy('program_id')
                ->get()->getResultArray();
            foreach ($rows as $r) {
                $terkumpulPer[$r['program_id']] = (float) $r['total'];
            }
        }
        foreach ($programs as &$p) {
            $p['collected'] = $terkumpulPer[$p['id']] ?? 0;
        }
        unset($p);

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
            // status disaring di sini juga, bukan hanya di daftar: tanpa ini
            // program yang masih draf terbaca publik oleh siapa pun yang tahu
            // slug-nya, padahal pengurus mengira belum terbit.
            ->where([
                'masjid_programs.masjid_id' => $masjid['id'],
                'masjid_programs.slug'      => $slug,
                'masjid_programs.status'    => 'published',
            ])
            ->first();

        if (!$program) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Program tidak ditemukan.");
        }

        // Foto bukti dampak hanya diambil bila laporan dampaknya dipublikasikan.
        $impactPhotos = [];
        if (! empty($program['impact_published'])) {
            $impactPhotos = (new \App\Models\MasjidProgramImpactPhotoModel())
                ->where(['program_id' => $program['id'], 'masjid_id' => $masjid['id']])
                ->orderBy('id', 'ASC')->findAll();
        }

        // Kehadiran terkonfirmasi (untuk RSVP publik & cek kuota).
        $rsvpTamu = (new \App\Models\MasjidProgramRsvpModel())->totalTamu((int) $program['id']);

        return view('public/program_detail', [
            'title'        => esc($program['title']) . ' - ' . esc($masjid['name']),
            'masjid'       => $masjid,
            'program'      => $program,
            'impactPhotos' => $impactPhotos,
            'rsvpTamu'     => $rsvpTamu,
            'storage'      => new \App\Libraries\Storage()
        ]);
    }

    /**
     * Kalkulator Zakat per-masjid. Perhitungan dilakukan di sisi klien (JS)
     * agar responsif; halaman ini hanya menyediakan konteks masjid dan tautan
     * "tunaikan" yang mengalir ke form donasi dengan nominal terisi.
     *
     * Nishab & harga (emas/beras) bisa diubah pengguna di form — angka default
     * hanyalah titik awal, sengaja tidak dikunci karena harga pasar berubah.
     */
    public function zakat($username): string
    {
        $masjid = (new \App\Models\MasjidModel())->where('username', $username)->first();
        if (! $masjid) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('public/zakat_calculator', [
            'title'   => 'Kalkulator Zakat - ' . esc($masjid['name']),
            'masjid'  => $masjid,
            'storage' => new \App\Libraries\Storage(),
        ]);
    }

    /**
     * Formulir Donasi Rutin (infaq terjadwal). Donatur berjanji nominal rutin;
     * sistem mengirim pengingat berkala. Bukan auto-charge — pembayaran tetap
     * manual/QRIS lewat tautan yang dikirim saat pengingat.
     */
    /**
     * Manifest PWA disajikan lewat PHP (bukan berkas statis) agar start_url,
     * scope, dan ikon mengikuti base_url — di lokal aplikasi di /masjid/, di
     * produksi di root. Berkas statis tak bisa menyesuaikan keduanya.
     */
    /**
     * Jelajah Masjid — direktori publik: cari masjid & temukan untuk berdonasi
     * lintas masjid. Pencarian nama/username/kota + saring provinsi. Peta hanya
     * menampilkan masjid yang punya koordinat (sisanya tetap muncul di daftar).
     */
    public function jelajah(): string
    {
        $masjidModel = new \App\Models\MasjidModel();
        $q    = trim((string) $this->request->getGet('q'));
        $prov = trim((string) $this->request->getGet('provinsi'));

        $builder = $masjidModel->where('status', 'active');
        if ($q !== '') {
            $builder->groupStart()
                ->like('name', $q)->orLike('username', $q)
                ->orLike('kabupaten', $q)->orLike('kecamatan', $q)
                ->groupEnd();
        }
        if ($prov !== '') {
            $builder->where('provinsi', $prov);
        }

        // Dipaginasi: direktori ini tumbuh seiring masjid yang mendaftar, dan
        // memuat seluruh baris + seluruh penanda peta dalam satu halaman akan
        // memberat seiring waktu.
        $masjids = $builder->orderBy('name', 'ASC')->paginate(self::JELAJAH_PER_HALAMAN);
        $pager   = $masjidModel->pager;

        // Daftar provinsi untuk penyaring — dibatasi masjid aktif saja, sama
        // seperti daftarnya; provinsi milik masjid non-aktif hanya akan
        // menghasilkan 0 hasil bila dipilih.
        $provinsiList = $masjidModel->distinct()->select('provinsi')
            ->where('status', 'active')
            ->where('provinsi IS NOT NULL')->where('provinsi !=', '')
            ->orderBy('provinsi', 'ASC')->findAll();

        // Titik peta dari hasil saring yang berkoordinat (halaman ini saja, agar
        // peta selalu menggambarkan daftar yang sedang tampil).
        $pins = [];
        foreach ($masjids as $m) {
            $lat = (float) ($m['latitude'] ?? 0);
            $lng = (float) ($m['longitude'] ?? 0);

            // 0,0 adalah nilai bawaan kolom yang belum benar-benar diisi, bukan
            // sebuah lokasi. Diloloskan, ia menaruh penanda di Teluk Guinea dan
            // memaksa fitBounds() memperlihatkan separuh dunia — masjid yang
            // koordinatnya benar jadi ikut tak terbaca.
            if ($lat === 0.0 || $lng === 0.0 || abs($lat) > 90 || abs($lng) > 180) {
                continue;
            }

            $pins[] = [
                'nama'  => $m['name'],
                'url'   => base_url($m['username']),
                'lat'   => $lat,
                'lng'   => $lng,
                'kota'  => $m['kabupaten'] ?? '',
            ];
        }

        return view('public/jelajah', [
            'title'        => 'Jelajah Masjid - Masj.id',
            'masjids'      => $masjids,
            'provinsiList' => $provinsiList,
            'filter'       => ['q' => $q, 'provinsi' => $prov],
            'pins'         => $pins,
            'pager'        => $pager,
            'total'        => $pager->getTotal(),
            'storage'      => new \App\Libraries\Storage(),
        ]);
    }

    public function manifest()
    {
        $data = [
            'name'             => 'Masj.id — Kelola Masjid',
            'short_name'       => 'Masj.id',
            'description'      => 'Kelola masjid dengan transparan & mudah: keuangan, program, donasi, jadwal sholat.',
            'start_url'        => base_url('/'),
            'scope'            => base_url('/'),
            'display'          => 'standalone',
            'orientation'      => 'portrait',
            'background_color' => '#f4f7f5',
            'theme_color'      => '#065f46',
            'lang'             => 'id',
            'icons'            => [
                ['src' => asset_url('logo_masjid_200.png'), 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any'],
                ['src' => asset_url('logo_masjid_200.png'), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any'],
                ['src' => asset_url('logo_masjid_200.png'), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'maskable'],
            ],
        ];
        return $this->response
            ->setContentType('application/manifest+json')
            ->setBody(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }

    /**
     * Simpan langganan Web Push (dikirim JS setelah izin diberikan). Idempoten
     * berdasarkan hash endpoint: langganan yang sama diperbarui, bukan dobel.
     */
    public function pushSubscribe()
    {
        // Rute ini publik DAN dikecualikan dari CSRF (lihat Config\Filters),
        // jadi pembatas laju adalah satu-satunya rem yang tersisa.
        if (! $this->lolosBatasLaju('push-subscribe', 10)) {
            return $this->response->setStatusCode(429)->setJSON(['ok' => false, 'error' => 'Terlalu banyak permintaan.']);
        }

        $body = json_decode($this->request->getBody(), true) ?: [];
        $masjidId = (int) ($body['masjid_id'] ?? 0);
        $sub      = $body['subscription'] ?? [];
        $endpoint = $sub['endpoint'] ?? '';
        $p256dh   = $sub['keys']['p256dh'] ?? '';
        $auth     = $sub['keys']['auth'] ?? '';

        if (! $masjidId || $endpoint === '' || $p256dh === '' || $auth === '') {
            return $this->response->setStatusCode(400)->setJSON(['ok' => false, 'error' => 'Data langganan tidak lengkap.']);
        }
        // Endpoint disaring SEBELUM disimpan: baris di tabel ini kelak menjadi
        // alamat tujuan curl saat broadcast, dan rute ini publik + bebas CSRF.
        if (! \App\Libraries\WebPush::endpointSah($endpoint)) {
            return $this->response->setStatusCode(422)->setJSON(['ok' => false, 'error' => 'Endpoint langganan tidak dikenali.']);
        }
        if (! (new \App\Models\MasjidModel())->find($masjidId)) {
            return $this->response->setStatusCode(404)->setJSON(['ok' => false, 'error' => 'Masjid tidak ditemukan.']);
        }

        $model = new \App\Models\MasjidPushSubscriptionModel();
        $hash  = hash('sha256', $endpoint);
        $ada   = $model->where('endpoint_hash', $hash)->first();
        $data  = ['masjid_id' => $masjidId, 'endpoint' => $endpoint, 'p256dh' => $p256dh, 'auth' => $auth, 'endpoint_hash' => $hash];

        $ada ? $model->update($ada['id'], $data) : $model->insert($data);

        return $this->response->setJSON(['ok' => true]);
    }

    /**
     * Isi notifikasi terbaru sebuah masjid — diambil service worker saat push
     * payloadless tiba. Tanpa data pribadi; hanya judul/isi/url publik.
     */
    public function pushLatest()
    {
        $masjidId = (int) $this->request->getGet('masjid');
        $msg = (new \App\Models\MasjidPushMessageModel())
            ->where('masjid_id', $masjidId)
            ->orderBy('created_at', 'DESC')->orderBy('id', 'DESC')->first();

        if (! $msg) {
            return $this->response->setJSON(['title' => 'Masj.id', 'body' => 'Ada pembaruan dari masjid Anda.', 'url' => base_url('/')]);
        }
        return $this->response->setJSON([
            'title' => $msg['title'],
            'body'  => $msg['body'] ?? '',
            'url'   => $msg['url'] ?: base_url('/'),
        ]);
    }

    public function donasiRutin($username): string
    {
        $masjid = (new \App\Models\MasjidModel())->where('username', $username)->first();
        if (! $masjid) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $programs = (new \App\Models\MasjidProgramModel())
            ->where(['masjid_id' => $masjid['id'], 'status' => 'published'])
            ->orderBy('title', 'ASC')->findAll();

        return view('public/donasi_rutin', [
            'title'    => 'Donasi Rutin - ' . esc($masjid['name']),
            'masjid'   => $masjid,
            'programs' => $programs,
            'storage'  => new \App\Libraries\Storage(),
        ]);
    }

    /**
     * Konfirmasi kehadiran (RSVP) publik untuk sebuah program. Tanpa login —
     * cukup nama + WA + jumlah orang. Nomor WA yang sama dianggap satu
     * pendaftaran (diperbarui, bukan diduplikasi). Kuota ditegakkan bila diisi.
     */
    public function simpanRsvp()
    {
        if (! $this->lolosBatasLaju('rsvp')) {
            return redirect()->back()->with('rsvp_error',
                'Terlalu banyak pengiriman dari perangkat ini. Coba lagi sebentar lagi.');
        }

        $masjidId  = (int) $this->request->getPost('masjid_id');
        $programId = (int) $this->request->getPost('program_id');

        $program = (new \App\Models\MasjidProgramModel())
            ->where(['id' => $programId, 'masjid_id' => $masjidId, 'status' => 'published'])->first();
        if (! $program) {
            return redirect()->to('/')->with('error', 'Program tidak ditemukan.');
        }
        $masjid = (new \App\Models\MasjidModel())->find($masjidId);
        if (! $masjid) {
            return redirect()->to('/')->with('error', 'Masjid tidak ditemukan.');
        }
        $kembali = base_url($masjid['username'] . '/program/' . $program['slug']);

        $nama   = trim((string) $this->request->getPost('name'));
        $telp   = trim((string) $this->request->getPost('phone'));
        // Dibatasi atas juga: tanpa batas, satu kiriman berisi jutaan tamu bisa
        // memborong seluruh kuota acara.
        $guests = min(self::RSVP_TAMU_MAKS, max(1, (int) $this->request->getPost('guests')));
        if ($nama === '' || $telp === '') {
            return redirect()->to($kembali)->with('rsvp_error', 'Nama dan nomor WhatsApp wajib diisi.');
        }

        $rsvpModel = new \App\Models\MasjidProgramRsvpModel();
        $existing  = $rsvpModel->where(['program_id' => $programId, 'phone' => $telp])->first();

        // Kuota (bila diisi): hitung tamu terkonfirmasi TANPA menghitung
        // pendaftaran nomor ini (agar pembaruan tak menuduh dirinya sendiri).
        if (! empty($program['quota']) && $program['quota'] > 0) {
            $terpakai = $rsvpModel->totalTamu($programId) - (int) ($existing['guests'] ?? 0);
            if ($terpakai + $guests > $program['quota']) {
                $sisa = max(0, $program['quota'] - $terpakai);
                return redirect()->to($kembali)->with('rsvp_error',
                    'Maaf, kuota hampir penuh. Sisa tempat: ' . $sisa . '.');
            }
        }

        if ($existing) {
            $rsvpModel->update($existing['id'], ['name' => $nama, 'guests' => $guests, 'status' => 'registered']);
        } else {
            $rsvpModel->insert([
                'masjid_id' => $masjidId, 'program_id' => $programId,
                'name' => $nama, 'phone' => $telp, 'guests' => $guests, 'status' => 'registered',
            ]);
        }

        // Tanpa esc(): view yang menampilkan flashdata ini sudah meng-esc(), dan
        // meloloskannya dua kali membuat nama ber-apostrof tampil sebagai &#039;.
        return redirect()->to($kembali)->with('rsvp_ok',
            'Terima kasih, ' . $nama . '! Kehadiran Anda tercatat. Sampai jumpa di acara.');
    }

    public function simpanDonasiRutin()
    {
        if (! $this->lolosBatasLaju('donasi-rutin')) {
            return redirect()->back()->withInput()
                ->with('error', 'Terlalu banyak pengiriman dari perangkat ini. Coba lagi sebentar lagi.');
        }

        $masjidId = (int) $this->request->getPost('masjid_id');
        $masjid   = (new \App\Models\MasjidModel())->find($masjidId);
        if (! $masjid) {
            return redirect()->to('/')->with('error', 'Masjid tidak ditemukan.');
        }
        // Komitmen donasi rutin sama saja dengan menjadwalkan penerimaan dana.
        if (! masjid_aktif($masjid)) {
            return redirect()->to(base_url($masjid['username']))
                ->with('error', 'Masjid ini sedang tidak menerima donasi untuk sementara. Halaman dan laporannya tetap dapat Anda lihat.');
        }

        helper('custom'); // parse_rupiah
        $frekuensi = in_array($this->request->getPost('frequency'), ['mingguan', 'bulanan'], true)
            ? $this->request->getPost('frequency') : 'bulanan';
        $nominal = abs(parse_rupiah($this->request->getPost('amount')));
        $nama    = trim((string) $this->request->getPost('name'));
        $telp    = trim((string) $this->request->getPost('phone'));

        if ($nama === '' || $telp === '' || $nominal <= 0) {
            return redirect()->back()->withInput()
                ->with('error', 'Nama, WhatsApp, dan nominal wajib diisi.');
        }

        // program_id divalidasi milik masjid ini agar tak bisa menautkan ke
        // program masjid lain lewat form.
        $programId = $this->request->getPost('program_id') ?: null;
        if ($programId) {
            $prog = (new \App\Models\MasjidProgramModel())
                ->where(['id' => $programId, 'masjid_id' => $masjidId])->first();
            $programId = $prog ? $programId : null;
        }

        // Pengingat pertama satu periode dari sekarang — donatur bisa berdonasi
        // hari ini secara terpisah; ini komitmen ke depan.
        $next = date('Y-m-d', strtotime($frekuensi === 'mingguan' ? '+1 week' : '+1 month'));

        $ok = (new \App\Models\MasjidRecurringPledgeModel())->insert([
            'masjid_id'          => $masjidId,
            'program_id'         => $programId,
            'donor_name'         => $nama,
            'donor_phone'        => $telp,
            'donor_email'        => $this->request->getPost('email') ?: null,
            'amount'             => $nominal,
            'frequency'          => $frekuensi,
            'next_reminder_date' => $next,
            'active'             => 1,
        ]);

        if (! $ok) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan donasi rutin.');
        }

        return redirect()->to(base_url($masjid['username'] . '/donasi-rutin'))
            ->with('sukses_rutin', 'Terima kasih! Komitmen donasi rutin Anda tercatat. Kami akan mengingatkan setiap ' . $frekuensi . '.');
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
        
        // Pilihan periode. 'bulan' (YYYY-MM) adalah cara utama pengunjung
        // menjelajah laporan per bulan; 'bulan=all' menampilkan seluruh riwayat.
        // start_date/end_date tetap didukung agar tautan lama tak putus.
        $bulan = $this->request->getGet('bulan');
        if ($bulan === 'all') {
            $start = '2000-01-01';
            $end   = date('Y-m-d');
        } elseif ($bulan && preg_match('/^\d{4}-\d{2}$/', $bulan)) {
            $start = $bulan . '-01';
            $end   = date('Y-m-t', strtotime($start)); // akhir bulan
        } else {
            $start = $this->request->getGet('start_date') ?: date('Y-m-01');
            $end   = $this->request->getGet('end_date') ?: date('Y-m-d');
            $bulan = date('Y-m', strtotime($start)); // untuk menyorot pilihan di UI
        }

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

        // Dinding transparansi: donasi online terbaru yang sudah lunas. Sengaja
        // TIDAK diikat filter periode — ini "papan hidup" arus kepercayaan, jadi
        // selalu menampilkan yang terbaru apa pun rentang laporannya.
        $recentDonations = (new \App\Models\MasjidDonationModel())
            ->select('masjid_donations.donor_name, masjid_donations.amount, masjid_donations.message, masjid_donations.paid_at, masjid_programs.title as program_title')
            ->join('masjid_programs', 'masjid_programs.id = masjid_donations.program_id', 'left')
            ->where('masjid_donations.masjid_id', $masjid['id'])
            ->where('masjid_donations.status', 'success')
            ->orderBy('masjid_donations.paid_at', 'DESC')
            ->limit(8)
            ->findAll();

        // Penyaluran + bukti: menutup rantai "dari mana → ke mana → buktinya".
        $db2 = \Config\Database::connect();
        $distributions = $db2->table('masjid_distributions d')
            ->select('d.date, d.type, d.amount, d.items, d.description, d.evidence_photo, w.name as warga_name, p.title as program_title')
            ->join('masjid_warga w', 'w.id = d.warga_id', 'left')
            ->join('masjid_programs p', 'p.id = d.program_id', 'left')
            ->where('d.masjid_id', $masjid['id'])
            ->orderBy('d.date', 'DESC')
            ->limit(6)
            ->get()
            ->getResultArray();

        // Daftar bulan untuk pemilih: dari bulan berdirinya masjid s.d. bulan ini
        // (maks 24, terbaru dulu). Bulan tanpa transaksi tetap ditawarkan —
        // "kosong" pun bagian dari transparansi.
        $namaBulan = [1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $awalMasjid = ! empty($masjid['created_at']) ? strtotime(date('Y-m-01', strtotime($masjid['created_at']))) : strtotime(date('Y-m-01', strtotime('-11 months')));
        $daftarBulan = [];
        $kursor = strtotime(date('Y-m-01'));
        while ($kursor >= $awalMasjid && count($daftarBulan) < 24) {
            $daftarBulan[] = [
                'value' => date('Y-m', $kursor),
                'label' => $namaBulan[(int) date('n', $kursor)] . ' ' . date('Y', $kursor),
            ];
            $kursor = strtotime('-1 month', $kursor);
        }

        return view('public/finance_report', [
            'title'            => 'Laporan Amanah - ' . esc($masjid['name']),
            'masjid'           => $masjid,
            'transactions'     => $transactions,
            'summary'          => $summary,
            'expenditureByCat' => $expenditureByCat,
            'recentDonations'  => $recentDonations,
            'distributions'    => $distributions,
            'filters'          => ['start' => $start, 'end' => $end],
            'daftarBulan'      => $daftarBulan,
            'bulanDipilih'     => $bulan,
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
            'jadwalKosong'     => $this->_alasanJadwalKosong($masjid, $prayerData),
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
     * Sebab jadwal sholat tidak tersedia — dipakai untuk menampilkan pesan yang
     * tepat, bukan sekadar menghilangkan bagian jadwal tanpa penjelasan.
     *
     * Membedakan dua hal yang butuh tindakan berbeda:
     *   'koordinat' → pengurus belum mengisi titik koordinat (perlu tindakan)
     *   'api'       → koordinat sudah ada tetapi AlAdhan gagal dihubungi
     *                 (sementara, bukan salah pengurus)
     */
    private function _alasanJadwalKosong(array $masjid, ?array $prayerData): ?string
    {
        if ($prayerData) {
            return null;
        }

        $adaKoordinat = !empty($masjid['latitude']) && !empty($masjid['longitude'])
            && ($masjid['latitude'] != 0 || $masjid['longitude'] != 0);

        return $adaKoordinat ? 'api' : 'koordinat';
    }

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
        // Logika dipindahkan ke App\Libraries\PrayerTimes agar dipakai bersama
        // pengingat terjadwal (spark broadcast:reminders) — satu sumber jadwal
        // untuk layar masjid maupun pesan ke grup.
        return (new \App\Libraries\PrayerTimes())->ambil($masjid);
    }

    private function _terapkanKoreksi(array $timings, array $koreksi): array
    {
        return (new \App\Libraries\PrayerTimes())->terapkanKoreksi($timings, $koreksi);

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

