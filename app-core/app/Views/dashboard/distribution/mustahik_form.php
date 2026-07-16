<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white"><?= esc($title) ?></h2>
        <p class="text-slate-500 text-sm mt-1">Data Mustahik akan dinilai secara otomatis oleh AI saat disimpan.</p>
    </div>
    <a href="<?= base_url('dashboard/distribution') ?>" class="text-slate-500 hover:text-slate-700 font-medium text-sm flex items-center gap-1">
        <span class="material-symbols-outlined text-sm">arrow_back</span> Kembali
    </a>
</div>

<?php // Kegagalan simpan mengembalikan pengurus ke formulir ini. Tanpa blok
      // berikut pesannya hilang tanpa jejak: layarnya tampak seperti tidak
      // terjadi apa-apa, padahal datanya tidak tersimpan. ?>
<?php if (session()->getFlashdata('error')): ?>
<div class="bg-rose-50 text-rose-600 p-4 rounded-xl mb-6 flex items-center gap-3 max-w-3xl">
    <span class="material-symbols-outlined">error</span>
    <p class="text-sm font-medium"><?= esc(session()->getFlashdata('error')) ?></p>
</div>
<?php endif; ?>

<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden max-w-3xl">
    <form action="<?= base_url('dashboard/distribution/mustahik/save') ?>" method="POST" class="p-6">
        <?php // Tanpa ini penyaringan CSRF menolak setiap pengiriman dengan 403:
              // mustahik tidak pernah bisa didaftarkan sama sekali. ?>
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= isset($mustahik) ? $mustahik['id'] : '' ?>">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Lengkap *</label>
                <input type="text" name="name" value="<?= isset($mustahik) ? esc($mustahik['name']) : '' ?>" required class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">NIK KTP</label>
                <input type="text" name="nik" value="<?= isset($mustahik) ? esc($mustahik['nik']) : '' ?>" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nomor HP/WA</label>
                <input type="text" name="phone" value="<?= isset($mustahik) ? esc($mustahik['phone']) : '' ?>" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Status Akun</label>
                <select name="status" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary">
                    <option value="active" <?= (isset($mustahik) && $mustahik['status'] == 'active') ? 'selected' : '' ?>>Aktif</option>
                    <option value="inactive" <?= (isset($mustahik) && $mustahik['status'] == 'inactive') ? 'selected' : '' ?>>Inaktif</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Alamat Domisili</label>
                <textarea name="address" rows="2" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary"><?= isset($mustahik) ? esc($mustahik['address']) : '' ?></textarea>
            </div>
        </div>

        <hr class="border-slate-100 dark:border-slate-800 my-6">
        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">Data Sosial Ekonomi (Untuk AI)</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Pendapatan Rata-rata per Bulan (Rp)</label>
                <input type="number" name="income_per_month" value="<?= isset($mustahik) ? $mustahik['income_per_month'] : '' ?>" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Jumlah Tanggungan (Orang)</label>
                <input type="number" name="dependents_count" value="<?= isset($mustahik) ? $mustahik['dependents_count'] : '' ?>" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Status Kepemilikan Rumah</label>
                <select name="house_ownership" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary">
                    <option value="milik_sendiri" <?= (isset($mustahik) && $mustahik['house_ownership'] == 'milik_sendiri') ? 'selected' : '' ?>>Milik Sendiri</option>
                    <option value="ngontrak" <?= (isset($mustahik) && $mustahik['house_ownership'] == 'ngontrak') ? 'selected' : '' ?>>Sewa / Ngontrak</option>
                    <option value="numpang" <?= (isset($mustahik) && $mustahik['house_ownership'] == 'numpang') ? 'selected' : '' ?>>Numpang (Keluarga/Orang Lain)</option>
                    <option value="lainnya" <?= (isset($mustahik) && $mustahik['house_ownership'] == 'lainnya') ? 'selected' : '' ?>>Lainnya</option>
                </select>
            </div>
        </div>

        <div class="mt-8 flex justify-end gap-3">
            <a href="<?= base_url('dashboard/distribution') ?>" class="px-6 py-3 rounded-xl font-bold text-sm text-slate-600 hover:bg-slate-100 transition-colors">Batal</a>
            <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-6 py-3 rounded-xl font-bold text-sm shadow-lg shadow-primary/30 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">smart_toy</span> Simpan & Generate AI Score
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
