<?= $this->extend('layout/main') ?>

<?= $this->section('extra_head') ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="bg-background-light dark:bg-background-dark min-h-screen">
    <!-- Hero + pencarian -->
    <div class="bg-primary/[0.04] border-b border-[#dbe6e3] dark:border-white/10">
        <div class="max-w-6xl mx-auto px-6 py-14 md:py-20">
            <h1 class="text-3xl md:text-5xl font-black text-[#111816] dark:text-white tracking-tight text-center">Jelajah Masjid</h1>
            <p class="text-[#608a7e] text-lg mt-3 text-center max-w-2xl mx-auto">Temukan masjid di sekitar Anda, lihat laporan amanahnya, dan salurkan donasi — di mana pun mereka berada.</p>

            <form method="get" class="mt-8 max-w-3xl mx-auto flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#608a7e]">search</span>
                    <input type="text" name="q" value="<?= esc($filter['q'], 'attr') ?>" placeholder="Cari nama masjid atau kota…"
                           class="w-full pl-12 pr-4 py-3.5 rounded-2xl border border-[#dbe6e3] dark:bg-white/5 dark:border-white/10 font-medium focus:ring-2 focus:ring-primary focus:border-primary">
                </div>
                <select name="provinsi" class="px-4 py-3.5 rounded-2xl border border-[#dbe6e3] dark:bg-white/5 dark:border-white/10 font-medium">
                    <option value="">Semua Provinsi</option>
                    <?php foreach ($provinsiList as $p): ?>
                        <option value="<?= esc($p['provinsi'], 'attr') ?>" <?= $filter['provinsi'] === $p['provinsi'] ? 'selected' : '' ?>><?= esc($p['provinsi']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="px-7 py-3.5 bg-primary text-white rounded-2xl font-bold hover:-translate-y-0.5 transition-all">Cari</button>
            </form>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-6 py-10">
        <?php if (! empty($pins)): ?>
            <div id="petaMasjid" class="w-full h-[320px] rounded-3xl overflow-hidden border border-[#dbe6e3] dark:border-white/10 mb-10 z-0"></div>
        <?php endif; ?>

        <p class="text-sm text-[#608a7e] mb-6"><strong class="text-[#111816] dark:text-white"><?= $total ?></strong> masjid ditemukan<?= $filter['q'] !== '' ? ' untuk "' . esc($filter['q']) . '"' : '' ?>.</p>

        <?php if (empty($masjids)): ?>
            <div class="text-center py-20">
                <span class="material-symbols-outlined text-5xl text-[#608a7e]/40 mb-3 block">search_off</span>
                <p class="text-[#608a7e]">Tidak ada masjid yang cocok. Coba kata kunci atau provinsi lain.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($masjids as $m): ?>
                    <div class="bg-white dark:bg-white/5 rounded-3xl border border-[#dbe6e3] dark:border-white/10 overflow-hidden hover:shadow-xl transition-all group flex flex-col">
                        <div class="h-32 bg-primary/10 relative overflow-hidden">
                            <?php if (! empty($m['foto_utama'])): ?>
                                <img src="<?= esc($storage->url($m['foto_utama']), 'attr') ?>" class="w-full h-full object-cover" loading="lazy" alt="">
                            <?php endif; ?>
                            <?php if (! empty($m['logo'])): ?>
                                <img src="<?= esc($storage->url($m['logo']), 'attr') ?>" class="absolute -bottom-6 left-5 size-16 rounded-2xl object-cover border-4 border-white dark:border-slate-900 bg-white" alt="">
                            <?php endif; ?>
                        </div>
                        <div class="p-5 pt-8 flex-1 flex flex-col">
                            <h3 class="font-black text-lg text-[#111816] dark:text-white leading-tight"><?= esc($m['name']) ?></h3>
                            <?php if (! empty($m['kabupaten']) || ! empty($m['provinsi'])): ?>
                                <p class="text-xs text-[#608a7e] mt-1 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">location_on</span>
                                    <?= esc(trim(($m['kabupaten'] ?? '') . ($m['provinsi'] ? ', ' . $m['provinsi'] : ''), ', ')) ?>
                                </p>
                            <?php endif; ?>
                            <div class="mt-auto pt-4 flex gap-2">
                                <a href="<?= base_url($m['username']) ?>" class="flex-1 text-center py-2.5 rounded-xl border border-primary text-primary font-bold text-sm hover:bg-primary hover:text-white transition-all">Kunjungi</a>
                                <a href="<?= base_url('donation/' . $m['username'] . '/form') ?>" class="flex-1 text-center py-2.5 rounded-xl bg-primary text-white font-bold text-sm hover:-translate-y-0.5 transition-all">Donasi</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($pager->getPageCount() > 1): ?>
                <?php
                    // Penyaring dipertahankan saat berpindah halaman, jika tidak
                    // halaman 2 akan mengembalikan seluruh masjid.
                    $tautanHalaman = static function (int $n) use ($filter): string {
                        $params = array_filter([
                            'q'        => $filter['q'],
                            'provinsi' => $filter['provinsi'],
                            'page'     => $n > 1 ? $n : null,
                        ], static fn ($v) => $v !== null && $v !== '');

                        return base_url('jelajah') . ($params ? '?' . http_build_query($params) : '');
                    };
                    $hal    = $pager->getCurrentPage();
                    $jumlah = $pager->getPageCount();
                ?>
                <nav class="mt-10 flex items-center justify-center gap-3" aria-label="Navigasi halaman">
                    <?php if ($hal > 1): ?>
                        <a href="<?= esc($tautanHalaman($hal - 1), 'attr') ?>" rel="prev"
                           class="px-5 py-2.5 rounded-xl border border-[#dbe6e3] dark:border-white/10 font-bold text-sm hover:border-primary hover:text-primary transition-all">Sebelumnya</a>
                    <?php endif; ?>
                    <span class="text-sm text-[#608a7e]">Halaman <strong class="text-[#111816] dark:text-white"><?= $hal ?></strong> dari <?= $jumlah ?></span>
                    <?php if ($hal < $jumlah): ?>
                        <a href="<?= esc($tautanHalaman($hal + 1), 'attr') ?>" rel="next"
                           class="px-5 py-2.5 rounded-xl border border-[#dbe6e3] dark:border-white/10 font-bold text-sm hover:border-primary hover:text-primary transition-all">Berikutnya</a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php if (! empty($pins)): ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script>
    (function () {
        // Bendera HEX: nama masjid diisi bebas saat pendaftaran, sehingga sebuah
        // nama berisi "</script>" bisa memutus blok skrip ini bila tak dikodekan.
        const pins = <?= json_encode($pins, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
        const map = L.map('petaMasjid', { scrollWheelZoom: false });
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap', maxZoom: 18,
        }).addTo(map);
        const grup = [];
        pins.forEach(function (p) {
            const mk = L.marker([p.lat, p.lng]).addTo(map);

            // Isi popup dibangun sebagai simpul DOM, bukan rangkaian string HTML:
            // nama & kota masjid adalah masukan pengurus, jadi harus masuk sebagai
            // TEKS (textContent) agar markup di dalamnya tak pernah dieksekusi.
            const isi = document.createElement('div');
            const judul = document.createElement('strong');
            judul.textContent = p.nama;
            isi.appendChild(judul);
            if (p.kota) {
                isi.appendChild(document.createElement('br'));
                isi.appendChild(document.createTextNode(p.kota));
            }
            isi.appendChild(document.createElement('br'));
            const tautan = document.createElement('a');
            tautan.href = p.url;
            tautan.textContent = 'Kunjungi »';
            isi.appendChild(tautan);
            mk.bindPopup(isi);

            grup.push([p.lat, p.lng]);
        });
        if (grup.length === 1) { map.setView(grup[0], 14); }
        else { map.fitBounds(grup, { padding: [40, 40] }); }
    })();
</script>
<?php endif; ?>
<?= $this->endSection() ?>
