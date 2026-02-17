<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('landing', ['title' => 'Masj.id - Manajemen Masjid Modern & Transparan']);
    }

    public function fitur(): string
    {
        return view('fitur', ['title' => 'Fitur Unggulan - Masj.id']);
    }

    public function kebaikan(): string
    {
        return view('program_kebaikan', ['title' => 'Statistik Dampak & Program - Masj.id']);
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
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Masjid dengan username @$username tidak ditemukan.");
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
