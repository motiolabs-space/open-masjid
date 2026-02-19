<?= $this->extend('layout/landing') ?>

<?= $this->section('content') ?>

<section class="relative overflow-hidden pt-16 pb-24 px-6">
    <div class="max-w-[1200px] mx-auto grid lg:grid-cols-2 gap-12 items-center">
        <div class="flex flex-col gap-8">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 border border-primary/20 text-primary text-xs font-bold uppercase tracking-wider w-fit">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                </span>
                SaaS Khusus Pengurus Masjid
            </div>
            <h1 class="text-5xl md:text-6xl font-black leading-[1.1] tracking-tight text-[#111815] dark:text-white">
                Manajemen Masjid <span class="text-primary">Modern</span>, Tanpa Biaya
            </h1>
            <p class="text-lg text-[#3d5a4d] dark:text-gray-400 max-w-[540px] leading-relaxed">
                Optimalkan pengelolaan program, transparansi keuangan, dan pendataan umat dalam satu platform yang amanah dan gratis selamanya.
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="<?= base_url('register') ?>" class="btn-primary-lg">
                    Mulai Sekarang
                </a>
                <a href="#" class="flex items-center gap-2 px-8 py-4 rounded-xl text-base font-bold border border-[#dbe6e1] dark:border-[#1e3a2f] hover:bg-white dark:hover:bg-gray-800 transition-colors">
                    <span class="material-symbols-outlined text-primary">play_circle</span>
                    Lihat Demo
                </a>
            </div>
        </div>
        <div class="relative group">
            <div class="absolute -inset-4 bg-primary/10 rounded-[2rem] blur-2xl group-hover:bg-primary/20 transition-colors"></div>
            <div class="relative bg-white dark:bg-[#1a2e25] border border-[#dbe6e1] dark:border-[#1e3a2f] rounded-2xl shadow-2xl overflow-hidden aspect-[4/3] flex items-center justify-center p-8">
                <div class="w-full h-full rounded-xl bg-center bg-cover border border-[#dbe6e1] dark:border-[#1e3a2f]" style="background-image: url('<?= base_url('images/masjid.png') ?>');">
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Helper to format numbers compactly (e.g., 1.5k, 1M)
function formatCompact($n) {
    if ($n < 1000) return $n;
    $suffix = '';
    if ($n < 1000000) {
        $n = $n / 1000;
        $suffix = 'rb+'; // Ribu
    } else if ($n < 1000000000) {
        $n = $n / 1000000;
        $suffix = 'Jt+'; // Juta
    } else {
        $n = $n / 1000000000;
        $suffix = 'M+'; // Miliar
    }
    return round($n, 1) . $suffix;
}

// Ensure stats exist
$stats = $stats ?? ['masjid' => 0, 'dana' => 0, 'jamaah' => 0];
?>

<section class="py-12 px-6 border-y border-[#dbe6e1] dark:border-[#1e3a2f] bg-white dark:bg-background-dark/50" id="statistik">
    <div class="max-w-[1200px] mx-auto flex flex-wrap justify-center gap-12 md:gap-24">
        <div class="flex flex-col items-center gap-1">
            <span class="text-4xl font-black text-[#111815] dark:text-white"><?= formatCompact($stats['masjid']) ?></span>
            <span class="text-sm font-medium text-primary uppercase tracking-widest">Masjid Terdaftar</span>
        </div>
        <div class="flex flex-col items-center gap-1">
            <span class="text-4xl font-black text-[#111815] dark:text-white">Rp <?= formatCompact($stats['dana']) ?></span>
            <span class="text-sm font-medium text-primary uppercase tracking-widest">Dana Terkelola</span>
        </div>
        <div class="flex flex-col items-center gap-1">
            <span class="text-4xl font-black text-[#111815] dark:text-white"><?= formatCompact($stats['jamaah']) ?></span>
            <span class="text-sm font-medium text-primary uppercase tracking-widest">Jamaah Terdata</span>
        </div>
        <div class="flex flex-col items-center gap-1">
            <span class="text-4xl font-black text-[#111815] dark:text-white">24/7</span>
            <span class="text-sm font-medium text-primary uppercase tracking-widest">Akses Amanah</span>
        </div>
    </div>
