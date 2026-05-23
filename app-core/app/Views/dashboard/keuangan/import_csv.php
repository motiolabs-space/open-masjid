<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<div class="px-8 py-8">
    <div class="mb-8">
        <a href="<?= base_url('dashboard/keuangan') ?>" class="text-[#608a7e] hover:text-primary flex items-center gap-2 mb-4 transition-colors">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Kembali ke Keuangan
        </a>
        <h1 class="text-3xl font-black text-[#111816] dark:text-white tracking-tight">Impor Mutasi (AI)</h1>
        <p class="text-[#608a7e] mt-2">Unggah file CSV dari mutasi bank (BCA, Mandiri, BSI, dll) dan biarkan AI mengkategorikannya secara otomatis.</p>
    </div>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="p-4 mb-6 text-sm text-red-800 rounded-2xl bg-red-50 dark:bg-red-900/20 dark:text-red-400">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="bg-white dark:bg-white/5 rounded-[3rem] border border-[#e5e7eb] dark:border-white/10 p-10 max-w-2xl mx-auto text-center shadow-sm">
        <div class="mb-8">
            <span class="material-symbols-outlined text-7xl text-primary/30">upload_file</span>
        </div>
        
        <form action="<?= base_url('dashboard/keuangan/import-csv/process') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="mb-8">
                <label class="flex flex-col items-center justify-center w-full h-64 border-2 border-dashed border-gray-300 dark:border-white/20 rounded-3xl cursor-pointer hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <span class="material-symbols-outlined text-4xl text-gray-400 group-hover:text-primary mb-3 transition-colors">csv</span>
                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-bold">Klik untuk unggah</span> atau drag and drop</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">File CSV Mutasi Bank (Format: Tanggal, Deskripsi, Jumlah, Tipe)</p>
                        <p id="file-name" class="mt-4 font-bold text-primary"></p>
                    </div>
                    <input type="file" name="csv_file" class="hidden" accept=".csv" required onchange="document.getElementById('file-name').innerText = this.files[0].name" />
                </label>
            </div>
            
            <button type="submit" class="w-full px-6 py-4 bg-primary text-white rounded-2xl font-black shadow-lg shadow-primary/20 hover:bg-emerald-900 transition-all flex items-center justify-center gap-2">
                <span class="material-symbols-outlined">auto_awesome</span>
                Proses dengan AI
            </button>
        </form>

        <div class="mt-10 text-left bg-gray-50 dark:bg-white/5 rounded-2xl p-6">
            <h4 class="font-bold text-sm mb-2 text-[#111816] dark:text-white">Format Kolom CSV yang didukung:</h4>
            <ul class="text-xs text-[#608a7e] space-y-2 list-disc list-inside">
                <li>Kolom 1: Tanggal Transaksi (DD/MM/YYYY atau YYYY-MM-DD)</li>
                <li>Kolom 2: Deskripsi / Keterangan Mutasi</li>
                <li>Kolom 3: Jumlah Nominal (Rp)</li>
                <li>Kolom 4: Tipe (Masuk / Keluar)</li>
            </ul>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
