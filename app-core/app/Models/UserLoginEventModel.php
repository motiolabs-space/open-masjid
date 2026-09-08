<?php

namespace App\Models;

use CodeIgniter\Model;

class UserLoginEventModel extends Model
{
    protected $table            = 'user_login_events';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['user_id', 'role', 'logged_at'];
    protected $useTimestamps    = false;

    /** Riwayat disimpan dua tahun — cukup untuk kohort tahunan, tanpa menumpuk selamanya. */
    public const RETENSI_BULAN = 24;

    /**
     * Mencatat satu login. Sengaja TIDAK melempar galat: kegagalan mencatat
     * statistik tidak boleh sampai menggagalkan proses masuk seseorang.
     */
    public function catat(int $userId, ?string $role): void
    {
        try {
            $this->insert([
                'user_id'   => $userId,
                'role'      => $role,
                'logged_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Gagal mencatat login: ' . $e->getMessage());
        }
    }

    /** Membuang riwayat yang lebih tua dari masa retensi. Dipanggil cron. */
    public function pangkasLama(): int
    {
        $batas = date('Y-m-d H:i:s', strtotime('-' . self::RETENSI_BULAN . ' months'));
        $this->where('logged_at <', $batas)->delete();

        return $this->db->affectedRows();
    }
}
