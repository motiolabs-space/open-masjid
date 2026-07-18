<?php

namespace App\Models;

use CodeIgniter\Model;

class MasjidGroupMessageModel extends Model
{
    protected $table         = 'masjid_group_messages';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'masjid_id', 'group_id', 'tg_message_id', 'sender_name', 'text', 'sent_at', 'created_at',
    ];
    protected $useTimestamps = false;

    /** Berapa hari pesan grup disimpan sebelum dibuang otomatis. */
    public const RETENSI_HARI = 3;

    /**
     * Menyimpan satu pesan grup. Aman dipanggil ulang untuk pesan yang sama:
     * kunci unik (group_id, tg_message_id) mencegah duplikat, dan galat duplikat
     * ditelan diam-diam.
     */
    public function simpan(int $masjidId, int $groupRowId, array $message): void
    {
        $teks = trim((string) ($message['text'] ?? ''));
        if ($teks === '') {
            return;
        }

        // Nama pengirim seadanya, TANPA nomor telепon (lihat catatan privasi
        // pada migrasi).
        $from = $message['from'] ?? [];
        $nama = trim(($from['first_name'] ?? '') . ' ' . ($from['last_name'] ?? ''));
        if ($nama === '') {
            $nama = $from['username'] ?? 'Anonim';
        }

        try {
            $this->insert([
                'masjid_id'     => $masjidId,
                'group_id'      => $groupRowId,
                'tg_message_id' => $message['message_id'] ?? null,
                'sender_name'   => mb_substr($nama, 0, 150),
                'text'          => $teks,
                'sent_at'       => isset($message['date']) ? date('Y-m-d H:i:s', (int) $message['date']) : null,
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            // Duplikat (kunci unik) atau galat lain tidak boleh mengganggu
            // penanganan webhook.
        }
    }

    /**
     * Pesan terbaru sebuah grup untuk diringkas — urut lama->baru agar konteks
     * percakapan runut.
     */
    public function terbaru(int $groupRowId, int $batas = 100): array
    {
        $rows = $this->where('group_id', $groupRowId)
            ->orderBy('id', 'DESC')
            ->limit($batas)
            ->findAll();

        return array_reverse($rows);
    }

    /**
     * Membuang pesan yang lebih tua dari masa retensi. Dipanggil cron
     * (broadcast:reminders) supaya tabel tidak menyimpan obrolan jamaah lebih
     * lama dari yang diperlukan.
     *
     * @return int jumlah baris terhapus
     */
    public function pangkasLama(): int
    {
        $batas = date('Y-m-d H:i:s', strtotime('-' . self::RETENSI_HARI . ' days'));
        $this->where('created_at <', $batas)->delete();

        return $this->db->affectedRows();
    }
}
