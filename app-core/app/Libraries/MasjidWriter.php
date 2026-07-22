<?php

namespace App\Libraries;

use App\Models\ApiAuditLogModel;
use App\Models\MasjidFinanceTransactionModel;
use App\Models\MasjidFinanceCategoryModel;
use App\Models\MasjidNewsModel;
use App\Models\MasjidNewsCategoryModel;
use App\Models\MasjidProgramModel;
use App\Models\MasjidProgramCategoryModel;

/**
 * Satu-satunya jalan menulis data lewat API / MCP.
 *
 * Dipakai bersama REST (Api\RestApi) dan agen AI (Api\Mcp) supaya aturan
 * keamanan hanya ditulis SEKALI dan tidak mungkin berbeda antar antarmuka:
 *
 *  1. MASJID SELALU DARI TOKEN. Konstruktor menerima baris masjid yang sudah
 *     diverifikasi pemanggil dari token; tidak ada method di sini yang menerima
 *     masjid_id dari input.
 *  2. UBAH/HAPUS WAJIB MILIK SENDIRI. Setiap update/delete memuat dulu barisnya
 *     dengan filter masjid_id — id milik masjid lain berhenti di sini.
 *  3. RELASI IKUT DIPERIKSA. category_id yang dikirim harus milik masjid ini
 *     (kalau tidak, data masjid ini bisa ditempelkan ke kategori masjid lain).
 *  4. SEMUA DICATAT. Berhasil maupun gagal masuk api_audit_logs — percobaan yang
 *     ditolak justru sinyal terpenting saat menyelidiki token bocor.
 *
 * Kembalian seragam: ['ok'=>bool, 'id'=>?int, 'error'=>?string]
 */
class MasjidWriter
{
    /** Entitas yang boleh ditulis lewat API/MCP. */
    public const ENTITAS = ['transaksi', 'berita', 'program'];

    private array $masjid;
    private string $source; // 'api' | 'mcp'
    private ?string $ip;
    private ApiAuditLogModel $audit;

    public function __construct(array $masjid, string $source, ?string $ip = null)
    {
        $this->masjid = $masjid;
        $this->source = $source === 'mcp' ? 'mcp' : 'api';
        $this->ip     = $ip;
        $this->audit  = new ApiAuditLogModel();
    }

    // ── Operasi publik ───────────────────────────────────────────────────

    public function buat(string $entitas, array $data): array
    {
        if (!in_array($entitas, self::ENTITAS, true)) {
            return $this->tolak('create', $entitas, null, $data, 'Jenis data tidak dikenal: ' . $entitas);
        }

        return match ($entitas) {
            'transaksi' => $this->buatTransaksi($data),
            'berita'    => $this->buatBerita($data),
            'program'   => $this->buatProgram($data),
        };
    }

    public function ubah(string $entitas, int $id, array $data): array
    {
        if (!in_array($entitas, self::ENTITAS, true)) {
            return $this->tolak('update', $entitas, $id, $data, 'Jenis data tidak dikenal: ' . $entitas);
        }

        // Pemeriksaan kepemilikan dilakukan SEBELUM apa pun disentuh.
        $lama = $this->milikSendiri($entitas, $id);
        if ($lama === null) {
            return $this->tolak('update', $entitas, $id, $data, 'Data tidak ditemukan pada masjid ini.');
        }

        return match ($entitas) {
            'transaksi' => $this->ubahTransaksi($id, $data),
            'berita'    => $this->ubahBerita($id, $data, $lama),
            'program'   => $this->ubahProgram($id, $data, $lama),
        };
    }

    public function hapus(string $entitas, int $id): array
    {
        if (!in_array($entitas, self::ENTITAS, true)) {
            return $this->tolak('delete', $entitas, $id, null, 'Jenis data tidak dikenal: ' . $entitas);
        }

        if ($this->milikSendiri($entitas, $id) === null) {
            return $this->tolak('delete', $entitas, $id, null, 'Data tidak ditemukan pada masjid ini.');
        }

        $this->model($entitas)->delete($id);

        return $this->terima('delete', $entitas, $id, null);
    }

    // ── Transaksi keuangan ───────────────────────────────────────────────

    private function buatTransaksi(array $d): array
    {
        $siap = $this->siapkanTransaksi($d);
        if (isset($siap['error'])) {
            return $this->tolak('create', 'transaksi', null, $d, $siap['error']);
        }

        $m = new MasjidFinanceTransactionModel();
        if (!$m->insert($siap['data'])) {
            return $this->tolak('create', 'transaksi', null, $d, implode(' ', $m->errors()));
        }

        return $this->terima('create', 'transaksi', (int) $m->getInsertID(), $d);
    }

    private function ubahTransaksi(int $id, array $d): array
    {
        $siap = $this->siapkanTransaksi($d, false);
        if (isset($siap['error'])) {
            return $this->tolak('update', 'transaksi', $id, $d, $siap['error']);
        }

        $m = new MasjidFinanceTransactionModel();
        if (!$m->update($id, $siap['data'])) {
            return $this->tolak('update', 'transaksi', $id, $d, implode(' ', $m->errors()));
        }

        return $this->terima('update', 'transaksi', $id, $d);
    }

