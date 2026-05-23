<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Histori Penyaluran</h2>
        <p class="text-slate-500 text-sm mt-1">Rekam jejak pemberian bantuan kepada para Mustahik.</p>
    </div>
    <div class="flex gap-2">
        <a href="<?= base_url('dashboard/distribution') ?>" class="text-slate-500 hover:text-slate-700 font-medium text-sm flex items-center gap-1 mr-4">
            <span class="material-symbols-outlined text-sm">arrow_back</span> Kembali
        </a>
        <a href="<?= base_url('dashboard/distribution/create') ?>" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-xl font-bold text-sm transition-colors flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">add</span> Catat Bantuan Baru
        </a>
    </div>
</div>

<?php if(session()->getFlashdata('success')): ?>
<div class="bg-emerald-50 text-emerald-600 p-4 rounded-xl mb-6 flex items-center gap-3">
    <span class="material-symbols-outlined">check_circle</span>
    <p class="text-sm font-medium"><?= session()->getFlashdata('success') ?></p>
</div>
<?php endif; ?>

<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 font-bold uppercase text-[10px]">
                <tr>
                    <th class="px-6 py-4 tracking-wider">Tanggal</th>
                    <th class="px-6 py-4 tracking-wider">Nama Mustahik</th>
                    <th class="px-6 py-4 tracking-wider">Nominal / Bentuk</th>
                    <th class="px-6 py-4 tracking-wider">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                <?php if(empty($history)): ?>
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                        Belum ada histori penyaluran bantuan.
                    </td>
                </tr>
                <?php endif; ?>

                <?php foreach($history as $h): ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                    <td class="px-6 py-4 text-slate-500 font-medium"><?= date('d M Y', strtotime($h['date'])) ?></td>
                    <td class="px-6 py-4 font-bold text-slate-900 dark:text-white"><?= esc($h['mustahik_name']) ?></td>
                    <td class="px-6 py-4 font-bold text-emerald-600 dark:text-emerald-400">Rp <?= number_format($h['amount'], 0, ',', '.') ?></td>
                    <td class="px-6 py-4 text-slate-500 text-xs"><?= esc($h['description']) ?: '-' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
