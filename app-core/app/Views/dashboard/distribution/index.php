<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>

<div class="mb-6 flex flex-wrap gap-4 items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Penyaluran & Mustahik</h2>
        <p class="text-slate-500 text-sm mt-1">Manajemen basis data penerima manfaat dan skor kelayakan bantuan (AI).</p>
    </div>
    <div class="flex gap-2">
        <a href="<?= base_url('dashboard/distribution/history') ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 px-4 py-2 rounded-xl font-bold text-sm transition-colors flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">history</span> Histori Penyaluran
        </a>
        <a href="<?= base_url('dashboard/distribution/mustahik/create') ?>" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-xl font-bold text-sm transition-colors flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">person_add</span> Tambah Mustahik
        </a>
    </div>
</div>

<?php if(session()->getFlashdata('success')): ?>
<div class="bg-emerald-50 text-emerald-600 p-4 rounded-xl mb-6 flex items-center gap-3">
    <span class="material-symbols-outlined">check_circle</span>
    <p class="text-sm font-medium"><?= session()->getFlashdata('success') ?></p>
</div>
<?php endif; ?>

<?php if(session()->getFlashdata('error')): ?>
<div class="bg-rose-50 text-rose-600 p-4 rounded-xl mb-6 flex items-center gap-3">
    <span class="material-symbols-outlined">error</span>
    <p class="text-sm font-medium"><?= session()->getFlashdata('error') ?></p>
</div>
<?php endif; ?>

<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 font-bold uppercase text-[10px]">
                <tr>
                    <th class="px-6 py-4 tracking-wider">Nama & Kontak</th>
                    <th class="px-6 py-4 tracking-wider">Ekonomi</th>
                    <th class="px-6 py-4 tracking-wider text-center">AI Score</th>
                    <th class="px-6 py-4 tracking-wider text-center">Status</th>
                    <th class="px-6 py-4 text-right tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                <?php if(empty($mustahiks)): ?>
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                        Belum ada data Mustahik.
                    </td>
                </tr>
                <?php endif; ?>

                <?php foreach($mustahiks as $m): ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-900 dark:text-white"><?= esc($m['name']) ?></div>
                        <div class="text-xs text-slate-500 mt-1"><?= esc($m['phone']) ?: '-' ?></div>
                        <div class="text-xs text-slate-400 mt-0.5 truncate max-w-[200px]"><?= esc($m['address']) ?></div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-semibold text-slate-700 dark:text-slate-300">Rp <?= number_format($m['income_per_month'], 0, ',', '.') ?></div>
                        <div class="text-xs text-slate-500 mt-1">Tanggungan: <?= $m['dependents_count'] ?> orang</div>
                        <div class="text-[10px] uppercase font-bold text-slate-400 mt-0.5"><?= esc(str_replace('_', ' ', $m['house_ownership'])) ?></div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <?php if($m['ai_score'] !== null): ?>
                            <?php 
                            $score = $m['ai_score'];
                            $colorClass = 'bg-slate-100 text-slate-600';
                            if ($score >= 80) $colorClass = 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400';
                            elseif ($score >= 50) $colorClass = 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400';
                            else $colorClass = 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-400';
                            ?>
                            <div class="inline-flex flex-col items-center group relative cursor-help">
                                <span class="<?= $colorClass ?> text-xl font-black px-3 py-1 rounded-xl"><?= $score ?></span>
                                <div class="opacity-0 group-hover:opacity-100 transition-opacity absolute bottom-full mb-2 w-48 p-2 bg-slate-800 text-white text-xs rounded-lg shadow-lg pointer-events-none z-10">
                                    <?= esc($m['ai_reasoning']) ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <span class="text-xs text-slate-400 italic">Belum dinilai</span>
                            <form action="<?= base_url('dashboard/distribution/mustahik/rescore/' . $m['id']) ?>" method="POST" class="mt-1">
                                <button type="submit" class="text-[10px] font-bold text-primary hover:underline">Generate</button>
                            </form>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <?php if($m['status'] == 'active'): ?>
                            <span class="bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400 px-2 py-1 rounded text-xs font-bold">Aktif</span>
                        <?php else: ?>
                            <span class="bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 px-2 py-1 rounded text-xs font-bold">Inaktif</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="<?= base_url('dashboard/distribution/create/' . $m['id']) ?>" class="bg-emerald-500 text-white hover:bg-emerald-600 px-3 py-1.5 rounded-lg font-bold text-xs transition-all inline-flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">volunteer_activism</span> Beri Bantuan
                        </a>
                        <a href="<?= base_url('dashboard/distribution/mustahik/edit/' . $m['id']) ?>" class="bg-primary/10 text-primary hover:bg-primary hover:text-white px-3 py-1.5 rounded-lg font-bold text-xs transition-all inline-flex items-center gap-1">
                            Edit
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
