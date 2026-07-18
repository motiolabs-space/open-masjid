<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\MasjidReminderModel;
use App\Models\MasjidModel;
use App\Models\MasjidFinanceTransactionModel;
use App\Libraries\PrayerTimes;
use App\Libraries\Channel\ChannelFactory;

/**
 * Mengirim pengingat terjadwal ke grup jamaah.
 *
 * Dipanggil cron di server, disarankan tiap 5 menit:
 *   *\/5 * * * * cd /path/app-core && php spark broadcast:reminders >> writable/logs/reminders.log 2>&1
 *
 * Aman dipanggil sesering apa pun: setiap pengingat hanya terkirim sekali per
 * hari berkat penanda last_sent_at (lihat MasjidReminderModel::jatuhTempo).
 *
 * Opsi:
 *   --dry   tampilkan yang AKAN dikirim tanpa benar-benar mengirim.
 */
class SendReminders extends BaseCommand
{
    protected $group       = 'Masjid';
    protected $name        = 'broadcast:reminders';
    protected $description  = 'Kirim pengingat terjadwal (jadwal sholat, laporan kas) ke grup jamaah.';
    protected $usage        = 'broadcast:reminders [--dry]';

    public function run(array $params)
    {
        $dry = array_key_exists('dry', $params) || in_array('--dry', $params, true);

        $reminderModel = new MasjidReminderModel();
        $masjidModel   = new MasjidModel();

        // Hanya pengingat aktif yang grupnya juga masih aktif.
        $reminders = $reminderModel
            ->select('masjid_reminders.*, masjid_groups.channel, masjid_groups.group_id AS chat_id, masjid_groups.name AS group_name, masjid_groups.is_active AS group_active')
            ->join('masjid_groups', 'masjid_groups.id = masjid_reminders.group_id', 'inner')
            ->where('masjid_reminders.is_active', 1)
            ->where('masjid_groups.is_active', 1)
            ->findAll();

        if (empty($reminders)) {
            CLI::write('Tidak ada pengingat aktif.', 'yellow');
            return;
        }

        $terkirim = 0;
        $dilewati = 0;
        $gagal    = 0;

        foreach ($reminders as $r) {
            $masjid = $masjidModel->find($r['masjid_id']);
            if (!$masjid) {
                continue;
            }

            $tz = trim((string) ($masjid['timezone'] ?? '')) ?: 'Asia/Jakarta';

            if (!$reminderModel->jatuhTempo($r, $tz)) {
                $dilewati++;
                continue;
            }

            $pesan = $this->susunPesan($r['type'], $masjid, $r['channel']);
            if ($pesan === null) {
                // Datanya belum siap (mis. koordinat kosong untuk jadwal sholat).
                // Bukan kegagalan kirim; jangan tandai terkirim agar dicoba lagi
                // saat datanya sudah ada.
                CLI::write("  [lewat] {$masjid['name']} / {$r['type']}: data belum siap", 'yellow');
                $dilewati++;
                continue;
            }

            if ($dry) {
                CLI::write("  [dry] -> {$r['group_name']} ({$r['channel']}) / {$r['type']}", 'cyan');
                CLI::write(rtrim($this->indent($pesan)));
                $terkirim++;
                continue;
            }

            $kanal = ChannelFactory::untuk($r['channel'], $masjid);
            if ($kanal->kirim($r['chat_id'], $pesan)) {
                $reminderModel->tandaiTerkirim((int) $r['id']);
                CLI::write("  [kirim] {$masjid['name']} -> {$r['group_name']} / {$r['type']}", 'green');
                $terkirim++;
            } else {
                // Tidak ditandai terkirim: cron berikutnya akan mencoba lagi.
                CLI::write("  [gagal] {$masjid['name']} -> {$r['group_name']}: " . $kanal->pesanGalat(), 'red');
                $gagal++;
            }
        }

        CLI::write("Selesai. Terkirim: {$terkirim}, dilewati: {$dilewati}, gagal: {$gagal}.",
            $gagal > 0 ? 'red' : 'green');
    }

    /**
     * Menyusun teks pesan sesuai jenis pengingat & kanal.
     *
     * @return string|null null bila datanya belum siap (jangan dikirim).
     */
    private function susunPesan(string $type, array $masjid, string $channel): ?string
    {
        [$judul, $isi] = match ($type) {
            'jadwal_sholat' => $this->isiJadwalSholat($masjid),
            'laporan_kas'   => $this->isiLaporanKas($masjid),
            default         => [null, null],
        };

        if ($judul === null || $isi === null) {
            return null;
        }

        // Telegram menerima HTML terbatas; WhatsApp memakai *bintang* untuk tebal.
        if ($channel === 'telegram') {
            return '<b>' . esc($judul) . '</b>' . "\n\n" . $isi . "\n\n— " . esc($masjid['name']);
        }

        return '*' . $judul . '*' . "\n\n" . $isi . "\n\n— " . $masjid['name'];
    }

    /** @return array{0:?string,1:?string} */
    private function isiJadwalSholat(array $masjid): array
    {
        $jadwal = (new PrayerTimes())->hariIni($masjid);
        if (!$jadwal) {
            return [null, null]; // koordinat belum diisi / AlAdhan gagal
        }

        $baris = [];
        foreach ($jadwal as $nama => $jam) {
            $baris[] = sprintf('%-8s %s', $nama, $jam);
        }

        $tanggal = $this->tanggalIndonesia($masjid);

        return ['Jadwal Sholat ' . $tanggal, implode("\n", $baris)];
    }

    /** @return array{0:?string,1:?string} */
    private function isiLaporanKas(array $masjid): array
    {
        $model = new MasjidFinanceTransactionModel();
        $bulan = date('m');
        $tahun = date('Y');

        $rows = $model->where('masjid_id', $masjid['id'])
            ->where('MONTH(date)', $bulan)
            ->where('YEAR(date)', $tahun)
            ->findAll();

        $masuk = 0;
        $keluar = 0;
        foreach ($rows as $t) {
            if ($t['type'] === 'pemasukan') {
                $masuk += $t['amount'];
            } elseif ($t['type'] === 'pengeluaran') {
                $keluar += $t['amount'];
            }
        }

        $rp = fn($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
        $isi = "Pemasukan   : {$rp($masuk)}\n"
             . "Pengeluaran : {$rp($keluar)}\n"
             . "Saldo       : {$rp($masuk - $keluar)}\n\n"
             . 'Laporan lengkap: ' . base_url($masjid['username'] . '/laporan');

        $namaBulan = $this->namaBulan((int) $bulan);

        return ["Laporan Kas {$namaBulan} {$tahun}", $isi];
    }

    private function tanggalIndonesia(array $masjid): string
    {
        $tz = trim((string) ($masjid['timezone'] ?? '')) ?: 'Asia/Jakarta';
        try {
            $d = new \DateTime('now', new \DateTimeZone($tz));
        } catch (\Throwable $e) {
            $d = new \DateTime('now');
        }

        $hari = [
            'Sunday' => 'Ahad', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
        ][$d->format('l')] ?? '';

        return trim($hari . ', ' . $d->format('j') . ' ' . $this->namaBulan((int) $d->format('n')) . ' ' . $d->format('Y'));
    }

    private function namaBulan(int $n): string
    {
        return [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][$n] ?? '';
    }

    private function indent(string $teks): string
    {
        return preg_replace('/^/m', '        ', $teks);
    }
}
