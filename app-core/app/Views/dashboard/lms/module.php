<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<div class="mb-6 flex justify-between items-center">
    <a href="<?= base_url('dashboard/lms') ?>" class="text-slate-500 hover:text-slate-800 font-medium text-sm flex items-center gap-1">
        <span class="material-symbols-outlined text-sm">arrow_back</span> Kembali ke Katalog
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Module Info -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <?php if ($module['thumbnail']): ?>
                <?php $storage = new \App\Libraries\Storage(); ?>
                <img src="<?= $storage->url($module['thumbnail']) ?>" class="w-full h-48 object-cover">
            <?php endif; ?>
            <div class="p-6">
                <?php if (!empty($module['lembaga_pemateri'])): ?>
                    <div class="text-[10px] font-bold text-primary uppercase mb-2 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[12px]">verified</span>
                        Oleh: <?= esc($module['lembaga_nama']) ?>
                    </div>
                <?php endif; ?>
                <h2 class="text-xl font-bold text-slate-800 dark:text-white mb-2"><?= esc($module['title']) ?></h2>
                <p class="text-sm text-slate-500 leading-relaxed"><?= nl2br(esc($module['description'])) ?></p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
            <h3 class="font-bold text-slate-800 dark:text-white mb-4">Progres Belajar Anda</h3>
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-slate-700 dark:text-slate-300"><?= $progress ?>% Selesai</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2.5">
                <div class="bg-emerald-500 h-2.5 rounded-full transition-all duration-1000" style="width: <?= $progress ?>%"></div>
            </div>
            <?php if($progress == 100): ?>
                <div class="mt-4 bg-emerald-50 text-emerald-600 p-3 rounded-lg flex items-center gap-2 text-sm font-medium">
                    <span class="material-symbols-outlined">workspace_premium</span>
                    Alhamdulillah, Modul Selesai!
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Syllabus -->
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-200 dark:border-slate-800">
                <h3 class="font-bold text-slate-800 dark:text-white">Silabus Materi</h3>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                <?php if(empty($materials)): ?>
                    <div class="p-6 text-center text-slate-500 text-sm">Belum ada materi untuk modul ini.</div>
                <?php endif; ?>
                <?php foreach($materials as $m): ?>
                <a href="<?= base_url('dashboard/lms/material/' . $m['id']) ?>" class="block p-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center <?= $m['is_completed'] ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-400' ?>">
                            <?php if($m['is_completed']): ?>
                                <span class="material-symbols-outlined text-lg">check</span>
                            <?php else: ?>
                                <span class="material-symbols-outlined text-lg">
                                    <?= $m['type'] == 'video' ? 'play_arrow' : ($m['type'] == 'pdf' ? 'picture_as_pdf' : 'article') ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-slate-800 dark:text-white text-sm <?= $m['is_completed'] ? 'line-through text-slate-400' : '' ?>">
                                <?= esc($m['title']) ?>
                            </h4>
                            <p class="text-xs text-slate-500 uppercase font-medium mt-1">Format: <?= esc($m['type']) ?></p>
                        </div>
                        <div>
                            <span class="material-symbols-outlined text-slate-300">chevron_right</span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
