<?= $this->extend('layout/superadmin') ?>

<?= $this->section('content') ?>
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">LMS Modul</h2>
        <p class="text-slate-500 text-sm">Kelola modul pembelajaran untuk pengurus masjid.</p>
    </div>
    <a href="<?= base_url('superadmin/lms/create') ?>" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg font-medium text-sm flex items-center gap-2">
        <span class="material-symbols-outlined text-sm">add</span> Tambah Modul
    </a>
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
                <th class="px-6 py-4">Modul</th>
                <th class="px-6 py-4">Lembaga Pemateri</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
            <?php foreach($modules as $m): ?>
            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <?php if ($m['thumbnail']): ?>
                            <img src="<?= asset_url('uploads/lms/' . $m['thumbnail']) ?>" class="w-12 h-12 object-cover rounded-lg">
                        <?php else: ?>
                            <div class="w-12 h-12 bg-slate-100 rounded-lg flex items-center justify-center">
                                <span class="material-symbols-outlined text-slate-400">image</span>
                            </div>
                        <?php endif; ?>
                        <div>
                            <p class="font-bold text-slate-900 dark:text-white"><?= esc($m['title']) ?></p>
                            <p class="text-[10px] text-slate-500"><?= date('d M Y', strtotime($m['created_at'])) ?></p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 font-medium"><?= esc($m['lembaga_pemateri'] ?? '-') ?></td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 rounded text-[10px] uppercase font-bold <?= $m['status'] == 'published' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' ?>">
                        <?= esc($m['status']) ?>
                    </span>
                </td>
                <td class="px-6 py-4 text-right space-x-2">
                    <a href="<?= base_url('superadmin/lms/' . $m['id'] . '/materials') ?>" class="text-blue-600 hover:underline font-bold text-xs">Kelola Materi</a>
                    <a href="<?= base_url('superadmin/lms/edit/' . $m['id']) ?>" class="text-primary hover:underline font-bold text-xs">Edit</a>
                    <form action="<?= base_url('superadmin/lms/delete/' . $m['id']) ?>" method="POST" class="inline" onsubmit="return confirm('Hapus modul ini?')">
                        <button type="submit" class="text-red-600 hover:underline font-bold text-xs">Hapus</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>
