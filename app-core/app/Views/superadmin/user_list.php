<?= $this->extend('layout/superadmin') ?>

<?= $this->section('content') ?>

<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex flex-wrap justify-between items-center gap-4">
        <div>
            <h4 class="font-bold text-slate-800 dark:text-white">Manajemen Seluruh User</h4>
            <p class="text-xs text-slate-500 mt-1">Total: <?= count($users) ?> User terdaftar</p>
        </div>
        <div class="flex items-center gap-2">
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
                    <th class="px-6 py-4 text-right tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                <?php foreach($users as $u): ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                    <td class="px-6 py-4 font-bold text-slate-900 dark:text-white"><?= esc($u['name']) ?></td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-medium text-slate-900 dark:text-white"><?= esc($u['email']) ?></span>
                            <span class="text-[10px] text-slate-400"><?= esc($u['phone'] ?? '-') ?></span>
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
                    <td class="px-6 py-4 text-right space-x-2">
                        <button class="text-primary hover:text-primary-dark font-bold text-xs">Edit</button>
                        <?php if($u['role'] != 'superadmin'): ?>
                        <button class="text-emerald-600 hover:text-emerald-700 font-bold text-xs">Promote</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
