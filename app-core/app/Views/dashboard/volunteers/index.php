<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-black text-[#111816]">Relawan & Piket Gerakan</h1>
            <p class="text-[#608a7e] mt-1">Koordinasikan jamaah yang siap berkontribusi dalam gerakan masjid.</p>
        </div>
        <a href="<?= base_url('dashboard/warga') ?>" class="btn-primary flex items-center gap-2">
            <span class="material-symbols-outlined">person_search</span>
            Cari Relawan di Basis Komunitas
        </a>
    </div>

    <!-- Alert: How it works -->
    <div class="bg-blue-50 border border-blue-100 rounded-2xl p-6 mb-8 flex gap-4 items-start">
        <span class="material-symbols-outlined text-blue-600 mt-1">info</span>
        <div>
            <h4 class="font-bold text-blue-900">Cara Menambahkan Relawan</h4>
            <p class="text-sm text-blue-700 mt-1">
                Masuk ke menu <b>Basis Komunitas</b>, edit data warga, lalu tambahkan tag <code>#relawan</code> pada kolom Catatan. Mereka akan otomatis muncul di halaman koordinasi ini.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Volunteers List -->
        <div class="lg:col-span-2 space-y-6">
            <h3 class="font-black text-xl flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">groups</span>
                Daftar Relawan Aktif
            </h3>
            
            <div class="bg-white rounded-[2rem] shadow-sm border border-[#dbe6e1] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-[#f0f5f3] border-b border-[#dbe6e1]">
                            <tr>
                                <th class="p-6 font-bold text-[#111816]">Nama Relawan</th>
                                <th class="p-6 font-bold text-[#111816]">Keahlian / Spesialisasi</th>
                                <th class="p-6 font-bold text-[#111816]">Kontak</th>
                                <th class="p-6 font-bold text-[#111816] text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#dbe6e1]">
                            <?php if (empty($volunteers)): ?>
                                <tr>
                                    <td colspan="4" class="p-12 text-center text-[#608a7e]">
                                        <span class="material-symbols-outlined text-6xl mb-4 opacity-20">person_off</span>
                                        <p>Belum ada warga yang ditandai sebagai relawan (#relawan).</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($volunteers as $v): ?>
                                    <tr class="hover:bg-[#f8faf9] transition-colors group">
                                        <td class="p-6">
                                            <div class="font-bold text-[#111816]"><?= esc($v['name']) ?></div>
                                            <div class="text-[10px] text-primary font-black uppercase tracking-widest mt-1">Ready to Serve</div>
                                        </td>
                                        <td class="p-6">
                                            <p class="text-sm text-[#608a7e]">
                                                <?= esc(str_replace(['#relawan', '#volunteer'], '', $v['notes']) ?: 'Umum') ?>
                                            </p>
                                        </td>
                                        <td class="p-6">
                                            <div class="text-sm font-medium"><?= esc($v['phone'] ?: '-') ?></div>
                                        </td>
                                        <td class="p-6 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <?php if ($v['phone']): 
                                                    $waPhone = preg_replace('/[^0-9]/', '', $v['phone']);
                                                    if (strpos($waPhone, '0') === 0) $waPhone = '62' . substr($waPhone, 1);
                                                    elseif (strpos($waPhone, '62') !== 0) $waPhone = '62' . $waPhone;
                                                ?>
                                                    <a href="https://wa.me/<?= $waPhone ?>?text=Assalamu'alaikum%20<?= urlencode($v['name']) ?>,%20kami%20dari%20pengurus%20masjid%20ingin%20berkoordinasi..." target="_blank" class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-xs font-bold hover:bg-emerald-700 transition-colors flex items-center gap-2">
                                                        <span class="material-symbols-outlined text-sm">chat</span>
                                                        Panggil
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Piket / Schedule Section (Coming Soon Placeholder) -->
        <div class="space-y-6">
            <h3 class="font-black text-xl flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">event_note</span>
                Jadwal Piket
            </h3>
            
            <div class="bg-white rounded-[2rem] p-8 border border-[#dbe6e1] shadow-sm relative overflow-hidden">
                <div class="relative z-10">
                    <h4 class="font-bold text-[#111816] mb-2">Piket Relawan</h4>
                    <p class="text-sm text-[#608a7e] mb-6">Fitur penjadwalan piket kebersihan, keamanan, dan distribusi bantuan sedang disiapkan.</p>
                    
                    <div class="space-y-4 opacity-50">
                        <div class="flex items-center gap-3 p-3 bg-[#f0f5f3] rounded-xl">
                            <div class="size-10 bg-primary/20 text-primary rounded-lg flex items-center justify-center font-bold">07</div>
                            <div>
                                <p class="text-xs font-bold">Piket Kebersihan</p>
                                <p class="text-[10px]">Ahad, 07:00 WIB</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-[#f0f5f3] rounded-xl">
                            <div class="size-10 bg-primary/20 text-primary rounded-lg flex items-center justify-center font-bold">12</div>
                            <div>
                                <p class="text-xs font-bold">Penyaluran Beras</p>
                                <p class="text-[10px]">Jumat, 13:30 WIB</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="absolute inset-0 bg-white/60 backdrop-blur-[2px] flex items-center justify-center p-8 text-center">
                    <div>
                        <span class="material-symbols-outlined text-4xl text-primary mb-2">construction</span>
                        <p class="font-bold text-[#111816]">Coming Soon</p>
                        <p class="text-[10px] text-[#608a7e] uppercase tracking-widest mt-1">Modul Koordinasi Piket</p>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="bg-primary rounded-[2rem] p-8 text-white">
                <h4 class="text-sm font-bold opacity-80 uppercase tracking-widest mb-4">Gerakan Bersama</h4>
                <div class="text-4xl font-black mb-1"><?= count($volunteers) ?></div>
                <p class="text-sm opacity-90">Relawan siap beraksi membantu sesama.</p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
