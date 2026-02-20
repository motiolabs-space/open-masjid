<?= $this->extend('layout/landing') ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="relative pt-24 pb-16 px-6 bg-white dark:bg-background-dark overflow-hidden text-center">
    <div class="max-w-[1200px] mx-auto relative z-10">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary/10 border border-primary/20 text-primary text-xs font-bold uppercase tracking-wider mb-8">
            Pondasi Gerakan Kebaikan
        </div>
        <h1 class="text-4xl md:text-7xl font-black tracking-tight mb-8 leading-tight text-gray-900 dark:text-white">
            Perjalanan Khidmah untuk <br/> <span class="text-primary italic">Kemandirian Umat</span>
        </h1>
        <p class="text-xl text-[#3d5a4d] dark:text-gray-400 max-w-[800px] mx-auto mb-10 leading-relaxed">
            Mengenal lebih dekat niat dan landasan di balik gerakan Masj.id.
        </p>
    </div>
</section>

<!-- Why Masj.id Born Section -->
<section class="py-24 px-6 bg-background-light dark:bg-background-dark/30">
    <div class="max-w-[800px] mx-auto text-center">
        <h2 class="text-primary font-bold uppercase tracking-widest text-sm mb-8">Latar Belakang</h2>
        <div class="space-y-8 text-lg text-[#3d5a4d] dark:text-gray-400 leading-relaxed">
            <p>
                Masj.id lahir dari keprihatinan sekaligus harapan besar. Kami melihat banyak masjid memiliki potensi luar biasa untuk menjadi solusi sosial, namun masih memerlukan dukungan untuk bisa bergerak lebih efektif dalam membantu persoalan nyata di sekitarnya.
            </p>
            <p>
                Kami percaya bahwa masjid bukan sekadar bangunan untuk beribadah ritual, melainkan episentrum keadilan sosial yang harus terus dibantu tata kelolanya agar manfaatnya meluas tanpa henti.
            </p>
        </div>
    </div>
</section>

<!-- Vision & Mission Section -->
<section class="py-24 px-6 bg-white dark:bg-background-dark">
    <div class="max-w-[1000px] mx-auto grid md:grid-cols-2 gap-16">
        <div class="flex flex-col gap-6">
            <h2 class="text-3xl font-black text-gray-900 dark:text-white italic">Visi Kami</h2>
            <p class="text-lg text-[#3d5a4d] dark:text-gray-400 leading-relaxed">
                Menjadikan setiap masjid sebagai pusat kemandirian dan keadilan sosial bagi seluruh masyarakat.
            </p>
        </div>
        <div class="flex flex-col gap-6">
            <h2 class="text-3xl font-black text-gray-900 dark:text-white italic">Misi Kami</h2>
            <p class="text-lg text-[#3d5a4d] dark:text-gray-400 leading-relaxed">
                Kami berkomitmen membangun kejujuran dalam pengelolaan, mempererat ikatan silaturahmi melalui kolaborasi, dan menghadirkan program sosial yang berdampak jangka panjang.
            </p>
        </div>
    </div>
</section>

<!-- Core Values Section -->
<section class="py-24 px-6 bg-background-light dark:bg-background-dark/30">
    <div class="max-w-[900px] mx-auto">
        <h2 class="text-3xl font-black text-center mb-16 text-gray-900 dark:text-white">Nilai Utama</h2>
        <div class="grid md:grid-cols-3 gap-12 text-center">
            <div>
                <div class="size-16 rounded-full bg-primary/10 text-primary flex items-center justify-center mx-auto mb-6">
                    <span class="material-symbols-outlined text-4xl">verified_user</span>
                </div>
                <h4 class="text-xl font-bold mb-3 text-gray-900 dark:text-white">Amanah</h4>
                <p class="text-sm text-gray-500 leading-relaxed">Menjaga kepercayaan sebagai harta yang paling berharga dalam setiap langkah.</p>
            </div>
            <div>
                <div class="size-16 rounded-full bg-primary/10 text-primary flex items-center justify-center mx-auto mb-6">
                    <span class="material-symbols-outlined text-4xl">diversity_1</span>
                </div>
                <h4 class="text-xl font-bold mb-3 text-gray-900 dark:text-white">Gotong Royong</h4>
                <p class="text-sm text-gray-500 leading-relaxed">Bergerak bersama dengan semangat kasih sayang untuk meringankan beban sesama.</p>
            </div>
            <div>
                <div class="size-16 rounded-full bg-primary/10 text-primary flex items-center justify-center mx-auto mb-6">
                    <span class="material-symbols-outlined text-4xl">volunteer_activism</span>
                </div>
                <h4 class="text-xl font-bold mb-3 text-gray-900 dark:text-white">Ketulusan</h4>
                <p class="text-sm text-gray-500 leading-relaxed">Melayani dengan hati tanpa mengharap pamrih duniawi, murni untuk kemaslahatan.</p>
            </div>
        </div>
    </div>
