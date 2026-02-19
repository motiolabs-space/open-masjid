<?= $this->extend('layout/landing') ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="relative overflow-hidden pt-16 pb-24 px-6">
    <div class="max-w-[1200px] mx-auto grid lg:grid-cols-2 gap-12 items-center">
        <div class="flex flex-col gap-8">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 border border-primary/20 text-primary text-xs font-bold uppercase tracking-wider w-fit">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                </span>
                Pusat Kebaikan Masyarakat
            </div>
            <h1 class="text-5xl md:text-6xl font-black leading-[1.1] tracking-tight text-[#111815] dark:text-white">
                Jadikan Masjid Jantung <span class="text-primary">Kebaikan</span> di Tengah Masyarakat
            </h1>
            <p class="text-lg text-[#3d5a4d] dark:text-gray-400 max-w-[540px] leading-relaxed">
                Masj.id hadir untuk membantu pengurus masjid mengelola amanah dengan transparan, membangun kepercayaan jamaah, dan menggerakkan perubahan nyata bagi warga di sekitarnya.
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="<?= base_url('register') ?>" class="btn-primary-lg">
                    Mulai Alirkan Kebaikan
                </a>
                <a href="#masalah" class="flex items-center gap-2 px-8 py-4 rounded-xl text-base font-bold border border-[#dbe6e1] dark:border-[#1e3a2f] hover:bg-white dark:hover:bg-gray-800 transition-colors">
                    Lihat Dampak Sosial
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

<!-- Problem Section -->
<section class="py-24 px-6 bg-white dark:bg-background-dark/30" id="masalah">
    <div class="max-w-[800px] mx-auto text-center">
        <h2 class="text-primary font-bold uppercase tracking-widest text-sm mb-4">Penderitaan Nyata di Sekitar Kita</h2>
        <h3 class="text-3xl md:text-4xl font-black mb-8 text-[#111815] dark:text-white leading-tight">
            Pernahkah kita menyadari, di gang sempit dekat masjid kita, mungkin ada seorang ibu yang kesulitan menyiapkan makan malam?
        </h3>
        <p class="text-lg text-[#3d5a4d] dark:text-gray-400 leading-relaxed mb-10">
            Niat baik seringkali terhambat karena bantuan yang tidak terkoordinasi, dana umat yang kurang transparan, dan program sosial yang hanya berjalan sekali lalu hilang. Ini bukan hanya soal sistem yang kurang rapi, ini soal amanah yang belum tertunaikan sepenuhnya.
        </p>
        <div class="grid sm:grid-cols-2 gap-6 text-left">
            <div class="p-6 bg-background-light dark:bg-[#1a2e25] rounded-2xl border border-[#dbe6e1] dark:border-[#1e3a2f]">
                <span class="material-symbols-outlined text-red-500 mb-3">volunteer_activism</span>
                <p class="font-bold text-[#111815] dark:text-white mb-2">Bantuan Tidak Terkoordinasi</p>
                <p class="text-sm text-gray-500">Sulit memastikan bantuan sampai ke tangan yang paling membutuhkan tanpa data yang akurat.</p>
            </div>
            <div class="p-6 bg-background-light dark:bg-[#1a2e25] rounded-2xl border border-[#dbe6e1] dark:border-[#1e3a2f]">
                <span class="material-symbols-outlined text-amber-500 mb-3">visibility_off</span>
                <p class="font-bold text-[#111815] dark:text-white mb-2">Kurang Transparan</p>
                <p class="text-sm text-gray-500">Jamaah ragu untuk berbagi lebih banyak jika aliran dana tidak terlihat jelas manfaatnya.</p>
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

