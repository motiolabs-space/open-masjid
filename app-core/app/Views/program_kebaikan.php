<?= $this->extend('layout/landing') ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="relative pt-24 pb-16 px-6 bg-white dark:bg-background-dark overflow-hidden text-center">
    <div class="max-w-[1200px] mx-auto relative z-10">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary/10 border border-primary/20 text-primary text-xs font-bold uppercase tracking-wider mb-8">
            Jejak Nyata Kemanusiaan
        </div>
        <h1 class="text-4xl md:text-7xl font-black tracking-tight mb-8 leading-tight text-gray-900 dark:text-white">
            Menapak Jejak Perubahan <br/> <span class="text-primary italic">di Setiap Sudut Gang</span>
        </h1>
        <p class="text-xl text-[#3d5a4d] dark:text-gray-400 max-w-[800px] mx-auto mb-10 leading-relaxed">
            Lihat bagaimana satu niat baik dari masjid berubah menjadi gelombang harapan yang nyata bagi tetangga kita.
        </p>
    </div>
</section>

<!-- Community Condition Section -->
<section class="py-24 px-6 bg-background-light dark:bg-background-dark/30">
    <div class="max-w-[800px] mx-auto text-center">
        <h2 class="text-primary font-bold uppercase tracking-widest text-sm mb-6">Kondisi Nyata di Sekitar Kita</h2>
        <p class="text-xl text-gray-800 dark:text-gray-200 leading-relaxed italic font-medium">
            "Di sekitar kita, masih banyak lansia yang berjuang sendirian dan anak-anak yatim yang mimpinya nyaris padam. Kondisi ini adalah alasan mengapa gerakan masjid harus terus diperkuat."
        </p>
    </div>
</section>

<!-- Impact Programs Section -->
<section class="py-24 px-6 bg-white dark:bg-background-dark">
    <div class="max-w-[1200px] mx-auto">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Program 1 -->
            <div class="p-10 bg-white dark:bg-[#1a2e25] rounded-[2.5rem] border border-primary/5 shadow-sm">
                <h4 class="text-2xl font-black text-primary mb-6 italic leading-tight">Nutrisi Bagi Sesama</h4>
                <p class="text-[#3d5a4d] dark:text-gray-400 leading-relaxed">
                    Berbagi paket pangan berkualitas agar tidak ada lagi orang tua yang bingung memikirkan makan malam keluarganya.
                </p>
            </div>
            <!-- Program 2 -->
            <div class="p-10 bg-white dark:bg-[#1a2e25] rounded-[2.5rem] border border-primary/5 shadow-sm">
                <h4 class="text-2xl font-black text-primary mb-6 italic leading-tight">Beasiswa Harapan</h4>
                <p class="text-[#3d5a4d] dark:text-gray-400 leading-relaxed">
                    Memberikan pendampingan biaya pendidikan untuk memastikan cahaya ilmu tetap menyala bagi anak-anak dhuafa.
                </p>
            </div>
            <!-- Program 3 -->
            <div class="p-10 bg-white dark:bg-[#1a2e25] rounded-[2.5rem] border border-primary/5 shadow-sm">
                <h4 class="text-2xl font-black text-primary mb-6 italic leading-tight">Sahabat UMKM</h4>
                <p class="text-[#3d5a4d] dark:text-gray-400 leading-relaxed">
                    Memberikan bantuan modal dan pelatihan bagi pedagang kecil agar bisa berdaya di atas kaki sendiri.
                </p>
            </div>
            <!-- Program 4 -->
            <div class="p-10 bg-white dark:bg-[#1a2e25] rounded-[2.5rem] border border-primary/5 shadow-sm">
                <h4 class="text-2xl font-black text-primary mb-6 italic leading-tight">Respon Cepat Sosial</h4>
                <p class="text-[#3d5a4d] dark:text-gray-400 leading-relaxed">
                    Layanan bantuan darurat dan kesehatan yang hadir di saat warga berada dalam titik tersulit hidupnya.
                </p>
            </div>
            <!-- Program 5 -->
            <div class="p-10 bg-white dark:bg-[#1a2e25] rounded-[2.5rem] border border-primary/5 shadow-sm">
                <h4 class="text-2xl font-black text-primary mb-6 italic leading-tight">Keluarga Asuh Masjid</h4>
                <p class="text-[#3d5a4d] dark:text-gray-400 leading-relaxed">
                    Pendampingan personal bagi keluarga yang membutuhkan perhatian lebih secara lahir dan batin.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Chain Impact Section -->
