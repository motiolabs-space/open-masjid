<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Mengisi masjid contoh (username: digital) dengan konten peraga.
 *
 *   php spark db:seed DemoMasjidSeeder      # isi / segarkan
 *   php spark db:seed DemoMasjidClearSeeder # kosongkan lagi
 *
 * IDEMPOTEN: setiap kali dijalankan, seluruh konten milik masjid contoh dihapus
 * lebih dulu lalu diisi ulang. Jadi aman dipanggil berkali-kali, dan tanggalnya
 * selalu ikut bergerak mengikuti hari ini — laporan tak pernah tampak basi.
 *
 * BATAS TEGAS: seeder ini HANYA menyentuh baris milik masjid contoh, ditapis
 * lewat masjid_id. Masjid sungguhan tidak pernah tersentuh.
 *
 * SOAL KEJUJURAN DATA. Halaman ini publik, dan isinya angka keuangan serta nama
 * donatur yang seluruhnya fiktif. Karena itu statusnya dinyatakan terang-terangan
 * di nama, tagline, dan profil masjidnya — bukan disembunyikan. Produk yang
 * menjual transparansi tidak boleh menerbitkan laporan karangan yang tampak
 * sungguhan.
 */
class DemoMasjidSeeder extends Seeder
{
    public const USERNAME = 'digital';

    /** Tabel milik masjid yang dibersihkan sebelum diisi ulang; urutan penting (anak dulu). */
    public const TABEL = [
        'masjid_program_impact_photos',
        'masjid_finance_transactions',
        'masjid_donations',
        'masjid_mustahik_distributions',
        'masjid_distributions',
        'masjid_mustahik',
        'masjid_warga',
        'masjid_schedules',
        'masjid_programs',
        'masjid_program_categories',
        'masjid_finance_categories',
        'masjid_news',
        'masjid_news_categories',
    ];

    /**
     * Gambar yang sudah tersedia di penyimpanan bersama. Jalur disimpan relatif
     * seperti kolom lain, sehingga Storage::url() menyusun alamatnya sendiri —
     * baik di lokal maupun produksi.
     */
    private const GAMBAR = [
        'images/masjid/1771446356_3fb42f1159552a7b392f.jpg',
        'images/masjid/1772714601_b71f40903cbc4af9c113.png',
        'images/masjid/1774588821_4f653847e290056a1e18.jpg',
        'images/news/1769857461_a57a5be8b12a9c35f6a9.png',
        'images/news/1772019970_ba679efe4d76b69499bc.png',
    ];
    private const LOGO = 'images/masjid/logo/1771453878_99acede7df2afc860671.jpg';
    private const HERO = 'images/masjid/1772018614_58869c9bf89cc6f86d39.png';

    public function run()
    {
        $db = \Config\Database::connect();

        $masjid = $db->table('masjid')->where('username', self::USERNAME)->get()->getRowArray();
        if (! $masjid) {
            $this->pesan('Masjid contoh (username: ' . self::USERNAME . ') tidak ada. Buat dulu lewat pendaftaran, lalu jalankan ulang.');

            return;
        }
        $id = (int) $masjid['id'];

        foreach (self::TABEL as $t) {
            if ($db->tableExists($t)) {
                $db->table($t)->where('masjid_id', $id)->delete();
            }
        }

        $this->profil($db, $id);
        $kat  = $this->kategoriKeuangan($db, $id);
        $trx  = $this->transaksi($db, $id, $kat);
        $prog = $this->program($db, $id);
        $don  = $this->donasi($db, $id, $prog);
        $this->fotoDampak($db, $id, $prog);
        $mus  = $this->mustahik($db, $id);
        $sal  = $this->penyaluran($db, $id, $mus);
        $ber  = $this->berita($db, $id);
        $jad  = $this->jadwal($db, $id);

        $this->pesan(sprintf(
            'Masjid contoh terisi: %d transaksi, %d program, %d donasi, %d mustahik, %d penyaluran, %d berita, %d jadwal. Buka: /%s',
            $trx, count($prog), $don, count($mus), $sal, $ber, $jad, self::USERNAME
        ));
    }

    // ── Profil ───────────────────────────────────────────────────────────

