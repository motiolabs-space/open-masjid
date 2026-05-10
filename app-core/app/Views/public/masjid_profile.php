<?= $this->extend('layout/masjid_public') ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="relative h-[60vh] md:h-[70vh] flex items-end pb-12 overflow-hidden">
    <?php 
        $photoUrl = !empty($masjid['foto_utama']) ? $storage->url($masjid['foto_utama']) : 'https://images.unsplash.com/photo-1596701062351-8c2c14d1fdd0?q=80&w=1600&auto=format&fit=crop';
    ?>
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat transition-transform duration-[20s] hover:scale-110" style="background-image: url('<?= $photoUrl ?>');"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
    
    <div class="max-w-[1200px] mx-auto px-6 w-full relative z-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div class="flex-1">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/20 backdrop-blur-md border border-white/20 text-white text-[10px] font-bold uppercase tracking-widest mb-4">
                    <span class="size-2 bg-emerald-400 rounded-full animate-pulse"></span>
                    Profil Terverifikasi
                </div>
                
                <div class="flex items-center gap-4 mb-4">
                    <?php 
                        $logoUrl = !empty($masjid['logo']) ? $storage->url($masjid['logo']) : asset_url('public/logo_masjid_200.png');
                    ?>
                    <img src="<?= $logoUrl ?>" alt="Logo <?= esc($masjid['name']) ?>" class="size-16 md:size-20 rounded-full bg-white p-1 shadow-lg object-contain">
                    <div>
                        <h1 class="text-3xl md:text-5xl font-black text-white leading-tight drop-shadow-xl"><?= esc($masjid['name']) ?></h1>
                        <?php if (!empty($masjid['nama_resmi']) && $masjid['nama_resmi'] !== $masjid['name']): ?>
                            <p class="text-white/70 text-base md:text-lg font-medium italic"><?= esc($masjid['nama_resmi']) ?></p>
                        <?php else: ?>
                            <p class="text-white/70 text-base md:text-lg font-medium italic"><?= esc($masjid['address']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="flex flex-wrap gap-4 text-white/80 text-sm">
                    <div class="flex items-center gap-1.5 bg-white/10 backdrop-blur-sm px-3 py-1.5 rounded-lg border border-white/5">
                        <span class="material-symbols-outlined text-sm">location_on</span>
                        <?= esc($masjid['kabupaten']) ?>, <?= esc($masjid['provinsi']) ?>
                    </div>
                    <div class="flex items-center gap-1.5 bg-white/10 backdrop-blur-sm px-3 py-1.5 rounded-lg border border-white/5">
                        <span class="material-symbols-outlined text-sm">category</span>
                        <?= esc($masjid['jenis_masjid'] ?? 'Masjid Umum') ?>
                    </div>
                    <?php if (!empty($masjid['tahun_berdiri'])): ?>
                        <div class="flex items-center gap-1.5 bg-white/10 backdrop-blur-sm px-3 py-1.5 rounded-lg border border-white/5">
                            <span class="material-symbols-outlined text-sm">history</span>
                            Berdiri Thn <?= esc($masjid['tahun_berdiri']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="flex flex-col gap-3">
                <?php if ($masjid['action_button_active'] ?? 1): ?>
                <a href="<?= esc($masjid['action_button_url'] ?? '#donasi') ?>" class="btn-primary-lg flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">volunteer_activism</span>
                    <?= esc($masjid['action_button_text'] ?? 'Donasi Sekarang') ?>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Worship Schedule Section -->
<?php if (!empty($todaySchedules) || !empty($fridaySchedule)): ?>
<section class="py-12 bg-[#0C1512] text-white relative overflow-hidden">
    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/arabesque.png')] opacity-5"></div>
    <div class="max-w-[1200px] mx-auto px-6 relative z-10">
        <div class="flex flex-col md:flex-row gap-8 items-stretch">
            
            <!-- Today's Schedule -->
            <div class="flex-1 bg-white/5 border border-white/10 rounded-3xl p-8 backdrop-blur-sm">
                <div class="flex items-center gap-3 mb-6">
                    <div class="size-10 bg-emerald-500/20 rounded-xl flex items-center justify-center text-emerald-400">
                        <span class="material-symbols-outlined">calendar_today</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">Petugas Hari Ini</h3>
                        <p class="text-white/60 text-xs"><?= date('l, d M Y') ?></p>
                    </div>
                </div>

                <?php if (empty($todaySchedules)): ?>
                    <p class="text-white/40 italic text-sm">Belum ada jadwal petugas untuk hari ini.</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($todaySchedules as $sched): ?>
                            <div class="flex items-center justify-between border-b border-white/5 pb-3 last:border-0 last:pb-0">
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-black uppercase text-emerald-400 w-16"><?= $sched['prayer_type'] ?></span>
                                    <div>
                                        <?php if ($sched['imam_name']): ?>
                                            <div class="text-sm font-bold flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[10px] opacity-50">person</span>
                                                <?= esc($sched['imam_name']) ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($sched['muadzin_name']): ?>
                                            <div class="text-xs text-white/60 flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[10px] opacity-50">campaign</span>
                                                <?= esc($sched['muadzin_name']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Friday Schedule -->
            <?php if ($fridaySchedule): ?>
            <div class="flex-1 bg-gradient-to-br from-emerald-900 to-emerald-950 border border-emerald-800 rounded-3xl p-8 relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-32 bg-emerald-500/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="size-10 bg-white/10 rounded-xl flex items-center justify-center text-white">
                            <span class="material-symbols-outlined">mosque</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-emerald-100">Jumat Berkah</h3>
                            <p class="text-emerald-200/60 text-xs"><?= date('d M Y', strtotime($fridaySchedule['date'])) ?></p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <?php if ($fridaySchedule['khatib_name']): ?>
                        <div>
                            <p class="text-emerald-400 text-xs font-bold uppercase tracking-widest mb-1">Khatib</p>
                            <h4 class="text-2xl font-black text-white"><?= esc($fridaySchedule['khatib_name']) ?></h4>
                        </div>
                        <?php endif; ?>
                        <?php if ($fridaySchedule['imam_name']): ?>
                        <div>
                            <p class="text-emerald-400 text-xs font-bold uppercase tracking-widest mb-1">Imam</p>
                            <h4 class="text-xl font-bold text-white/90"><?= esc($fridaySchedule['imam_name']) ?></h4>
                        </div>
                        <?php endif; ?>
                        <?php if ($fridaySchedule['muadzin_name']): ?>
                        <div>
                            <p class="text-emerald-400 text-xs font-bold uppercase tracking-widest mb-1">Muadzin/Bilal</p>
                            <h4 class="text-lg font-medium text-white/80"><?= esc($fridaySchedule['muadzin_name']) ?> <?= $fridaySchedule['bilal_name'] ? ' / ' . esc($fridaySchedule['bilal_name']) : '' ?></h4>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</section>
<?php endif; ?>

<!-- Vision & Mission -->
<section id="tentang" class="py-24 px-6 bg-white dark:bg-background-dark">
    <div class="max-w-[1200px] mx-auto">
        <div class="grid lg:grid-cols-2 gap-16 items-start">
            <div class="space-y-8">
                <div>
                    <h2 class="text-sm font-bold text-primary uppercase tracking-[0.2em] mb-3">Tentang Kami</h2>
                    <h3 class="text-3xl md:text-4xl font-black leading-tight mb-6">
                        <?= !empty($masjid['tagline']) ? nl2br(esc($masjid['tagline'])) : 'Membangun Ummat, <br/>Memakmurkan Rumah Allah' ?>
                    </h3>
                    
                    <?php if (!empty($masjid['about_us'])): ?>
                        <p class="text-[#3d5a4d] dark:text-gray-400 text-lg leading-relaxed mb-4">
                            <?= nl2br(esc($masjid['about_us'])) ?>
                        </p>
                    <?php else: ?>
                        <p class="text-[#3d5a4d] dark:text-gray-400 text-lg leading-relaxed">
                            Selamat datang di portal informasi resmi <?= esc($masjid['name']) ?>. Melalui platform Masj.id, kami berupaya menghadirkan transparansi pengelolaan dan kemudahan bagi jamaah dalam berinteraksi dengan program-program edukasi, sosial, dan ibadah kami.
                        </p>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($service_areas)): ?>
                <div class="bg-primary/5 border border-primary/10 rounded-2xl p-6">
                    <h4 class="font-bold text-primary mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined">map</span>
                        Wilayah Layanan Kami:
                    </h4>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($service_areas as $w): ?>
                            <span class="px-3 py-1 bg-white border border-primary/20 rounded-full text-xs font-semibold text-primary"><?= esc($w['name']) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="grid gap-6">
                <!-- Prayer Times Widget (AlAdhan) -->
                <?php if (!empty($prayerData)): ?>
                <div class="bg-primary text-white p-8 rounded-[2rem] shadow-xl relative overflow-hidden group">
                     <div class="absolute top-0 right-0 p-24 bg-white/10 rounded-full blur-2xl -translate-y-1/2 translate-x-1/3"></div>
                     <div class="relative z-10">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h4 class="text-xl font-bold flex items-center gap-2">
                                    <span class="material-symbols-outlined">schedule</span> Jadwal Sholat
                                </h4>
                                <div class="flex flex-col text-xs text-emerald-100/90 mt-1">
                                    <span><?= $prayerData['date']['readable'] ?></span>
                                    <span class="font-bold text-white">
                                        <?= $prayerData['date']['hijri']['day'] ?> <?= $prayerData['date']['hijri']['month']['en'] ?> <?= $prayerData['date']['hijri']['year'] ?> H
                                    </span>
                                </div>
                            </div>
                            <div class="px-3 py-1 bg-white/20 rounded-lg text-xs font-bold text-white">
                                <?= $prayerData['meta']['timezone'] ?? 'WIB' ?>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <?php 
                            $times = [
                                'Imsak'   => $prayerData['timings']['Imsak'],
                                'Subuh'   => $prayerData['timings']['Fajr'],
                                'Terbit'  => $prayerData['timings']['Sunrise'],
                                'Dzuhur'  => $prayerData['timings']['Dhuhr'],
                                'Ashar'   => $prayerData['timings']['Asr'],
                                'Maghrib' => $prayerData['timings']['Maghrib'],
                                'Isya'    => $prayerData['timings']['Isha'],
                            ];
                            ?>
                            <div class="grid grid-cols-2 gap-3">
                                <?php foreach($times as $name => $time): 
                                    $isHighlight = ($name == 'Maghrib' || $name == 'Subuh' || $name == 'Dzuhur'); 
                                ?>
                                <div class="bg-white/10 p-2.5 rounded-xl flex items-center justify-between border border-white/5 <?= $isHighlight ? 'bg-white/20' : '' ?>">
                                    <span class="text-xs font-medium opacity-80"><?= $name ?></span>
                                    <span class="text-sm font-bold tracking-wide"><?= $time ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="mt-4 text-[10px] text-center text-emerald-200/60">
                            *Waktu sholat berdasarkan Koordinat Masjid (Kemenag RI)
                        </div>
                     </div>
                </div>
                <?php endif; ?>

                <!-- Visi & Misi logic -->
                <?php if (!empty($masjid['visi']) || !empty($masjid['misi'])): ?>
                    <!-- Visi -->
                    <?php if (!empty($masjid['visi'])): ?>
                    <div class="bg-background-light dark:bg-[#1a2e25] border border-[#dbe6e1] dark:border-[#1e3a2f] p-8 rounded-[2rem] shadow-sm relative overflow-hidden group">
                        <span class="material-symbols-outlined absolute -top-4 -right-4 text-8xl text-primary/5 transition-transform group-hover:scale-125">visibility</span>
                        <h4 class="text-xl font-bold mb-4 flex items-center gap-2">
                            <span class="size-8 bg-primary rounded-lg flex items-center justify-center text-white">
                                <span class="material-symbols-outlined text-sm">visibility</span>
                            </span>
                            Visi
                        </h4>
                        <p class="text-[#3d5a4d] dark:text-gray-400 font-medium italic">
                            "<?= esc($masjid['visi']) ?>"
                        </p>
                    </div>
                    <?php endif; ?>

                    <!-- Misi -->
                    <?php if (!empty($masjid['misi'])): ?>
                    <div class="bg-white dark:bg-[#1a2e25] border border-primary/20 p-8 rounded-[2rem] shadow-xl relative overflow-hidden group">
                        <span class="material-symbols-outlined absolute -top-4 -right-4 text-8xl text-primary/5 transition-transform group-hover:scale-125">flag</span>
                        <h4 class="text-xl font-bold mb-4 flex items-center gap-2 text-primary">
                            <span class="size-8 bg-primary rounded-lg flex items-center justify-center text-white shadow-lg">
                                <span class="material-symbols-outlined text-sm">flag</span>
                            </span>
                            Misi
                        </h4>
                        <ul class="space-y-4">
                            <?php 
                                $misiArray = explode("\n", $masjid['misi']);
                                foreach ($misiArray as $m): if (empty(trim($m))) continue;
                            ?>
                                <li class="flex gap-3 text-sm font-medium text-[#3d5a4d] dark:text-gray-400">
                                    <span class="material-symbols-outlined text-primary">check_circle</span>
                                    <?= esc(trim($m)) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Committee Section -->
<?php if (!empty($pengurus)): ?>
<section id="pengurus" class="py-24 px-6 bg-background-light dark:bg-background-dark/50">
    <div class="max-w-[1200px] mx-auto">
        <div class="text-center mb-16">
            <h2 class="text-sm font-bold text-primary uppercase tracking-[0.2em] mb-3">Struktur Organisasi</h2>
            <h3 class="text-3xl md:text-5xl font-black">Pengurus Masjid</h3>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php foreach ($pengurus as $p): ?>
                <div class="bg-white dark:bg-[#1a2e25] border border-[#dbe6e1] dark:border-[#1e3a2f] p-6 rounded-3xl shadow-sm hover:shadow-xl transition-all group flex flex-col items-center text-center">
                    <div class="size-20 bg-primary/10 rounded-full flex items-center justify-center text-primary mb-4 relative overflow-hidden">
                        <span class="material-symbols-outlined text-3xl">person</span>
                        <?php if (($p['is_creator'] ?? 0) == 1): ?>
                            <div class="absolute inset-0 bg-primary/20 flex items-center justify-center">
                                <span class="material-symbols-outlined text-3xl opacity-20">shield</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <h4 class="text-lg font-bold mb-1"><?= esc($p['user_name']) ?></h4>
                    <p class="text-sm font-bold text-primary mb-4 uppercase tracking-wider"><?= esc($p['title'] ?? ucfirst($p['role'])) ?></p>
                    
                    <div class="mt-auto flex gap-2">
                        <?php if (!empty($p['user_phone'])): 
                            $waPhone = preg_replace('/[^0-9]/', '', $p['user_phone']);
                            if (strpos($waPhone, '0') === 0) $waPhone = '62' . substr($waPhone, 1);
                            elseif (strpos($waPhone, '62') !== 0) $waPhone = '62' . $waPhone;
                        ?>
                            <a href="https://wa.me/<?= $waPhone ?>" target="_blank" class="size-10 bg-green-500 rounded-xl flex items-center justify-center text-white hover:scale-110 transition-transform">
                                <span class="material-symbols-outlined">chat</span>
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($p['user_email'])): ?>
                            <a href="mailto:<?= esc($p['user_email']) ?>" class="size-10 bg-primary rounded-xl flex items-center justify-center text-white hover:scale-110 transition-transform">
                                <span class="material-symbols-outlined">mail</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Berita & Kegiatan Section -->
<?php if ($masjid['menu_berita'] ?? 1): ?>
<section id="berita" class="py-24 px-6 bg-background-light dark:bg-background-dark/50 overflow-hidden">
    <div class="max-w-[1200px] mx-auto">
        <div class="text-center mb-16">
            <h2 class="text-sm font-bold text-primary uppercase tracking-[0.2em] mb-3">Warta Masjid</h2>
            <h3 class="text-3xl md:text-5xl font-black italic">Berita & Kegiatan</h3>
        </div>

        <?php if (empty($news)): ?>
            <div class="p-12 bg-white dark:bg-[#1a2e25] rounded-[2.5rem] border border-dashed border-[#dbe6e1] dark:border-[#1e3a2f] text-center">
                <span class="material-symbols-outlined text-6xl text-primary/20 mb-4 font-light">edit_calendar</span>
                <p class="text-[#608a7e] font-medium">Belum ada berita atau kegiatan terbaru yang dipublikasikan.</p>
            </div>
        <?php else: ?>
            <div class="grid md:grid-cols-3 gap-8">
                <?php foreach ($news as $item): ?>
                    <a href="<?= base_url($masjid['username'] . '/berita/' . $item['slug']) ?>" class="group bg-white dark:bg-white/5 rounded-[2.5rem] overflow-hidden border border-[#e5e7eb] dark:border-white/10 hover:shadow-2xl transition-all duration-500 flex flex-col">
                        <div class="aspect-[16/10] overflow-hidden relative">
                            <?php if (!empty($item['thumbnail'])): ?>
                                <img src="<?= $storage->url($item['thumbnail']) ?>" class="size-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <?php else: ?>
                                <div class="size-full bg-slate-100 flex items-center justify-center text-slate-300">
                                    <span class="material-symbols-outlined text-5xl">image</span>
                                </div>
                            <?php endif; ?>
                            <div class="absolute top-6 left-6">
                                <span class="px-3 py-1 bg-primary text-white text-[10px] font-bold uppercase tracking-widest rounded-lg">
                                    <?= esc($item['category_name'] ?: 'Umum') ?>
                                </span>
                            </div>
                        </div>
                        <div class="p-8 flex-1 flex flex-col">
                            <h4 class="text-xl font-black mb-4 group-hover:text-primary transition-colors line-clamp-2 leading-tight"><?= esc($item['title']) ?></h4>
                            <div class="mt-auto pt-6 border-t border-gray-100 dark:border-white/5 flex items-center justify-between">
                                <div class="flex items-center gap-2 text-xs font-bold text-[#608a7e]">
                                    <span class="material-symbols-outlined text-sm">calendar_today</span>
                                    <?= date('d M Y', strtotime($item['created_at'])) ?>
                                </div>
                                <span class="material-symbols-outlined text-primary group-hover:translate-x-2 transition-transform">arrow_right_alt</span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-12">
                <a href="<?= base_url($masjid['username'] . '/berita') ?>" class="inline-flex items-center gap-2 text-primary font-bold hover:underline">
                    Lihat Semua Berita
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<!-- Program Section -->
<?php if ($masjid['menu_program'] ?? 1): ?>
<section id="program" class="py-24 px-6 bg-white dark:bg-background-dark overflow-hidden">
    <div class="max-w-[1200px] mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-end gap-8 mb-16">
            <div>
                <h2 class="text-sm font-bold text-primary uppercase tracking-[0.2em] mb-3">Layanan Kami</h2>
                <h3 class="text-3xl md:text-5xl font-black">Agenda & Kegiatan</h3>
            </div>
            <a href="<?= base_url($masjid['username'] . '/program') ?>" class="inline-flex items-center gap-2 text-primary font-bold hover:underline mb-2">
                Lihat Semua Agenda
                <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>

        <?php if (empty($programs)): ?>
            <div class="p-12 bg-background-light dark:bg-white/5 rounded-[2.5rem] border border-dashed border-[#dbe6e1] dark:border-white/10 text-center">
                <span class="material-symbols-outlined text-6xl text-primary/20 mb-4 font-light">volunteer_activism</span>
                <p class="text-[#608a7e] font-medium">Belum ada agenda kegiatan dalam waktu dekat.</p>
            </div>
        <?php else: ?>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($programs as $item): ?>
                    <a href="<?= base_url($masjid['username'] . '/program/' . $item['slug']) ?>" class="group bg-white dark:bg-white/5 rounded-[2.5rem] border border-gray-100 dark:border-white/10 p-2 hover:shadow-2xl transition-all duration-500">
                        <div class="aspect-[4/3] rounded-[2rem] overflow-hidden relative mb-6">
                            <?php if (!empty($item['thumbnail'])): ?>
                                <img src="<?= $storage->url($item['thumbnail']) ?>" class="size-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <?php else: ?>
                                <div class="size-full bg-slate-50 dark:bg-white/5 flex items-center justify-center text-slate-300">
                                    <span class="material-symbols-outlined text-5xl">event</span>
                                </div>
                            <?php endif; ?>
                            <div class="absolute top-4 left-4 bg-white/90 dark:bg-background-dark/90 backdrop-blur-md px-3 py-1.5 rounded-xl flex items-center gap-1.5 shadow-sm">
                                <span class="material-symbols-outlined text-xs text-primary">calendar_today</span>
                                <span class="text-[10px] font-bold"><?= date('d M Y', strtotime($item['date_start'])) ?></span>
                            </div>
                        </div>
                        <div class="px-6 pb-6 mt-4">
                            <h4 class="text-xl font-bold mb-4 line-clamp-2 group-hover:text-primary transition-colors"><?= esc($item['title']) ?></h4>
                            <div class="flex items-center gap-2 text-xs text-[#608a7e]">
                                <span class="material-symbols-outlined text-sm">location_on</span>
                                <?= esc($item['location']) ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<!-- Laporan Transparansi -->
<?php if ($masjid['menu_laporan'] ?? 1): ?>
<section id="laporan" class="py-24 px-6 bg-background-light dark:bg-background-dark/50 relative overflow-hidden">
    <div class="max-w-[1200px] mx-auto relative z-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 mb-16">
            <div class="max-w-xl">
                <h2 class="text-sm font-bold text-primary uppercase tracking-[0.2em] mb-3">Transparansi</h2>
                <h3 class="text-3xl md:text-5xl font-black mb-6 tracking-tight leading-none text-[#111816] dark:text-white">Laporan Keuangan</h3>
                <p class="text-lg text-[#608a7e] mb-6">Laporan keuangan masjid yang dikelola secara terbuka untuk jamaah.</p>
                <a href="<?= base_url($masjid['username'] . '/laporan') ?>" class="inline-flex items-center gap-2 bg-white dark:bg-white/5 border border-primary text-primary px-6 py-3 rounded-xl font-bold hover:bg-primary hover:text-white transition-all">
                    <span class="material-symbols-outlined">analytics</span>
                    Lihat Laporan Detail
                </a>
            </div>
            <div class="bg-primary px-8 py-6 rounded-[2rem] text-white shadow-2xl shadow-primary/20">
                <p class="text-emerald-200 text-[10px] font-black uppercase tracking-widest mb-1">Saldo Saat Ini</p>
                <h3 class="text-3xl font-black">Rp <?= number_format($financeSummary['balance'], 0, ',', '.') ?></h3>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white dark:bg-white/5 rounded-[3rem] p-10 border border-[#dbe6e1] dark:border-white/10 group hover:shadow-2xl transition-all duration-500">
                <div class="flex items-center gap-4 mb-8">
                    <div class="size-16 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600">
                        <span class="material-symbols-outlined text-3xl">trending_up</span>
                    </div>
                    <h4 class="text-xl font-bold">Total Pemasukan</h4>
                </div>
                <div class="text-4xl font-black text-emerald-600 mb-4">Rp <?= number_format($financeSummary['total_income'], 0, ',', '.') ?></div>
                <p class="text-[#608a7e] text-sm leading-relaxed">Akumulasi donasi, zakat, infaq dan sedekah dari jamaah.</p>
            </div>

            <div class="bg-white dark:bg-white/5 rounded-[3rem] p-10 border border-[#dbe6e1] dark:border-white/10 group hover:shadow-2xl transition-all duration-500">
                <div class="flex items-center gap-4 mb-8">
                    <div class="size-16 bg-red-50 rounded-2xl flex items-center justify-center text-red-500">
                        <span class="material-symbols-outlined text-3xl">trending_down</span>
                    </div>
                    <h4 class="text-xl font-bold">Total Pengeluaran</h4>
                </div>
                <div class="text-4xl font-black text-red-500 mb-4">Rp <?= number_format($financeSummary['total_expense'], 0, ',', '.') ?></div>
                <p class="text-[#608a7e] text-sm leading-relaxed">Penggunaan dana untuk operasional dan program masjid.</p>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Gallery Section -->
<?php if (!empty($gallery)): ?>
<section id="galeri" class="py-24 px-6 bg-white dark:bg-background-dark overflow-hidden">
    <div class="max-w-[1200px] mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-end gap-8 mb-16">
            <div>
                <h2 class="text-sm font-bold text-primary uppercase tracking-[0.2em] mb-3">Galeri & Fasilitas</h2>
                <h3 class="text-3xl md:text-5xl font-black italic">Dokumentasi Kami</h3>
            </div>
            
            <?php 
                $uniqueCats = array_unique(array_column($gallery, 'category'));
            ?>
            <div class="flex flex-wrap gap-2">
                <button onclick="filterPublicGallery('all')" class="pub-gallery-btn active px-5 py-2 rounded-full text-xs font-bold border border-primary bg-primary text-white transition-all shadow-lg" data-category="all">Semua</button>
                <?php foreach ($uniqueCats as $cat): ?>
                    <button onclick="filterPublicGallery('<?= esc($cat) ?>')" class="pub-gallery-btn px-5 py-2 rounded-full text-xs font-bold border border-[#dbe6e3] text-[#608a7e] hover:border-primary hover:text-primary transition-all" data-category="<?= esc($cat) ?>"><?= esc($cat) ?></button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6" id="publicGalleryGrid">
            <?php foreach ($gallery as $img): ?>
                <div class="photo-item-pub aspect-square rounded-3xl overflow-hidden relative group cursor-pointer shadow-lg shadow-black/5" data-category="<?= esc($img['category']) ?>">
                    <img src="<?= $storage->url($img['image_path']) ?>" alt="Gallery" class="size-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent opacity-0 group-hover:opacity-100 transition-opacity p-6 flex items-end">
                        <span class="text-white text-[10px] font-bold uppercase tracking-widest px-2 py-1 bg-primary rounded"><?= esc($img['category']) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script>
    function filterPublicGallery(category) {
        const photos = document.querySelectorAll('.photo-item-pub');
        const buttons = document.querySelectorAll('.pub-gallery-btn');
        
        buttons.forEach(btn => {
            if (btn.dataset.category === category) {
                btn.classList.add('active', 'bg-primary', 'text-white', 'shadow-lg');
                btn.classList.remove('border-[#dbe6e3]', 'text-[#608a7e]');
            } else {
                btn.classList.remove('active', 'bg-primary', 'text-white', 'shadow-lg');
                btn.classList.add('border-[#dbe6e3]', 'text-[#608a7e]');
            }
        });

        photos.forEach(photo => {
            if (category === 'all' || photo.dataset.category === category) {
                photo.style.display = 'block';
                photo.classList.add('animate-in', 'fade-in', 'zoom-in');
            } else {
                photo.style.display = 'none';
            }
        });
    }
</script>
<?php endif; ?>

<!-- Newsletter Subscription -->
<section id="subscribe" class="py-24 px-6 bg-gradient-to-br from-indigo-900 to-slate-900 relative overflow-hidden text-white">
    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
    <div class="max-w-[1200px] mx-auto relative z-10 text-center">
        <span class="material-symbols-outlined text-6xl text-emerald-400 mb-6 animate-bounce">mark_email_unread</span>
        <h2 class="text-sm font-bold text-emerald-400 uppercase tracking-[0.2em] mb-3">Newsletter</h2>
        <h3 class="text-3xl md:text-5xl font-black mb-6">Jangan Lewatkan Info Terbaru</h3>
        <p class="text-white/70 text-lg mb-12 max-w-2xl mx-auto">Dapatkan update jadwal kajian, laporan keuangan, dan kegiatan sosial <?= esc($masjid['name']) ?> langsung di email Anda.</p>

        <form action="<?= base_url('subscribe') ?>" method="POST" class="max-w-md mx-auto flex flex-col gap-4">
            <?= csrf_field() ?>
            <input type="hidden" name="masjid_username" value="<?= esc($masjid['username']) ?>">
            
            <input type="text" name="name" required placeholder="Nama Lengkap" class="w-full px-6 py-4 rounded-2xl bg-white/10 border border-white/20 text-white placeholder-white/50 focus:ring-2 focus:ring-emerald-400 focus:border-transparent backdrop-blur-sm transition-all text-center font-bold">
            
            <input type="email" name="email" required placeholder="Alamat Email Anda" class="w-full px-6 py-4 rounded-2xl bg-white/10 border border-white/20 text-white placeholder-white/50 focus:ring-2 focus:ring-emerald-400 focus:border-transparent backdrop-blur-sm transition-all text-center font-bold">
            
            <button type="submit" class="bg-emerald-500 hover:bg-emerald-400 text-white px-8 py-4 rounded-2xl font-black uppercase tracking-widest transition-all shadow-lg shadow-emerald-500/30 transform hover:scale-105">
                Berlangganan Sekarang
            </button>
            <p class="text-xs text-white/40 mt-2">Kami menjaga privasi data Anda. Unsubscribe kapan saja.</p>
        </form>
    </div>
</section>

<!-- Location Section -->
<?php if ($masjid['menu_kontak'] ?? 1): ?>
<section id="kontak" class="py-24 px-6 bg-background-light dark:bg-background-dark/50">
    <div class="max-w-[1200px] mx-auto">
        <div class="flex flex-col lg:flex-row gap-16">
            <div class="flex-1 space-y-8">
                <div>
                    <h2 class="text-sm font-bold text-primary uppercase tracking-[0.2em] mb-3">Informasi Kontak</h2>
                    <h3 class="text-3xl font-black mb-6">Lokasi & Alamat</h3>
                    <p class="text-[#3d5a4d] dark:text-gray-400 text-lg mb-8 leading-relaxed">
                        Kami mengundang Anda untuk bersilaturahmi langsung ke <?= esc($masjid['name']) ?>. Berikut adalah informasi alamat dan titik lokasi tepat kami:
                    </p>
                </div>

                <div class="grid sm:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-[#1a2e25] p-6 rounded-2xl border border-[#dbe6e1] dark:border-[#1e3a2f] shadow-sm">
                        <div class="size-10 bg-primary/10 rounded-lg flex items-center justify-center text-primary mb-4">
                            <span class="material-symbols-outlined">location_on</span>
                        </div>
                        <h4 class="font-bold mb-2">Alamat Lengkap</h4>
                        <p class="text-sm text-gray-500 leading-relaxed"><?= esc($masjid['address']) ?></p>
                    </div>
                    <div class="bg-white dark:bg-[#1a2e25] p-6 rounded-2xl border border-[#dbe6e1] dark:border-[#1e3a2f] shadow-sm">
                        <div class="size-10 bg-primary/10 rounded-lg flex items-center justify-center text-primary mb-4">
                            <span class="material-symbols-outlined">mail</span>
                        </div>
                        <h4 class="font-bold mb-2">Kontak Resmi</h4>
                        <div class="space-y-2 mt-2">
                            <?php if (!empty($masjid['phone'])): ?>
                                <p class="text-sm text-gray-500 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-xs">call</span> <?= esc($masjid['phone']) ?>
                                </p>
                            <?php endif; ?>
                            <?php if (!empty($masjid['whatsapp'])): ?>
                                <p class="text-sm text-gray-500 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-xs">chat</span> <?= esc($masjid['whatsapp']) ?> (WA)
                                </p>
                            <?php endif; ?>
                            <p class="text-sm text-gray-500 flex items-center gap-2">
                                <span class="material-symbols-outlined text-xs">mail</span> 
                                <?= !empty($masjid['email']) ? esc($masjid['email']) : esc($masjid['username']) . '@masj.id' ?>
                            </p>
                        </div>
                        
                        <?php if (!empty($socials)): ?>
                        <h4 class="font-bold mb-2 mt-6">Ikuti Kami</h4>
                        <div class="flex flex-wrap gap-3">
                            <?php foreach($socials as $soc): 
                                $icon = 'link';
                                $color = 'bg-gray-100 text-gray-600';
                                switch($soc['platform']) {
                                    case 'instagram': $icon = 'photo_camera'; $color = 'bg-pink-100 text-pink-600'; break; // Material doesn't have brands, using proxies
                                    case 'facebook': $icon = 'thumb_up'; $color = 'bg-blue-100 text-blue-600'; break;
                                    case 'tiktok': $icon = 'music_note'; $color = 'bg-black text-white'; break;
                                    case 'youtube': $icon = 'play_circle'; $color = 'bg-red-100 text-red-600'; break;
                                    case 'twitter': $icon = 'flutter_dash'; $color = 'bg-sky-100 text-sky-600'; break;
                                    case 'whatsapp_group': $icon = 'groups'; $color = 'bg-green-100 text-green-600'; break;
                                    case 'telegram_group': $icon = 'send'; $color = 'bg-blue-50 text-blue-500'; break;
                                    case 'website': $icon = 'language'; $color = 'bg-emerald-100 text-emerald-600'; break;
                                }
                            ?>
                            <a href="<?= esc($soc['url']) ?>" target="_blank" class="size-10 <?= $color ?> rounded-xl flex items-center justify-center hover:scale-110 transition-transform shadow-sm" title="<?= ucfirst(str_replace('_', ' ', $soc['platform'])) ?>">
                                <span class="material-symbols-outlined"><?= $icon ?></span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Call to Action -->
                <div class="p-8 bg-primary rounded-[2.5rem] text-white relative overflow-hidden shadow-2xl shadow-primary/30">
                    <div class="relative z-10">
                        <h4 class="text-2xl font-black mb-4">Ingin Berkolaborasi?</h4>
                        <p class="text-white/70 mb-8 max-w-[400px]">Hubungi sekretariat kami untuk pengajuan kerjasama, program sosial, atau informasi lainnya.</p>
                        <a href="mailto:<?= esc($masjid['username']) ?>@masj.id" class="inline-flex items-center gap-2 bg-white text-primary px-8 py-3 rounded-xl font-bold hover:bg-emerald-50 transition-all">
                            <span class="material-symbols-outlined text-sm">send</span>
                            Kirim Pesan
                        </a>
                    </div>
                    <span class="material-symbols-outlined absolute -bottom-10 -right-10 text-[12rem] opacity-10">forum</span>
                </div>
            </div>

            <div class="flex-1 min-h-[400px] h-full rounded-[2.5rem] overflow-hidden border-8 border-white dark:border-white/5 shadow-2xl relative">
                <div id="map" class="size-full bg-slate-100"></div>
                <!-- Pin Detail Overlay -->
                <div class="absolute bottom-6 left-6 right-6 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md p-5 rounded-2xl border border-[#dbe6e1] dark:border-white/10 shadow-lg flex items-center gap-4">
                    <div class="size-12 bg-primary rounded-xl flex items-center justify-center text-white flex-shrink-0">
                        <span class="material-symbols-outlined">mosque</span>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1 text-left">Titik Koordinat</p>
                        <p class="text-xs font-bold truncate text-left"><?= $masjid['latitude'] ?>, <?= $masjid['longitude'] ?></p>
                    </div>
                    <a href="https://www.google.com/maps/search/?api=1&query=<?= $masjid['latitude'] ?>,<?= $masjid['longitude'] ?>" target="_blank" class="ml-auto size-10 bg-primary/10 rounded-full flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-colors">
                        <span class="material-symbols-outlined text-lg">directions</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Implementation -->
<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?= env('GOOGLE_MAPS_API_KEY') ?>&callback=initMap"></script>
<script>
    function initMap() {
        const loc = { lat: <?= $masjid['latitude'] ?? 0 ?>, lng: <?= $masjid['longitude'] ?? 0 ?> };
        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 15,
            center: loc,
            styles: [
                {
                    "featureType": "all",
                    "elementType": "labels.text.fill",
                    "stylers": [{ "saturation": 36 }, { "color": "#000000" }, { "lightness": 40 }]
                },
                {
                    "featureType": "water",
                    "elementType": "all",
                    "stylers": [{ "color": "#e9f3f1" }, { "visibility": "on" }]
                }
            ],
            disableDefaultUI: true,
            zoomControl: true,
        });
        const marker = new google.maps.Marker({
            position: loc,
            map: map,
            icon: {
                url: "https://maps.google.com/mapfiles/ms/icons/green-dot.png"
            }
        });
    }
</script>
<?php endif; ?>

<?= $this->endSection() ?>
