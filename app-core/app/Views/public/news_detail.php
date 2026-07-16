<?= $this->extend('layout/masjid_public') ?>

<?= $this->section('content') ?>
<!-- Hero / Header Section -->
<section class="relative pt-32 pb-20 px-6 bg-white dark:bg-background-dark overflow-hidden">
    <div class="max-w-[1000px] mx-auto">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 mb-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
            <a href="<?= base_url($masjid['username']) ?>" class="text-[10px] font-bold uppercase tracking-wider text-[#608a7e] hover:text-primary transition-colors">Beranda</a>
            <span class="material-symbols-outlined text-xs text-gray-300">chevron_right</span>
            <span class="text-[10px] font-bold uppercase tracking-wider text-primary">Berita & Kegiatan</span>
        </nav>

        <h1 class="text-4xl md:text-6xl font-black mb-8 leading-tight animate-in fade-in slide-in-from-bottom-8 duration-1000">
            <?= esc($news['title']) ?>
        </h1>

        <div class="flex flex-wrap items-center gap-6 text-sm text-[#608a7e] animate-in fade-in duration-1000 delay-300">
            <div class="flex items-center gap-2">
                <div class="size-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-lg">mosque</span>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Diterbitkan Oleh</p>
                    <p class="font-bold text-slate-900 dark:text-white"><?= esc($masjid['name']) ?></p>
                </div>
            </div>
            <div class="h-8 w-px bg-gray-100 dark:bg-white/10 hidden sm:block"></div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider">
                <span class="material-symbols-outlined text-sm">calendar_today</span>
                <?= date('d M Y', strtotime($news['created_at'])) ?>
            </div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider px-3 py-1 bg-primary/5 text-primary rounded-full">
                <span class="material-symbols-outlined text-sm">category</span>
                <?= esc($news['category_name'] ?: 'Umum') ?>
            </div>
        </div>
    </div>
</section>

<!-- Content Section -->
<section class="pb-32 px-6">
    <div class="max-w-[1000px] mx-auto lg:grid lg:grid-cols-12 gap-16">
        <div class="lg:col-span-8">
            <!-- Main Featured Image / Video -->
            <?php // Dulu hanya YouTube yang dikenali di sini, dan tautan lain
                  // berakhir sebagai ikon play dicoret tanpa penjelasan apa pun.
                  // Pengenalannya kini di App\Libraries\Embed (dipanggil dari
                  // Home::newsDetail): YouTube, TikTok, Instagram, dan kartu
                  // tautan untuk sisanya. ?>
            <?php if (!empty($embed)): ?>
                <?= $this->include('partials/embed') ?>
            <?php elseif (!empty($news['thumbnail'])): ?>
                <div class="aspect-video rounded-[2.5rem] overflow-hidden bg-gray-100 shadow-2xl mb-12 animate-in fade-in zoom-in duration-1000">
                    <img src="<?= $storage->url($news['thumbnail']) ?>" class="size-full object-cover">
                </div>
            <?php endif; ?>

            <!-- Article Content -->
            <div class="article-content prose prose-lg dark:prose-invert max-w-none prose-headings:font-black prose-p:text-[#4a5568] dark:prose-p:text-gray-300 prose-a:text-primary animate-in fade-in slide-in-from-bottom-8 duration-1000">
                <?= $news['content'] ?>
            </div>

            <!-- Share Section -->
            <div class="mt-20 p-8 bg-background-light dark:bg-white/5 rounded-3xl border border-[#dbe6e1] dark:border-white/10 flex flex-col sm:flex-row items-center justify-between gap-6">
                <div>
                    <h4 class="font-bold mb-1">Bagikan Kabar Ini</h4>
                    <p class="text-sm text-[#608a7e]">Mari sebarkan informasi kebaikan ke jamaah lainnya.</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="https://wa.me/?text=<?= urlencode($news['title'] . ' ' . current_url()) ?>" target="_blank" class="size-12 rounded-xl bg-[#25D366] text-white flex items-center justify-center hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined">share</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-4 mt-20 lg:mt-0 space-y-12">
            <!-- Sidebar Mosque Info -->
            <div class="p-8 bg-white dark:bg-white/5 rounded-[2.5rem] border border-[#dbe6e1] dark:border-white/10 sticky top-28">
                <div class="flex items-center gap-4 mb-8">
                    <?php if (!empty($masjid['foto_utama'])): ?>
                        <img src="<?= $storage->url($masjid['foto_utama']) ?>" class="size-16 rounded-2xl object-cover border border-primary/20">
                    <?php else: ?>
                        <div class="size-16 bg-primary rounded-2xl flex items-center justify-center text-white">
                            <span class="material-symbols-outlined text-2xl">mosque</span>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h4 class="font-bold leading-tight"><?= esc($masjid['name']) ?></h4>
                        <p class="text-xs text-[#608a7e]"><?= esc($masjid['address']) ?></p>
                    </div>
                </div>
                <a href="<?= base_url($masjid['username']) ?>" class="w-full h-14 bg-primary text-white rounded-xl font-bold flex items-center justify-center gap-2 hover:bg-emerald-900 transition-all shadow-lg shadow-primary/20">
                    Kunjungi Profil
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
        </div>
    </div>
</section>

<style>
    .article-content iframe {
        width: 100%;
        aspect-ratio: 16/9;
        border-radius: 1.5rem;
        margin: 2rem 0;
    }
    .article-content img {
        border-radius: 1.5rem;
    }
</style>
<?= $this->endSection() ?>
