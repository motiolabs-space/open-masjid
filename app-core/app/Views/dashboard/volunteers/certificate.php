<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Symbols+Outlined">
    <style>
        @media print {
            @page { size: A4 landscape; margin: 0; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none; }
        }
        body { font-family: Georgia, 'Times New Roman', serif; }
    </style>
</head>
<body class="bg-slate-100 p-6">
    <?php
        $bulan = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $ts = time();
        $tglIndo = date('d', $ts) . ' ' . ($bulan[(int) date('n', $ts)]) . ' ' . date('Y', $ts);
    ?>

    <div class="max-w-[27cm] mx-auto bg-white shadow-xl" style="aspect-ratio: 297/210;">
        <div class="h-full border-[10px] border-emerald-700 p-3">
            <div class="h-full border-2 border-emerald-600/40 flex flex-col items-center justify-center text-center px-12 py-8 relative">

                <span class="material-symbols-outlined absolute top-6 left-6 text-emerald-700/10" style="font-size:120px">mosque</span>

                <p class="text-emerald-800 font-black tracking-[0.3em] uppercase text-sm mb-1"><?= esc($masjid['name'] ?? 'Masjid') ?></p>
                <h1 class="text-4xl md:text-5xl font-black text-emerald-800 tracking-wide mb-1" style="font-family: Georgia, serif;">Sertifikat Penghargaan</h1>
                <div class="w-24 h-1 bg-emerald-600 rounded-full my-4"></div>

                <p class="text-slate-500 text-lg">Diberikan dengan penuh penghargaan kepada</p>
                <p class="text-5xl font-black text-slate-800 my-4" style="font-family: Georgia, serif;"><?= esc($volunteer['name']) ?></p>

                <p class="text-slate-600 max-w-2xl leading-relaxed">
                    atas dedikasi dan keikhlasannya sebagai <strong class="text-emerald-800"><?= esc($volunteer['role'] ?: 'relawan masjid') ?></strong>,
                    dengan raihan <strong class="text-emerald-800"><?= number_format($volunteer['points'], 0, ',', '.') ?> poin partisipasi</strong>.
                    Semoga menjadi amal jariyah yang tak terputus.
                </p>

                <div class="flex items-end justify-between w-full mt-10 px-6">
                    <div class="text-center">
                        <div class="size-16 rounded-full bg-emerald-700/10 flex items-center justify-center mx-auto mb-1">
                            <span class="material-symbols-outlined text-emerald-700 text-3xl">verified</span>
                        </div>
                        <p class="text-xs text-slate-400">Relawan sejak <?= !empty($volunteer['joined_at']) ? date('M Y', strtotime($volunteer['joined_at'])) : '—' ?></p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-slate-500 mb-10"><?= esc($tglIndo) ?></p>
                        <p class="font-bold border-t-2 border-slate-300 pt-1 px-8 text-slate-700">Pengurus Masjid</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-6 no-print">
        <button onclick="window.print()" class="bg-emerald-700 hover:bg-emerald-800 text-white font-bold px-8 py-3 rounded-xl" style="font-family: system-ui, sans-serif;">
            Cetak / Simpan PDF
        </button>
        <a href="<?= base_url('dashboard/relawan') ?>" class="ml-3 text-slate-500 font-bold" style="font-family: system-ui, sans-serif;">Kembali</a>
    </div>
</body>
</html>