    private function profil($db, int $id): void
    {
        $db->table('masjid')->where('id', $id)->update([
            'name'       => 'Masjid Digital',
            'nama_resmi' => 'Masjid Jami Digital (Contoh)',
            'tagline'    => "Halaman Contoh Masj.id\nSeluruh angka dan nama di halaman ini fiktif",
            'tahun_berdiri' => 1996,
            'address'    => 'Jl. Cendana No. 12, Kel. Sukamaju',
            'phone'      => '0274-555123',
            'whatsapp'   => '6281200000000',
            'email'      => 'contoh@masj.id',
            'provinsi'   => 'DI Yogyakarta',
            'kabupaten'  => 'KOTA YOGYAKARTA',
            'kecamatan'  => 'Mergangsan',
            'latitude'   => '-7.81250000',
            'longitude'  => '110.37000000',
            'timezone'   => 'Asia/Jakarta',
            'visi'       => 'Menjadi masjid yang memakmurkan jamaah dan dipercaya penuh dalam mengelola amanah.',
            'misi'       => "Melayani ibadah dengan nyaman\nMengelola keuangan secara terbuka\nMenghadirkan program yang berdampak bagi warga sekitar",
            'about_us'   => 'Halaman ini adalah CONTOH. Masjid Digital tidak benar-benar ada — '
                          . 'seluruh transaksi, donatur, program, dan penyaluran di sini dibuat sebagai peraga '
                          . 'agar pengurus masjid dapat melihat wujud Masj.id sebelum memakainya. '
                          . 'Jangan memakai angka di halaman ini sebagai rujukan apa pun.',
            'foto_utama' => self::HERO,
            'logo'       => self::LOGO,
            'menu_berita' => 1, 'menu_program' => 1, 'menu_laporan' => 1, 'menu_kontak' => 1,
            'action_button_active' => 1,
            'action_button_text'   => 'Donasi Sekarang',
            'action_button_url'    => base_url('donation/' . self::USERNAME . '/form'),
            'status'     => 'active',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    // ── Keuangan ─────────────────────────────────────────────────────────

    /** @return array<string,int> nama kategori => id */
    private function kategoriKeuangan($db, int $id): array
    {
        $kat = [];
        foreach ([
            ['Infaq Jumat', 'pemasukan'], ['Kotak Amal Harian', 'pemasukan'], ['Donasi Program', 'pemasukan'],
            ['Listrik & Air', 'pengeluaran'], ['Kebersihan & Perawatan', 'pengeluaran'],
            ['Honor Guru Ngaji', 'pengeluaran'], ['Santunan & Sosial', 'pengeluaran'],
        ] as [$nama, $tipe]) {
            $db->table('masjid_finance_categories')->insert([
                'masjid_id' => $id, 'name' => $nama, 'type' => $tipe,
                'slug' => url_title($nama, '-', true),
                'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $kat[$nama] = (int) $db->insertID();
        }

        return $kat;
    }

    private function transaksi($db, int $id, array $kat): int
    {
        // Angka dibuat dari benih tetap agar tampilannya konsisten tiap dijalankan.
        mt_srand(20260906);
        $masuk = ['Infaq Jumat' => [6500000, 9500000], 'Kotak Amal Harian' => [2200000, 3800000], 'Donasi Program' => [3000000, 12000000]];
        $keluar = ['Listrik & Air' => [900000, 1400000], 'Kebersihan & Perawatan' => [600000, 1200000],
                   'Honor Guru Ngaji' => [2400000, 2400000], 'Santunan & Sosial' => [1500000, 4500000]];

        $baris = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = date('Y-m', strtotime("-$i months"));
            // Bulan berjalan tak boleh punya transaksi bertanggal masa depan —
            // kalau itu terjadi, laporan bulan ini tampil Rp 0 padahal ada isinya.
            $maks = $i === 0 ? max(1, (int) date('j')) : 26;

            foreach ([['pemasukan', $masuk], ['pengeluaran', $keluar]] as [$tipe, $daftar]) {
                foreach ($daftar as $nama => [$a, $b]) {
                    $baris[] = [
                        'masjid_id'   => $id,
                        'category_id' => $kat[$nama],
                        'date'        => $bulan . '-' . str_pad((string) mt_rand(1, $maks), 2, '0', STR_PAD_LEFT),
                        'amount'      => mt_rand($a, $b),
                        'type'        => $tipe,
                        'description' => $nama . ' bulan ' . date('F Y', strtotime($bulan . '-01')),
                        'created_at'  => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
                    ];
                }
            }
        }
        $db->table('masjid_finance_transactions')->insertBatch($baris);

        return count($baris);
    }

    // ── Program ──────────────────────────────────────────────────────────

    /** @return array<string,int> judul => id */
    private function program($db, int $id): array
    {
        $db->table('masjid_program_categories')->insert([
            'masjid_id' => $id, 'name' => 'Pembangunan', 'slug' => 'pembangunan',
            'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $katId = (int) $db->insertID();

        $daftar = [
            ['Renovasi Tempat Wudhu & Toilet Jamaah',
             'Tempat wudhu masjid sudah 12 tahun tidak diperbarui. Keran bocor, lantai licin, dan antrean panjang setiap Jumat. Renovasi ini menambah 8 titik wudhu baru, lantai anti-slip, dan akses ramah lansia.',
             85000000, null, null, 0],
            ['Beasiswa Anak Yatim Binaan',
             'Bantuan biaya sekolah dan perlengkapan belajar untuk anak yatim di sekitar masjid, disalurkan setiap awal semester.',
             40000000, 32,
             'Tiga puluh dua anak menerima seragam, sepatu, dan biaya SPP satu semester. Dua di antaranya melanjutkan ke jenjang SMA setelah sempat berencana berhenti sekolah. Penyaluran disaksikan perwakilan RT dan orang tua asuh.',
             1],
            ['Dapur Jumat Berkah',
             'Empat ratus porsi makanan matang dibagikan setiap Jumat untuk jamaah, petugas kebersihan, dan warga sekitar masjid.',
             null, 1600,
             'Selama empat bulan, 1.600 porsi telah dibagikan. Penerima terbanyak adalah pekerja harian di pasar sebelah yang sebelumnya melewatkan makan siang saat Jumat.',
             1],
        ];

        $prog = [];
        foreach ($daftar as $i => [$judul, $desc, $target, $penerima, $narasi, $terbit]) {
            $db->table('masjid_programs')->insert([
                'masjid_id'   => $id,
                'category_id' => $katId,
                'title'       => $judul,
                'slug'        => url_title($judul, '-', true),
                'description' => '<p>' . $desc . '</p>',
                'thumbnail'   => self::GAMBAR[$i % count(self::GAMBAR)],
                'date_start'  => date('Y-m-d', strtotime('-' . (60 - $i * 20) . ' days')),
                'date_end'    => date('Y-m-d', strtotime('+' . (40 + $i * 10) . ' days')),
                'location'    => 'Masjid Digital',
                'target_donation'     => $target,
                'beneficiaries_count' => $penerima,
                'impact_narrative'    => $narasi,
                'impact_published'    => $terbit,
                'status'      => 'published',
                'created_at'  => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $prog[$judul] = (int) $db->insertID();
        }

        return $prog;
    }

    private function fotoDampak($db, int $id, array $prog): void
    {
        if (! $db->tableExists('masjid_program_impact_photos')) {
            return;
        }
        $foto = [
            [$prog['Beasiswa Anak Yatim Binaan'], 'Penyerahan perlengkapan sekolah, disaksikan perwakilan RT'],
            [$prog['Beasiswa Anak Yatim Binaan'], 'Anak binaan menerima seragam dan sepatu baru'],
            [$prog['Dapur Jumat Berkah'],         'Pembagian 400 porsi selepas Jumat'],
        ];
        foreach ($foto as $i => [$pid, $cap]) {
            $db->table('masjid_program_impact_photos')->insert([
                'masjid_id' => $id, 'program_id' => $pid,
                'photo' => self::GAMBAR[($i + 1) % count(self::GAMBAR)], 'caption' => $cap,
                'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    // ── Donasi ───────────────────────────────────────────────────────────

    private function donasi($db, int $id, array $prog): int
    {
        $reno = $prog['Renovasi Tempat Wudhu & Toilet Jamaah'];
        $bea  = $prog['Beasiswa Anak Yatim Binaan'];

        // Nama sengaja umum/anonim — ini data peraga, bukan orang sungguhan.
        $daftar = [
            [null, 5000000, $reno], ['Keluarga Bapak S.', 7500000, $reno], ['Ibu R. W.', 2500000, $reno],
            [null, 10000000, $reno], ['Alumni Angkatan 95', 12500000, $reno], ['Bapak W.', 1500000, $reno],
            [null, 3500000, $reno], ['Ibu Hj. S.', 5000000, $reno], ['Keluarga Besar R.', 2000000, $reno],
            ['Bapak A. S.', 1500000, $reno],
            [null, 6000000, $bea], ['Ibu N. A.', 4500000, $bea], ['Hamba Allah', 3000000, $bea], [null, 2500000, $bea],
            ['Warga RT 03', 1250000, null], [null, 750000, null], ['Bapak H.', 2000000, null], ['Ibu S. M.', 500000, null],
        ];

        $baris = [];
        foreach ($daftar as $i => [$nama, $jumlah, $programId]) {
            $waktu = date('Y-m-d H:i:s', strtotime("-$i days"));
            $baris[] = [
                'masjid_id'      => $id,
                'program_id'     => $programId,
                'invoice_number' => 'INV-CONTOH-' . date('Ymd', strtotime("-$i days")) . '-' . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'amount'         => $jumlah,
                'donor_name'     => $nama,
                'status'         => 'success',
                'payment_method' => 'manual',
                'payment_channel'=> 'transfer',
                'paid_at'        => $waktu,
                'created_at'     => $waktu, 'updated_at' => $waktu,
            ];
        }
        $db->table('masjid_donations')->insertBatch($baris);

        return count($baris);
    }

    // ── Penyaluran & penerima ────────────────────────────────────────────

    /**
     * Mustahik lengkap dengan profil ekonominya — halaman mustahik di dashboard
     * menampilkan penghasilan, tanggungan, status rumah, dan skor kelayakan,
     * jadi bila kolom itu kosong halamannya tampak setengah jadi.
     *
     * @return list<array{id:int,nama:string}>
     */
    private function mustahik($db, int $id): array
    {
        $daftar = [
            ['Ibu Sumiyati', 'fakir', 900000, 3, 'ngontrak', 9,
             'Janda dengan tiga anak usia sekolah, berjualan gorengan keliling. Penghasilan jauh di bawah UMR dan masih menanggung biaya kontrakan.'],
            ['Bapak Karjo', 'miskin', 1600000, 4, 'milik_sendiri', 7,
             'Buruh serabutan, penghasilan tidak tetap. Rumah milik sendiri tetapi tanggungan empat orang termasuk satu anggota keluarga sakit menahun.'],
            ['Keluarga Alm. Bapak Slamet', 'fakir', 600000, 5, 'numpang', 10,
             'Kepala keluarga wafat tahun lalu. Ibu bekerja paruh waktu, lima tanggungan, saat ini menumpang di rumah kerabat.'],
            ['Ibu Warsini', 'gharim', 2100000, 2, 'ngontrak', 6,
             'Terlilit utang biaya pengobatan suami. Masih berpenghasilan, namun sebagian besar habis untuk cicilan.'],
            ['Bapak Hamdan', 'ibnu_sabil', 0, 0, 'lainnya', 5,
             'Musafir yang kehabisan bekal dalam perjalanan pulang. Bantuan bersifat sekali, untuk ongkos dan makan.'],
            ['Ibu Ratmi', 'miskin', 1400000, 3, 'ngontrak', 8,
             'Pekerja cuci harian. Tiga anak, dua di antaranya masih SD. Kontrakan jatuh tempo tiap tiga bulan.'],
        ];

        $mus = [];
        foreach ($daftar as $i => [$nama, $asnaf, $penghasilan, $tanggungan, $rumah, $skor, $alasan]) {
            $db->table('masjid_mustahik')->insert([
                'masjid_id'        => $id,
                'name'             => $nama,
                'phone'            => '08120000' . str_pad((string) ($i + 10), 4, '0', STR_PAD_LEFT),
                'address'          => 'RT 0' . ($i % 4 + 1) . ' RW 02, Kel. Sukamaju',
                'income_per_month' => $penghasilan,
                'dependents_count' => $tanggungan,
                'house_ownership'  => $rumah,
                'asnaf'            => $asnaf,
                // enum kolom ini 'active'/'inactive' — bukan 'aktif'.
                'status'           => 'active',
                'ai_score'         => $skor,
                'ai_reasoning'     => $alasan,
                'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $mus[] = ['id' => (int) $db->insertID(), 'nama' => $nama];
        }

        return $mus;
    }

    /**
     * Penyaluran ditulis ke DUA tabel yang berbeda, dan keduanya memang dipakai:
     *
     *  - masjid_mustahik_distributions -> menu "Penyaluran Bantuan" di dashboard,
     *    tertaut ke mustahik beserta profil kelayakannya.
     *  - masjid_distributions          -> "Bantuan Warga", dan inilah yang tampil
     *    sebagai "Penyaluran & Bukti" di halaman transparansi publik (punya
     *    kolom foto bukti).
     *
     * Mengisi salah satu saja membuat separuh peraga kosong.
     */
    private function penyaluran($db, int $id, array $mus): int
    {
        $n = 0;

        // 1. Penyaluran ke mustahik (tampil di dashboard pengurus).
        $keMustahik = [
            [0, 750000,  'Santunan bulanan — Januari s.d. bulan berjalan'],
            [1, 500000,  'Bantuan biaya sekolah anak'],
            [2, 1200000, 'Santunan keluarga ditinggal wafat + paket sembako'],
            [3, 1500000, 'Bantuan pelunasan sebagian utang pengobatan'],
            [4, 350000,  'Bantuan ongkos perjalanan dan bekal'],
            [5, 600000,  'Bantuan biaya kontrakan jatuh tempo'],
            [0, 750000,  'Santunan bulanan periode berikutnya'],
            [2, 800000,  'Paket sembako bulanan'],
        ];
        foreach ($keMustahik as $k => [$idx, $jumlah, $ket]) {
            if (! isset($mus[$idx])) {
                continue;
            }
            $db->table('masjid_mustahik_distributions')->insert([
                'masjid_id'   => $id,
                'mustahik_id' => $mus[$idx]['id'],
                'date'        => date('Y-m-d', strtotime('-' . (7 + $k * 9) . ' days')),
                'amount'      => $jumlah,
                'description' => $ket,
                'created_at'  => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $n++;
        }

        // 2. Bantuan warga berbukti foto (tampil di halaman transparansi publik).
        $warga = [];
        foreach (array_slice($mus, 0, 4) as $m) {
            $db->table('masjid_warga')->insert([
                'masjid_id' => $id, 'name' => $m['nama'],
                'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $warga[] = (int) $db->insertID();
        }

        $berbukti = [
            ['Santunan bulanan untuk 4 keluarga prasejahtera', 4000000],
            ['Bantuan biaya berobat jamaah lansia', 1750000],
            ['Paket sembako untuk warga terdampak banjir RT 04', 6200000],
            ['Santunan anak yatim menjelang tahun ajaran baru', 5400000],
        ];
        foreach ($berbukti as $i => [$ket, $jumlah]) {
            $db->table('masjid_distributions')->insert([
                'masjid_id' => $id,
                'warga_id'  => $warga[$i] ?? null,
                'date'      => date('Y-m-d', strtotime('-' . (10 + $i * 12) . ' days')),
                'type'      => 'uang',
                'amount'    => $jumlah,
                'description'    => $ket,
                'evidence_photo' => self::GAMBAR[($i + 2) % count(self::GAMBAR)],
                'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $n++;
        }

        return $n;
    }

    // ── Berita & jadwal ──────────────────────────────────────────────────

    private function berita($db, int $id): int
    {
        // Kategori berita ikut diisi — daftar berita di dashboard menampilkan
        // kolom kategori, dan tanpa ini kolomnya kosong semua.
        $kat = [];
        foreach (['Pengumuman', 'Kegiatan', 'Laporan'] as $nama) {
            $db->table('masjid_news_categories')->insert([
                'masjid_id' => $id, 'name' => $nama, 'slug' => url_title($nama, '-', true),
                'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $kat[$nama] = (int) $db->insertID();
        }

        $daftar = [
            ['Laporan Keuangan Bulan Ini Sudah Terbit', 'Laporan',
             'Rekap pemasukan, pengeluaran, dan saldo akhir bulan ini sudah dapat dilihat jamaah kapan saja lewat halaman laporan masjid. Tidak perlu menunggu diumumkan di mimbar, dan tidak perlu bertanya kepada pengurus.'],
            ['Renovasi Tempat Wudhu Memasuki Tahap Kedua', 'Kegiatan',
             'Pengerjaan lantai anti-slip telah selesai. Tahap berikutnya adalah pemasangan delapan titik keran baru dan jalur khusus bagi jamaah lansia. Terima kasih kepada seluruh donatur yang telah menitipkan amanahnya.'],
            ['Kajian Rutin Ahad Pagi Kembali Dibuka', 'Pengumuman',
             'Kajian rutin Ahad pagi kembali digelar mulai pekan depan, pukul 06.30 hingga selesai, terbuka untuk umum. Bagi yang berhalangan hadir, rekamannya akan ditayangkan pada halaman program.'],
            ['Penyaluran Beasiswa Semester Ini Telah Selesai', 'Laporan',
             'Tiga puluh dua anak binaan telah menerima seragam, sepatu, dan biaya SPP satu semester. Penyaluran disaksikan perwakilan RT dan orang tua asuh. Rincian penerima manfaat dapat dilihat pada halaman program.'],
            ['Jadwal Imam & Khatib Jumat Empat Pekan ke Depan', 'Pengumuman',
             'Jadwal petugas Jumat untuk empat pekan ke depan sudah ditetapkan dan dapat dilihat pada halaman beranda masjid. Bagi petugas yang berhalangan, mohon mengabari pengurus paling lambat Rabu.'],
            ['Dapur Jumat Berkah Menembus 1.600 Porsi', 'Kegiatan',
             'Sejak dimulai empat bulan lalu, Dapur Jumat Berkah telah membagikan 1.600 porsi makanan matang. Penerima terbanyak adalah pekerja harian di pasar sebelah yang sebelumnya melewatkan makan siang saat Jumat.'],
        ];

        foreach ($daftar as $i => [$judul, $namaKat, $isi]) {
            $db->table('masjid_news')->insert([
                'masjid_id'   => $id,
                'category_id' => $kat[$namaKat] ?? null,
                'title'       => $judul,
                'slug'        => url_title($judul, '-', true),
                'content'     => '<p>' . $isi . '</p>',
                'thumbnail'   => self::GAMBAR[$i % count(self::GAMBAR)],
                'status'      => 'published',
                'views'       => 37 + $i * 23,
                'created_at'  => date('Y-m-d H:i:s', strtotime('-' . ($i * 5 + 2) . ' days')),
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);
        }

        return count($daftar);
    }

    private function jadwal($db, int $id): int
    {
        if (! $db->tableExists('masjid_schedules')) {
            return 0;
        }
        $n = 0;
        // Empat Jumat ke depan.
        for ($i = 0; $i < 4; $i++) {
            $db->table('masjid_schedules')->insert([
                'masjid_id'   => $id,
                'date'        => date('Y-m-d', strtotime("+$i week friday")),
                'prayer_type' => 'jumat',
                'imam_name'   => ['Ust. Abdurrahman', 'Ust. Hanafi', 'Ust. Yusuf', 'Ust. Ridwan'][$i],
                'khatib_name' => ['Ust. Salim', 'Ust. Mahmud', 'Ust. Ibrahim', 'Ust. Anwar'][$i],
                'muadzin_name'=> 'Bapak Sholeh',
                'created_at'  => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $n++;
        }

        return $n;
    }

    private function pesan(string $teks): void
    {
        if (is_cli()) {
            \CodeIgniter\CLI\CLI::write($teks, 'green');
        }
    }
}
