<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\MasjidModel;
use App\Libraries\MasjidData;

/**
 * REST API per masjid untuk integrasi umum (bukan agen AI; untuk itu ada MCP di
 * Api\Mcp). Mendukung BACA dan TULIS (create/update/delete).
 *
 * KEAMANAN MULTI-TENANT
 * Autentikasi token Bearer -> masjid.api_token (unik) -> tepat SATU masjid.
 * Tidak ada endpoint yang menerima masjid_id dari pemanggil; masjid selalu
 * diturunkan dari token. Baca lewat App\Libraries\MasjidData dan tulis lewat
 * App\Libraries\MasjidWriter — keduanya dipakai bersama MCP, jadi aturan
 * keamanan dan angka yang disajikan kedua antarmuka selalu sama.
 *
 * SETIAP PERUBAHAN TERCATAT di api_audit_logs (berhasil maupun gagal), karena
 * perubahan lewat API tidak meninggalkan jejak seperti aksi lewat dashboard.
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

    // ── Tulis data (create / update / delete) ────────────────────────────
    //
    // Seluruh penjagaan ada di App\Libraries\MasjidWriter: masjid selalu dari
    // token, ubah/hapus wajib milik sendiri, relasi kategori diperiksa, dan
    // SEMUA percobaan (berhasil/gagal) tercatat di audit log.

    public function buat($entitas = null)
    {
        if ($resp = $this->guard()) {
            return $resp;
        }

        $hasil = $this->writer()->buat((string) $entitas, $this->bodyData());

        return $hasil['ok']
            ? $this->response->setStatusCode(201)->setJSON(['status' => 'success', 'data' => ['id' => $hasil['id']]])
            : $this->galat(422, $hasil['error']);
    }

    public function ubah($entitas = null, $id = null)
    {
        if ($resp = $this->guard()) {
            return $resp;
        }

        $hasil = $this->writer()->ubah((string) $entitas, (int) $id, $this->bodyData());

        return $hasil['ok']
            ? $this->sukses(['id' => (int) $id])
            : $this->galat(422, $hasil['error']);
    }

    public function hapus($entitas = null, $id = null)
    {
        if ($resp = $this->guard()) {
            return $resp;
        }

        $hasil = $this->writer()->hapus((string) $entitas, (int) $id);

        return $hasil['ok']
            ? $this->sukses(['id' => (int) $id, 'dihapus' => true])
            : $this->galat(422, $hasil['error']);
    }

    private function writer(): \App\Libraries\MasjidWriter
    {
        return new \App\Libraries\MasjidWriter($this->masjid, 'api', $this->request->getIPAddress());
    }

    /**
     * Isi permintaan: menerima JSON maupun form biasa, supaya klien bebas
     * memilih. PUT/PATCH lewat form tidak terisi otomatis di PHP, jadi JSON
     * didahulukan.
     */
    private function bodyData(): array
    {
        $json = $this->request->getJSON(true);
        if (is_array($json) && $json !== []) {
            return $json;
        }

        $post = $this->request->getPost();
        if (is_array($post) && $post !== []) {
            return $post;
        }

        parse_str((string) $this->request->getBody(), $raw);

        return is_array($raw) ? $raw : [];
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
