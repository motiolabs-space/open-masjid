<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\MasjidModel;
use App\Libraries\MasjidData;

/**
 * REST API per masjid — HANYA-BACA, dipakai integrasi umum (bukan agen AI;
 * untuk itu ada MCP di Api\Mcp).
 *
 * KEAMANAN MULTI-TENANT
 * Autentikasi token Bearer -> masjid.api_token (unik) -> tepat SATU masjid.
 * Tidak ada endpoint yang menerima masjid_id dari pemanggil; masjid selalu
 * diturunkan dari token. Datanya diambil lewat App\Libraries\MasjidData yang
 * sama dengan MCP, jadi angka yang disajikan kedua antarmuka selalu konsisten.
 *
 * Balasan seragam:
 *   sukses -> {"status":"success","data":...}
 *   gagal  -> {"status":"error","message":...}  (dengan kode HTTP yang sesuai)
 */
class RestApi extends BaseController
{
    private ?array $masjid = null;

    // ── Endpoint ─────────────────────────────────────────────────────────

    public function kas()
    {
        if ($resp = $this->guard()) {
            return $resp;
        }

        return $this->sukses((new MasjidData())->kasBulanIni($this->masjid));
    }

    public function jadwalSholat()
    {
        if ($resp = $this->guard()) {
            return $resp;
        }

        $jadwal = (new MasjidData())->jadwalSholatHariIni($this->masjid);
        if ($jadwal === null) {
            // 200 dengan data null + keterangan: bukan galat server, hanya
            // jadwal belum tersedia (koordinat masjid belum diisi).
            return $this->sukses(null, 'Jadwal belum tersedia. Lengkapi koordinat masjid.');
        }

        return $this->sukses(['tanggal' => date('Y-m-d'), 'waktu' => $jadwal]);
    }

    public function donasi()
    {
        if ($resp = $this->guard()) {
            return $resp;
        }

        $limit = (int) ($this->request->getGet('limit') ?: 10);

        return $this->sukses((new MasjidData())->donasiTerbaru($this->masjid, $limit));
    }

    public function profil()
    {
        if ($resp = $this->guard()) {
            return $resp;
        }

        // Hanya kolom publik. JANGAN pernah membocorkan token/kunci di sini.
        return $this->sukses([
            'nama'     => $this->masjid['name'],
            'username' => $this->masjid['username'],
            'alamat'   => $this->masjid['address'] ?? null,
            'telepon'  => $this->masjid['phone'] ?? null,
            'timezone' => $this->masjid['timezone'] ?? null,
        ]);
    }

    // ── Autentikasi ──────────────────────────────────────────────────────

    /**
     * Mengisi $this->masjid dari token, atau mengembalikan respons 401 bila
     * gagal. Pola: `if ($resp = $this->guard()) return $resp;` di tiap endpoint.
     */
    private function guard()
    {
        $header = $this->request->getHeaderLine('Authorization');
        if (!preg_match('/^Bearer\s+(.+)$/i', trim($header), $m)) {
            return $this->galat(401, 'Sertakan header Authorization: Bearer <token>.');
        }
        $token = trim($m[1]);
        if ($token === '') {
            return $this->galat(401, 'Token kosong.');
        }

        $masjid = (new MasjidModel())->where('api_token', $token)->first();
        if (!$masjid) {
            return $this->galat(401, 'Token API tidak sah atau API tidak aktif untuk masjid ini.');
        }

        $this->masjid = $masjid;

        return null;
    }

    // ── Pembungkus JSON ──────────────────────────────────────────────────

    private function sukses($data, ?string $pesan = null)
    {
        $body = ['status' => 'success', 'data' => $data];
        if ($pesan !== null) {
            $body['message'] = $pesan;
        }

        return $this->response->setJSON($body);
    }

    private function galat(int $code, string $pesan)
    {
        return $this->response->setStatusCode($code)
            ->setJSON(['status' => 'error', 'message' => $pesan]);
    }
}
