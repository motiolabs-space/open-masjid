<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<div class="px-8 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="mb-8 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="<?= base_url('dashboard/keuangan') ?>" class="size-10 bg-white dark:bg-white/5 border border-[#dbe6e3] dark:border-white/10 rounded-xl flex items-center justify-center text-[#608a7e] hover:text-primary transition-all">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <div>
                    <h1 class="text-3xl font-black text-[#111816] dark:text-white tracking-tight">Rekonsiliasi Mutasi Bank</h1>
                    <p class="text-[#608a7e]">Petakan transaksi bank langsung ke program masjid.</p>
                </div>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Sidebar: Upload Section -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white dark:bg-white/5 p-8 rounded-3xl border border-[#e5e7eb] dark:border-white/10 shadow-sm">
                    <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">upload_file</span>
                        Upload Mutasi
                    </h3>
                    <form action="<?= base_url('dashboard/keuangan/mutasi/upload') ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
                        <?= csrf_field() ?>
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-400 mb-2">Pilih Bank</label>
                            <select name="bank_type" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl focus:ring-2 focus:ring-primary p-4 font-bold">
                                <option value="bca">BCA (KlikBCA)</option>
                                <option value="bsi">BSI (Net Banking)</option>
                                <option value="mandiri">Mandiri (Livin/MCM)</option>
                                <option value="generic">Lainnya (Generic CSV)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-400 mb-2">File Mutasi (CSV)</label>
                            <input type="file" name="csv_file" accept=".csv" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                        </div>
                        <button type="submit" class="w-full bg-primary text-white py-4 rounded-2xl font-black shadow-lg shadow-primary/20 hover:bg-emerald-900 transition-all">
                            Proses File
                        </button>
                    </form>
                    <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-2xl border border-blue-100 dark:border-blue-800">
                        <p class="text-[10px] text-blue-600 dark:text-blue-300 leading-relaxed font-medium">
                            <strong>Tips:</strong> Ekspor mutasi dari internet banking Anda dalam format CSV. Pastikan kolom tanggal dan nominal terbaca dengan benar.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Main: Mapping Section -->
            <div class="lg:col-span-2">
                <?php if (empty($mutations)): ?>
                    <div class="bg-white dark:bg-white/5 rounded-3xl border border-dashed border-[#e5e7eb] dark:border-white/10 p-12 text-center">
                        <div class="size-20 bg-slate-100 dark:bg-white/5 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-300">
                            <span class="material-symbols-outlined text-4xl">rebase_edit</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-400">Belum ada data untuk dipetakan</h3>
                        <p class="text-slate-400 mt-2">Silakan unggah file mutasi bank Anda melalui panel di samping kiri.</p>
                    </div>
                <?php else: ?>
                    <form action="<?= base_url('dashboard/keuangan/mutasi/map') ?>" method="POST" class="space-y-6">
                        <?= csrf_field() ?>
                        <div class="bg-white dark:bg-white/5 rounded-3xl border border-[#e5e7eb] dark:border-white/10 overflow-hidden shadow-sm">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="bg-slate-50 dark:bg-white/5 text-slate-500 text-[10px] font-bold uppercase tracking-wider">
                                        <th class="px-6 py-4 w-10">
                                            <input type="checkbox" id="select-all" class="rounded text-primary focus:ring-primary">
                                        </th>
                                        <th class="px-6 py-4">Transaksi</th>
                                        <th class="px-6 py-4">Nominal</th>
                                        <th class="px-6 py-4">Petakan ke Program</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                                    <?php foreach ($mutations as $idx => $mut): 
                                        $hasAiSuggestion = !empty($mut['suggested_program_id']);
                                    ?>
                                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors <?= $hasAiSuggestion ? 'bg-emerald-50/50 dark:bg-emerald-900/10' : '' ?>">
                                        <td class="px-6 py-4">
                                            <input type="checkbox" name="selected[]" value="<?= $idx ?>" class="row-checkbox rounded text-primary focus:ring-primary" <?= $hasAiSuggestion ? 'checked' : '' ?>>
                                            <input type="hidden" name="date[<?= $idx ?>]" value="<?= $mut['date'] ?>">
                                            <input type="hidden" name="description[<?= $idx ?>]" value="<?= esc($mut['description']) ?>">
                                            <input type="hidden" name="amount[<?= $idx ?>]" value="<?= $mut['amount'] ?>">
                                            <?php // Tanpa ini penyimpanan tidak tahu arah uangnya, dan
                                                  // pengeluaran ikut tercatat sebagai pemasukan. ?>
                                            <input type="hidden" name="type[<?= $idx ?>]" value="<?= esc($mut['type']) ?>">
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-xs font-bold text-slate-900 dark:text-white"><?= date('d M Y', strtotime($mut['date'])) ?></p>
                                            <p class="text-[10px] text-slate-500 line-clamp-1 italic"><?= esc($mut['description']) ?></p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="font-black text-sm <?= $mut['type'] == 'CR' ? 'text-emerald-600' : 'text-red-500' ?>">
                                                <?= $mut['type'] == 'CR' ? '+' : '-' ?> Rp <?= number_format($mut['amount'], 0, ',', '.') ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="relative">
                                                <select name="program_id[<?= $idx ?>]" class="text-xs <?= $hasAiSuggestion ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800' : 'bg-slate-100 dark:bg-white/5' ?> border-none rounded-lg focus:ring-2 focus:ring-primary p-2 w-full font-bold">
                                                    <option value="">Pilih Program...</option>
                                                    <?php foreach ($programs as $prog): ?>
                                                        <option value="<?= $prog['id'] ?>" <?= (isset($mut['suggested_program_id']) && $mut['suggested_program_id'] == $prog['id']) ? 'selected' : '' ?>>
                                                            <?= esc($prog['title']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <?php if ($hasAiSuggestion): ?>
                                                    <div class="absolute -top-2 -right-2 bg-emerald-500 text-white text-[8px] font-black px-1.5 py-0.5 rounded-md shadow-sm flex items-center gap-0.5">
                                                        <span class="material-symbols-outlined text-[10px]">smart_toy</span> AI
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="bg-primary text-white px-12 py-4 rounded-2xl font-black shadow-lg shadow-primary/20 hover:bg-emerald-900 transition-all flex items-center gap-3">
                                <span class="material-symbols-outlined">sync_alt</span>
                                Simpan Mapping Keuangan
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('select-all')?.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
</script>
<?= $this->endSection() ?>
