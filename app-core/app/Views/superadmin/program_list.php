<?= $this->extend('layout/superadmin') ?>

<?= $this->section('content') ?>

<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex flex-wrap justify-between items-center gap-4">
        <div>
            <h4 class="font-bold text-slate-800 dark:text-white">Monitoring Program</h4>
            <p class="text-xs text-slate-500 mt-1">Total: <?= count($programs) ?> Program dibuat oleh masjid</p>
        </div>
        <div class="flex items-center gap-2">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">search</span>
                <input type="text" placeholder="Cari program..." class="pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border-none rounded-lg text-sm focus:ring-2 focus:ring-primary w-64">
            </div>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 font-bold uppercase text-[10px]">
                <tr>
                    <th class="px-6 py-4 tracking-wider">ID</th>
                    <th class="px-6 py-4 tracking-wider">Program</th>
                    <th class="px-6 py-4 tracking-wider">Masjid Penyelenggara</th>
                    <th class="px-6 py-4 tracking-wider">Jadwal & Target</th>
                    <th class="px-6 py-4 tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                <?php foreach($programs as $p): ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                    <td class="px-6 py-4 text-[10px] font-bold text-slate-400">#<?= $p['id'] ?></td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-900 dark:text-white"><?= esc($p['title']) ?></div>
                        <div class="text-[10px] text-slate-400 mt-0.5"><?= esc($p['slug']) ?></div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-primary"><?= esc($p['masjid_name']) ?></div>
                        <div class="text-[10px] text-slate-400 mt-0.5">@<?= esc($p['masjid_username']) ?></div>
                    </td>
                    <td class="px-6 py-4 text-xs space-y-1">
                        <div>
                            <span class="text-slate-400 block mb-0.5">Tgl Mulai:</span>
                            <span class="font-medium text-slate-700 dark:text-slate-300"><?= !empty($p['date_start']) && $p['date_start'] != '0000-00-00 00:00:00' ? date('d M Y', strtotime($p['date_start'])) : '-' ?></span>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-0.5">Target/Kuota:</span>
                            <?php if(!empty($p['target_donation']) && $p['target_donation'] > 0): ?>
                                <span class="font-bold text-emerald-600">Rp <?= number_format($p['target_donation'], 0, ',', '.') ?></span>
                            <?php elseif(!empty($p['quota'])): ?>
                                <span class="font-bold text-indigo-600"><?= esc($p['quota']) ?> Orang</span>
                            <?php else: ?>
                                <span class="text-slate-400 italic">Tidak ada target</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded text-[10px] uppercase font-bold <?= $p['status'] == 'published' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' ?>">
                            <?= esc($p['status']) ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($programs)): ?>
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                        Belum ada program yang diselenggarakan oleh masjid manapun.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
