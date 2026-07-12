<?= $this->extend('layout/superadmin') ?>

<?= $this->section('content') ?>

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-black text-slate-800 dark:text-white">User Analytics</h1>
        <p class="text-slate-500 mt-1">Detail aktivitas dan profil pengguna.</p>
    </div>
    <a href="<?= base_url('superadmin/users') ?>" class="text-slate-400 hover:text-slate-600 transition-colors">
        <span class="material-symbols-outlined">arrow_back</span>
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- User Profile Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 lg:col-span-1">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center text-primary text-2xl font-black uppercase">
                <?= substr($user['name'], 0, 1) ?>
            </div>
            <div>
                <h3 class="font-bold text-slate-800 dark:text-white text-lg"><?= esc($user['name']) ?></h3>
                <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-slate-100 text-slate-600">
                    <?= esc($user['role']) ?>
                </span>
            </div>
        </div>

        <div class="space-y-4">
            <div>
                <p class="text-xs text-slate-500 mb-1">Email</p>
                <a href="mailto:<?= esc($user['email']) ?>" class="font-medium text-blue-600 hover:underline flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">mail</span>
                    <?= esc($user['email']) ?>
                </a>
            </div>
            <div>
                <p class="text-xs text-slate-500 mb-1">WhatsApp / Phone</p>
                <?php 
                $cleanPhone = format_wa($user['phone'] ?? ''); 
                if($cleanPhone): 
                ?>
                <a href="https://wa.me/<?= $cleanPhone ?>" target="_blank" class="font-medium text-emerald-600 hover:underline flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">call</span>
                    <?= esc($user['phone']) ?>
                </a>
                <?php else: ?>
                <span class="text-slate-400 font-medium">-</span>
                <?php endif; ?>
            </div>
            <div>
                <p class="text-xs text-slate-500 mb-1">Telegram Chat ID</p>
                <span class="font-medium text-slate-800 dark:text-slate-200">
                    <?= esc($user['telegram_chat_id'] ?: '-') ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Registration & Engagement Stats -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 lg:col-span-2">
        <h4 class="font-bold text-slate-800 dark:text-white mb-4 border-b border-slate-100 dark:border-slate-800 pb-3">Security & Registration</h4>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <p class="text-xs text-slate-500 mb-1">IP Address Saat Daftar</p>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-slate-400">router</span>
                    <span class="font-medium text-slate-800 dark:text-slate-200"><?= esc($user['register_ip'] ?: 'Tidak terekam') ?></span>
                </div>
            </div>
            <div>
                <p class="text-xs text-slate-500 mb-1">Lokasi Negara (Saat Daftar)</p>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-slate-400">public</span>
                    <span class="font-medium text-slate-800 dark:text-slate-200"><?= esc($user['register_country'] ?: 'Tidak terekam') ?></span>
                </div>
            </div>
            <div>
                <p class="text-xs text-slate-500 mb-1">Bergabung Sejak</p>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-slate-400">calendar_month</span>
                    <span class="font-medium text-slate-800 dark:text-slate-200"><?= date('d F Y, H:i', strtotime($user['created_at'])) ?></span>
                </div>
            </div>
            <div>
                <p class="text-xs text-slate-500 mb-1">Login Terakhir</p>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-slate-400">login</span>
                    <span class="font-medium text-slate-800 dark:text-slate-200">
                        <?= !empty($user['last_login']) ? date('d F Y, H:i', strtotime($user['last_login'])) : 'Belum pernah login' ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Masjid Affiliations Table -->
<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-slate-200 dark:border-slate-800">
        <h4 class="font-bold text-slate-800 dark:text-white">Afiliasi Kepengurusan Masjid</h4>
        <p class="text-xs text-slate-500 mt-1">Daftar masjid di mana pengguna ini terdaftar sebagai pengurus.</p>
    </div>
    <div class="overflow-x-auto">
        <?php if(empty($affiliations)): ?>
        <div class="p-8 text-center text-slate-500">
            <span class="material-symbols-outlined text-4xl text-slate-300 mb-3 block">domain_disabled</span>
            <p>Pengguna ini tidak mengurus masjid apapun.</p>
        </div>
        <?php else: ?>
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 font-bold uppercase text-[10px]">
                <tr>
                    <th class="px-6 py-4 tracking-wider">Nama Masjid</th>
                    <th class="px-6 py-4 tracking-wider">Peran (Role)</th>
                    <th class="px-6 py-4 tracking-wider">Status Akses</th>
                    <th class="px-6 py-4 tracking-wider">Terdaftar Sejak</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                <?php foreach($affiliations as $af): ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                    <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">
                        <?= esc($af['masjid_name']) ?>
                        <div class="text-[10px] text-slate-400 font-normal">@<?= esc($af['masjid_username']) ?></div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-medium"><?= esc($af['title'] ?: ucfirst($af['role'])) ?></span>
                        <?php if($af['is_creator']): ?>
                        <span class="ml-2 px-1.5 py-0.5 rounded text-[9px] uppercase font-bold bg-indigo-100 text-indigo-700">Creator</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded text-[10px] uppercase font-bold <?= $af['role'] == 'admin' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' ?>">
                            <?= esc($af['role']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-slate-500 font-medium"><?= date('d M Y', strtotime($af['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
