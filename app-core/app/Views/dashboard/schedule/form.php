<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<div class="px-8 py-8">
    <div class="max-w-3xl mx-auto">
        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-rose-50 text-rose-600 p-4 rounded-xl mb-6 flex items-center gap-3">
                <span class="material-symbols-outlined">error</span>
                <p class="text-sm font-medium"><?= esc(session()->getFlashdata('error')) ?></p>
            </div>
        <?php endif; ?>
        <div class="mb-8 flex items-center gap-4">
            <a href="<?= base_url('dashboard/schedules') ?>" class="size-10 bg-white dark:bg-white/5 border border-[#dbe6e3] dark:border-white/10 rounded-xl flex items-center justify-center text-[#608a7e] hover:text-primary transition-all">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div>
                <h1 class="text-3xl font-black text-[#111816] dark:text-white tracking-tight"><?= isset($data['id']) ? 'Edit Jadwal' : 'Tambah Jadwal' ?></h1>
                <p class="text-[#608a7e]">Atur petugas sholat untuk tanggal tertentu.</p>
            </div>
        </div>

        <form action="<?= base_url('dashboard/schedules/save') ?>" method="POST" class="bg-white dark:bg-white/5 rounded-3xl border border-[#e5e7eb] dark:border-white/10 p-8 space-y-6">
            <?= csrf_field() ?>
            <?php if (isset($data['id'])): ?>
                <input type="hidden" name="id" value="<?= $data['id'] ?>">
            <?php endif; ?>

            <div class="grid md:grid-cols-2 gap-6">
                <!-- Date -->
                <div>
                    <label class="block text-sm font-bold mb-2 text-[#111816] dark:text-white">Tanggal</label>
                    <input type="date" name="date" required value="<?= esc($data['date'] ?? date('Y-m-d')) ?>" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl focus:ring-2 focus:ring-primary p-4 font-bold text-[#111816] dark:text-white">
                </div>

                <!-- Prayer Type -->
                <div>
                    <label class="block text-sm font-bold mb-2 text-[#111816] dark:text-white">Waktu Sholat</label>
                    <div class="relative">
                        <select name="prayer_type" required class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl focus:ring-2 focus:ring-primary p-4 font-bold text-[#111816] dark:text-white appearance-none cursor-pointer">
                            <?php 
                            $types = [
                                'subuh' => 'Subuh', 
                                'dzuhur' => 'Dzuhur', 
                                'ashar' => 'Ashar', 
                                'maghrib' => 'Maghrib', 
                                'isya' => 'Isya', 
                                'jumat' => 'Jumat',
                                'tarawih' => 'Tarawih',
                                'eid_fitr' => 'Idul Fitri',
                                'eid_adha' => 'Idul Adha'
                            ];
                            foreach ($types as $key => $label): 
                            ?>
                                <option value="<?= $key ?>" <?= ($data['prayer_type'] ?? '') === $key ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">expand_more</span>
                    </div>
                </div>
            </div>

            <div class="h-px bg-gray-100 dark:bg-white/5"></div>

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold mb-2 text-[#111816] dark:text-white">Nama Imam</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">person</span>
                        <input type="text" name="imam_name" value="<?= esc($data['imam_name'] ?? '') ?>" placeholder="Ustadz..." class="w-full pl-12 bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl focus:ring-2 focus:ring-primary p-4 font-bold text-[#111816] dark:text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2 text-[#111816] dark:text-white">Nama Khatib (Khusus Jumat/Raya)</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">record_voice_over</span>
                        <input type="text" name="khatib_name" value="<?= esc($data['khatib_name'] ?? '') ?>" placeholder="Ustadz..." class="w-full pl-12 bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl focus:ring-2 focus:ring-primary p-4 font-bold text-[#111816] dark:text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2 text-[#111816] dark:text-white">Nama Muadzin</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">campaign</span>
                        <input type="text" name="muadzin_name" value="<?= esc($data['muadzin_name'] ?? '') ?>" placeholder="Bapak..." class="w-full pl-12 bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl focus:ring-2 focus:ring-primary p-4 font-bold text-[#111816] dark:text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2 text-[#111816] dark:text-white">Nama Bilal (Opsional)</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">mic</span>
                        <input type="text" name="bilal_name" value="<?= esc($data['bilal_name'] ?? '') ?>" placeholder="Bapak..." class="w-full pl-12 bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl focus:ring-2 focus:ring-primary p-4 font-bold text-[#111816] dark:text-white">
                    </div>
                </div>
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full bg-primary text-white py-4 rounded-2xl font-black shadow-lg shadow-primary/20 hover:bg-emerald-900 transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">save</span>
                    Simpan Jadwal
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
