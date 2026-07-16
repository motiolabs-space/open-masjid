<?php

if (!function_exists('format_wa')) {
    function format_wa($phone)
    {
        if (empty($phone)) return '';
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (substr($cleanPhone, 0, 1) === '0') {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        } elseif (substr($cleanPhone, 0, 1) === '8') {
            $cleanPhone = '62' . $cleanPhone;
        }
        return $cleanPhone;
    }
}

if (!function_exists('pengurus_saat_ini')) {
    /**
     * Baris masjid_pengurus milik pengguna yang login untuk masjid yang sedang
     * dibuka — dibaca dari basis data, BUKAN dari session.
     *
     * Ini disengaja. Jabatan pernah disalin ke session saat login, dan
     * akibatnya setiap perubahan dari menu admin baru berlaku setelah orangnya
     * logout: menurunkan admin menjadi pengurus tidak mencabut apa pun, dan
     * mencopot pengurus sama sekali tidak mengeluarkannya — ia tetap bisa
     * mengelola masjid yang sudah bukan haknya selama session masih hidup.
     * Session adalah salinan; salinan bisa basi. Basis data tidak.
     *
     * Hasilnya di-cache per-permintaan, jadi satu kueri saja meski dipanggil
     * berkali-kali oleh filter dan view.
     *
     * @return array|null null bila pengguna bukan pengurus masjid tersebut.
     */
    function pengurus_saat_ini(): ?array
    {
        static $cache = null;
        static $sudahDibaca = false;

        if ($sudahDibaca) {
            return $cache;
        }
        $sudahDibaca = true;

        $userId   = session()->get('user_id');
        $masjidId = session()->get('masjid_id');

        if (empty($userId) || empty($masjidId)) {
            return $cache = null;
        }

        $cache = (new \App\Models\MasjidPengurusModel())
            ->where(['user_id' => $userId, 'masjid_id' => $masjidId])
            ->first();

        return $cache;
    }
}

if (!function_exists('is_admin_masjid')) {
    /**
     * Apakah pengguna yang login adalah Admin Masjid pada masjid yang dibuka.
     *
     * Dipakai filter 'masjidAdmin' maupun view. Menyembunyikan tombol di view
     * hanya merapikan tampilan — penegakannya tetap di filter.
     */
    function is_admin_masjid(): bool
    {
        // Superadmin platform mengelola semua masjid dan tidak punya baris di
        // masjid_pengurus.
        if (session()->get('role') === 'superadmin') {
            return true;
        }

        return (pengurus_saat_ini()['role'] ?? null) === 'admin';
    }
}

if (!function_exists('parse_rupiah')) {
    /**
     * Membaca nominal uang dari teks bebas menjadi angka.
     *
     * Dipakai untuk berkas yang formatnya TIDAK kita kendalikan — mutasi bank
     * dan impor CSV. Setiap bank menulis angka dengan gaya berbeda: BCA memakai
     * gaya Indonesia (1.500.000,00), sebagian ekspor memakai gaya Inggris
     * (1,500,000.00), sebagian lagi polos (1500000).
     *
     * Aturannya: bila titik dan koma sama-sama ada, yang paling KANAN adalah
     * pemisah desimal dan yang lain pemisah ribuan. Bila hanya satu jenis yang
     * muncul dan di belakangnya tepat 3 angka, itu pemisah ribuan — 250.000
     * berarti dua ratus lima puluh ribu, bukan dua ratus lima puluh koma nol.
     * Anggapan ini aman untuk rupiah, yang praktis tidak pernah bernilai pecahan.
     *
     * Mengembalikan nilai BERTANDA: '-1.500.000' dan '(1.500.000)' (gaya
     * akuntansi) menjadi negatif, supaya pemanggil bisa membedakan uang keluar,
     * bukan diam-diam membacanya sebagai uang masuk.
     */
    function parse_rupiah($nilai): float
    {
        $teks = trim((string) $nilai);
        if ($teks === '') {
            return 0.0;
        }

        $negatif = str_contains($teks, '-') || preg_match('/\(.+\)/', $teks) === 1;

        // Sisakan angka dan pemisahnya saja: buang 'Rp', spasi, dan lainnya.
        $teks = preg_replace('/[^0-9.,]/', '', $teks);
        if ($teks === '' || !preg_match('/[0-9]/', $teks)) {
            return 0.0;
        }

        $adaTitik = strrpos($teks, '.');
        $adaKoma  = strrpos($teks, ',');

        if ($adaTitik !== false && $adaKoma !== false) {
            $desimal = $adaTitik > $adaKoma ? '.' : ',';
            $ribuan  = $desimal === '.' ? ',' : '.';
            $teks    = str_replace($ribuan, '', $teks);
            $teks    = str_replace($desimal, '.', $teks);
        } elseif ($adaTitik !== false || $adaKoma !== false) {
            $pemisah = $adaTitik !== false ? '.' : ',';
            $bagian  = explode($pemisah, $teks);

            if (strlen(end($bagian)) === 3) {
                $teks = str_replace($pemisah, '', $teks);   // ribuan: 250.000
            } else {
                $teks = str_replace($pemisah, '.', $teks);  // desimal: 500,00
            }
        }

        return ((float) $teks) * ($negatif ? -1 : 1);
    }
}

if (!function_exists('parse_tanggal')) {
    /**
     * Membaca tanggal dari berkas bank/CSV menjadi 'Y-m-d'.
     *
     * strtotime() TIDAK boleh dipakai langsung untuk ini. Pada bentuk berpemisah
     * garis miring ia menganggap urutan Amerika (bulan/tanggal/tahun), sehingga
     * data Indonesia rusak dengan dua cara sekaligus:
     *   '15/07/2026' -> bulan ke-15 tidak sah -> false -> tersimpan 1970-01-01
     *   '01/02/2026' -> terbaca 2 Januari, padahal maksudnya 1 Februari
     * Yang kedua paling berbahaya karena tampak berhasil.
     *
     * Karena itu bentuk berpemisah dicoba secara eksplisit dengan urutan
     * Indonesia lebih dulu. Mengembalikan null bila tidak terbaca, supaya
     * pemanggil bisa menolak barisnya alih-alih menyimpan tanggal ngawur.
     */
    function parse_tanggal($nilai): ?string
    {
        $teks = trim((string) $nilai);
        if ($teks === '') {
            return null;
        }

        $bentuk = [
            'd/m/Y', 'd-m-Y', 'd.m.Y',   // Indonesia — didahulukan
            'Y-m-d', 'Y/m/d',            // ISO
            'd/m/y', 'd-m-y',            // tahun 2 angka
        ];

        foreach ($bentuk as $f) {
            $tgl = DateTime::createFromFormat('!' . $f, $teks);
            // createFromFormat menerima tanggal mustahil (32/13/2026) lalu
            // menggesernya diam-diam; format($f) yang tak sama persis menandakan
            // hal itu terjadi.
            if ($tgl !== false && $tgl->format($f) === $teks) {
                return $tgl->format('Y-m-d');
            }
        }

        return null;
    }
}
