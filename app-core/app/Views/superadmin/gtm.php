<?= $this->extend('layout/superadmin') ?>

<?= $this->section('content') ?>
<?php
    // Pembantu format & delta — mirip kartu konsol toko aplikasi.
    $fmt = fn ($n, $dec = 0) => number_format((float) $n, $dec, ',', '.');

    // Perubahan ember terakhir vs sebelumnya, dalam persen.
    $deltaPersen = function (array $s): array {
        $n = count($s);
        if ($n < 2) return ['0%', 'flat'];
        $last = $s[$n - 1];
        $prev = $s[$n - 2];
        if ($prev == 0 && $last == 0) return ['0%', 'flat'];
        if ($prev == 0)               return ['&gt;+999%', 'up'];
        $p = round(($last - $prev) / $prev * 100);
        $arah = $p > 0 ? 'up' : ($p < 0 ? 'down' : 'flat');
        return [($p > 0 ? '+' : '') . number_format($p, 0, ',', '.') . '%', $arah];
    };

    // Ringkas satu kartu tren jadi headline + unit + delta.
    $ringkas = function (array $k) use ($fmt, $deltaPersen): array {
        $s = $k['series'];
        $n = count($s);
        if ($k['mode'] === 'total') {
            $head  = $fmt($s[$n - 1]);
            $unit  = 'total';
            $naik  = $s[$n - 1] - $s[0];
            $delta = ($naik > 0 ? '+' : '') . $fmt($naik);
            $arah  = $naik > 0 ? 'up' : ($naik < 0 ? 'down' : 'flat');
        } elseif ($k['mode'] === 'rupiah') {
            $head  = 'Rp ' . $fmt(array_sum($s));
            $unit  = 'total';
            [$delta, $arah] = $deltaPersen($s);
        } else { // flow
            $head  = $fmt(array_sum($s) / max($n, 1), 1);
            $unit  = 'rata-rata / bln';
            [$delta, $arah] = $deltaPersen($s);
        }
        return compact('head', 'unit', 'delta', 'arah');
    };

    $warnaHex = [
        'emerald' => '#059669', 'sky' => '#0284c7', 'blue' => '#2563eb', 'violet' => '#7c3aed',
        'teal' => '#0d9488', 'amber' => '#d97706', 'rose' => '#e11d48', 'indigo' => '#4f46e5',
    ];
?>

<div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Laporan GTM</h1>
        <p class="text-slate-500 text-sm mt-1">Pertumbuhan platform · <?= esc($rentang) ?></p>
    </div>
    <div class="inline-flex rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-1 text-sm font-semibold self-start">
        <?php foreach ([6 => '6 bln', 12 => '12 bln', 24 => '24 bln'] as $b => $t): ?>
            <a href="<?= base_url('superadmin/gtm?bulan=' . $b) ?>"
               class="px-3 py-1.5 rounded-lg <?= $bulan === $b ? 'bg-primary text-white' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white' ?>">
                <?= $t ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Kartu tren (histori asli dari stempel waktu) -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    <?php foreach ($tren as $i => $k): $r = $ringkas($k); ?>
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-5">
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-center gap-2 text-slate-500 text-sm font-medium">
                    <span class="material-symbols-outlined text-[20px] text-<?= $k['warna'] ?>-600"><?= esc($k['ikon']) ?></span>
                    <?= esc($k['judul']) ?>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <h3 class="text-2xl font-black"><?= $r['head'] ?></h3>
                <span class="text-xs text-slate-400 font-medium"><?= esc($r['unit']) ?></span>
            </div>
            <div class="mt-1 mb-2">
                <?php
                    $pill = $r['arah'] === 'up' ? 'text-emerald-600' : ($r['arah'] === 'down' ? 'text-rose-500' : 'text-slate-400');
                    $ar   = $r['arah'] === 'up' ? 'arrow_upward' : ($r['arah'] === 'down' ? 'arrow_downward' : 'remove');
                ?>
                <span class="inline-flex items-center gap-0.5 text-xs font-bold <?= $pill ?>">
                    <span class="material-symbols-outlined text-[14px]"><?= $ar ?></span><?= $r['delta'] ?>
                </span>
            </div>
            <div class="h-16">
                <canvas id="spark<?= $i ?>"></canvas>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Posisi terkini (snapshot, tanpa histori harian) -->
