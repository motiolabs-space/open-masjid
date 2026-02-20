<?= $this->extend('layout/landing') ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="max-w-[1200px] mx-auto px-6 py-24 md:py-32">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
        <div class="flex flex-col gap-8">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold uppercase tracking-wider w-fit">
                Alat Bantu Perubahan
            </div>
            <h1 class="text-4xl md:text-6xl font-black tracking-tight leading-[1.1] text-gray-900 dark:text-white">
                Alat Khidmah untuk <br/> <span class="text-primary italic">Amanah yang Lebih Besar</span>
            </h1>
            <p class="text-xl text-[#3d5a4d] dark:text-gray-400 max-w-lg leading-relaxed">
                Kami menyediakan instrumen yang memudahkan pengurus masjid untuk mengelola kepedulian masyarakat dengan tertata dan jujur.
            </p>
        </div>
        <div class="relative">
            <div class="w-full aspect-square bg-gradient-to-br from-primary/10 to-transparent rounded-[3rem] border border-primary/5 flex items-center justify-center p-12">
                 <span class="material-symbols-outlined text-[120px] text-primary/20">settings_accessibility</span>
            </div>
        </div>
    </div>
</section>

<!-- Introduction Section -->
<section class="py-24 px-6 bg-background-light dark:bg-background-dark/30">
    <div class="max-w-[800px] mx-auto text-center">
        <h2 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-200 leading-relaxed font-bold italic">
            "Masjid butuh alat yang amanah. Karena tanpa pengelolaan yang baik, niat mulia seringkali tidak sampai ke tangan yang paling membutuhkan."
        </h2>
    </div>
</section>

<!-- Impact Enablers Section -->
<section class="py-24 px-6 bg-white dark:bg-background-dark">
    <div class="max-w-[1000px] mx-auto flex flex-col gap-24">
        
        <!-- Pillar 1 -->
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div class="flex flex-col gap-6">
                <div class="size-16 rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-4xl">hail</span>
                </div>
                <h3 class="text-3xl font-black text-gray-900 dark:text-white">1. Penataan Warga & Lingkungan</h3>
                <div class="space-y-6">
                    <div>
                        <p class="text-xs font-bold text-red-500 uppercase tracking-widest mb-1">Masalah</p>
                        <p class="text-gray-600 dark:text-gray-400">Banyak masjid belum mengenal dengan baik kondisi ekonomi setiap jamaah di sekitarnya.</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-primary uppercase tracking-widest mb-1">Solusi</p>
                        <p class="text-gray-600 dark:text-gray-400">Pendataan warga dhuafa dan pemetaan kebutuhan sosial komunitas secara terpadu.</p>
                    </div>
                    <div class="p-4 bg-primary/5 rounded-xl border border-primary/10">
                        <p class="text-xs font-bold text-primary uppercase tracking-widest mb-1 italic">Dampak</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">Bantuan sosial jatuh ke tangan yang paling tepat dan tidak ada warga yang terlewatkan.</p>
                    </div>
                </div>
            </div>
            <div class="bg-background-light dark:bg-gray-800 rounded-[2.5rem] aspect-square flex items-center justify-center border border-primary/5">
                <span class="material-symbols-outlined text-8xl text-primary/10">location_searching</span>
            </div>
        </div>

        <!-- Pillar 2 -->
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div class="md:order-2 flex flex-col gap-6">
                <div class="size-16 rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-4xl">verified_user</span>
                </div>
                <h3 class="text-3xl font-black text-gray-900 dark:text-white">2. Transparansi Amanah Dana</h3>
                <div class="space-y-6">
                    <div>
                        <p class="text-xs font-bold text-red-500 uppercase tracking-widest mb-1">Masalah</p>
                        <p class="text-gray-600 dark:text-gray-400">Ketidaktahuan jamaah tentang penggunaan dana masjid sering menghambat niat berdonasi.</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-primary uppercase tracking-widest mb-1">Solusi</p>
                        <p class="text-gray-600 dark:text-gray-400">Laporan keuangan publik yang dapat diakses siapa pun secara terbuka dan jujur.</p>
                    </div>
                    <div class="p-4 bg-primary/5 rounded-xl border border-primary/10">
                        <p class="text-xs font-bold text-primary uppercase tracking-widest mb-1 italic">Dampak</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">Kepercayaan tumbuh, membuat jamaah lebih ringan tangan untuk mendukung program sosial.</p>
                    </div>
                </div>
            </div>
            <div class="md:order-1 bg-background-light dark:bg-gray-800 rounded-[2.5rem] aspect-square flex items-center justify-center border border-primary/5">
                <span class="material-symbols-outlined text-8xl text-primary/10">account_balance</span>
            </div>
        </div>

        <!-- Pillar 3 -->
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div class="flex flex-col gap-6">
                <div class="size-16 rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-4xl">volunteer_activism</span>
                </div>
                <h3 class="text-3xl font-black text-gray-900 dark:text-white">3. Jembatan Kepedulian</h3>
                <div class="space-y-6">
                    <div>
                        <p class="text-xs font-bold text-red-500 uppercase tracking-widest mb-1">Masalah</p>
                        <p class="text-gray-600 dark:text-gray-400">Sulitnya akses bagi warga untuk berkontribusi atau mendapatkan bantuan di saat darurat.</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-primary uppercase tracking-widest mb-1">Solusi</p>
                        <p class="text-gray-600 dark:text-gray-400">Saluran kontribusi digital dan pendaftaran bantuan yang mudah dijangkau melalui masjid.</p>
                    </div>
                    <div class="p-4 bg-primary/5 rounded-xl border border-primary/10">
                        <p class="text-xs font-bold text-primary uppercase tracking-widest mb-1 italic">Dampak</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">Bantuan mengalir lebih cepat di saat-saat yang paling kritis bagi masyarakat.</p>
                    </div>
                </div>
            </div>
            <div class="bg-background-light dark:bg-gray-800 rounded-[2.5rem] aspect-square flex items-center justify-center border border-primary/5">
                <span class="material-symbols-outlined text-8xl text-primary/10">connect_without_contact</span>
            </div>
        </div>

    </div>
</section>

<!-- Closing Section -->
<section class="py-24 px-6 bg-background-light dark:bg-background-dark/30">
    <div class="max-w-[800px] mx-auto text-center">
        <p class="text-xl text-[#3d5a4d] dark:text-gray-400 leading-relaxed font-bold">
            "Teknologi bagi kami hanyalah sarana. Tujuan utamanya tetap satu: kemudahan bagi masjid dalam melayani umat."
        </p>
        <div class="mt-12">
            <a href="<?= base_url('register') ?>" class="btn-primary-lg px-12">Daftarkan Masjid Kami</a>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