</section>

<!-- Yayasan Section -->
<section class="py-24 px-6 bg-white dark:bg-[#1a2e25] border-y border-primary/10">
    <div class="max-w-[1000px] mx-auto flex flex-col md:flex-row items-center gap-16">
        <div class="size-48 rounded-full bg-primary/5 flex items-center justify-center text-primary border border-primary/10 shrink-0">
             <span class="material-symbols-outlined text-[80px]">shield_with_heart</span>
        </div>
        <div>
            <h2 class="text-3xl font-black mb-6 text-gray-900 dark:text-white leading-tight">Dukungan Yayasan Generasi Sahabat Muslim</h2>
            <p class="text-lg text-[#3d5a4d] dark:text-gray-400 leading-relaxed">
                Gerakan ini sepenuhnya didukung oleh <strong>Yayasan Generasi Sahabat Muslim</strong>. Yayasan ini menjadi pondasi moral dan hukum yang menjamin bahwa setiap gerak Masj.id akan selalu selaras dengan kepentingan pemberdayaan umat dan terjaga keberlanjutannya secara profesional.
            </p>
        </div>
    </div>
</section>

<!-- Ecosystem Section -->
<section class="py-24 px-6 bg-white dark:bg-background-dark text-center">
    <div class="max-w-[1000px] mx-auto">
        <h2 class="text-3xl font-black mb-16 text-gray-900 dark:text-white">Ekosistem Pemberdayaan</h2>
        <p class="text-xl text-[#3d5a4d] dark:text-gray-400 leading-relaxed mb-12">
            Kami bekerja dengan menghubungkan ketulusan <strong>Relawan</strong>, kesetiaan <strong>Jamaah</strong>, dan kemurahan hati <strong>Donatur</strong> dalam satu wadah yang berpusat di institusi <strong>Masjid</strong>.
        </p>
        <div class="flex flex-wrap justify-center gap-6">
            <span class="px-6 py-2 bg-background-light dark:bg-gray-800 rounded-full text-sm font-bold border border-primary/10">Ketulusan Relawan</span>
            <span class="px-6 py-2 bg-background-light dark:bg-gray-800 rounded-full text-sm font-bold border border-primary/10">Kesetiaan Jamaah</span>
            <span class="px-6 py-2 bg-background-light dark:bg-gray-800 rounded-full text-sm font-bold border border-primary/10">Kemurahan Hati Donatur</span>
            <span class="px-6 py-2 bg-primary text-white rounded-full text-sm font-bold">Institusi Masjid</span>
        </div>
    </div>
</section>

<!-- Final Reassurance Section -->
<section class="py-24 px-6 bg-background-light dark:bg-background-dark/30">
    <div class="max-w-[800px] mx-auto text-center">
        <h2 class="text-2xl md:text-3xl font-black mb-8 text-gray-900 dark:text-white italic">"Masjid yang kuat bukan hanya bangunannya. Tetapi dampaknya bagi masyarakat."</h2>
        <p class="text-primary font-black tracking-wide uppercase text-xs mb-12">Didukung Yayasan Generasi Sahabat Muslim. Amanah untuk kebaikan bersama.</p>
        
        <div class="grid md:grid-cols-3 gap-6">
             <a href="https://instagram.com/webmasjid" target="_blank" class="flex flex-col items-center gap-2 p-6 bg-white dark:bg-gray-800 rounded-2xl border border-primary/5 hover:border-primary transition-all">
                 <span class="material-symbols-outlined">photo_camera</span>
                 <span class="text-xs font-bold uppercase">Instagram</span>
             </a>
             <a href="https://www.linkedin.com/company/portal-masjid/" target="_blank" class="flex flex-col items-center gap-2 p-6 bg-white dark:bg-gray-800 rounded-2xl border border-primary/5 hover:border-primary transition-all">
                 <span class="material-symbols-outlined">work</span>
                 <span class="text-xs font-bold uppercase">LinkedIn</span>
             </a>
             <a href="https://t.me/novrand" target="_blank" class="flex flex-col items-center gap-2 p-6 bg-white dark:bg-gray-800 rounded-2xl border border-primary/5 hover:border-primary transition-all">
                 <span class="material-symbols-outlined">send</span>
                 <span class="text-xs font-bold uppercase">Telegram</span>
             </a>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