<div class="mt-8">
    <div class="flex items-center gap-2 mb-3">
        <h2 class="text-lg font-bold">Posisi Terkini</h2>
    </div>
    <div class="bg-amber-50 dark:bg-amber-900/15 border border-amber-200 dark:border-amber-800/40 text-amber-800 dark:text-amber-300 rounded-xl p-3 text-xs flex gap-2 mb-4">
        <span class="material-symbols-outlined text-base shrink-0">info</span>
        <?php if (! $adaRiwayat): ?>
            <p>Angka aktif dihitung dari <strong>login terakhir</strong> tiap pengguna, jadi ditampilkan sebagai nilai saat ini, <strong>bukan grafik tren</strong>. Riwayat login sudah mulai dicatat sejak pemasangannya — tren yang sungguhan akan muncul setelah datanya terkumpul.</p>
        <?php else: ?>
            <p>Kartu di bawah adalah <strong>posisi terkini</strong>. Trennya kini tersedia dari riwayat login yang tercatat sejak <strong><?= esc(date('M Y', strtotime($awalRekam . '-01'))) ?></strong> — lihat kartu "Pengguna Aktif Bulanan" di atas dan tabel retensi di bawah.</p>
        <?php endif; ?>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <?php foreach ($snapshot as $s): ?>
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-5">
                <div class="flex items-center gap-2 text-slate-500 text-sm font-medium mb-3">
                    <span class="material-symbols-outlined text-[20px] text-<?= $s['warna'] ?>-600"><?= esc($s['ikon']) ?></span>
                    <?= esc($s['judul']) ?>
                </div>
                <h3 class="text-3xl font-black"><?= $fmt($s['nilai']) ?></h3>
                <p class="text-xs text-slate-400 mt-1"><?= esc($s['sub']) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Funnel aktivasi masjid -->
<div class="mt-10">
    <h2 class="text-lg font-black mb-1">Aktivasi Masjid</h2>
    <p class="text-sm text-slate-500 mb-4">
        Sejauh mana masjid yang mendaftar benar-benar memakainya, <?= esc($rentang) ?>.
        Dihitung dari data yang sudah ada, jadi berlaku juga untuk masjid lama.
    </p>

    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <?php if ($funnelTotal === 0): ?>
            <p class="p-6 text-sm text-slate-500">Belum ada masjid mendaftar pada rentang ini.</p>
        <?php else: ?>
            <div class="p-6 space-y-4">
                <?php foreach ($funnel as $f): ?>
                    <?php $persen = $funnelTotal > 0 ? round($f['jumlah'] / $funnelTotal * 100) : 0; ?>
                    <div>
                        <div class="flex items-baseline justify-between gap-4 mb-1.5">
                            <div class="min-w-0">
                                <span class="font-bold text-sm"><?= esc($f['tahap']) ?></span>
                                <span class="text-xs text-slate-400 ml-2"><?= esc($f['catatan']) ?></span>
                            </div>
                            <div class="shrink-0 text-sm">
                                <span class="font-black"><?= $fmt($f['jumlah']) ?></span>
                                <span class="text-slate-400 text-xs ml-1"><?= $persen ?>%</span>
                            </div>
                        </div>
                        <div class="h-2 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                            <div class="h-full rounded-full bg-primary" style="width: <?= $persen ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 grid sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-slate-500 text-xs mb-1">Mendaftar lalu diam</p>
                    <p class="font-black text-lg">
                        <?= $fmt($funnelDiam) ?>
                        <span class="text-xs font-medium text-slate-400">
                            dari <?= $fmt($funnelTotal) ?> masjid
                        </span>
                    </p>
                    <p class="text-xs text-slate-500 mt-1">
                        Tak pernah mencatat transaksi, program, berita, maupun menerima donasi.
                    </p>
                </div>
                <div>
                    <p class="text-slate-500 text-xs mb-1">Jeda sampai transaksi pertama</p>
                    <p class="font-black text-lg">
                        <?php if ($jedaTengah === null): ?>
                            <span class="text-slate-300 dark:text-slate-600">—</span>
                        <?php else: ?>
                            <?= $fmt($jedaTengah) ?> <span class="text-xs font-medium text-slate-400">hari (median)</span>
                        <?php endif; ?>
                    </p>
                    <p class="text-xs text-slate-500 mt-1">
                        Dari mendaftar sampai transaksi pertama dicatat.
                    </p>
                </div>
            </div>

            <p class="px-6 pb-4 text-xs text-slate-500">
                Tahapan ini <strong>tidak berjenjang ketat</strong> — sebuah masjid bisa menerbitkan
                program tanpa pernah mencatat transaksi. Bacalah tiap baris sebagai capaian
                tersendiri, bukan corong yang harus mengecil berurutan.
            </p>
        <?php endif; ?>
    </div>
