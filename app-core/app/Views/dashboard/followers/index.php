<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl font-black text-slate-800 dark:text-white">Daftar Pengikut (Jamaah)</h2>
        <p class="text-slate-500 text-sm mt-1">Daftar jamaah yang terafiliasi dan mengikuti pembaruan masjid ini.</p>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-600 px-4 py-3 rounded-lg mb-6 flex items-center gap-2 text-sm font-medium">
        <span class="material-symbols-outlined">check_circle</span>
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-6 flex items-center gap-2 text-sm font-medium">
        <span class="material-symbols-outlined">error</span>
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                    <th class="px-6 py-4 text-xs font-bold uppercase text-slate-500">Profil Jamaah</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase text-slate-500">Kontak</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase text-slate-500">Diikuti Sejak</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase text-slate-500 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                <?php if (empty($followers)): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-slate-400 italic">Belum ada jamaah yang mengikuti masjid ini.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($followers as $follower): 
                        $isPengurus = in_array($follower['user_id'], $pengurusIds);
                    ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold overflow-hidden shrink-0">
                                    <?php if (!empty($follower['avatar'])): ?>
                                        <img src="<?= htmlspecialchars($follower['avatar']) ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <?= strtoupper(substr($follower['name'], 0, 1)) ?>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <p class="font-bold text-sm text-slate-900 dark:text-white"><?= esc($follower['name']) ?></p>
                                    <?php if ($isPengurus): ?>
                                        <span class="inline-flex mt-1 items-center gap-1 text-[10px] font-bold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">
                                            <span class="material-symbols-outlined text-[10px]">verified</span> Pengurus
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-block mt-1 text-[10px] font-bold uppercase tracking-wider text-slate-500 bg-slate-100 px-2 py-0.5 rounded">
                                            Jamaah
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm">
                                <p class="text-slate-700 dark:text-slate-300"><?= esc($follower['email'] ?? '-') ?></p>
                                <p class="text-xs text-slate-500 mt-1"><?= esc($follower['phone'] ?? '-') ?></p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-slate-600 dark:text-slate-400">
                                <?= date('d M Y', strtotime($follower['created_at'])) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <?php if (!$isPengurus): ?>
                                <button type="button" onclick="openPromoteModal(<?= $follower['user_id'] ?>, '<?= addslashes(esc($follower['name'])) ?>')" class="inline-flex items-center gap-2 px-3 py-1.5 bg-primary/10 text-primary hover:bg-primary hover:text-white rounded-lg text-xs font-bold transition-colors">
                                    <span class="material-symbols-outlined text-sm">person_add</span> Angkat Pengurus
                                </button>
                            <?php else: ?>
                                <span class="text-xs text-emerald-600 font-bold italic">Sudah Menjabat</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Angkat Pengurus -->
<div id="promoteModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closePromoteModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white dark:bg-slate-900 rounded-2xl shadow-xl">
        <form id="promoteForm" onsubmit="submitPromote(event)">
            <?= csrf_field() ?>
            <input type="hidden" name="user_id" id="promoteUserId">
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                <h3 class="text-lg font-bold">Angkat Menjadi Pengurus</h3>
                <button type="button" onclick="closePromoteModal()" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Nama Jamaah</label>
                    <input type="text" id="promoteUserName" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm" readonly>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Peran Akses (Role Sistem)</label>
                    <select name="role" required class="w-full px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 outline-none">
                        <option value="admin">Admin (Akses Penuh)</option>
                        <option value="bendahara">Bendahara (Keuangan)</option>
                        <option value="lms_admin">Admin LMS (E-Learning)</option>
                        <option value="amil">Amil Zakat (Penerimaan)</option>
                        <option value="distributor">Distributor Bantuan (Penyaluran)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Jabatan Resmi (Opsional)</label>
                    <input type="text" name="title" placeholder="Contoh: Sekretaris DKM, Seksi Dakwah..." class="w-full px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 outline-none">
                    <p class="text-[10px] text-slate-400 mt-1">Jabatan ini akan ditampilkan di halaman Profil Masjid Publik.</p>
                </div>
            </div>
            <div class="p-6 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                <button type="button" onclick="closePromoteModal()" class="px-4 py-2 text-slate-500 hover:bg-slate-100 rounded-lg text-sm font-bold transition-colors">Batal</button>
                <button type="submit" class="px-4 py-2 bg-primary text-white hover:bg-primary-dark rounded-lg text-sm font-bold transition-colors shadow-sm" id="btnSubmitPromote">Simpan Akses</button>
            </div>
        </form>
    </div>
</div>

<script>
function openPromoteModal(userId, userName) {
    document.getElementById('promoteUserId').value = userId;
    document.getElementById('promoteUserName').value = userName;
    document.getElementById('promoteModal').classList.remove('hidden');
}

function closePromoteModal() {
    document.getElementById('promoteModal').classList.add('hidden');
}

async function submitPromote(e) {
    e.preventDefault();
    const form = e.target;
    const btn = document.getElementById('btnSubmitPromote');
    btn.disabled = true;
    btn.innerHTML = 'Memproses...';

    const formData = new FormData(form);
    
    try {
        const response = await fetch('<?= base_url('dashboard/followers/promote') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const result = await response.json();
        if (result.status === 'success') {
            alert(result.message);
            window.location.reload();
        } else {
            alert(result.message || 'Terjadi kesalahan.');
            btn.disabled = false;
            btn.innerHTML = 'Simpan Akses';
        }
    } catch (err) {
        alert('Terjadi kesalahan jaringan.');
        btn.disabled = false;
        btn.innerHTML = 'Simpan Akses';
    }
}
</script>
<?= $this->endSection() ?>
