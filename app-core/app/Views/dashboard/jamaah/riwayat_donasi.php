<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Riwayat Donasi</h2>
    <p class="text-slate-500 text-sm mt-1">Catatan donasi Anda berdasarkan email <?= esc(session()->get('user_email')) ?>.</p>
</div>

<div class="bg-gradient-to-br from-primary to-emerald-800 text-white rounded-2xl p-6 mb-6 max-w-sm">
    <p class="text-emerald-100 text-xs mb-1">Total Donasi Berhasil</p>
    <h3 class="text-3xl font-black">Rp <?= number_format($totalSukses, 0, ',', '.') ?></h3>
</div>

<?php if (empty($donations)): ?>
<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-10 text-center flex flex-col items-center">
    <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center text-slate-400 mb-4">
        <span class="material-symbols-outlined text-3xl">volunteer_activism</span>
    </div>
    <h4 class="font-bold text-slate-700 dark:text-slate-300 mb-1">Belum Ada Donasi</h4>
    <p class="text-sm text-slate-500 max-w-sm mb-6">Donasi yang Anda lakukan akan tercatat otomatis di sini selama menggunakan email yang sama.</p>
    <a href="<?= base_url('dashboard/cari-masjid') ?>" class="px-6 py-2.5 bg-primary text-white text-sm font-bold rounded-lg hover:bg-primary/90 transition-colors">Cari Masjid untuk Berdonasi</a>
</div>
<?php else: ?>
<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 font-bold uppercase text-[10px]">
                <tr>
                    <th class="px-6 py-4 tracking-wider">Tanggal</th>
                    <th class="px-6 py-4 tracking-wider">Masjid / Program</th>
                    <th class="px-6 py-4 tracking-wider text-right">Jumlah</th>
                    <th class="px-6 py-4 tracking-wider text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                <?php foreach ($donations as $d): ?>
                <?php
                    $badge = [
                        'success' => ['Berhasil', 'bg-emerald-50 text-emerald-600'],
                        'pending' => ['Menunggu', 'bg-amber-50 text-amber-600'],
                        'failed'  => ['Gagal', 'bg-rose-50 text-rose-600'],
                        'expired' => ['Kedaluwarsa', 'bg-slate-100 text-slate-500'],
                    ][$d['status']] ?? ['-', 'bg-slate-100 text-slate-500'];
                ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                    <td class="px-6 py-4 text-slate-600 dark:text-slate-300 whitespace-nowrap">
                        <?= date('d M Y', strtotime($d['created_at'])) ?>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-bold text-slate-800 dark:text-white"><?= esc($d['masjid_name'] ?? '-') ?></p>
                        <p class="text-xs text-slate-500"><?= esc($d['program_title'] ?? 'Donasi Umum') ?></p>
                    </td>
                    <td class="px-6 py-4 text-right font-bold text-slate-800 dark:text-white whitespace-nowrap">
                        Rp <?= number_format((float) $d['amount'], 0, ',', '.') ?>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold <?= $badge[1] ?>"><?= $badge[0] ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