<!-- Solution Section -->
<section class="py-24 px-6 bg-background-light dark:bg-background-dark">
    <div class="max-w-[1200px] mx-auto">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div class="relative">
                <div class="aspect-video bg-primary/20 rounded-3xl overflow-hidden border border-primary/10 shadow-2xl relative group">
                    <img alt="Community gathering" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCH6rPE_09TLBALsaxYwC-HYCb0gcQ5pL30SIbC5NI5brKt_v0dNXmcxxDfBGu_Wp3QJEBcjauKQTDR9sTrzpEMsgzpnPFf3mcjXLe_5k6tUZ_6pdDqpWZtq5NGsBblfmvn7MtooixCULuSg_Zq862nDNhPGgVt2eZ2L_2XAvFmWtSEVkc-Uec7aXoI3L0AJmS33p199NPfzKQj2bikTxEOysH9iUj78mJQCpjUaMVVI_xQxkBuMO5gZ9EZsFlTHuYlVjY2ZiVQ3t_c"/>
                    <div class="absolute inset-0 bg-primary/20 mix-blend-overlay"></div>
                </div>
            </div>
            <div class="flex flex-col gap-6">
                <h2 class="text-primary font-bold uppercase tracking-widest text-sm">Masj.id: Penggerak Rantai Kebaikan</h2>
                <h3 class="text-3xl font-black text-[#111815] dark:text-white leading-tight">
                    Masj.id bukan sekadar aplikasi, melainkan jembatan yang menghubungkan niat mulia dengan kebutuhan nyata.
                </h3>
                <p class="text-lg text-[#3d5a4d] dark:text-gray-400 leading-relaxed">
                    Kami percaya pada kekuatan kejujuran. Ketika masjid transparan, jamaah akan lebih percaya. Ketika kepercayaan tumbuh, bantuan sosial akan mengalir lebih deras untuk mereka yang benar-benar membutuhkan.
                </p>
                <div class="space-y-4 py-4">
                    <div class="flex items-center gap-4">
                        <div class="size-10 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 flex items-center justify-center font-bold">1</div>
                        <p class="font-bold text-[#111815] dark:text-white">Bangun Kepercayaan Jamaah</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="size-10 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 flex items-center justify-center font-bold">2</div>
                        <p class="font-bold text-[#111815] dark:text-white">Alirkan Bantuan Tepat Sasaran</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="size-10 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 flex items-center justify-center font-bold">3</div>
                        <p class="font-bold text-[#111815] dark:text-white">Ciptakan Dampak Berkelanjutan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-24 px-6 bg-white dark:bg-[#1a1d21]" id="fitur">
    <div class="max-w-[1200px] mx-auto">
        <div class="flex flex-col gap-4 mb-16 max-w-[720px]">
            <h2 class="text-primary font-bold uppercase tracking-widest text-sm">Alat Pemantik Kebaikan</h2>
            <h3 class="text-4xl font-black tracking-tight text-[#111815] dark:text-white">
                Teknologi Hanya Sarana, Dampak Bagi Umat Adalah Tujuan
            </h3>
            <p class="text-lg text-[#3d5a4d] dark:text-gray-400">
                Kami merancang setiap fitur untuk memudahkan masjid menjalankan peran sosialnya dengan lebih amanah.
            </p>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="group bg-background-light dark:bg-[#1a2e25] p-8 rounded-2xl border border-[#dbe6e1] dark:border-[#1e3a2f] hover:border-primary transition-all shadow-sm hover:shadow-xl">
                <div class="size-14 bg-primary/10 rounded-xl flex items-center justify-center text-primary mb-6 group-hover:bg-primary group-hover:text-white transition-all shadow-md">
                    <span class="material-symbols-outlined text-3xl">verified_user</span>
                </div>
                <h4 class="text-xl font-bold mb-3 text-[#111815] dark:text-white">Menjaga Amanah dengan Transparansi Dana</h4>
                <p class="text-[#3d5a4d] dark:text-gray-400 leading-relaxed text-sm">
                    Laporan keuangan terbuka yang bisa diakses siapa saja. Menghapus keraguan jamaah dan menumbuhkan semangat untuk berbagi lebih banyak.
                </p>
            </div>
            <div class="group bg-background-light dark:bg-[#1a2e25] p-8 rounded-2xl border border-[#dbe6e1] dark:border-[#1e3a2f] hover:border-primary transition-all shadow-sm hover:shadow-xl">
                <div class="size-14 bg-primary/10 rounded-xl flex items-center justify-center text-primary mb-6 group-hover:bg-primary group-hover:text-white transition-all shadow-md">
                    <span class="material-symbols-outlined text-3xl">volunteer_activism</span>
                </div>
                <h4 class="text-xl font-bold mb-3 text-[#111815] dark:text-white">Gerakan Kebaikan Terukur Melalui Program Sosial</h4>
                <p class="text-[#3d5a4d] dark:text-gray-400 leading-relaxed text-sm">
                    Penataan agenda dakwah dan sosial yang rapi. Memastikan kehadiran masjid dirasakan manfaatnya secara berkelanjutan oleh warga.
                </p>
            </div>
            <div class="group bg-background-light dark:bg-[#1a2e25] p-8 rounded-2xl border border-[#dbe6e1] dark:border-[#1e3a2f] hover:border-primary transition-all shadow-sm hover:shadow-xl">
                <div class="size-14 bg-primary/10 rounded-xl flex items-center justify-center text-primary mb-6 group-hover:bg-primary group-hover:text-white transition-all shadow-md">
                    <span class="material-symbols-outlined text-3xl">groups_3</span>
                </div>
                <h4 class="text-xl font-bold mb-3 text-[#111815] dark:text-white">Mengenal Lebih Dekat Kondisi Umat</h4>
                <p class="text-[#3d5a4d] dark:text-gray-400 leading-relaxed text-sm">
                    Pendataan kondisi ekonomi warga sekitar dengan akurat. Keadilan sosial bagi dhuafa agar bantuan jatuh ke tangan yang paling tepat.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Social Impact Section -->
<section class="py-24 px-6 bg-background-light dark:bg-background-dark">
    <div class="max-w-[1200px] mx-auto">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-black mb-4 text-[#111815] dark:text-white">Dampak Nyata untuk Masyarakat Sekitar</h2>
            <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">
                Perubahan yang kita ciptakan bukan sekadar angka di layar, melainkan kehidupan yang lebih baik bagi warga.
            </p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="p-8 bg-white dark:bg-[#1a2e25] rounded-3xl border border-[#dbe6e1] dark:border-[#1e3a2f] shadow-sm">
                <span class="material-symbols-outlined text-4xl text-primary mb-6">local_shippping</span>
                <h4 class="text-xl font-bold mb-3 text-[#111815] dark:text-white">Sembako Tepat Sasaran</h4>
                <p class="text-sm text-gray-500 leading-relaxed">Memastikan tidak ada lagi janda dan yatim di sekitar masjid yang kelaparan karena bantuan tersampaikan dengan data akurat.</p>
            </div>
            <div class="p-8 bg-white dark:bg-[#1a2e25] rounded-3xl border border-[#dbe6e1] dark:border-[#1e3a2f] shadow-sm">
                <span class="material-symbols-outlined text-4xl text-primary mb-6">storefront</span>
                <h4 class="text-xl font-bold mb-3 text-[#111815] dark:text-white">Sangga Usaha Kecil</h4>
                <p class="text-sm text-gray-500 leading-relaxed">Memberikan modal dan dukungan bagi pedagang kecil di lingkungan masjid agar mandiri secara ekonomi.</p>
            </div>
            <div class="p-8 bg-white dark:bg-[#1a2e25] rounded-3xl border border-[#dbe6e1] dark:border-[#1e3a2f] shadow-sm">
                <span class="material-symbols-outlined text-4xl text-primary mb-6">school</span>
                <h4 class="text-xl font-bold mb-3 text-[#111815] dark:text-white">Beasiswa Pendidikan</h4>
                <p class="text-sm text-gray-500 leading-relaxed">Menjamin anak-anak kurang mampu di sekitar masjid tetap bisa bersekolah dan meraih cita-cita mereka.</p>
            </div>
        </div>
    </div>
</section>

<!-- Story Section -->
<section class="py-24 px-6 bg-white dark:bg-background-dark/30">
    <div class="max-w-[1000px] mx-auto bg-primary/5 dark:bg-primary/5 rounded-[3rem] p-12 md:p-20 border border-primary/10">
        <div class="flex flex-col items-center text-center gap-8">
            <div class="size-20 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                <span class="material-symbols-outlined text-4xl">auto_stories</span>
            </div>
            <h3 class="text-2xl md:text-3xl font-black text-[#111815] dark:text-white italic leading-tight">
                "Sekarang tidak ada lagi warga yang tidur dalam keadaan lapar dalam radius 1 kilometer dari masjid kami..."
            </h3>
            <div class="flex flex-col gap-2">
                <p class="text-lg text-[#3d5a4d] dark:text-gray-400 leading-relaxed">
                    Di sebuah masjid pemukiman padat, dulu pengurus kesulitan menyalurkan bantuan karena data yang tumpang tindih. Setelah mulai terbuka dan tertata, jamaah semakin percaya. Kini, tiga pemuda yang tadinya menganggur telah memulai usaha kecil berkat dukungan modal dari masjid.
                </p>
                <p class="text-sm font-bold text-primary uppercase tracking-widest mt-4">Cerita Perubahan Nyata</p>
            </div>
        </div>
    </div>
</section>

<!-- Community & Trust -->
<section class="py-24 px-6 bg-white dark:bg-[#1a1d21]">
    <div class="max-w-[1200px] mx-auto grid lg:grid-cols-2 gap-16 items-center">
        <div>
            <h2 class="text-3xl font-black mb-6 text-[#111815] dark:text-white leading-tight">Membangun Ekosistem Kepercayaan</h2>
            <p class="text-lg text-[#3d5a4d] dark:text-gray-400 mb-8 leading-relaxed">
                Bagi kami, kepercayaan adalah amanah tertinggi. Masj.id dirancang untuk merawat kepercayaan itu melalui keterbukaan, sehingga menggerakkan kembali semangat gotong royong jamaah dan generasi muda.
            </p>
            <div class="grid grid-cols-2 gap-6">
                <div class="flex flex-col gap-2">
                    <span class="text-2xl font-black text-primary">High Trust</span>
                    <span class="text-xs font-bold text-gray-500 uppercase">Kepercayaan Jamaah</span>
                </div>
                <div class="flex flex-col gap-2">
                    <span class="text-2xl font-black text-primary">Real Action</span>
                    <span class="text-xs font-bold text-gray-500 uppercase">Kolaborasi Relawan</span>
                </div>
            </div>
        </div>
        <div class="bg-background-light dark:bg-[#1a2e25] p-2 rounded-3xl border border-[#dbe6e1] dark:border-[#1e3a2f] shadow-2xl">
            <img alt="Community interaction" class="rounded-2xl" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAGipiyg48agSRjm2VZqV0Jz_RgaKsdz8xGwWaWGOvuTQcwyeuF7uYCIA1buc2YkHwDgoitWPFGTHw795Diik7_IE9afPqHFpppJDgjpjuZnP9b8vliWlYm8KYK4GL8MRHRKz7zArIniCG3uyoDSW1tQKAeBOC5aKM5L_QXrQzPspDBeWZePlF5tiXkG2cEbxEq8nizgLHLshGJ-WhK4o7O85iNNkPwC0k7B3lLRGrZu1I_J0YAZ9ZyctT7dOm6b_3xfY3cQxBRPYlD"/>
        </div>
    </div>
</section>

<!-- Final CTA Section -->
<section class="py-24 px-6">
    <div class="max-w-[1000px] mx-auto bg-[#0a1f18] dark:bg-[#11241d] rounded-[2.5rem] p-12 md:p-20 text-center relative overflow-hidden shadow-2xl">
        <div class="absolute top-0 right-0 w-64 h-64 bg-primary/20 blur-[100px] -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-primary/10 blur-[100px] translate-y-1/2 -translate-x-1/2"></div>
        <div class="relative z-10 flex flex-col items-center gap-8">
            <h2 class="text-4xl md:text-5xl font-black text-white leading-tight">
                Siap Memulai Perubahan <br/> Melalui Masjid Anda?
            </h2>
            <p class="text-lg text-emerald-100/70 max-w-[600px]">
                Mari buat perubahan nyata, mulai dari gang-gang di sekitar masjid kita. Gabung bersama ratusan komunitas masjid lainnya hari ini.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="<?= base_url('register') ?>" class="bg-white text-primary px-8 py-4 rounded-xl text-base font-black hover:bg-emerald-50 transition-all shadow-xl">
                    Daftarkan Masjid Kami
                </a>
                <a href="#" class="bg-white/10 text-white border border-white/20 hover:bg-white/20 px-8 py-4 rounded-xl text-base font-black transition-all">
                    Jadi Relawan Kebaikan
                </a>
            </div>
            <p class="text-sm text-emerald-200/50 italic font-medium">"Gratis, amanah, dan untuk kebaikan bersama."</p>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
