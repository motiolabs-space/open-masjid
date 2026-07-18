<?php

namespace App\Libraries;

/**
 * Jadwal sholat sebuah masjid dari AlAdhan, plus koreksi menit pengurus.
 *
 * Semula logika ini berupa method privat di Home (Display TV & halaman profil).
 * Diangkat ke sini agar pengingat terjadwal (spark broadcast:reminders) memakai
 * SATU sumber yang sama — kalau tidak, jadwal di layar masjid dan jadwal yang
 * dikirim ke grup bisa berbeda.
 */
class PrayerTimes
{
    /** Nama pada AlAdhan -> nama yang dipakai pengurus/koreksi. */
    private const PETA = [
        'Fajr'    => 'Subuh',
        'Dhuhr'   => 'Dzuhur',
        'Asr'     => 'Ashar',
        'Maghrib' => 'Maghrib',
        'Isha'    => 'Isya',
    ];

    /**
     * Data mentah AlAdhan untuk hari ini (timings, date, meta), atau null bila
     * koordinat belum diisi / AlAdhan gagal dihubungi.
     */
    public function ambil(array $masjid): ?array
    {
        if (empty($masjid['latitude']) || empty($masjid['longitude'])
            || ($masjid['latitude'] == 0 && $masjid['longitude'] == 0)) {
            return null;
        }

        $lat  = $masjid['latitude'];
        $long = $masjid['longitude'];
        $tz   = $this->timezone($masjid);
        $date = date('d-m-Y');

        $cacheKey = 'prayer_aladhan_' . $masjid['id'] . '_' . $date . '_' . md5((string) $tz);
        $tersimpan = cache($cacheKey);
        if ($tersimpan) {
            return $tersimpan;
        }

        try {
            $client = \Config\Services::curlrequest();
            // Method 20 = Kementerian Agama RI. WAJIB https (http -> 301).
            $apiUrl = "https://api.aladhan.com/v1/timings/$date?latitude=$lat&longitude=$long&method=20";
            if ($tz !== null) {
                $apiUrl .= '&timezonestring=' . rawurlencode($tz);
            }

            $response = $client->request('GET', $apiUrl, ['timeout' => 5]);
            $body = json_decode($response->getBody(), true);

            if (isset($body['code']) && $body['code'] == 200) {
                cache()->save($cacheKey, $body['data'], 86400);
                return $body['data'];
            }
        } catch (\Exception $e) {
            log_message('error', 'AlAdhan API Error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Lima waktu sholat hari ini yang SUDAH dikoreksi, dalam bentuk siap tampil:
     * ['Subuh' => '04:35', 'Dzuhur' => '11:48', ...]. Null bila jadwal tak ada.
     */
    public function hariIni(array $masjid): ?array
    {
        $data = $this->ambil($masjid);
        if (!$data || empty($data['timings'])) {
            return null;
        }

        $koreksi  = json_decode($masjid['koreksi_menit'] ?? '', true) ?: [];
        $terkoreksi = $this->terapkanKoreksi($data['timings'], $koreksi);

        $hasil = [];
        foreach (self::PETA as $kunciApi => $nama) {
            if (!empty($terkoreksi[$kunciApi])) {
                // AlAdhan bisa mengembalikan "04:35 (WIB)".
                $hasil[$nama] = explode(' ', trim($terkoreksi[$kunciApi]))[0];
            }
        }

        return $hasil ?: null;
    }

    /**
     * Zona waktu pilihan pengurus, atau null bila belum dipilih.
     */
    public function timezone(array $masjid): ?string
    {
        $tz = trim((string) ($masjid['timezone'] ?? ''));

        return $tz !== '' ? $tz : null;
    }

    /**
     * Menggeser jadwal sesuai koreksi menit pengurus (boleh negatif/positif).
     */
    public function terapkanKoreksi(array $timings, array $koreksi): array
    {
        foreach (self::PETA as $kunciApi => $namaSholat) {
            $menit = (int) ($koreksi[$namaSholat] ?? 0);
            if ($menit === 0 || empty($timings[$kunciApi])) {
                continue;
            }

            $jam = explode(' ', trim($timings[$kunciApi]))[0];
            [$j, $m] = array_map('intval', explode(':', $jam));

            // Dijaga tetap dalam satu hari agar tidak melompat tanggal.
            $total = (($j * 60) + $m + $menit + 1440) % 1440;
            $timings[$kunciApi] = sprintf('%02d:%02d', intdiv($total, 60), $total % 60);
        }

        return $timings;
    }
}
