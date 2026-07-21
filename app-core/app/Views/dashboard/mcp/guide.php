<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<?php
    $isAdminMasjid = is_admin_masjid();
    $token   = $masjid['mcp_token'] ?? '';
    $aktif   = $token !== '';
    $contoh  = $aktif ? $token : 'TOKEN_MASJID_ANDA';
?>
<div class="px-8 py-8">
    <div class="max-w-3xl mx-auto">

        <div class="mb-8">
            <h1 class="text-3xl font-black text-[#111816] dark:text-white tracking-tight">API / MCP Masjid</h1>
            <p class="text-[#608a7e] mt-1">Hubungkan asisten AI (seperti Claude) ke data masjid Anda.</p>
        </div>

        <?php foreach (['error' => 'rose', 'success' => 'emerald'] as $jenis => $warna): ?>
            <?php if (session()->getFlashdata($jenis)): ?>
                <div class="bg-<?= $warna ?>-50 text-<?= $warna ?>-600 p-4 rounded-xl mb-6 flex items-center gap-3">
                    <span class="material-symbols-outlined"><?= $jenis === 'error' ? 'error' : 'check_circle' ?></span>
                    <p class="text-sm font-medium"><?= esc(session()->getFlashdata($jenis)) ?></p>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <!-- Apa ini -->
        <div class="bg-primary/5 border border-primary/15 rounded-2xl p-6 mb-6">
            <h2 class="font-bold text-[#111816] dark:text-white mb-2 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">smart_toy</span> Apa ini?
            </h2>
            <p class="text-sm text-[#374151] dark:text-slate-300 leading-relaxed">
                Fitur ini membuka "pintu" agar asisten AI bisa <strong>membaca</strong> data masjid Anda
                &mdash; misalnya menanyakan saldo kas, jadwal sholat, atau donasi terbaru &mdash; lalu
                menjawabnya untuk Anda. Aksesnya <strong>hanya membaca, tidak mengubah apa pun</strong>,
                dan <strong>hanya untuk masjid ini</strong>. Butuh sedikit pengetahuan teknis untuk
                menghubungkannya; bila ragu, minta bantuan pengembang Anda.
            </p>
        </div>

        <!-- Langkah 1: Token -->
        <div class="bg-white dark:bg-white/5 rounded-2xl border border-[#e5e7eb] dark:border-white/10 p-6 mb-6">
            <h2 class="font-bold text-[#111816] dark:text-white mb-1">Langkah 1 — Token Akses</h2>
            <p class="text-xs text-[#608a7e] mb-4">Token adalah "kunci" rahasia. Siapa pun yang memegangnya bisa membaca data masjid ini. Jaga baik-baik.</p>

            <?php if ($aktif): ?>
                <div class="flex items-center gap-2 mb-3">
                    <input readonly value="<?= esc($token) ?>" onclick="this.select()" id="tokenBox"
                           class="flex-1 rounded-lg border-[#dbe6e3] dark:bg-white/5 dark:border-white/10 font-mono text-xs bg-[#f0f5f3]"/>
                    <button type="button" onclick="salin('tokenBox', this)"
                            class="px-3 py-2 bg-primary text-white rounded-lg text-xs font-bold whitespace-nowrap">Salin</button>
                </div>
                <?php if ($isAdminMasjid): ?>
                    <div class="flex gap-4">
                        <a href="<?= base_url('dashboard/mcp/generate') ?>" onclick="return confirm('Buat token baru? Token lama langsung tidak berlaku.')"
                           class="text-xs font-bold text-primary hover:underline">Buat ulang</a>
                        <a href="<?= base_url('dashboard/mcp/revoke') ?>" onclick="return confirm('Cabut token? Akses AI dinonaktifkan.')"
                           class="text-xs font-bold text-red-500 hover:underline">Cabut (matikan akses)</a>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <?php if ($isAdminMasjid): ?>
                    <a href="<?= base_url('dashboard/mcp/generate') ?>"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white rounded-xl text-sm font-bold">
                        <span class="material-symbols-outlined text-base">key</span> Buat Token
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

        <!-- Langkah 2: Alamat -->
        <div class="bg-white dark:bg-white/5 rounded-2xl border border-[#e5e7eb] dark:border-white/10 p-6 mb-6">
            <h2 class="font-bold text-[#111816] dark:text-white mb-3">Langkah 2 — Alamat (Endpoint)</h2>
            <div class="flex items-center gap-2">
                <input readonly value="<?= esc($endpoint) ?>" onclick="this.select()" id="epBox"
                       class="flex-1 rounded-lg border-[#dbe6e3] dark:bg-white/5 dark:border-white/10 font-mono text-xs bg-[#f0f5f3]"/>
                <button type="button" onclick="salin('epBox', this)"
                        class="px-3 py-2 bg-primary text-white rounded-lg text-xs font-bold">Salin</button>
            </div>
            <p class="text-xs text-[#608a7e] mt-2">
                Kirim permintaan <strong>POST</strong> ke alamat ini dengan header
                <code>Authorization: Bearer &lt;token&gt;</code>. Format isi: JSON-RPC 2.0 (MCP).
            </p>
        </div>

        <!-- Langkah 3: Yang bisa ditanyakan -->
        <div class="bg-white dark:bg-white/5 rounded-2xl border border-[#e5e7eb] dark:border-white/10 p-6 mb-6">
            <h2 class="font-bold text-[#111816] dark:text-white mb-3">Yang Bisa Ditanyakan AI (Tools)</h2>
            <div class="space-y-3">
                <?php foreach ($tools as $t): ?>
                    <div class="flex gap-3">
                        <code class="text-xs font-bold text-primary bg-primary/10 px-2 py-1 rounded h-fit whitespace-nowrap"><?= esc($t['name']) ?></code>
                        <p class="text-sm text-[#374151] dark:text-slate-300"><?= esc($t['description']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <p class="text-[11px] text-[#608a7e] mt-4">Semua bersifat <strong>hanya-baca</strong> dan otomatis terbatas pada masjid ini.</p>
        </div>

        <!-- Langkah 4: Contoh -->
        <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 mb-6">
            <div class="flex items-center justify-between mb-3">
                <h2 class="font-bold text-white">Contoh — Cek Saldo Kas</h2>
                <button type="button" onclick="salin('curlBox', this)" class="px-3 py-1.5 bg-white/10 text-white rounded-lg text-xs font-bold">Salin</button>
            </div>
            <pre id="curlBox" class="text-[11px] text-emerald-300 font-mono overflow-x-auto leading-relaxed whitespace-pre">curl -X POST <?= esc($endpoint) ?> \
  -H "Authorization: Bearer <?= esc($contoh) ?>" \
  -H "Content-Type: application/json" \
  -d '{"jsonrpc":"2.0","id":1,"method":"tools/call","params":{"name":"cek_kas"}}'</pre>
            <p class="text-[11px] text-slate-400 mt-3">
                Untuk melihat semua tool: ganti <code class="text-slate-300">params</code> menjadi
                <code class="text-slate-300">"method":"tools/list"</code>.
            </p>
        </div>

        <!-- Untuk agen AI (Claude) -->
        <div class="bg-white dark:bg-white/5 rounded-2xl border border-[#e5e7eb] dark:border-white/10 p-6">
            <h2 class="font-bold text-[#111816] dark:text-white mb-2">Menghubungkan ke Asisten AI</h2>
            <p class="text-sm text-[#374151] dark:text-slate-300 leading-relaxed">
                Alamat &amp; token di atas adalah <strong>MCP server</strong> standar. Pada aplikasi AI yang
                mendukung MCP (mis. Claude), tambahkan sebagai server MCP jenis HTTP: isi alamat endpoint,
                dan sertakan header <code>Authorization: Bearer &lt;token&gt;</code>. Setelah terhubung, AI
                dapat menjawab pertanyaan seputar kas, jadwal sholat, dan donasi masjid Anda.
            </p>
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
