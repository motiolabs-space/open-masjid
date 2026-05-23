<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
            <span class="material-symbols-outlined text-emerald-500">policy</span>
            Virtual Auditor
        </h2>
        <p class="text-slate-500 text-sm mt-1">Deteksi anomali pengeluaran bulanan menggunakan kecerdasan buatan.</p>
    </div>
</div>

<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 mb-6">
    <div class="flex flex-col md:flex-row items-center gap-4">
        <div class="w-full md:w-1/3">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Pilih Bulan Audit</label>
            <select id="monthSelector" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:border-emerald-500 focus:ring-emerald-500">
                <option value="">-- Pilih Bulan --</option>
                <?php foreach ($months as $m): ?>
                    <?php
                        $dateObj = \DateTime::createFromFormat('Y-m', $m['month_year']);
                        $formattedMonth = strftime('%B %Y', $dateObj->getTimestamp());
                    ?>
                    <option value="<?= $m['month_year'] ?>"><?= $formattedMonth ?></option>
                <?php endforeach; ?>
                <?php if(empty($months)): ?>
                    <option value="" disabled>Belum ada data transaksi</option>
                <?php endif; ?>
            </select>
        </div>
        <div class="w-full md:w-auto md:pt-7">
            <button id="btnRunAudit" class="w-full md:w-auto bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-lg font-medium transition-all flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-lg">search</span>
                Jalankan AI Auditor
            </button>
        </div>
    </div>
</div>

<!-- Loading State -->
<div id="loadingState" class="hidden">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-10 flex flex-col items-center justify-center text-center">
        <span class="material-symbols-outlined text-emerald-500 text-6xl animate-pulse mb-4">memory</span>
        <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2">AI Sedang Menganalisis...</h3>
        <p class="text-slate-500">Virtual Auditor sedang membandingkan pengeluaran bulan ini dengan data historis 3 bulan ke belakang.</p>
    </div>
</div>

<!-- Results Area -->
<div id="resultsArea" class="hidden space-y-4">
    <div class="flex items-center justify-between mb-2">
        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Hasil Temuan Virtual Auditor</h3>
        <span id="badgeNoAnomaly" class="hidden bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-bold">Aman (Tidak Ada Anomali)</span>
    </div>
    
    <div id="anomaliesContainer" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- JS will populate cards here -->
    </div>
</div>

