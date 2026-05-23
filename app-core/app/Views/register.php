<!DOCTYPE html>
<html class="light" lang="id"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<link rel="icon" type="image/png" href="<?= asset_url('ico_masjid.png') ?>">
<title>Registrasi - Masj.id</title>
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
                        "background-light": "#F8FAFC",
                    },
                    fontFamily: {
                        "sans": ["Inter", "sans-serif"],
                        "display": ["Poppins", "sans-serif"]
                    },
                    boxShadow: {
                        'soft': '0 4px 20px -2px rgba(0, 0, 0, 0.05), 0 2px 10px -2px rgba(0, 0, 0, 0.03)',
                        'card': '0 20px 50px -12px rgba(6, 95, 70, 0.08)',
                    }
                },
            },
        }
    </script>
<style type="text/tailwindcss">
        @layer base {
            body {
                @apply font-sans text-slate-900 bg-background-light antialiased;
            }
        }
        .role-card-active {
            @apply border-primary bg-emerald-50/40 ring-1 ring-primary;
        }
        .input-group:focus-within .input-prefix {
            @apply border-primary text-primary;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 md:p-12 relative">
<div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
<div class="absolute top-[-10%] right-[-5%] w-[35%] h-[35%] bg-emerald-100/40 rounded-full blur-[120px]"></div>
<div class="absolute bottom-[-5%] left-[-5%] w-[30%] h-[30%] bg-slate-200/50 rounded-full blur-[120px]"></div>
</div>
<main class="relative z-10 w-full max-w-2xl">
<div class="text-center mb-10">
<img src="<?= asset_url('logo_masjid_200.png') ?>" alt="Logo Masj.id" class="size-16 mx-auto mb-6 px-1">
<h1 class="text-3xl md:text-4xl font-display font-bold text-slate-900 tracking-tight" id="page-title">Pendaftaran Akun Baru</h1>
<p class="text-slate-500 mt-3 text-lg" id="page-subtitle">Pilih kategori pendaftaran untuk memulai</p>
</div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="mb-10 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-center font-medium">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="mb-10 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl text-center font-medium">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-10">
    <!-- Role Masjid -->
    <button onclick="setRole('masjid')" id="btn-role-masjid" class="flex items-start gap-5 p-6 bg-white border border-slate-200 rounded-2xl shadow-soft transition-all hover:border-emerald-200 role-card-active group text-left">
        <div class="flex-shrink-0 w-12 h-12 bg-primary rounded-xl flex items-center justify-center text-white transition-all duration-300">
            <span class="material-symbols-outlined text-2xl">corporate_fare</span>
        </div>
        <div class="pt-0.5">
            <h3 class="font-bold text-slate-900 text-lg leading-tight">Masjid</h3>
            <p class="text-sm text-slate-500 mt-1 leading-relaxed">Pengelola DKM, operasional, & kegiatan masjid</p>
        </div>
    </button>
    <!-- Role Jamaah -->
    <button onclick="setRole('jamaah')" id="btn-role-jamaah" class="flex items-start gap-5 p-6 bg-white border border-slate-200 rounded-2xl shadow-soft transition-all hover:border-emerald-200 text-left group">
        <div class="flex-shrink-0 w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center text-slate-500 group-hover:bg-primary/10 group-hover:text-primary transition-all duration-300">
            <span class="material-symbols-outlined text-2xl">person</span>
        </div>
        <div class="pt-0.5">
            <h3 class="font-bold text-slate-900 text-lg leading-tight">Jamaah</h3>
            <p class="text-sm text-slate-500 mt-1 leading-relaxed">Personal user untuk dukungan & donasi</p>
        </div>
    </button>
</div>

<div class="bg-white rounded-[2.5rem] shadow-card border border-slate-100 p-8 md:p-14">
    <!-- Form Masjid -->
    <form id="form-masjid" action="<?= base_url('register/masjid') ?>" method="POST" class="space-y-12">
        <?= csrf_field() ?>
        <section class="space-y-7">
            <div class="flex items-center gap-3 mb-1">
                <div class="w-1.5 h-6 bg-primary rounded-full"></div>
                <h2 class="font-display font-bold text-xl text-slate-800 tracking-tight">Informasi Masjid</h2>
            </div>
            <div class="grid grid-cols-1 gap-7">
                <div class="space-y-2.5">
                    <label class="text-sm font-bold text-slate-700 ml-1">Nama Masjid</label>
                    <input name="nama_masjid" class="w-full h-[54px] px-4 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all placeholder:text-slate-300 text-slate-900" placeholder="Contoh: Masjid Agung Al-Azhar" type="text" required/>
                </div>
                <div class="space-y-2.5">
                    <label class="text-sm font-bold text-slate-700 ml-1">Username Masjid</label>
                    <div class="group relative flex items-center input-group">
                        <div class="input-prefix h-[54px] flex items-center px-4 bg-slate-50 border border-r-0 border-slate-200 rounded-l-xl text-slate-400 font-medium transition-colors">
                            masj.id/
                        </div>
                        <input name="username_masjid" id="username_masjid" class="flex-1 h-[54px] px-4 bg-white border border-slate-200 rounded-r-xl focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all placeholder:text-slate-300 text-slate-900 font-medium" placeholder="nama-masjid-anda" type="text" required/>
                        <div id="availability-badge" class="absolute right-4 flex items-center gap-1.5 bg-white pl-2 hidden">
                            <!-- Content will be injected by JS -->
                        </div>
                    </div>
                    <p class="text-[12px] text-slate-400 px-1 leading-relaxed">Digunakan sebagai tautan publik profil masjid Anda.</p>
                </div>
            </div>
        </section>
        <section class="space-y-7">
            <div class="flex items-center gap-3 mb-1">
                <div class="w-1.5 h-6 bg-primary rounded-full"></div>
                <h2 class="font-display font-bold text-xl text-slate-800 tracking-tight">Data Pengelola (PIC)</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-7">
                <div class="md:col-span-2 space-y-2.5">
                    <label class="text-sm font-bold text-slate-700 ml-1">Nama Lengkap PIC</label>
                    <input name="nama_pic" class="w-full h-[54px] px-4 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all placeholder:text-slate-300 text-slate-900" placeholder="Nama lengkap penanggung jawab" type="text" required/>
                </div>
                <div class="space-y-2.5 relative">
                    <label class="text-sm font-bold text-slate-700 ml-1">Email</label>
                    <input name="email_pic" id="email_pic" class="w-full h-[54px] px-4 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all placeholder:text-slate-300 text-slate-900" placeholder="nama@email.com" type="email" required/>
                    <div id="email-badge-pic" class="absolute right-4 top-[45px] flex items-center gap-1.5 bg-white pl-2 hidden"></div>
                </div>
                <div class="space-y-2.5">
                    <label class="text-sm font-bold text-slate-700 ml-1">Nomor WhatsApp</label>
                    <div class="relative flex items-center">
                        <span class="absolute left-4 text-slate-400 font-medium">+62</span>
                        <input name="phone_pic" class="w-full h-[54px] pl-14 pr-4 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all placeholder:text-slate-300 text-slate-900" placeholder="8123456789" type="tel" required/>
                    </div>
                </div>
                <div class="md:col-span-2 space-y-2.5">
                    <label class="text-sm font-bold text-slate-700 ml-1">Kata Sandi</label>
                    <div class="relative flex items-center">
                        <input name="password_pic" class="w-full h-[54px] px-4 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all placeholder:text-slate-300 text-slate-900" placeholder="Minimal 8 karakter" type="password" required minlength="8"/>
                        <button class="absolute right-4 text-slate-400 hover:text-primary transition-colors flex items-center justify-center" type="button" onclick="togglePassword(this)">
                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                        </button>
                    </div>
                </div>
            </div>
        </section>
        <div class="pt-4 space-y-8">
            <div class="flex items-start gap-3.5 px-1">
                <div class="flex items-center h-5 mt-0.5">
                    <input class="w-5 h-5 text-primary border-slate-300 rounded focus:ring-primary/20 transition-all cursor-pointer" id="terms-masjid" type="checkbox"/>
                </div>
                <label class="text-xs text-slate-500 leading-relaxed cursor-pointer" for="terms-masjid">
                    Dengan menekan tombol daftar, saya menyatakan setuju dengan <a class="text-primary font-bold hover:underline" href="#">Syarat & Ketentuan</a> serta <a class="text-primary font-bold hover:underline" href="#">Kebijakan Privasi</a> Masj.id.
                </label>
            </div>
            <button class="w-full bg-primary hover:bg-[#044a36] text-white font-display font-bold py-5 rounded-2xl shadow-xl shadow-primary/20 transition-all hover:-translate-y-0.5 active:scale-[0.98] text-lg">
                Daftar Sekarang (Masjid)
            </button>
            <div class="text-center space-y-6">
                <p class="text-[13px] text-slate-400 font-medium">
                    <span class="italic text-slate-400/80">*Kelengkapan profil dapat diperbarui setelah login di Dashboard</span>
                </p>
                <div class="pt-6 border-t border-slate-100">
                    <p class="text-sm text-slate-600">
                        Sudah memiliki akun? 
                        <a class="text-primary font-bold hover:underline ml-1" href="<?= base_url('login') ?>">Masuk di sini</a>
                    </p>
                </div>
            </div>
        </div>
    </form>

    <!-- Form Jamaah -->
    <form id="form-jamaah" action="<?= base_url('register/jamaah') ?>" method="POST" class="space-y-10 hidden">
        <?= csrf_field() ?>
        <section class="space-y-7">
            <div class="flex items-center gap-3 mb-1">
                <div class="w-1.5 h-6 bg-primary rounded-full"></div>
                <h2 class="font-display font-bold text-xl text-slate-800 tracking-tight">Data Pribadi Jamaah</h2>
            </div>
            <div class="grid grid-cols-1 gap-7">
                <div class="space-y-2.5">
                    <label class="text-sm font-bold text-slate-700 ml-1">Nama Lengkap</label>
                    <input name="nama_lengkap" class="w-full h-[54px] px-4 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all placeholder:text-slate-300 text-slate-900" placeholder="Masukkan nama lengkap sesuai identitas" type="text" required/>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-7">
                    <div class="space-y-2.5 relative">
                        <label class="text-sm font-bold text-slate-700 ml-1">Email</label>
                        <input name="email" id="email_jamaah" class="w-full h-[54px] px-4 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all placeholder:text-slate-300 text-slate-900" placeholder="nama@email.com" type="email" required/>
                        <div id="email-badge-jamaah" class="absolute right-4 top-[45px] flex items-center gap-1.5 bg-white pl-2 hidden"></div>
                    </div>
                    <div class="space-y-2.5">
                        <label class="text-sm font-bold text-slate-700 ml-1">Nomor WhatsApp</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-4 text-slate-400 font-medium">+62</span>
                            <input name="phone" class="w-full h-[54px] pl-14 pr-4 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all placeholder:text-slate-300 text-slate-900" placeholder="8123456789" type="tel" required/>
                        </div>
                    </div>
                </div>
                <div class="space-y-2.5">
                    <label class="text-sm font-bold text-slate-700 ml-1">Kata Sandi</label>
                    <div class="relative">
                        <input name="password" class="w-full h-[54px] px-4 pr-12 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all placeholder:text-slate-300 text-slate-900" placeholder="Min. 8 karakter" type="password" required minlength="8"/>
                        <button class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors" type="button" onclick="togglePassword(this)">
                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                        </button>
                    </div>
                </div>
            </div>
        </section>
        <div class="pt-4 space-y-8">
            <div class="flex items-start gap-3.5 px-1">
                <div class="flex items-center h-5 mt-0.5">
                    <input class="w-5 h-5 text-primary border-slate-300 rounded focus:ring-primary/20 transition-all cursor-pointer" id="terms-jamaah" type="checkbox"/>
                </div>
                <label class="text-xs text-slate-500 leading-relaxed cursor-pointer" for="terms-jamaah">
                    Dengan menekan tombol daftar, saya menyatakan setuju dengan <a class="text-primary font-bold hover:underline" href="#">Syarat & Ketentuan</a> serta <a class="text-primary font-bold hover:underline" href="#">Kebijakan Privasi</a> Masj.id.
                </label>
            </div>
            <button class="w-full bg-primary hover:bg-[#044a36] text-white font-display font-bold py-5 rounded-2xl shadow-xl shadow-primary/20 transition-all hover:-translate-y-0.5 active:scale-[0.98] text-lg">
                Daftar Sekarang (Jamaah)
            </button>
            
            <div class="relative flex items-center py-2">
                <div class="flex-grow border-t border-slate-200"></div>
                <span class="flex-shrink-0 mx-4 text-slate-400 text-xs">Atau</span>
                <div class="flex-grow border-t border-slate-200"></div>
            </div>

            <a href="<?= base_url('auth/google') ?>" class="w-full bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-display font-bold py-5 rounded-2xl shadow-sm transition-all hover:-translate-y-0.5 active:scale-[0.98] text-lg flex items-center justify-center gap-3">
                <svg class="w-6 h-6" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                Daftar dengan Google
            </a>
            <div class="text-center">
                <div class="pt-6 border-t border-slate-100">
                    <p class="text-sm text-slate-600">
                        Sudah memiliki akun? 
                        <a class="text-primary font-bold hover:underline ml-1" href="<?= base_url('login') ?>">Masuk di sini</a>
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>

<footer class="mt-16 mb-8 text-center space-y-6">
    <div class="flex items-center justify-center gap-8">
        <a class="text-[11px] font-bold text-slate-400 hover:text-primary transition-colors uppercase tracking-[0.15em]" href="<?= base_url('bantuan') ?>">Bantuan</a>
        <a class="text-[11px] font-bold text-slate-400 hover:text-primary transition-colors uppercase tracking-[0.15em]" href="<?= base_url('tentang') ?>">Tentang</a>
        <a class="text-[11px] font-bold text-slate-400 hover:text-primary transition-colors uppercase tracking-[0.15em]" href="<?= base_url('kontak') ?>">Hubungi Kami</a>
    </div>
    <p class="text-[10px] text-slate-300 font-bold tracking-[0.25em] uppercase">
        © <?= date('Y') ?> <?= env('APP_FOUNDATION_NAME', 'Yayasan Masjid Digital Indonesia') ?> — Profesional & Aman
    </p>
</footer>
</main>

<script>
    function setRole(role) {
        const btnMasjid = document.getElementById('btn-role-masjid');
        const btnJamaah = document.getElementById('btn-role-jamaah');
        const formMasjid = document.getElementById('form-masjid');
        const formJamaah = document.getElementById('form-jamaah');
        const title = document.getElementById('page-title');
        const subtitle = document.getElementById('page-subtitle');

        if (role === 'masjid') {
            btnMasjid.classList.add('role-card-active');
            btnMasjid.querySelector('div').classList.remove('bg-slate-100', 'text-slate-500');
            btnMasjid.querySelector('div').classList.add('bg-primary', 'text-white');
            
            btnJamaah.classList.remove('role-card-active');
            btnJamaah.querySelector('div').classList.add('bg-slate-100', 'text-slate-500');
            btnJamaah.querySelector('div').classList.remove('bg-primary', 'text-white');

            formMasjid.classList.remove('hidden');
            formJamaah.classList.add('hidden');
            
            subtitle.innerText = 'Pilih kategori pendaftaran untuk memulai';
        } else {
            btnJamaah.classList.add('role-card-active');
            btnJamaah.querySelector('div').classList.remove('bg-slate-100', 'text-slate-500');
            btnJamaah.querySelector('div').classList.add('bg-primary', 'text-white');
            
            btnMasjid.classList.remove('role-card-active');
            btnMasjid.querySelector('div').classList.add('bg-slate-100', 'text-slate-500');
            btnMasjid.querySelector('div').classList.remove('bg-primary', 'text-white');

            formJamaah.classList.remove('hidden');
            formMasjid.classList.add('hidden');
            
            subtitle.innerText = 'Daftar sebagai Jamaah untuk akses layanan & donasi';
        }
    }

    function togglePassword(btn) {
        const input = btn.previousElementSibling;
        const icon = btn.querySelector('span');
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerText = 'visibility_off';
        } else {
            input.type = 'password';
            icon.innerText = 'visibility';
        }
    }

    // Username Availability Check
    const usernameInput = document.getElementById('username_masjid');
    const badge = document.getElementById('availability-badge');
    let timeout = null;

    if (usernameInput) {
        usernameInput.addEventListener('input', function() {
            const username = this.value.trim().toLowerCase();
            clearTimeout(timeout);
            
            if (username === '') {
                badge.classList.add('hidden');
                return;
            }

            badge.classList.remove('hidden');
            badge.innerHTML = `<span class="text-[10px] text-slate-400">Memeriksa...</span>`;

            timeout = setTimeout(() => {
                fetch(`<?= base_url('auth/check-username') ?>?username=${username}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.available) {
                            badge.innerHTML = `
                                <span class="material-symbols-outlined text-emerald-600 text-[18px] fill-1">check_circle</span>
                                <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-[0.1em]">Tersedia</span>
                            `;
                        } else {
                            badge.innerHTML = `
                                <span class="material-symbols-outlined text-rose-500 text-[18px] fill-1">cancel</span>
                                <span class="text-[10px] font-bold text-rose-500 uppercase tracking-[0.1em]">Terpakai</span>
                            `;
                        }
                    })
                    .catch(err => {
                        badge.classList.add('hidden');
                    });
            }, 500);
        });
    }

    // Email Availability Check (PIC)
    const emailPicInput = document.getElementById('email_pic');
    const emailBadgePic = document.getElementById('email-badge-pic');
    
    if (emailPicInput) {
        emailPicInput.addEventListener('input', function() {
            checkEmailAvailability(this.value, emailBadgePic);
        });
    }

    // Email Availability Check (Jamaah)
    const emailJamaahInput = document.getElementById('email_jamaah');
    const emailBadgeJamaah = document.getElementById('email-badge-jamaah');
    
    if (emailJamaahInput) {
        emailJamaahInput.addEventListener('input', function() {
            checkEmailAvailability(this.value, emailBadgeJamaah);
        });
    }

    let emailTimeout = null;
    function checkEmailAvailability(email, badgeElement) {
        clearTimeout(emailTimeout);
        if (email === '' || !email.includes('@')) {
            badgeElement.classList.add('hidden');
            return;
        }

        badgeElement.classList.remove('hidden');
        badgeElement.innerHTML = `<span class="text-[10px] text-slate-400">Memeriksa...</span>`;

        emailTimeout = setTimeout(() => {
            fetch(`<?= base_url('auth/check-email') ?>?email=${email}`)
                .then(response => response.json())
                .then(data => {
                    if (data.available) {
                        badgeElement.innerHTML = `
                            <span class="material-symbols-outlined text-emerald-600 text-[18px] fill-1">check_circle</span>
                            <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-[0.1em]">Tersedia</span>
                        `;
                    } else {
                        badgeElement.innerHTML = `
                            <span class="material-symbols-outlined text-rose-500 text-[18px] fill-1">cancel</span>
                            <span class="text-[10px] font-bold text-rose-500 uppercase tracking-[0.1em]">Terdaftar</span>
                        `;
                    }
                })
                .catch(err => {
                    badgeElement.classList.add('hidden');
                });
        }, 500);
    }
</script>

</body></html>
