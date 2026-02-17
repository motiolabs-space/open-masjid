<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= $title ?? env('seo.title') ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('public/ico_masjid.png') ?>">
    <meta name="description" content="<?= esc($masjid['misi'] ?? env('seo.description')) ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= current_url() ?>">
    <meta property="og:title" content="<?= $title ?? env('seo.title') ?>">
    <meta property="og:description" content="<?= esc($masjid['visi'] ?? env('seo.description')) ?>">
    <meta property="og:image" content="<?= !empty($masjid['foto_utama']) ? $storage->url($masjid['foto_utama']) : env('seo.ogImage') ?>">

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#065F46",
                        "primary-light": "#059669",
                        "background-light": "#f6f8f7",
                        "background-dark": "#061510",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <style type="text/tailwindcss">
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .btn-primary {
            @apply bg-primary text-white px-5 py-2 rounded-lg text-sm font-bold hover:bg-emerald-900 transition-all shadow-sm;
        }
        .btn-primary-lg {
            @apply bg-primary text-white px-8 py-4 rounded-xl text-base font-bold hover:scale-[1.02] transition-transform shadow-lg shadow-primary/20;
        }
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-[#111815] dark:text-white transition-colors duration-300">
    
    <?= $this->include('layout/navbar_masjid') ?>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div id="flash-success" class="fixed top-24 right-6 z-50 bg-emerald-500 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 animate-in fade-in slide-in-from-right duration-500">
            <span class="material-symbols-outlined">check_circle</span>
            <div class="font-bold"><?= session()->getFlashdata('success') ?></div>
            <button onclick="document.getElementById('flash-success').remove()" class="ml-2 hover:bg-white/20 rounded-full p-1 transition-colors">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>
        <script>setTimeout(() => document.getElementById('flash-success').remove(), 5000);</script>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div id="flash-error" class="fixed top-24 right-6 z-50 bg-red-500 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 animate-in fade-in slide-in-from-right duration-500">
            <span class="material-symbols-outlined">error</span>
            <div class="font-bold"><?= session()->getFlashdata('error') ?></div>
            <button onclick="document.getElementById('flash-error').remove()" class="ml-2 hover:bg-white/20 rounded-full p-1 transition-colors">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>
        <script>setTimeout(() => document.getElementById('flash-error').remove(), 5000);</script>
    <?php endif; ?>

    <main>
        <?= $this->renderSection('content') ?>
    </main>

    <?= $this->include('layout/footer') ?>

</body>
</html>
