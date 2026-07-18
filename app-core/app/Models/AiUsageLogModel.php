<?php

namespace App\Models;

use CodeIgniter\Model;

class AiUsageLogModel extends Model
{
    protected $table         = 'ai_usage_logs';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    // created_at WAJIB terdaftar di sini. useTimestamps=false berarti model
    // tidak mengisinya sendiri, jadi ia dikirim manual saat mencatat — dan
    // kolom yang tidak terdaftar dibuang diam-diam oleh model. Tanpa baris ini
    // created_at selalu NULL, dan seluruh rekap yang menyaring per tanggal
    // mengecualikan NULL sehingga tampak kosong meski data ada.
    protected $allowedFields  = [
        'masjid_id', 'tier', 'feature',
        'model_requested', 'model_used',
        'prompt_tokens', 'completion_tokens', 'total_tokens',
        'created_at',
    ];

    // created_at diisi manual saat mencatat (lihat SumoPodAI::catatPemakaian)
    // supaya pencatatan tidak pernah menggagalkan panggilan AI-nya.
    protected $useTimestamps = false;

    /**
     * Rekap pemakaian per model dalam rentang tanggal.
     *
     * @return array<int, array{model_used:string, panggilan:int, total_tokens:int, ...}>
     */
    public function perModel(string $sejak, string $sampai): array
    {
        return $this->select('model_used,
                COUNT(*) AS panggilan,
                SUM(prompt_tokens) AS prompt_tokens,
                SUM(completion_tokens) AS completion_tokens,
                SUM(total_tokens) AS total_tokens')
            ->where('created_at >=', $sejak)
            ->where('created_at <=', $sampai . ' 23:59:59')
            ->groupBy('model_used')
            ->orderBy('total_tokens', 'DESC')
            ->findAll();
    }

    /**
     * Rekap per masjid — untuk melihat masjid mana yang paling banyak memakai.
     * LEFT JOIN agar baris tanpa masjid_id (panggilan non-tenant) tetap terhitung.
     */
    public function perMasjid(string $sejak, string $sampai): array
    {
        // Kolom created_at & total_tokens dikualifikasi dengan nama tabel:
        // JOIN ke 'masjid' membuat 'created_at' ambigu tanpa itu.
        return $this->select('ai_usage_logs.masjid_id,
                COALESCE(masjid.name, "(tanpa masjid)") AS masjid_name,
                COUNT(*) AS panggilan,
                SUM(ai_usage_logs.total_tokens) AS total_tokens')
            ->join('masjid', 'masjid.id = ai_usage_logs.masjid_id', 'left')
            ->where('ai_usage_logs.created_at >=', $sejak)
            ->where('ai_usage_logs.created_at <=', $sampai . ' 23:59:59')
            ->groupBy('ai_usage_logs.masjid_id')
            ->orderBy('total_tokens', 'DESC')
            ->findAll();
    }

    /**
     * Rekap per fitur — apa yang menghabiskan token.
     */
    public function perFitur(string $sejak, string $sampai): array
    {
        return $this->select('COALESCE(feature, "(lainnya)") AS feature,
                COUNT(*) AS panggilan,
                SUM(total_tokens) AS total_tokens')
            ->where('created_at >=', $sejak)
            ->where('created_at <=', $sampai . ' 23:59:59')
            ->groupBy('feature')
            ->orderBy('total_tokens', 'DESC')
            ->findAll();
    }

    /**
     * Angka ringkas untuk kartu di atas halaman.
     */
    public function ringkasan(string $sejak, string $sampai): array
    {
        $row = $this->select('COUNT(*) AS panggilan, SUM(total_tokens) AS total_tokens')
            ->where('created_at >=', $sejak)
            ->where('created_at <=', $sampai . ' 23:59:59')
            ->first();

        return [
            'panggilan'    => (int) ($row['panggilan'] ?? 0),
            'total_tokens' => (int) ($row['total_tokens'] ?? 0),
        ];
    }
}
