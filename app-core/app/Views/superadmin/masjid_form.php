<?= $this->extend('layout/superadmin') ?>

<?= $this->section('content') ?>

<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
        <a href="<?= base_url('superadmin/masjid') ?>" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-primary hover:text-white transition-colors">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
        </a>
        <div>
            <h4 class="font-bold text-slate-800 dark:text-white">Tambah Masjid Baru</h4>
            <p class="text-xs text-slate-500 mt-1">Tambahkan data masjid secara manual ke dalam sistem.</p>
        </div>
    </div>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="bg-rose-100 text-rose-700 p-4 rounded-xl text-sm mb-6 font-medium border border-rose-200">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <form action="<?= base_url('superadmin/masjid/save') ?>" method="post" class="p-6 space-y-6">
            <?= csrf_field() ?>
            
            <div class="space-y-4">
                <h5 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider border-b border-slate-200 dark:border-slate-800 pb-2">Informasi Dasar</h5>
                
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Nama Masjid <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="<?= old('name') ?>" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Username (Untuk URL Portal) <span class="text-rose-500">*</span></label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-800 text-slate-500 text-sm">
                            masj.id/
                        </span>
                        <input type="text" name="username" value="<?= old('username') ?>" required class="flex-1 min-w-0 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-none rounded-r-lg px-4 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-primary" placeholder="contoh: masjid-istiqlal">
                    </div>
                    <p class="text-[10px] text-slate-500 mt-1">Hanya boleh huruf, angka, dan strip (-). Tanpa spasi.</p>
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Alamat Lengkap</label>
                    <textarea name="address" rows="3" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-primary"><?= old('address') ?></textarea>
                </div>
            </div>

            <div class="space-y-4 pt-4">
                <h5 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider border-b border-slate-200 dark:border-slate-800 pb-2">Kontak Publik</h5>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">No. Telepon / HP</label>
                        <input type="text" name="phone" value="<?= old('phone') ?>" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">No. WhatsApp</label>
                        <input type="text" name="whatsapp" value="<?= old('whatsapp') ?>" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Email Masjid</label>
                    <input type="email" name="email" value="<?= old('email') ?>" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                </div>
            </div>
            
            <div class="pt-6 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-3">
                <a href="<?= base_url('superadmin/masjid') ?>" class="px-5 py-2.5 rounded-xl font-bold text-sm text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">Batal</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-sm text-white bg-primary hover:bg-primary-dark transition-colors">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
