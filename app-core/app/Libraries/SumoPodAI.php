<?php

namespace App\Libraries;

use Config\Services;

class SumoPodAI
{
    protected $apiUrl;
    protected $apiKey;
    protected $model;        // model tingkat 'ringan' (harian, murah)
    protected $modelBerat;   // model tingkat 'berat' (kualitas, tugas penting)
    protected $fallbackModels;
    protected $client;
    protected $masjidId;     // konteks tenant untuk catatan pemakaian, boleh null

    /**
     * @param int|null $masjidId Masjid pemicu, dicatat pada log pemakaian token.
     *        Boleh null untuk panggilan yang tidak terikat masjid tertentu.
     */
    public function __construct(?int $masjidId = null)
    {
        $this->apiUrl     = getenv('sumopod.apiUrl') ?: 'https://ai.sumopod.com/v1';
        $this->apiKey     = getenv('sumopod.apiKey');
        $this->model      = getenv('sumopod.model') ?: 'gpt-4o-mini';
        // Bila model berkualitas tidak disetel, jatuh ke model ringan — lebih
        // baik memakai yang murah daripada memakai nama model yang tidak ada.
        $this->modelBerat = getenv('sumopod.modelBerat') ?: $this->model;
        $this->masjidId   = $masjidId;

        $fallback = getenv('sumopod.fallbackModels');
        if ($fallback) {
            $this->fallbackModels = array_map('trim', explode(',', $fallback));
        } else {
            $this->fallbackModels = ['gpt-4o-mini', 'claude-haiku-4-5', 'gemini/gemini-2.5-flash'];
        }

        $this->client = Services::curlrequest();
    }

    /**
     * Menerjemahkan tingkat tugas menjadi nama model.
     *
     * Model tetap tinggal di .env (config), tidak tersebar di kode. Pemanggil
     * cukup menyebut 'ringan' atau 'berat', sehingga mengganti model satu
     * tingkat tidak menyentuh satu baris kode pun.
     */
    private function modelUntukTingkat(string $tier): string
    {
        return $tier === 'berat' ? $this->modelBerat : $this->model;
    }

