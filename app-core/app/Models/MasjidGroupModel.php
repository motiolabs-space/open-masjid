<?php

namespace App\Models;

use CodeIgniter\Model;

class MasjidGroupModel extends Model
{
    protected $table            = 'masjid_groups';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['masjid_id', 'channel', 'group_id', 'name', 'is_active'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Label disisipkan ke dalam aturan agar pesan kesalahan menyebut nama yang
    // dikenal pengurus. Kalimatnya ada di app/Language/en/Validation.php.
    protected $validationRules = [
        'masjid_id' => 'required|is_natural_no_zero',
        'channel'   => [
            'label' => 'Kanal',
            'rules' => 'required|in_list[telegram,whatsapp]',
        ],
        'group_id' => [
            'label' => 'ID Grup',
            'rules' => 'required|max_length[100]',
        ],
        'name' => [
            'label' => 'Nama Grup',
            'rules' => 'required|min_length[3]|max_length[150]',
        ],
    ];

    /**
     * Grup aktif milik satu masjid. Selalu lewat sini, jangan menyusun kueri
     * sendiri — supaya penyaringan masjid_id tidak pernah lupa dipasang.
     */
    public function aktifMilik(int $masjidId): array
    {
        return $this->where(['masjid_id' => $masjidId, 'is_active' => 1])
            ->orderBy('channel', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Apakah grup ini terdaftar dan aktif untuk masjid tersebut.
     *
     * Dipakai webhook Telegram sebelum melayani sebuah grup. Tanpa pemeriksaan
     * ini, siapa pun bisa menambahkan bot masjid ke grupnya sendiri lalu
     * memancing keluar ringkasan keuangan masjid itu.
     */
    public function terdaftar(string $channel, string $groupId): ?array
    {
        return $this->where([
            'channel'   => $channel,
            'group_id'  => $groupId,
            'is_active' => 1,
        ])->first();
    }

    /**
     * Baris grup apa pun statusnya (aktif maupun menunggu persetujuan).
     * Dipakai webhook untuk membedakan grup baru dari yang sudah tercatat.
     */
    public function cari(string $channel, string $groupId): ?array
    {
        return $this->where(['channel' => $channel, 'group_id' => $groupId])->first();
    }

    /**
     * Mencatat grup baru sebagai MENUNGGU PERSETUJUAN (is_active = 0).
     *
     * Dipanggil webhook saat bot menerima pesan dari grup yang belum dikenal.
     * Tujuannya semata memunculkan group_id di halaman kelola grup supaya
     * pengurus bisa mengaktifkannya — bot TIDAK melayani grup selama masih
     * menunggu, sehingga siapa pun boleh memasukkan bot ke grupnya tanpa
     * membocorkan apa-apa. Aktivasi adalah keputusan pengurus.
     *
     * Tidak menimpa baris yang sudah ada (unik channel+group_id), sehingga grup
     * yang sudah diaktifkan tidak diam-diam dikembalikan ke status menunggu.
     */
    public function catatPending(int $masjidId, string $channel, string $groupId, string $nama): void
    {
        if ($this->cari($channel, $groupId) !== null) {
            return;
        }

        $this->insert([
            'masjid_id' => $masjidId,
            'channel'   => $channel,
            'group_id'  => $groupId,
            'name'      => $nama !== '' ? $nama : 'Grup Baru',
            'is_active' => 0,
        ]);
    }
}

