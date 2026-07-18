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
                <p class="text-[#608a7e]">Tulis pengumuman yang akan dikirim ke grup jamaah.</p>
            </div>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-rose-50 text-rose-600 p-4 rounded-xl mb-6 flex items-center gap-3">
                <span class="material-symbols-outlined">error</span>
                <p class="text-sm font-medium"><?= esc(session()->getFlashdata('error')) ?></p>
            </div>
        <?php endif; ?>

        <?php // Tanpa grup terdaftar, formulirnya sia-sia: tunjukkan jalan
              // keluarnya alih-alih membiarkan pengurus menulis lalu gagal. ?>
        <?php if (empty($groups)): ?>
            <div class="bg-amber-50 text-amber-800 p-6 rounded-2xl flex gap-4">
                <span class="material-symbols-outlined shrink-0">groups</span>
                <div>
                    <p class="font-bold mb-1">Belum ada grup jamaah terdaftar</p>
                    <p class="text-sm mb-3">Siaran dikirim ke grup Telegram atau WhatsApp. Daftarkan grupnya lebih dulu.</p>
                    <a href="<?= base_url('dashboard/broadcast/groups') ?>" class="inline-flex items-center gap-1 font-bold text-sm underline">
                        Kelola Grup Jamaah <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                </div>
            </div>
        <?php else: ?>

        <form action="<?= base_url('dashboard/broadcast/send') ?>" method="POST" class="space-y-6" onsubmit="return confirm('Kirim pengumuman ini ke grup jamaah yang dipilih? Pesan yang sudah terkirim tidak bisa ditarik kembali.');">
            <?= csrf_field() ?>
            <div class="bg-white dark:bg-white/5 p-8 rounded-3xl border border-[#e5e7eb] dark:border-white/10 space-y-6">

                <div>
                    <label class="block text-sm font-bold mb-2 text-[#111816] dark:text-white">Grup Tujuan</label>
                    <div class="space-y-2">
                        <?php foreach ($groups as $g): ?>
                            <?php $siap = $g['channel'] === 'telegram' ? $telegramSiap : $whatsappSiap; ?>
                            <label class="flex items-center gap-3 p-3 rounded-xl bg-[#f0f5f3] dark:bg-white/5 cursor-pointer hover:bg-primary/5 transition-colors">
                                <input type="checkbox" name="group_ids[]" value="<?= $g['id'] ?>" class="rounded text-primary focus:ring-primary">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $g['channel'] === 'telegram' ? 'bg-sky-100 text-sky-700' : 'bg-green-100 text-green-700' ?>">
                                    <?= $g['channel'] === 'telegram' ? 'Telegram' : 'WhatsApp' ?>
                                </span>
                                <span class="text-sm font-bold text-[#111816] dark:text-white"><?= esc($g['name']) ?></span>
                                <?php if (!$siap): ?>
                                    <span class="ml-auto text-[10px] font-bold text-amber-600">kanal belum disetel</span>
                                <?php endif; ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <p class="text-xs text-[#608a7e] mt-2">
                        <a href="<?= base_url('dashboard/broadcast/groups') ?>" class="underline">Kelola grup jamaah</a>
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2 text-[#111816] dark:text-white">Judul Pengumuman</label>
                    <input type="text" name="subject" required class="w-full bg-[#f0f5f3] dark:bg-background-dark border-none rounded-xl focus:ring-2 focus:ring-primary p-4 font-bold text-[#111816] dark:text-white placeholder-gray-400" placeholder="Contoh: Jadwal Sholat Jumat & Laporan Keuangan Bulan Ini">
                </div>

                <?php // Bantu susun via AI. Opsional sepenuhnya: pengurus tetap
                      // bisa mengetik langsung di kolom isi. Hasil AI mengisi
                      // kolom itu dan tetap dapat disunting sebelum dikirim. ?>
                <div class="bg-primary/5 border border-primary/15 rounded-2xl p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="material-symbols-outlined text-primary">auto_awesome</span>
                        <h3 class="font-bold text-[#111816] dark:text-white">Bantu Susun dengan AI</h3>
                        <span class="text-[10px] font-bold text-primary bg-primary/10 px-2 py-0.5 rounded-full">opsional</span>
                    </div>
                    <p class="text-xs text-[#608a7e] mb-3">
                        Tulis poin-poin singkat, AI akan merapikannya menjadi pengumuman.
                        Anda tetap bisa menyunting hasilnya sebelum dikirim.
                    </p>
                    <textarea id="aiPoin" rows="4" class="w-full bg-white dark:bg-white/5 border border-primary/20 rounded-xl focus:ring-2 focus:ring-primary p-3 text-sm mb-3" placeholder="Contoh:&#10;- kerja bakti sabtu pagi&#10;- bawa alat kebersihan sendiri&#10;- lanjut sarapan bersama"></textarea>
                    <div class="flex flex-wrap items-center gap-3">
                        <select id="aiNada" class="bg-white dark:bg-white/5 border border-primary/20 rounded-lg text-sm py-2 px-3">
                            <option value="resmi">Nada: Resmi</option>
                            <option value="hangat">Nada: Hangat</option>
                            <option value="ringkas">Nada: Ringkas</option>
                            <option value="duka">Nada: Belasungkawa</option>
                        </select>
                        <button type="button" id="aiDraftBtn" class="bg-primary text-white px-5 py-2 rounded-lg text-sm font-bold flex items-center gap-2 hover:bg-emerald-900 transition-all">
                            <span class="material-symbols-outlined text-base" id="aiIcon">auto_awesome</span>
                            <span id="aiBtnText">Susun</span>
                        </button>
                        <span id="aiMsg" class="text-xs font-medium"></span>
                    </div>
                    <p class="text-[10px] text-amber-600 mt-3 flex items-start gap-1">
                        <span class="material-symbols-outlined text-xs">info</span>
                        Periksa hasilnya sebelum kirim. AI diminta tidak mengarang tanggal/angka,
                        tetapi tetap tanggung jawab Anda memastikan isinya benar.
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2 text-[#111816] dark:text-white">Isi Pengumuman</label>
                    <textarea name="content" id="aiContent" rows="10" required class="w-full bg-[#f0f5f3] dark:bg-background-dark border-none rounded-xl focus:ring-2 focus:ring-primary p-4 text-[#111816] dark:text-white font-medium" placeholder="Tulis pengumuman Anda di sini..."><?= old('content') ?></textarea>
                    <p class="text-xs text-gray-500 mt-2">
                        Tulis apa adanya. Teks tebal boleh memakai &lt;b&gt;. Tag lain dibuang otomatis
                        karena Telegram dan WhatsApp tidak menerimanya.
                    </p>
                </div>

            </div>

            <script>
            (function () {
                const btn = document.getElementById('aiDraftBtn');
                if (!btn) return;
                const poin = document.getElementById('aiPoin');
                const nada = document.getElementById('aiNada');
                const content = document.getElementById('aiContent');
                const msg = document.getElementById('aiMsg');
                const btnText = document.getElementById('aiBtnText');
                const icon = document.getElementById('aiIcon');

                btn.addEventListener('click', async function () {
                    if (poin.value.trim() === '') {
                        msg.textContent = 'Tulis poin-poinnya dulu.';
                        msg.className = 'text-xs font-medium text-amber-600';
                        return;
                    }
                    // Kalau kolom isi sudah ada teks, minta konfirmasi supaya draf
                    // tidak menimpa tulisan pengurus tanpa sengaja.
                    if (content.value.trim() !== '' &&
                        !confirm('Ganti isi pengumuman yang sudah ada dengan hasil AI?')) {
                        return;
                    }

                    btn.disabled = true;
                    icon.classList.add('animate-spin');
                    btnText.textContent = 'Menyusun...';
                    msg.textContent = '';

                    try {
                        const fd = new FormData();
                        fd.append('poin', poin.value);
                        fd.append('nada', nada.value);
                        fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

                        const res = await fetch('<?= base_url('dashboard/broadcast/draft') ?>', {
                            method: 'POST',
                            headers: { 'X-Requested-With': 'XMLHttpRequest' },
                            body: fd,
                        });
                        const data = await res.json();

                        if (data.status === 'success') {
                            content.value = data.draft;
                            content.focus();
                            msg.textContent = 'Draf dibuat. Silakan periksa & sunting.';
                            msg.className = 'text-xs font-medium text-emerald-600';
                        } else {
                            msg.textContent = data.message || 'Gagal menyusun draf.';
                            msg.className = 'text-xs font-medium text-rose-600';
                        }
                    } catch (e) {
                        msg.textContent = 'Gagal terhubung. Coba lagi.';
                        msg.className = 'text-xs font-medium text-rose-600';
                    } finally {
                        btn.disabled = false;
                        icon.classList.remove('animate-spin');
                        btnText.textContent = 'Susun';
                    }
                });
            })();
            </script>

            <div class="flex justify-end gap-4">
                <button type="submit" class="bg-primary text-white px-8 py-4 rounded-xl font-bold hover:bg-emerald-900 transition-all shadow-lg shadow-primary/20 flex items-center gap-2">
                    <span class="material-symbols-outlined">send</span>
                    <span>Kirim Siaran Sekarang</span>
                </button>
            </div>

        </form>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
