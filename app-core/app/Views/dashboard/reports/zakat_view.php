<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Zakat - <?= esc($masjid['name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page { size: A4; margin: 1cm; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body class="bg-white text-slate-800 p-8 max-w-[21cm] mx-auto">

    <?php
        $rp = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
        $selisih = $totalMasuk - $totalKeluar;
    ?>

    <!-- Header -->
    <div class="text-center border-b-4 border-emerald-600 pb-6 mb-8">
        <h1 class="text-2xl font-black uppercase tracking-wider text-emerald-800"><?= esc($masjid['name']) ?></h1>
        <p class="text-sm text-slate-500 font-medium mt-1"><?= esc($masjid['address'] ?? '') ?></p>
        <h2 class="text-lg font-bold mt-4">LAPORAN ZAKAT</h2>
        <p class="text-sm text-slate-500">Periode <?= date('d M Y', strtotime($startDate)) ?> &ndash; <?= date('d M Y', strtotime($endDate)) ?></p>
    </div>

    <!-- Ringkasan -->
    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
            <p class="text-[10px] font-black uppercase tracking-widest text-emerald-700">Zakat Terkumpul</p>
            <p class="text-xl font-black text-emerald-700 mt-1"><?= $rp($totalMasuk) ?></p>
        </div>
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-5">
            <p class="text-[10px] font-black uppercase tracking-widest text-rose-700">Zakat Tersalur</p>
            <p class="text-xl font-black text-rose-700 mt-1"><?= $rp($totalKeluar) ?></p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-600">Belum Tersalur</p>
            <p class="text-xl font-black mt-1 <?= $selisih < 0 ? 'text-rose-600' : 'text-slate-800' ?>"><?= $rp($selisih) ?></p>
        </div>
    </div>

    <!-- Zakat Masuk per jenis -->
    <h3 class="font-black text-slate-700 mb-3 flex items-center gap-2">
        <span class="w-1.5 h-5 bg-emerald-500 rounded"></span> Penerimaan Zakat (per jenis)
    </h3>
    <table class="w-full text-sm mb-8 border border-slate-200">
        <thead>
            <tr class="bg-slate-100 text-slate-600 text-left text-xs uppercase tracking-wider">
                <th class="px-4 py-2">Jenis Zakat</th>
                <th class="px-4 py-2 text-center">Transaksi</th>
                <th class="px-4 py-2 text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($jenisLabel as $key => $label): ?>
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-2 font-medium"><?= esc($label) ?></td>
                    <td class="px-4 py-2 text-center text-slate-500"><?= $masukPer[$key]['jml'] ?></td>
                    <td class="px-4 py-2 text-right font-bold"><?= $rp($masukPer[$key]['total']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="border-t-2 border-emerald-600 bg-emerald-50 font-black">
                <td class="px-4 py-2" colspan="2">TOTAL TERKUMPUL</td>
                <td class="px-4 py-2 text-right text-emerald-700"><?= $rp($totalMasuk) ?></td>
            </tr>
        </tfoot>
    </table>

    <!-- Zakat Keluar per asnaf -->
    <h3 class="font-black text-slate-700 mb-3 flex items-center gap-2">
        <span class="w-1.5 h-5 bg-rose-500 rounded"></span> Penyaluran Zakat (8 Asnaf)
    </h3>
    <table class="w-full text-sm mb-6 border border-slate-200">
        <thead>
            <tr class="bg-slate-100 text-slate-600 text-left text-xs uppercase tracking-wider">
                <th class="px-4 py-2">Golongan (Asnaf)</th>
                <th class="px-4 py-2 text-center">Penerima</th>
                <th class="px-4 py-2 text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($asnafLabel as $key => $label): ?>
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-2 font-medium"><?= esc($label) ?></td>
                    <td class="px-4 py-2 text-center text-slate-500"><?= $keluarPer[$key]['jml'] ?></td>
                    <td class="px-4 py-2 text-right font-bold"><?= $rp($keluarPer[$key]['total']) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if ($keluarBelum['jml'] > 0): ?>
                <tr class="border-t border-slate-100 text-amber-700 bg-amber-50/50">
                    <td class="px-4 py-2 font-medium italic">Belum diklasifikasi</td>
                    <td class="px-4 py-2 text-center"><?= $keluarBelum['jml'] ?></td>
                    <td class="px-4 py-2 text-right font-bold"><?= $rp($keluarBelum['total']) ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr class="border-t-2 border-rose-600 bg-rose-50 font-black">
                <td class="px-4 py-2" colspan="2">TOTAL TERSALUR</td>
                <td class="px-4 py-2 text-right text-rose-700"><?= $rp($totalKeluar) ?></td>
            </tr>
        </tfoot>
    </table>

    <p class="text-[11px] text-slate-400 leading-relaxed mb-8">
        Laporan ini mencakup zakat yang tercatat sistem: donasi online bertanda zakat dan penyaluran ke
        mustahik. Zakat tunai yang dibukukan sebagai kas biasa (tanpa tanda) tidak termasuk. Klasifikasikan
        golongan (asnaf) tiap mustahik di menu Penyaluran agar penyalurannya muncul pada golongan yang tepat.
    </p>

    <!-- Tanda tangan -->
    <div class="grid grid-cols-2 gap-8 mt-12 text-sm">
        <div class="text-center">
            <p class="text-slate-500">Mengetahui,</p>
            <div class="h-16"></div>
            <p class="font-bold border-t border-slate-300 pt-1">Ketua / Ketua Takmir</p>
        </div>
        <div class="text-center">
            <p class="text-slate-500">Dibuat oleh,</p>
            <div class="h-16"></div>
            <p class="font-bold border-t border-slate-300 pt-1">Amil / Bendahara</p>
        </div>
    </div>

    <div class="text-center mt-10 no-print">
        <button onclick="window.print()" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-8 py-3 rounded-xl">
            Cetak / Simpan PDF
        </button>
    </div>
</body>
</html>
