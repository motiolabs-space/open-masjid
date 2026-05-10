<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= $title ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6">
    <div class="max-w-xl w-full">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-black text-slate-800 mb-2">Pilih Masjid</h2>
            <p class="text-slate-500">Anda terdaftar sebagai pengurus di beberapa masjid. Pilih salah satu untuk mulai mengelola.</p>
        </div>

        <div class="grid gap-4">
            <?php foreach($masjids as $m): ?>
            <a href="<?= base_url('auth/set-masjid/' . $m['id']) ?>" class="group bg-white p-6 rounded-2xl border-2 border-transparent hover:border-emerald-500 shadow-sm hover:shadow-xl transition-all flex items-center justify-between">
                <div class="flex items-center gap-5">
                    <div class="size-14 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600 group-hover:bg-emerald-500 group-hover:text-white transition-colors">
                        <span class="material-symbols-outlined text-2xl">mosque</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 text-lg"><?= esc($m['name']) ?></h4>
                        <p class="text-sm text-slate-500">@<?= esc($m['username']) ?></p>
                    </div>
                </div>
                <span class="material-symbols-outlined text-slate-300 group-hover:text-emerald-500 transition-colors">arrow_forward_ios</span>
            </a>
            <?php endforeach; ?>
        </div>

        <div class="mt-10 text-center">
            <a href="<?= base_url('logout') ?>" class="text-sm font-bold text-rose-500 hover:underline flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-sm">logout</span>
                Bukan akun Anda? Keluar Sesi
            </a>
        </div>
    </div>
</body>
</html>
