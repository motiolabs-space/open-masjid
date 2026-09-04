<?= $this->extend('layout/masjid_public') ?>

<?= $this->section('content') ?>
<section class="py-16 md:py-24 bg-background-light dark:bg-background-dark min-h-screen">
    <div class="max-w-xl mx-auto px-6">

        <div class="mb-8">
            <a href="<?= base_url($masjid['username']) ?>" class="inline-flex items-center gap-2 text-primary font-bold mb-4 hover:underline">
                <span class="material-symbols-outlined text-sm">arrow_back</span> Kembali ke Profil
            </a>
            <h1 class="text-3xl md:text-4xl font-black text-[#111816] dark:text-white tracking-tight">Donasi Rutin</h1>
            <p class="text-[#608a7e] text-lg mt-1">Jadikan kebaikan sebuah kebiasaan. Berkomitmen berinfaq rutin di <?= esc($masjid['name']) ?>.</p>
        </div>

        <?php if (session()->getFlashdata('sukses_rutin')): ?>
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl p-5 mb-6 flex items-start gap-3">
                <span class="material-symbols-outlined">check_circle</span>
                <div>
                    <p class="font-bold">Komitmen tercatat</p>
                    <p class="text-sm mt-0.5"><?= esc(session()->getFlashdata('sukses_rutin')) ?></p>
                </div>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-rose-50 border border-rose-200 text-rose-600 rounded-2xl p-4 mb-6 text-sm font-bold flex items-center gap-2">
                <span class="material-symbols-outlined">error</span> <?= esc(session()->getFlashdata('error')) ?>
            </div>
        <?php endif; ?>

        <div class="bg-primary/5 border border-primary/15 rounded-2xl p-4 mb-6 text-sm text-[#374151] dark:text-slate-300 flex gap-3">
            <span class="material-symbols-outlined text-primary shrink-0">info</span>
            <p>Ini adalah <strong>komitmen &amp; pengingat</strong>, bukan penarikan dana otomatis. Setiap periode kami mengingatkan lewat WhatsApp beserta tautan pembayaran — Anda tetap memegang kendali penuh.</p>
        </div>

        <form action="<?= base_url('donasi-rutin/simpan') ?>" method="post" class="bg-white dark:bg-white/5 rounded-3xl border border-[#dbe6e3] dark:border-white/10 p-6 md:p-8 space-y-5">
            <?= csrf_field() ?>
            <input type="hidden" name="masjid_id" value="<?= $masjid['id'] ?>">

            <div>
                <label class="block text-sm font-bold mb-2">Nominal per Donasi (Rp)</label>
                <div class="relative">
                    <span class="absolute left-4 top-3.5 text-gray-400 font-bold">Rp</span>
                    <input type="text" name="amount" inputmode="numeric" required placeholder="0" oninput="fmtRp(this)"
                           class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 dark:bg-white/5 dark:border-white/10 focus:border-primary focus:ring-4 focus:ring-primary/10 font-bold text-lg">
                </div>
                <div class="flex gap-2 mt-3">
                    <?php foreach ([50000, 100000, 250000] as $n): ?>
                        <button type="button" onclick="setNom(<?= $n ?>)" class="px-4 py-2 rounded-lg bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 text-sm font-bold text-gray-600 dark:text-slate-300 hover:border-primary hover:text-primary"><?= number_format($n, 0, ',', '.') ?></button>
                    <?php endforeach; ?>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold mb-2">Frekuensi</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="frequency" value="bulanan" class="peer sr-only" checked>
                        <div class="text-center py-3 rounded-xl border-2 border-gray-200 dark:border-white/10 font-bold text-sm peer-checked:border-primary peer-checked:text-primary peer-checked:bg-primary/5">Setiap Bulan</div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="frequency" value="mingguan" class="peer sr-only">
                        <div class="text-center py-3 rounded-xl border-2 border-gray-200 dark:border-white/10 font-bold text-sm peer-checked:border-primary peer-checked:text-primary peer-checked:bg-primary/5">Setiap Pekan</div>
                    </label>
                </div>
            </div>

            <?php if (! empty($programs)): ?>
                <div>
                    <label class="block text-sm font-bold mb-2">Untuk Program (opsional)</label>
                    <select name="program_id" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:bg-white/5 dark:border-white/10 font-medium">
                        <option value="">Donasi Umum</option>
                        <?php foreach ($programs as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= esc($p['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <div class="grid md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold mb-2">Nama Lengkap</label>
                    <input type="text" name="name" required placeholder="Nama Anda" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:bg-white/5 dark:border-white/10">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-2">No. WhatsApp</label>
                    <input type="tel" name="phone" required placeholder="0812..." class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:bg-white/5 dark:border-white/10">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-2">Email (opsional)</label>
                    <input type="email" name="email" placeholder="email@contoh.com" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:bg-white/5 dark:border-white/10">
                </div>
            </div>

            <button type="submit" class="w-full py-4 bg-primary text-white text-lg font-bold rounded-xl shadow-lg shadow-primary/30 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                <span class="material-symbols-outlined">favorite</span> Mulai Donasi Rutin
            </button>
        </form>
    </div>
</section>

<script>
    function fmtRp(el) { var v = el.value.replace(/[^0-9]/g, ''); el.value = v ? parseInt(v, 10).toLocaleString('id-ID') : ''; }
    function setNom(n) { var el = document.querySelector('input[name=amount]'); el.value = n.toLocaleString('id-ID'); }
</script>
<?= $this->endSection() ?>
