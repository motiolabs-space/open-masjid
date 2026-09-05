<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<div class="px-4 sm:px-8 py-8">
    <div class="max-w-2xl mx-auto">

        <?php foreach (['error' => 'rose', 'success' => 'emerald'] as $j => $w): ?>
            <?php if (session()->getFlashdata($j)): ?>
                <div class="bg-<?= $w ?>-50 text-<?= $w ?>-600 p-4 rounded-xl mb-6 flex items-center gap-3">
                    <span class="material-symbols-outlined"><?= $j === 'error' ? 'error' : 'check_circle' ?></span>
                    <p class="text-sm font-medium"><?= esc(session()->getFlashdata($j)) ?></p>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-black text-[#111816] dark:text-white tracking-tight">Kirim Notifikasi</h1>
            <p class="text-[#608a7e] text-sm mt-1"><?= number_format($jumlahSub, 0, ',', '.') ?> perangkat berlangganan notifikasi masjid ini.</p>
        </div>

        <?php if (! $vapidSiap): ?>
            <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-xl p-4 mb-6 text-sm flex gap-3">
                <span class="material-symbols-outlined shrink-0">warning</span>
                <p>Kunci VAPID belum diatur di server. Notifikasi akan <strong>tersimpan</strong> tetapi <strong>belum terkirim</strong> sampai admin server menjalankan <code>php spark push:vapid</code> dan mengisi <code>.env</code>.</p>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('dashboard/notifikasi/kirim') ?>" method="post" class="bg-white dark:bg-white/5 rounded-2xl border border-[#e5e7eb] dark:border-white/10 p-6 space-y-5">
            <?= csrf_field() ?>
            <div>
                <label class="block text-sm font-bold mb-2">Judul</label>
                <input type="text" name="title" required maxlength="150" placeholder="mis. Kajian Ahad malam ini" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl px-4 py-3 text-sm">
            </div>
            <div>
                <label class="block text-sm font-bold mb-2">Isi Pesan</label>
                <textarea name="body" rows="3" maxlength="500" placeholder="Ba'da Isya di aula utama. Terbuka untuk umum." class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl px-4 py-3 text-sm"></textarea>
            </div>
            <div>
                <label class="block text-sm font-bold mb-2">Tautan Tujuan (opsional)</label>
                <input type="url" name="url" placeholder="https://masj.id/nama-masjid/program/..." class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl px-4 py-3 text-sm">
                <p class="text-xs text-[#608a7e] mt-1">Halaman yang dibuka saat notifikasi diklik.</p>
            </div>
            <div class="flex justify-end">
                <button type="submit" onclick="return confirm('Kirim notifikasi ke <?= (int) $jumlahSub ?> perangkat?')" class="bg-primary text-white px-6 py-3 rounded-xl font-bold text-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">send</span> Kirim Notifikasi
                </button>
            </div>
        </form>

        <?php if (! empty($terakhir)): ?>
            <h2 class="text-sm font-bold text-[#608a7e] uppercase tracking-wider mt-8 mb-3">Notifikasi Terkini</h2>
            <div class="space-y-2">
                <?php foreach ($terakhir as $m): ?>
                    <div class="bg-white dark:bg-white/5 border border-[#e5e7eb] dark:border-white/10 rounded-xl p-4">
                        <p class="font-bold text-sm"><?= esc($m['title']) ?></p>
                        <?php if (!empty($m['body'])): ?><p class="text-xs text-[#608a7e] mt-0.5"><?= esc($m['body']) ?></p><?php endif; ?>
                        <p class="text-[10px] text-slate-400 mt-1"><?= date('d M Y H:i', strtotime($m['created_at'])) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
