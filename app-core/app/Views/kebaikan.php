<?= $this->extend('layout/main') ?>

<?= $this->section('extra_head') ?>
<title>Statistik Dampak &amp; Program Kebaikan - Masj.id</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Hero Section -->
<section class="relative pt-24 pb-16 px-6 bg-white dark:bg-background-dark overflow-hidden">
    <div class="absolute top-0 right-0 w-96 h-96 bg-primary/5 rounded-full translate-x-1/3 -translate-y-1/3 blur-3xl"></div>
    <div class="max-w-[1200px] mx-auto text-center relative z-10">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary/10 border border-primary/20 text-primary text-xs font-bold uppercase tracking-wider mb-6">
            Gerakan Solidaritas Berbasis Masjid
        </div>
        <h1 class="text-4xl md:text-6xl font-black tracking-tight mb-6 leading-tight">
            Dari Masjid, <span class="text-primary">Kebaikan</span> <br/> Mengalir ke Sekitar
        </h1>
        <p class="text-lg text-[#3d5a4d] dark:text-gray-400 max-w-[800px] mx-auto mb-10 leading-relaxed">
            Masjid bukan hanya tempat untuk bersujud, melainkan mata air kepedulian yang mengalirkan bantuan dan harapan bagi warga di sekitarnya. Masj.id hadir untuk memastikan setiap niat baik tersampaikan dengan amanah dan menciptakan perubahan nyata.
        </p>
        <p class="text-primary font-bold italic mb-12">"Mari Menjadi Bagian dari Gerakan Kebaikan"</p>
        <div class="flex justify-center gap-4">
            <a href="#gerakan" class="px-8 py-4 bg-primary text-white rounded-xl font-black shadow-xl shadow-primary/20 hover:scale-105 transition-all">
                Mulai Bergerak
            </a>
        </div>
    </div>
</section>

<!-- Reality Section -->
<section class="py-24 px-6 bg-background-light dark:bg-background-dark/30">
    <div class="max-w-[800px] mx-auto">
        <h2 class="text-primary font-bold uppercase tracking-widest text-sm mb-6 text-center">Kondisi Nyata di Sekitar Kita</h2>
        <div class="space-y-8 text-lg text-[#3d5a4d] dark:text-gray-400 leading-relaxed">
            <p>
                Di balik megahnya menara masjid kita, seringkali tersimpan cerita yang luput dari pandangan. Di gang-gang sempit itu, mungkin ada seorang ibu yang sedang bingung memikirkan makan malam untuk anaknya. Ada lansia yang merindukan sapaan dan perhatian di masa senjanya.
            </p>
            <p>
                Ada anak-anak cerdas yang mimpinya nyaris padam karena kendala biaya sekolah, hingga pedagang kecil yang modal usahanya kian menipis tertelan keadaan. Kondisi ini bukan sekadar angka statistik, melainkan wajah-wajah tetangga kita yang membutuhkan uluran tangan dan kepedulian yang tertata dari rumah Allah.
            </p>
        </div>
    </div>
</section>

