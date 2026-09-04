<?= $this->extend('layout/masjid_public') ?>

<?= $this->section('content') ?>
<!-- Hero Section -->
<section class="relative pt-32 pb-20 px-6 bg-white dark:bg-background-dark overflow-hidden">
    <div class="max-w-[1200px] mx-auto text-center">
        <h1 class="text-4xl md:text-6xl font-black mb-6 tracking-tight animate-in fade-in slide-in-from-bottom-8 duration-700">Agenda & Kegiatan</h1>
        <p class="text-[#608a7e] max-w-2xl mx-auto text-lg animate-in fade-in duration-1000 delay-200">
            Daftar kegiatan, kajian, dan program sosial yang dapat diikuti oleh seluruh jamaah <?= esc($masjid['name']) ?>.
        </p>
    </div>
</section>

<!-- Main Grid -->
<section class="pb-32 px-6">
    <div class="max-w-[1200px] mx-auto">
        <!-- Category Filter -->
        <div class="flex flex-wrap items-center justify-center gap-2 mb-16 animate-in fade-in duration-1000 delay-400">
            <a href="<?= base_url($masjid['username'] . '/program') ?>" class="px-6 py-3 rounded-full text-sm font-bold transition-all <?= !$activeCat ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'bg-gray-50 text-[#608a7e] hover:bg-gray-100' ?>">
                Semua Program
            </a>
            <?php foreach ($categories as $cat): ?>
                <a href="<?= base_url($masjid['username'] . '/program?category=' . $cat['slug']) ?>" class="px-6 py-3 rounded-full text-sm font-bold transition-all <?= $activeCat == $cat['slug'] ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'bg-gray-50 text-[#608a7e] hover:bg-gray-100' ?>">
                    <?= esc($cat['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($programs)): ?>
            <div class="p-20 bg-white dark:bg-white/5 rounded-[3rem] border border-dashed border-[#dbe6e1] dark:border-white/10 text-center animate-in fade-in zoom-in duration-700">
                <div class="size-20 bg-slate-50 dark:bg-white/5 rounded-full flex items-center justify-center text-slate-300 mx-auto mb-6">
                    <span class="material-symbols-outlined text-4xl">event_busy</span>
                </div>
                <h4 class="text-xl font-bold mb-2">Belum ada agenda</h4>
                <p class="text-[#608a7e] text-sm">Belum ada agenda atau kegiatan mendatang yang dipublikasikan.</p>
            </div>
        <?php else: ?>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($programs as $item): ?>
                    <a href="<?= base_url($masjid['username'] . '/program/' . $item['slug']) ?>" class="group bg-white dark:bg-white/5 rounded-[2.5rem] overflow-hidden border border-[#e5e7eb] dark:border-white/10 hover:shadow-2xl transition-all duration-500 flex flex-col animate-in fade-in slide-in-from-bottom-8 duration-700">
                        <div class="aspect-[3/4] overflow-hidden relative">
                            <?php if (!empty($item['thumbnail'])): ?>
                                <img src="<?= $storage->url($item['thumbnail']) ?>" class="size-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <?php else: ?>
                                <div class="size-full bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-300">
                                    <span class="material-symbols-outlined text-5xl">event</span>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Date Badge -->
                            <div class="absolute top-6 left-6 flex flex-col items-center min-w-[60px] p-3 bg-white/90 dark:bg-background-dark/90 backdrop-blur-md rounded-2xl shadow-xl">
                                <span class="text-xs font-black text-primary uppercase leading-none mb-1"><?= date('M', strtotime($item['date_start'])) ?></span>
                                <span class="text-2xl font-black leading-none"><?= date('d', strtotime($item['date_start'])) ?></span>
                            </div>
                        </div>

                        <div class="p-8 flex-1 flex flex-col">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="px-3 py-1 bg-primary/10 text-primary text-[10px] font-black uppercase tracking-widest rounded-lg italic">
                                    <?= esc($item['category_name'] ?: 'Umum') ?>
                                </span>
                            </div>
                            <h4 class="text-2xl font-black mb-6 group-hover:text-primary transition-colors line-clamp-2 leading-tight"><?= esc($item['title']) ?></h4>
                            
                            <div class="space-y-3 mb-8">
                                <div class="flex items-center gap-3 text-sm text-[#608a7e] font-medium">
                                    <span class="material-symbols-outlined text-lg text-primary">schedule</span>
                                    <?= date('H:i', strtotime($item['date_start'])) ?> WIB - Selesai
                                </div>
                                <div class="flex items-center gap-3 text-sm text-[#608a7e] font-medium">
                                    <span class="material-symbols-outlined text-lg text-primary">location_on</span>
                                    <?= esc($item['location']) ?>
                                </div>
                            </div>

                            <?php if (!empty($item['target_donation']) && $item['target_donation'] > 0): ?>
                                <?php $persen = min(100, round((($item['collected'] ?? 0) / $item['target_donation']) * 100)); ?>
                                <div class="mb-6">
                                    <div class="flex justify-between items-end mb-1.5">
                                        <div>
                                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Terkumpul</p>
                                            <p class="text-base font-black text-primary">Rp <?= number_format($item['collected'] ?? 0, 0, ',', '.') ?></p>
                                        </div>
                                        <p class="text-[11px] font-bold text-gray-400">dari Rp <?= number_format($item['target_donation'], 0, ',', '.') ?></p>
                                    </div>
                                    <div class="w-full bg-gray-100 dark:bg-white/10 rounded-full h-2.5 overflow-hidden">
                                        <div class="bg-primary h-2.5 rounded-full transition-all duration-1000" style="width: <?= $persen ?>%"></div>
                                    </div>
                                    <p class="text-right text-[11px] font-bold text-primary mt-1"><?= $persen ?>% terpenuhi</p>
                                </div>
                            <?php endif; ?>

                            <div class="mt-auto pt-6 border-t border-gray-100 dark:border-white/5 flex items-center justify-between">
                                <span class="text-xs font-black text-primary group-hover:translate-x-2 transition-transform uppercase tracking-widest">Detail Kegiatan</span>
                                <span class="material-symbols-outlined text-primary group-hover:translate-x-2 transition-transform">arrow_forward</span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?= $this->endSection() ?>
