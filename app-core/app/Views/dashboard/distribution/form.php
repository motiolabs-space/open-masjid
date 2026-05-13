<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<div class="px-8 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="mb-8 flex items-center gap-4">
            <a href="<?= base_url('dashboard/distribution') ?>" class="size-10 flex items-center justify-center bg-white dark:bg-white/5 rounded-xl border border-gray-200 dark:border-white/10 hover:bg-gray-50 text-gray-500 transition-colors">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div>
                <h1 class="text-3xl font-black text-[#111816] dark:text-white tracking-tight"><?= isset($item) ? 'Edit' : 'Input' ?> Penyaluran</h1>
                <p class="text-[#608a7e]">Catat detail distribusi bantuan kepada penerima.</p>
            </div>
        </div>

        <form action="<?= base_url('dashboard/distribution/save') ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
            <?= csrf_field() ?>
            <?php if (isset($item)): ?>
                <input type="hidden" name="id" value="<?= $item['id'] ?>">
            <?php endif; ?>
            
            <div class="bg-white dark:bg-white/5 p-8 rounded-3xl border border-[#e5e7eb] dark:border-white/10 space-y-6">
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold mb-2 text-[#111816] dark:text-white">Tanggal Penyaluran</label>
                        <input type="date" name="date" required value="<?= isset($item) ? $item['date'] : date('Y-m-d') ?>" class="w-full bg-[#f0f5f3] dark:bg-background-dark border-none rounded-xl focus:ring-2 focus:ring-primary p-4 font-bold text-[#111816] dark:text-white">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold mb-2 text-[#111816] dark:text-white">Jenis Bantuan</label>
                        <select name="type" id="type" onchange="toggleTypeFields()" class="w-full bg-[#f0f5f3] dark:bg-background-dark border-none rounded-xl focus:ring-2 focus:ring-primary p-4 font-bold text-[#111816] dark:text-white">
                            <option value="money" <?= (isset($item) && $item['type'] == 'money') ? 'selected' : '' ?>>Uang Tunai (Money)</option>
                            <option value="goods" <?= (isset($item) && $item['type'] == 'goods') ? 'selected' : '' ?>>Barang (Goods)</option>
                            <option value="service" <?= (isset($item) && $item['type'] == 'service') ? 'selected' : '' ?>>Jasa/Lainnya</option>
                        </select>
                    </div>
                </div>

                <div id="field-amount" class="<?= (isset($item) && $item['type'] == 'goods') ? 'hidden' : '' ?>">
                    <label class="block text-sm font-bold mb-2 text-[#111816] dark:text-white">Nominal (Rp)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-gray-500">Rp</span>
                        <input type="text" name="amount" id="amount-input" value="<?= isset($item) ? number_format($item['amount'], 0, '.', '.') : '0' ?>" class="w-full bg-[#f0f5f3] dark:bg-background-dark border-none rounded-xl focus:ring-2 focus:ring-primary pl-12 pr-4 py-4 font-bold text-[#111816] dark:text-white text-lg" onkeyup="formatRupiah(this)">
                    </div>
                </div>

                <div id="field-items" class="<?= (!isset($item) || $item['type'] == 'money') ? 'hidden' : '' ?>">
                    <label class="block text-sm font-bold mb-2 text-[#111816] dark:text-white">Rincian Barang</label>
                    <textarea name="items" rows="3" class="w-full bg-[#f0f5f3] dark:bg-background-dark border-none rounded-xl focus:ring-2 focus:ring-primary p-4 font-medium text-[#111816] dark:text-white" placeholder="Contoh: Beras 5kg, Minyak 2L, Mie Instan 1 Dus"><?= isset($item) ? esc($item['items']) : '' ?></textarea>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold mb-2 text-[#111816] dark:text-white">Penerima (Warga)</label>
                        <select name="warga_id" class="w-full bg-[#f0f5f3] dark:bg-background-dark border-none rounded-xl focus:ring-2 focus:ring-primary p-4 font-bold text-[#111816] dark:text-white cursor-pointer">
                            <option value="">-- Umum / Non-Warga --</option>
                            <?php foreach ($warga as $w): ?>
                                <option value="<?= $w['id'] ?>" <?= (isset($item) && $item['warga_id'] == $w['id']) || (isset($selectedWargaId) && $selectedWargaId == $w['id']) ? 'selected' : '' ?>>
                                    <?= esc($w['name']) ?> (<?= esc($w['nik']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="text-xs text-gray-500 mt-2">Pilih dari Database Warga atau kosongkan untuk umum.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2 text-[#111816] dark:text-white">Sumber Program (Opsional)</label>
                        <select name="program_id" class="w-full bg-[#f0f5f3] dark:bg-background-dark border-none rounded-xl focus:ring-2 focus:ring-primary p-4 font-bold text-[#111816] dark:text-white cursor-pointer">
                            <option value="">-- Tidak Terkait Program --</option>
                            <?php foreach ($programs as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= (isset($item) && $item['program_id'] == $p['id']) ? 'selected' : '' ?>>
                                    <?= esc($p['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2 text-[#111816] dark:text-white">Keterangan Tambahan</label>
                    <textarea name="description" rows="2" class="w-full bg-[#f0f5f3] dark:bg-background-dark border-none rounded-xl focus:ring-2 focus:ring-primary p-4 font-medium text-[#111816] dark:text-white" placeholder="Catatan tambahan..."><?= isset($item) ? esc($item['description']) : '' ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2 text-[#111816] dark:text-white">Bukti Foto Penyaluran</label>
                    <?php if (isset($item) && !empty($item['evidence_photo'])): ?>
                        <?php $storage = new \App\Libraries\Storage(); ?>
                        <div class="mb-4 relative w-32 aspect-square rounded-xl overflow-hidden group">
                            <img src="<?= $storage->url($item['evidence_photo']) ?>" class="size-full object-cover">
                        </div>
                    <?php endif; ?>
                    <div class="relative max-w-sm">
                        <input type="file" name="evidence_photo" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div class="bg-[#f0f5f3] dark:bg-background-dark border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-4 flex items-center justify-center text-gray-500 gap-2">
                            <span class="material-symbols-outlined">add_a_photo</span>
                            <span class="text-sm font-bold">Upload Foto</span>
                        </div>
                    </div>
                </div>

                <?php if (!isset($item)): ?>
                <div id="expense-option" class="bg-yellow-50 text-yellow-800 p-4 rounded-xl flex items-start gap-3 border border-yellow-100">
                    <input type="checkbox" name="create_expense" value="1" id="create_expense" class="mt-1 rounded text-primary focus:ring-primary">
                    <label for="create_expense" class="text-sm font-medium cursor-pointer select-none">
                        <b>Otomatis Catat Pengeluaran?</b><br>
                        Centang ini untuk membuat transaksi <code>Pengeluaran (Expense)</code> di modul Keuangan secara otomatis sesuai nominal di atas.
                    </label>
                </div>
                <?php endif; ?>

            </div>

            <div class="flex justify-end gap-4">
                <a href="<?= base_url('dashboard/distribution') ?>" class="px-6 py-4 rounded-xl font-bold text-gray-500 hover:bg-gray-100 transition-all">Batal</a>
                <button type="submit" class="bg-primary text-white px-8 py-4 rounded-xl font-bold hover:bg-emerald-900 transition-all shadow-lg shadow-primary/20 flex items-center gap-2">
                    <span class="material-symbols-outlined">save</span>
                    <span>Simpan Data</span>
                </button>
            </div>

        </form>
    </div>
</div>

<script>
    function toggleTypeFields() {
        const type = document.getElementById('type').value;
        const fieldAmount = document.getElementById('field-amount');
        const fieldItems = document.getElementById('field-items');
        const expenseOption = document.getElementById('expense-option');

        if (type === 'money') {
            fieldAmount.classList.remove('hidden');
            fieldItems.classList.add('hidden');
            if(expenseOption) expenseOption.classList.remove('hidden');
        } else {
            fieldAmount.classList.add('hidden');
            fieldItems.classList.remove('hidden');
            if(expenseOption) expenseOption.classList.add('hidden');
        }
    }

    function formatRupiah(input) {
        let value = input.value.replace(/[^,\d]/g, '').toString();
        let split = value.split(',');
        let sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        input.value = rupiah;
    }
</script>
<?= $this->endSection() ?>
