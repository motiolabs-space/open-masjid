<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<div class="px-8 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="mb-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-black text-[#111816] dark:text-white tracking-tight">Broadcast Newsletter</h1>
                <p class="text-[#608a7e]">Kirim update, berita, dan laporan ke jamaah yang berlangganan.</p>
            </div>
            
            <div class="flex flex-wrap gap-2">
                <a href="<?= base_url('dashboard/broadcast/reminders') ?>" class="bg-white dark:bg-white/5 border border-[#e5e7eb] dark:border-white/10 text-[#111816] dark:text-white px-4 py-3 rounded-xl font-bold hover:bg-[#f0f5f3] transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-xl">schedule</span>
                    <span>Pengingat</span>
                </a>
                <a href="<?= base_url('dashboard/broadcast/groups') ?>" class="bg-white dark:bg-white/5 border border-[#e5e7eb] dark:border-white/10 text-[#111816] dark:text-white px-4 py-3 rounded-xl font-bold hover:bg-[#f0f5f3] transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-xl">groups</span>
                    <span>Grup</span>
                </a>
                <a href="<?= base_url('dashboard/broadcast/new') ?>" class="bg-primary text-white px-5 py-3 rounded-xl font-bold hover:bg-emerald-900 transition-all shadow-lg shadow-primary/20 flex items-center gap-2">
                    <span class="material-symbols-outlined text-xl">send</span>
                    <span>Buat Siaran Baru</span>
                </a>
            </div>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="bg-emerald-50 text-emerald-800 px-4 py-3 rounded-xl mb-6 flex items-center gap-2 font-bold border border-emerald-100">
                <span class="material-symbols-outlined">check_circle</span>
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-red-50 text-red-800 px-4 py-3 rounded-xl mb-6 flex items-center gap-2 font-bold border border-red-100">
                <span class="material-symbols-outlined">error</span>
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white dark:bg-white/5 p-6 rounded-3xl border border-[#e5e7eb] dark:border-white/10 flex items-center gap-4">
                <div class="size-12 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center">
                    <span class="material-symbols-outlined">campaign</span>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-[#111816] dark:text-white"><?= count($broadcasts) ?></h3>
                    <p class="text-sm font-bold text-gray-400">Total Siaran</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-white/5 rounded-3xl border border-[#e5e7eb] dark:border-white/10 overflow-hidden">
            <div class="p-6 border-b border-[#e5e7eb] dark:border-white/10 flex justify-between items-center">
                <h3 class="font-bold text-lg text-[#111816] dark:text-white">Riwayat Siaran</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 dark:bg-white/5 text-xs text-gray-500 uppercase tracking-wider font-bold">
                        <tr>
                            <th class="px-6 py-4">Subjek</th>
                            <th class="px-6 py-4">Penerima</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        <?php if (empty($broadcasts)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-400 font-medium">
                                    Belum ada riwayat siaran. Mulai kirim update ke jamaah!
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($broadcasts as $item): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                <td class="px-6 py-4 font-bold text-[#111816] dark:text-gray-200">
                                    <?= esc($item['subject']) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-gray-400 text-sm">group</span>
                                        <span class="font-bold text-gray-600 dark:text-gray-400"><?= $item['recipient_count'] ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($item['status'] == 'sent'): ?>
                                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold inline-flex items-center gap-1">
                                            <span class="material-symbols-outlined text-xs">check</span>
                                            Terkirim
                                        </span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-bold">
                                            Draft
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-500">
                                    <?= date('d M Y, H:i', strtotime($item['created_at'])) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