    /**
     * Send a chat completion request to SumoPod AI
     *
     * @param string|array $messages Pesan tunggal atau array pesan.
     * @param array $options Opsi tambahan. Selain parameter API biasa
     *        (max_tokens, temperature), dikenali tiga kunci khusus yang TIDAK
     *        diteruskan ke API:
     *          - 'tier'    : 'ringan' (default) | 'berat' — memilih model.
     *          - 'feature' : label pemicu untuk catatan pemakaian (mis. 'audit').
     *          - 'model'   : memaksa model tertentu, mengalahkan 'tier'.
     * @return string|null Isi balasan, atau null bila semua model gagal.
     */
    public function chatCompletion($messages, array $options = [])
    {
        if (is_string($messages)) {
            $messages = [['role' => 'user', 'content' => $messages]];
        }

        // Keluarkan kunci khusus agar tidak ikut terkirim sebagai parameter API.
        $tier    = $options['tier'] ?? 'ringan';
        $feature = $options['feature'] ?? null;
        $paksaModel = $options['model'] ?? null;
        unset($options['tier'], $options['feature'], $options['model']);

        $primaryModel = $paksaModel ?: $this->modelUntukTingkat($tier);
        $modelsToTry  = [$primaryModel];
        foreach ($this->fallbackModels as $fb) {
            if (!in_array($fb, $modelsToTry, true)) {
                $modelsToTry[] = $fb;
            }
        }

        $url = rtrim($this->apiUrl, '/') . '/chat/completions';

        foreach ($modelsToTry as $currentModel) {
            $payload = array_merge([
                'max_tokens'  => 150,
                'temperature' => 0.7,
            ], $options);
            $payload['model']    = $currentModel;
            $payload['messages'] = $messages;

            try {
                $response = $this->client->post($url, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Content-Type'  => 'application/json',
                    ],
                    'json' => $payload,
                    'http_errors' => false,
                ]);

                if ($response->getStatusCode() === 200) {
                    $body = json_decode($response->getBody(), true);

                    // Model yang dicatat adalah yang DILAPORKAN balasan API —
                    // bisa berbeda dari yang diminta bila server mengarahkannya.
                    $this->catatPemakaian(
                        $tier,
                        $feature,
                        $primaryModel,
                        $body['model'] ?? $currentModel,
                        $body['usage'] ?? []
                    );

                    return $body['choices'][0]['message']['content'] ?? null;
                }

                // Gagal (mis. 429 kuota habis): lanjut ke model berikutnya.
                log_message('warning', "SumoPod AI Error on model {$currentModel}: [" . $response->getStatusCode() . '] ' . $response->getBody());
                continue;

            } catch (\Exception $e) {
                log_message('error', "SumoPod AI Exception on model {$currentModel}: " . $e->getMessage());
                continue;
            }
        }

        // If all models fail
        log_message('error', 'SumoPod AI: All fallback models failed.');
        return null;
    }

    /**
     * Mencatat satu pemakaian token ke ai_usage_logs.
     *
     * Dibungkus try/catch dan tidak pernah melempar: pencatatan biaya TIDAK
     * boleh menggagalkan jawaban AI yang sudah berhasil didapat jamaah. Bila
     * tabelnya belum ada atau basis data sedang bermasalah, panggilan tetap
     * berjalan seperti biasa.
     */
    private function catatPemakaian(string $tier, ?string $feature, string $diminta, string $dipakai, array $usage): void
    {
        try {
            (new \App\Models\AiUsageLogModel())->insert([
                'masjid_id'         => $this->masjidId,
                'tier'              => $tier,
                'feature'           => $feature,
                'model_requested'   => $diminta,
                'model_used'        => $dipakai,
                'prompt_tokens'     => (int) ($usage['prompt_tokens'] ?? 0),
                'completion_tokens' => (int) ($usage['completion_tokens'] ?? 0),
                'total_tokens'      => (int) ($usage['total_tokens'] ?? 0),
                'created_at'        => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Gagal mencatat pemakaian token AI: ' . $e->getMessage());
        }
    }

    /**
     * Generate an AI Scoring and Reasoning for Mustahik
     *
     * @param array $mustahikData Data containing income, dependents, house status, etc.
     * @return array|null Associative array with 'score' and 'reasoning' keys
     */
    public function scoreMustahik(array $mustahikData)
    {
        $prompt = "Anda adalah AI Asisten Amil Zakat. Berikan penilaian objyektif kelayakan menerima bantuan (skor 1-100) berdasarkan data Mustahik berikut:\n";
        $prompt .= "Nama: " . ($mustahikData['name'] ?? '-') . "\n";
        $prompt .= "Pendapatan per Bulan: Rp " . number_format($mustahikData['income_per_month'] ?? 0, 0, ',', '.') . "\n";
        $prompt .= "Jumlah Tanggungan: " . ($mustahikData['dependents_count'] ?? 0) . " orang\n";
        $prompt .= "Status Kepemilikan Rumah: " . ($mustahikData['house_ownership'] ?? 'lainnya') . "\n\n";
        $prompt .= "Output HANYA dalam format JSON valid tanpa markdown block seperti berikut:\n";
        $prompt .= "{\"score\": 85, \"reasoning\": \"Alasan maksimal 2 kalimat.\"}";

        $response = $this->chatCompletion($prompt, [
            'temperature' => 0.2, // Low temperature for consistent scoring
            'max_tokens'  => 100,
            // Berkualitas: skor ini menentukan siapa yang dinilai layak menerima
            // bantuan zakat — bukan tempat berhemat model.
            'tier'    => 'berat',
            'feature' => 'skor_mustahik',
        ]);

        if ($response) {
            // Clean markdown if AI accidentally adds it
            $response = str_replace(['```json', '```'], '', $response);
            $decoded = json_decode(trim($response), true);
            if (isset($decoded['score']) && isset($decoded['reasoning'])) {
                return $decoded;
            }
        }

        return null;
    }
    /**
     * Run Financial Audit
     *
     * @param array $currentData
     * @param array $historicalData
     * @return array|null
     */
    public function runFinancialAudit(array $currentData, array $historicalData)
    {
        $prompt = "Kamu adalah Auditor Finansial Masjid profesional yang tajam.\n";
        $prompt .= "Bandingkan pengeluaran bulan ini dengan rata-rata 3 bulan terakhir. Cari anomali, lonjakan tidak wajar, atau pengeluaran mencurigakan.\n\n";
        
        $prompt .= "Data Rata-rata 3 Bulan Terakhir:\n" . json_encode($historicalData, JSON_PRETTY_PRINT) . "\n\n";
        $prompt .= "Data Pengeluaran Bulan Ini:\n" . json_encode($currentData, JSON_PRETTY_PRINT) . "\n\n";
        
        $prompt .= "Output HANYA berupa array JSON valid. Setiap objek harus memiliki key:\n";
        $prompt .= "- 'category_name' (string)\n";
        $prompt .= "- 'finding' (string: penjelasan singkat)\n";
        $prompt .= "- 'severity' (string: 'high', 'medium', 'low')\n";
        $prompt .= "- 'recommendation' (string: saran tindakan)\n";
        $prompt .= "Jika tidak ada anomali sama sekali, kembalikan array kosong [].\n";
        $prompt .= "HANYA output JSON TANPA tag ```json atau teks lain.";

        $response = $this->chatCompletion($prompt, [
            'temperature' => 0.2,
            'max_tokens'  => 800,
            // Berkualitas: audit anomali keuangan perlu nalar yang tajam.
            'tier'    => 'berat',
            'feature' => 'audit_keuangan',
        ]);

        if ($response) {
            $response = str_replace(['```json', '```'], '', trim($response));
            $decoded = json_decode($response, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }
}
