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
                        <input type="date" name="start_date" required value="<?= date('Y-m-01') ?>" class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-sm font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" required value="<?= date('Y-m-d') ?>" class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2 text-sm font-bold">
                    </div>
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl flex items-center justify-center gap-2 transition-colors">
                        <span class="material-symbols-outlined">print</span>
                        Cetak Laporan
                    </button>
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

        </div>
    </div>
</div>
<?= $this->endSection() ?>
