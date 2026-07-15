<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <style>
        body { font-family: 'Inter', sans-serif; cursor: none; overflow: hidden; }
        .swiper-slide { height: 100vh; }
        .bg-glass { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); }
        @keyframes pulse-soft { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
        .animate-pulse-soft { animation: pulse-soft 3s infinite; }

        /* Teks berjalan: mulai dari tepi kanan layar lalu bergerak ke kiri
           sampai habis, berapa pun panjang teksnya. */
        @keyframes running-text {
            from { transform: translateX(100vw); }
            to   { transform: translateX(-100%); }
        }
        .running-text {
            animation: running-text 40s linear infinite;
            will-change: transform;
        }
    </style>
</head>
<body class="bg-[#061510] text-white">
    
    <!-- Top Navbar (Always Visible) -->
    <div class="fixed top-0 left-0 right-0 z-50 bg-black/40 backdrop-blur-md border-b border-white/10 px-12 py-6 flex justify-between items-center">
        <div class="flex items-center gap-4">
            <?php if (!empty($masjid['logo'])): ?>
                <img src="<?= $storage->url($masjid['logo']) ?>" class="size-16 rounded-full bg-white p-1">
            <?php endif; ?>
            <div>
                <h1 class="text-3xl font-black tracking-tight"><?= esc($masjid['name']) ?></h1>
                <p class="text-emerald-400 text-sm font-bold uppercase tracking-widest"><?= esc($masjid['kabupaten']) ?>, <?= esc($masjid['provinsi']) ?></p>
            </div>
        </div>
        <div class="text-right">
            <h2 id="live-clock" class="text-5xl font-black">00:00:00</h2>
            <p id="live-date" class="text-emerald-400 font-bold">Minggu, 10 Mei 2026</p>
            <?php if (!empty($hijriDate)): ?>
                <p class="text-white/50 text-sm font-bold tracking-wide"><?= esc($hijriDate) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Slideshow -->
    <div class="swiper mySwiper">
        <div class="swiper-wrapper">
            
            <!-- Slide 1: Prayer Times -->
            <?php if ($prayerData): ?>
            <div class="swiper-slide flex items-center justify-center pt-24 px-12">
                <div class="grid grid-cols-7 gap-6 w-full max-w-7xl">
                    <?php 
                    $times = [
                        'Subuh'   => $prayerData['timings']['Fajr'],
                        'Terbit'  => $prayerData['timings']['Sunrise'],
                        'Dzuhur'  => $prayerData['timings']['Dhuhr'],
                        'Ashar'   => $prayerData['timings']['Asr'],
                        'Maghrib' => $prayerData['timings']['Maghrib'],
                        'Isya'    => $prayerData['timings']['Isha'],
                    ];
                    foreach($times as $name => $time): 
                    ?>
                    <div class="bg-glass border border-white/10 p-8 rounded-[2.5rem] flex flex-col items-center justify-center text-center">
                        <p class="text-emerald-400 text-sm font-black uppercase tracking-[0.2em] mb-4"><?= $name ?></p>
                        <h3 class="text-6xl font-black mb-2"><?= $time ?></h3>
                    </div>
                    <?php endforeach; ?>
                    
                    <div class="bg-primary p-8 rounded-[2.5rem] flex flex-col items-center justify-center text-center shadow-2xl shadow-primary/20">
                        <p class="text-emerald-200 text-sm font-black uppercase tracking-[0.2em] mb-4">Sholat Berikutnya</p>
                        <h3 id="next-prayer-name" class="text-2xl font-bold mb-1">-</h3>
                        <h3 id="next-prayer-time" class="text-5xl font-black">--:--</h3>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Slide 2: Financial Transparency -->
            <div class="swiper-slide flex items-center justify-center pt-24 px-12">
                <div class="max-w-6xl w-full">
                    <div class="text-center mb-16">
                        <h2 class="text-emerald-400 text-xl font-black uppercase tracking-[0.3em] mb-4">Transparansi Keuangan</h2>
                        <h3 class="text-6xl font-black tracking-tight leading-none">Laporan Kas Masjid</h3>
                    </div>
                    <div class="grid grid-cols-3 gap-12">
                        <div class="bg-glass border border-white/10 p-12 rounded-[3rem] text-center">
                            <span class="material-symbols-outlined text-6xl text-emerald-400 mb-6">trending_up</span>
                            <p class="text-gray-400 font-bold uppercase tracking-widest mb-2">Total Pemasukan</p>
                            <h4 class="text-5xl font-black text-emerald-500">Rp <?= number_format($financeSummary['total_income'], 0, ',', '.') ?></h4>
                        </div>
                        <div class="bg-glass border border-white/10 p-12 rounded-[3rem] text-center">
                            <span class="material-symbols-outlined text-6xl text-red-400 mb-6">trending_down</span>
                            <p class="text-gray-400 font-bold uppercase tracking-widest mb-2">Total Pengeluaran</p>
                            <h4 class="text-5xl font-black text-red-500">Rp <?= number_format($financeSummary['total_expense'], 0, ',', '.') ?></h4>
                        </div>
                        <div class="bg-primary p-12 rounded-[3rem] text-center shadow-2xl shadow-primary/40">
                            <span class="material-symbols-outlined text-6xl text-white mb-6">account_balance_wallet</span>
                            <p class="text-emerald-200 font-bold uppercase tracking-widest mb-2">Saldo Amanah</p>
                            <h4 class="text-6xl font-black">Rp <?= number_format($financeSummary['balance'], 0, ',', '.') ?></h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 3: Active Programs -->
            <?php if (!empty($programs)): ?>
            <div class="swiper-slide flex flex-col items-center justify-center pt-24 px-12">
                <div class="text-center mb-12">
                    <h2 class="text-emerald-400 text-xl font-black uppercase tracking-[0.3em] mb-4">Agenda & Kegiatan</h2>
                    <h3 class="text-6xl font-black">Makmurkan Rumah Allah</h3>
                </div>
                <div class="grid grid-cols-3 gap-8 w-full max-w-7xl">
                    <?php foreach ($programs as $p): ?>
                    <div class="bg-glass border border-white/10 rounded-[2.5rem] overflow-hidden flex flex-col">
                        <div class="h-48 bg-slate-800 relative">
                            <?php if ($p['thumbnail']): ?>
                                <img src="<?= $storage->url($p['thumbnail']) ?>" class="size-full object-cover opacity-60">
                            <?php endif; ?>
                            <div class="absolute inset-0 bg-gradient-to-t from-[#061510] to-transparent"></div>
                            <div class="absolute bottom-6 left-6">
                                <span class="px-3 py-1 bg-primary text-white text-xs font-black rounded-lg uppercase tracking-widest"><?= date('d M Y', strtotime($p['date_start'])) ?></span>
                            </div>
                        </div>
                        <div class="p-8 flex-1">
                            <h4 class="text-2xl font-black mb-4 leading-tight"><?= esc($p['title']) ?></h4>
                            <div class="flex items-center gap-2 text-gray-400 font-bold">
                                <span class="material-symbols-outlined text-sm">location_on</span>
                                <?= esc($p['location']) ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Slide 4: Recent Impact Highlights -->
            <?php if (!empty($impactHighlights)): ?>
            <div class="swiper-slide flex flex-col items-center justify-center pt-24 px-12">
                <div class="text-center mb-12">
                    <h2 class="text-emerald-400 text-xl font-black uppercase tracking-[0.3em] mb-4">Jejak Kebaikan</h2>
                    <h3 class="text-6xl font-black">Amanah Yang Terwujud</h3>
                </div>
                <div class="w-full max-w-5xl space-y-4">
                    <?php foreach ($impactHighlights as $impact): ?>
                    <div class="bg-glass border border-white/10 p-6 rounded-3xl flex justify-between items-center group hover:bg-white/10 transition-all">
                        <div class="flex items-center gap-6">
                            <div class="size-16 rounded-2xl bg-emerald-500/20 flex items-center justify-center text-emerald-400">
                                <span class="material-symbols-outlined text-3xl">volunteer_activism</span>
                            </div>
                            <div>
                                <h4 class="text-2xl font-bold"><?= esc($impact['description']) ?></h4>
                                <p class="text-gray-400 text-sm font-bold uppercase tracking-widest"><?= esc($impact['category_name']) ?> • <?= date('d M Y', strtotime($impact['date'])) ?></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-emerald-400 text-3xl font-black">Rp <?= number_format($impact['amount'], 0, ',', '.') ?></p>
                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-[0.2em]">Disalurkan</p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Slide 5: Donasi Masuk Terbaru (apresiasi donatur) -->
            <?php if (!empty($recentDonations)): ?>
            <div class="swiper-slide flex flex-col items-center justify-center pt-24 px-12">
                <div class="text-center mb-12">
                    <h2 class="text-emerald-400 text-xl font-black uppercase tracking-[0.3em] mb-4">Jazakumullahu Khairan</h2>
                    <h3 class="text-6xl font-black">Donasi Terbaru</h3>
                </div>
                <div class="w-full max-w-5xl grid grid-cols-2 gap-4">
                    <?php foreach ($recentDonations as $donasi): ?>
                    <?php
                        // Donatur boleh tidak mencantumkan nama.
                        $namaDonatur = trim((string) ($donasi['donor_name'] ?? ''));
                        if ($namaDonatur === '') $namaDonatur = 'Hamba Allah';
                        $tanggal = $donasi['paid_at'] ?: $donasi['created_at'];
                    ?>
                    <div class="bg-glass border border-white/10 p-6 rounded-3xl flex justify-between items-center gap-4">
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="size-14 rounded-2xl bg-emerald-500/20 flex items-center justify-center text-emerald-400 shrink-0">
                                <span class="material-symbols-outlined text-2xl">favorite</span>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xl font-bold truncate"><?= esc($namaDonatur) ?></h4>
                                <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">
                                    <?= $tanggal ? date('d M Y', strtotime($tanggal)) : '' ?>
                                </p>
                            </div>
                        </div>
                        <p class="text-emerald-400 text-2xl font-black whitespace-nowrap">
                            Rp <?= number_format((float) $donasi['amount'], 0, ',', '.') ?>
                        </p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>

        <!-- Teks Berjalan (running text) -->
        <?php if (!empty($runningText)): ?>
        <div class="fixed bottom-2 left-0 right-0 z-40 h-14 bg-black/70 backdrop-blur-md border-t border-white/10 flex items-center overflow-hidden">
            <div class="running-text whitespace-nowrap text-2xl font-bold text-white/90">
                <?= esc($runningText) ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Progress Bar -->
        <div class="fixed bottom-0 left-0 right-0 h-2 bg-white/5 z-50">
            <div id="slide-progress" class="h-full bg-primary transition-all duration-100 ease-linear" style="width: 0%"></div>
        </div>
    </div>

    <!-- QR Code Overlay (Bottom Right) — diangkat agar tidak tertimpa teks berjalan -->
    <div class="fixed <?= !empty($runningText) ? 'bottom-24' : 'bottom-12' ?> right-12 z-50 bg-white p-4 rounded-3xl shadow-2xl flex flex-col items-center gap-3">
        <?php 
            $publicUrl = base_url($masjid['username']);
            $qrUrl = "https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=" . urlencode($publicUrl) . "&choe=UTF-8";
        ?>
        <img src="<?= $qrUrl ?>" class="size-32">
        <p class="text-black text-[10px] font-black uppercase tracking-widest">Scan Profil Masjid</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        // Swiper Config
        const SLIDE_DURATION = 10000; // 10 seconds per slide
        const swiper = new Swiper(".mySwiper", {
            loop: true,
            effect: "fade",
            autoplay: {
                delay: SLIDE_DURATION,
                disableOnInteraction: false,
            },
            on: {
                autoplayTimeLeft(s, time, progress) {
                    document.getElementById('slide-progress').style.width = ((1 - progress) * 100) + '%';
                }
            }
        });

        // Live Clock
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour12: false });
            const dateStr = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            
            document.getElementById('live-clock').textContent = timeStr;
            document.getElementById('live-date').textContent = dateStr;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Refresh Page every 15 minutes to sync data
        setTimeout(() => window.location.reload(), 15 * 60 * 1000);

        // Next Prayer Logic
        const prayers = <?= json_encode($times ?? []) ?>;
        function updateNextPrayer() {
            const now = new Date();
            const currentH = now.getHours();
            const currentM = now.getMinutes();
            const currentTime = (currentH * 60) + currentM;

            let nextP = { name: '-', time: '--:--' };
            let minDiff = 1440;

            for (const [name, time] of Object.entries(prayers)) {
                const [h, m] = time.split(':').map(Number);
                const prayerTime = (h * 60) + m;
                let diff = prayerTime - currentTime;
                
                if (diff > 0 && diff < minDiff) {
                    minDiff = diff;
                    nextP = { name, time };
                }
            }

            if (nextP.name === '-') {
                // If all passed, first one is tomorrow (Subuh)
                const first = Object.entries(prayers)[0];
                nextP = { name: first[0], time: first[1] };
            }

            document.getElementById('next-prayer-name').textContent = nextP.name;
            document.getElementById('next-prayer-time').textContent = nextP.time;
        }
        updateNextPrayer();
    </script>
</body>
</html>
