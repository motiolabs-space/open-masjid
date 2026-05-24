<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 px-1">
    <div>
        <h2 class="text-2xl font-black text-slate-900 dark:text-white">Assalamu'alaikum, <?= esc(session()->get('user_name') ?? 'Jamaah') ?>!</h2>
        <p class="text-slate-500 text-sm">Selamat datang di pusat aktivitas kebaikan Anda.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="<?= base_url('dashboard/masjid-saya') ?>" class="flex items-center gap-2 px-5 py-2.5 rounded-xl border border-primary text-primary text-xs font-bold hover:bg-primary hover:text-white transition-all shadow-sm">
            <span class="material-symbols-outlined text-sm">search</span>
            Eksplorasi Masjid Lain
        </a>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
    <!-- Left Column: Feed Kegiatan -->
    <div class="xl:col-span-2 space-y-6">
        <div class="flex items-center justify-between px-1 mb-2">
            <h3 class="text-lg font-bold flex items-center gap-2 text-slate-800 dark:text-white">
                <span class="material-symbols-outlined text-primary">dynamic_feed</span>
                Kabar dari Masjid Anda
            </h3>
        </div>

        <?php if (empty($recentNews)): ?>
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-10 text-center flex flex-col items-center">
                <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center text-slate-400 mb-4">
                    <span class="material-symbols-outlined text-3xl">notifications_off</span>
                </div>
                <h4 class="font-bold text-slate-700 dark:text-slate-300 mb-1">Belum Ada Kabar Terbaru</h4>
                <p class="text-sm text-slate-500 max-w-sm">Mulai ikuti (follow) masjid di sekitar Anda untuk mendapatkan pembaruan kegiatan dan transparansi laporan.</p>
                <a href="#" class="mt-6 px-6 py-2.5 bg-primary text-white text-sm font-bold rounded-lg hover:bg-primary-dark transition-colors">Cari Masjid</a>
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php 
                $storage = new \App\Libraries\Storage();
                foreach ($recentNews as $news): 
                    $thumb = !empty($news['thumbnail']) ? $storage->url($news['thumbnail']) : asset_url('logo.png');
                ?>
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                    <div class="p-5 flex items-center gap-3 border-b border-slate-100 dark:border-slate-800">
                        <div class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center text-primary font-bold">
                            M
                        </div>
                        <div>
                            <p class="font-bold text-sm text-slate-900 dark:text-white">Masjid Baiturrahman</p>
                            <p class="text-[11px] text-slate-500"><?= date('d M Y - H:i', strtotime($news['created_at'])) ?></p>
                        </div>
                    </div>
                    <?php if (!empty($news['thumbnail'])): ?>
                    <div class="h-64 w-full bg-slate-100 relative">
                        <img src="<?= $thumb ?>" alt="Cover" class="w-full h-full object-cover">
                        <span class="absolute top-3 right-3 px-3 py-1 bg-white/90 text-primary text-[10px] font-bold rounded-full shadow-sm">
                            <?= esc($news['category_name'] ?? 'Pembaruan') ?>
                        </span>
                    </div>
                    <?php endif; ?>
                    <div class="p-6">
                        <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-2"><?= esc($news['title']) ?></h4>
                        <p class="text-slate-600 dark:text-slate-400 text-sm line-clamp-3 mb-4">
                            <?= strip_tags($news['content']) ?>
                        </p>
                        <a href="#" class="text-primary font-bold text-sm hover:underline flex items-center gap-1">
                            Baca Selengkapnya <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Right Column: Personal Stats & Widgets -->
    <div class="space-y-6">
        <!-- Dampak Saya -->
        <div class="bg-gradient-to-br from-primary to-emerald-800 p-6 rounded-2xl shadow-sm text-white relative overflow-hidden">
            <span class="material-symbols-outlined absolute -right-4 -bottom-4 text-[120px] text-white/10">volunteer_activism</span>
            <div class="relative z-10">
                <h4 class="font-bold text-sm text-emerald-100 mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined">analytics</span> Dampak Kebaikan Anda
                </h4>
                <div class="space-y-4">
                    <div>
                        <p class="text-emerald-100 text-xs mb-1">Total Infak / Sedekah</p>
                        <h3 class="text-3xl font-black">Rp 0</h3>
                    </div>
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-white/20">
                        <div>
                            <p class="text-emerald-100 text-xs mb-1">Program Diikuti</p>
                            <p class="text-lg font-bold">0</p>
                        </div>
                        <div>
                            <p class="text-emerald-100 text-xs mb-1">Aksi Kerelawanan</p>
                            <p class="text-lg font-bold">0</p>
                        </div>
                    </div>
                </div>
                <a href="#" class="mt-6 block text-center w-full py-2.5 bg-white/20 hover:bg-white/30 text-white rounded-xl text-xs font-bold transition-colors">
                    Lihat Riwayat Lengkap
                </a>
            </div>
        </div>

        <!-- Lanjutkan Belajar LMS -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
            <h4 class="font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-500">school</span> Lanjutkan Belajar
            </h4>
            <div class="p-4 bg-amber-50 dark:bg-amber-900/10 rounded-xl border border-amber-100 dark:border-amber-900/30">
                <h5 class="font-bold text-sm text-slate-900 dark:text-white mb-1">Fikih Muamalah Dasar</h5>
                <p class="text-xs text-slate-500 mb-3">Oleh: Masjid Jogokariyan</p>
                <div class="flex justify-between text-[10px] font-bold text-amber-600 mb-1">
                    <span>Progres: 40%</span>
                    <span>2/5 Materi</span>
                </div>
                <div class="w-full bg-amber-200 dark:bg-amber-900/50 rounded-full h-2 mb-4 overflow-hidden">
                    <div class="bg-amber-500 h-2 rounded-full" style="width: 40%"></div>
                </div>
                <a href="<?= base_url('dashboard/lms') ?>" class="block text-center w-full py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-xs font-bold transition-colors">
                    Lanjutkan Materi
                </a>
            </div>
        </div>

        <!-- Jadwal Kajian -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
            <h4 class="font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-blue-500">event_upcoming</span> Agenda Terdekat
            </h4>
            <div class="space-y-3">
                <!-- Mockup Agenda -->
                <div class="flex gap-3 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-700/50">
                    <div class="bg-blue-100 text-blue-600 w-12 h-12 rounded-lg flex flex-col items-center justify-center shrink-0">
                        <span class="text-[10px] font-bold uppercase">Ahad</span>
                        <span class="text-lg font-black leading-none">12</span>
                    </div>
                    <div>
                        <h5 class="font-bold text-sm text-slate-900 dark:text-white">Kajian Rutin Ahad Pagi</h5>
                        <p class="text-[11px] text-slate-500 mb-1">05:30 WIB - Masjid Baiturrahman</p>
                        <span class="inline-block px-2 py-0.5 bg-slate-200 dark:bg-slate-700 text-[9px] font-bold rounded text-slate-600 dark:text-slate-300">Terbuka Untuk Umum</span>
                    </div>
                </div>
            </div>
            <a href="#" class="block text-center mt-4 text-xs font-bold text-primary hover:underline">Lihat Semua Agenda</a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
