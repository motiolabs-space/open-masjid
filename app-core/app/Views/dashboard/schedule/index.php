<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<div class="px-8 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="mb-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-black text-[#111816] dark:text-white tracking-tight">Jadwal Peribadatan</h1>
                <p class="text-[#608a7e]">Kelola petugas sholat harian, jum'at, dan hari raya.</p>
            </div>
            
            <div class="flex items-center gap-3 w-full md:w-auto">
                <form action="" method="GET" class="flex items-center gap-2 bg-white dark:bg-white/5 p-1.5 rounded-xl border border-[#dbe6e3] dark:border-white/10 shadow-sm">
                    <select name="month" class="bg-transparent border-none text-sm font-bold text-[#111816] dark:text-white focus:ring-0 cursor-pointer" onchange="this.form.submit()">
                        <?php for($m=1; $m<=12; $m++): ?>
                            <option value="<?= sprintf('%02d', $m) ?>" <?= $month == sprintf('%02d', $m) ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 10)) ?></option>
                        <?php endfor; ?>
                    </select>
                    <div class="w-px h-4 bg-gray-200 dark:bg-white/10"></div>
                    <select name="year" class="bg-transparent border-none text-sm font-bold text-[#111816] dark:text-white focus:ring-0 cursor-pointer" onchange="this.form.submit()">
                        <?php for($y=date('Y')-1; $y<=date('Y')+1; $y++): ?>
                            <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </form>

                <a href="<?= base_url('dashboard/schedules/new') ?>" class="bg-primary text-white px-5 py-3 rounded-xl font-bold hover:bg-emerald-900 transition-all shadow-lg shadow-primary/20 flex items-center gap-2">
                    <span class="material-symbols-outlined text-xl">add</span>
                    <span>Tambah Jadwal</span>
                </a>
            </div>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="bg-emerald-50 text-emerald-800 px-4 py-3 rounded-xl mb-6 flex items-center gap-2 font-bold border border-emerald-100">
                <span class="material-symbols-outlined">check_circle</span>
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (empty($schedules)): ?>
            <div class="bg-white dark:bg-white/5 rounded-3xl p-12 text-center border dashed border-2 border-gray-200 dark:border-white/10">
                <div class="size-20 bg-gray-50 dark:bg-white/5 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-300">
                    <span class="material-symbols-outlined text-4xl">calendar_month</span>
                </div>
                <h3 class="font-bold text-lg text-[#111816] dark:text-white mb-2">Belum ada jadwal</h3>
                <p class="text-[#608a7e] mb-6">Silakan tambahkan jadwal untuk bulan <?= date('F Y', mktime(0,0,0, $month, 1, $year)) ?>.</p>
                <a href="<?= base_url('dashboard/schedules/new') ?>" class="text-primary font-bold hover:underline">Tambah Jadwal Baru</a>
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <!-- Group by Date -->
                <?php 
                // Sort keys (dates)
                ksort($schedules);
                foreach ($schedules as $date => $items): 
                    $timestamp = strtotime($date);
                    $dayName = date('l', $timestamp);
                    $isToday = $date === date('Y-m-d');
                ?>
                    <div class="bg-white dark:bg-white/5 rounded-3xl border border-[#e5e7eb] dark:border-white/10 overflow-hidden <?= $isToday ? 'ring-2 ring-primary ring-offset-2 dark:ring-offset-gray-900' : '' ?>">
                        <div class="bg-[#f0f5f3] dark:bg-white/5 px-6 py-4 flex items-center justify-between border-b border-[#e5e7eb] dark:border-white/10">
                            <div class="flex items-center gap-3">
                                <div class="bg-white dark:bg-white/10 px-3 py-1 rounded-lg border border-gray-200 dark:border-white/5 font-black text-lg text-[#111816] dark:text-white">
                                    <?= date('d', $timestamp) ?>
                                </div>
                                <div>
                                    <h3 class="font-bold text-[#111816] dark:text-white"><?= $dayName ?></h3>
                                    <p class="text-xs text-[#608a7e]"><?= date('F Y', $timestamp) ?></p>
                                </div>
                            </div>
                            <?php if ($dayName === 'Friday'): ?>
                                <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-3 py-1 rounded-full border border-emerald-200">Jumat Berkah</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="divide-y divide-gray-100 dark:divide-white/5">
                            <?php foreach ($items as $item): ?>
                                <div class="p-4 sm:flex items-center justify-between hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group">
                                    <div class="flex items-center gap-4 mb-4 sm:mb-0">
                                        <div class="size-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs uppercase">
                                            <?= substr($item['prayer_type'], 0, 3) ?>
                                        </div>
                                        <div>
                                            <p class="font-bold text-sm text-[#111816] dark:text-white capitalize"><?= str_replace('_', ' ', $item['prayer_type']) ?></p>
                                            <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1 text-xs text-gray-500">
                                                <?php if ($item['imam_name']): ?>
                                                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">person</span> Imam: <strong><?= esc($item['imam_name']) ?></strong></span>
                                                <?php endif; ?>
                                                <?php if ($item['khatib_name']): ?>
                                                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">record_voice_over</span> Khatib: <strong><?= esc($item['khatib_name']) ?></strong></span>
                                                <?php endif; ?>
                                                <?php if ($item['muadzin_name']): ?>
                                                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">campaign</span> Muadzin: <strong><?= esc($item['muadzin_name']) ?></strong></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="<?= base_url('dashboard/schedules/edit/' . $item['id']) ?>" class="size-8 bg-white dark:bg-white/10 border border-gray-200 dark:border-white/10 rounded-lg flex items-center justify-center text-gray-500 hover:text-primary hover:border-primary transition-colors">
                                            <span class="material-symbols-outlined text-lg">edit</span>
                                        </a>
                                        <a href="<?= base_url('dashboard/schedules/delete/' . $item['id']) ?>" onclick="return confirm('Hapus jadwal ini?')" class="size-8 bg-white dark:bg-white/10 border border-gray-200 dark:border-white/10 rounded-lg flex items-center justify-center text-gray-500 hover:text-red-500 hover:border-red-500 transition-colors">
                                            <span class="material-symbols-outlined text-lg">delete</span>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
