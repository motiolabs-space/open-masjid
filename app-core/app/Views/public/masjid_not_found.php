<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<section class="min-h-[70vh] flex items-center justify-center px-6 py-20">
    <div class="max-w-xl w-full text-center">

        <!-- Ikon -->
        <div class="relative inline-flex items-center justify-center mb-8">
            <div class="absolute inset-0 bg-primary/10 rounded-full blur-2xl"></div>
            <div class="relative w-24 h-24 rounded-3xl bg-primary/10 border border-primary/20 flex items-center justify-center">
                <span class="material-symbols-outlined text-primary text-5xl">mosque</span>
            </div>
        </div>

        <p class="text-xs font-bold uppercase tracking-widest text-primary mb-3">Halaman Tidak Ditemukan</p>

        <h1 class="text-3xl md:text-4xl font-black text-[#121715] dark:text-white mb-4 tracking-tight">
            Masjid ini tidak ditemukan
        </h1>

        <!-- Alamat yang dicoba -->
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 mb-6">
            <span class="material-symbols-outlined text-slate-400 text-base">link</span>
            <code class="text-sm font-bold text-slate-600 dark:text-slate-300">masj.id/<?= esc($username) ?></code>
        </div>

        <p class="text-slate-500 dark:text-slate-400 mb-10 leading-relaxed">
            Alamat ini belum terdaftar di Masj.id. Mungkin ada salah ketik pada tautan,
            atau masjid yang Anda cari memang belum bergabung.
        </p>

        <!-- Ajakan untuk pengurus -->
        <div class="bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-2xl p-6 md:p-8 text-left shadow-sm mb-6">
            <div class="flex items-start gap-4">
                <div class="w-11 h-11 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined">volunteer_activism</span>
                </div>
                <div class="min-w-0">
                    <h2 class="font-bold text-[#121715] dark:text-white mb-1">
                        Anda pengurus masjid ini?
                    </h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-5 leading-relaxed">
                        Daftarkan masjid Anda dan klaim alamat
                        <span class="font-bold text-slate-600 dark:text-slate-300">masj.id/<?= esc($username) ?></span>.
                        Gratis, dan langsung dapat halaman profil, laporan keuangan yang transparan,
                        serta penerimaan donasi online.
                    </p>
                    <a href="<?= base_url('register') ?>"
                       class="inline-flex items-center justify-center gap-2 bg-primary text-white px-5 py-2.5 rounded-lg text-sm font-bold shadow-lg shadow-primary/20 hover:bg-[#1f8e5f] transition-all">
                        <span class="material-symbols-outlined text-base">add_business</span>
                        Daftarkan Masjid Ini
                    </a>
                </div>
            </div>
        </div>

        <!-- Navigasi lain -->
        <div class="flex flex-wrap items-center justify-center gap-3">
            <a href="<?= base_url('/') ?>"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg border border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-300 text-sm font-bold hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                <span class="material-symbols-outlined text-base">home</span>
                Kembali ke Beranda
            </a>
            <a href="<?= base_url('login') ?>"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-slate-500 dark:text-slate-400 text-sm font-bold hover:text-primary transition-colors">
                Sudah punya akun? Masuk
            </a>
        </div>

    </div>
</section>

<?= $this->endSection() ?>
