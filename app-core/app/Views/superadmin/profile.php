<?= $this->extend('layout/superadmin') ?>

<?= $this->section('content') ?>

<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-slate-200 dark:border-slate-800">
            <h4 class="font-bold text-slate-800 dark:text-white">Pengaturan Keamanan</h4>
            <p class="text-sm text-slate-500 mt-1">Kelola informasi akun dan kata sandi Anda.</p>
        </div>
        
        <form action="<?= base_url('superadmin/update-password') ?>" method="POST" class="p-8 space-y-6">
            <?= csrf_field() ?>
            
            <?php if(session()->getFlashdata('success')): ?>
                <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 rounded-xl text-sm font-bold flex items-center gap-2 border border-emerald-100 dark:border-emerald-900/30">
                    <span class="material-symbols-outlined text-sm">check_circle</span>
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if(session()->getFlashdata('error')): ?>
                <div class="p-4 bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 rounded-xl text-sm font-bold flex items-center gap-2 border border-rose-100 dark:border-rose-900/30">
                    <span class="material-symbols-outlined text-sm">error</span>
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <div class="grid gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Email Akun</label>
                    <input type="text" value="<?= esc($user['email']) ?>" disabled class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-slate-500 cursor-not-allowed">
                </div>

                <hr class="border-slate-100 dark:border-slate-800">

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Password Saat Ini</label>
                    <input type="password" name="current_password" required placeholder="••••••••" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-primary">
                    <?php if(isset(session('errors')['current_password'])): ?>
                        <p class="text-xs text-rose-500 mt-1"><?= session('errors')['current_password'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Password Baru</label>
                    <input type="password" name="new_password" required placeholder="Minimal 6 karakter" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-primary">
                    <?php if(isset(session('errors')['new_password'])): ?>
                        <p class="text-xs text-rose-500 mt-1"><?= session('errors')['new_password'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Konfirmasi Password Baru</label>
                    <input type="password" name="confirm_password" required placeholder="Ulangi password baru" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-primary">
                    <?php if(isset(session('errors')['confirm_password'])): ?>
                        <p class="text-xs text-rose-500 mt-1"><?= session('errors')['confirm_password'] ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-primary text-white font-bold py-3.5 rounded-xl hover:bg-primary-dark transition-all shadow-lg shadow-primary/20 flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-sm">save</span>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
