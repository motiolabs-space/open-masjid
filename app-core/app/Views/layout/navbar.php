<header class="sticky top-0 z-50 w-full border-b border-[#dbe6e1] dark:border-[#1e3a2f] bg-white/80 dark:bg-background-dark/80 backdrop-blur-md">
    <div class="max-w-[1200px] mx-auto px-6 h-16 flex items-center justify-between">
        <a href="<?= base_url() ?>" class="flex items-center gap-2">
            <img src="<?= asset_url('logo.png') ?>" alt="Logo Masj.id" class="h-8">
        </a>
        <nav class="hidden md:flex items-center gap-8">
            <a class="text-sm font-medium hover:text-primary transition-colors" href="<?= base_url() ?>">Beranda</a>
            <a class="text-sm font-medium hover:text-primary transition-colors" href="<?= base_url('fitur') ?>">Fitur</a>
            <a class="text-sm font-medium hover:text-primary transition-colors" href="<?= base_url('kebaikan') ?>">Program Kebaikan</a>
            <a class="text-sm font-medium hover:text-primary transition-colors" href="<?= base_url('tentang') ?>">Tentang Kami</a>
        </nav>
        <div class="flex items-center gap-4">
            <a href="<?= base_url('login') ?>" class="hidden sm:block text-sm font-semibold hover:text-primary px-4 py-2">Masuk</a>
            <a href="<?= base_url('register') ?>" class="btn-primary">
                Daftar Gratis
            </a>
        </div>
    </div>
</header>
