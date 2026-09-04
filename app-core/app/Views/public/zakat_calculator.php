<?= $this->extend('layout/masjid_public') ?>

<?= $this->section('content') ?>
<section class="py-16 md:py-24 bg-background-light dark:bg-background-dark min-h-screen">
    <div class="max-w-2xl mx-auto px-6">

        <div class="mb-8">
            <a href="<?= base_url($masjid['username']) ?>" class="inline-flex items-center gap-2 text-primary font-bold mb-4 hover:underline">
                <span class="material-symbols-outlined text-sm">arrow_back</span> Kembali ke Profil
            </a>
            <h1 class="text-3xl md:text-4xl font-black text-[#111816] dark:text-white tracking-tight">Kalkulator Zakat</h1>
            <p class="text-[#608a7e] text-lg mt-1">Hitung kewajiban zakat Anda, lalu tunaikan melalui <?= esc($masjid['name']) ?>.</p>
        </div>

        <!-- Pengaturan harga (nishab) -->
        <div class="bg-primary/5 border border-primary/15 rounded-2xl p-4 mb-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <label class="block">
                <span class="text-xs font-bold text-[#608a7e] uppercase tracking-wider">Harga Emas / gram</span>
                <div class="relative mt-1">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">Rp</span>
                    <input type="text" inputmode="numeric" id="hargaEmas" value="1.400.000" oninput="fmt(this);hitung()"
                           class="w-full pl-10 pr-3 py-2.5 rounded-xl border border-[#dbe6e3] dark:bg-white/5 dark:border-white/10 font-bold text-sm">
                </div>
                <span class="text-[10px] text-gray-400">Nishab zakat maal = 85 gram emas.</span>
            </label>
            <label class="block">
                <span class="text-xs font-bold text-[#608a7e] uppercase tracking-wider">Harga Beras / kg</span>
                <div class="relative mt-1">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">Rp</span>
                    <input type="text" inputmode="numeric" id="hargaBeras" value="15.000" oninput="fmt(this);hitung()"
                           class="w-full pl-10 pr-3 py-2.5 rounded-xl border border-[#dbe6e3] dark:bg-white/5 dark:border-white/10 font-bold text-sm">
                </div>
                <span class="text-[10px] text-gray-400">Zakat fitrah = 2,5 kg beras / jiwa.</span>
            </label>
        </div>

        <!-- Tab jenis zakat -->
        <div class="flex gap-1 p-1 bg-slate-100 dark:bg-white/5 rounded-2xl mb-6">
            <?php $tabs = ['maal' => 'Maal (Harta)', 'penghasilan' => 'Penghasilan', 'fitrah' => 'Fitrah']; ?>
            <?php foreach ($tabs as $key => $label): ?>
                <button type="button" data-tab="<?= $key ?>" onclick="pilihTab('<?= $key ?>')"
                        class="tab-btn flex-1 py-2.5 rounded-xl text-sm font-bold transition-all <?= $key === 'maal' ? 'bg-white dark:bg-white/10 text-primary shadow-sm' : 'text-[#608a7e]' ?>">
                    <?= $label ?>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="bg-white dark:bg-white/5 rounded-3xl border border-[#dbe6e3] dark:border-white/10 p-6 md:p-8">
            <!-- MAAL -->
            <div class="tab-panel" data-panel="maal">
                <p class="text-sm text-[#608a7e] mb-5">Zakat harta yang telah dimiliki penuh selama 1 tahun (haul) dan mencapai nishab. Tarif <strong>2,5%</strong>.</p>
                <?php
                    $maalFields = [
                        'emas'      => 'Emas, perak & logam mulia',
                        'tabungan'  => 'Uang tunai & tabungan',
                        'investasi' => 'Investasi & saham',
                        'dagang'    => 'Harta perdagangan',
                        'piutang'   => 'Piutang (yang akan kembali)',
                    ];
                    foreach ($maalFields as $id => $lbl):
                ?>
                    <label class="block mb-3">
                        <span class="text-sm font-medium"><?= $lbl ?></span>
                        <div class="relative mt-1">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">Rp</span>
                            <input type="text" inputmode="numeric" data-maal placeholder="0" oninput="fmt(this);hitung()"
                                   class="w-full pl-10 pr-3 py-2.5 rounded-xl border border-gray-200 dark:bg-white/5 dark:border-white/10 font-bold">
                        </div>
                    </label>
                <?php endforeach; ?>
                <label class="block mb-1">
                    <span class="text-sm font-medium text-red-500">Utang jatuh tempo (pengurang)</span>
                    <div class="relative mt-1">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">Rp</span>
                        <input type="text" inputmode="numeric" id="maalUtang" placeholder="0" oninput="fmt(this);hitung()"
                               class="w-full pl-10 pr-3 py-2.5 rounded-xl border border-gray-200 dark:bg-white/5 dark:border-white/10 font-bold">
                    </div>
                </label>
            </div>

            <!-- PENGHASILAN -->
            <div class="tab-panel hidden" data-panel="penghasilan">
                <p class="text-sm text-[#608a7e] mb-5">Zakat profesi/penghasilan, tarif <strong>2,5%</strong> dari penghasilan bila mencapai nishab bulanan (setara 85 gram emas ÷ 12).</p>
                <label class="block mb-3">
                    <span class="text-sm font-medium">Penghasilan per bulan</span>
                    <div class="relative mt-1">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">Rp</span>
                        <input type="text" inputmode="numeric" id="phBulan" placeholder="0" oninput="fmt(this);hitung()"
                               class="w-full pl-10 pr-3 py-2.5 rounded-xl border border-gray-200 dark:bg-white/5 dark:border-white/10 font-bold">
                    </div>
                </label>
                <label class="block mb-3">
                    <span class="text-sm font-medium">Penghasilan lain / bonus per bulan</span>
                    <div class="relative mt-1">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">Rp</span>
                        <input type="text" inputmode="numeric" id="phLain" placeholder="0" oninput="fmt(this);hitung()"
                               class="w-full pl-10 pr-3 py-2.5 rounded-xl border border-gray-200 dark:bg-white/5 dark:border-white/10 font-bold">
                    </div>
                </label>
                <label class="block mb-1">
                    <span class="text-sm font-medium text-red-500">Kebutuhan pokok per bulan (pengurang, opsional)</span>
                    <div class="relative mt-1">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">Rp</span>
                        <input type="text" inputmode="numeric" id="phPokok" placeholder="0" oninput="fmt(this);hitung()"
                               class="w-full pl-10 pr-3 py-2.5 rounded-xl border border-gray-200 dark:bg-white/5 dark:border-white/10 font-bold">
                    </div>
                </label>
            </div>

            <!-- FITRAH -->
            <div class="tab-panel hidden" data-panel="fitrah">
                <p class="text-sm text-[#608a7e] mb-5">Zakat fitrah wajib bagi setiap jiwa menjelang Idul Fitri, sebesar <strong>2,5 kg beras</strong> (atau nilainya) per orang.</p>
                <label class="block mb-1">
                    <span class="text-sm font-medium">Jumlah jiwa (anggota keluarga)</span>
                    <input type="number" min="1" step="1" id="fitrahJiwa" value="1" oninput="hitung()"
                           class="w-full mt-1 px-4 py-2.5 rounded-xl border border-gray-200 dark:bg-white/5 dark:border-white/10 font-bold">
                </label>
            </div>

            <!-- HASIL -->
            <div class="mt-6 pt-6 border-t border-dashed border-[#dbe6e3] dark:border-white/10">
                <p id="hasilNishab" class="text-xs text-[#608a7e] mb-1"></p>
                <p class="text-[10px] uppercase tracking-widest font-black text-[#608a7e]">Zakat yang perlu ditunaikan</p>
                <p id="hasilNominal" class="text-3xl font-black text-primary mt-1">Rp 0</p>
                <p id="hasilStatus" class="text-sm mt-1"></p>

                <a id="ctaBayar" href="#" class="mt-5 hidden w-full py-4 bg-primary text-white font-bold rounded-xl shadow-lg shadow-primary/30 hover:-translate-y-0.5 transition-all items-center justify-center gap-2 flex">
                    <span class="material-symbols-outlined">volunteer_activism</span>
                    <span id="ctaText">Tunaikan Zakat</span>
                </a>
            </div>
        </div>

        <p class="text-[11px] text-slate-400 mt-5 text-center max-w-md mx-auto">
            Perhitungan ini adalah panduan. Untuk kondisi harta yang kompleks, konsultasikan dengan amil atau ustadz terpercaya.
        </p>
    </div>
