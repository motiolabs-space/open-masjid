<?php

namespace App\Libraries;

/**
 * Penerbitan dan pemeriksaan tautan verifikasi email.
 *
 * Bentuknya meniru alur reset kata sandi yang sudah terbukti di proyek ini:
 * token mentah hanya dikirim lewat email, yang disimpan cukup hash-nya, ada
 * masa berlaku, dan sekali pakai.
 */
class EmailVerification
{
    /** Masa berlaku tautan. Panjang dengan sengaja — ini bukan tautan sensitif
     *  seperti reset sandi, dan orang kerap membuka emailnya beberapa hari
     *  kemudian. Tautan mati justru menambah gesekan tanpa menambah keamanan. */
    public const BERLAKU_HARI = 7;

    /**
     * Menerbitkan tautan baru untuk seorang pengguna.
     *
     * Token lama miliknya dibatalkan lebih dulu supaya hanya tautan terakhir
     * yang hidup — kalau tidak, tautan dari email lama tetap bisa dipakai
     * setelah pengguna meminta kirim ulang.
     *
     * @return string|null URL lengkap, atau null bila gagal.
     */
    public static function terbitkan(int $userId, string $email): ?string
    {
        try {
            $db = \Config\Database::connect();

            $db->table('email_verifications')
                ->where('user_id', $userId)
                ->where('used_at IS NULL')
                ->update(['used_at' => date('Y-m-d H:i:s')]);

            $mentah = bin2hex(random_bytes(32));
            $db->table('email_verifications')->insert([
                'user_id'    => $userId,
                'email'      => $email,
                'token_hash' => hash('sha256', $mentah),
                'expires_at' => date('Y-m-d H:i:s', time() + self::BERLAKU_HARI * 86400),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return base_url('verifikasi-email/' . $mentah);
        } catch (\Throwable $e) {
            log_message('error', 'Gagal menerbitkan tautan verifikasi: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Menukar token mentah menjadi verifikasi yang sah.
     *
     * @return array{status:string, user_id?:int} status: 'ok' | 'terpakai' |
     *               'kedaluwarsa' | 'tidak_sah' | 'sudah'
     */
    public static function tukarkan(string $mentah): array
    {
        $db = \Config\Database::connect();

        $baris = $db->table('email_verifications')
            ->where('token_hash', hash('sha256', $mentah))
            ->get()->getRowArray();

        if (! $baris) {
            return ['status' => 'tidak_sah'];
        }

        $userId = (int) $baris['user_id'];

        // Sudah terverifikasi lewat tautan lain: perlakukan sebagai berhasil,
        // bukan galat. Mengklik dua kali bukan kesalahan pengguna.
        $user = $db->table('users')->select('email_verified_at')->where('id', $userId)->get()->getRowArray();
        if ($user && ! empty($user['email_verified_at'])) {
            return ['status' => 'sudah', 'user_id' => $userId];
        }

        if (! empty($baris['used_at'])) {
            return ['status' => 'terpakai'];
        }
        if (strtotime($baris['expires_at']) < time()) {
            return ['status' => 'kedaluwarsa'];
        }

        $waktu = date('Y-m-d H:i:s');
        $db->table('email_verifications')->where('id', $baris['id'])->update(['used_at' => $waktu]);
        $db->table('users')->where('id', $userId)->update(['email_verified_at' => $waktu]);

        return ['status' => 'ok', 'user_id' => $userId];
    }

    /**
     * Membuang token yang sudah terpakai atau kedaluwarsa lebih dari 30 hari.
     * Dipanggil cron `broadcast:reminders`, seperti tabel berumur lainnya.
     *
     * Memakai kueri langsung alih-alih query builder: susunan groupStart /
     * orGroupStart untuk dua syarat OR ini sempat menghasilkan SQL yang tak
     * menghapus apa pun. Bentuk di bawah lebih mudah dibaca sekaligus dipastikan.
     */
    public static function pangkasLama(): int
    {
        $db = \Config\Database::connect();
        $batas = date('Y-m-d H:i:s', strtotime('-30 days'));

        $db->query(
            'DELETE FROM email_verifications
             WHERE (used_at IS NOT NULL AND used_at < ?) OR expires_at < ?',
            [$batas, $batas]
        );

        return $db->affectedRows();
    }

    /** Apakah pengguna yang sedang login sudah memverifikasi emailnya? */
    public static function sudahTerverifikasi(): bool
    {
        $userId = session()->get('user_id');
        if (empty($userId)) {
            return true;   // belum login: bukan urusan spanduk verifikasi
        }

        // Dibaca dari basis data, bukan session — sama alasannya dengan
        // pengurus_saat_ini(): salinan di session bisa basi setelah pengguna
        // memverifikasi lewat perangkat lain.
        static $cache = null;
        if ($cache !== null) {
            return $cache;
        }

        $user = \Config\Database::connect()
            ->table('users')->select('email_verified_at')->where('id', $userId)
            ->get()->getRowArray();

        return $cache = ! empty($user['email_verified_at']);
    }
}
