<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<div class="px-8 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8 flex items-center gap-4">
            <a href="<?= base_url('dashboard/broadcast') ?>" class="size-10 flex items-center justify-center bg-white dark:bg-white/5 rounded-xl border border-gray-200 dark:border-white/10 hover:bg-gray-50 text-gray-500 transition-colors">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div>
                <h1 class="text-3xl font-black text-[#111816] dark:text-white tracking-tight">Buat Siaran Baru</h1>
                <p class="text-[#608a7e]">Tulis pesan yang akan dikirim ke seluruh subscriber via Email.</p>
            </div>
        </div>

        <form action="<?= base_url('dashboard/broadcast/send') ?>" method="POST" class="space-y-6" onsubmit="return confirm('Apakah Anda yakin ingin mengirim pesan ini ke semua subscriber?');">
            
            <div class="bg-white dark:bg-white/5 p-8 rounded-3xl border border-[#e5e7eb] dark:border-white/10 space-y-6">
                
                <div>
                    <label class="block text-sm font-bold mb-2 text-[#111816] dark:text-white">Subjek Email</label>
                    <input type="text" name="subject" required class="w-full bg-[#f0f5f3] dark:bg-background-dark border-none rounded-xl focus:ring-2 focus:ring-primary p-4 font-bold text-[#111816] dark:text-white placeholder-gray-400" placeholder="Contoh: Jadwal Sholat Jumat & Laporan Keuangan Bulan Ini">
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2 text-[#111816] dark:text-white">Isi Pesan (HTML Support)</label>
                    <textarea name="content" rows="10" required class="w-full bg-[#f0f5f3] dark:bg-background-dark border-none rounded-xl focus:ring-2 focus:ring-primary p-4 text-[#111816] dark:text-white font-medium" placeholder="Tulis pesan Anda di sini..."></textarea>
                    <p class="text-xs text-gray-500 mt-2">*Anda bisa menggunakan tag HTML dasar seperti &lt;b&gt;, &lt;br&gt;, &lt;ul&gt;.</p>
                </div>
                
                <div class="bg-blue-50 text-blue-800 p-4 rounded-xl flex gap-3 text-sm font-medium border border-blue-100">
                    <span class="material-symbols-outlined shrink-0">info</span>
                    <div>
                        Pesan akan dikirim menggunakan <b>SMTP Email</b> yang terkonfigurasi. Pastikan pengaturan SMTP di file <code>.env</code> sudah benar.
                    </div>
                </div>

            </div>

            <div class="flex justify-end gap-4">
                <button type="submit" class="bg-primary text-white px-8 py-4 rounded-xl font-bold hover:bg-emerald-900 transition-all shadow-lg shadow-primary/20 flex items-center gap-2">
                    <span class="material-symbols-outlined">send</span>
                    <span>Kirim Siaran Sekarang</span>
                </button>
            </div>

        </form>
    </div>
</div>
<?= $this->endSection() ?>