</div>

<!-- Retensi kohort masjid -->
<div class="mt-10">
    <h2 class="text-lg font-black mb-1">Retensi Masjid (Kohort)</h2>
    <p class="text-sm text-slate-500 mb-4">
        Dari masjid yang mendaftar tiap bulan, berapa yang masih aktif pada bulan-bulan sesudahnya.
        Aktif = ada pengurusnya yang login pada bulan itu.
    </p>

    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <?php if (! $adaRiwayat): ?>
            <?php // Jujur soal sebabnya, bukan sekadar "belum ada data". ?>
            <div class="p-6">
                <p class="text-sm font-bold mb-1">Belum bisa dihitung</p>
                <p class="text-sm text-slate-500 leading-relaxed">
                    Riwayat login baru mulai dicatat sejak pemasangannya. Angka retensi
                    baru bermakna setelah ada data beberapa bulan — kolom pertama akan
                    terisi bulan depan.
                </p>
            </div>
        <?php elseif (empty($kohort)): ?>
            <p class="p-6 text-sm text-slate-500">Belum ada masjid mendaftar pada rentang ini.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 text-xs font-bold uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3">Kohort</th>
                            <th class="px-6 py-3 text-right">Mendaftar</th>
                            <th class="px-6 py-3 text-right">+1 bulan</th>
                            <th class="px-6 py-3 text-right">+2 bulan</th>
                            <th class="px-6 py-3 text-right">+3 bulan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <?php foreach ($kohort as $k): ?>
                            <tr>
                                <td class="px-6 py-3 font-bold"><?= esc($k['bulan']) ?></td>
                                <td class="px-6 py-3 text-right font-bold"><?= $fmt($k['jumlah']) ?></td>
                                <?php foreach ($k['lanjut'] as $n): ?>
                                    <td class="px-6 py-3 text-right">
                                        <?php if ($n === null): ?>
                                            <?php // Bulannya belum tiba — beda dari "nol yang bertahan". ?>
                                            <span class="text-slate-300 dark:text-slate-600">—</span>
                                        <?php else: ?>
                                            <?php $persen = $k['jumlah'] > 0 ? round($n / $k['jumlah'] * 100) : 0; ?>
                                            <span class="font-bold"><?= $fmt($n) ?></span>
                                            <span class="text-xs text-slate-400 ml-1"><?= $persen ?>%</span>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="px-6 py-4 text-xs text-slate-500 border-t border-slate-100 dark:border-slate-800">
                Tanda &mdash; berarti <strong>bulannya belum tiba atau belum terekam</strong>, bukan nol.
                Riwayat login baru tercatat sejak <strong><?= esc(date('M Y', strtotime($awalRekam . '-01'))) ?></strong>,
                jadi kohort sebelum itu memang tak punya datanya — bukan berarti masjidnya berhenti.
            </p>
        <?php endif; ?>
    </div>
