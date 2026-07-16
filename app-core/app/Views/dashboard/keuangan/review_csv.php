<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<div class="px-8 py-8">
    <?php if (session()->getFlashdata('error')): ?>
        <div class="bg-amber-50 text-amber-800 px-4 py-3 rounded-xl mb-6 flex items-center gap-2 font-bold border border-amber-100">
            <span class="material-symbols-outlined">info</span>
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black text-[#111816] dark:text-white tracking-tight">Review Hasil AI</h1>
            <p class="text-[#608a7e] mt-2">Periksa hasil kategorisasi mutasi bank oleh AI sebelum disimpan ke sistem.</p>
        </div>
        <div class="flex gap-3">
            <a href="<?= base_url('dashboard/keuangan/import-csv') ?>" class="px-6 py-2.5 rounded-xl border border-gray-300 text-sm font-bold hover:bg-gray-50 transition-all">
                Batalkan
            </a>
            <button type="submit" form="saveForm" class="px-6 py-2.5 bg-primary text-white rounded-xl text-sm font-bold hover:bg-emerald-900 transition-all shadow-lg shadow-primary/20 flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">save</span>
                Simpan Transaksi
            </button>
        </div>
    </div>

    <div class="bg-white dark:bg-white/5 rounded-[3rem] border border-[#e5e7eb] dark:border-white/10 overflow-hidden shadow-sm">
        <form id="saveForm" action="<?= base_url('dashboard/keuangan/import-csv/save') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-white/5">
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-[#608a7e]">Tanggal</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-[#608a7e]">Keterangan</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-[#608a7e]">Nominal</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-[#608a7e]">Tipe</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-primary flex items-center gap-1">
                                <span class="material-symbols-outlined text-xs">auto_awesome</span> Kategori (AI)
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        <?php foreach ($transactions as $idx => $t): ?>
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <input type="text" name="transactions[<?= $idx ?>][date]" value="<?= esc($t['date']) ?>" class="w-full bg-transparent border-none p-0 text-sm focus:ring-0">
                                </td>
                                <td class="px-6 py-4">
                                    <input type="text" name="transactions[<?= $idx ?>][description]" value="<?= esc($t['description']) ?>" class="w-full bg-transparent border-none p-0 text-sm font-bold focus:ring-0">
                                </td>
                                <td class="px-6 py-4">
                                    <input type="hidden" name="transactions[<?= $idx ?>][amount]" value="<?= esc($t['amount']) ?>">
                                    <span class="text-sm font-black <?= $t['type'] === 'pemasukan' ? 'text-emerald-600' : 'text-red-500' ?>">
                                        <?= $t['type'] === 'pemasukan' ? '+' : '-' ?> Rp <?= number_format($t['amount'], 0, ',', '.') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <input type="hidden" name="transactions[<?= $idx ?>][type]" value="<?= esc($t['type']) ?>">
                                    <span class="text-xs uppercase font-bold text-gray-500"><?= esc($t['type']) ?></span>
                                </td>
                                <td class="px-6 py-4">
                                    <?php
                                        // Highlight if AI suggested a new category that doesn't exist
                                        $isNewCat = empty($t['category_id']);
                                    ?>
                                    <select name="transactions[<?= $idx ?>][category_id]" class="w-full bg-[#f0f5f3] border-none rounded-xl text-xs font-bold focus:ring-2 focus:ring-primary p-2 <?= $isNewCat ? 'border border-amber-300 bg-amber-50 text-amber-800' : '' ?>">
                                        <option value="">-- Pilih --</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <?php if ($cat['type'] === $t['type']): ?>
                                                <option value="<?= $cat['id'] ?>" <?= ($t['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                                    <?= esc($cat['name']) ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                        <?php if ($isNewCat): ?>
                                            <option value="" selected>[BARU] <?= esc($t['suggested_category_name']) ?></option>
                                        <?php endif; ?>
                                    </select>
                                    <input type="hidden" name="transactions[<?= $idx ?>][suggested_category_name]" value="<?= esc($t['suggested_category_name']) ?>">
                                    
                                    <?php if ($isNewCat): ?>
                                        <div class="text-[10px] text-amber-600 mt-1 flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[10px]">info</span>
                                            AI menyarankan kategori baru ini
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
