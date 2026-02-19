<header class="sticky top-0 z-50 w-full border-b border-[#dbe6e1] dark:border-[#1e3a2f] bg-white/80 dark:bg-background-dark/80 backdrop-blur-md">
    <div class="max-w-[1200px] mx-auto px-6 h-16 flex items-center justify-between">
        <a href="<?= base_url($masjid['username']) ?>" class="flex items-center gap-2">
            <?php if (!empty($masjid['logo'])): ?>
                <img src="<?= $storage->url($masjid['logo']) ?>" alt="Logo <?= esc($masjid['name']) ?>" class="size-8 rounded-full object-contain bg-white border border-primary/20">
            <?php elseif (!empty($masjid['foto_utama'])): ?>
                <img src="<?= $storage->url($masjid['foto_utama']) ?>" alt="Logo <?= esc($masjid['name']) ?>" class="size-8 rounded-full object-cover border border-primary/20">
            <?php else: ?>
                <div class="size-8 bg-primary rounded-full flex items-center justify-center text-white">
                    <span class="material-symbols-outlined text-sm">mosque</span>
                </div>
            <?php endif; ?>
            <span class="font-bold text-lg tracking-tight hidden sm:block"><?= esc($masjid['name']) ?></span>
        </a>
        <nav class="hidden md:flex items-center gap-8">
            <a class="text-sm font-semibold hover:text-primary transition-colors" href="<?= base_url($masjid['username']) ?>">Beranda</a>
            <?php if ($masjid['menu_berita'] ?? 1): ?>
                <a class="text-sm font-semibold hover:text-primary transition-colors" href="#berita">Berita & Kegiatan</a>
            <?php endif; ?>
            <?php if ($masjid['menu_program'] ?? 1): ?>
                <a class="text-sm font-semibold hover:text-primary transition-colors" href="#program">Program</a>
            <?php endif; ?>
            <?php if ($masjid['menu_laporan'] ?? 1): ?>
                <a class="text-sm font-semibold hover:text-primary transition-colors" href="#laporan">Laporan</a>
            <?php endif; ?>
            <?php if ($masjid['menu_kontak'] ?? 1): ?>
                <a class="text-sm font-semibold hover:text-primary transition-colors" href="#kontak">Kontak</a>
            <?php endif; ?>
        </nav>
        <div class="flex items-center gap-4">
            <a href="<?= base_url('login') ?>" class="hidden sm:block text-xs font-bold text-[#608a7e] hover:text-primary uppercase tracking-wider">Portal Admin</a>
            <?php if ($masjid['action_button_active'] ?? 1): ?>
            <a href="<?= esc($masjid['action_button_url'] ?? '#donasi') ?>" class="bg-primary text-white px-5 py-2 rounded-lg text-sm font-bold hover:bg-emerald-900 transition-all shadow-sm">
                <?= esc($masjid['action_button_text'] ?? 'Donasi') ?>
            </a>
            <?php endif; ?>
        </div>
    </div>
</header>
