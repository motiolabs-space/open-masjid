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
<main class="relative z-20 w-full max-w-[440px] px-6 py-6">
<div class="bg-white dark:bg-slate-900 shadow-xl shadow-emerald-950/5 rounded-2xl border border-zinc-100 dark:border-slate-800 p-6 md:p-8">
<header class="flex flex-col items-center mb-6 text-center">
<img src="<?= asset_url('logo_masjid_200.png') ?>" alt="Logo Masjid" class="size-12 mx-auto mb-3">
<h2 class="text-slate-900 dark:text-white tracking-tight text-xl font-bold leading-tight">
                    Dashboard Masjid
                </h2>
<p class="text-slate-500 dark:text-slate-400 text-sm font-normal leading-relaxed mt-1 max-w-[280px]">
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
                    
<div class="relative flex items-center py-2">
    <div class="flex-grow border-t border-slate-200 dark:border-slate-700"></div>
    <span class="flex-shrink-0 mx-4 text-slate-400 text-xs">Atau</span>
    <div class="flex-grow border-t border-slate-200 dark:border-slate-700"></div>
</div>

<a href="<?= base_url('auth/google') ?>" class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold py-3.5 px-4 rounded-lg shadow-sm transition-all active:scale-[0.99] flex items-center justify-center gap-2">
    <svg class="w-5 h-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
    </svg>
    Masuk dengan Google
</a>
<div class="text-center">
<p class="text-sm text-slate-500 dark:text-slate-400">
                            Belum memiliki akun?
                            <a href="<?= base_url('register') ?>" class="text-primary font-bold hover:underline">Daftar Sekarang</a>
</p>
</div>
</div>
</form>
<footer class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex flex-col items-center gap-4">
<a class="flex items-center gap-2 text-slate-500 dark:text-slate-400 hover:text-primary text-sm font-medium transition-colors" href="<?= base_url() ?>">
<span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    Kembali ke Beranda
                </a>
</footer>
</div>
<p class="mt-4 text-center text-slate-400 dark:text-slate-500 text-[10px] uppercase tracking-[0.2em] font-medium">
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
