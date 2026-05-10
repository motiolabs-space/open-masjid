<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<div class="px-8 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-black text-[#111816] dark:text-white tracking-tight">Laporan & Rekap</h1>
            <p class="text-[#608a7e]">Pusat laporan keuangan, kegiatan, dan aset masjid.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            
            <!-- Financial Report -->
            <div class="bg-white dark:bg-white/5 rounded-3xl border border-[#e5e7eb] dark:border-white/10 p-8 hover:shadow-xl transition-shadow group">
                <div class="size-14 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-3xl">payments</span>
                </div>
                <h3 class="text-xl font-black text-[#111816] dark:text-white mb-2">Laporan Keuangan</h3>
                <p class="text-sm text-gray-500 mb-6">Rekap pemasukan, pengeluaran, dan saldo kas per periode.</p>
                
                <form action="<?= base_url('dashboard/reports/finance') ?>" method="GET" target="_blank" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Mulai Tanggal</label>
                        <input type="date" name="start_date" id="fin_start" required value="<?= date('Y-m-01') ?>" class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-sm font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" id="fin_end" required value="<?= date('Y-m-d') ?>" class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-sm font-bold">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl flex items-center justify-center gap-2 transition-colors">
                            <span class="material-symbols-outlined">print</span>
                            Cetak
                        </button>
                        <button type="button" onclick="shareFinanceReport()" class="flex-1 bg-emerald-50 text-emerald-600 border border-emerald-200 font-bold py-3 rounded-xl flex items-center justify-center gap-2 hover:bg-emerald-100 transition-colors">
                            <span class="material-symbols-outlined text-xl">share</span>
                            Bagikan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Program Report -->
            <div class="bg-white dark:bg-white/5 rounded-3xl border border-[#e5e7eb] dark:border-white/10 p-8 hover:shadow-xl transition-shadow group">
                <div class="size-14 bg-blue-100 dark:bg-blue-900/30 text-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-3xl">event_note</span>
                </div>
                <h3 class="text-xl font-black text-[#111816] dark:text-white mb-2">Laporan Kegiatan</h3>
                <p class="text-sm text-gray-500 mb-6">Daftar program dan kegiatan masjid per bulan.</p>
                
                <form action="<?= base_url('dashboard/reports/program') ?>" method="GET" target="_blank" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Pilih Bulan</label>
                        <input type="month" name="month" required value="<?= date('Y-m') ?>" class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-sm font-bold">
                    </div>
                    <div class="h-[66px]"></div> <!-- Spacer to align buttons -->
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl flex items-center justify-center gap-2 transition-colors">
                        <span class="material-symbols-outlined">print</span>
                        Cetak Laporan
                    </button>
                </form>
            </div>

            <!-- Inventory Report -->
            <div class="bg-white dark:bg-white/5 rounded-3xl border border-[#e5e7eb] dark:border-white/10 p-8 hover:shadow-xl transition-shadow group">
                <div class="size-14 bg-purple-100 dark:bg-purple-900/30 text-purple-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-3xl">inventory_2</span>
                </div>
                <h3 class="text-xl font-black text-[#111816] dark:text-white mb-2">Laporan Aset</h3>
                <p class="text-sm text-gray-500 mb-6">Daftar inventaris dan kondisi aset masjid.</p>
                
                <form action="<?= base_url('dashboard/reports/inventory') ?>" method="GET" target="_blank" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Kondisi</label>
                        <select name="condition" class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-sm font-bold">
                            <option value="all">Semua Kondisi</option>
                            <option value="good">Baik (Good)</option>
                            <option value="damaged">Rusak (Damaged)</option>
                            <option value="lost">Hilang (Lost)</option>
                        </select>
                    </div>
                    <div class="h-[66px]"></div> <!-- Spacer -->
                    <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 rounded-xl flex items-center justify-center gap-2 transition-colors">
                        <span class="material-symbols-outlined">print</span>
                        Cetak Laporan
                    </button>
                </form>
            </div>

            <!-- QR Code Public Report -->
            <div class="bg-gradient-to-br from-[#11241d] to-[#08110e] rounded-3xl p-8 text-white relative overflow-hidden shadow-2xl">
                <div class="absolute top-0 right-0 p-32 bg-primary/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
                <div class="relative z-10">
                    <div class="size-14 bg-white/10 rounded-2xl flex items-center justify-center mb-6">
                        <span class="material-symbols-outlined text-3xl text-emerald-400">qr_code_2</span>
                    </div>
                    <h3 class="text-xl font-black mb-2">QR Code Laporan Publik</h3>
                    <p class="text-sm text-emerald-100/60 mb-8">Cetak dan tempel QR Code ini di area masjid agar jamaah bisa scan langsung via HP.</p>
                    
                    <?php 
                        $publicUrl = base_url(session()->get('masjid_username') . '/laporan');
                        $qrUrl = "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=" . urlencode($publicUrl) . "&choe=UTF-8";
                    ?>
                    
                    <div class="bg-white p-4 rounded-2xl w-full aspect-square mb-6">
                        <img src="<?= $qrUrl ?>" alt="QR Code Laporan" class="w-full h-full object-contain">
                    </div>

                    <div class="space-y-3">
                        <a href="<?= $qrUrl ?>" download="qr_laporan.png" target="_blank" class="w-full bg-emerald-500 hover:bg-emerald-400 text-white font-bold py-3 rounded-xl flex items-center justify-center gap-2 transition-all">
                            <span class="material-symbols-outlined text-sm">download</span>
                            Download QR
                        </a>
                        <button type="button" onclick="copyToClipboard('<?= $publicUrl ?>')" class="w-full bg-white/5 hover:bg-white/10 border border-white/10 text-white font-bold py-3 rounded-xl flex items-center justify-center gap-2 transition-all">
                            <span class="material-symbols-outlined text-sm">content_copy</span>
                            Salin Link
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function shareFinanceReport() {
        const start = document.getElementById('fin_start').value;
        const end = document.getElementById('fin_end').value;
        const url = `<?= base_url('dashboard/reports/finance') ?>?start_date=${start}&end_date=${end}`;
        const message = encodeURIComponent(`Assalamu'alaikum Warahmatullahi Wabarakatuh,\n\nBerikut adalah Laporan Keuangan Masjid periode ${start} s/d ${end}:\n\n${url}\n\nJazaakumullahu Khairan.`);
        
        window.open(`https://wa.me/?text=${message}`, '_blank');
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('Link berhasil disalin!');
        });
    }
</script>
<?= $this->endSection() ?>
