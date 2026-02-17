<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan - <?= esc($masjid['name']) ?></title>
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
    
    <!-- Header -->
    <div class="text-center border-b-4 border-emerald-600 pb-6 mb-8">
        <h1 class="text-2xl font-black uppercase tracking-wider text-emerald-800"><?= esc($masjid['name']) ?></h1>
        <p class="text-sm text-slate-500 font-medium mt-1"><?= esc($masjid['address']) ?></p>
        <h2 class="text-xl font-bold mt-4">LAPORAN KEUANGAN</h2>
        <p class="text-sm font-bold text-slate-500">Periode: <?= date('d M Y', strtotime($startDate)) ?> s/d <?= date('d M Y', strtotime($endDate)) ?></p>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-4 gap-4 mb-8 text-sm">
        <div class="bg-gray-50 border border-gray-200 p-3 rounded-lg text-center">
            <p class="text-xs text-gray-500 uppercase font-bold">Saldo Awal</p>
            <p class="font-black text-gray-700">Rp <?= number_format($openingBalance, 0, ',', '.') ?></p>
        </div>
        <div class="bg-emerald-50 border border-emerald-200 p-3 rounded-lg text-center">
            <p class="text-xs text-emerald-600 uppercase font-bold">Pemasukan (+)</p>
            <p class="font-black text-emerald-700">Rp <?= number_format($totalIncome, 0, ',', '.') ?></p>
        </div>
        <div class="bg-red-50 border border-red-200 p-3 rounded-lg text-center">
            <p class="text-xs text-red-600 uppercase font-bold">Pengeluaran (-)</p>
            <p class="font-black text-red-700">Rp <?= number_format($totalExpense, 0, ',', '.') ?></p>
        </div>
        <div class="bg-gray-100 border border-gray-300 p-3 rounded-lg text-center">
            <p class="text-xs text-gray-500 uppercase font-bold">Saldo Akhir</p>
            <p class="font-black text-gray-800">Rp <?= number_format($closingBalance, 0, ',', '.') ?></p>
        </div>
    </div>

    <!-- Transaction Table -->
    <table class="w-full text-xs text-left border-collapse mb-8">
        <thead>
            <tr class="bg-gray-100 border-y border-gray-300">
                <th class="py-2 px-2 w-10 text-center">No</th>
                <th class="py-2 px-2 w-24">Tanggal</th>
                <th class="py-2 px-2">Keterangan</th>
                <th class="py-2 px-2 w-32">Kategori</th>
                <th class="py-2 px-2 w-28 text-right">Masuk</th>
                <th class="py-2 px-2 w-28 text-right">Keluar</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $i = 1; 
            $runningBalance = $openingBalance;
            ?>
            <?php foreach ($transactions as $t): ?>
                <?php 
                    if ($t['type'] == 'income') {
                        $in = $t['amount'];
                        $out = 0;
                        $runningBalance += $in;
                    } else {
                        $in = 0;
                        $out = $t['amount'];
                        $runningBalance -= $out;
                    }
                ?>
                <tr class="border-b border-gray-200">
                    <td class="py-2 px-2 text-center text-gray-500"><?= $i++ ?></td>
                    <td class="py-2 px-2 text-gray-600 font-medium"><?= date('d/m/Y', strtotime($t['date'])) ?></td>
                    <td class="py-2 px-2 font-medium"><?= esc($t['description']) ?></td>
                    <td class="py-2 px-2 text-gray-500"><?= esc($t['category_name']) ?></td>
                    <td class="py-2 px-2 text-right font-bold text-emerald-700"><?= $in > 0 ? number_format($in, 0, ',', '.') : '-' ?></td>
                    <td class="py-2 px-2 text-right font-bold text-red-600"><?= $out > 0 ? number_format($out, 0, ',', '.') : '-' ?></td>
                </tr>
            <?php endforeach; ?>
            <tr class="bg-gray-100 font-bold border-t border-gray-300">
                <td colspan="4" class="py-2 px-2 text-right uppercase text-xs">Total</td>
                <td class="py-2 px-2 text-right text-emerald-700"><?= number_format($totalIncome, 0, ',', '.') ?></td>
                <td class="py-2 px-2 text-right text-red-700"><?= number_format($totalExpense, 0, ',', '.') ?></td>
            </tr>
        </tbody>
    </table>

    <!-- Signature -->
    <div class="flex justify-end mt-12 break-inside-avoid">
        <div class="text-center w-48">
            <p class="text-xs mb-16"><?= $masjid['kabupaten'] ?? 'Kota' ?>, <?= date('d F Y') ?></p>
            <p class="font-bold underline"><?= esc($masjid['name']) ?></p>
            <p class="text-xs text-gray-500">Bendahara / Pengurus</p>
        </div>
    </div>

    <!-- Print Button -->
    <div class="fixed bottom-8 right-8 no-print">
        <button onclick="window.print()" class="bg-emerald-600 text-white shadow-lg rounded-full p-4 hover:bg-emerald-700 transition-all" title="Print Laporan">
             <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 0 24 24" fill="currentColor"><path d="M18 7H6V3h12v4zm0 5.5q.425 0 .713-.288.287-.287.287-.712t-.287-.713Q18.425 10.5 18 10.5t-.712.287Q17 11.075 17 11.5t.288.712Q17.575 12.5 18 12.5zM16 19v-4H8v4h8zm2 2H6v-4H2v-6q0-1.275.875-2.138Q3.75 8 5.025 8h13.95q1.275 0 2.15.862Q22 9.725 22 11v6h-4v4z"/></svg>
        </button>
    </div>

</body>
</html>
