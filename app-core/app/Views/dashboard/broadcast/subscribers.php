<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<div class="px-8 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="mb-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-black text-[#111816] dark:text-white tracking-tight">Daftar Subscriber</h1>
                <p class="text-[#608a7e]">Kelola data jamaah yang berlangganan newsletter.</p>
            </div>
            
            <div class="bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 px-4 py-2 rounded-xl flex items-center gap-2">
                <span class="material-symbols-outlined text-gray-400">search</span>
                <input type="text" placeholder="Cari email/nama..." class="bg-transparent border-none focus:ring-0 text-sm font-bold text-gray-600 dark:text-white placeholder-gray-400">
            </div>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="bg-emerald-50 text-emerald-800 px-4 py-3 rounded-xl mb-6 flex items-center gap-2 font-bold border border-emerald-100">
                <span class="material-symbols-outlined">check_circle</span>
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <div class="bg-white dark:bg-white/5 rounded-3xl border border-[#e5e7eb] dark:border-white/10 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 dark:bg-white/5 text-xs text-gray-500 uppercase tracking-wider font-bold">
                        <tr>
                            <th class="px-6 py-4">Nama</th>
                            <th class="px-6 py-4">Kontak</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Terdaftar</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        <?php if (empty($subscribers)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400 font-medium">
                                    Belum ada subscriber. Ajak jamaah untuk berlangganan!
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($subscribers as $s): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                <td class="px-6 py-4 font-bold text-[#111816] dark:text-gray-200">
                                    <?= esc($s['name'] ?? '-') ?>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-gray-600 dark:text-gray-300"><?= esc($s['email']) ?></span>
                                        <span class="text-xs text-gray-400"><?= esc($s['phone'] ?? '-') ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($s['is_active']): ?>
                                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                            Aktif
                                        </span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                            Tidak Aktif
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-500">
                                    <?= date('d M Y', strtotime($s['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="<?= base_url('dashboard/subscribers/delete/' . $s['id']) ?>" onclick="return confirm('Hapus subscriber ini?')" class="text-gray-400 hover:text-red-500 transition-colors tooltip" title="Hapus">
                                        <span class="material-symbols-outlined">delete</span>
                                    </a>
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
