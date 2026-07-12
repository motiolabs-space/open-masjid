<?= $this->extend('layout/superadmin') ?>

<?= $this->section('content') ?>
<div class="max-w-3xl">
    <div class="mb-8">
        <h2 class="text-2xl font-bold mb-2 text-slate-800 dark:text-slate-100">Pengaturan Platform</h2>
        <p class="text-slate-500">Kelola pengaturan global untuk platform Open Masjid.</p>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="bg-emerald-50 dark:bg-emerald-500/10 border-l-4 border-emerald-500 p-4 mb-6 rounded-r-lg">
            <p class="text-emerald-700 dark:text-emerald-400 font-medium"><?= session()->getFlashdata('success') ?></p>
        </div>
    <?php endif; ?>

    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
        <form action="<?= base_url('superadmin/settings/save') ?>" method="post">
            <?= csrf_field() ?>
            
            <h3 class="text-lg font-bold mb-4 text-slate-800 dark:text-slate-100 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">forum</span>
                Komunitas & Diskusi
            </h3>
            
            <p class="text-sm text-slate-500 mb-6">
                Tautan grup ini akan ditampilkan di *dashboard* pengurus masjid dan jamaah untuk bergabung dalam diskusi pengembangan atau bantuan platform.
            </p>

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Tautan Grup WhatsApp</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <span class="material-symbols-outlined">link</span>
                        </div>
                        <input type="url" name="community_wa_link" class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 focus:ring-2 focus:ring-primary focus:border-primary transition-all text-slate-900 dark:text-slate-100" placeholder="https://chat.whatsapp.com/..." value="<?= esc($settings['community_wa_link'] ?? '') ?>">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Tautan Grup Telegram</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <span class="material-symbols-outlined">send</span>
                        </div>
                        <input type="url" name="community_tg_link" class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 focus:ring-2 focus:ring-primary focus:border-primary transition-all text-slate-900 dark:text-slate-100" placeholder="https://t.me/..." value="<?= esc($settings['community_tg_link'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <h3 class="text-lg font-bold mt-10 mb-4 text-slate-800 dark:text-slate-100 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">stars</span>
                North Star Metrics (Target Tahunan)
            </h3>
            
            <p class="text-sm text-slate-500 mb-6">
                Tentukan target yang ingin dicapai pada tahun ini. Progres dari target ini akan muncul di Dashboard Superadmin.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Target Jumlah Masjid</label>
                    <input type="number" name="target_masjid" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 focus:ring-2 focus:ring-primary focus:border-primary transition-all text-slate-900 dark:text-slate-100" placeholder="0" value="<?= esc($settings['target_masjid'] ?? '') ?>">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Target Jumlah Program</label>
                    <input type="number" name="target_program" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 focus:ring-2 focus:ring-primary focus:border-primary transition-all text-slate-900 dark:text-slate-100" placeholder="0" value="<?= esc($settings['target_program'] ?? '') ?>">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Target Jamaah Terjangkau</label>
                    <input type="number" name="target_jamaah" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 focus:ring-2 focus:ring-primary focus:border-primary transition-all text-slate-900 dark:text-slate-100" placeholder="0" value="<?= esc($settings['target_jamaah'] ?? '') ?>">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Target Mustahik Terjangkau</label>
                    <input type="number" name="target_mustahik" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 focus:ring-2 focus:ring-primary focus:border-primary transition-all text-slate-900 dark:text-slate-100" placeholder="0" value="<?= esc($settings['target_mustahik'] ?? '') ?>">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Target Total Dana Tersalurkan (Rp)</label>
                    <input type="number" name="target_donasi" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 focus:ring-2 focus:ring-primary focus:border-primary transition-all text-slate-900 dark:text-slate-100" placeholder="0" value="<?= esc($settings['target_donasi'] ?? '') ?>">
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-800">
                <button type="submit" class="bg-primary hover:bg-primary-dark text-white font-medium py-2.5 px-6 rounded-lg transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">save</span>
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
