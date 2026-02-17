<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<div class="px-8 py-8">
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-[#111816] dark:text-white tracking-tight">Inventaris Masjid</h1>
            <p class="text-[#608a7e]">Manajemen aset dan barang milik masjid.</p>
        </div>
        <a href="<?= base_url('dashboard/inventory/new') ?>" class="bg-primary hover:bg-emerald-900 text-white px-6 py-3 rounded-xl font-bold transition-all shadow-lg shadow-primary/20 flex items-center gap-2">
            <span class="material-symbols-outlined">add</span>
            Tambah Aset
        </a>
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

    <div class="bg-white dark:bg-white/5 rounded-3xl border border-[#dbe6e3] dark:border-white/10 p-6 mb-8">
        <form action="" method="get" class="grid md:grid-cols-4 gap-4">
            <div class="md:col-span-2 relative">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#608a7e]">search</span>
                <input type="text" name="q" value="<?= esc($request->getGet('q') ?? '') ?>" placeholder="Cari nama aset..." class="w-full pl-12 pr-4 py-3 bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl focus:ring-2 focus:ring-primary transition-all font-bold text-[#111816] dark:text-white">
            </div>
            <div>
                <select name="condition" class="w-full px-4 py-3 bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl focus:ring-2 focus:ring-primary transition-all font-bold text-[#111816] dark:text-white appearance-none">
                    <option value="">Semua Kondisi</option>
                    <option value="good" <?= ($request->getGet('condition') == 'good') ? 'selected' : '' ?>>Baik</option>
                    <option value="damaged_light" <?= ($request->getGet('condition') == 'damaged_light') ? 'selected' : '' ?>>Rusak Ringan</option>
                    <option value="damaged_heavy" <?= ($request->getGet('condition') == 'damaged_heavy') ? 'selected' : '' ?>>Rusak Berat</option>
                    <option value="lost" <?= ($request->getGet('condition') == 'lost') ? 'selected' : '' ?>>Hilang</option>
                </select>
            </div>
            <button type="submit" class="bg-[#111816] dark:bg-black text-white px-6 py-3 rounded-xl font-bold hover:opacity-90 transition-all flex items-center justify-center gap-2">
                <span class="material-symbols-outlined">filter_list</span>
                Filter
            </button>
        </form>
    </div>

    <div class="bg-white dark:bg-white/5 rounded-3xl border border-[#dbe6e3] dark:border-white/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#f0f5f3] dark:bg-white/5 text-[#111816] dark:text-white border-b border-[#dbe6e3] dark:border-white/10">
                        <th class="p-6 text-xs font-black uppercase tracking-widest">Foto</th>
                        <th class="p-6 text-xs font-black uppercase tracking-widest">Nama Aset</th>
                        <th class="p-6 text-xs font-black uppercase tracking-widest">Kondisi</th>
                        <th class="p-6 text-xs font-black uppercase tracking-widest">Jumlah</th>
                        <th class="p-6 text-xs font-black uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#dbe6e3] dark:divide-white/10">
                    <?php if (empty($inventory)): ?>
                        <tr>
                            <td colspan="5" class="p-12 text-center text-[#608a7e]">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="material-symbols-outlined text-4xl opacity-50">inventory_2</span>
                                    <p class="font-bold">Belum ada data inventaris.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($inventory as $item): ?>
                            <tr class="hover:bg-[#f0f5f3] dark:hover:bg-white/5 transition-colors group">
                                <td class="p-6">
                                    <div class="size-16 rounded-xl bg-gray-100 overflow-hidden border border-gray-200">
                                        <?php if ($item['photo']): ?>
                                            <img src="<?= $storage->url($item['photo']) ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                                <span class="material-symbols-outlined">image</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="p-6 align-middle">
                                    <h3 class="font-bold text-[#111816] dark:text-white"><?= esc($item['name']) ?></h3>
                                    <?php if ($item['brand']): ?>
                                        <p class="text-xs text-[#608a7e]">Merk: <?= esc($item['brand']) ?></p>
                                    <?php endif; ?>
                                </td>
                                <td class="p-6 align-middle">
                                    <?php
                                    $badges = [
                                        'good'          => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'label' => 'Baik'],
                                        'damaged_light' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'label' => 'Rusak Ringan'],
                                        'damaged_heavy' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'label' => 'Rusak Berat'],
                                        'lost'          => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => 'Hilang'],
                                    ];
                                    $status = $badges[$item['condition']] ?? $badges['good'];
                                    ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold <?= $status['bg'] ?> <?= $status['text'] ?>">
                                        <?= $status['label'] ?>
                                    </span>
                                </td>
                                <td class="p-6 align-middle">
                                    <span class="font-bold text-lg"><?= esc($item['quantity']) ?></span>
                                    <span class="text-xs text-gray-500 uppercase"><?= esc($item['unit']) ?></span>
                                </td>
                                <td class="p-6 align-middle text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="<?= base_url('dashboard/inventory/edit/' . $item['id']) ?>" class="size-10 rounded-xl bg-white border border-[#dbe6e3] flex items-center justify-center text-[#608a7e] hover:bg-primary hover:text-white hover:border-primary transition-all" title="Edit">
                                            <span class="material-symbols-outlined text-lg">edit</span>
                                        </a>
                                        <button onclick="confirmDelete('<?= base_url('dashboard/inventory/delete/' . $item['id']) ?>', '<?= esc($item['name']) ?>')" class="size-10 rounded-xl bg-white border border-[#dbe6e3] flex items-center justify-center text-red-500 hover:bg-red-500 hover:text-white hover:border-red-500 transition-all" title="Hapus">
                                            <span class="material-symbols-outlined text-lg">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function confirmDelete(url, name) {
    if (confirm('Apakah Anda yakin ingin menghapus data aset "' + name + '"?')) {
        window.location.href = url;
    }
}
</script>
<?= $this->endSection() ?>
