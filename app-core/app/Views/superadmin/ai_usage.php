<?= $this->extend('layout/superadmin') ?>

<?= $this->section('content') ?>
<?php
    $fmt = fn($n) => number_format((int) $n, 0, ',', '.');
?>

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-black text-slate-900 dark:text-white">Pemakaian Token AI</h1>
        <p class="text-slate-500 text-sm mt-1">
            Kunci SumoPod dipakai bersama seluruh masjid, jadi seluruh token ini adalah biaya platform.
        </p>
    </div>
    <form method="get" class="flex items-end gap-2">
        <div>
            <label class="block text-[11px] font-bold text-slate-500 mb-1">Dari</label>
            <input type="date" name="dari" value="<?= esc($dari, 'attr') ?>"
                   class="rounded-lg border-slate-300 dark:bg-slate-800 dark:border-slate-700 text-sm py-2">
        </div>
        <div>
            <label class="block text-[11px] font-bold text-slate-500 mb-1">Sampai</label>
            <input type="date" name="sampai" value="<?= esc($sampai, 'attr') ?>"
                   class="rounded-lg border-slate-300 dark:bg-slate-800 dark:border-slate-700 text-sm py-2">
        </div>
        <button class="px-4 py-2 bg-primary text-white font-bold text-sm rounded-lg">Terapkan</button>
    </form>
</div>

<!-- Ringkasan -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Total Token</p>
        <p class="text-3xl font-black text-slate-900 dark:text-white"><?= $fmt($ringkasan['total_tokens']) ?></p>
    </div>
    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Jumlah Panggilan</p>
        <p class="text-3xl font-black text-slate-900 dark:text-white"><?= $fmt($ringkasan['panggilan']) ?></p>
    </div>
</div>

<?php if ($ringkasan['panggilan'] === 0): ?>
    <div class="bg-white dark:bg-slate-900 p-12 rounded-2xl border border-slate-200 dark:border-slate-800 text-center text-slate-400">
        <span class="material-symbols-outlined text-4xl mb-2 block opacity-40">monitoring</span>
        Belum ada pemakaian AI pada rentang ini.
    </div>
<?php else: ?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Per model -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
            <h2 class="font-bold text-slate-900 dark:text-white">Per Model</h2>
            <p class="text-xs text-slate-400">Model yang benar-benar melayani (bisa berbeda dari yang diminta bila cadangan dipakai).</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 text-xs uppercase">
                    <tr><th class="px-6 py-3">Model</th><th class="px-6 py-3 text-right">Panggilan</th><th class="px-6 py-3 text-right">Total Token</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php foreach ($perModel as $r): ?>
                        <tr>
                            <td class="px-6 py-3 font-mono text-xs text-slate-900 dark:text-white"><?= esc($r['model_used']) ?></td>
                            <td class="px-6 py-3 text-right"><?= $fmt($r['panggilan']) ?></td>
                            <td class="px-6 py-3 text-right font-bold"><?= $fmt($r['total_tokens']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Per fitur -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
            <h2 class="font-bold text-slate-900 dark:text-white">Per Fitur</h2>
            <p class="text-xs text-slate-400">Apa yang menghabiskan token.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 text-xs uppercase">
                    <tr><th class="px-6 py-3">Fitur</th><th class="px-6 py-3 text-right">Panggilan</th><th class="px-6 py-3 text-right">Total Token</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php foreach ($perFitur as $r): ?>
                        <tr>
                            <td class="px-6 py-3 text-slate-900 dark:text-white"><?= esc($r['feature']) ?></td>
                            <td class="px-6 py-3 text-right"><?= $fmt($r['panggilan']) ?></td>
                            <td class="px-6 py-3 text-right font-bold"><?= $fmt($r['total_tokens']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Per masjid -->
<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden mt-6">
    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
        <h2 class="font-bold text-slate-900 dark:text-white">Per Masjid</h2>
        <p class="text-xs text-slate-400">Masjid mana yang paling banyak memakai AI.</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 text-xs uppercase">
                <tr><th class="px-6 py-3">Masjid</th><th class="px-6 py-3 text-right">Panggilan</th><th class="px-6 py-3 text-right">Total Token</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                <?php foreach ($perMasjid as $r): ?>
                    <tr>
                        <td class="px-6 py-3 text-slate-900 dark:text-white"><?= esc($r['masjid_name']) ?></td>
                        <td class="px-6 py-3 text-right"><?= $fmt($r['panggilan']) ?></td>
                        <td class="px-6 py-3 text-right font-bold"><?= $fmt($r['total_tokens']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php endif; ?>
<?= $this->endSection() ?>
