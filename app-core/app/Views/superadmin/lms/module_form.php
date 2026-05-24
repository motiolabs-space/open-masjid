<?= $this->extend('layout/superadmin') ?>

<?= $this->section('content') ?>
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white"><?= $title ?></h2>
    </div>
    <a href="<?= base_url('superadmin/lms') ?>" class="text-slate-500 hover:text-slate-800 font-medium text-sm flex items-center gap-1">
        <span class="material-symbols-outlined text-sm">arrow_back</span> Kembali
    </a>
</div>

<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
    <form action="<?= base_url('superadmin/lms/save') ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <?php if ($module): ?>
            <input type="hidden" name="id" value="<?= $module['id'] ?>">
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Judul Modul</label>
                    <input type="text" name="title" value="<?= esc($module['title'] ?? '') ?>" required class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:outline-none">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Lembaga Pemateri</label>
                    <select name="lembaga_pemateri" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:outline-none">
                        <option value="">-- Pilih Masjid / Lembaga --</option>
                        <?php foreach($masjids as $masjid): ?>
                            <option value="<?= esc($masjid['id']) ?>" <?= ($module['lembaga_pemateri'] ?? '') == $masjid['id'] ? 'selected' : '' ?>>
                                <?= esc($masjid['name']) ?> (@<?= esc($masjid['username']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:outline-none">
                        <option value="draft" <?= ($module['status'] ?? '') == 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="published" <?= ($module['status'] ?? '') == 'published' ? 'selected' : '' ?>>Published</option>
                    </select>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Thumbnail (Opsional)</label>
                    <input type="file" name="thumbnail" accept="image/*" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:outline-none">
                    <?php if (!empty($module['thumbnail'])): ?>
                        <div class="mt-2">
                            <img src="<?= asset_url('uploads/lms/' . $module['thumbnail']) ?>" class="h-24 rounded-lg object-cover">
                        </div>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Deskripsi Modul</label>
                    <textarea name="description" rows="4" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:outline-none"><?= esc($module['description'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <div class="mt-8 border-t border-slate-200 dark:border-slate-800 pt-6">
            <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-6 py-2.5 rounded-lg font-medium text-sm transition-colors">
                Simpan Modul
            </button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
