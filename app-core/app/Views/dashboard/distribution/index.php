<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<div class="px-8 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="mb-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-black text-[#111816] dark:text-white tracking-tight">Penyaluran Bantuan</h1>
                <p class="text-[#608a7e]">Kelola data distribusi bantuan (ZISWAF/Sosial) kepada mustahik.</p>
            </div>
            
            <a href="<?= base_url('dashboard/distribution/new') ?>" class="bg-primary text-white px-5 py-3 rounded-xl font-bold hover:bg-emerald-900 transition-all shadow-lg shadow-primary/20 flex items-center gap-2">
                <span class="material-symbols-outlined text-xl">volunteer_activism</span>
                <span>Input Penyaluran</span>
            </a>
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
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Penerima (Warga)</th>
                            <th class="px-6 py-4">Jenis</th>
                            <th class="px-6 py-4">Detail (Jumlah/Barang)</th>
                            <th class="px-6 py-4">Program Sumber</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        <?php if (empty($distributions)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400 font-medium">
                                    Belum ada data penyaluran bantuan.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($distributions as $item): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group">
                                <td class="px-6 py-4 text-sm font-medium text-gray-500">
                                    <?= date('d M Y', strtotime($item['date'])) ?>
                                </td>
                                <td class="px-6 py-4 font-bold text-[#111816] dark:text-gray-200">
                                    <?php if ($item['warga_id']): ?>
                                        <div class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-gray-400 text-sm">person</span>
                                            <?= esc($item['warga_name']) ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-gray-400 italic">Umum / Non-Warga</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($item['type'] == 'money'): ?>
                                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-bold uppercase tracking-wide">Uang Tunai</span>
                                    <?php elseif ($item['type'] == 'goods'): ?>
                                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-[10px] font-bold uppercase tracking-wide">Barang</span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-[10px] font-bold uppercase tracking-wide">Jasa/Lainnya</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-[#111816] dark:text-gray-200">
                                    <?php if ($item['type'] == 'money'): ?>
                                        Rp <?= number_format($item['amount'], 0, ',', '.') ?>
                                    <?php else: ?>
                                        <?= esc($item['items']) ?>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <?= esc($item['program_name'] ?? '-') ?>
                                </td>
                                <td class="px-6 py-4 text-right flex justify-end gap-2">
                                    <?php if (!empty($item['evidence_photo'])): ?>
                                        <?php $storage = new \App\Libraries\Storage(); ?>
                                        <a href="<?= $storage->url($item['evidence_photo']) ?>" target="_blank" class="text-blue-400 hover:text-blue-500 tooltip" title="Lihat Bukti Foto">
                                            <span class="material-symbols-outlined">image</span>
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?= base_url('dashboard/distribution/edit/' . $item['id']) ?>" class="text-gray-400 hover:text-blue-500 transition-colors tooltip" title="Edit">
                                        <span class="material-symbols-outlined">edit</span>
                                    </a>
                                    <a href="<?= base_url('dashboard/distribution/delete/' . $item['id']) ?>" onclick="return confirm('Hapus data penyaluran ini?')" class="text-gray-400 hover:text-red-500 transition-colors tooltip" title="Hapus">
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
