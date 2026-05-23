<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">AI Report Generator</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Buat narasi laporan otomatis dengan bantuan AI (SumoPod)</p>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="mb-4 bg-emerald-50 text-emerald-600 p-4 rounded-lg flex items-center gap-3">
            <span class="material-symbols-outlined">check_circle</span>
            <p><?= session()->getFlashdata('success') ?></p>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="mb-4 bg-red-50 text-red-600 p-4 rounded-lg flex items-center gap-3">
            <span class="material-symbols-outlined">error</span>
            <p><?= session()->getFlashdata('error') ?></p>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Generate -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <form action="<?= base_url('dashboard/reports/ai-generate') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Bulan & Tahun Laporan</label>
                        <input type="month" name="month" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none" value="<?= $month ?>" required>
                    </div>
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2.5 px-4 rounded-lg transition-colors flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-sm">auto_awesome</span>
                        Generate dengan AI
                    </button>
                    <p class="text-xs text-slate-400 mt-3 text-center">AI akan membaca rekap keuangan dan kegiatan di bulan yang dipilih, lalu menulis draft laporan.</p>
                </form>
            </div>
        </div>

        <!-- Editor & Publisher -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <h2 class="text-lg font-bold text-slate-800 dark:text-white mb-4">Hasil Copywriting (Draft)</h2>
                
                <?php if (!empty($generatedText)): ?>
                <form action="<?= base_url('dashboard/reports/ai-publish') ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="month" value="<?= $month ?>">
                    
                    <div class="mb-4">
                        <textarea name="content" rows="12" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none" required><?= esc($generatedText) ?></textarea>
                        <p class="text-xs text-slate-500 mt-2">Anda bisa merevisi tulisan di atas sebelum dipublikasikan. Format yang didukung adalah Markdown.</p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <button type="submit" name="action" value="news" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors">
                            <span class="material-symbols-outlined text-sm">public</span>
                            Publish ke Berita Masjid
                        </button>
                        <button type="submit" name="action" value="broadcast" class="bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors">
                            <span class="material-symbols-outlined text-sm">campaign</span>
                            Simpan sbg Draft Broadcast
                        </button>
                        <button type="button" onclick="copyToClipboard()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-5 py-2.5 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors border border-slate-200">
                            <span class="material-symbols-outlined text-sm">content_copy</span>
                            Salin (Untuk WhatsApp)
                        </button>
                    </div>
                </form>
                <?php else: ?>
                    <div class="text-center py-10">
                        <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">edit_note</span>
                        <p class="text-slate-500 text-sm">Pilih bulan dan klik Generate untuk melihat hasil tulisan AI di sini.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard() {
    const textarea = document.querySelector('textarea[name="content"]');
    if (textarea) {
        textarea.select();
        document.execCommand('copy');
        alert('Teks berhasil disalin!');
    }
}
</script>
<?= $this->endSection() ?>
