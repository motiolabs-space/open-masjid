<?= $this->extend('layout/landing') ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="relative overflow-hidden pt-16 pb-24 px-6 bg-white dark:bg-background-dark">
    <div class="max-w-[1200px] mx-auto grid lg:grid-cols-2 gap-12 items-center relative z-10">
        <div class="flex flex-col gap-8">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 border border-primary/20 text-primary text-xs font-bold uppercase tracking-wider w-fit">
                Pusat Kebaikan Masyarakat
            </div>
            <h1 class="text-5xl md:text-7xl font-black leading-[1.05] tracking-tight text-[#111815] dark:text-white">
                Masjid yang Kuat, <br/> <span class="text-primary italic">Masyarakat yang Berdaya</span>
            </h1>
            <p class="text-xl text-[#3d5a4d] dark:text-gray-400 max-w-[540px] leading-relaxed">
                Kami hadir untuk mengembalikan masjid sebagai sumber solusi bagi setiap persoalan warga. Masj.id adalah gerakan kolaboratif untuk memperkuat peran sosial rumah Allah.
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="<?= base_url('register') ?>" class="px-10 py-5 bg-primary text-white rounded-2xl font-black text-lg shadow-2xl shadow-primary/20 hover:scale-105 transition-all">
                    Mulai Langkah Kebaikan
                </a>
            </div>
        </div>
        <div class="relative group">
            <div class="absolute -inset-10 bg-primary/5 rounded-full blur-[100px] group-hover:bg-primary/10 transition-colors"></div>
            <div class="relative rounded-[3rem] overflow-hidden shadow-2xl border-4 border-white dark:border-gray-800">
                <img alt="Community Movement" class="w-full aspect-square object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCH6rPE_09TLBALsaxYwC-HYCb0gcQ5pL30SIbC5NI5brKt_v0dNXmcxxDfBGu_Wp3QJEBcjauKQTDR9sTrzpEMsgzpnPFf3mcjXLe_5k6tUZ_6pdDqpWZtq5NGsBblfmvn7MtooixCULuSg_Zq862nDNhPGgVt2eZ2L_2XAvFmWtSEVkc-Uec7aXoI3L0AJmS33p199NPfzKQj2bikTxEOysH9iUj78mJQCpjUaMVVI_xQxkBuMO5gZ9EZsFlTHuYlVjY2ZiVQ3t_c"/>
                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
            </div>
        </div>
    </div>
</section>

<!-- 3 Impact Pillars Section -->
<section class="py-24 px-6 bg-background-light dark:bg-background-dark/30">
    <div class="max-w-[1200px] mx-auto text-center mb-16">
        <h2 class="text-primary font-bold uppercase tracking-widest text-sm mb-4">Pilar Perubahan</h2>
        <h3 class="text-3xl md:text-5xl font-black text-[#111815] dark:text-white">Fokus Dampak Gerakan Kita</h3>
    </div>
    <div class="max-w-[1200px] mx-auto grid md:grid-cols-3 gap-8">
        <div class="bg-white dark:bg-[#1a2e25] p-10 rounded-[2.5rem] border border-primary/5 shadow-sm hover:shadow-xl transition-all text-center">
            <div class="size-20 bg-primary/10 rounded-3xl flex items-center justify-center text-primary mx-auto mb-8">
                <span class="material-symbols-outlined text-4xl">favorite</span>
            </div>
            <h4 class="text-2xl font-black mb-4 text-[#111815] dark:text-white leading-tight">Rangkulan Kasih Sayang</h4>
            <p class="text-[#3d5a4d] dark:text-gray-400 leading-relaxed">
                Memastikan tidak ada tetangga kita yang merasa sendirian di tengah kesulitan, dengan dukungan nyata yang hadir dari pintu masjid.
            </p>
        </div>
        <div class="bg-white dark:bg-[#1a2e25] p-10 rounded-[2.5rem] border border-primary/5 shadow-sm hover:shadow-xl transition-all text-center">
            <div class="size-20 bg-primary/10 rounded-3xl flex items-center justify-center text-primary mx-auto mb-8">
                <span class="material-symbols-outlined text-4xl">trending_up</span>
            </div>
            <h4 class="text-2xl font-black mb-4 text-[#111815] dark:text-white leading-tight">Kemandirian Umat</h4>
            <p class="text-[#3d5a4d] dark:text-gray-400 leading-relaxed">
                Membuka jalan bagi warga dhuafa untuk kembali tegak dan mandiri secara ekonomi melalui pemberdayaan berbasis komunitas.
            </p>
        </div>
        <div class="bg-white dark:bg-[#1a2e25] p-10 rounded-[2.5rem] border border-primary/5 shadow-sm hover:shadow-xl transition-all text-center">
            <div class="size-20 bg-primary/10 rounded-3xl flex items-center justify-center text-primary mx-auto mb-8">
                <span class="material-symbols-outlined text-4xl">lightbh</span>
            </div>
            <h4 class="text-2xl font-black mb-4 text-[#111815] dark:text-white leading-tight">Cahaya Perubahan</h4>
            <p class="text-[#3d5a4d] dark:text-gray-400 leading-relaxed">
                Menjadikan masjid sebagai pusat ilmu dan kepedulian yang mencerahkan setiap jiwa di setiap sudut lingkungannya.
            </p>
        </div>
    </div>