<!-- Program Kebaikan Section -->
<section class="py-24 px-6 bg-white dark:bg-background-dark" id="program">
    <div class="max-w-[1200px] mx-auto">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-black mb-4 text-[#111815] dark:text-white">Program Kebaikan dari Rumah Allah</h2>
            <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">
                Melalui manajemen yang tertata, masjid hadir menjawab kebutuhan nyata masyarakat di sekitarnya.
            </p>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Program 1 -->
            <div class="p-8 bg-background-light dark:bg-[#1a2e25] rounded-3xl border border-[#dce4e1] dark:border-[#1e3a2f] shadow-sm flex flex-col gap-6">
                <div class="size-14 rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-3xl">local_mall</span>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-3">Memastikan Tak Ada Lagi Perut yang Lapar</h3>
                    <p class="text-sm text-[#3d5a4d] dark:text-gray-400 leading-relaxed">
                        Banyak warga kesulitan memenuhi kebutuhan pangan harian. Masjid hadir menyalurkan paket sembako rutin bagi dhuafa, membawa ketenangan bagi para orang tua di lingkungan kita.
                    </p>
                </div>
            </div>
            <!-- Program 2 -->
            <div class="p-8 bg-background-light dark:bg-[#1a2e25] rounded-3xl border border-[#dce4e1] dark:border-[#1e3a2f] shadow-sm flex flex-col gap-6">
                <div class="size-14 rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-3xl">school</span>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-3">Menjaga Mimpi Tetap Menyala</h3>
                    <p class="text-sm text-[#3d5a4d] dark:text-gray-400 leading-relaxed">
                        Anak-anak yatim dan dhuafa terancam putus sekolah karena biaya. Masjid hadir sebagai orang tua asuh, menjaga cahaya ilmu tetap terang bagi generasi masa depan umat.
                    </p>
                </div>
            </div>
            <!-- Program 3 -->
            <div class="p-8 bg-background-light dark:bg-[#1a2e25] rounded-3xl border border-[#dce4e1] dark:border-[#1e3a2f] shadow-sm flex flex-col gap-6">
                <div class="size-14 rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-3xl">storefront</span>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-3">Membangkitkan Kemandirian Ekonomi</h3>
                    <p class="text-sm text-[#3d5a4d] dark:text-gray-400 leading-relaxed">
                        Pedagang kecil di gang sekitar sering terjepit modal. Masjid hadir memberikan dukungan usaha dan pendampingan, agar warga berdaya dan mandiri di tanah sendiri.
                    </p>
                </div>
            </div>
            <!-- Program 4 -->
            <div class="p-8 bg-background-light dark:bg-[#1a2e25] rounded-3xl border border-[#dce4e1] dark:border-[#1e3a2f] shadow-sm flex flex-col gap-6">
                <div class="size-14 rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-3xl">medical_services</span>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-3">Dekat Saat Warga Membutuhkan</h3>
                    <p class="text-sm text-[#3d5a4d] dark:text-gray-400 leading-relaxed">
                        Akses kesehatan mahal bagi dhuafa. Masjid hadir menyediakan layanan kesehatan komunitas dan bantuan medis darurat, menguatkan ikatan kasih sayang antar tetangga.
                    </p>
                </div>
            </div>
            <!-- Program 5 -->
            <div class="p-8 bg-background-light dark:bg-[#1a2e25] rounded-3xl border border-[#dce4e1] dark:border-[#1e3a2f] shadow-sm flex flex-col gap-6">
                <div class="size-14 rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-3xl">emergency</span>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-3">Selalu Ada Saat Musibah Melanda</h3>
                    <p class="text-sm text-[#3d5a4d] dark:text-gray-400 leading-relaxed">
                        Kondisi darurat tak pernah terduga. Masjid menjadi pusat koordinasi relawan dan bantuan responsif, memastikan tidak ada warga yang merasa sendirian di masa sulit.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Rantai Dampak Section -->
<section class="py-24 px-6 bg-background-light dark:bg-background-dark/30">
    <div class="max-w-[1200px] mx-auto">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-black mb-4 text-[#111815] dark:text-white">Bagaimana Kebaikan Mengalir</h2>
            <p class="text-gray-500">Membangun ekosistem kepedulian melalui langkah-langkah nyata.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="flex flex-col items-center text-center gap-4">
                <div class="p-4 bg-white dark:bg-[#1a2e25] rounded-2xl border border-primary/20 shadow-sm w-full">
                    <span class="text-primary font-bold text-sm">Jamaah Berkontribusi</span>
                </div>
                <span class="material-symbols-outlined text-primary/30 hidden lg:block">arrow_forward</span>
            </div>
            <div class="flex flex-col items-center text-center gap-4">
                <div class="p-4 bg-white dark:bg-[#1a2e25] rounded-2xl border border-primary/20 shadow-sm w-full">
                    <span class="text-primary font-bold text-sm">Masjid Jaga Amanah</span>
                </div>
                <span class="material-symbols-outlined text-primary/30 hidden lg:block">arrow_forward</span>
            </div>
            <div class="flex flex-col items-center text-center gap-4">
                <div class="p-4 bg-white dark:bg-[#1a2e25] rounded-2xl border border-primary/20 shadow-sm w-full">
                    <span class="text-primary font-bold text-sm">Program Berjalan</span>
                </div>
                <span class="material-symbols-outlined text-primary/30 hidden lg:block">arrow_forward</span>
            </div>
            <div class="flex flex-col items-center text-center gap-4">
                <div class="p-4 bg-white dark:bg-[#1a2e25] rounded-2xl border border-primary/20 shadow-sm w-full">
                    <span class="text-primary font-bold text-sm">Warga Terbantu</span>
                </div>
                <span class="material-symbols-outlined text-primary/30 hidden lg:block">arrow_forward</span>
            </div>
            <div class="flex flex-col items-center text-center gap-4">
                <div class="p-4 bg-white dark:bg-[#1a2e25] rounded-2xl border border-primary/20 shadow-sm w-full">
                    <span class="text-primary font-bold text-sm">Kepercayaan Tumbuh</span>
                </div>
                <span class="material-symbols-outlined text-primary/30 hidden lg:block">arrow_forward</span>
            </div>
            <div class="flex flex-col items-center text-center gap-4">
                <div class="p-4 bg-primary text-white rounded-2xl shadow-lg w-full">
                    <span class="font-bold text-sm text-[11px] leading-tight">Lebih Banyak yang Tergerak</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Story Section -->
<section class="py-24 px-6 bg-white dark:bg-background-dark/30">
    <div class="max-w-[1000px] mx-auto bg-primary/5 rounded-[3.5rem] p-12 md:p-24 border border-primary/10 relative">
        <div class="absolute -top-10 left-12 size-20 bg-primary rounded-3xl flex items-center justify-center text-white shadow-xl rotate-3">
            <span class="material-symbols-outlined text-4xl">favorite</span>
        </div>
        <div class="flex flex-col items-center text-center gap-10">
            <h3 class="text-2xl md:text-3xl font-black text-[#111815] dark:text-white italic leading-tight">
                "Di sebuah masjid di lingkungan padat penduduk, dulu kegiatan hanya sebatas di dalam gedung. Masjid terasa jauh bagi warga yang kesulitan di sekitarnya."
            </h3>
            <div class="space-y-6 text-lg text-[#3d5a4d] dark:text-gray-400 leading-relaxed max-w-2xl">
                <p>
                    Sejak kebaikan mulai tertata, masjid ini mulai "mengetuk pintu" rumah warga. Seorang kakek sebatang kara kini rutin menerima kunjungan relawan dan bantuan makanan. Tiga orang anak yatim yang nyaris putus sekolah kini tersenyum kembali dengan seragam baru berkat beasiswa masjid.
                </p>
                <p>
                    Perubahan ini perlahan mengubah wajah lingkungan; warga yang tadinya acuh kini mulai aktif bergotong royong. Masjid bukan lagi sekadar tempat singgah, melainkan detak jantung yang menghidupkan harapan di setiap sudut gang.
                </p>
            </div>
            <p class="text-sm font-bold text-primary uppercase tracking-widest">Harapan Baru di Sudut Gang</p>
        </div>
    </div>
</section>

<!-- Gerakan Bersama Section -->
<section class="py-24 px-6 bg-white dark:bg-[#1a1d21]" id="gerakan">
    <div class="max-w-[1200px] mx-auto grid lg:grid-cols-2 gap-16 items-center">
        <div class="flex flex-col gap-6">
            <h2 class="text-3xl font-black text-[#111815] dark:text-white leading-tight">Membangun Kekuatan dari Akar Rumput</h2>
            <p class="text-lg text-[#3d5a4d] dark:text-gray-400 leading-relaxed">
                Kebaikan ini bukanlah sekadar bantuan sosial biasa. Ini adalah gerakan untuk membangun masyarakat yang lebih kuat dan peduli melalui solidaritas warga dan pemuda di rumah Allah.
            </p>
            <div class="grid grid-cols-2 gap-8 py-4">
                <div class="flex flex-col gap-2">
                    <span class="text-3xl font-black text-primary">Relawan</span>
                    <span class="text-xs font-bold text-gray-400 uppercase">Aksi Nyata</span>
                </div>
                <div class="flex flex-col gap-2">
                    <span class="text-3xl font-black text-primary">Generasi Muda</span>
                    <span class="text-xs font-bold text-gray-400 uppercase">Penggerak Perubahan</span>
                </div>
            </div>
        </div>
        <div class="relative">
            <div class="aspect-square bg-primary/10 rounded-[3rem] overflow-hidden border border-primary/5">
                <img alt="Community solidarity portrait" class="w-full h-full object-cover mix-blend-overlay" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAGipiyg48agSRjm2VZqV0Jz_RgaKsdz8xGwWaWGOvuTQcwyeuF7uYCIA1buc2YkHwDgoitWPFGTHw795Diik7_IE9afPqHFpppJDgjpjuZnP9b8vliWlYm8KYK4GL8MRHRKz7zArIniCG3uyoDSW1tQKAeBOC5aKM5L_QXrQzPspDBeWZePlF5tiXkG2cEbxEq8nizgLHLshGJ-WhK4o7O85iNNkPwC0k7B3lLRGrZu1I_J0YAZ9ZyctT7dOm6b_3xfY3cQxBRPYlD"/>
            </div>
        </div>
    </div>
</section>

<!-- Final CTA Section -->
<section class="py-24 px-6 bg-white dark:bg-[#1a1d21]">
    <div class="max-w-[1000px] mx-auto text-center">
        <h2 class="text-3xl md:text-5xl font-black mb-8 text-[#111815] dark:text-white leading-tight">
            Masjid yang kuat bukan hanya bangunannya. <br/> Tetapi dampaknya bagi masyarakat.
        </h2>
        <div class="flex flex-wrap justify-center gap-4 mb-8">
            <a href="#" class="px-8 py-4 bg-primary text-white rounded-xl font-black hover:scale-105 transition-all shadow-xl shadow-primary/20">
                Ikut Mendukung Gerakan
            </a>
            <a href="#" class="px-8 py-4 bg-background-light dark:bg-gray-800 text-gray-700 dark:text-white rounded-xl font-black border border-[#dce4e1] dark:border-gray-700">
                Bergabung Jadi Relawan
            </a>
            <a href="<?= base_url('register') ?>" class="px-8 py-4 bg-primary/10 text-primary rounded-xl font-black hover:bg-primary/20 transition-all">
                Daftarkan Masjid Kami
            </a>
        </div>
        <p class="text-sm text-gray-500 italic">"Gratis, amanah, dan untuk kebaikan bersama."</p>
    </div>
</section>
<?= $this->endSection() ?>
