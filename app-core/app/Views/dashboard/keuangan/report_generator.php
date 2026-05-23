<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<div class="px-8 py-8">
    <div class="mb-8">
        <a href="<?= base_url('dashboard/keuangan') ?>" class="text-[#608a7e] hover:text-primary flex items-center gap-2 mb-4 transition-colors">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Kembali ke Keuangan
        </a>
        <h1 class="text-3xl font-black text-[#111816] dark:text-white tracking-tight">AI Report Generator</h1>
        <p class="text-[#608a7e] mt-2">Buat narasi laporan bulanan secara otomatis dengan bantuan Artificial Intelligence.</p>
    </div>

    <div class="grid md:grid-cols-3 gap-8">
        <!-- Sidebar Summary -->
        <div class="col-span-1 space-y-6">
            <div class="bg-white dark:bg-white/5 rounded-3xl p-6 border border-[#e5e7eb] dark:border-white/10 shadow-sm">
                <h3 class="font-bold mb-4 text-[#111816] dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">data_usage</span>
                    Data Agregasi Bulan Ini
                </h3>
                
                <div class="space-y-4 text-sm">
                    <div class="flex justify-between items-center border-b border-gray-100 dark:border-white/5 pb-2">
                        <span class="text-gray-500">Pemasukan</span>
                        <span class="font-bold text-emerald-600">Rp <?= number_format($totalPemasukan, 0, ',', '.') ?></span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-100 dark:border-white/5 pb-2">
                        <span class="text-gray-500">Pengeluaran</span>
                        <span class="font-bold text-red-500">Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?></span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-100 dark:border-white/5 pb-2">
                        <span class="text-gray-500">Saldo Akhir</span>
                        <span class="font-bold text-primary">Rp <?= number_format($totalPemasukan - $totalPengeluaran, 0, ',', '.') ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Program Aktif</span>
                        <span class="font-bold"><?= $activePrograms ?> Kegiatan</span>
                    </div>
                </div>
            </div>

            <button id="btnGenerate" onclick="generateReport()" class="w-full px-6 py-4 bg-primary text-white rounded-2xl font-black shadow-lg shadow-primary/20 hover:bg-emerald-900 transition-all flex items-center justify-center gap-2">
                <span class="material-symbols-outlined" id="iconGenerate">auto_awesome</span>
                <span>Mulai Buat Laporan</span>
            </button>
        </div>

        <!-- Main Content (Result) -->
        <div class="col-span-2">
            <div class="bg-white dark:bg-white/5 rounded-3xl p-8 border border-[#e5e7eb] dark:border-white/10 shadow-sm h-full flex flex-col relative">
                
                <!-- Loading State -->
                <div id="loadingState" class="absolute inset-0 bg-white/80 dark:bg-[#11241d]/80 backdrop-blur-sm z-10 flex flex-col items-center justify-center rounded-3xl hidden">
                    <span class="material-symbols-outlined text-5xl text-primary animate-spin mb-4">progress_activity</span>
                    <p class="text-[#608a7e] font-bold animate-pulse">Menulis Laporan...</p>
                    <p class="text-xs text-gray-400 mt-2">SumoPod AI sedang merangkai kata</p>
                </div>

                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-[#111816] dark:text-white">Hasil Copywriting (Siap Broadcast)</h3>
                    <button onclick="copyResult()" class="text-xs font-bold text-primary bg-primary/10 px-4 py-2 rounded-lg hover:bg-primary/20 transition-colors flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">content_copy</span> Copy Teks
                    </button>
                </div>
                
                <textarea id="aiResult" class="w-full h-full min-h-[400px] bg-[#f0f5f3] dark:bg-black/20 border-none rounded-2xl p-6 text-sm text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-primary leading-relaxed resize-none" placeholder="Hasil tulisan AI akan muncul di sini..."></textarea>
            </div>
        </div>
    </div>
</div>

<script>
    async function generateReport() {
        const btn = document.getElementById('btnGenerate');
        const icon = document.getElementById('iconGenerate');
        const loading = document.getElementById('loadingState');
        const result = document.getElementById('aiResult');
        
        // UI Loading
        btn.disabled = true;
        btn.classList.add('opacity-50');
        loading.classList.remove('hidden');
        
        try {
            const formData = new FormData();
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
            
            const response = await fetch('<?= base_url('dashboard/keuangan/report') ?>', {
                method: 'POST',
                body: formData
            });
            
            const res = await response.json();
            
            if (res.status === 'success') {
                result.value = res.data;
            } else {
                alert('Gagal membuat laporan: ' + res.message);
            }
        } catch (error) {
            console.error(error);
            alert('Terjadi kesalahan jaringan.');
        } finally {
            // Revert UI Loading
            btn.disabled = false;
            btn.classList.remove('opacity-50');
            loading.classList.add('hidden');
        }
    }

    function copyResult() {
        const result = document.getElementById('aiResult');
        if (!result.value) return alert('Tidak ada teks untuk di-copy.');
        
        result.select();
        result.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(result.value);
        
        alert('Teks berhasil disalin ke clipboard!');
    }
</script>
<?= $this->endSection() ?>