</section>

<section class="py-24 px-6 bg-background-light dark:bg-background-dark" id="fitur">
    <div class="max-w-[1200px] mx-auto">
        <div class="flex flex-col gap-4 mb-16 max-w-[720px]">
            <h2 class="text-primary font-bold uppercase tracking-widest text-sm">Fitur Unggulan</h2>
            <h3 class="text-4xl font-black tracking-tight text-[#111815] dark:text-white">
                Transparansi & Pemberdayaan dalam Satu Genggaman
            </h3>
            <p class="text-lg text-[#3d5a4d] dark:text-gray-400">
                Kami menyediakan ekosistem digital yang dirancang khusus untuk memenuhi kebutuhan administrasi masjid kontemporer.
            </p>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="group bg-white dark:bg-[#1a2e25] p-8 rounded-2xl border border-[#dbe6e1] dark:border-[#1e3a2f] hover:border-primary transition-all shadow-sm hover:shadow-xl">
                <div class="size-14 bg-primary rounded-xl flex items-center justify-center text-white mb-6 group-hover:scale-110 transition-transform shadow-md shadow-primary/10">
                    <span class="material-symbols-outlined text-3xl">insights</span>
                </div>
                <h4 class="text-xl font-bold mb-3 text-[#111815] dark:text-white">Transparansi Keuangan</h4>
                <p class="text-[#3d5a4d] dark:text-gray-400 leading-relaxed mb-6">
                    Laporan otomatis dan visualisasi dana infaq, sedekah, dan wakaf secara real-time yang dapat diakses oleh jamaah.
                </p>
                <div class="w-full h-24 bg-background-light dark:bg-background-dark rounded-lg flex items-end gap-1 p-3">
                    <div class="w-full bg-primary/20 h-1/2 rounded-sm"></div>
                    <div class="w-full bg-primary/40 h-3/4 rounded-sm"></div>
                    <div class="w-full bg-primary h-full rounded-sm"></div>
                    <div class="w-full bg-primary/60 h-2/3 rounded-sm"></div>
                    <div class="w-full bg-primary/30 h-1/2 rounded-sm"></div>
                </div>
            </div>
            <div class="group bg-white dark:bg-[#1a2e25] p-8 rounded-2xl border border-[#dbe6e1] dark:border-[#1e3a2f] hover:border-primary transition-all shadow-sm hover:shadow-xl">
                <div class="size-14 bg-primary rounded-xl flex items-center justify-center text-white mb-6 group-hover:scale-110 transition-transform shadow-md shadow-primary/10">
                    <span class="material-symbols-outlined text-3xl">groups</span>
                </div>
                <h4 class="text-xl font-bold mb-3 text-[#111815] dark:text-white">Pemberdayaan Umat</h4>
                <p class="text-[#3d5a4d] dark:text-gray-400 leading-relaxed mb-6">
                    Database jamaah terintegrasi untuk distribusi zakat dan pemetaan bantuan sosial yang tepat sasaran.
                </p>
                <div class="flex -space-x-3">
                    <div class="size-10 rounded-full border-2 border-white dark:border-[#1a2e25] bg-gray-200" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCD09EHpsLPG1Ws8UzAQXYQBnWXiOKRMTosOz3R9tQfb3ZsgaQ1Svwhn9OmkjqzCy_eSAVhx2RwztvzJYxeH0iEJLrtY-J_u4pWt2eDi3B9v8PG7465txZv3yfp3Brtc0rN3D1MBPj0h1VM0fBjs9ypffDw_jOxW24n4XpARhs8kvtWhlGdwkIB_e1MuB98eTnQ161cTJP2YESwQj4BGYKl99DtaeTWKjcdRDNdNIuew4pzCphiUZ270ecR6FAnuAQKzGMmi3KDYztf')"></div>
                    <div class="size-10 rounded-full border-2 border-white dark:border-[#1a2e25] bg-gray-300" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCH6rPE_09TLBALsaxYwC-HYCb0gcQ5pL30SIbC5NI5brKt_v0dNXmcxxDfBGu_Wp3QJEBcjauKQTDR9sTrzpEMsgzpnPFf3mcjXLe_5k6tUZ_6pdDqpWZtq5NGsBblfmvn7MtooixCULuSg_Zq862nDNhPGgVt2eZ2L_2XAvFmWtSEVkc-Uec7aXoI3L0AJmS33p199NPfzKQj2bikTxEOysH9iUj78mJQCpjUaMVVI_xQxkBuMO5gZ9EZsFlTHuYlVjY2ZiVQ3t_c')"></div>
                    <div class="size-10 rounded-full border-2 border-white dark:border-[#1a2e25] bg-gray-400" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDdz69YOxMsodAPleTwSbVGshagEYWfMOz7RDNFAFKDb2IgtgjJ-Oq6TzYHwWbDGyl2z1DjSx8hKDNCDUSe_r-q16zciNQuKeX5T00nOBsTgpdBbG3-ThAG8f_zh-QiuJIhjh9KmheDwzlrryJQPSQ-VqIbioxeNeaeMesGgF9w8lgDmcOkhTbuIgYqhMpNLgCP-1B5MI1Bzh7Fys77FA8wHD1b8wZSIEIjeXho3oWR4seE4wMp3LxoW5ccCjaHReHCWWfPZqRyk3Cn')"></div>
                    <div class="size-10 rounded-full border-2 border-white dark:border-[#1a2e25] bg-primary flex items-center justify-center text-[10px] font-bold text-white">+1.2k</div>
                </div>
            </div>
            <div class="group bg-white dark:bg-[#1a2e25] p-8 rounded-2xl border border-[#dbe6e1] dark:border-[#1e3a2f] hover:border-primary transition-all shadow-sm hover:shadow-xl">
                <div class="size-14 bg-primary rounded-xl flex items-center justify-center text-white mb-6 group-hover:scale-110 transition-transform shadow-md shadow-primary/10">
                    <span class="material-symbols-outlined text-3xl">event_available</span>
                </div>
                <h4 class="text-xl font-bold mb-3 text-[#111815] dark:text-white">Kelola Program</h4>
                <p class="text-[#3d5a4d] dark:text-gray-400 leading-relaxed mb-6">
                    Penjadwalan Ta'lim, petugas Shalat Jumat, dan papan pengumuman digital dalam satu dashboard terpadu.
                </p>
                <div class="flex flex-col gap-2">
                    <div class="h-4 w-full bg-background-light dark:bg-background-dark rounded animate-pulse"></div>
                    <div class="h-4 w-2/3 bg-background-light dark:bg-background-dark rounded animate-pulse"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-24 px-6 bg-white dark:bg-background-dark/30">
    <div class="max-w-[1200px] mx-auto flex flex-col lg:flex-row items-center gap-16">
        <div class="flex-1 order-2 lg:order-1">
            <div class="bg-background-light dark:bg-[#0d1b15] p-2 rounded-2xl border border-[#dbe6e1] dark:border-[#1e3a2f] shadow-2xl">
                <div class="bg-white dark:bg-[#1a2e25] rounded-xl overflow-hidden">
                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800 flex items-center gap-4 border-b border-[#dbe6e1] dark:border-[#1e3a2f]">
                        <div class="flex gap-1.5">
                            <div class="size-3 rounded-full bg-red-400"></div>
                            <div class="size-3 rounded-full bg-yellow-400"></div>
                            <div class="size-3 rounded-full bg-emerald-600"></div>
                        </div>
                        <div class="flex-1 bg-white dark:bg-gray-900 rounded-md px-3 py-1 flex items-center gap-2 border border-[#dbe6e1] dark:border-gray-700">
                            <span class="material-symbols-outlined text-sm text-gray-400">lock</span>
                            <span class="text-xs font-medium text-gray-500">masj.id/</span>
                            <span class="text-xs font-bold text-primary">baitul-hikmah</span>
                        </div>
                    </div>
                    <div class="p-8 h-64 bg-center bg-cover flex flex-col justify-end" style='background-image: linear-gradient(to bottom, transparent, rgba(0,0,0,0.8)), url("https://lh3.googleusercontent.com/aida-public/AB6AXuD8UH2p3M-vnDzDyWjSGIEp7ZEwCDrH7PpHpVrq59mY6xiQqx8-8Enf1PGPtx1_d0miE9IlzAv_4rn_7KuTQBP8Leu_iI525sBG97w1Kg-4ysb9-fr9uSnADZwjYjxSSvDmWs_4BoxCP-JvVcTSWKvgLYXrlE8blhFV3qPV26X1ndabvnjw4cl1Z1EQFo5F-TrPZQFiGpVzNzVdXqBW8UGB-h0rrWc-GiFcjmI3FaGexzcAelX0mJdYjTnMUEySyyWPzIGbL6aSTB51");'>
                        <h5 class="text-2xl font-bold text-white">Masjid Baitul Hikmah</h5>
                        <p class="text-sm text-gray-300">Jl. Menteng Raya No. 1, Jakarta</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex-1 order-1 lg:order-2">
            <h2 class="text-3xl font-black mb-6 text-[#111815] dark:text-white leading-tight">Bangun Profil Digital Masjid Anda</h2>
            <p class="text-lg text-[#3d5a4d] dark:text-gray-400 mb-8 leading-relaxed">
                Dapatkan alamat URL khusus untuk masjid Anda. Berikan kemudahan bagi jamaah untuk melihat jadwal kajian, laporan keuangan, dan berdonasi secara online melalui satu tautan resmi.
            </p>
            <ul class="flex flex-col gap-4">
                <li class="flex items-center gap-3 font-semibold text-[#111815] dark:text-gray-200">
                    <span class="material-symbols-outlined text-primary font-bold">check_circle</span>
                    QR Code otomatis untuk Infaq
                </li>
                <li class="flex items-center gap-3 font-semibold text-[#111815] dark:text-gray-200">
                    <span class="material-symbols-outlined text-primary font-bold">check_circle</span>
                    Papan pengumuman kegiatan real-time
                </li>
                <li class="flex items-center gap-3 font-semibold text-[#111815] dark:text-gray-200">
                    <span class="material-symbols-outlined text-primary font-bold">check_circle</span>
                    Laporan keuangan bulanan publik
                </li>
            </ul>
        </div>
    </div>
</section>

<section class="py-24 px-6">
    <div class="max-w-[1000px] mx-auto bg-[#0a1f18] dark:bg-[#11241d] rounded-[2.5rem] p-12 md:p-20 text-center relative overflow-hidden shadow-2xl">
        <div class="absolute top-0 right-0 w-64 h-64 bg-primary/20 blur-[100px] -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-primary/10 blur-[100px] translate-y-1/2 -translate-x-1/2"></div>
        <div class="relative z-10 flex flex-col items-center gap-8">
            <h2 class="text-4xl md:text-5xl font-black text-white leading-tight">
                Siap Memodernisasi <br/> Manajemen Masjid Anda?
            </h2>
            <p class="text-lg text-emerald-100/70 max-w-[600px]">
                Gabung bersama ratusan masjid lainnya. Proses pendaftaran hanya butuh 5 menit dan gratis selamanya.
            </p>
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="<?= base_url('register') ?>" class="bg-white text-primary px-10 py-4 rounded-xl text-lg font-bold hover:bg-emerald-50 transition-all shadow-xl">
                    Daftar Sekarang
                </a>
                <a href="#" class="bg-white/10 text-white border border-white/20 hover:bg-white/20 px-10 py-4 rounded-xl text-lg font-bold transition-all">
                    Hubungi Tim Support
                </a>
            </div>
            <p class="text-sm text-emerald-200/50 italic">"Sebaik-baik manusia adalah yang paling bermanfaat bagi manusia lainnya."</p>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
