<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<?php $isAdmin = is_admin_masjid(); ?>
<div class="px-4 sm:px-8 py-8">
    <div class="max-w-5xl mx-auto">

        <?php foreach (['error' => 'rose', 'success' => 'emerald'] as $j => $w): ?>
            <?php if (session()->getFlashdata($j)): ?>
                <div class="bg-<?= $w ?>-50 text-<?= $w ?>-600 p-4 rounded-xl mb-6 flex items-center gap-3">
                    <span class="material-symbols-outlined"><?= $j === 'error' ? 'error' : 'check_circle' ?></span>
                    <p class="text-sm font-medium"><?= esc(session()->getFlashdata($j)) ?></p>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-[#111816] dark:text-white tracking-tight">Relawan</h1>
                <p class="text-[#608a7e] text-sm mt-1"><?= $totalAktif ?> relawan aktif &middot; total <?= number_format($totalPoin, 0, ',', '.') ?> poin partisipasi</p>
            </div>
            <button onclick="bukaForm()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white rounded-xl text-sm font-bold self-start">
                <span class="material-symbols-outlined text-base">person_add</span> Rekrut Relawan
            </button>
        </div>

        <div class="bg-white dark:bg-white/5 rounded-2xl border border-[#e5e7eb] dark:border-white/10 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-[#f0f5f3] dark:bg-white/5 text-[#608a7e] text-xs font-bold uppercase tracking-wider">
                        <tr>
                            <th class="px-4 sm:px-6 py-4">Nama</th>
                            <th class="px-4 sm:px-6 py-4">Peran</th>
                            <th class="px-4 sm:px-6 py-4 text-center">Poin</th>
                            <th class="px-4 sm:px-6 py-4 text-center">Status</th>
                            <th class="px-4 sm:px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e5e7eb] dark:divide-white/10">
                        <?php if (empty($volunteers)): ?>
                            <tr><td colspan="5" class="px-6 py-12 text-center text-[#608a7e]">
                                <span class="material-symbols-outlined text-4xl mb-2 block opacity-40">diversity_3</span>
                                Belum ada relawan. Klik "Rekrut Relawan" untuk menambah.
                            </td></tr>
                        <?php endif; ?>
                        <?php foreach ($volunteers as $v): ?>
                            <tr class="hover:bg-[#f0f5f3]/50 dark:hover:bg-white/5">
                                <td class="px-4 sm:px-6 py-3">
                                    <p class="font-bold text-[#111816] dark:text-white"><?= esc($v['name']) ?></p>
                                    <?php if (!empty($v['phone'])): ?><p class="text-xs text-[#608a7e]"><?= esc($v['phone']) ?></p><?php endif; ?>
                                </td>
                                <td class="px-4 sm:px-6 py-3 text-[#608a7e]"><?= esc($v['role'] ?: '—') ?></td>
                                <td class="px-4 sm:px-6 py-3 text-center">
                                    <span class="inline-flex items-center gap-1 font-black text-amber-600"><span class="material-symbols-outlined text-base">stars</span><?= number_format($v['points'], 0, ',', '.') ?></span>
                                </td>
                                <td class="px-4 sm:px-6 py-3 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $v['status'] === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' ?>"><?= $v['status'] === 'active' ? 'Aktif' : 'Nonaktif' ?></span>
                                </td>
                                <td class="px-4 sm:px-6 py-3">
                                    <div class="flex gap-1 justify-end">
                                        <button onclick='beriPoin(<?= (int) $v['id'] ?>, <?= json_encode($v['name']) ?>)' title="Beri Poin" class="size-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center hover:bg-amber-500 hover:text-white"><span class="material-symbols-outlined text-base">add_circle</span></button>
                                        <a href="<?= base_url('dashboard/relawan/sertifikat/' . $v['id']) ?>" target="_blank" title="Sertifikat" class="size-8 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center hover:bg-teal-600 hover:text-white"><span class="material-symbols-outlined text-base">workspace_premium</span></a>
                                        <button onclick='editRelawan(<?= json_encode($v) ?>)' title="Ubah" class="size-8 rounded-lg bg-primary/5 text-primary flex items-center justify-center hover:bg-primary hover:text-white"><span class="material-symbols-outlined text-base">edit</span></button>
                                        <?php if ($isAdmin): ?>
                                            <a href="<?= base_url('dashboard/relawan/delete/' . $v['id']) ?>" onclick="return confirm('Hapus relawan ini?')" title="Hapus" class="size-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white"><span class="material-symbols-outlined text-base">delete</span></a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Rekrut/Edit -->
<div id="modalForm" hidden class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-2xl w-full max-w-md p-6">
        <h3 id="modalFormTitle" class="text-lg font-black mb-4">Rekrut Relawan</h3>
        <form action="<?= base_url('dashboard/relawan/save') ?>" method="post" class="space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="f_id">
            <div>
                <label class="block text-sm font-bold mb-1">Nama</label>
                <input type="text" name="name" id="f_name" required class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl px-4 py-2.5 text-sm">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-bold mb-1">No. WhatsApp</label>
                    <input type="tel" name="phone" id="f_phone" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Status</label>
                    <select name="status" id="f_status" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl px-4 py-2.5 text-sm">
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold mb-1">Peran / Bidang</label>
                <input type="text" name="role" id="f_role" placeholder="mis. Kebersihan, Konsumsi, Keamanan" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl px-4 py-2.5 text-sm">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="tutupForm()" class="px-5 py-2.5 rounded-xl font-bold text-sm text-slate-600 hover:bg-slate-100 dark:hover:bg-white/5">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-primary text-white rounded-xl font-bold text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Beri Poin -->
<div id="modalPoin" hidden class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-2xl w-full max-w-sm p-6">
        <h3 class="text-lg font-black mb-1">Beri Poin</h3>
        <p id="poinNama" class="text-sm text-[#608a7e] mb-4"></p>
        <form action="<?= base_url('dashboard/relawan/points') ?>" method="post" class="space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="volunteer_id" id="p_id">
            <div>
                <label class="block text-sm font-bold mb-1">Jumlah Poin</label>
                <input type="number" name="points" id="p_points" value="10" required class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl px-4 py-2.5 text-sm">
                <p class="text-xs text-[#608a7e] mt-1">Boleh negatif untuk koreksi.</p>
            </div>
            <div>
                <label class="block text-sm font-bold mb-1">Alasan</label>
                <input type="text" name="reason" placeholder="mis. Piket Jumat, bantu acara" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl px-4 py-2.5 text-sm">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalPoin').hidden=true" class="px-5 py-2.5 rounded-xl font-bold text-sm text-slate-600 hover:bg-slate-100 dark:hover:bg-white/5">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-amber-500 text-white rounded-xl font-bold text-sm">Beri Poin</button>
            </div>
        </form>
    </div>
</div>

<script>
    function bukaForm() {
        document.getElementById('modalFormTitle').innerText = 'Rekrut Relawan';
        document.getElementById('f_id').value = '';
        document.getElementById('f_name').value = '';
        document.getElementById('f_phone').value = '';
        document.getElementById('f_role').value = '';
        document.getElementById('f_status').value = 'active';
        document.getElementById('modalForm').hidden = false;
    }
    function editRelawan(v) {
        document.getElementById('modalFormTitle').innerText = 'Ubah Relawan';
        document.getElementById('f_id').value = v.id;
        document.getElementById('f_name').value = v.name || '';
        document.getElementById('f_phone').value = v.phone || '';
        document.getElementById('f_role').value = v.role || '';
        document.getElementById('f_status').value = v.status || 'active';
        document.getElementById('modalForm').hidden = false;
    }
    function tutupForm() { document.getElementById('modalForm').hidden = true; }
    function beriPoin(id, nama) {
        document.getElementById('p_id').value = id;
        document.getElementById('poinNama').innerText = nama;
        document.getElementById('modalPoin').hidden = false;
    }
</script>
<?= $this->endSection() ?>
