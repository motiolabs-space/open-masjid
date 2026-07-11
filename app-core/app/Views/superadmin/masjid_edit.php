<?= $this->extend('layout/superadmin') ?>

<?= $this->section('content') ?>

<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden max-w-4xl mx-auto">
    <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center">
        <div>
            <h4 class="font-bold text-slate-800 dark:text-white">Edit & Suspend Masjid</h4>
            <p class="text-xs text-slate-500 mt-1">Kelola data dasar masjid, atur status, dan tetapkan admin utama.</p>
        </div>
        <a href="<?= base_url('superadmin/masjid') ?>" class="bg-slate-100 text-slate-700 hover:bg-slate-200 px-4 py-2 rounded-lg text-sm font-bold transition-all">Kembali</a>
    </div>
    
    <div class="p-6">
        <?php if(session()->getFlashdata('error')): ?>
            <div class="bg-rose-100 text-rose-600 p-4 rounded-lg mb-6 text-sm font-bold">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('superadmin/masjid/update/' . $masjid['id']) ?>" method="post" class="space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Data Dasar -->
                <div class="space-y-4">
                    <h5 class="font-bold text-slate-700 dark:text-slate-300 border-b border-slate-100 pb-2">Data Dasar Masjid</h5>
                    
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Masjid <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="<?= esc($masjid['name']) ?>" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Username (URL) <span class="text-rose-500">*</span></label>
                        <input type="text" name="username" value="<?= esc($masjid['username']) ?>" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor Telepon</label>
                        <input type="text" name="phone" value="<?= esc($masjid['phone']) ?>" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">WhatsApp</label>
                        <input type="text" name="whatsapp" value="<?= esc($masjid['whatsapp']) ?>" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Email</label>
                        <input type="email" name="email" value="<?= esc($masjid['email']) ?>" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary">
                    </div>
                </div>

                <!-- Keamanan & Hak Akses -->
                <div class="space-y-4">
                    <h5 class="font-bold text-slate-700 dark:text-slate-300 border-b border-slate-100 pb-2">Kontrol Sistem</h5>
                    
                    <div class="bg-amber-50 dark:bg-amber-900/10 p-4 rounded-xl border border-amber-100 dark:border-amber-900/30">
                        <label class="block text-sm font-bold text-amber-900 dark:text-amber-500 mb-2">Status Akun Masjid</label>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="status" value="active" <?= (!isset($masjid['status']) || $masjid['status'] == 'active') ? 'checked' : '' ?> class="text-primary focus:ring-primary h-4 w-4">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Active <span class="text-xs text-slate-400 font-normal ml-1">(Bisa diakses publik & admin)</span></span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="status" value="suspended" <?= (isset($masjid['status']) && $masjid['status'] == 'suspended') ? 'checked' : '' ?> class="text-rose-500 focus:ring-rose-500 h-4 w-4">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Suspended <span class="text-xs text-slate-400 font-normal ml-1">(Ditangguhkan sementara)</span></span>
                            </label>
                        </div>
                    </div>

                    <div class="bg-blue-50 dark:bg-blue-900/10 p-4 rounded-xl border border-blue-100 dark:border-blue-900/30 mt-4">
                        <label class="block text-sm font-bold text-blue-900 dark:text-blue-400 mb-1">Tetapkan Admin Utama (Owner)</label>
                        <p class="text-[10px] text-blue-700/70 mb-3">Pilih user yang akan diberikan kendali penuh atas masjid ini.</p>
                        
                        <select name="admin_id" class="w-full px-4 py-2 bg-white border border-blue-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih User --</option>
                            <?php foreach($users as $user): ?>
                                <option value="<?= $user['id'] ?>" <?= ($currentAdminId == $user['id']) ? 'selected' : '' ?>>
                                    <?= esc($user['name']) ?> (<?= esc($user['email'] ?? $user['phone']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>
            </div>

            <div class="pt-6 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-3">
                <a href="<?= base_url('superadmin/masjid') ?>" class="px-6 py-2.5 rounded-lg text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">Batal</a>
                <button type="submit" class="px-6 py-2.5 rounded-lg text-sm font-bold text-white bg-primary hover:bg-primary/90 transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
