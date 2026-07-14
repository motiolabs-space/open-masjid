<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Cari Masjid</h2>
    <p class="text-slate-500 text-sm mt-1">Temukan dan ikuti masjid untuk mendapatkan kabar kegiatan serta laporan transparansinya.</p>
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

<form action="<?= base_url('dashboard/cari-masjid') ?>" method="GET" class="mb-6">
    <div class="relative max-w-xl">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
        <input type="text" name="q" value="<?= esc($keyword) ?>" placeholder="Cari nama, username, atau alamat masjid..."
               class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-primary py-3 pl-11 pr-4 text-sm text-slate-800 dark:text-white">
    </div>
</form>

<?php if (empty($masjids)): ?>
<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-10 text-center text-slate-500">
    <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">mosque</span>
    <p class="text-sm">Tidak ada masjid yang cocok dengan pencarian Anda.</p>
</div>
<?php else: ?>
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    <?php foreach ($masjids as $m): ?>
    <?php $isFollowed = in_array((int) $m['id'], $followedIds, true); ?>
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 flex flex-col">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined">mosque</span>
            </div>
            <div class="min-w-0">
                <h3 class="font-bold text-slate-800 dark:text-white truncate"><?= esc($m['name']) ?></h3>
                <p class="text-xs text-slate-500 truncate">@<?= esc($m['username']) ?></p>
            </div>
        </div>
        <?php if (!empty($m['address'])): ?>
        <p class="text-xs text-slate-500 mb-4 line-clamp-2 flex items-start gap-1">
            <span class="material-symbols-outlined text-sm">location_on</span>
            <span><?= esc($m['address']) ?></span>
        </p>
        <?php endif; ?>
        <div class="mt-auto flex items-center gap-2">
            <a href="<?= base_url(esc($m['username'], 'url')) ?>" target="_blank"
               class="flex-1 text-center px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-xs font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                Lihat Profil
            </a>
            <?php if ($isFollowed): ?>
            <form action="<?= base_url('dashboard/masjid-saya/unfollow/' . $m['id']) ?>" method="POST" class="flex-1">
                <?= csrf_field() ?>
                <button type="submit" class="w-full px-3 py-2 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-bold hover:bg-slate-200 transition-colors flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-sm">check</span> Diikuti
                </button>
            </form>
            <?php else: ?>
            <form action="<?= base_url('dashboard/masjid-saya/follow/' . $m['id']) ?>" method="POST" class="flex-1">
                <?= csrf_field() ?>
                <button type="submit" class="w-full px-3 py-2 rounded-lg bg-primary text-white text-xs font-bold hover:bg-primary/90 transition-colors flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-sm">add</span> Ikuti
                </button>
            </form>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
