<?= $this->extend('layout/public') ?>

<?= $this->section('content') ?>
<div class="py-12 bg-neutral-50 min-h-screen">
    <div class="container mx-auto px-4 max-w-2xl">
        <a href="<?= base_url($masjid['username']) ?>" class="inline-flex items-center gap-2 text-primary font-bold mb-8 hover:-translate-x-1 transition-transform">
            <span class="material-symbols-outlined">arrow_back</span>
            Kembali ke Profil Masjid
        </a>

        <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 overflow-hidden">
            <!-- Header -->
            <div class="bg-primary/5 p-8 border-b border-primary/10">
                <h1 class="text-3xl font-black text-gray-900 mb-2">Formulir Donasi</h1>
                <p class="text-gray-600">
                    Bantu program kebaikan masjid 
                    <strong class="text-primary"><?= esc($masjid['name']) ?></strong>
                </p>
            </div>

            <div class="p-8">
                <?php if ($program): ?>
                    <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-4 mb-8 flex items-start gap-4">
                        <div class="size-16 bg-emerald-100 rounded-lg flex-shrink-0 overflow-hidden">
                            <?php if ($program['thumbnail']): ?>
                                <img src="<?= $program['thumbnail'] ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-emerald-600">
                                    <span class="material-symbols-outlined">volunteer_activism</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <p class="text-xs text-emerald-600 font-bold uppercase tracking-widest mb-1">Donasi Untuk Program</p>
                            <h3 class="font-bold text-gray-900"><?= esc($program['title']) ?></h3>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="bg-red-50 text-red-600 px-4 py-3 rounded-xl mb-6 text-sm font-bold flex items-center gap-2">
                        <span class="material-symbols-outlined">error</span>
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('donation/process') ?>" method="post" class="space-y-6">
                    <?= csrf_field() ?>
                    <input type="hidden" name="masjid_id" value="<?= $masjid['id'] ?>">
                    <input type="hidden" name="program_id" value="<?= $program['id'] ?? '' ?>">

                    <!-- Amount -->
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Nominal Donasi (Rp)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-3.5 text-gray-400 font-bold">Rp</span>
                            <input type="text" name="amount" id="amount" class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all font-bold text-lg" placeholder="0" required onkeyup="formatCurrency(this)">
                        </div>
                        <div class="flex gap-2 mt-3 overflow-x-auto pb-2 scrollbar-hide">
                            <button type="button" onclick="setAmount(50000)" class="px-4 py-2 rounded-lg bg-gray-50 border border-gray-200 text-sm font-bold text-gray-600 hover:bg-primary/10 hover:border-primary hover:text-primary transition-all whitespace-nowrap">50.000</button>
                            <button type="button" onclick="setAmount(100000)" class="px-4 py-2 rounded-lg bg-gray-50 border border-gray-200 text-sm font-bold text-gray-600 hover:bg-primary/10 hover:border-primary hover:text-primary transition-all whitespace-nowrap">100.000</button>
                            <button type="button" onclick="setAmount(250000)" class="px-4 py-2 rounded-lg bg-gray-50 border border-gray-200 text-sm font-bold text-gray-600 hover:bg-primary/10 hover:border-primary hover:text-primary transition-all whitespace-nowrap">250.000</button>
                            <button type="button" onclick="setAmount(500000)" class="px-4 py-2 rounded-lg bg-gray-50 border border-gray-200 text-sm font-bold text-gray-600 hover:bg-primary/10 hover:border-primary hover:text-primary transition-all whitespace-nowrap">500.000</button>
                        </div>
                    </div>

                    <!-- Donor Info -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="col-span-full">
                            <label class="block text-sm font-bold text-gray-900 mb-2">Nama Lengkap (Hamba Allah)</label>
                            <input type="text" name="name" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all" placeholder="Nama Anda" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Email (Opsional)</label>
                            <input type="email" name="email" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all" placeholder="email@contoh.com">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">No. WhatsApp</label>
                            <input type="tel" name="phone" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all" placeholder="0812..." required>
                        </div>
                        <div class="col-span-full">
                            <label class="block text-sm font-bold text-gray-900 mb-2">Doa / Pesan (Opsional)</label>
                            <textarea name="message" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all" placeholder="Tuliskan doa atau pesan untuk masjid..."></textarea>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-4 bg-primary text-white text-lg font-bold rounded-xl shadow-lg shadow-primary/30 hover:shadow-xl hover:-translate-y-1 transition-all mt-8">
                        Lanjut Pembayaran
                    </button>
                    <p class="text-center text-xs text-gray-400 mt-4 flex items-center justify-center gap-1">
                        <span class="material-symbols-outlined text-sm">lock</span>
                        Pembayaran aman & terverifikasi otomatis.
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function formatCurrency(input) {
    let value = input.value.replace(/\D/g, '');
    let formatted = new Intl.NumberFormat('id-ID').format(value);
    input.value = formatted;
}

function setAmount(amount) {
    let input = document.getElementById('amount');
    input.value = new Intl.NumberFormat('id-ID').format(amount);
}
</script>
<?= $this->endSection() ?>
