<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= $title ?? env('seo.title') ?></title>
    <link rel="icon" type="image/png" href="<?= asset_url('ico_masjid.png') ?>">
    <meta name="description" content="<?= env('seo.description') ?>">
    <meta name="keywords" content="<?= env('seo.keywords') ?>">
    <meta name="author" content="<?= env('seo.author') ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= current_url() ?>">
    <meta property="og:title" content="<?= $title ?? env('seo.title') ?>">
    <meta property="og:description" content="<?= env('seo.description') ?>">
    <meta property="og:image" content="<?= env('seo.ogImage') ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?= current_url() ?>">
    <meta property="twitter:title" content="<?= $title ?? env('seo.title') ?>">
    <meta property="twitter:description" content="<?= env('seo.description') ?>">
    <meta property="twitter:image" content="<?= env('seo.ogImage') ?>">
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
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(8px);
        }.btn-primary {
            @apply bg-primary text-white px-5 py-2 rounded-lg text-sm font-bold hover:bg-emerald-900 transition-all shadow-sm;
        }
        .btn-primary-lg {
            @apply bg-primary text-white px-8 py-4 rounded-xl text-base font-bold hover:scale-[1.02] transition-transform shadow-lg shadow-primary/20;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-[#111815] dark:text-white transition-colors duration-300">
    
    <?= $this->include('layout/navbar') ?>

    <main>
        <?= $this->renderSection('content') ?>
    </main>

    <?= $this->include('layout/footer') ?>

</body>
</html>
