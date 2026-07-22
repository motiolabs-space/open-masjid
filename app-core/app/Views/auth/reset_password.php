<!DOCTYPE html>
<html class="light" lang="id"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<link rel="icon" type="image/png" href="<?= asset_url('ico_masjid.png') ?>">
<title><?= esc($title ?? 'Atur Password Baru') ?></title>
<script src="https://cdn.tailwindcss.com?plugins=forms"></script>
<script>tailwind.config = { theme: { extend: { colors: { primary: "#065F46" } } } };</script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
<style>body{font-family:'Inter',sans-serif}</style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-slate-100 p-8">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center size-14 bg-primary/10 text-primary rounded-2xl mb-3">
                <span class="material-symbols-outlined text-3xl">password</span>
            </div>
            <h2 class="text-xl font-bold text-slate-900">Atur Password Baru</h2>
            <p class="text-slate-500 text-sm mt-1">Masukkan password baru untuk akun Anda.</p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="mb-5 p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-center text-sm font-medium">
                <?= esc(session()->getFlashdata('error')) ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('reset-password') ?>" method="POST" class="space-y-5">
            <?= csrf_field() ?>
            <input type="hidden" name="token" value="<?= esc($token, 'attr') ?>">
            <div class="flex flex-col gap-1.5">
                <label class="text-slate-700 text-sm font-semibold">Password Baru</label>
                <input name="password" type="password" required minlength="8"
                       placeholder="Minimal 8 karakter"
                       class="form-input w-full rounded-lg border border-slate-200 bg-slate-50/50 focus:border-primary focus:ring-4 focus:ring-primary/10 h-12 p-4 text-sm transition-all"/>
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-slate-700 text-sm font-semibold">Ulangi Password</label>
                <input name="password_confirm" type="password" required minlength="8"
                       placeholder="Ketik ulang password"
                       class="form-input w-full rounded-lg border border-slate-200 bg-slate-50/50 focus:border-primary focus:ring-4 focus:ring-primary/10 h-12 p-4 text-sm transition-all"/>
            </div>
            <button type="submit" class="w-full h-12 bg-primary hover:bg-emerald-800 text-white font-bold rounded-lg transition-all">
                Simpan Password Baru
            </button>
        </form>
    </div>
</body></html>
