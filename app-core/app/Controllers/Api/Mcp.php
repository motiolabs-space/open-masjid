<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\MasjidModel;
use App\Models\MasjidFinanceTransactionModel;
use App\Libraries\PrayerTimes;

/**
 * MCP (Model Context Protocol) server — memberi agen AI akses TERKONTROL dan
 * HANYA-BACA ke data satu masjid.
 *
 * KEAMANAN MULTI-TENANT — INTI DARI SELURUH BERKAS INI
 * Aplikasi ini melayani banyak masjid di satu server. Karena itu:
 *   - Autentikasi lewat token Bearer yang memetakan tepat SATU masjid
 *     (masjid.mcp_token, unik). Masjid TIDAK PERNAH diambil dari parameter
 *     pemanggil — selalu diturunkan dari token. Dengan begitu, tool apa pun
 *     secara struktural tidak bisa menyentuh masjid lain.
 *   - Semua tool HANYA-BACA. Tidak ada yang mengubah data. Menambah tool yang
 *     menulis kelak menuntut pertimbangan tersendiri (persetujuan, audit).
 *
 * Ditulis tangan sebagai JSON-RPC 2.0 minimal (tanpa pustaka pihak ketiga) agar
 * permukaan serangannya kecil dan mudah diaudit — yang paling penting di sini
 * bukan kelengkapan protokol, melainkan penyaringan tenant yang tak bisa
 * ditembus.
 */
class Mcp extends BaseController
{
    private const PROTOCOL = '2024-11-05';

    /** Masjid pemilik token; diisi guard() sebelum tool mana pun berjalan. */
    private ?array $masjid = null;

    public function handle()
    {
        // Hanya POST JSON-RPC.
        if (strtoupper($this->request->getMethod()) !== 'POST') {
            return $this->response->setStatusCode(405)->setJSON(['error' => 'Method not allowed']);
        }

        // 1. Autentikasi -> tentukan masjid dari token. Gagal = berhenti total,
        // sebelum body diproses.
        $masjid = $this->masjidDariToken();
        if ($masjid === null) {
            return $this->response->setStatusCode(401)
                ->setJSON($this->galat(null, -32001, 'Token MCP tidak sah atau MCP tidak aktif untuk masjid ini.'));
        }
        $this->masjid = $masjid;

        // 2. Parse JSON-RPC.
        $req = json_decode($this->request->getBody(), true);
        if (!is_array($req) || ($req['jsonrpc'] ?? null) !== '2.0' || empty($req['method'])) {
            return $this->response->setJSON($this->galat($req['id'] ?? null, -32600, 'Permintaan JSON-RPC tidak sah.'));
        }

        $id     = $req['id'] ?? null;
        $method = $req['method'];
        $params = $req['params'] ?? [];

        // 3. Dispatch.
        switch ($method) {
            case 'initialize':
                return $this->response->setJSON($this->hasil($id, [
                    'protocolVersion' => self::PROTOCOL,
                    'capabilities'    => ['tools' => new \stdClass()],
                    'serverInfo'      => ['name' => 'masjid-mcp', 'version' => '1.0'],
                ]));

            case 'notifications/initialized':
                // Notifikasi tanpa id: tak perlu balasan berisi.
                return $this->response->setStatusCode(202)->setBody('');

            case 'tools/list':
                return $this->response->setJSON($this->hasil($id, ['tools' => self::daftarTool()]));

            case 'tools/call':
                return $this->response->setJSON($this->panggilTool($id, $params));

            default:
                return $this->response->setJSON($this->galat($id, -32601, "Metode tidak dikenal: {$method}"));
        }
    }

    // ── Autentikasi ──────────────────────────────────────────────────────

    private function masjidDariToken(): ?array
    {
        $header = $this->request->getHeaderLine('Authorization');
        if (!preg_match('/^Bearer\s+(.+)$/i', trim($header), $m)) {
            return null;
        }
        $token = trim($m[1]);
        // Token kosong tidak boleh mencocokkan masjid yang mcp_token-nya NULL.
        if ($token === '') {
            return null;
        }

        return (new MasjidModel())->where('mcp_token', $token)->first();
    }

