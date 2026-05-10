<!DOCTYPE html>
<html class="light" lang="id"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?= $title ?? 'Super Admin - Masj.id' ?></title>
<link rel="icon" type="image/png" href="<?= asset_url('ico_masjid.png') ?>">
<meta name="robots" content="noindex, nofollow">
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#24a871",
                        "primary-dark": "#1f8e5f",
                        "background-light": "#F9FAFB",
                        "background-dark": "#0F172A",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {"DEFAULT": "0.375rem", "lg": "0.625rem", "xl": "1rem", "full": "9999px"},
                },
            },
        }
    </script>
<style type="text/tailwindcss">
        body { font-family: 'Inter', sans-serif; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .active-nav {
            background-color: #065F46;
            color: white !important;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen flex">

    <!-- Super Admin Sidebar -->
    <aside class="w-72 bg-slate-900 text-white flex flex-col shrink-0">
        <div class="p-6 border-b border-slate-800">
            <a href="<?= base_url('superadmin') ?>" class="flex items-center gap-3">
                <img src="<?= asset_url('logo.png') ?>" alt="Logo" class="h-6 brightness-0 invert">
                <span class="font-black text-xs uppercase tracking-widest text-emerald-400">Pusat</span>
            </a>
        </div>
        <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
            <a href="<?= base_url('superadmin') ?>" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800 transition-colors <?= current_url() == base_url('superadmin') ? 'bg-primary text-white' : 'text-slate-400' ?>">
                <span class="material-symbols-outlined">dashboard</span>
                Dashboard
            </a>
            <a href="<?= base_url('superadmin/masjid') ?>" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800 transition-colors <?= str_contains(current_url(), 'masjid') ? 'bg-primary text-white' : 'text-slate-400' ?>">
                <span class="material-symbols-outlined">mosque</span>
                Daftar Masjid
            </a>
            <a href="<?= base_url('superadmin/users') ?>" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800 transition-colors <?= str_contains(current_url(), 'users') ? 'bg-primary text-white' : 'text-slate-400' ?>">
                <span class="material-symbols-outlined">group</span>
                Manajemen User
            </a>
            <a href="<?= base_url('superadmin/profile') ?>" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800 transition-colors <?= str_contains(current_url(), 'profile') ? 'bg-primary text-white' : 'text-slate-400' ?>">
                <span class="material-symbols-outlined">lock_person</span>
                Profil & Password
            </a>
        </nav>
        <div class="p-4 border-t border-slate-800 bg-slate-950/50">
            <a href="<?= base_url('logout') ?>" class="flex items-center gap-3 px-4 py-3 rounded-lg text-rose-400 hover:bg-rose-500/10 transition-colors">
                <span class="material-symbols-outlined">logout</span>
                Keluar Sesi
            </a>
        </div>
    </aside>

    <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Header -->
        <header class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-8 shrink-0">
            <div class="flex items-center gap-4">
                <h1 class="font-bold text-lg"><?= $title ?></h1>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-bold"><?= session()->get('user_name') ?></p>
                    <p class="text-[10px] text-emerald-500 font-bold uppercase tracking-wider">Super Admin</p>
                </div>
                <div class="size-10 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-700 font-bold">
                    SA
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-8 space-y-8 bg-slate-50 dark:bg-slate-950/50">
            <?= $this->renderSection('content') ?>
        </div>
    </main>
</body></html>
