<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white"><?= esc($title) ?></h2>
        <p class="text-slate-500 text-sm mt-1">Catat bantuan yang diberikan ke Mustahik.</p>
    </div>
    <a href="<?= base_url('dashboard/distribution/history') ?>" class="text-slate-500 hover:text-slate-700 font-medium text-sm flex items-center gap-1">
        <span class="material-symbols-outlined text-sm">arrow_back</span> Kembali
    </a>
</div>

<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden max-w-2xl">
    <form action="<?= base_url('dashboard/distribution/save') ?>" method="POST" class="p-6">
        
        <div class="mb-6">
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Pilih Mustahik *</label>
            <select name="mustahik_id" required class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary">
                <option value="">-- Pilih Mustahik --</option>
                <?php foreach($mustahiks as $m): ?>
                    <option value="<?= $m['id'] ?>" <?= ($selected_mustahik_id == $m['id']) ? 'selected' : '' ?>>
                        <?= esc($m['name']) ?> (Skor AI: <?= $m['ai_score'] ?? 'N/A' ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Tanggal Penyaluran *</label>
                <input type="date" name="date" value="<?= date('Y-m-d') ?>" required class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nominal Rupiah *</label>
                <input type="number" name="amount" required placeholder="100000" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary">
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Keterangan / Rincian Bantuan</label>
            <textarea name="description" rows="3" placeholder="Contoh: Sembako beras 5kg dan uang tunai" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary"></textarea>
        </div>

        <div class="mt-8 flex justify-end gap-3">
            <a href="<?= base_url('dashboard/distribution/history') ?>" class="px-6 py-3 rounded-xl font-bold text-sm text-slate-600 hover:bg-slate-100 transition-colors">Batal</a>
            <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-6 py-3 rounded-xl font-bold text-sm shadow-lg shadow-primary/30 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">save</span> Simpan Catatan
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
