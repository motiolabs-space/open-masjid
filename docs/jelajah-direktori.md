# Jelajah Masjid (direktori publik)

`/jelajah` — direktori lintas masjid: menemukan masjid, melihat laporan
amanahnya, dan berdonasi ke mana pun, tanpa harus tahu alamat halamannya lebih
dulu. Ditautkan dari navbar publik.

---

## Isi halaman

- **Pencarian** nama / username / kabupaten / kecamatan.
- **Penyaring provinsi** — daftarnya diambil dari provinsi yang benar-benar
  terisi **pada masjid aktif saja**; provinsi milik masjid non-aktif akan selalu
  memberi 0 hasil kalau ikut ditawarkan.
- **Peta** (Leaflet + OpenStreetMap) berisi masjid yang punya koordinat.
- **Kartu masjid**: foto/logo, lokasi, tombol **Kunjungi** + **Donasi**.

Hanya masjid berstatus `active` yang muncul.

## Ketahanan terhadap data kosong

Data masjid di lapangan tidak pernah lengkap, jadi halaman ini dirancang tetap
berguna dengan data seadanya:

- Masjid **tanpa koordinat** tetap muncul di daftar, hanya tak muncul di peta.
- Bila **tak ada satu pun** titik, peta disembunyikan seluruhnya — bukan
  ditampilkan kosong.
- Koordinat `0,0` diperlakukan sebagai **belum diisi**, bukan sebagai lokasi.
  Kalau diloloskan, penandanya jatuh di Teluk Guinea dan `fitBounds()` terpaksa
  menampilkan separuh dunia — masjid yang koordinatnya benar ikut tak terbaca.
  Koordinat di luar rentang (lintang > 90, bujur > 180) diperlakukan sama.
- Tak ada hasil → empty-state ramah, bukan halaman kosong.

## Paginasi

24 kartu per halaman. Penyaring `q` dan `provinsi` **dipertahankan** saat
berpindah halaman — tanpa itu halaman 2 akan mengembalikan seluruh masjid.

Penanda peta diambil dari **halaman yang sedang tampil saja**, sehingga peta
selalu menggambarkan daftar di bawahnya.

## Catatan keamanan

Nama masjid adalah isian bebas saat pendaftaran (validasi hanya
`required|min_length[3]`, tanpa penyaringan HTML) dan halaman ini menampilkannya
ke publik. Karena itu:

- Isi popup peta dibangun sebagai **simpul DOM** (`textContent`), bukan rangkaian
  string HTML — `bindPopup` dengan string akan mengeksekusi markup di dalam nama.
- Data pin di-`json_encode` dengan `JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS |
  JSON_HEX_QUOT`, agar nama berisi tag penutup skrip tak bisa memutus blok
  skripnya.