</section>

<!-- Movement Explanation Section -->
<section class="py-24 px-6 bg-white dark:bg-background-dark">
    <div class="max-w-[900px] mx-auto text-center">
        <div class="inline-flex items-center justify-center size-20 rounded-full bg-primary/10 text-primary mb-10">
            <span class="material-symbols-outlined text-4xl">groups</span>
        </div>
        <h2 class="text-3xl md:text-5xl font-black mb-10 text-[#111815] dark:text-white leading-tight">Gerakan Berbagi Harapan</h2>
        <div class="space-y-8 text-xl text-[#3d5a4d] dark:text-gray-400 leading-relaxed italic">
            <p>
                "Ini bukan sekadar tentang teknologi, melainkan tentang menghidupkan kembali ruh gotong royong di tengah masyarakat kita."
            </p>
            <p class="not-italic text-lg">
                Masj.id menghubungkan niat tulus jamaah dengan kebutuhan nyata masyarakat, dikelola dengan cara yang paling amanah dan transparan melalui institusi masjid terdekat Anda.
            </p>
        </div>
    </div>
</section>

<!-- Final CTA Section -->
<section class="py-24 px-6">
    <div class="max-w-[1100px] mx-auto bg-[#0a241a] dark:bg-[#11241d] rounded-[3rem] p-12 md:p-24 text-center relative overflow-hidden shadow-2xl">
        <div class="absolute top-0 right-0 w-96 h-96 bg-primary/20 blur-[120px] -translate-y-1/2 translate-x-1/2"></div>
        <div class="relative z-10 flex flex-col items-center gap-10">
            <h2 class="text-4xl md:text-6xl font-black text-white leading-tight">
                Jadilah Bagian dari <br/> <span class="text-primary italic">Cahaya Kebaikan Ini</span>
            </h2>
            <p class="text-xl text-emerald-100/70 max-w-[700px]">
                Dampak besar dimulai dari langkah kecil di masjid terdekat Anda. Mari bersama-sama menebar manfaat nyata bagi lingkungan sekitar.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="<?= base_url('register') ?>" class="bg-white text-primary px-12 py-5 rounded-2xl text-lg font-black hover:bg-emerald-50 transition-all shadow-xl">
                    Mulai Bergerak Sekarang
                </a>
            </div>
            <div class="flex flex-col items-center gap-4 mt-4">
                <p class="text-emerald-200/50 italic font-bold">Didukung Yayasan Generasi Sahabat Muslim. Amanah untuk kebaikan bersama.</p>
                <div class="flex items-center gap-8 opacity-40 grayscale group-hover:grayscale-0 transition-all">
                    <span class="material-symbols-outlined text-white text-3xl">verified</span>
                    <span class="material-symbols-outlined text-white text-3xl">volunteer_activism</span>
                    <span class="material-symbols-outlined text-white text-3xl">shield_with_heart</span>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