    // ── Definisi & pemanggilan tool ──────────────────────────────────────

    /**
     * Daftar tool MCP. public static agar halaman panduan di dashboard memakai
     * sumber yang sama — deskripsi tool tidak berduplikat.
     */
    public static function daftarTool(): array
    {
        $kosong = ['type' => 'object', 'properties' => new \stdClass()];

        return [
            [
                'name'        => 'cek_kas',
                'description' => 'Ringkasan kas masjid bulan berjalan: total pemasukan, pengeluaran, dan saldo.',
                'inputSchema' => $kosong,
            ],
            [
                'name'        => 'jadwal_sholat',
                'description' => 'Jadwal sholat hari ini untuk masjid ini (sudah termasuk koreksi menit pengurus).',
                'inputSchema' => $kosong,
            ],
            [
                'name'        => 'donasi_terbaru',
                'description' => 'Ringkasan donasi berhasil terbaru masjid ini (maksimal 10).',
                'inputSchema' => $kosong,
            ],
        ];
    }

    private function panggilTool($id, array $params)
    {
        $nama = $params['name'] ?? '';

        $teks = match ($nama) {
            'cek_kas'         => $this->toolCekKas(),
            'jadwal_sholat'   => $this->toolJadwalSholat(),
            'donasi_terbaru'  => $this->toolDonasiTerbaru(),
            default           => null,
        };

        if ($teks === null) {
            return $this->galat($id, -32602, "Tool tidak dikenal: {$nama}");
        }

        return $this->hasil($id, [
            'content' => [['type' => 'text', 'text' => $teks]],
        ]);
    }

    // ── Tool (semua ter-scope ke $this->masjid) ─────────────────────────

    private function toolCekKas(): string
    {
        $k  = (new \App\Libraries\MasjidData())->kasBulanIni($this->masjid);
        $rp = fn($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');

        return "Kas {$this->masjid['name']} bulan " . date('m/Y') . ":\n"
             . "- Pemasukan: {$rp($k['pemasukan'])}\n"
             . "- Pengeluaran: {$rp($k['pengeluaran'])}\n"
             . "- Saldo: {$rp($k['saldo'])}";
    }

    private function toolJadwalSholat(): string
    {
        $jadwal = (new \App\Libraries\MasjidData())->jadwalSholatHariIni($this->masjid);
        if (!$jadwal) {
            return "Jadwal sholat belum tersedia (koordinat masjid belum diisi atau layanan jadwal gagal).";
        }

        $baris = [];
        foreach ($jadwal as $nama => $jam) {
            $baris[] = "- {$nama}: {$jam}";
        }

        return "Jadwal sholat hari ini di {$this->masjid['name']}:\n" . implode("\n", $baris);
    }

    private function toolDonasiTerbaru(): string
    {
        $rows = (new \App\Libraries\MasjidData())->donasiTerbaru($this->masjid, 10);
        if (empty($rows)) {
            return "Belum ada donasi berhasil di {$this->masjid['name']}.";
        }

        $baris = [];
        foreach ($rows as $d) {
            $baris[] = '- ' . $d['donor_name'] . ': Rp ' . number_format($d['amount'], 0, ',', '.')
                     . ' (' . ($d['paid_at'] ?: '-') . ')';
        }

        return "Donasi berhasil terbaru di {$this->masjid['name']}:\n" . implode("\n", $baris);
    }

    // ── Pembungkus JSON-RPC ──────────────────────────────────────────────

    private function hasil($id, array $result): array
    {
        return ['jsonrpc' => '2.0', 'id' => $id, 'result' => $result];
    }

    private function galat($id, int $code, string $message): array
    {
        return ['jsonrpc' => '2.0', 'id' => $id, 'error' => ['code' => $code, 'message' => $message]];
    }
}
