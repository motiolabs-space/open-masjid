<?= $this->extend('layout/superadmin') ?>

<?= $this->section('content') ?>

<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex flex-wrap justify-between items-center gap-4">
        <div>
            <h4 class="font-bold text-slate-800 dark:text-white">Manajemen Seluruh User</h4>
            <p class="text-xs text-slate-500 mt-1">Total: <?= count($users) ?> User terdaftar</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?= base_url('superadmin/users/create') ?>" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg text-sm font-bold shadow-lg shadow-primary/30 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">add</span>
                Tambah User
            </a>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">search</span>
                <input type="text" placeholder="Cari nama atau email..." class="pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border-none rounded-lg text-sm focus:ring-2 focus:ring-primary w-64">
            </div>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 font-bold uppercase text-[10px]">
                <tr>
                    <th class="px-6 py-4 tracking-wider">Nama Lengkap</th>
                    <th class="px-6 py-4 tracking-wider">Email & Phone</th>
                    <th class="px-6 py-4 tracking-wider">Role</th>
                    <th class="px-6 py-4 tracking-wider">Bergabung</th>
                    <th class="px-6 py-4 tracking-wider">Last Login</th>
                    <th class="px-6 py-4 text-right tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                <?php foreach($users as $u): ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                    <td class="px-6 py-4 font-bold text-slate-900 dark:text-white"><?= esc($u['name']) ?></td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <a href="mailto:<?= esc($u['email']) ?>" class="font-medium text-blue-600 hover:underline dark:text-blue-400"><?= esc($u['email']) ?></a>
                            <?php $cleanPhone = format_wa($u['phone'] ?? ''); ?>
                            <?php if($cleanPhone): ?>
                            <a href="https://wa.me/<?= $cleanPhone ?>" target="_blank" class="text-[10px] text-emerald-600 hover:underline mt-0.5"><?= esc($u['phone']) ?></a>
                            <?php else: ?>
                            <span class="text-[10px] text-slate-400">-</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <?php 
                        $roleClass = 'bg-slate-100 text-slate-600';
                        if($u['role'] == 'superadmin') $roleClass = 'bg-emerald-100 text-emerald-700 font-black';
                        if($u['role'] == 'pengurus') $roleClass = 'bg-blue-100 text-blue-700 font-bold';
                        ?>
                        <span class="px-2 py-1 rounded text-[10px] uppercase font-bold <?= $roleClass ?>">
                            <?= esc($u['role']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-slate-500 font-medium"><?= date('d M Y, H:i', strtotime($u['created_at'])) ?></td>
                    <td class="px-6 py-4 text-slate-500 font-medium">
                        <?= !empty($u['last_login']) ? date('d M Y, H:i', strtotime($u['last_login'])) : '<span class="text-slate-300">Belum pernah</span>' ?>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                        <a href="<?= base_url('superadmin/users/analytics/' . $u['id']) ?>" class="text-indigo-600 hover:text-indigo-700 font-bold text-xs">Analytics</a>
                        <a href="<?= base_url('superadmin/users/edit/' . $u['id']) ?>" class="text-primary hover:text-primary-dark font-bold text-xs">Edit</a>
                        <?php if($u['id'] != session()->get('user_id')): ?>
                        <form action="<?= base_url('superadmin/users/delete/' . $u['id']) ?>" method="post" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus user ini?');">
                            <?= csrf_field() ?>
                            <button type="submit" class="text-rose-600 hover:text-rose-700 font-bold text-xs">Hapus</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
