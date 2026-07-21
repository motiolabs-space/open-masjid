<header class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-4 sm:px-8 sticky top-0 z-20">
<div class="flex items-center gap-2">
    <?php // Tombol menu — hanya di mobile (lg:hidden); membuka drawer sidebar. ?>
    <button type="button" onclick="toggleSidebar(true)" class="lg:hidden p-2 -ml-2 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
        <span class="material-symbols-outlined">menu</span>
    </button>
    <span class="material-symbols-outlined text-primary">location_on</span>
    <span class="text-sm font-bold text-slate-700 dark:text-slate-200"><?= session()->get('masjid_name') ?? 'Masj.id' ?></span>
    <?php if (session()->get('role') === 'superadmin'): ?>
        <a href="<?= base_url('superadmin') ?>" class="ml-4 flex items-center gap-1.5 px-3 py-1.5 bg-rose-500 text-white rounded-lg text-[10px] font-bold hover:bg-rose-600 transition-all shadow-sm">
            <span class="material-symbols-outlined text-xs">admin_panel_settings</span>
            KEMBALI KE SUPER ADMIN
        </a>
    <?php endif; ?>
</div>
    <div class="flex-1"></div>
<div class="flex items-center gap-4">
<button class="p-2 rounded-full text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 relative">
<span class="material-symbols-outlined">notifications</span>
<span class="absolute top-2 right-2 size-2 bg-red-500 rounded-full border-2 border-white dark:border-slate-900"></span>
</button>
<div class="flex items-center gap-3 pl-4 border-l border-slate-200 dark:border-slate-800">
<div class="text-right hidden sm:block">
<p class="text-sm font-bold text-slate-800 dark:text-slate-100 leading-none"><?= session()->get('user_name') ?></p>
<p class="text-[10px] text-primary font-bold uppercase tracking-wider mt-1">
    <?php 
    if (session()->get('role') === 'superadmin') echo 'Super Admin';
    else if (session()->get('role') === 'pengurus') echo 'Pengurus Masjid';
    else echo 'Jamaah';
    ?>
</p>
</div>
<div class="size-10 rounded-full border-2 border-primary/20 overflow-hidden bg-slate-200">
<img alt="User Avatar" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAk8m_lBpTsskcCgaD_GcxeCzBlhzKcNMXQBJoMVysZcT-f-2Mf3rB4UyqEMWrSbooIj6V9E8Vme7d46RAg3g3Uww7LwpLxhv9GIOtAUR2GNL87frqgjaTb_ozMGcHDVPhTqC7hLTTcvxiUaiDp5FJ5S9OpWoJbJxxlGHrsGnC1ObuR0lAGMPmr_5-TT-isYkTNYm5bS8I4AnxmK9_hvC7sQaA7P7ry15V4e-GwfQ3CrIMQXSGNo7wAb7HbXZd6k47ao83UrBksN8zV"/>
</div>
</div>
</div>
</header>
