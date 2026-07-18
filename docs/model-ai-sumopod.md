# Panduan Model AI (SumoPod)

Catatan pemilihan model untuk fitur AI Masj.id: perbandingan model, pembagian
tingkat **ringan/berat**, `max_tokens` minimum, dan biaya (konsumsi token).

> **Diukur:** 2026-07-18, lewat panggilan nyata ke SumoPod dengan prompt &
> `max_tokens` yang identik untuk semua model. Angka di sini hasil pengukuran,
> bukan estimasi. Daftar model SumoPod berubah dari waktu ke waktu — ukur ulang
> bila ragu (lihat "Cara mengambil daftar model" di bawah).

---

## Ringkasan keputusan

| | Tingkat **ringan** (harian) | Tingkat **berat** (penting) |
|---|---|---|
| **Dipakai untuk** | bot Telegram, kategorisasi CSV bank | skoring mustahik, audit & laporan keuangan |
| **Pilihan sekarang** | `gpt-4o-mini` | `claude-haiku-4-5` |
| **Alasan** | volume tinggi, murah, jejak token kecil | menyangkut uang & keadilan zakat; nalar lebih kuat |
| **Diatur di** | `.env` → `sumopod.model` | `.env` → `sumopod.modelBerat` |

Model **tidak** ditulis di dalam kode. Mengganti model satu tingkat = ubah satu
baris `.env` (lihat `app-core/env`), berlaku untuk semua fitur di tingkat itu.
Mekanismenya di `app-core/app/Libraries/SumoPodAI.php`.

---

## Perbandingan model (terukur)

Prompt uji: skoring kelayakan zakat, minta output JSON — mewakili beban kerja
nyata. Kolom **token** = total token satu panggilan (prompt + output).

| Model | Prompt tok | Total tok | Jalan di `max_tokens` kecil? | Kelas | Cocok untuk |
|---|---|---|---|---|---|
| `gpt-4o-mini` | 68 | **99** | ✅ ya | ringan, sangat murah | **ringan** (dipakai) |
| `gpt-4o` | 68 | **92** | ✅ ya | jejak kecil, kualitas lebih baik | berat-hemat |
| `claude-haiku-4-5` | 1436 | **1515** | ✅ ya | baseline tinggi, output stabil | **berat** (dipakai) |
| `claude-sonnet-4-6` | 1437 | **1503** | ✅ ya | setara haiku tokennya, nalar lebih kuat | berat+ |
| `deepseek-v4-flash` | 67 | **339** | ✅ ya | murah, agak boros output | ringan (alternatif) |
| `qwen3.6-flash` | 74 | **804** | ✅ ya | boros output untuk kelas "flash" | ringan (kurang ideal) |
| `kimi-k3` | ~2000 | **2264** | ❌ **tidak** — perlu ≥1500 | *thinking*, mahal token | berat, hanya bila max_tokens besar |
| `gpt-5-nano` | 67 | — | ❌ **tidak** — kosong bahkan di 1500 | *thinking* berat | **hindari** (butuh anggaran sangat besar) |
| `gemini/gemini-2.5-flash` | — | — | — | **404 / tak tersedia** | tidak bisa dipakai |
| `gemini/gemini-2.5-flash-lite` | — | — | — | **404 / 429** | tidak bisa dipakai |

### Tiga temuan yang perlu diingat

1. **Terdaftar ≠ bisa dipakai.** `gemini-2.5-flash` dan `-flash-lite` muncul di
   `/v1/models` tetapi menjawab **404 "NotFoundError"** saat dipanggil. Selalu
   uji panggilan nyata sebelum memakai model di produksi.

2. **Model *thinking* mengosongkan jawaban bila `max_tokens` kecil.** `kimi-k3`
   dan `gpt-5-nano` memakai token untuk menalar dulu sebelum keluar teks. Dengan
   `max_tokens` kecil, seluruh anggaran habis di penalaran → `finish_reason:
   length` → **jawaban kosong, token tetap terpakai**. Ini kegagalan yang
   diam-diam: HTTP 200 tetapi hasilnya nihil.

3. **Baseline token berbeda tajam antar keluarga model.** Untuk prompt yang
   SAMA: GPT ~68 token prompt, Claude ~1436, kimi ~2000. Jadi Claude & kimi
   membawa "ongkos tetap" token yang jauh lebih besar per panggilan pada
   SumoPod, terlepas dari panjang pertanyaan. Perhitungkan ini untuk fitur
   bervolume tinggi.

---

## `max_tokens` minimum yang perlu diatur