    /**
     * @param bool $wajib true untuk create (field wajib harus ada).
     * @return array{data?:array, error?:string}
     */
    private function siapkanTransaksi(array $d, bool $wajib = true): array
    {
        $data = ['masjid_id' => $this->masjid['id']];

        if ($wajib || array_key_exists('tanggal', $d)) {
            $tgl = parse_tanggal((string) ($d['tanggal'] ?? ''));
            if ($tgl === null) {
                return ['error' => 'Tanggal tidak terbaca. Pakai format YYYY-MM-DD atau DD/MM/YYYY.'];
            }
            $data['date'] = $tgl;
        }

        if ($wajib || array_key_exists('jenis', $d)) {
            $jenis = strtolower((string) ($d['jenis'] ?? ''));
            if (!in_array($jenis, ['pemasukan', 'pengeluaran'], true)) {
                return ['error' => "Jenis harus 'pemasukan' atau 'pengeluaran'."];
            }
            $data['type'] = $jenis;
        }

        if ($wajib || array_key_exists('nominal', $d)) {
            $nominal = abs(parse_rupiah((string) ($d['nominal'] ?? '')));
            if ($nominal <= 0) {
                return ['error' => 'Nominal harus lebih besar dari 0.'];
            }
            $data['amount'] = $nominal;
        }

        // Kategori WAJIB milik masjid ini.
        if ($wajib || array_key_exists('kategori_id', $d)) {
            $katId = (int) ($d['kategori_id'] ?? 0);
            $kat = (new MasjidFinanceCategoryModel())
                ->where(['id' => $katId, 'masjid_id' => $this->masjid['id']])->first();
            if (!$kat) {
                return ['error' => 'Kategori tidak ditemukan pada masjid ini.'];
            }
            $data['category_id'] = $katId;
        }

        if (array_key_exists('keterangan', $d)) {
            $data['description'] = (string) $d['keterangan'];
        }

        return ['data' => $data];
    }

    // ── Berita ───────────────────────────────────────────────────────────

    private function buatBerita(array $d): array
    {
        $judul = trim((string) ($d['judul'] ?? ''));
        $isi   = trim((string) ($d['isi'] ?? ''));
        if ($judul === '' || $isi === '') {
            return $this->tolak('create', 'berita', null, $d, 'Judul dan isi berita wajib diisi.');
        }

        $kat = $this->kategoriSah('berita', $d);
        if (isset($kat['error'])) {
            return $this->tolak('create', 'berita', null, $d, $kat['error']);
        }

        $m = new MasjidNewsModel();
        $data = [
            'masjid_id'   => $this->masjid['id'],
            'category_id' => $kat['id'],
            'title'       => $judul,
            'slug'        => url_title($judul, '-', true) . '-' . substr(md5(uniqid()), 0, 6),
            'content'     => $isi,
            'status'      => ($d['status'] ?? 'published') === 'draft' ? 'draft' : 'published',
        ];

        if (!$m->insert($data)) {
            return $this->tolak('create', 'berita', null, $d, implode(' ', $m->errors()));
        }

        return $this->terima('create', 'berita', (int) $m->getInsertID(), $d);
    }

    private function ubahBerita(int $id, array $d, array $lama): array
    {
        $kat = $this->kategoriSah('berita', $d);
        if (isset($kat['error'])) {
            return $this->tolak('update', 'berita', $id, $d, $kat['error']);
        }

        $data = [];
        if (array_key_exists('judul', $d)) {
            $data['title'] = trim((string) $d['judul']);
        }
        if (array_key_exists('isi', $d)) {
            $data['content'] = trim((string) $d['isi']);
        }
        if (array_key_exists('status', $d)) {
            $data['status'] = $d['status'] === 'draft' ? 'draft' : 'published';
        }
        if (array_key_exists('kategori_id', $d)) {
            $data['category_id'] = $kat['id'];
        }
        // Slug dipertahankan agar tautan lama tidak mati.
        $data['slug'] = $lama['slug'];

        $m = new MasjidNewsModel();
        if (!$m->update($id, $data)) {
            return $this->tolak('update', 'berita', $id, $d, implode(' ', $m->errors()));
        }

        return $this->terima('update', 'berita', $id, $d);
    }

    // ── Program ──────────────────────────────────────────────────────────

