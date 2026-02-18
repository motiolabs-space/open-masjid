<!DOCTYPE html>
<html class="light" lang="id">

<head>
    <?php $seo = config('SEO'); ?>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <link rel="icon" type="image/png" href="<?= asset_url('ico_masjid.png') ?>">
    
    <!-- SEO Tags -->
    <title><?= $title ?? $seo->title ?></title>
    <meta name="description" content="<?= $description ?? $seo->description ?>">
    <meta name="keywords" content="<?= $keywords ?? $seo->keywords ?>">
    <meta name="author" content="<?= $seo->author ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= current_url() ?>">
    <meta property="og:title" content="<?= $title ?? $seo->title ?>">
    <meta property="og:description" content="<?= $description ?? $seo->description ?>">
    <meta property="og:image" content="<?= $seo->ogImage ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?= current_url() ?>">
    <meta property="twitter:title" content="<?= $title ?? $seo->title ?>">
    <meta property="twitter:description" content="<?= $description ?? $seo->description ?>">
    <meta property="twitter:image" content="<?= $seo->ogImage ?>">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#24a871",
                        "primary-light": "#2dc887",
                        "background-light": "#f6f7f8",
                        "background-dark": "#22262a",
                    },
                    fontFamily: {
                        "display": ["Manrope", "sans-serif"]
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
        body {
            font-family: 'Manrope', sans-serif;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(8px);
        }
        .btn-primary {
            @apply bg-primary text-white px-5 py-2.5 rounded-lg text-sm font-bold shadow-lg shadow-primary/20 hover:bg-[#1f8e5f] transition-all;
        }
        .btn-primary-lg {
            @apply bg-primary text-white px-8 py-4 rounded-xl text-lg font-bold shadow-xl shadow-primary/30 hover:scale-[1.02] transition-transform;
        }
        .feature-card:hover {
            transform: translateY(-2px);
            transition: all 0.2s ease-in-out;
        }
    </style>
    <?= $this->renderSection('extra_head') ?>
</head>

<body class="bg-background-light dark:bg-background-dark text-[#121715] dark:text-white transition-colors duration-300">
    <div class="relative flex min-h-screen w-full flex-col">
        <?= $this->include('layout/navbar') ?>

        <main class="flex-1">
            <?= $this->renderSection('content') ?>
        </main>

        <?= $this->include('layout/footer') ?>
    </div>
</body>

</html>