</section>

<script>
    var BASE = <?= json_encode(base_url('donation/' . $masjid['username'] . '/form')) ?>;
    var tabAktif = 'maal';
    var labelZakat = { maal: 'Zakat Maal', penghasilan: 'Zakat Penghasilan', fitrah: 'Zakat Fitrah' };

    // Rupiah: ambil angka murni dari input berformat.
    function angka(el) { return el ? parseInt((el.value || '').replace(/[^0-9]/g, ''), 10) || 0 : 0; }
    function fmt(el) {
        var v = el.value.replace(/[^0-9]/g, '');
        el.value = v ? parseInt(v, 10).toLocaleString('id-ID') : '';
    }
    function rp(n) { return 'Rp ' + Math.round(n).toLocaleString('id-ID'); }

    function pilihTab(t) {
        tabAktif = t;
        document.querySelectorAll('.tab-btn').forEach(function (b) {
            var on = b.dataset.tab === t;
            b.classList.toggle('bg-white', on);
            b.classList.toggle('dark:bg-white/10', on);
            b.classList.toggle('text-primary', on);
            b.classList.toggle('shadow-sm', on);
            b.classList.toggle('text-[#608a7e]', !on);
        });
        document.querySelectorAll('.tab-panel').forEach(function (p) {
            p.classList.toggle('hidden', p.dataset.panel !== t);
        });
        hitung();
    }

    function hitung() {
        var hargaEmas = angka(document.getElementById('hargaEmas'));
        var hargaBeras = angka(document.getElementById('hargaBeras'));
        var nishabTahun = 85 * hargaEmas;
        var zakat = 0, wajib = false, nishabTeks = '', status = '';

        if (tabAktif === 'maal') {
            var harta = 0;
            document.querySelectorAll('[data-maal]').forEach(function (i) { harta += angka(i); });
            harta -= angka(document.getElementById('maalUtang'));
            nishabTeks = 'Nishab (85 gr emas): ' + rp(nishabTahun) + ' — harta bersih Anda: ' + rp(Math.max(harta, 0));
            if (harta >= nishabTahun && harta > 0) { zakat = harta * 0.025; wajib = true; }
            else { status = 'Harta belum mencapai nishab — zakat maal belum wajib.'; }
        } else if (tabAktif === 'penghasilan') {
            var ph = angka(document.getElementById('phBulan')) + angka(document.getElementById('phLain')) - angka(document.getElementById('phPokok'));
            var nishabBulan = nishabTahun / 12;
            nishabTeks = 'Nishab bulanan (85 gr emas ÷ 12): ' + rp(nishabBulan) + ' — penghasilan Anda: ' + rp(Math.max(ph, 0));
            if (ph >= nishabBulan && ph > 0) { zakat = ph * 0.025; wajib = true; }
            else { status = 'Penghasilan belum mencapai nishab bulanan — namun bersedekah tetap dianjurkan.'; }
        } else {
            var jiwa = parseInt(document.getElementById('fitrahJiwa').value, 10) || 0;
            var perJiwa = 2.5 * hargaBeras;
            nishabTeks = 'Setara 2,5 kg beras / jiwa = ' + rp(perJiwa) + ' × ' + jiwa + ' jiwa';
            zakat = jiwa * perJiwa;
            wajib = zakat > 0;
        }

        document.getElementById('hasilNishab').textContent = nishabTeks;
        document.getElementById('hasilNominal').textContent = rp(zakat);
        document.getElementById('hasilStatus').textContent = wajib ? '' : status;
        document.getElementById('hasilStatus').className = 'text-sm mt-1 ' + (wajib ? '' : 'text-amber-600');

        var cta = document.getElementById('ctaBayar');
        if (wajib && zakat > 0) {
            cta.href = BASE + '?nominal=' + Math.round(zakat) + '&untuk=' + encodeURIComponent(labelZakat[tabAktif]);
            document.getElementById('ctaText').textContent = 'Tunaikan ' + labelZakat[tabAktif] + ' — ' + rp(zakat);
            cta.classList.remove('hidden');
        } else {
            cta.classList.add('hidden');
        }
    }
    hitung();
</script>
<?= $this->endSection() ?>
