<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<?php $isAdminMasjid = is_admin_masjid(); ?>
<div class="px-8 py-8">
    <div class="max-w-4xl mx-auto">

        <div class="mb-8">
            <h1 class="text-3xl font-black text-[#111816] dark:text-white tracking-tight">Grup Jamaah</h1>
            <p class="text-[#608a7e] mt-1">Daftarkan grup Telegram atau WhatsApp yang akan menerima siaran masjid.</p>
        </div>

        <?php foreach (['error' => 'rose', 'success' => 'emerald'] as $jenis => $warna): ?>
            <?php if (session()->getFlashdata($jenis)): ?>
                <div class="bg-<?= $warna ?>-50 text-<?= $warna ?>-600 p-4 rounded-xl mb-6 flex items-center gap-3">
                    <span class="material-symbols-outlined"><?= $jenis === 'error' ? 'error' : 'check_circle' ?></span>
                    <p class="text-sm font-medium"><?= esc(session()->getFlashdata($jenis)) ?></p>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php // Kanal yang belum disetel disebutkan lebih dulu — daripada
              // pengurus mendaftarkan grup lalu bingung kenapa tak ada yang
              // terkirim. ?>
        <?php if (!$telegramSiap || !$whatsappSiap): ?>
            <div class="bg-amber-50 text-amber-700 p-4 rounded-xl mb-6 text-sm">
                <p class="font-bold mb-1 flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">info</span> Perlu disiapkan lebih dulu
                </p>
                <ul class="list-disc ml-8 space-y-0.5">
                    <?php if (!$telegramSiap): ?>
                        <li><strong>Telegram</strong> &mdash; Token Bot masjid belum diisi di Pengaturan Masjid.</li>
                    <?php endif; ?>
                    <?php if (!$whatsappSiap): ?>
                        <li><strong>WhatsApp</strong> &mdash; Kunci Gateway WhatsApp masjid belum diisi di Pengaturan Masjid.
                            Perlu akun Fonnte sendiri; Telegram tidak memerlukannya dan gratis.</li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Daftar grup -->
        <div class="bg-white dark:bg-white/5 rounded-2xl border border-[#e5e7eb] dark:border-white/10 overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-[#f0f5f3] dark:bg-white/5 text-[#608a7e] text-xs font-bold uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Nama Grup</th>
                            <th class="px-6 py-4">Kanal</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e5e7eb] dark:divide-white/10">
                        <?php if (empty($groups)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-[#608a7e]">
                                    <span class="material-symbols-outlined text-4xl mb-2 block opacity-40">groups</span>
                                    Belum ada grup terdaftar.
                                </td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($groups as $g): ?>
                            <tr class="hover:bg-[#f0f5f3]/50 dark:hover:bg-white/5">
                                <td class="px-6 py-4">
                                    <p class="font-bold text-[#111816] dark:text-white"><?= esc($g['name']) ?></p>
                                    <p class="text-[10px] text-slate-400 font-mono"><?= esc($g['group_id']) ?></p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold <?= $g['channel'] === 'telegram' ? 'bg-sky-100 text-sky-700' : 'bg-green-100 text-green-700' ?>">
                                        <?= $g['channel'] === 'telegram' ? 'Telegram' : 'WhatsApp' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs font-bold <?= $g['is_active'] ? 'text-green-600' : 'text-slate-400' ?>">
                                        <?= $g['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-3">
                                        <a href="<?= base_url('dashboard/broadcast/groups/test/' . $g['id']) ?>"
                                           class="text-[#608a7e] hover:text-primary" title="Uji kirim ke grup ini">
                                            <span class="material-symbols-outlined text-xl">send</span>
                                        </a>
                                        <?php if ($isAdminMasjid): ?>
                                            <a href="<?= base_url('dashboard/broadcast/groups/delete/' . $g['id']) ?>"
                                               onclick="return confirm('Hapus grup <?= esc($g['name'], 'js') ?> dari daftar tujuan siaran?')"
                                               class="text-[#608a7e] hover:text-red-500" title="Hapus">
                                                <span class="material-symbols-outlined text-xl">delete</span>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if ($isAdminMasjid): ?>
        <!-- Tambah grup -->
        <div class="bg-white dark:bg-white/5 rounded-2xl border border-[#e5e7eb] dark:border-white/10 p-6">
            <h2 class="text-lg font-bold text-[#111816] dark:text-white mb-1">Daftarkan Grup Baru</h2>
            <p class="text-xs text-[#608a7e] mb-6">Hanya grup yang terdaftar di sini yang menerima siaran dan dilayani bot.</p>

            <form action="<?= base_url('dashboard/broadcast/groups/save') ?>" method="POST" class="space-y-5">
                <?= csrf_field() ?>

                <div class="grid md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold mb-2">Nama Grup</label>
                        <input type="text" name="name" value="<?= old('name') ?>" required
                               placeholder="Contoh: Info Jamaah Al Habsya"
                               class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl focus:ring-2 focus:ring-primary py-3 px-4 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-2">Kanal</label>
                        <select name="channel" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl focus:ring-2 focus:ring-primary py-3 px-4 text-sm">
                            <option value="telegram">Telegram</option>
                            <option value="whatsapp">WhatsApp</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2">ID Grup</label>
                    <input type="text" name="group_id" value="<?= old('group_id') ?>" required
                           placeholder="-1001234567890  atau  62812xxxx-1234567890@g.us"
                           class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl focus:ring-2 focus:ring-primary py-3 px-4 text-sm font-mono">
                    <div class="text-[11px] text-[#608a7e] mt-2 space-y-1 leading-relaxed">
                        <p><strong>Telegram:</strong> masukkan bot masjid ke grup, kirim satu pesan di grup itu,
                           lalu ID grupnya muncul di halaman ini. Diawali tanda minus.</p>
                        <p><strong>WhatsApp:</strong> ambil ID grup dari dasbor gateway (Fonnte), berakhiran <code>@g.us</code>.</p>
                    </div>
                </div>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded text-primary focus:ring-primary">
                    <span class="text-sm font-medium">Aktif &mdash; grup ini menerima siaran</span>
                </label>

                <button type="submit" class="px-8 py-3 bg-primary hover:bg-primary/90 text-white font-bold text-sm rounded-xl shadow-lg shadow-primary/20 transition-all">
                    Daftarkan Grup
                </button>
            </form>
        </div>
        <?php else: ?>
            <p class="text-sm text-[#608a7e] flex items-center gap-2">
                <span class="material-symbols-outlined text-base">lock</span>
                Hanya Admin Masjid yang dapat mendaftarkan atau menghapus grup.
            </p>
        <?php endif; ?>

    </div>
</div>
<?= $this->endSection() ?>
