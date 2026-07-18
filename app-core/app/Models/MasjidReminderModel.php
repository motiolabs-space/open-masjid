<?php

namespace App\Models;

use CodeIgniter\Model;

class MasjidReminderModel extends Model
{
    protected $table         = 'masjid_reminders';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'masjid_id', 'group_id', 'type', 'frequency',
        'day_of_week', 'day_of_month', 'time', 'is_active', 'last_sent_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'masjid_id' => 'required|is_natural_no_zero',
        'group_id'  => [
            'label' => 'Grup Tujuan',
            'rules' => 'required|is_natural_no_zero',
        ],
        'type' => [
            'label' => 'Jenis Pengingat',
            'rules' => 'required|in_list[jadwal_sholat,laporan_kas]',
        ],
        'frequency' => [
            'label' => 'Frekuensi',
            'rules' => 'required|in_list[harian,mingguan,bulanan]',
        ],
        'time' => [
            'label' => 'Jam Kirim',
            'rules' => 'required',
        ],
    ];

    /** Pengingat aktif milik satu masjid, digabung nama grupnya. */
    public function aktifMilik(int $masjidId): array
    {
        return $this->select('masjid_reminders.*, masjid_groups.name AS group_name, masjid_groups.channel')
            ->join('masjid_groups', 'masjid_groups.id = masjid_reminders.group_id', 'left')
            ->where('masjid_reminders.masjid_id', $masjidId)
            ->orderBy('masjid_reminders.time', 'ASC')
            ->findAll();
    }

    /**
     * Menentukan apakah sebuah pengingat harus dikirim SEKARANG.
     *
     * Semua perbandingan waktu memakai ZONA WAKTU MASJID, bukan zona server:
     * pengingat subuh 04:30 harus menyala pukul 04:30 waktu setempat, bukan
     * waktu server yang bisa berbeda beberapa jam.
     *
     * Desain "susulan": bila cron terlewat menit persisnya, pengingat tetap
     * terkirim di pemanggilan berikutnya pada hari yang sama — lebih baik telat
     * daripada hilang. Anti-kirim-ganda dijaga dengan membandingkan TANGGAL
     * last_sent_at (zona masjid) dengan hari ini.
     *
     * @param string $timezone zona waktu masjid (mis. 'Asia/Jakarta')
     */
    public function jatuhTempo(array $reminder, string $timezone): bool
    {
        try {
            $tz  = new \DateTimeZone($timezone !== '' ? $timezone : 'Asia/Jakarta');
        } catch (\Throwable $e) {
            $tz = new \DateTimeZone('Asia/Jakarta');
        }
        $now = new \DateTime('now', $tz);

        // 1. Frekuensi harus cocok dengan hari ini.
        if ($reminder['frequency'] === 'mingguan') {
            // DateTime 'w': 0=Minggu..6=Sabtu — sama dengan day_of_week kita.
            if ((int) $now->format('w') !== (int) $reminder['day_of_week']) {
                return false;
            }
        } elseif ($reminder['frequency'] === 'bulanan') {
            if ((int) $now->format('j') !== (int) $reminder['day_of_month']) {
                return false;
            }
        }

        // 2. Jam kirim sudah lewat (atau tepat) hari ini.
        $jamKirim = substr((string) $reminder['time'], 0, 5); // 'HH:MM'
        if ($now->format('H:i') < $jamKirim) {
            return false;
        }

        // 3. Belum terkirim hari ini (bandingkan tanggal dalam zona masjid).
        if (!empty($reminder['last_sent_at'])) {
            $terakhir = new \DateTime($reminder['last_sent_at'], new \DateTimeZone(date_default_timezone_get()));
            $terakhir->setTimezone($tz);
            if ($terakhir->format('Y-m-d') === $now->format('Y-m-d')) {
                return false;
            }
        }

        return true;
    }

    /** Menandai pengingat sudah terkirim (memakai waktu server, apa adanya). */
    public function tandaiTerkirim(int $id): void
    {
        $this->update($id, ['last_sent_at' => date('Y-m-d H:i:s')]);
    }
}
