<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<section class="bg-background-light dark:bg-background-dark">

    <!-- Pembuka -->
    <div class="bg-primary/[0.04] border-b border-[#dbe6e3] dark:border-white/10">
        <div class="max-w-4xl mx-auto px-6 py-16 md:py-24 text-center">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary/10 text-primary text-xs font-bold tracking-wide uppercase">
                <span class="material-symbols-outlined text-sm">auto_stories</span> Seri Tulisan
            </span>
            <h1 class="text-3xl md:text-5xl font-black text-[#111816] dark:text-white tracking-tight mt-5 leading-tight">
                Masjid yang Amanah,<br class="hidden md:block"> Tanpa Menambah Kerja Pengurus
            </h1>
            <p class="text-[#608a7e] text-lg mt-5 max-w-2xl mx-auto leading-relaxed">
                Lima tulisan berurutan untuk dibagikan pekan demi pekan. Bukan daftar
                fitur — melainkan hal-hal kecil yang berubah ketika sebuah masjid
                mulai terbuka: pertanyaan yang tak lagi perlu ditahan, sedekah yang
                selesai ceritanya, dan bendahara yang bisa pulang lebih awal.
            </p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-6 py-14 md:py-20">

        <!-- Cara pakai -->
        <div class="bg-white dark:bg-white/5 border border-[#dbe6e3] dark:border-white/10 rounded-3xl p-6 md:p-8 mb-16">
            <h2 class="font-black text-lg text-[#111816] dark:text-white flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">tips_and_updates</span> Cara memakai seri ini
            </h2>
            <div class="grid sm:grid-cols-3 gap-5 mt-5 text-sm">
                <div>
                    <p class="font-bold text-[#111816] dark:text-white mb-1">Satu pekan, satu tulisan</p>
                    <p class="text-[#608a7e] leading-relaxed">Urutannya sengaja: dari kepercayaan, lalu bukti, baru ajakan.</p>
                </div>
                <div>
                    <p class="font-bold text-[#111816] dark:text-white mb-1">Gambar sebagai penguat</p>
                    <p class="text-[#608a7e] leading-relaxed">Tangkapan layar membuat janji tulisan terasa nyata, bukan klaim.</p>
                </div>
                <div>
                    <p class="font-bold text-[#111816] dark:text-white mb-1">Ganti dengan cerita sendiri</p>
                    <p class="text-[#608a7e] leading-relaxed">Angka dan nama di bawah hanya contoh. Yang paling kuat selalu kisah masjid Anda.</p>
                </div>
            </div>
        </div>

        <?php foreach ($seri as $i => $t): ?>
            <article class="mb-20 last:mb-0 scroll-mt-24" id="tulisan-<?= $i + 1 ?>">

                <div class="flex items-center gap-3 mb-4">
                    <span class="size-10 rounded-2xl bg-primary text-white font-black flex items-center justify-center shrink-0"><?= $i + 1 ?></span>
                    <span class="text-xs font-bold uppercase tracking-wider text-[#608a7e]"><?= esc($t['label']) ?></span>
                </div>

                <h2 class="text-2xl md:text-4xl font-black text-[#111816] dark:text-white tracking-tight leading-tight">
                    <?= esc($t['judul']) ?>
                </h2>

                <p class="text-lg md:text-xl text-primary font-bold mt-4 leading-snug">
                    <?= esc($t['hook']) ?>
                </p>

                <div class="mt-5 space-y-4 text-[#4a5568] dark:text-gray-300 leading-relaxed">
                    <?php foreach ($t['isi'] as $p): ?>
                        <p><?= $p /* teks tetap, ditulis di controller */ ?></p>
                    <?php endforeach; ?>
                </div>

                <!-- Tangkapan layar -->
                <figure class="mt-8">
                    <div class="rounded-3xl overflow-hidden border border-[#dbe6e3] dark:border-white/10 shadow-lg bg-white">
                        <img src="<?= esc(asset_url('img/digital/' . $t['gambar'] . '.jpg'), 'attr') ?>"
                             alt="<?= esc($t['gambar_alt'], 'attr') ?>" loading="lazy" class="w-full">
                    </div>
                    <figcaption class="text-xs text-[#608a7e] mt-3 flex items-start gap-2">
                        <span class="material-symbols-outlined text-sm shrink-0">info</span>
                        <span>
                            <?= esc($t['gambar_alt']) ?> — <em>tampilan asli aplikasi, data pada gambar hanya contoh.</em>
                            <?php if (! empty($t['tautan'])): ?>
                                <a href="<?= esc(base_url($t['tautan']), 'attr') ?>" class="text-primary font-bold hover:underline">Lihat versi hidupnya &raquo;</a>
                            <?php endif; ?>
                        </span>
                    </figcaption>
                </figure>

                <!-- Penutup + tagar -->
                <div class="mt-8 bg-primary/[0.05] border border-primary/15 rounded-3xl p-6">
                    <p class="font-bold text-[#111816] dark:text-white leading-relaxed"><?= esc($t['penutup']) ?></p>
                    <p class="text-sm text-primary font-medium mt-3"><?= esc($t['tagar']) ?></p>
                </div>
            </article>
        <?php endforeach; ?>

        <!-- Penutup halaman -->
        <div class="mt-20 pt-12 border-t border-[#dbe6e3] dark:border-white/10 text-center">
            <h2 class="text-2xl md:text-3xl font-black text-[#111816] dark:text-white tracking-tight">
                Mulai dari satu laporan yang terbuka
            </h2>
            <p class="text-[#608a7e] mt-3 max-w-xl mx-auto leading-relaxed">
                Tidak perlu langsung sempurna. Satu bulan laporan yang bisa dilihat jamaah
                sudah cukup untuk mengubah cara mereka memandang masjidnya.
            </p>
            <div class="mt-7 flex flex-col sm:flex-row gap-3 justify-center">
                <a href="<?= base_url('register') ?>" class="px-7 py-3.5 bg-primary text-white rounded-2xl font-bold hover:-translate-y-0.5 transition-all">Daftarkan Masjid</a>
                <a href="<?= base_url('digital') ?>" class="px-7 py-3.5 border border-[#dbe6e3] dark:border-white/10 rounded-2xl font-bold hover:border-primary hover:text-primary transition-all">Lihat Halaman Contoh</a>
            </div>
            <p class="text-xs text-[#608a7e] mt-4">
                Halaman contoh berisi data peraga, bukan masjid sungguhan.
            </p>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