<!-- Context Area -->
<div id="contextArea" class="hidden mt-8">
    <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">Konteks Data (Raw)</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-slate-50 dark:bg-slate-800 rounded-lg p-4 text-xs font-mono overflow-auto max-h-64 border border-slate-200 dark:border-slate-700">
            <p class="font-bold text-slate-700 dark:text-slate-300 mb-2">Pengeluaran Bulan Ini</p>
            <pre id="rawCurrent" class="text-slate-600 dark:text-slate-400"></pre>
        </div>
        <div class="bg-slate-50 dark:bg-slate-800 rounded-lg p-4 text-xs font-mono overflow-auto max-h-64 border border-slate-200 dark:border-slate-700">
            <p class="font-bold text-slate-700 dark:text-slate-300 mb-2">Rata-rata Historis (3 Bulan)</p>
            <pre id="rawHistorical" class="text-slate-600 dark:text-slate-400"></pre>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('btnRunAudit').addEventListener('click', async function() {
    const monthYear = document.getElementById('monthSelector').value;
    if (!monthYear) {
        Swal.fire({
            icon: 'warning',
            title: 'Oops...',
            text: 'Silakan pilih bulan audit terlebih dahulu!'
        });
        return;
    }

    const btn = this;
    const loadingState = document.getElementById('loadingState');
    const resultsArea = document.getElementById('resultsArea');
    const anomaliesContainer = document.getElementById('anomaliesContainer');
    const badgeNoAnomaly = document.getElementById('badgeNoAnomaly');
    const contextArea = document.getElementById('contextArea');
    
    // UI Loading
    btn.disabled = true;
    btn.innerHTML = '<span class="material-symbols-outlined text-lg animate-spin">sync</span> Memproses...';
    resultsArea.classList.add('hidden');
    contextArea.classList.add('hidden');
    loadingState.classList.remove('hidden');
    anomaliesContainer.innerHTML = '';
    badgeNoAnomaly.classList.add('hidden');

    try {
        const formData = new FormData();
        formData.append('month_year', monthYear);

        const response = await fetch('<?= base_url('dashboard/auditor/run') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const result = await response.json();

        loadingState.classList.add('hidden');

        if (result.status === 'success') {
            resultsArea.classList.remove('hidden');
            contextArea.classList.remove('hidden');
            
            // Render Raw Data
            document.getElementById('rawCurrent').textContent = JSON.stringify(result.current_data, null, 2);
            document.getElementById('rawHistorical').textContent = JSON.stringify(result.historical_data, null, 2);

            if (result.anomalies.length === 0) {
                badgeNoAnomaly.classList.remove('hidden');
                anomaliesContainer.innerHTML = `
                    <div class="col-span-1 md:col-span-2 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl p-6 text-center">
                        <span class="material-symbols-outlined text-emerald-500 text-4xl mb-2">verified_user</span>
                        <h4 class="text-lg font-bold text-emerald-700 dark:text-emerald-400">Keuangan Sehat</h4>
                        <p class="text-emerald-600 dark:text-emerald-500 text-sm">Tidak ditemukan lonjakan atau pengeluaran mencurigakan pada bulan ini.</p>
                    </div>
                `;
            } else {
                result.anomalies.forEach(anomaly => {
                    let colorClass = 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700';
                    let icon = 'info';
                    let iconColor = 'text-slate-500';
                    let titleColor = 'text-slate-800 dark:text-white';

                    if (anomaly.severity.toLowerCase() === 'high') {
                        colorClass = 'bg-red-50 dark:bg-red-900/10 border-red-200 dark:border-red-900/50';
                        icon = 'warning';
                        iconColor = 'text-red-500';
                        titleColor = 'text-red-700 dark:text-red-400';
                    } else if (anomaly.severity.toLowerCase() === 'medium') {
                        colorClass = 'bg-amber-50 dark:bg-amber-900/10 border-amber-200 dark:border-amber-900/50';
                        icon = 'error';
                        iconColor = 'text-amber-500';
                        titleColor = 'text-amber-700 dark:text-amber-400';
                    }

                    const card = `
                        <div class="rounded-xl border p-5 ${colorClass} transition-all hover:shadow-md">
                            <div class="flex items-start gap-4">
                                <div class="mt-1">
                                    <span class="material-symbols-outlined text-3xl ${iconColor}">${icon}</span>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-1">
                                        <h4 class="font-bold ${titleColor}">${anomaly.category_name}</h4>
                                        <span class="text-[10px] uppercase font-bold px-2 py-1 rounded-full ${anomaly.severity.toLowerCase() === 'high' ? 'bg-red-100 text-red-700' : (anomaly.severity.toLowerCase() === 'medium' ? 'bg-amber-100 text-amber-700' : 'bg-slate-200 text-slate-700')}">${anomaly.severity}</span>
                                    </div>
                                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">${anomaly.finding}</p>
                                    <div class="bg-white/50 dark:bg-black/20 rounded-lg p-3 text-sm">
                                        <span class="font-semibold text-slate-700 dark:text-slate-300 block mb-1">Saran Tindakan:</span>
                                        <span class="text-slate-600 dark:text-slate-400">${anomaly.recommendation}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    anomaliesContainer.innerHTML += card;
                });
            }
        } else {
            Swal.fire('Error', result.message, 'error');
        }
    } catch (error) {
        loadingState.classList.add('hidden');
        Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
        console.error(error);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<span class="material-symbols-outlined text-lg">search</span> Jalankan AI Auditor';
    }
});
</script>
<?= $this->endSection() ?>
