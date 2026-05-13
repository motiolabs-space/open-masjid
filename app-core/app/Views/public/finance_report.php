<?= $this->extend('layout/masjid_public') ?>

<?= $this->section('content') ?>
<section class="py-24 bg-background-light dark:bg-background-dark min-h-screen">
    <div class="max-w-[1000px] mx-auto px-6">
        <!-- Header -->
        <div class="mb-12 flex flex-col md:flex-row justify-between items-end gap-6">
            <div>
                <a href="<?= base_url($masjid['username']) ?>" class="inline-flex items-center gap-2 text-primary font-bold mb-4 hover:underline">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    Kembali ke Profil
                </a>
                <h1 class="text-4xl font-black text-[#111816] dark:text-white tracking-tight">Laporan Transparansi</h1>
                <p class="text-[#608a7e] text-lg">Periode: <?= date('d M Y', strtotime($filters['start'])) ?> - <?= date('d M Y', strtotime($filters['end'])) ?></p>
            </div>
            <div class="flex gap-2">
                <button onclick="window.print()" class="px-5 py-3 bg-white dark:bg-white/5 border border-[#dbe6e3] dark:border-white/10 rounded-xl font-bold flex items-center gap-2 hover:bg-slate-50 transition-all">
                    <span class="material-symbols-outlined text-sm">print</span>
                    Cetak
                </button>
            </div>
        </div>

        <!-- Impact Distribution Section -->
        <?php if (!empty($expenditureByCat)): ?>
        <div class="mb-12">
            <h3 class="text-xl font-bold mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">volunteer_activism</span>
                Distribusi Kebaikan (Amanah Terpenuhi)
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <?php foreach ($expenditureByCat as $item): ?>
                <div class="bg-white dark:bg-white/5 border border-[#dbe6e3] dark:border-white/10 p-6 rounded-3xl group hover:border-primary transition-all">
                    <p class="text-[#608a7e] text-[10px] font-black uppercase tracking-widest mb-2"><?= esc($item['name']) ?></p>
                    <h4 class="text-xl font-black">Rp <?= number_format($item['total'], 0, ',', '.') ?></h4>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Summary Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="bg-primary p-8 rounded-[2rem] text-white shadow-xl relative overflow-hidden group">
                <p class="text-emerald-200 text-[10px] font-black uppercase tracking-widest mb-1">Amanah Kas (Saldo)</p>
                <h3 class="text-3xl font-black">Rp <?= number_format($summary['balance'], 0, ',', '.') ?></h3>
                <span class="material-symbols-outlined absolute -bottom-4 -right-4 text-7xl opacity-10 group-hover:scale-110 transition-transform">account_balance_wallet</span>
            </div>
            <div class="bg-white dark:bg-white/5 border border-[#dbe6e3] dark:border-white/10 p-8 rounded-[2rem] group">
                <p class="text-[#608a7e] text-[10px] font-black uppercase tracking-widest mb-1">Amanah Diterima (Pemasukan)</p>
                <h3 class="text-3xl font-black text-emerald-600">Rp <?= number_format(array_sum(array_column(array_filter($transactions, fn($t) => $t['type'] == 'pemasukan'), 'amount')), 0, ',', '.') ?></h3>
            </div>
            <div class="bg-white dark:bg-white/5 border border-[#dbe6e3] dark:border-white/10 p-8 rounded-[2rem] group">
                <p class="text-[#608a7e] text-[10px] font-black uppercase tracking-widest mb-1">Manfaat Disalurkan (Pengeluaran)</p>
                <h3 class="text-3xl font-black text-red-500">Rp <?= number_format(array_sum(array_column(array_filter($transactions, fn($t) => $t['type'] == 'pengeluaran'), 'amount')), 0, ',', '.') ?></h3>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="bg-white dark:bg-white/5 rounded-[2.5rem] border border-[#dbe6e3] dark:border-white/10 overflow-hidden shadow-sm">
            <div class="p-8 border-b border-slate-100 dark:border-white/5">
                <h3 class="font-bold text-xl">Rincian Transaksi</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-white/5 text-slate-500 text-[10px] font-black uppercase tracking-widest">
                            <th class="px-8 py-4">Tanggal</th>
                            <th class="px-8 py-4">Keterangan</th>
                            <th class="px-8 py-4">Kategori</th>
                            <th class="px-8 py-4 text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                        <?php if (empty($transactions)): ?>
                            <tr>
                                <td colspan="4" class="px-8 py-20 text-center text-slate-400 italic">
                                    Tidak ada transaksi pada periode ini.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($transactions as $t): ?>
                                <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                    <td class="px-8 py-5">
                                        <div class="text-sm font-bold"><?= date('d/m/Y', strtotime($t['date'])) ?></div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="text-sm font-medium"><?= esc($t['description']) ?></div>
                                        <?php if ($t['program_title']): ?>
                                            <div class="text-[10px] text-primary font-bold uppercase mt-1 flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[10px]">event</span>
                                                <?= esc($t['program_title']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="text-[10px] font-bold uppercase text-slate-500 bg-slate-100 dark:bg-white/10 px-2 py-1 rounded">
                                            <?= esc($t['category_name'] ?: 'Umum') ?>
                                        </span>
                                    </td>
                                    <td class="px-8 py-5 text-right">
                                        <div class="text-sm font-black <?= $t['type'] == 'pemasukan' ? 'text-emerald-600' : 'text-red-500' ?>">
                                            <?= $t['type'] == 'pemasukan' ? '+' : '-' ?> <?= number_format($t['amount'], 0, ',', '.') ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="p-8 bg-slate-50 dark:bg-white/5 text-center">
                <p class="text-[10px] text-slate-400 font-medium italic">
                    "Dan apa saja harta yang baik yang kamu nafkahkan (di jalan Allah), maka pahalanya itu untuk kamu sendiri. Dan janganlah kamu membelanjakan sesuatu melainkan karena mencari keridhaan Allah..." (QS. Al-Baqarah: 272)
                </p>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
