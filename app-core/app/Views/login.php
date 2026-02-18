<!DOCTYPE html>
<html class="light" lang="id"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<link rel="icon" type="image/png" href="<?= asset_url('ico_masjid.png') ?>">
<title>Dashboard Masjid - Login</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#065F46",
                        "background-light": "#f7f7f7",
                        "background-dark": "#0f172a",
                    },
                    fontFamily: {
                        "sans": ["Inter", "sans-serif"],
                        "display": ["Poppins", "sans-serif"]
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
            font-family: "Inter", sans-serif;
        }
        h2 {
            font-family: "Poppins", sans-serif;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-sans min-h-screen flex items-center justify-center relative overflow-hidden">
<div class="absolute inset-0 z-0 overflow-hidden">
<div class="absolute inset-0 bg-zinc-50/90 dark:bg-slate-950/95 z-10"></div>
<div class="w-full h-full bg-center bg-no-repeat bg-cover opacity-20 grayscale" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBudonmMogHC4Qe65DREMA52svEPfFH92RtpMlwG0k2wRfQcIcuOPsOk9Ep3zWe8gPilv4CEBZ-K6fkkfXzEoYmu9gEAT_ukIUA5qejOT9j1ZQJZFViULJg7BIX3KDhxvvmeXCpt-HtQI-FHQ9i4i2fd3RFMdZuik91j0maZskUhjRx8pEUZsFPGVYllQYHrL9H4k5t42I-l_yXom8f8rWRkyiw0SMJ3NkgL2Jd9UqWQBxIzrHSsu3Z7n5rfEY-8Zv7mEUNJCCnIqO6");'>
</div>
</div>
<main class="relative z-20 w-full max-w-[440px] px-6 py-12">
<div class="bg-white dark:bg-slate-900 shadow-xl shadow-emerald-950/5 rounded-2xl border border-zinc-100 dark:border-slate-800 p-8 md:p-10">
<header class="flex flex-col items-center mb-10 text-center">
<img src="<?= asset_url('logo_masjid_200.png') ?>" alt="Logo Masjid" class="size-16 mx-auto mb-6">
<h2 class="text-slate-900 dark:text-white tracking-tight text-2xl font-bold leading-tight">
                    Dashboard Masjid
                </h2>
<p class="text-slate-500 dark:text-slate-400 text-sm font-normal leading-relaxed mt-3 max-w-[280px]">
                    Selamat datang kembali untuk menyebarkan kebaikan bersama masjid
                </p>
</header>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-center text-sm font-medium">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-center text-sm font-medium">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('login') ?>" method="POST" class="space-y-6">
            <?= csrf_field() ?>
<div class="flex flex-col gap-1.5">
<label class="text-slate-700 dark:text-slate-300 text-sm font-semibold px-0.5">
                        Email
                    </label>
<input name="email" class="form-input flex w-full rounded-lg text-slate-900 dark:text-white focus:outline-0 focus:ring-4 focus:ring-primary/10 border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 focus:border-primary h-12 placeholder:text-slate-400 p-4 text-sm font-normal transition-all" placeholder="Masukkan alamat email Anda" type="email" required/>
</div>
<div class="flex flex-col gap-1.5">
<div class="flex justify-between items-center px-0.5">
<label class="text-slate-700 dark:text-slate-300 text-sm font-semibold">
                            Password
                        </label>
<a class="text-primary hover:text-emerald-800 text-xs font-bold transition-colors" href="#">
                            Lupa Password?
                        </a>
</div>
<div class="relative flex w-full items-stretch">
<input name="password" class="form-input flex w-full rounded-lg text-slate-900 dark:text-white focus:outline-0 focus:ring-4 focus:ring-primary/10 border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 focus:border-primary h-12 placeholder:text-slate-400 p-4 pr-12 text-sm font-normal transition-all" placeholder="••••••••" type="password" required/>
<button class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200" type="button" onclick="const input = this.previousElementSibling; input.type = input.type === 'password' ? 'text' : 'password'; this.firstElementChild.innerText = input.type === 'password' ? 'visibility' : 'visibility_off';">
<span class="material-symbols-outlined text-[20px]">visibility</span>
</button>
</div>
</div>
<div class="flex items-center gap-2.5 px-0.5">
<input class="rounded border-slate-300 text-primary focus:ring-primary w-4 h-4 cursor-pointer" id="remember" type="checkbox"/>
<label class="text-xs text-slate-500 dark:text-slate-400 cursor-pointer select-none" for="remember">Ingat saya di perangkat ini</label>
</div>
<div class="pt-2 flex flex-col gap-4">
<button class="w-full bg-primary hover:bg-[#044e3a] text-white font-bold py-3.5 px-4 rounded-lg shadow-md shadow-emerald-900/10 transition-all active:scale-[0.99] flex items-center justify-center gap-2">
                        Masuk Dashboard
                    </button>
<div class="text-center">
<p class="text-sm text-slate-500 dark:text-slate-400">
                            Belum memiliki akun?
                            <a href="<?= base_url('register') ?>" class="text-primary font-bold hover:underline">Daftar Sekarang</a>
</p>
</div>
</div>
</form>
<footer class="mt-10 pt-8 border-t border-slate-100 dark:border-slate-800 flex flex-col items-center gap-6">
<a class="flex items-center gap-2 text-slate-500 dark:text-slate-400 hover:text-primary text-sm font-medium transition-colors" href="<?= base_url() ?>">
<span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    Kembali ke Beranda
                </a>
</footer>
</div>
<p class="mt-8 text-center text-slate-400 dark:text-slate-500 text-[10px] uppercase tracking-[0.2em] font-medium">
            © <?= date('Y') ?> <?= env('APP_FOUNDATION_NAME', 'Yayasan Masjid Digital Indonesia') ?>
        </p>
</main>
<div class="fixed bottom-6 right-6 z-50">
<button class="p-3 bg-white dark:bg-slate-800 rounded-full shadow-lg border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors" onclick="document.documentElement.classList.toggle('dark')">
<span class="material-symbols-outlined block dark:hidden">dark_mode</span>
<span class="material-symbols-outlined hidden dark:block">light_mode</span>
</button>
</div>

</body></html>
