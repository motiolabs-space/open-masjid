<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>

<div class="mb-6 flex flex-wrap gap-4 items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Masjid Saya</h2>
        <p class="text-slate-500 text-sm mt-1">Masjid yang Anda ikuti.</p>
    </div>
    <a href="<?= base_url('dashboard/cari-masjid') ?>" class="bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-xl font-bold text-sm transition-colors flex items-center gap-2">
        <span class="material-symbols-outlined text-sm">search</span> Cari Masjid
    </a>
</div>

<?php if (session()->getFlashdata('success')): ?>
<div class="bg-emerald-50 text-emerald-600 p-4 rounded-xl mb-6 flex items-center gap-3">
    <span class="material-symbols-outlined">check_circle</span>
    <p class="text-sm font-medium"><?= esc(session()->getFlashdata('success')) ?></p>
</div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
<div class="bg-rose-50 text-rose-600 p-4 rounded-xl mb-6 flex items-center gap-3">
    <span class="material-symbols-outlined">error</span>
    <p class="text-sm font-medium"><?= esc(session()->getFlashdata('error')) ?></p>
</div>
<?php endif; ?>

<?php if (empty($masjids)): ?>
<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-10 text-center flex flex-col items-center">
    <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center text-slate-400 mb-4">
        <span class="material-symbols-outlined text-3xl">mosque</span>
    </div>
    <h4 class="font-bold text-slate-700 dark:text-slate-300 mb-1">Anda Belum Mengikuti Masjid</h4>
    <p class="text-sm text-slate-500 max-w-sm mb-6">Ikuti masjid untuk melihat kabar kegiatan, program, dan laporan transparansinya di sini.</p>
    <a href="<?= base_url('dashboard/cari-masjid') ?>" class="px-6 py-2.5 bg-primary text-white text-sm font-bold rounded-lg hover:bg-primary/90 transition-colors">Cari Masjid Sekarang</a>
</div>
<?php else: ?>
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    <?php foreach ($masjids as $m): ?>
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 flex flex-col">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined">mosque</span>
            </div>
            <div class="min-w-0">
                <h3 class="font-bold text-slate-800 dark:text-white truncate"><?= esc($m['name']) ?></h3>
                <p class="text-xs text-slate-500 truncate">Diikuti sejak <?= date('d M Y', strtotime($m['followed_at'])) ?></p>
            </div>
        </div>
        <div class="mt-auto flex items-center gap-2">
            <a href="<?= base_url(esc($m['username'], 'url')) ?>" target="_blank"
               class="flex-1 text-center px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-xs font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                Kunjungi
            </a>
            <form action="<?= base_url('dashboard/masjid-saya/unfollow/' . $m['id']) ?>" method="POST" class="flex-1">
                <?= csrf_field() ?>
                <button type="submit" class="w-full px-3 py-2 rounded-lg bg-rose-50 text-rose-600 text-xs font-bold hover:bg-rose-100 transition-colors flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-sm">close</span> Berhenti Ikuti
                </button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
