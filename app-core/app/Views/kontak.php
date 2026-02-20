<?= $this->extend('layout/main') ?>

<?= $this->section('extra_head') ?>
<title>Kontak Kami - Masj.id</title>
<style type="text/tailwindcss">
    .form-input:focus { border-color: #24a871 !important; ring: 0 !important; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main class="max-w-[1200px] mx-auto px-6 py-12">
    <div class="mb-12">
        <h2 class="text-4xl md:text-5xl font-black leading-tight tracking-tight mb-4 text-gray-900 dark:text-white">Kontak Kami</h2>
        <p class="text-gray-600 dark:text-gray-400 text-lg max-w-2xl leading-relaxed">
            Kami siap membantu proses digitalisasi manajemen masjid Anda dengan solusi terbaik. Tim kami akan merespon pesan Anda dalam waktu 24 jam.
        </p>
    </div>

    <!-- Community Section -->
    <section class="mb-16">
        <div class="bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/50 rounded-2xl p-8 flex flex-col lg:flex-row items-center justify-between gap-8">
            <div class="flex-1">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900 text-primary dark:text-emerald-400 text-xs font-bold uppercase tracking-wider mb-4">
                    <span class="material-symbols-outlined text-sm">groups</span>
                    Komunitas Masj.id
                </div>
                <h3 class="text-2xl font-bold mb-3 text-gray-900 dark:text-white">Bergabung ke Grup WhatsApp Komunitas</h3>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed max-w-2xl">
                    Mari berkolaborasi bersama para pengelola masjid lainnya. Grup ini ditujukan bagi Anda yang ingin berdiskusi aktif dalam membangun, berkontribusi, dan menyebarkan platform Masj.id guna memakmurkan umat melalui teknologi.
                </p>
            </div>
            <div class="shrink-0">
                <a class="inline-flex items-center gap-3 px-8 py-4 bg-primary hover:bg-[#1f8e5f] text-white font-bold rounded-xl transition-all shadow-lg shadow-emerald-900/10" href="#">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"></path>
                    </svg>
                    Bergabung Sekarang
                </a>
            </div>
        </div>
    </section>

    <!-- Contact Info & Form -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        <div class="lg:col-span-5 space-y-8">
            <div class="bg-gray-50 dark:bg-gray-800/50 p-8 rounded-2xl space-y-8 border border-gray-100 dark:border-gray-800">
                <!-- 
                <div>
                    <div class="flex items-center gap-3 mb-4 text-primary">
                        <span class="material-symbols-outlined">location_on</span>
                        <h3 class="font-bold text-lg text-gray-900 dark:text-white">Alamat Kantor</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                        Pusat Digitalisasi Masjid Indonesia<br/>
                        Jl. Ibrahim Adjie No. 123, Lantai 4<br/>
                        Bandung, Jawa Barat 40285
                    </p>
                </div>
                <div class="border-t border-gray-200 dark:border-gray-700 pt-8">
                    <div class="flex items-center gap-3 mb-4 text-primary">
                        <span class="material-symbols-outlined">chat</span>
                        <h3 class="font-bold text-lg text-gray-900 dark:text-white">WhatsApp Customer Success</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">+62 812-3456-7890</p>
                    <a class="inline-flex items-center gap-2 text-primary font-bold hover:underline" href="#">
                        Chat sekarang
                        <span class="material-symbols-outlined text-sm">open_in_new</span>
                    </a>
                </div>
                -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-8">
                    <div class="flex items-center gap-3 mb-4 text-primary">
                        <span class="material-symbols-outlined">mail</span>
                        <h3 class="font-bold text-lg text-gray-900 dark:text-white">Email Dukungan</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300">support@masj.id</p>
                    <p class="text-xs text-gray-500 mt-1 italic">Tersedia 24/7 untuk kendala teknis</p>
                </div>
            </div>
            <div class="p-6 border border-gray-100 dark:border-gray-800 rounded-2xl flex items-center gap-4 bg-white dark:bg-gray-800 shadow-sm">
                <div class="bg-emerald-100 dark:bg-emerald-900/50 p-3 rounded-full text-primary dark:text-emerald-400">
                    <span class="material-symbols-outlined">verified</span>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900 dark:text-white">Terpercaya &amp; Aman</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Telah digunakan oleh 500+ masjid di seluruh Indonesia.</p>
                </div>
            </div>
        </div>

        <div class="lg:col-span-7 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl p-8 shadow-xl shadow-gray-200/50 dark:shadow-none">
            <form action="#" class="space-y-6" method="POST">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Nama Lengkap</label>
                        <input class="w-full rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 h-12 px-4 focus:ring-primary focus:border-primary" placeholder="Masukkan nama Anda" type="text"/>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Nama Masjid</label>
                        <input class="w-full rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 h-12 px-4 focus:ring-primary focus:border-primary" placeholder="Contoh: Masjid Raya Al-Mansur" type="text"/>
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Subjek</label>
                    <select class="w-full rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 h-12 px-4 focus:ring-primary focus:border-primary">
                        <option>Pertanyaan Pendaftaran</option>
                        <option>Bantuan Teknis</option>
                        <option>Kerjasama Strategis</option>
                        <option>Lainnya</option>
                    </select>
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Pesan</label>
                    <textarea class="w-full rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 p-4 focus:ring-primary focus:border-primary resize-none" placeholder="Tuliskan pesan atau pertanyaan Anda secara detail..." rows="5"></textarea>
                </div>
                <button class="w-full bg-primary hover:bg-[#1f8e5f] text-white font-bold h-14 rounded-lg transition-all flex items-center justify-center gap-2 shadow-lg shadow-emerald-900/10" type="submit">
                    <span class="material-symbols-outlined">send</span>
                    Kirim Pesan Sekarang
                </button>
            </form>
        </div>
    </div>

    <!-- FAQ Section -->
    <section class="mt-24">
        <div class="text-center mb-12">
            <h3 class="text-3xl font-bold mb-4 text-gray-900 dark:text-white">Pertanyaan Umum (FAQ)</h3>
            <p class="text-gray-600 dark:text-gray-400">Jawaban cepat untuk pertanyaan yang sering diajukan.</p>
        </div>
        <div class="max-w-3xl mx-auto space-y-4">
            <div class="border border-gray-100 dark:border-gray-800 rounded-xl p-6 bg-white dark:bg-gray-800 shadow-sm">
                <h4 class="font-bold text-lg mb-2 text-gray-900 dark:text-white">Bagaimana cara mendaftarkan masjid baru?</h4>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed text-sm">Anda dapat menekan tombol "Daftar Masjid" di pojok kanan atas, mengisi formulir profil masjid, dan tim kami akan melakukan verifikasi dalam 1x24 jam.</p>
            </div>
            <div class="border border-gray-100 dark:border-gray-800 rounded-xl p-6 bg-white dark:bg-gray-800 shadow-sm">
                <h4 class="font-bold text-lg mb-2 text-gray-900 dark:text-white">Apakah ada biaya berlangganan?</h4>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed text-sm">Masj.id memiliki paket gratis untuk fitur dasar dan paket premium untuk fitur manajemen keuangan dan inventaris yang lebih kompleks.</p>
            </div>
            <div class="border border-gray-100 dark:border-gray-800 rounded-xl p-6 bg-white dark:bg-gray-800 shadow-sm">
                <h4 class="font-bold text-lg mb-2 text-gray-900 dark:text-white">Bagaimana keamanan data masjid kami?</h4>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed text-sm">Semua data dienkripsi dengan standar industri perbankan dan disimpan di server cloud lokal yang aman serta rutin di-backup.</p>
            </div>
        </div>
    </section>

    <!-- Map Section Placeholder -->
    <!-- 
    <section class="mt-24">
        <div class="rounded-3xl overflow-hidden border border-gray-100 dark:border-gray-800 h-[400px] relative">
            <img alt="Peta Lokasi Kantor Masj.id di Bandung" class="w-full h-full object-cover grayscale opacity-30 dark:opacity-20" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAx-IqwkH-wKMBqd2R99WzHtxg4pViZyUScuWMoUdb_wtB91-H7zNpX0MdMn5N5_OTp02V3ky20kt_y7e7MptoB5U_xNTLNhXl9S6una_8mp6TR-XyE9a6kIQmgAmzRV9HFdK1hkE9ElTv9Fe23iMl9Lly-tHZa5XFQBK6ybaa6s0kqVKCH_FOVYSjgSrF5yOjTqTR_uL-lEGCaJL8sGA2aTrrPgm9W2xUi6bkU_IT_ADfdXExau7H37_s_3EFySxb8VWTiuGYzjEja"/>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-2xl border border-emerald-100 dark:border-emerald-900/30 text-center max-w-xs">
                    <div class="bg-emerald-50 dark:bg-emerald-900/30 w-12 h-12 rounded-full flex items-center justify-center text-primary mx-auto mb-4">
                        <span class="material-symbols-outlined">location_on</span>
                    </div>
                    <h4 class="font-bold mb-1 text-gray-900 dark:text-white">Kantor Operasional</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Jl. Ibrahim Adjie No. 123, Bandung</p>
                    <button class="text-primary text-sm font-bold border border-emerald-200 dark:border-emerald-900 px-4 py-2 rounded-lg hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition-colors">
                        Buka di Google Maps
                    </button>
                </div>
            </div>
        </div>
    </section>
    -->
</main>
<?= $this->endSection() ?>