`max_tokens` per fitur ada di controller/pustaka. Model biasa aman dengan nilai
kecil; model *thinking* perlu ruang besar atau jawabannya kosong.

| Fitur | `max_tokens` sekarang | Aman untuk model biasa? | Aman untuk *thinking* (kimi/nano)? |
|---|---|---|---|
| Skoring mustahik (`Distribution` → `scoreMustahik`) | **100** | ✅ | ❌ perlu ≥1500 |
| Bot Telegram (`Api\Telegram`) | **150** | ✅ | ❌ perlu ≥1500 |
| Audit keuangan (`SumoPodAI::runFinancialAudit`) | 800 | ✅ | ⚠️ mepet, naikkan ke ≥1500 |
| Laporan keuangan (`FinanceAI::generateReport`) | 800 | ✅ | ⚠️ mepet, naikkan ke ≥1500 |
| Kategorisasi CSV (`FinanceAI::processCSV`) | 1500 | ✅ | ✅ |

**Aturan praktis:** memakai model *thinking* untuk tingkat **berat** berarti
`max_tokens` semua jalur berat harus dinaikkan ke **minimal 1500** lebih dulu —
kalau tidak, skoring mustahik akan menghasilkan skor kosong begitu
`sumopod.modelBerat` diganti ke `kimi-k3`. Model biasa (Claude, GPT) tidak
memerlukan perubahan ini.

---

## Biaya

**SumoPod tidak memberikan harga lewat API.** Endpoint `/v1/models` hanya
memuat `id`, `max_input_tokens`, `max_output_tokens` — tidak ada field harga,
dan `/v1/pricing`, `/v1/prices`, `/v1/usage` semuanya 404. Tarif rupiah per
token **harus dilihat di dashboard/dokumentasi SumoPod**.

Yang bisa diukur — dan yang sebenarnya menggerakkan biaya — adalah **konsumsi
token per panggilan**. Urutan dari yang paling hemat (berdasarkan tabel di atas):

```
gpt-4o (92)  ≈  gpt-4o-mini (99)  <  deepseek-v4-flash (339)  <  qwen3.6-flash (804)
   <  claude-sonnet-4-6 (1503)  ≈  claude-haiku-4-5 (1515)  <  kimi-k3 (2264)
```

Catatan biaya:
- **Biaya nyata = jumlah token × tarif per token model.** Model dengan token
  sedikit belum tentu termurah bila tarif per tokennya tinggi — cek tarif di
  dashboard, lalu kalikan dengan konsumsi terukur di atas.
- **Log pemakaian ada di aplikasi.** Superadmin › **Pemakaian AI** mencatat
  token nyata per model, per fitur, per masjid. Pakai itu untuk memantau biaya
  aktual setelah memilih model — jangan menebak.
- **`kimi-k3` bukan pilihan hemat** meski menarik: ~2.264 token/panggilan +
  butuh `max_tokens` besar. Untuk bot Telegram (harian, volume tinggi) ini
  kebalikan dari "murah".

---

## Cara mengambil daftar model

```bash
curl https://ai.sumopod.com/v1/models \
  -H "Authorization: Bearer sk-KUNCI-ANDA"
```

Balasan format OpenAI-kompatibel: `{"data":[{"id":"gpt-4o-mini", ...}, ...]}`.

## Cara menguji satu model (WAJIB sebelum dipakai di produksi)

```bash
curl https://ai.sumopod.com/v1/chat/completions \
  -H "Authorization: Bearer sk-KUNCI-ANDA" -H "Content-Type: application/json" \
  -d '{"model":"NAMA-MODEL","messages":[{"role":"user","content":"halo"}],"max_tokens":50}'
```

- Ada `"content"` berisi teks → model hidup.
- `"error"` / 404 → nama salah atau model tak tersedia (walau terdaftar).
- `content` kosong + `finish_reason: length` → model *thinking*, perlu
  `max_tokens` jauh lebih besar.

## Cara mengganti model

Di `app-core/.env` server (tak perlu sentuh kode, tak perlu restart):

```
sumopod.model      = 'gpt-4o-mini'        # tingkat ringan
sumopod.modelBerat = 'claude-haiku-4-5'   # tingkat berat
# Cadangan bila model utama gagal (mis. kuota habis), dipisah koma:
sumopod.fallbackModels = 'gpt-4o-mini, claude-haiku-4-5, gpt-4o'
```

> Format Gemini memakai awalan `gemini/` (mis. `gemini/gemini-2.5-flash`).
> Salah tulis nama model → SumoPod menolak → sistem jatuh ke rantai cadangan.
