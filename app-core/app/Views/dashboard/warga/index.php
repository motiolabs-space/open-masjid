<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-black text-[#111816]">Data Warga & Mustahik</h1>
            <p class="text-[#608a7e] mt-1">Kelola data warga sekitar dan penerima manfaat.</p>
        </div>
        <a href="<?= base_url('dashboard/warga/new') ?>" class="btn-primary flex items-center gap-2">
            <span class="material-symbols-outlined">person_add</span>
            Tambah Warga
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm border border-[#dbe6e1] p-6 mb-8">
        <form action="" method="get" class="grid md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-3 text-[#608a7e]">search</span>
                    <input type="text" name="q" value="<?= esc($filters['q']) ?>" placeholder="Cari nama, NIK, atau no. HP..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-[#dbe6e1] focus:border-primary focus:ring-0">
                </div>
            </div>
            <div>
                <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-[#dbe6e1] focus:border-primary focus:ring-0">
                    <option value="">Semua Status</option>
                    <option value="active" <?= $filters['status'] == 'active' ? 'selected' : '' ?>>Aktif</option>
                    <option value="inactive" <?= $filters['status'] == 'inactive' ? 'selected' : '' ?>>Tidak Aktif</option>
                    <option value="moved" <?= $filters['status'] == 'moved' ? 'selected' : '' ?>>Pindah</option>
                    <option value="deceased" <?= $filters['status'] == 'deceased' ? 'selected' : '' ?>>Meninggal</option>
                </select>
            </div>
            <div>
                <select name="economic" class="w-full px-4 py-2.5 rounded-xl border border-[#dbe6e1] focus:border-primary focus:ring-0">
                    <option value="">Semua Ekonomi</option>
                    <option value="mampu" <?= $filters['economic'] == 'mampu' ? 'selected' : '' ?>>Mampu</option>
                    <option value="cukup" <?= $filters['economic'] == 'cukup' ? 'selected' : '' ?>>Cukup</option>
                    <option value="kurang_mampu" <?= $filters['economic'] == 'kurang_mampu' ? 'selected' : '' ?>>Kurang Mampu</option>
                    <option value="fakir" <?= $filters['economic'] == 'fakir' ? 'selected' : '' ?>>Fakir</option>
                    <option value="miskin" <?= $filters['economic'] == 'miskin' ? 'selected' : '' ?>>Miskin</option>
                    <option value="yatim" <?= $filters['economic'] == 'yatim' ? 'selected' : '' ?>>Yatim</option>
                </select>
            </div>
            <div class="col-span-full flex justify-end">
                <button type="submit" class="btn-secondary flex items-center gap-2">
                    <span class="material-symbols-outlined">filter_list</span>
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-2">
            <span class="material-symbols-outlined">check_circle</span>
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <!-- Table -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-[#dbe6e1] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-[#f0f5f3] border-b border-[#dbe6e1]">
                    <tr>
                        <th class="p-6 font-bold text-[#111816]">Nama Lengkap</th>
                        <th class="p-6 font-bold text-[#111816]">Kontak / Alamat</th>
                        <th class="p-6 font-bold text-[#111816]">Status Ekonomi</th>
                        <th class="p-6 font-bold text-[#111816]">Bantuan Terakhir</th>
                        <th class="p-6 font-bold text-[#111816]">Status Warga</th>
                        <th class="p-6 font-bold text-[#111816] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#dbe6e1]">
                    <?php if (empty($warga)): ?>
                        <tr>
                            <td colspan="6" class="p-12 text-center text-[#608a7e]">
                                <span class="material-symbols-outlined text-6xl mb-4 opacity-20">person_search</span>
                                <p>Belum ada data warga yang sesuai filter.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($warga as $w): 
                            $isMustahik = in_array($w['economic_status'], ['kurang_mampu', 'fakir', 'miskin', 'yatim']);
                        ?>
                            <tr class="hover:bg-[#f8faf9] transition-colors group <?= $isMustahik ? 'bg-primary/5' : '' ?>">
                                <td class="p-6">
                                    <h3 class="font-bold text-[#111816] flex items-center gap-2">
                                        <?= esc($w['name']) ?>
                                        <?php if ($isMustahik): ?>
                                            <span class="material-symbols-outlined text-primary text-sm" title="Mustahik">volunteer_activism</span>
                                        <?php endif; ?>
                                    </h3>
                                    <?php if ($w['nik']): ?>
                                        <p class="text-xs text-[#608a7e] mt-1">NIK: <?= esc($w['nik']) ?></p>
                                    <?php endif; ?>
                                </td>
                                <td class="p-6">
                                    <?php if ($w['phone']): ?>
                                        <div class="flex items-center gap-2 text-sm text-[#111816] mb-1">
                                            <span class="material-symbols-outlined text-xs text-[#608a7e]">call</span>
                                            <?= esc($w['phone']) ?>
                                        </div>
                                    <?php endif; ?>
                                    <p class="text-sm text-[#608a7e] line-clamp-2"><?= esc($w['address'] ?: '-') ?></p>
                                </td>
                                <td class="p-6">
                                    <?php
                                    $ecoColors = [
                                        'mampu' => 'bg-green-100 text-green-700 border-green-200',
                                        'cukup' => 'bg-blue-100 text-blue-700 border-blue-200',
                                        'kurang_mampu' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                        'fakir' => 'bg-red-100 text-red-700 border-red-200',
                                        'miskin' => 'bg-red-100 text-red-700 border-red-200',
                                        'yatim' => 'bg-purple-100 text-purple-700 border-purple-200',
                                    ];
                                    $ecoClass = $ecoColors[$w['economic_status']] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                                    ?>
                                    <span class="px-3 py-1 rounded-full text-xs font-bold border <?= $ecoClass ?> uppercase tracking-wider">
                                        <?= str_replace('_', ' ', $w['economic_status']) ?>
                                    </span>
                                </td>
                                <td class="p-6">
                                    <?php if ($w['last_aid_date']): ?>
                                        <div class="text-sm font-bold text-primary">
                                            <?= date('d M Y', strtotime($w['last_aid_date'])) ?>
                                        </div>
                                        <p class="text-[10px] text-[#608a7e] uppercase font-black tracking-widest">Penyaluran Terakhir</p>
                                    <?php else: ?>
                                        <span class="text-xs text-[#608a7e] italic">Belum ada catatan</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-6">
                                    <?php
                                    $statusColors = [
                                        'active'   => 'text-green-600',
                                        'inactive' => 'text-gray-400',
                                        'moved'    => 'text-orange-500',
                                        'deceased' => 'text-red-500',
                                    ];
                                    $statusClass = $statusColors[$w['status']] ?? 'text-gray-500';
                                    $statusIcon = [
                                        'active'   => 'check_circle',
                                        'inactive' => 'cancel',
                                        'moved'    => 'move_up',
                                        'deceased' => 'sentiment_very_dissatisfied',
                                    ];
                                    ?>
                                    <div class="flex items-center gap-2 <?= $statusClass ?> font-bold text-sm">
                                        <span class="material-symbols-outlined text-lg"><?= $statusIcon[$w['status']] ?? 'help' ?></span>
                                        <?= ucfirst($w['status'] == 'deceased' ? 'Meninggal' : ($w['status'] == 'moved' ? 'Pindah' : ($w['status'] == 'active' ? 'Aktif' : 'Non-Aktif'))) ?>
                                    </div>
                                </td>
                                <td class="p-6 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <?php if ($isMustahik): ?>
                                            <a href="<?= base_url('dashboard/distribution/new?warga_id=' . $w['id']) ?>" class="px-4 py-2 flex items-center gap-2 rounded-xl bg-primary text-white hover:bg-primary/90 transition-all text-xs font-bold" title="Beri Bantuan">
                                                <span class="material-symbols-outlined text-sm">volunteer_activism</span>
                                                Beri Bantuan
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if ($w['phone']): 
                                            $waPhone = preg_replace('/[^0-9]/', '', $w['phone']);
                                            if (strpos($waPhone, '0') === 0) $waPhone = '62' . substr($waPhone, 1);
                                            elseif (strpos($waPhone, '62') !== 0) $waPhone = '62' . $waPhone;
                                        ?>
                                            <a href="https://wa.me/<?= $waPhone ?>" target="_blank" class="size-10 flex items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-colors" title="Hubungi via WhatsApp">
                                                <span class="material-symbols-outlined">chat</span>
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?= base_url('dashboard/warga/edit/' . $w['id']) ?>" class="size-10 flex items-center justify-center rounded-xl bg-[#ebf2ef] text-[#111816] hover:bg-primary hover:text-white transition-colors">
                                            <span class="material-symbols-outlined">edit</span>
                                        </a>
                                        <button onclick="deleteWarga(<?= $w['id'] ?>)" class="size-10 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-colors">
                                            <span class="material-symbols-outlined">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function deleteWarga(id) {
    if(confirm('Apakah Anda yakin ingin menghapus data warga ini?')) {
        window.location.href = '<?= base_url('dashboard/warga/delete/') ?>' + id;
    }
}
</script>
<?= $this->endSection() ?>