<section class="py-24 px-6 bg-background-light dark:bg-background-dark/30">
    <div class="max-w-[1000px] mx-auto text-center">
        <h2 class="text-3xl font-black mb-16 text-gray-900 dark:text-white">Rantai Kebaikan Tanpa Putus</h2>
        <div class="flex flex-col md:flex-row items-center justify-between gap-8 md:gap-4 relative">
             <div class="absolute top-1/2 left-0 right-0 h-0.5 bg-primary/10 -z-10 hidden md:block"></div>
             
             <div class="flex flex-col items-center gap-4 bg-background-light dark:bg-gray-800 p-6 rounded-2xl border border-primary/5 w-full md:w-auto">
                 <span class="text-primary font-bold">Amanah Jamaah</span>
             </div>
             <span class="material-symbols-outlined text-primary/30 rotate-90 md:rotate-0">arrow_forward</span>
             <div class="flex flex-col items-center gap-4 bg-background-light dark:bg-gray-800 p-6 rounded-2xl border border-primary/5 w-full md:w-auto">
                 <span class="text-primary font-bold">Masyarakat Terbantu</span>
             </div>
             <span class="material-symbols-outlined text-primary/30 rotate-90 md:rotate-0">arrow_forward</span>
             <div class="flex flex-col items-center gap-4 bg-background-light dark:bg-gray-800 p-6 rounded-2xl border border-primary/5 w-full md:w-auto">
                 <span class="text-primary font-bold">Kesejahteraan</span>
             </div>
             <span class="material-symbols-outlined text-primary/30 rotate-90 md:rotate-0">arrow_forward</span>
             <div class="flex flex-col items-center gap-4 bg-primary text-white p-6 rounded-2xl shadow-xl w-full md:w-auto">
                 <span class="font-bold">Lingkungan Harmonis</span>
             </div>
        </div>
    </div>
</section>

<!-- Human Story Section -->
<section class="py-24 px-6 bg-white dark:bg-background-dark text-center">
    <div class="max-w-[900px] mx-auto">
        <div class="inline-flex items-center justify-center size-20 rounded-full bg-primary/10 text-primary mb-12">
            <span class="material-symbols-outlined text-4xl">auto_stories</span>
        </div>
        <p class="text-2xl md:text-3xl text-gray-800 dark:text-gray-200 leading-relaxed font-bold italic mb-6">
            "Dulu, masjid di kampung kami hanya terbuka saat waktu shalat. Sekarang, masjid adalah tempat pertama yang kami tuju saat ada warga yang sakit atau anak yang putus sekolah. Perubahan tata kelola ini benar-benar menghidupkan kembali harapan kami."
        </p>
    </div>
</section>

<!-- Final CTA Section -->
<section class="py-24 px-6 bg-background-light dark:bg-background-dark/30">
    <div class="max-w-[1000px] mx-auto text-center">
        <h2 class="text-3xl md:text-5xl font-black mb-12 text-[#111815] dark:text-white leading-tight">
            Mari Perluas Jejak <br/> <span class="text-primary italic">Kebaikan Ini Bersama</span>
        </h2>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="<?= base_url('register') ?>" class="px-10 py-5 bg-primary text-white rounded-2xl font-black text-lg shadow-2xl shadow-primary/20 hover:scale-105 transition-all">
                Daftarkan Masjid Kami
            </a>
            <a href="#" class="px-10 py-5 bg-background-light dark:bg-gray-800 text-gray-700 dark:text-white rounded-2xl font-black border border-[#dce4e1] dark:border-gray-700">
                Jadi Relawan Kebaikan
            </a>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
