<?= $this->extend('layout/superadmin') ?>

<?= $this->section('content') ?>
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Materi: <?= esc($module['title']) ?></h2>
    </div>
    <div class="flex gap-2">
        <a href="<?= base_url('superadmin/lms') ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-lg font-medium text-sm transition-colors">
            Kembali ke Modul
        </a>
        <a href="<?= base_url('superadmin/lms/' . $module['id'] . '/materials/create') ?>" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg font-medium text-sm flex items-center gap-2 transition-colors">
            <span class="material-symbols-outlined text-sm">add</span> Tambah Materi
        </a>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="mb-4 bg-emerald-50 text-emerald-600 p-4 rounded-lg flex items-center gap-3 text-sm">
        <span class="material-symbols-outlined">check_circle</span>
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <table class="w-full text-sm text-left">
        <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 font-bold uppercase text-[10px]">
            <tr>
                <th class="px-6 py-4">Urutan</th>
                <th class="px-6 py-4">Judul Materi</th>
                <th class="px-6 py-4">Tipe</th>
                <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
            <?php if(empty($materials)): ?>
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-slate-500">Belum ada materi di modul ini.</td>
                </tr>
            <?php endif; ?>
            <?php foreach($materials as $m): ?>
            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30">
                <td class="px-6 py-4 font-bold"><?= $m['order_number'] ?></td>
                <td class="px-6 py-4 font-medium text-slate-900 dark:text-white"><?= esc($m['title']) ?></td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 rounded text-[10px] uppercase font-bold bg-slate-100 text-slate-600">
                        <?= esc($m['type']) ?>
                    </span>
                </td>
                <td class="px-6 py-4 text-right space-x-2">
                    <a href="<?= base_url('superadmin/lms/materials/edit/' . $m['id']) ?>" class="text-primary hover:underline font-bold text-xs">Edit</a>
                    <form action="<?= base_url('superadmin/lms/materials/delete/' . $m['id']) ?>" method="POST" class="inline" onsubmit="return confirm('Hapus materi ini?')">
                        <button type="submit" class="text-red-600 hover:underline font-bold text-xs">Hapus</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>
