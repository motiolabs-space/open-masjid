<aside class="w-72 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col h-screen sticky top-0">
<div class="p-6 border-b border-slate-100 dark:border-slate-800">
    <div class="flex items-center justify-center">
        <img src="<?= asset_url('logo.png') ?>" alt="Logo Masj.id" class="h-12">
    </div>
</div>
<div class="flex-1 overflow-y-auto p-4 sidebar-scroll">
    <div class="mb-6">
        <p class="px-3 mb-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Menu Utama</p>
        <nav class="flex flex-col gap-1">
            <?php
                $uri = uri_string();
                $activeClass = 'active-nav shadow-sm';
                $inactiveClass = 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors';
            ?>
            <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= ($uri == 'dashboard' || $uri == 'dashboard/') ? $activeClass : $inactiveClass ?>" href="<?= base_url('dashboard') ?>">
                <span class="material-symbols-outlined text-xl">dashboard</span>
                <span class="text-sm font-semibold">Dashboard</span>
            </a>
            
            <?php if (session()->get('role') === 'pengurus' || session()->get('role') === 'superadmin'): ?>
            <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= ($uri == 'dashboard/profil') ? $activeClass : $inactiveClass ?>" href="<?= base_url('dashboard/profil') ?>">
                <span class="material-symbols-outlined text-xl">account_balance</span>
                <span class="text-sm font-medium">Profil Masjid</span>
            </a>
            <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= ($uri == 'dashboard/program') ? $activeClass : $inactiveClass ?>" href="<?= base_url('dashboard/program') ?>">
                <span class="material-symbols-outlined text-xl">list_alt</span>
                <span class="text-sm font-medium">Program & Kegiatan</span>
            </a>
            <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= ($uri == 'dashboard/berita') ? $activeClass : $inactiveClass ?>" href="<?= base_url('dashboard/berita') ?>">
                <span class="material-symbols-outlined text-xl">newspaper</span>
                <span class="text-sm font-medium">Berita & Dokumentasi</span>
            </a>
            <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= ($uri == 'dashboard/keuangan' || str_contains($uri, 'dashboard/keuangan')) ? $activeClass : $inactiveClass ?>" href="<?= base_url('dashboard/keuangan') ?>">
                <span class="material-symbols-outlined text-xl">payments</span>
                <span class="text-sm font-medium">Keuangan & Mutasi</span>
            </a>
            <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= ($uri == 'dashboard/distribution') ? $activeClass : $inactiveClass ?>" href="<?= base_url('dashboard/distribution') ?>">
                <span class="material-symbols-outlined text-xl">volunteer_activism</span>
                <span class="text-sm font-medium">Penyaluran Bantuan</span>
            </a>
            <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= ($uri == 'dashboard/warga') ? $activeClass : $inactiveClass ?>" href="<?= base_url('dashboard/warga') ?>">
                <span class="material-symbols-outlined text-xl">groups_3</span>
                <span class="text-sm font-medium">Data Warga</span>
            </a>
<?php else: ?>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors" href="#">
<span class="material-symbols-outlined text-xl">volunteer_activism</span>
<span class="text-sm font-medium">Riwayat Donasi</span>
</a>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors" href="#">
<span class="material-symbols-outlined text-xl">favorite</span>
<span class="text-sm font-medium">Program Diikuti</span>
</a>
<?php endif; ?>
</nav>
</div>

<?php if (session()->get('role') === 'pengurus' || session()->get('role') === 'superadmin'): ?>
<div>
<p class="px-3 mb-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Laporan & Sistem</p>
<nav class="flex flex-col gap-1">
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors" href="#">
<span class="material-symbols-outlined text-xl">calendar_month</span>
<span class="text-sm font-medium">Kalender</span>
</a>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= ($uri == 'dashboard/reports') ? $activeClass : $inactiveClass ?>" href="<?= base_url('dashboard/reports') ?>">
<span class="material-symbols-outlined text-xl">description</span>
<span class="text-sm font-medium">Laporan</span>
</a>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= ($uri == 'dashboard/pembayaran') ? $activeClass : $inactiveClass ?>" href="<?= base_url('dashboard/pembayaran') ?>">
<span class="material-symbols-outlined text-xl">payments</span>
<span class="text-sm font-medium">Pengaturan Pembayaran</span>
</a>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors" href="#">
<span class="material-symbols-outlined text-xl">settings</span>
<span class="text-sm font-medium">Pengaturan Umum</span>
</a>
</nav>
</div>
<?php endif; ?>
</div>
<div class="p-4 border-t border-slate-100 dark:border-slate-800">
<a href="<?= base_url('logout') ?>" class="flex w-full items-center gap-3 px-3 py-2.5 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
<span class="material-symbols-outlined text-xl">logout</span>
<span class="text-sm font-semibold">Keluar</span>
</a>
</div>
</aside>
