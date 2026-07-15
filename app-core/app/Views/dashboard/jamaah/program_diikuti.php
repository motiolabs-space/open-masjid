<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Program Diikuti</h2>
    <p class="text-slate-500 text-sm mt-1">Program & kegiatan dari masjid yang Anda ikuti.</p>
</div>

<?php if (empty($programs)): ?>
<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-10 text-center flex flex-col items-center">
    <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center text-slate-400 mb-4">
        <span class="material-symbols-outlined text-3xl">list_alt</span>
    </div>
    <?php if (!$hasFollowed): ?>
    <h4 class="font-bold text-slate-700 dark:text-slate-300 mb-1">Belum Ada Program</h4>
    <p class="text-sm text-slate-500 max-w-sm mb-6">Ikuti masjid terlebih dahulu untuk melihat program dan kegiatan mereka di sini.</p>
    <a href="<?= base_url('dashboard/cari-masjid') ?>" class="px-6 py-2.5 bg-primary text-white text-sm font-bold rounded-lg hover:bg-primary/90 transition-colors">Cari Masjid</a>
    <?php else: ?>
    <h4 class="font-bold text-slate-700 dark:text-slate-300 mb-1">Belum Ada Program Aktif</h4>
    <p class="text-sm text-slate-500 max-w-sm">Masjid yang Anda ikuti belum memublikasikan program apa pun saat ini.</p>
    <?php endif; ?>
</div>
<?php else: ?>
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    <?php foreach ($programs as $p): ?>
    <?php
        $target    = (float) ($p['target_donation'] ?? 0);
        $collected = (float) ($p['collected_amount'] ?? 0); // opsional; default 0
        $pct = ($target > 0) ? min(100, round($collected / $target * 100)) : 0;
    ?>
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 flex flex-col">
        <p class="text-[11px] font-bold text-primary uppercase tracking-wide mb-1"><?= esc($p['masjid_name']) ?></p>
        <h3 class="font-bold text-slate-800 dark:text-white mb-2 line-clamp-2"><?= esc($p['title']) ?></h3>
        <?php if (!empty($p['date_start'])): ?>
        <p class="text-xs text-slate-500 mb-3 flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">event</span>
            <?= date('d M Y', strtotime($p['date_start'])) ?>
            <?php if (!empty($p['date_end'])): ?> &ndash; <?= date('d M Y', strtotime($p['date_end'])) ?><?php endif; ?>
        </p>
        <?php endif; ?>
        <?php if ($target > 0): ?>
        <div class="mb-3">
            <div class="flex justify-between text-[10px] font-bold text-slate-500 mb-1">
                <span>Terkumpul <?= $pct ?>%</span>
                <span>Target Rp <?= number_format($target, 0, ',', '.') ?></span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 overflow-hidden">
                <div class="bg-primary h-2 rounded-full" style="width: <?= $pct ?>%"></div>
            </div>
        </div>
        <?php endif; ?>
        <a href="<?= base_url('donation/' . esc($p['masjid_username'], 'url') . '/form/' . esc($p['slug'] ?? '', 'url')) ?>"
           class="mt-auto block text-center w-full py-2 bg-primary text-white rounded-lg text-xs font-bold hover:bg-primary/90 transition-colors">
            Donasi untuk Program Ini
        </a>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
