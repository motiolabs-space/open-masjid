<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<div class="px-8 py-8">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-black text-[#111816] dark:text-white tracking-tight">Keuangan & Donasi</h1>
            <p class="text-[#608a7e]">Pantau saldo, catat pemasukan, dan kelola pengeluaran masjid.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= base_url('dashboard/keuangan/mutasi') ?>" class="flex items-center gap-2 px-5 py-2.5 rounded-xl border border-primary/20 bg-primary/5 text-primary text-sm font-bold hover:bg-primary/10 transition-all">
                <span class="material-symbols-outlined text-sm">sync_alt</span>
                Impor Mutasi Bank
            </a>
            <button onclick="openCategoryModal()" class="flex items-center gap-2 px-5 py-2.5 rounded-xl border border-[#dbe6e3] dark:border-white/10 text-sm font-bold hover:bg-white dark:hover:bg-white/5 transition-all">
                <span class="material-symbols-outlined text-sm">category</span>
                Kelola Kategori
            </button>
            <button onclick="openTransactionModal()" class="flex items-center gap-2 px-6 py-2.5 bg-primary text-white rounded-xl text-sm font-bold hover:bg-emerald-900 transition-all shadow-lg shadow-primary/20">
                <span class="material-symbols-outlined text-sm">add_card</span>
                Catat Transaksi
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <!-- Saldo Card -->
        <div class="bg-primary rounded-[2.5rem] p-8 text-white relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-emerald-200 text-sm font-bold mb-2 uppercase tracking-widest">Saldo Saat Ini</p>
                <h3 class="text-4xl font-black">Rp <?= number_format($summary['balance'], 0, ',', '.') ?></h3>
            </div>
            <span class="material-symbols-outlined absolute -bottom-6 -right-6 text-9xl text-white/10 group-hover:scale-110 transition-transform duration-700">account_balance_wallet</span>
        </div>

        <!-- Pemasukan Card -->
        <div class="bg-white dark:bg-white/5 rounded-[2.5rem] p-8 border border-[#e5e7eb] dark:border-white/10 relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[#608a7e] text-sm font-bold mb-2 uppercase tracking-widest">Total Pemasukan</p>
                <h3 class="text-3xl font-black text-emerald-600">Rp <?= number_format($summary['total_income'], 0, ',', '.') ?></h3>
            </div>
            <span class="material-symbols-outlined absolute -bottom-6 -right-6 text-9xl text-emerald-500/5 group-hover:scale-110 transition-transform duration-700">trending_up</span>
        </div>

        <!-- Pengeluaran Card -->
        <div class="bg-white dark:bg-white/5 rounded-[2.5rem] p-8 border border-[#e5e7eb] dark:border-white/10 relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[#608a7e] text-sm font-bold mb-2 uppercase tracking-widest">Total Pengeluaran</p>
                <h3 class="text-3xl font-black text-red-500">Rp <?= number_format($summary['total_expense'], 0, ',', '.') ?></h3>
            </div>
            <span class="material-symbols-outlined absolute -bottom-6 -right-6 text-9xl text-red-500/5 group-hover:scale-110 transition-transform duration-700">trending_down</span>
        </div>
    </div>

    <!-- Quick Stats & Recent Activity -->
    <div class="bg-white dark:bg-white/5 rounded-[3rem] border border-[#e5e7eb] dark:border-white/10 overflow-hidden shadow-sm">
        <div class="p-8 border-b border-gray-100 dark:border-white/5 flex items-center justify-between">
            <h3 class="font-black text-xl">Riwayat Transaksi</h3>
            <div class="flex items-center gap-4">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">search</span>
                    <input type="text" placeholder="Cari transaksi..." class="pl-10 pr-4 py-2 bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl text-xs focus:ring-1 focus:ring-primary w-64">
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-white/5">
                        <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-[#608a7e]">Tanggal</th>
                        <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-[#608a7e]">Keterangan</th>
                        <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-[#608a7e]">Kategori</th>
                        <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-[#608a7e]">Program</th>
                        <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-[#608a7e] text-right">Jumlah</th>
                        <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-[#608a7e] text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    <?php if (empty($transactions)): ?>
                        <tr>
                            <td colspan="6" class="px-8 py-20 text-center">
                                <span class="material-symbols-outlined text-6xl text-gray-200 mb-4 block">receipt_long</span>
                                <p class="text-[#608a7e] italic">Belum ada catatan transaksi.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $trans): ?>
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition-colors group">
                                <td class="px-8 py-5">
                                    <div class="text-sm font-bold"><?= date('d M Y', strtotime($trans['date'])) ?></div>
                                    <div class="text-[10px] text-gray-400 uppercase"><?= date('H:i', strtotime($trans['created_at'])) ?></div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-2">
                                        <div class="text-sm font-bold line-clamp-1"><?= esc($trans['description']) ?></div>
                                        <?php if ($trans['attachment']): ?>
                                            <?php $storage = new \App\Libraries\Storage(); ?>
                                            <a href="<?= $storage->url($trans['attachment']) ?>" target="_blank" class="text-primary hover:scale-110 transition-transform" title="Lihat Bukti">
                                                <span class="material-symbols-outlined text-sm">image</span>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($trans['donor_name']): ?>
                                        <div class="text-[10px] text-primary font-bold uppercase flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[10px]">person</span>
                                            <?= esc($trans['donor_name']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="px-3 py-1 bg-gray-100 dark:bg-white/10 rounded-full text-[10px] font-bold text-[#608a7e] uppercase tracking-widest">
                                        <?= esc($trans['category_name'] ?: 'Umum') ?>
                                    </span>
                                </td>
                                <td class="px-8 py-5">
                                    <?php if ($trans['program_title']): ?>
                                        <div class="text-[10px] font-bold text-emerald-600 uppercase flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[10px]">event_available</span>
                                            <?= esc($trans['program_title']) ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-[10px] text-gray-300">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="text-sm font-black <?= $trans['type'] == 'pemasukan' ? 'text-emerald-600' : 'text-red-500' ?>">
                                        <?= $trans['type'] == 'pemasukan' ? '+' : '-' ?> Rp <?= number_format($trans['amount'], 0, ',', '.') ?>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button onclick="editTransaction(<?= htmlspecialchars(json_encode($trans)) ?>)" class="size-8 bg-primary/5 text-primary rounded-lg flex items-center justify-center hover:bg-primary hover:text-white transition-all">
                                            <span class="material-symbols-outlined text-lg">edit</span>
                                        </button>
                                        <button onclick="deleteTransaction(<?= $trans['id'] ?>)" class="size-8 bg-red-50 text-red-500 rounded-lg flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">
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

<!-- Category Modal -->
<div id="categoryModal" class="fixed inset-0 z-[60] bg-black/60 backdrop-blur-sm hidden items-center justify-center p-6">
    <div class="bg-white dark:bg-[#11241d] w-full max-w-md rounded-[2.5rem] overflow-hidden shadow-2xl animate-in zoom-in duration-300">
        <div class="p-8 border-b border-gray-100 dark:border-white/10 flex items-center justify-between">
            <h3 class="text-xl font-bold">Kategori Keuangan</h3>
            <button onclick="closeCategoryModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-8">
            <form action="<?= base_url('dashboard/keuangan/category/save') ?>" method="POST" class="space-y-4 mb-8">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="cat_id">
                <input type="text" name="name" id="cat_name" placeholder="Nama kategori..." required class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl text-sm focus:ring-2 focus:ring-primary h-12">
                <div class="flex gap-2">
                    <select name="type" id="cat_type" class="flex-1 bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl text-sm focus:ring-2 focus:ring-primary h-12">
                        <option value="pemasukan">Pemasukan</option>
                        <option value="pengeluaran">Pengeluaran</option>
                    </select>
                    <button type="submit" class="px-6 bg-primary text-white rounded-xl font-bold hover:bg-emerald-900 transition-all shadow-lg shadow-primary/20">
                        Simpan
                    </button>
                </div>
            </form>

            <div class="space-y-3 max-h-[250px] overflow-y-auto pr-2">
                <?php foreach ($categories as $cat): ?>
                    <div class="flex items-center justify-between p-4 bg-[#f0f5f3] dark:bg-white/5 rounded-2xl group">
                        <div>
                            <span class="font-bold text-sm"><?= esc($cat['name']) ?></span>
                            <span class="text-[10px] block uppercase tracking-widest <?= $cat['type'] == 'pemasukan' ? 'text-emerald-500' : 'text-red-400' ?> font-black">
                                <?= $cat['type'] ?>
                            </span>
                        </div>
                        <div class="flex items-center gap-1">
                            <button onclick="editCat(<?= htmlspecialchars(json_encode($cat)) ?>)" class="size-8 text-primary hover:bg-primary/10 rounded-lg flex items-center justify-center transition-all">
                                <span class="material-symbols-outlined text-lg">edit</span>
                            </button>
                            <button onclick="deleteCat(<?= $cat['id'] ?>)" class="size-8 text-red-400 hover:text-red-500 hover:bg-red-50 rounded-lg flex items-center justify-center transition-all">
                                <span class="material-symbols-outlined text-lg">delete</span>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Transaction Modal -->
<div id="transactionModal" class="fixed inset-0 z-[60] bg-black/60 backdrop-blur-sm hidden items-center justify-center p-6">
    <div class="bg-white dark:bg-[#11241d] w-full max-w-2xl rounded-[3rem] overflow-hidden shadow-2xl animate-in zoom-in duration-300">
        <div class="p-8 border-b border-gray-100 dark:border-white/10 flex items-center justify-between">
            <h3 class="text-2xl font-black" id="transModalTitle">Catat Transaksi</h3>
            <button onclick="closeTransactionModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form action="<?= base_url('dashboard/keuangan/save') ?>" method="POST" enctype="multipart/form-data" class="p-8">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="trans_id">
            
            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-[#608a7e] mb-2">Jenis Transaksi</label>
                    <select name="type" id="trans_type" onchange="toggleDonorField()" required class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-primary">
                        <option value="pemasukan">Pemasukan (Donasi/Masuk)</option>
                        <option value="pengeluaran">Pengeluaran (Operasional/Keluar)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-[#608a7e] mb-2">Kategori</label>
                    <select name="category_id" id="trans_category" required class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-primary">
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" data-type="<?= $cat['type'] ?>"><?= esc($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-[#608a7e] mb-2">Tanggal</label>
                    <input type="date" name="date" id="trans_date" required value="<?= date('Y-m-d') ?>" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-[#608a7e] mb-2">Jumlah (Rp)</label>
                    <input type="text" name="amount" id="trans_amount" required placeholder="0" onkeyup="formatRupiah(this)" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-2xl p-4 text-lg font-black text-primary focus:ring-2 focus:ring-primary">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-black uppercase tracking-widest text-[#608a7e] mb-2">Keterangan / Deskripsi</label>
                <textarea name="description" id="trans_desc" rows="2" placeholder="Tulis catatan transaksi..." class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-2xl p-4 text-sm focus:ring-2 focus:ring-primary"></textarea>
            </div>

            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-[#608a7e] mb-2">Tautkan ke Program (Opsional)</label>
                    <select name="program_id" id="trans_program" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-2xl p-4 text-sm font-bold focus:ring-1 focus:ring-primary">
                        <option value="">-- Tidak Ada --</option>
                        <?php foreach ($programs as $prog): ?>
                            <option value="<?= $prog['id'] ?>"><?= esc($prog['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div id="donor_fields">
                    <label class="block text-xs font-black uppercase tracking-widest text-[#608a7e] mb-2">Nama Donatur (Opsional)</label>
                    <input type="text" name="donor_name" id="trans_donor" placeholder="Nama hamba Allah..." class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-primary">
                </div>
            </div>

            <div class="flex items-center justify-between gap-4 mt-8">
                <div class="flex-1">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <div class="size-10 bg-gray-50 dark:bg-white/5 rounded-xl flex items-center justify-center text-gray-400 group-hover:bg-primary/10 group-hover:text-primary transition-all">
                            <span class="material-symbols-outlined">attach_file</span>
                        </div>
                        <div class="text-xs text-[#608a7e]">
                            <p class="font-bold group-hover:text-primary transition-colors">Lampiran / Bukti</p>
                            <p id="file-name" class="font-normal italic">Opsional (JPG, PNG, PDF)</p>
                        </div>
                        <input type="file" name="attachment" class="hidden" onchange="document.getElementById('file-name').innerText = this.files[0].name">
                    </label>
                </div>
                <button type="submit" class="px-10 py-4 bg-primary text-white rounded-[1.5rem] font-black shadow-lg shadow-primary/20 hover:bg-emerald-900 transition-all">
                    Simpan Transaksi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCategoryModal() {
        document.getElementById('categoryModal').classList.remove('hidden');
        document.getElementById('categoryModal').classList.add('flex');
    }
    function closeCategoryModal() {
        document.getElementById('categoryModal').classList.add('hidden');
        document.getElementById('categoryModal').classList.remove('flex');
        // Reset form
        document.getElementById('cat_id').value = '';
        document.getElementById('cat_name').value = '';
    }

    function openTransactionModal() {
        document.getElementById('transactionModal').classList.remove('hidden');
        document.getElementById('transactionModal').classList.add('flex');
        document.getElementById('transModalTitle').innerText = 'Catat Transaksi';
    }
    function closeTransactionModal() {
        document.getElementById('transactionModal').classList.add('hidden');
        document.getElementById('transactionModal').classList.remove('flex');
        // Reset form
        document.getElementById('trans_id').value = '';
        document.getElementById('trans_amount').value = '';
        document.getElementById('trans_desc').value = '';
        document.getElementById('trans_donor').value = '';
    }

    function toggleDonorField() {
        const type = document.getElementById('trans_type').value;
        const donorField = document.getElementById('donor_fields');
        if (type === 'pengeluaran') {
            donorField.classList.add('opacity-50');
            document.getElementById('trans_donor').disabled = true;
        } else {
            donorField.classList.remove('opacity-50');
            document.getElementById('trans_donor').disabled = false;
        }

        // Filter categories based on type
        const catSelect = document.getElementById('trans_category');
        const options = catSelect.options;
        for (let i = 0; i < options.length; i++) {
            const opt = options[i];
            if (opt.value === '') continue;
            if (opt.getAttribute('data-type') === type) {
                opt.style.display = 'block';
            } else {
                opt.style.display = 'none';
            }
        }
        catSelect.value = '';
    }

    function formatRupiah(input) {
        let value = input.value.replace(/[^,\d]/g, "").toString();
        let split = value.split(",");
        let sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? "." : "";
            rupiah += separator + ribuan.join(".");
        }

        rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
        input.value = rupiah;
    }

    function editCat(cat) {
        document.getElementById('cat_id').value = cat.id;
        document.getElementById('cat_name').value = cat.name;
        document.getElementById('cat_type').value = cat.type;
    }

    async function deleteCat(id) {
        if (!confirm('Hapus kategori ini? Transaksi di dalamnya akan menjadi tanpa kategori.')) return;
        const formData = new FormData();
        formData.append('id', id);
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
        const response = await fetch('<?= base_url('dashboard/keuangan/category/delete') ?>', {
            method: 'POST',
            body: formData
        });
        const res = await response.json();
        if (res.status === 'success') location.reload();
        else alert(res.message);
    }

    function editTransaction(trans) {
        openTransactionModal();
        document.getElementById('transModalTitle').innerText = 'Edit Transaksi';
        document.getElementById('trans_id').value = trans.id;
        document.getElementById('trans_type').value = trans.type;
        document.getElementById('trans_category').value = trans.category_id;
        document.getElementById('trans_date').value = trans.date;
        document.getElementById('trans_amount').value = new Intl.NumberFormat('id-ID').format(trans.amount);
        document.getElementById('trans_desc').value = trans.description;
        document.getElementById('trans_program').value = trans.program_id;
        document.getElementById('trans_donor').value = trans.donor_name;
        toggleDonorField();
    }

    async function deleteTransaction(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus catatan transaksi ini?')) return;
        const formData = new FormData();
        formData.append('id', id);
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
        const response = await fetch('<?= base_url('dashboard/keuangan/delete') ?>', {
            method: 'POST',
            body: formData
        });
        const res = await response.json();
        if (res.status === 'success') location.reload();
        else alert(res.message);
    }
</script>
<?= $this->endSection() ?>
