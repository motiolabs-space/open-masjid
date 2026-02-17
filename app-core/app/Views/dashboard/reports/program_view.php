<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kegiatan - <?= esc($masjid['name']) ?></title>
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
    <div class="text-center border-b-4 border-blue-600 pb-6 mb-8">
        <h1 class="text-2xl font-black uppercase tracking-wider text-blue-800"><?= esc($masjid['name']) ?></h1>
        <p class="text-sm text-slate-500 font-medium mt-1"><?= esc($masjid['address']) ?></p>
        <h2 class="text-xl font-bold mt-4">LAPORAN KEGIATAN & PROGRAM</h2>
        <p class="text-sm font-bold text-slate-500">Bulan: <?= date('F Y', strtotime($month)) ?></p>
    </div>

    <!-- Program Table -->
    <table class="w-full text-xs text-left border-collapse mb-8">
        <thead>
            <tr class="bg-gray-100 border-y border-gray-300">
                <th class="py-2 px-2 w-10 text-center">No</th>
                <th class="py-2 px-2 w-32">Tanggal</th>
                <th class="py-2 px-2">Nama Program</th>
                <th class="py-2 px-2 w-32">Kategori</th>
                <th class="py-2 px-2 w-48">Lokasi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($programs)): ?>
                <tr>
                    <td colspan="5" class="py-8 text-center text-gray-500 italic">Tidak ada kegiatan pada bulan ini.</td>
                </tr>
            <?php else: ?>
                <?php $i = 1; foreach ($programs as $p): ?>
                <tr class="border-b border-gray-200">
                    <td class="py-3 px-2 text-center text-gray-500 align-top"><?= $i++ ?></td>
                    <td class="py-3 px-2 font-medium text-gray-600 align-top">
                        <?= date('d M Y', strtotime($p['date_start'])) ?><br>
                        <span class="text-[10px] text-gray-400"><?= date('H:i', strtotime($p['date_start'])) ?> WIB</span>
                    </td>
                    <td class="py-3 px-2 align-top">
                        <div class="font-bold text-slate-800 mb-1"><?= esc($p['title']) ?></div>
                        <div class="text-[10px] text-gray-500 line-clamp-2"><?= strip_tags($p['description']) ?></div>
                    </td>
                    <td class="py-3 px-2 align-top">
                        <span class="px-2 py-1 bg-gray-100 rounded text-[10px] uppercase font-bold text-gray-600 border border-gray-200">
                            <?= esc($p['category_name']) ?>
                        </span>
                    </td>
                    <td class="py-3 px-2 text-gray-600 align-top text-xs">
                        <?= esc($p['location']) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Signature -->
    <div class="flex justify-end mt-12 break-inside-avoid">
        <div class="text-center w-48">
            <p class="text-xs mb-16"><?= $masjid['kabupaten'] ?? 'Kota' ?>, <?= date('d F Y') ?></p>
            <p class="font-bold underline"><?= esc($masjid['name']) ?></p>
            <p class="text-xs text-gray-500">Ketua DKM / Pengurus</p>
        </div>
    </div>

    <!-- Print Button -->
    <div class="fixed bottom-8 right-8 no-print">
        <button onclick="window.print()" class="bg-blue-600 text-white shadow-lg rounded-full p-4 hover:bg-blue-700 transition-all" title="Print Laporan">
             <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 0 24 24" fill="currentColor"><path d="M18 7H6V3h12v4zm0 5.5q.425 0 .713-.288.287-.287.287-.712t-.287-.713Q18.425 10.5 18 10.5t-.712.287Q17 11.075 17 11.5t.288.712Q17.575 12.5 18 12.5zM16 19v-4H8v4h8zm2 2H6v-4H2v-6q0-1.275.875-2.138Q3.75 8 5.025 8h13.95q1.275 0 2.15.862Q22 9.725 22 11v6h-4v4z"/></svg>
        </button>
    </div>

</body>
</html>
