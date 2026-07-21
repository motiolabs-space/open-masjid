<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<?php
    $isAdminMasjid = is_admin_masjid();
    $token  = $masjid['api_token'] ?? '';
    $aktif  = $token !== '';
    $contoh = $aktif ? $token : 'TOKEN_API_ANDA';

    $endpoints = [
        ['kas',            'GET', 'Ringkasan kas bulan berjalan (pemasukan, pengeluaran, saldo).'],
        ['jadwal-sholat',  'GET', 'Jadwal sholat hari ini (sudah termasuk koreksi menit).'],
        ['donasi',         'GET', 'Donasi berhasil terbaru. Opsi: ?limit=10 (maks 50).'],
        ['profil',         'GET', 'Data publik masjid: nama, alamat, telepon, zona waktu.'],
    ];
?>
<div class="px-8 py-8">
    <div class="max-w-3xl mx-auto">

        <div class="mb-8">
            <h1 class="text-3xl font-black text-[#111816] dark:text-white tracking-tight">REST API Masjid</h1>
            <p class="text-[#608a7e] mt-1">Ambil data masjid dari aplikasi atau website lain, dalam format JSON.</p>
        </div>

        <?php foreach (['error' => 'rose', 'success' => 'emerald'] as $jenis => $warna): ?>
            <?php if (session()->getFlashdata($jenis)): ?>
                <div class="bg-<?= $warna ?>-50 text-<?= $warna ?>-600 p-4 rounded-xl mb-6 flex items-center gap-3">
                    <span class="material-symbols-outlined"><?= $jenis === 'error' ? 'error' : 'check_circle' ?></span>
                    <p class="text-sm font-medium"><?= esc(session()->getFlashdata($jenis)) ?></p>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <!-- Beda dengan MCP -->
        <div class="bg-primary/5 border border-primary/15 rounded-2xl p-6 mb-6">
            <h2 class="font-bold text-[#111816] dark:text-white mb-2 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">api</span> Apa ini?
            </h2>
            <p class="text-sm text-[#374151] dark:text-slate-300 leading-relaxed">
                REST API memberi <strong>aplikasi atau website lain</strong> cara membaca data masjid ini
                dalam format JSON &mdash; misalnya menampilkan jadwal sholat di website desa, atau saldo kas
                di aplikasi internal. Aksesnya <strong>hanya membaca</strong> dan <strong>hanya masjid ini</strong>.
                Untuk asisten AI (Claude), gunakan
                <a href="<?= base_url('dashboard/mcp') ?>" class="text-primary font-bold hover:underline">MCP</a>;
                REST API ini untuk integrasi biasa.
            </p>
        </div>

        <!-- Token -->
        <div class="bg-white dark:bg-white/5 rounded-2xl border border-[#e5e7eb] dark:border-white/10 p-6 mb-6">
            <h2 class="font-bold text-[#111816] dark:text-white mb-1">Token Akses</h2>
            <p class="text-xs text-[#608a7e] mb-4">Kunci rahasia. Jaga baik-baik &mdash; siapa pun yang memegangnya bisa membaca data masjid ini. Terpisah dari token MCP.</p>

            <?php if ($aktif): ?>
                <div class="flex items-center gap-2 mb-3">
                    <input readonly value="<?= esc($token) ?>" onclick="this.select()" id="tokenBox"
                           class="flex-1 rounded-lg border-[#dbe6e3] dark:bg-white/5 dark:border-white/10 font-mono text-xs bg-[#f0f5f3]"/>
                    <button type="button" onclick="salin('tokenBox', this)"
                            class="px-3 py-2 bg-primary text-white rounded-lg text-xs font-bold whitespace-nowrap">Salin</button>
                </div>
                <?php if ($isAdminMasjid): ?>
                    <div class="flex gap-4">
                        <a href="<?= base_url('dashboard/api/generate') ?>" onclick="return confirm('Buat token baru? Token lama langsung tidak berlaku.')"
                           class="text-xs font-bold text-primary hover:underline">Buat ulang</a>
                        <a href="<?= base_url('dashboard/api/revoke') ?>" onclick="return confirm('Cabut token? Akses API dinonaktifkan.')"
                           class="text-xs font-bold text-red-500 hover:underline">Cabut (matikan akses)</a>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <?php if ($isAdminMasjid): ?>
                    <a href="<?= base_url('dashboard/api/generate') ?>"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white rounded-xl text-sm font-bold">
                        <span class="material-symbols-outlined text-base">key</span> Buat Token API
                    </a>
                    <p class="text-xs text-[#608a7e] mt-2">Belum aktif. Buat token untuk mulai.</p>
                <?php else: ?>
                    <p class="text-sm text-[#608a7e] flex items-center gap-2">
                        <span class="material-symbols-outlined text-base">lock</span>
                        Belum ada token. Hanya Admin Masjid yang dapat membuatnya.
                    </p>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Cara pakai -->
        <div class="bg-white dark:bg-white/5 rounded-2xl border border-[#e5e7eb] dark:border-white/10 p-6 mb-6">
            <h2 class="font-bold text-[#111816] dark:text-white mb-3">Cara Pakai</h2>
            <p class="text-sm text-[#374151] dark:text-slate-300 mb-3">
                Kirim permintaan <strong>GET</strong> ke alamat endpoint di bawah, sertakan header
                <code>Authorization: Bearer &lt;token&gt;</code>. Balasan berformat JSON:
                <code>{"status":"success","data":...}</code>.
            </p>
            <div class="bg-slate-900 rounded-xl p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-400">Contoh — cek kas</span>
                    <button type="button" onclick="salin('curlBox', this)" class="px-3 py-1 bg-white/10 text-white rounded text-xs font-bold">Salin</button>
                </div>
                <pre id="curlBox" class="text-[11px] text-emerald-300 font-mono overflow-x-auto leading-relaxed whitespace-pre">curl <?= esc($baseUrl) ?>/kas \
  -H "Authorization: Bearer <?= esc($contoh) ?>"</pre>
            </div>
        </div>

        <!-- Daftar endpoint -->
        <div class="bg-white dark:bg-white/5 rounded-2xl border border-[#e5e7eb] dark:border-white/10 p-6">
            <h2 class="font-bold text-[#111816] dark:text-white mb-4">Daftar Endpoint</h2>
            <div class="space-y-4">
                <?php foreach ($endpoints as [$path, $method, $desc]): ?>
                    <div class="border-b border-[#e5e7eb] dark:border-white/10 pb-4 last:border-0 last:pb-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[10px] font-black text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded"><?= $method ?></span>
                            <code class="text-xs font-mono text-[#111816] dark:text-white"><?= esc($baseUrl) ?>/<?= esc($path) ?></code>
                        </div>
                        <p class="text-sm text-[#608a7e]"><?= esc($desc) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <p class="text-[11px] text-[#608a7e] mt-4">Semua <strong>hanya-baca</strong> dan otomatis terbatas pada masjid ini.</p>
        </div>

    </div>
</div>

<script>
function salin(elId, btn) {
    const el = document.getElementById(elId);
    const teks = el.value !== undefined ? el.value : el.innerText;
    navigator.clipboard.writeText(teks).then(function () {
        const asli = btn.innerText;
        btn.innerText = 'Tersalin';
        setTimeout(function () { btn.innerText = asli; }, 1500);
    });
}
</script>
<?= $this->endSection() ?>
