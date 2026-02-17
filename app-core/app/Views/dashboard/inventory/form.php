<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<div class="px-8 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8 flex items-center gap-4">
            <a href="<?= base_url('dashboard/inventory') ?>" class="size-10 bg-white dark:bg-white/5 border border-[#dbe6e3] dark:border-white/10 rounded-xl flex items-center justify-center text-[#608a7e] hover:text-primary transition-all">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div>
                <h1 class="text-3xl font-black text-[#111816] dark:text-white tracking-tight"><?= $item ? 'Edit Aset' : 'Tambah Aset Baru' ?></h1>
                <p class="text-[#608a7e]">Kelola data inventaris masjid.</p>
            </div>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-red-50 text-red-800 px-4 py-3 rounded-xl mb-6 flex items-center gap-2 font-bold border border-red-100">
                <span class="material-symbols-outlined">error</span>
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('dashboard/inventory/save') ?>" method="POST" enctype="multipart/form-data" class="space-y-8">
            <?= csrf_field() ?>
            <?php if ($item): ?>
                <input type="hidden" name="id" value="<?= $item['id'] ?>">
            <?php endif; ?>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Main Form -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-white/5 rounded-3xl border border-[#e5e7eb] dark:border-white/10 p-8 space-y-6">
                        
                        <div>
                            <label class="block text-sm font-bold mb-2">Nama Barang / Aset <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="<?= old('name', $item['name'] ?? '') ?>" required placeholder="Contoh: Sound System Utama" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-2xl text-lg font-bold focus:ring-2 focus:ring-primary p-4">
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold mb-2">Merk / Brand</label>
                                <input type="text" name="brand" value="<?= old('brand', $item['brand'] ?? '') ?>" placeholder="Contoh: Yamaha, Sony" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl focus:ring-2 focus:ring-primary p-4 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-bold mb-2">Unit Satuan</label>
                                <input type="text" name="unit" value="<?= old('unit', $item['unit'] ?? 'pcs') ?>" placeholder="pcs, set, unit" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl focus:ring-2 focus:ring-primary p-4 text-sm">
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold mb-2">Jumlah Barang <span class="text-red-500">*</span></label>
                                <input type="number" name="quantity" value="<?= old('quantity', $item['quantity'] ?? 1) ?>" required min="1" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl focus:ring-2 focus:ring-primary p-4 font-bold">
                            </div>
                            <div>
                                <label class="block text-sm font-bold mb-2">Kondisi Saat Ini <span class="text-red-500">*</span></label>
                                <select name="condition" required class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl focus:ring-2 focus:ring-primary p-4 font-bold">
                                    <option value="good" <?= (old('condition', $item['condition'] ?? '') == 'good') ? 'selected' : '' ?>>Baik (Layak Pakai)</option>
                                    <option value="damaged_light" <?= (old('condition', $item['condition'] ?? '') == 'damaged_light') ? 'selected' : '' ?>>Rusak Ringan</option>
                                    <option value="damaged_heavy" <?= (old('condition', $item['condition'] ?? '') == 'damaged_heavy') ? 'selected' : '' ?>>Rusak Berat</option>
                                    <option value="lost" <?= (old('condition', $item['condition'] ?? '') == 'lost') ? 'selected' : '' ?>>Hilang</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-2">Deskripsi / Catatan</label>
                            <textarea name="description" rows="4" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl focus:ring-2 focus:ring-primary p-4" placeholder="Keterangan tambahan mengenai aset..."><?= old('description', $item['description'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-white/5 rounded-3xl border border-[#e5e7eb] dark:border-white/10 p-8 space-y-6">
                        <h3 class="font-bold text-lg border-b border-[#e5e7eb] pb-4">Info Perolehan (Opsional)</h3>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold mb-2">Tanggal Perolehan</label>
                                <input type="date" name="purchase_date" value="<?= old('purchase_date', $item['purchase_date'] ?? '') ?>" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl focus:ring-2 focus:ring-primary p-4 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-bold mb-2">Harga Perolehan (Rp)</label>
                                 <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#608a7e] font-bold">Rp</span>
                                    <input type="text" name="purchase_price" value="<?= isset($item['purchase_price']) ? number_format($item['purchase_price'], 0, ',', '.') : '' ?>" onkeyup="formatCurrency(this)" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl focus:ring-2 focus:ring-primary py-4 pl-12 pr-4 text-sm font-bold">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Photo -->
                <div class="space-y-6">
                    <div class="bg-white dark:bg-white/5 rounded-3xl border border-[#e5e7eb] dark:border-white/10 p-6 space-y-6">
                        <div>
                            <label class="block text-sm font-bold mb-2">Foto Barang</label>
                            <div class="relative aspect-square bg-[#f0f5f3] dark:bg-white/5 rounded-2xl border-2 border-dashed border-[#dbe6e3] dark:border-white/10 flex flex-col items-center justify-center text-[#608a7e] overflow-hidden group hover:border-primary transition-colors">
                                <?php if (!empty($item['photo'])): ?>
                                    <img id="thumbPrev" src="<?= $storage->url($item['photo']) ?>" class="absolute inset-0 size-full object-cover">
                                <?php else: ?>
                                    <img id="thumbPrev" src="" class="absolute inset-0 size-full object-cover hidden">
                                <?php endif; ?>
                                <div id="thumbPlaceholder" class="relative z-10 flex flex-col items-center group-hover:scale-110 transition-transform <?= !empty($item['photo']) ? 'opacity-0' : '' ?>">
                                    <span class="material-symbols-outlined text-3xl mb-1">add_a_photo</span>
                                    <span class="text-[10px] font-bold uppercase tracking-wider">Unggah Foto</span>
                                </div>
                                <input type="file" name="photo" class="absolute inset-0 opacity-0 cursor-pointer z-20" onchange="previewThumb(this)">
                            </div>
                            <p class="text-xs text-gray-400 mt-2 text-center">Format JPG/PNG, Max 2MB</p>
                        </div>

                        <button type="submit" class="w-full bg-primary text-white py-4 rounded-2xl font-black shadow-lg shadow-primary/20 hover:bg-emerald-900 transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined">save</span>
                            Simpan Data
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function previewThumb(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('thumbPrev');
                const ph = document.getElementById('thumbPlaceholder');
                img.src = e.target.result;
                img.classList.remove('hidden');
                ph.classList.add('opacity-0');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function formatCurrency(input) {
        let value = input.value.replace(/\D/g, '');
        let formatted = new Intl.NumberFormat('id-ID').format(value);
        input.value = formatted;
    }
</script>
<?= $this->endSection() ?>
