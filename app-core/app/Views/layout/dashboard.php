<!DOCTYPE html>
<html class="light" lang="id"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?= $title ?? env('seo.title') ?></title>
<link rel="icon" type="image/png" href="<?= asset_url('ico_masjid.png') ?>">
<meta name="description" content="<?= env('seo.description') ?>">
<meta name="author" content="<?= env('seo.author') ?>">
<meta name="robots" content="noindex, nofollow">
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "primary": "#065F46",
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
    .sidebar-scroll::-webkit-scrollbar {
        width: 4px;
    }
    .sidebar-scroll::-webkit-scrollbar-thumb {
        background: #E2E8F0;
        border-radius: 10px;
    }
</style>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
</head>
<body class="bg-background-light dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen flex">

    <?= $this->include('layout/sidebar_dashboard') ?>

    <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        <?= $this->include('layout/header_dashboard') ?>

        <div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 space-y-6 lg:space-y-8">
            <?= $this->renderSection('content') ?>
        </div>
    </main>

    <?php // Buka/tutup drawer sidebar di mobile. Di desktop (lg) sidebar selalu
          // tampil, jadi fungsi ini praktis hanya dipakai di layar kecil. ?>
    <script>
    function toggleSidebar(buka) {
        const sb = document.getElementById('dashboardSidebar');
        const bd = document.getElementById('sidebarBackdrop');
        if (!sb || !bd) return;
        if (buka) {
            sb.classList.remove('-translate-x-full');
            bd.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // cegah scroll latar saat drawer buka
        } else {
            sb.classList.add('-translate-x-full');
            bd.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }
    // Klik menu di dalam sidebar (mobile) langsung menutup drawer.
    document.addEventListener('DOMContentLoaded', function () {
        const sb = document.getElementById('dashboardSidebar');
        if (!sb) return;
        sb.querySelectorAll('a').forEach(function (a) {
            a.addEventListener('click', function () {
                if (window.innerWidth < 1024) toggleSidebar(false);
            });
        });
    });
    </script>
    <?= $this->renderSection('scripts') ?>
</body></html>