    private function buatProgram(array $d): array
    {
        $judul = trim((string) ($d['judul'] ?? ''));
        $desk  = trim((string) ($d['deskripsi'] ?? ''));
        $mulai = parse_tanggal((string) ($d['tanggal_mulai'] ?? ''));
        if ($judul === '' || $desk === '' || $mulai === null) {
            return $this->tolak('create', 'program', null, $d,
                'Judul, deskripsi, dan tanggal_mulai wajib diisi (tanggal YYYY-MM-DD).');
        }

        $kat = $this->kategoriSah('program', $d);
        if (isset($kat['error'])) {
            return $this->tolak('create', 'program', null, $d, $kat['error']);
        }

        $m = new MasjidProgramModel();
        $data = [
            'masjid_id'   => $this->masjid['id'],
            'category_id' => $kat['id'],
            'title'       => $judul,
            'slug'        => url_title($judul, '-', true) . '-' . substr(md5(uniqid()), 0, 6),
            'description' => $desk,
            'date_start'  => $mulai,
            'location'    => (string) ($d['lokasi'] ?? ''),
            'status'      => ($d['status'] ?? 'published') === 'draft' ? 'draft' : 'published',
        ];
        if (isset($d['target_donasi'])) {
            $data['target_donation'] = abs(parse_rupiah((string) $d['target_donasi']));
        }

        if (!$m->insert($data)) {
            return $this->tolak('create', 'program', null, $d, implode(' ', $m->errors()));
        }

        return $this->terima('create', 'program', (int) $m->getInsertID(), $d);
    }

    private function ubahProgram(int $id, array $d, array $lama): array
    {
        $kat = $this->kategoriSah('program', $d);
        if (isset($kat['error'])) {
            return $this->tolak('update', 'program', $id, $d, $kat['error']);
        }

        $data = ['slug' => $lama['slug']];
        if (array_key_exists('judul', $d))     $data['title'] = trim((string) $d['judul']);
        if (array_key_exists('deskripsi', $d)) $data['description'] = trim((string) $d['deskripsi']);
        if (array_key_exists('lokasi', $d))    $data['location'] = (string) $d['lokasi'];
        if (array_key_exists('status', $d))    $data['status'] = $d['status'] === 'draft' ? 'draft' : 'published';
        if (array_key_exists('kategori_id', $d)) $data['category_id'] = $kat['id'];
        if (array_key_exists('target_donasi', $d)) $data['target_donation'] = abs(parse_rupiah((string) $d['target_donasi']));
        if (array_key_exists('tanggal_mulai', $d)) {
            $mulai = parse_tanggal((string) $d['tanggal_mulai']);
            if ($mulai === null) {
                return $this->tolak('update', 'program', $id, $d, 'tanggal_mulai tidak terbaca.');
            }
            $data['date_start'] = $mulai;
        }

        $m = new MasjidProgramModel();
        if (!$m->update($id, $data)) {
            return $this->tolak('update', 'program', $id, $d, implode(' ', $m->errors()));
        }

        return $this->terima('update', 'program', $id, $d);
    }

    // ── Penolong ─────────────────────────────────────────────────────────

    /**
     * kategori_id opsional; bila diisi WAJIB milik masjid ini.
     *
     * @return array{id?:?int, error?:string}
     */
    private function kategoriSah(string $entitas, array $d): array
    {
        if (!array_key_exists('kategori_id', $d) || $d['kategori_id'] === null || $d['kategori_id'] === '') {
            return ['id' => null];
        }

        $model = $entitas === 'berita' ? new MasjidNewsCategoryModel() : new MasjidProgramCategoryModel();
        $kat = $model->where(['id' => (int) $d['kategori_id'], 'masjid_id' => $this->masjid['id']])->first();

        return $kat ? ['id' => (int) $d['kategori_id']] : ['error' => 'Kategori tidak ditemukan pada masjid ini.'];
    }

    /** Baris milik masjid ini, atau null. Inti penjagaan ubah/hapus. */
    private function milikSendiri(string $entitas, int $id): ?array
    {
        return $this->model($entitas)
            ->where(['id' => $id, 'masjid_id' => $this->masjid['id']])
            ->first();
    }

    private function model(string $entitas)
    {
        return match ($entitas) {
            'transaksi' => new MasjidFinanceTransactionModel(),
            'berita'    => new MasjidNewsModel(),
            'program'   => new MasjidProgramModel(),
        };
    }

    private function terima(string $aksi, string $entitas, ?int $id, ?array $payload): array
    {
        $this->audit->catat([
            'masjid_id' => $this->masjid['id'], 'source' => $this->source,
            'action' => $aksi, 'entity' => $entitas, 'entity_id' => $id,
            'payload' => $payload, 'status' => 'success', 'ip' => $this->ip,
        ]);

        return ['ok' => true, 'id' => $id, 'error' => null];
    }

    private function tolak(string $aksi, string $entitas, ?int $id, ?array $payload, string $pesan): array
    {
        $this->audit->catat([
            'masjid_id' => $this->masjid['id'], 'source' => $this->source,
            'action' => $aksi, 'entity' => $entitas, 'entity_id' => $id,
            'payload' => $payload, 'status' => 'failed', 'message' => $pesan, 'ip' => $this->ip,
        ]);

        return ['ok' => false, 'id' => null, 'error' => $pesan];
    }
}
