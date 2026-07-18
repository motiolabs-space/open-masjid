<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<?php
    $isAdminMasjid = is_admin_masjid();
    $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $jenisLabel = ['jadwal_sholat' => 'Jadwal Sholat Harian', 'laporan_kas' => 'Laporan Kas'];
?>
<div class="px-8 py-8">
    <div class="max-w-4xl mx-auto">

        <div class="mb-8">
            <h1 class="text-3xl font-black text-[#111816] dark:text-white tracking-tight">Pengingat Terjadwal</h1>
            <p class="text-[#608a7e] mt-1">Kirim otomatis ke grup jamaah: jadwal sholat harian atau laporan kas.</p>
        </div>

        <?php foreach (['error' => 'rose', 'success' => 'emerald'] as $jenis => $warna): ?>
            <?php if (session()->getFlashdata($jenis)): ?>
                <div class="bg-<?= $warna ?>-50 text-<?= $warna ?>-600 p-4 rounded-xl mb-6 flex items-center gap-3">
                    <span class="material-symbols-outlined"><?= $jenis === 'error' ? 'error' : 'check_circle' ?></span>
                    <p class="text-sm font-medium"><?= esc(session()->getFlashdata($jenis)) ?></p>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if (empty($groups)): ?>
            <div class="bg-amber-50 text-amber-800 p-6 rounded-2xl flex gap-4 mb-8">
                <span class="material-symbols-outlined shrink-0">groups</span>
                <div>
                    <p class="font-bold mb-1">Belum ada grup jamaah aktif</p>
                    <p class="text-sm mb-3">Pengingat dikirim ke grup. Daftarkan &amp; aktifkan grupnya lebih dulu.</p>
                    <a href="<?= base_url('dashboard/broadcast/groups') ?>" class="inline-flex items-center gap-1 font-bold text-sm underline">
                        Kelola Grup Jamaah <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <!-- Daftar pengingat -->
        <div class="bg-white dark:bg-white/5 rounded-2xl border border-[#e5e7eb] dark:border-white/10 overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-[#f0f5f3] dark:bg-white/5 text-[#608a7e] text-xs font-bold uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Pengingat</th>
                            <th class="px-6 py-4">Jadwal</th>
                            <th class="px-6 py-4">Grup</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e5e7eb] dark:divide-white/10">
                        <?php if (empty($reminders)): ?>
                            <tr><td colspan="5" class="px-6 py-10 text-center text-[#608a7e]">Belum ada pengingat.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($reminders as $r): ?>
                            <tr class="hover:bg-[#f0f5f3]/50 dark:hover:bg-white/5">
                                <td class="px-6 py-4 font-bold text-[#111816] dark:text-white">
                                    <?= esc($jenisLabel[$r['type']] ?? $r['type']) ?>
                                </td>
                                <td class="px-6 py-4 text-[#608a7e]">
                                    <?php
                                        $jam = substr((string) $r['time'], 0, 5);
                                        if ($r['frequency'] === 'harian') {
                                            echo 'Setiap hari, ' . esc($jam);
                                        } elseif ($r['frequency'] === 'mingguan') {
                                            echo 'Tiap ' . esc($hari[(int) $r['day_of_week']] ?? '?') . ', ' . esc($jam);
                                        } else {
                                            echo 'Tgl ' . esc((int) $r['day_of_month']) . ' tiap bulan, ' . esc($jam);
                                        }
                                    ?>
                                </td>
                                <td class="px-6 py-4 text-[#608a7e]"><?= esc($r['group_name'] ?? '-') ?></td>
                                <td class="px-6 py-4">
                                    <span class="text-xs font-bold <?= $r['is_active'] ? 'text-green-600' : 'text-slate-400' ?>">
                                        <?= $r['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <?php if ($isAdminMasjid): ?>
                                        <div class="flex justify-end gap-3">
                                            <a href="<?= base_url('dashboard/broadcast/reminders/toggle/' . $r['id']) ?>"
                                               class="text-xs font-bold <?= $r['is_active'] ? 'text-slate-400 hover:text-slate-600' : 'text-green-600' ?>">
                                                <?= $r['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>
                                            </a>
                                            <a href="<?= base_url('dashboard/broadcast/reminders/delete/' . $r['id']) ?>"
                                               onclick="return confirm('Hapus pengingat ini?')"
                                               class="text-[#608a7e] hover:text-red-500" title="Hapus">
                                                <span class="material-symbols-outlined text-xl">delete</span>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if ($isAdminMasjid && !empty($groups)): ?>
        <!-- Tambah pengingat -->
        <div class="bg-white dark:bg-white/5 rounded-2xl border border-[#e5e7eb] dark:border-white/10 p-6">
            <h2 class="text-lg font-bold text-[#111816] dark:text-white mb-5">Tambah Pengingat</h2>
            <form action="<?= base_url('dashboard/broadcast/reminders/save') ?>" method="POST" class="space-y-5">
                <?= csrf_field() ?>
                <div class="grid md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold mb-2">Jenis Pengingat</label>
                        <select name="type" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl focus:ring-2 focus:ring-primary py-3 px-4 text-sm">
                            <option value="jadwal_sholat">Jadwal Sholat Harian</option>
                            <option value="laporan_kas">Laporan Kas</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-2">Grup Tujuan</label>
                        <select name="group_id" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl focus:ring-2 focus:ring-primary py-3 px-4 text-sm">
                            <?php foreach ($groups as $g): ?>
                                <option value="<?= $g['id'] ?>"><?= esc($g['name']) ?> (<?= esc($g['channel']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sm font-bold mb-2">Frekuensi</label>
                        <select name="frequency" id="freq" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl focus:ring-2 focus:ring-primary py-3 px-4 text-sm">
                            <option value="harian">Setiap hari</option>
                            <option value="mingguan">Mingguan</option>
                            <option value="bulanan">Bulanan</option>
                        </select>
                    </div>
                    <div id="wrapDow" style="display:none">
                        <label class="block text-sm font-bold mb-2">Hari</label>
                        <select name="day_of_week" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl focus:ring-2 focus:ring-primary py-3 px-4 text-sm">
                            <?php foreach ($hari as $i => $h): ?><option value="<?= $i ?>"><?= $h ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div id="wrapDom" style="display:none">
                        <label class="block text-sm font-bold mb-2">Tanggal (1&ndash;28)</label>
                        <input type="number" name="day_of_month" min="1" max="28" value="1" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl focus:ring-2 focus:ring-primary py-3 px-4 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-2">Jam Kirim</label>
                        <input type="time" name="time" value="05:00" required class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl focus:ring-2 focus:ring-primary py-3 px-4 text-sm">
                    </div>
                </div>

                <p class="text-[11px] text-[#608a7e]">Jam mengikuti zona waktu masjid (atur di Pengaturan Masjid).</p>

                <button type="submit" class="px-8 py-3 bg-primary hover:bg-primary/90 text-white font-bold text-sm rounded-xl shadow-lg shadow-primary/20">
                    Tambah Pengingat
                </button>
            </form>
        </div>
        <?php elseif (!$isAdminMasjid): ?>
            <p class="text-sm text-[#608a7e] flex items-center gap-2">
                <span class="material-symbols-outlined text-base">lock</span>
                Hanya Admin Masjid yang dapat mengatur pengingat.
            </p>
        <?php endif; ?>

    </div>
</div>

<script>
(function () {
    const freq = document.getElementById('freq');
    if (!freq) return;
    const dow = document.getElementById('wrapDow');
    const dom = document.getElementById('wrapDom');
    function sync() {
        dow.style.display = freq.value === 'mingguan' ? '' : 'none';
        dom.style.display = freq.value === 'bulanan' ? '' : 'none';
    }
    freq.addEventListener('change', sync);
    sync();
})();
</script>
<?= $this->endSection() ?>
