<?php

namespace App\Models;

use CodeIgniter\Model;

class CsvImportDraftModel extends Model
{
    protected $table         = 'csv_import_drafts';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields  = ['masjid_id', 'user_id', 'payload', 'created_at'];
    protected $useTimestamps = false;

    /** Draf menganggur dibuang setelah sekian jam. */
    public const RETENSI_JAM = 6;

    /**
     * Menyimpan draf, mengembalikan id-nya.
     */
    public function simpan(int $masjidId, int $userId, array $transaksi): int
    {
        return (int) $this->insert([
            'masjid_id'  => $masjidId,
            'user_id'    => $userId,
            'payload'    => json_encode($transaksi),
            'created_at' => date('Y-m-d H:i:s'),
        ], true);
    }

    /**
     * Mengambil transaksi sebuah draf — HANYA bila milik masjid & pengguna ini
     * (mencegah menebak id draf orang lain).
     *
     * @return array<int,array>|null
     */
    public function ambil(int $id, int $masjidId, int $userId): ?array
    {
        $baris = $this->where(['id' => $id, 'masjid_id' => $masjidId, 'user_id' => $userId])->first();
        if (!$baris) {
            return null;
        }

        $data = json_decode($baris['payload'], true);

        return is_array($data) ? $data : null;
    }

    /** Menghapus draf milik masjid & pengguna ini (dipanggil setelah disimpan). */
    public function hapusMilik(int $id, int $masjidId, int $userId): void
    {
        $this->where(['id' => $id, 'masjid_id' => $masjidId, 'user_id' => $userId])->delete();
    }

    /**
     * Membuang draf menganggur yang lebih tua dari masa retensi. Dipanggil cron
     * (broadcast:reminders).
     */
    public function pangkasLama(): int
    {
        $batas = date('Y-m-d H:i:s', strtotime('-' . self::RETENSI_JAM . ' hours'));
        $this->where('created_at <', $batas)->delete();

        return $this->db->affectedRows();
    }
}