</div>

<!-- Kanal akuisisi masjid -->
<div class="mt-10">
    <h2 class="text-lg font-black mb-1">Kanal Akuisisi Masjid</h2>
    <p class="text-sm text-slate-500 mb-4">
        Dari mana masjid yang mendaftar berasal, <?= esc($rentang) ?>.
    </p>

    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <?php
            $totalKanal = array_sum(array_column($kanal, 'jumlah'));
            $adaTercatat = false;
            foreach ($kanal as $k) { if ($k['sumber'] !== '') { $adaTercatat = true; break; } }
        ?>
        <?php if ($totalKanal === 0): ?>
            <p class="p-6 text-sm text-slate-500">Belum ada masjid mendaftar pada rentang ini.</p>
        <?php else: ?>
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 text-xs font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3">Sumber</th>
                        <th class="px-6 py-3">Medium</th>
                        <th class="px-6 py-3 text-right">Masjid</th>
                        <th class="px-6 py-3 text-right">Porsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php foreach ($kanal as $k): ?>
                        <tr>
                            <td class="px-6 py-3 font-bold">
                                <?= $k['sumber'] !== '' ? esc($k['sumber']) : '<span class="text-slate-400 font-medium">Tidak tercatat</span>' ?>
                            </td>
                            <td class="px-6 py-3 text-slate-500"><?= $k['medium'] !== '' ? esc($k['medium']) : '—' ?></td>
                            <td class="px-6 py-3 text-right font-bold"><?= $fmt($k['jumlah']) ?></td>
                            <td class="px-6 py-3 text-right text-slate-500"><?= round($k['jumlah'] / $totalKanal * 100) ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if (! $adaTercatat): ?>
                <?php // Jujur soal batasnya: tanpa catatan asal, tabel ini tak
                      // bisa dipakai menilai kanal mana pun. ?>
                <p class="px-6 py-4 text-xs text-slate-500 border-t border-slate-100 dark:border-slate-800">
                    Belum ada masjid dengan asal yang tercatat. Pencatatan kanal baru berjalan sejak
                    pemasangannya — masjid yang mendaftar sebelum itu tidak punya datanya.
                    Sebarkan tautan berpenanda seperti
                    <code class="px-1 rounded bg-slate-100 dark:bg-slate-800">?utm_source=instagram&amp;utm_medium=social&amp;utm_campaign=pekan-1</code>
                    agar kolom ini mulai terisi.
                </p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const gtmLabel  = <?= json_encode($label) ?>;
    const gtmSeries = <?= json_encode(array_map(fn ($k) => $k['series'], $tren)) ?>;
    const gtmColor  = <?= json_encode(array_map(fn ($k) => $warnaHex[$k['warna']] ?? '#059669', $tren)) ?>;

    gtmSeries.forEach((data, i) => {
        const el = document.getElementById('spark' + i);
        if (!el) return;
        const c = gtmColor[i];
        new Chart(el, {
            type: 'line',
            data: {
                labels: gtmLabel,
                datasets: [{
                    data: data,
                    borderColor: c,
                    backgroundColor: c + '22',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 0,
                    pointHoverRadius: 3,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: {
                    displayColors: false,
                    callbacks: { title: (t) => t[0].label, label: (t) => new Intl.NumberFormat('id-ID').format(t.raw) },
                } },
                scales: { x: { display: false }, y: { display: false, beginAtZero: true } },
                interaction: { intersect: false, mode: 'index' },
            },
        });
    });
</script>
<?= $this->endSection() ?>
