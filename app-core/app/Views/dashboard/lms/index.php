<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-800 dark:text-white">E-Learning Pengurus</h2>
    <p class="text-slate-500 text-sm mt-1">Tingkatkan kapasitas manajemen dan pelayanan masjid Anda melalui modul-modul terbaik.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if(empty($modules)): ?>
        <div class="col-span-full p-8 text-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
            <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">menu_book</span>
            <p class="text-slate-500">Belum ada modul pembelajaran yang tersedia saat ini.</p>
        </div>
    <?php endif; ?>

    <?php foreach($modules as $m): ?>
    <a href="<?= base_url('dashboard/lms/module/' . $m['slug']) ?>" class="group bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden hover:shadow-md hover:border-primary/50 transition-all">
        <?php if ($m['thumbnail']): ?>
            <?php 
                $storage = new \App\Libraries\Storage(); 
                $thumbPath = (strpos($m['thumbnail'], '/') === false) ? 'uploads/lms/' . $m['thumbnail'] : $m['thumbnail'];
            ?>
            <img src="<?= $storage->url($thumbPath) ?>" class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500">
        <?php else: ?>
            <div class="w-full h-48 bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                <span class="material-symbols-outlined text-4xl text-slate-300">school</span>
            </div>
        <?php endif; ?>
        
        <div class="p-5">
            <?php if (!empty($m['lembaga_pemateri'])): ?>
                <div class="text-[10px] font-bold text-primary uppercase mb-2 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[12px]">verified</span>
                    Oleh: <?= esc($m['lembaga_nama']) ?>
                </div>
            <?php endif; ?>
            <h3 class="font-bold text-lg text-slate-800 dark:text-white group-hover:text-primary transition-colors line-clamp-2"><?= esc($m['title']) ?></h3>
            <p class="text-sm text-slate-500 mt-2 line-clamp-2"><?= esc($m['description']) ?></p>
        </div>
    </a>
    <?php endforeach; ?>
</div>
<?= $this->endSection() ?>
