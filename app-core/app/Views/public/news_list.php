<?= $this->extend('layout/masjid_public') ?>

<?= $this->section('content') ?>
<!-- Hero Section -->
<section class="relative pt-32 pb-20 px-6 bg-white dark:bg-background-dark overflow-hidden">
    <div class="max-w-[1200px] mx-auto text-center">
        <h1 class="text-4xl md:text-6xl font-black mb-6 tracking-tight animate-in fade-in slide-in-from-bottom-8 duration-700">Warta & Kegiatan</h1>
        <p class="text-[#608a7e] max-w-2xl mx-auto text-lg animate-in fade-in duration-1000 delay-200">
            Ikuti berbagai informasi terbaru, dokumentasi kegiatan, dan pengumuman dari <?= esc($masjid['name']) ?>.
        </p>
    </div>
</section>

<!-- Main Content -->
<section class="pb-32 px-6">
    <div class="max-w-[1200px] mx-auto lg:grid lg:grid-cols-12 gap-12">
        <!-- Sidebar Filter -->
        <aside class="lg:col-span-3 mb-12 lg:mb-0">
            <div class="sticky top-28 space-y-8">
                <div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-[0.2em] mb-6">Kategori</h3>
                    <div class="flex flex-wrap lg:flex-col gap-2">
                        <a href="<?= base_url($masjid['username'] . '/berita') ?>" class="px-5 py-3 rounded-2xl text-sm font-bold transition-all <?= !$activeCat ? 'bg-primary text-white shadow-lg' : 'bg-white dark:bg-white/5 border border-[#dbe6e1] dark:border-white/10 hover:border-primary' ?>">
                            Semua Warta
                        </a>
                        <?php foreach ($categories as $cat): ?>
                            <a href="?category=<?= $cat['slug'] ?>" class="px-5 py-3 rounded-2xl text-sm font-bold transition-all <?= ($activeCat == $cat['slug']) ? 'bg-primary text-white shadow-lg' : 'bg-white dark:bg-white/5 border border-[#dbe6e1] dark:border-white/10 hover:border-primary' ?>">
                                <?= esc($cat['name']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="p-8 bg-primary rounded-[2.5rem] text-white relative overflow-hidden shadow-2xl shadow-primary/20 hidden lg:block">
                    <div class="relative z-10">
                        <h4 class="font-black mb-2">Ingin berkontribusi?</h4>
                        <p class="text-xs text-white/70 leading-relaxed mb-6">Punya informasi atau dokumentasi kegiatan masjid yang ingin dimuat?</p>
                        <a href="#kontak" class="inline-flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest bg-white text-primary px-4 py-2 rounded-lg hover:bg-emerald-50 transition-colors">Hubungi Kami</a>
                    </div>
                    <span class="material-symbols-outlined absolute -bottom-6 -right-6 text-7xl opacity-10">draw</span>
                </div>
            </div>
        </aside>

        <!-- News Grid -->
        <div class="lg:col-span-9">
            <?php if (empty($news)): ?>
                <div class="p-20 bg-white dark:bg-white/5 rounded-[3rem] border border-dashed border-[#dbe6e1] dark:border-white/10 text-center animate-in fade-in zoom-in duration-700">
                    <div class="size-20 bg-slate-50 dark:bg-white/5 rounded-full flex items-center justify-center text-slate-300 mx-auto mb-6">
                        <span class="material-symbols-outlined text-4xl">inventory_2</span>
                    </div>
                    <h4 class="text-xl font-bold mb-2">Belum ada berita</h4>
                    <p class="text-[#608a7e] text-sm">Belum ada warta yang dipublikasikan dalam kategori ini.</p>
                </div>
            <?php else: ?>
                <div class="grid md:grid-cols-2 gap-8">
                    <?php foreach ($news as $item): ?>
                        <a href="<?= base_url($masjid['username'] . '/berita/' . $item['slug']) ?>" class="group bg-white dark:bg-white/5 rounded-[2.5rem] overflow-hidden border border-[#e5e7eb] dark:border-white/10 hover:shadow-2xl transition-all duration-500 flex flex-col animate-in fade-in slide-in-from-bottom-8 duration-700">
                            <div class="aspect-[16/10] overflow-hidden relative">
                                <?php if (!empty($item['thumbnail'])): ?>
                                    <img src="<?= $storage->url($item['thumbnail']) ?>" class="size-full object-cover transition-transform duration-700 group-hover:scale-110">
                                <?php else: ?>
                                    <div class="size-full bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-300">
                                        <span class="material-symbols-outlined text-5xl">image</span>
                                    </div>
                                <?php endif; ?>
                                <div class="absolute top-6 left-6">
                                    <div class="px-4 py-2 bg-white/90 dark:bg-background-dark/90 backdrop-blur-md rounded-xl text-[10px] font-bold uppercase tracking-widest shadow-sm">
                                        <?= esc($item['category_name'] ?: 'Umum') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="p-8 flex-1 flex flex-col">
                                <div class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">
                                    <span class="material-symbols-outlined text-sm">calendar_today</span>
                                    <?= date('d M Y', strtotime($item['created_at'])) ?>
                                </div>
                                <h4 class="text-2xl font-black mb-6 group-hover:text-primary transition-colors line-clamp-2 leading-tight"><?= esc($item['title']) ?></h4>
                                <div class="mt-auto flex items-center gap-2 text-xs font-bold text-primary group-hover:translate-x-2 transition-transform">
                                    Baca Selengkapnya
                                    <span class="material-symbols-outlined text-sm">arrow_right_alt</span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
