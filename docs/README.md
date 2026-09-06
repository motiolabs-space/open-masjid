# Dokumentasi Open Masjid

Catatan per modul dan panduan operasional. Untuk gambaran produk secara
keseluruhan — modul yang sudah ada, yang belum, dan urutan pengerjaannya — mulai
dari [analisis-produk-roadmap.md](analisis-produk-roadmap.md).

## Peta jalan & rilis

| Dokumen | Isi |
|---------|-----|
| [analisis-produk-roadmap.md](analisis-produk-roadmap.md) | Inventaris modul, benchmark platform sejenis, roadmap Tahap 1–4, catatan keamanan |
| [go-live-checklist.md](go-live-checklist.md) | Yang wajib beres sebelum dipakai sungguhan |

## Modul

| Dokumen | Isi |
|---------|-----|
| [transparansi-donasi.md](transparansi-donasi.md) | Kwitansi otomatis, Dinding Transparansi, laporan bulanan, QRIS, donasi rutin |
| [zakat.md](zakat.md) | Kalkulator zakat, `zakat_type` pada donasi, 8 asnaf pada mustahik, laporan zakat |
| [program-kegiatan.md](program-kegiatan.md) | Kampanye donasi, RSVP + absensi, laporan dampak, live streaming |
| [relawan.md](relawan.md) | Registry relawan, poin partisipasi, sertifikat |
| [jelajah-direktori.md](jelajah-direktori.md) | Direktori publik lintas masjid + peta |
| [pwa-web-push.md](pwa-web-push.md) | Aplikasi installable & notifikasi (VAPID self-hosted) |

## Broadcast & AI

| Dokumen | Isi |
|---------|-----|
| [pengingat-terjadwal.md](pengingat-terjadwal.md) | Pengingat otomatis ke grup + **pemasangan cron** |
| [ringkas-obrolan-grup.md](ringkas-obrolan-grup.md) | Ringkasan obrolan grup |
| [model-ai-sumopod.md](model-ai-sumopod.md) | Konfigurasi model AI |

## Integrasi & infrastruktur

| Dokumen | Isi |
|---------|-----|
| [mcp-server.md](mcp-server.md) | Server MCP: tool, token, audit log |
| [DOCKER.md](DOCKER.md) | Menjalankan dengan Docker |

---

## Yang perlu cron atau kunci di server

Beberapa modul **tidak akan berjalan** hanya dengan mengaktifkannya di
dashboard — masing-masing menunggu sesuatu yang dipasang di server:

| Modul | Yang dibutuhkan |
|-------|-----------------|
| Pengingat siaran & donasi rutin | Cron `php spark broadcast:reminders` — [caranya](pengingat-terjadwal.md) |
| Web Push | Kunci VAPID di `.env` (`php spark push:vapid`) — [caranya](pwa-web-push.md) |
| Laporan harian | Cron `php spark report:daily` |
