<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<?php
    $labelAksi = ['create' => 'Buat', 'update' => 'Ubah', 'delete' => 'Hapus'];
    $labelEntitas = ['transaksi' => 'Transaksi Kas', 'berita' => 'Berita', 'program' => 'Program'];
?>
<div class="px-0 sm:px-4">
    <div class="max-w-5xl mx-auto">

        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-[#111816] dark:text-white tracking-tight">Audit API / MCP</h1>
                <p class="text-[#608a7e] text-sm mt-1">Catatan setiap perubahan data lewat token API &amp; agen AI.</p>
            </div>
            <a href="<?= base_url('dashboard/mcp') ?>" class="text-sm font-bold text-primary hover:underline whitespace-nowrap">
                &larr; Kembali ke Panduan
            </a>
        </div>

        <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl p-4 mb-6 text-sm flex gap-3">
            <span class="material-symbols-outlined shrink-0">shield</span>
            <p>
                Token API/MCP dapat <strong>membuat, mengubah, dan menghapus</strong> data masjid ini.
                Semua perubahan tercatat di sini &mdash; termasuk <strong>percobaan yang ditolak</strong>.
                Bila ada baris yang tidak Anda kenali, segera <a href="<?= base_url('dashboard/mcp') ?>" class="underline font-bold">cabut tokennya</a>.
            </p>
        </div>

        <div class="bg-white dark:bg-white/5 rounded-2xl border border-[#e5e7eb] dark:border-white/10 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-[#f0f5f3] dark:bg-white/5 text-[#608a7e] text-xs font-bold uppercase tracking-wider">
                        <tr>
                            <th class="px-4 sm:px-6 py-4">Waktu</th>
                            <th class="px-4 sm:px-6 py-4">Sumber</th>
                            <th class="px-4 sm:px-6 py-4">Aksi</th>
                            <th class="px-4 sm:px-6 py-4">Data</th>
                            <th class="px-4 sm:px-6 py-4">Hasil</th>
                            <th class="px-4 sm:px-6 py-4">IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e5e7eb] dark:divide-white/10">
                        <?php if (empty($riwayat)): ?>
                            <tr><td colspan="6" class="px-6 py-12 text-center text-[#608a7e]">
                                <span class="material-symbols-outlined text-4xl mb-2 block opacity-40">history</span>
                                Belum ada perubahan lewat API atau MCP.
                            </td></tr>
                        <?php endif; ?>
                        <?php foreach ($riwayat as $r): ?>
                            <tr class="hover:bg-[#f0f5f3]/50 dark:hover:bg-white/5">
                                <td class="px-4 sm:px-6 py-3 text-[#608a7e]"><?= esc($r['created_at']) ?></td>
                                <td class="px-4 sm:px-6 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $r['source'] === 'mcp' ? 'bg-purple-100 text-purple-700' : 'bg-sky-100 text-sky-700' ?>">
                                        <?= $r['source'] === 'mcp' ? 'Agen AI' : 'API' ?>
                                    </span>
                                </td>
                                <td class="px-4 sm:px-6 py-3 font-bold text-[#111816] dark:text-white">
                                    <?= esc($labelAksi[$r['action']] ?? $r['action']) ?>
                                </td>
                                <td class="px-4 sm:px-6 py-3 text-[#608a7e]">
                                    <?= esc($labelEntitas[$r['entity']] ?? $r['entity']) ?>
                                    <?= $r['entity_id'] ? ' #' . esc($r['entity_id']) : '' ?>
                                </td>
                                <td class="px-4 sm:px-6 py-3">
                                    <?php if ($r['status'] === 'success'): ?>
                                        <span class="text-xs font-bold text-green-600">Berhasil</span>
                                    <?php else: ?>
                                        <span class="text-xs font-bold text-rose-600">Ditolak</span>
                                        <?php if (!empty($r['message'])): ?>
                                            <p class="text-[11px] text-[#608a7e] whitespace-normal max-w-xs"><?= esc($r['message']) ?></p>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 sm:px-6 py-3 text-[10px] text-slate-400 font-mono"><?= esc($r['ip'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <p class="text-[11px] text-[#608a7e] mt-4">Menampilkan maksimal 200 catatan terbaru.</p>

    </div>
</div>
<?= $this->endSection() ?>
