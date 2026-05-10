<?= $this->extend('layout/superadmin') ?>

<?= $this->section('content') ?>

<!-- Stats Overview -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="size-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center text-primary">
                <span class="material-symbols-outlined text-2xl">mosque</span>
            </div>
        </div>
        <p class="text-slate-500 text-sm font-medium">Total Masjid</p>
        <h3 class="text-2xl font-black mt-1"><?= $stats['total_masjid'] ?></h3>
    </div>
    
    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="size-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-blue-600">
                <span class="material-symbols-outlined text-2xl">groups</span>
            </div>
        </div>
        <p class="text-slate-500 text-sm font-medium">Total User</p>
        <h3 class="text-2xl font-black mt-1"><?= $stats['total_users'] ?></h3>
    </div>

    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="size-12 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center text-amber-600">
                <span class="material-symbols-outlined text-2xl">volunteer_activism</span>
            </div>
        </div>
        <p class="text-slate-500 text-sm font-medium">Total Jamaah</p>
        <h3 class="text-2xl font-black mt-1"><?= $stats['total_warga'] ?></h3>
    </div>

    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="size-12 bg-rose-100 dark:bg-rose-900/30 rounded-xl flex items-center justify-center text-rose-600">
                <span class="material-symbols-outlined text-2xl">account_balance_wallet</span>
            </div>
        </div>
        <p class="text-slate-500 text-sm font-medium">Dana Terkelola</p>
        <h3 class="text-2xl font-black mt-1 text-sm leading-none pt-2">Rp <?= number_format($stats['total_dana'], 0, ',', '.') ?></h3>
    </div>

    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="size-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center text-indigo-600">
                <span class="material-symbols-outlined text-2xl">campaign</span>
            </div>
        </div>
        <p class="text-slate-500 text-sm font-medium">Program Aktif</p>
        <h3 class="text-2xl font-black mt-1"><?= $stats['active_programs'] ?></h3>
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-8 mt-8">
    <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center">
            <h4 class="font-bold text-slate-800 dark:text-white">Masjid Baru Terdaftar</h4>
            <a href="<?= base_url('superadmin/masjid') ?>" class="text-xs font-bold text-primary hover:underline">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 font-bold uppercase text-[10px]">
                    <tr>
                        <th class="px-6 py-4 tracking-wider">Nama Masjid</th>
                        <th class="px-6 py-4 tracking-wider">Username</th>
                        <th class="px-6 py-4 tracking-wider">Tanggal Daftar</th>
                        <th class="px-6 py-4 text-right tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php foreach($recent_masjids as $m): ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="px-6 py-4 font-bold"><?= esc($m['name']) ?></td>
                        <td class="px-6 py-4 text-slate-500">@<?= esc($m['username']) ?></td>
                        <td class="px-6 py-4 text-slate-400 font-medium"><?= date('d M Y', strtotime($m['created_at'])) ?></td>
                        <td class="px-6 py-4 text-right">
                            <a href="<?= base_url('superadmin/masjid/manage/' . $m['id']) ?>" class="text-primary hover:underline font-bold text-xs">Kelola</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 p-8 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <h4 class="font-bold mb-6">Pusat Informasi Kontrol</h4>
        <div class="space-y-4">
            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/50 rounded-xl">
                <p class="text-xs text-emerald-600 dark:text-emerald-400 font-bold uppercase mb-2">Platform Health</p>
                <div class="flex items-center gap-2">
                    <div class="size-2 bg-emerald-500 rounded-full animate-pulse"></div>
                    <span class="text-sm font-bold">Semua Sistem Normal</span>
                </div>
            </div>
            
            <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl">
                <p class="text-xs text-slate-500 font-bold uppercase mb-1">Backup Terakhir</p>
                <p class="text-sm font-medium"><?= date('d M Y, H:i') ?></p>
            </div>

            <div class="p-4 border border-slate-100 dark:border-slate-800 rounded-xl">
                <p class="text-xs text-slate-400 font-bold uppercase mb-3">Tindakan Cepat</p>
                <div class="grid grid-cols-2 gap-2">
                    <button class="px-3 py-2 bg-slate-100 dark:bg-slate-800 text-[10px] font-bold rounded-lg hover:bg-slate-200">Broadcast Global</button>
                    <button class="px-3 py-2 bg-slate-100 dark:bg-slate-800 text-[10px] font-bold rounded-lg hover:bg-slate-200">Maintenance Mode</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
