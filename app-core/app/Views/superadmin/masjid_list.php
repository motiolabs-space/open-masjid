<?= $this->extend('layout/superadmin') ?>

<?= $this->section('content') ?>

<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex flex-wrap justify-between items-center gap-4">
        <div>
            <h4 class="font-bold text-slate-800 dark:text-white">Daftar Seluruh Masjid</h4>
            <p class="text-xs text-slate-500 mt-1">Total: <?= count($masjids) ?> Masjid terdaftar</p>
        </div>
        <div class="flex items-center gap-2">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">search</span>
                <input type="text" placeholder="Cari masjid..." class="pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border-none rounded-lg text-sm focus:ring-2 focus:ring-primary w-64">
            </div>
            <button class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">add</span>
                Tambah Manual
            </button>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 font-bold uppercase text-[10px]">
                <tr>
                    <th class="px-6 py-4 tracking-wider">ID</th>
                    <th class="px-6 py-4 tracking-wider">Nama Masjid</th>
                    <th class="px-6 py-4 tracking-wider">Username</th>
                    <th class="px-6 py-4 tracking-wider">Alamat</th>
                    <th class="px-6 py-4 tracking-wider">Tanggal Daftar</th>
                    <th class="px-6 py-4 text-right tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                <?php foreach($masjids as $m): ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                    <td class="px-6 py-4 text-[10px] font-bold text-slate-400">#<?= $m['id'] ?></td>
                    <td class="px-6 py-4 font-bold text-slate-900 dark:text-white"><?= $m['name'] ?></td>
                    <td class="px-6 py-4">
                        <span class="bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded text-xs">@<?= $m['username'] ?></span>
                    </td>
                    <td class="px-6 py-4 truncate max-w-[200px]"><?= $m['address'] ?? '<span class="italic text-slate-400">Belum diisi</span>' ?></td>
                    <td class="px-6 py-4 text-slate-500 font-medium"><?= date('d M Y', strtotime($m['created_at'])) ?></td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <button class="text-primary hover:text-primary-dark font-bold text-xs">Edit</button>
                        <button class="text-rose-500 hover:text-rose-600 font-bold text-xs">Suspend</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
