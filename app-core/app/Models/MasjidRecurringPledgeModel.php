<?php

namespace App\Models;

use CodeIgniter\Model;

class MasjidRecurringPledgeModel extends Model
{
    protected $table         = 'masjid_recurring_pledges';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'masjid_id', 'program_id', 'donor_name', 'donor_phone', 'donor_email',
        'amount', 'frequency', 'next_reminder_date', 'active', 'last_reminded_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Janji donasi rutin yang pengingatnya sudah jatuh tempo hari ini atau
     * terlewat, dan masih aktif. Dipakai command broadcast:reminders.
     */
    public function jatuhTempo(?string $hari = null): array
    {
        $hari ??= date('Y-m-d');
        return $this->where('active', 1)
            ->where('next_reminder_date <=', $hari)
            ->findAll();
    }

    /**
     * Majukan jadwal pengingat berikutnya sesuai frekuensi, dan tandai kapan
     * terakhir diingatkan. Dipanggil setelah pengingat berhasil terkirim.
     *
     * Basis maju adalah tanggal jatuh tempo, bukan hari ini — supaya jadwal
     * tidak "melar" bila cron telat berjalan.
     */
    public function majukanJadwal(array $pledge): bool
    {
        $basis  = $pledge['next_reminder_date'] ?: date('Y-m-d');
        $tambah = ($pledge['frequency'] === 'mingguan') ? '+1 week' : '+1 month';
        $berikut = date('Y-m-d', strtotime($basis . ' ' . $tambah));

        // Bila cron lama mati, satu lompatan mungkin masih di masa lalu; kejar
        // sampai melewati hari ini agar tidak mengirim beruntun.
        $hariIni = date('Y-m-d');
        while ($berikut <= $hariIni) {
            $berikut = date('Y-m-d', strtotime($berikut . ' ' . $tambah));
        }

        return $this->update($pledge['id'], [
            'next_reminder_date' => $berikut,
            'last_reminded_at'   => date('Y-m-d H:i:s'),
        ]);
    }
}
