<?= $this->extend('layout/superadmin') ?>

<?= $this->section('content') ?>
<?php 
$isEdit = isset($user); 
$actionUrl = $isEdit ? base_url('superadmin/users/update/' . $user['id']) : base_url('superadmin/users/save');
?>
<div class="max-w-2xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-800 dark:text-white"><?= $isEdit ? 'Edit User' : 'Tambah User Baru' ?></h1>
            <p class="text-slate-500 mt-1">Lengkapi data user di bawah ini.</p>
        </div>
        <a href="<?= base_url('superadmin/users') ?>" class="text-slate-400 hover:text-slate-600 transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
    </div>

    <?php if(session()->getFlashdata('error')): ?>
    <div class="mb-6 bg-rose-500/10 border-l-4 border-rose-500 p-4 rounded-r-lg">
        <p class="text-rose-700 font-medium"><?= session()->getFlashdata('error') ?></p>
    </div>
    <?php endif; ?>

    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
        <form action="<?= $actionUrl ?>" method="post" class="space-y-6">
            <?= csrf_field() ?>

            <!-- Nama -->
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Lengkap</label>
                <input type="text" name="name" value="<?= old('name', $user['name'] ?? '') ?>" required 
                    class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary">
            </div>

            <!-- Email & Phone -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Email</label>
                    <input type="email" name="email" value="<?= old('email', $user['email'] ?? '') ?>" required 
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">No. Handphone / WhatsApp</label>
                    <input type="text" name="phone" value="<?= old('phone', $user['phone'] ?? '') ?>" 
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary">
                </div>
            </div>

            <!-- Role & Telegram -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Role Akses</label>
                    <select name="role" required class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary">
                        <?php $currentRole = old('role', $user['role'] ?? 'user'); ?>
                        <option value="user" <?= $currentRole == 'user' ? 'selected' : '' ?>>User Biasa</option>
                        <option value="pengurus" <?= $currentRole == 'pengurus' ? 'selected' : '' ?>>Pengurus Masjid</option>
                        <option value="superadmin" <?= $currentRole == 'superadmin' ? 'selected' : '' ?>>Superadmin Platform</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Telegram Chat ID (Notifikasi)</label>
                    <input type="text" name="telegram_chat_id" value="<?= old('telegram_chat_id', $user['telegram_chat_id'] ?? '') ?>" 
                        class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary" placeholder="Misal: 123456789">
                </div>
            </div>

            <!-- Password -->
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                    Password <?= $isEdit ? '<span class="text-xs font-normal text-slate-400">(Kosongkan jika tidak ingin mengubah password)</span>' : '*' ?>
                </label>
                <input type="password" name="password" <?= !$isEdit ? 'required' : '' ?> minlength="6"
                    class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary">
            </div>

            <!-- Buttons -->
            <div class="pt-4 flex items-center justify-end gap-3">
                <a href="<?= base_url('superadmin/users') ?>" class="px-6 py-3 rounded-xl font-bold text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">Batal</a>
                <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-primary/30 transition-all">
                    Simpan User
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
