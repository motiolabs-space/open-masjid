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
        <!-- Hitung mundur menuju sholat berikutnya — selalu terlihat, tidak
             ikut bergantian seperti slide. -->
        <?php if ($prayerData): ?>
        <div id="navbar-countdown" class="hidden flex-col items-center px-8 py-3 rounded-2xl bg-white/5 border border-white/10">
            <p class="text-white/50 text-[11px] font-black uppercase tracking-[0.25em] mb-1">
                Menuju <span id="navbar-next-name">-</span>
            </p>
            <h2 id="navbar-next-countdown" class="text-4xl font-black tabular-nums text-emerald-400">--:--:--</h2>
        </div>
        <?php endif; ?>

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
                        <p id="next-prayer-countdown" class="text-emerald-200/70 text-xl font-black tabular-nums mt-3">--:--:--</p>
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

    <!--
        Layar Waktu Sholat — mengambil alih seluruh layar saat masuk waktu sholat.
        Urutan: ADZAN -> hitung mundur IQOMAH -> layar gelap saat SHOLAT.
        Disembunyikan secara bawaan; dikendalikan oleh mesin keadaan di bawah.
    -->
    <div id="prayer-overlay" class="fixed inset-0 z-[60] hidden items-center justify-center text-center">

        <!-- Mode ADZAN & IQOMAH -->
        <div id="overlay-adzan" class="hidden flex-col items-center justify-center w-full h-full bg-gradient-to-br from-[#04120d] via-[#062b1d] to-[#04120d]">
            <p class="text-emerald-400 text-2xl font-black uppercase tracking-[0.4em] mb-6" id="overlay-label">Waktu Sholat</p>
            <h1 class="text-[10rem] leading-none font-black tracking-tight mb-4" id="overlay-prayer">Maghrib</h1>
            <h2 class="text-7xl font-black text-emerald-400 mb-12" id="overlay-time">17:52</h2>

            <div id="overlay-countdown-wrap" class="hidden flex-col items-center">
                <p id="overlay-countdown-label" class="text-white/50 text-xl font-bold uppercase tracking-[0.3em] mb-3">Iqomah Dalam</p>
                <h2 class="text-9xl font-black tabular-nums" id="overlay-countdown">05:00</h2>
            </div>

            <p id="overlay-note" class="text-white/40 text-xl font-bold mt-10">Marilah menunaikan sholat berjamaah</p>
        </div>

        <!-- Mode SHOLAT — layar gelap, jam redup agar jamaah tahu display tetap hidup -->
        <div id="overlay-sholat" class="hidden flex-col items-center justify-center w-full h-full bg-black">
            <h2 class="text-white/20 text-8xl font-black tabular-nums" id="overlay-dim-clock">00:00</h2>
            <p class="text-white/10 text-lg font-bold uppercase tracking-[0.4em] mt-6">Sedang Sholat Berjamaah</p>
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

        // Muat ulang tiap 15 menit untuk menyegarkan data. Ditunda bila layar
        // waktu sholat sedang aktif agar adzan/iqomah tidak terpotong reload.
        setTimeout(function muatUlang() {
            if (modeKini === 'NORMAL' || modeKini === null) {
                window.location.reload();
            } else {
                setTimeout(muatUlang, 30 * 1000);
            }
        }, 15 * 60 * 1000);

        const prayers = <?= json_encode($times ?? []) ?>;

        // 'Terbit' (syuruq) bukan waktu sholat — tidak boleh dihitung sebagai
        // sholat berikutnya maupun memicu layar adzan.
        const WAKTU_SHOLAT = Object.fromEntries(
            Object.entries(prayers).filter(([nama]) => nama !== 'Terbit')
        );

        // AlAdhan dapat mengembalikan "17:52" atau "17:52 (WIB)".
        function jamKeMenit(jam) {
            const [h, m] = String(jam).trim().split(' ')[0].split(':').map(Number);
            return (h * 60) + m;
        }
        function jamBersih(jam) {
            return String(jam).trim().split(' ')[0];
        }

        // ── Sholat berikutnya (navbar) ──────────────────────────────────────
        function updateNextPrayer() {
            const now = new Date();
            const currentTime = (now.getHours() * 60) + now.getMinutes();

            let nextP = null;
            let minDiff = 1440;

            for (const [name, time] of Object.entries(WAKTU_SHOLAT)) {
                const diff = jamKeMenit(time) - currentTime;
                if (diff > 0 && diff < minDiff) {
                    minDiff = diff;
                    nextP = { name, time };
                }
            }

            // Semua sudah lewat → sholat pertama besok.
            let besok = false;
            if (!nextP) {
                const first = Object.entries(WAKTU_SHOLAT)[0];
                if (!first) return;
                nextP = { name: first[0], time: first[1] };
                besok = true;
            }

            document.getElementById('next-prayer-name').textContent = nextP.name;
            document.getElementById('next-prayer-time').textContent = jamBersih(nextP.time);

            // Sisa waktu menuju adzan berikutnya (melewati tengah malam bila perlu).
            const detikKini = (now.getHours() * 3600) + (now.getMinutes() * 60) + now.getSeconds();
            let sisa = (jamKeMenit(nextP.time) * 60) - detikKini;
            if (besok || sisa < 0) sisa += 24 * 3600;

            const jj = String(Math.floor(sisa / 3600)).padStart(2, '0');
            const mm = String(Math.floor((sisa % 3600) / 60)).padStart(2, '0');
            const dd = String(Math.floor(sisa % 60)).padStart(2, '0');
            const teks = jj + ':' + mm + ':' + dd;

            const elSlide = document.getElementById('next-prayer-countdown');
            if (elSlide) elSlide.textContent = teks;

            const elNav = document.getElementById('navbar-countdown');
            if (elNav) {
                document.getElementById('navbar-next-name').textContent = nextP.name;
                document.getElementById('navbar-next-countdown').textContent = teks;
                // Disembunyikan saat layar sholat aktif agar tidak mengganggu.
                tampil(elNav, modeKini === 'NORMAL' || modeKini === null);
            }
        }

        // ── Layar Waktu Sholat ──────────────────────────────────────────────
        // Alur: ADZAN → hitung mundur IQOMAH → layar gelap saat SHOLAT → NORMAL.
        const IQOMAH_MENIT    = <?= json_encode($iqomahSettings ?? []) ?>;
        const SHOLAT_MENIT    = <?= (int) ($sholatDuration ?? 10) ?>;
        const ADZAN_MENIT     = 3; // lama layar adzan sebelum hitung mundur iqomah
        const PRA_ADZAN_MENIT = 5; // layar penuh hitung mundur menjelang adzan

        function cariKeadaan(now) {
            const detikKini = (now.getHours() * 3600) + (now.getMinutes() * 60) + now.getSeconds();

            for (const [nama, jam] of Object.entries(WAKTU_SHOLAT)) {
                const mulai = jamKeMenit(jam) * 60;
                const lewat = detikKini - mulai; // detik sejak adzan

                // Menjelang adzan: layar penuh berisi hitung mundur.
                if (lewat < 0) {
                    const menuju = -lewat;
                    if (menuju <= PRA_ADZAN_MENIT * 60) {
                        return { mode: 'PRA_ADZAN', nama, jam, sisa: menuju };
                    }
                    continue;
                }

                const iqomah = (IQOMAH_MENIT[nama] ?? 10) * 60;
                // Bila jeda iqomah lebih pendek dari layar adzan, layar adzan
                // dipersingkat agar tidak menabrak hitung mundur.
                const adzanSelesai  = Math.min(ADZAN_MENIT * 60, iqomah);
                const sholatSelesai = iqomah + (SHOLAT_MENIT * 60);

                if (lewat < adzanSelesai)  return { mode: 'ADZAN', nama, jam };
                if (lewat < iqomah)        return { mode: 'IQOMAH', nama, jam, sisa: iqomah - lewat };
                if (lewat < sholatSelesai) return { mode: 'SHOLAT' };
            }
            return { mode: 'NORMAL' };
        }

        const elOverlay   = document.getElementById('prayer-overlay');
        const elAdzan     = document.getElementById('overlay-adzan');
        const elSholat    = document.getElementById('overlay-sholat');
        const elCountWrap = document.getElementById('overlay-countdown-wrap');
        let modeKini = null;

        function tampil(el, tampilkan) {
            el.classList.toggle('hidden', !tampilkan);
            el.classList.toggle('flex', tampilkan);
        }

        function terapkanKeadaan() {
            const now = new Date();
            const s = cariKeadaan(now);

            if (s.mode === 'NORMAL') {
                tampil(elOverlay, false);
                if (modeKini !== 'NORMAL' && swiper.autoplay) swiper.autoplay.start();
                modeKini = 'NORMAL';
                return;
            }

            // Slideshow dihentikan selama layar sholat aktif.
            if (modeKini === 'NORMAL' || modeKini === null) {
                if (swiper.autoplay) swiper.autoplay.stop();
            }
            tampil(elOverlay, true);

            if (s.mode === 'SHOLAT') {
                tampil(elAdzan, false);
                tampil(elSholat, true);
                document.getElementById('overlay-dim-clock').textContent =
                    now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false });
            } else {
                tampil(elSholat, false);
                tampil(elAdzan, true);
                document.getElementById('overlay-prayer').textContent = s.nama;
                document.getElementById('overlay-time').textContent = jamBersih(s.jam);

                if (s.mode === 'IQOMAH' || s.mode === 'PRA_ADZAN') {
                    const menujuAdzan = (s.mode === 'PRA_ADZAN');
                    document.getElementById('overlay-label').textContent =
                        menujuAdzan ? 'Menjelang Adzan' : 'Menunggu Iqomah';
                    document.getElementById('overlay-note').textContent =
                        menujuAdzan ? 'Bersiap menyambut waktu sholat' : 'Rapatkan dan luruskan shaf';
                    document.getElementById('overlay-countdown-label').textContent =
                        menujuAdzan ? 'Adzan Dalam' : 'Iqomah Dalam';
                    tampil(elCountWrap, true);
                    const mnt = String(Math.floor(s.sisa / 60)).padStart(2, '0');
                    const dtk = String(Math.floor(s.sisa % 60)).padStart(2, '0');
                    document.getElementById('overlay-countdown').textContent = mnt + ':' + dtk;
                } else {
                    document.getElementById('overlay-label').textContent = 'Waktu Sholat';
                    document.getElementById('overlay-note').textContent = 'Marilah menunaikan sholat berjamaah';
                    tampil(elCountWrap, false);
                }
            }
            modeKini = s.mode;
        }

        setInterval(() => { terapkanKeadaan(); updateNextPrayer(); }, 1000);
        terapkanKeadaan();
        updateNextPrayer();
    </script>
</body>
</html>
