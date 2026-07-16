<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<div class="px-8 py-8">
    <?php if (session()->getFlashdata('error')): ?>
        <div class="bg-rose-50 text-rose-600 p-4 rounded-xl mb-6 flex items-center gap-3">
            <span class="material-symbols-outlined">error</span>
            <p class="text-sm font-medium"><?= esc(session()->getFlashdata('error')) ?></p>
        </div>
    <?php endif; ?>
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
        <div>
            <h1 class="text-3xl font-black text-[#111816] dark:text-white tracking-tight">Program & Kegiatan</h1>
            <p class="text-[#608a7e]">Manajemen jadwal, lokasi, dan pendaftaran kegiatan masjid.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="openCategoryModal()" class="flex items-center gap-2 px-5 py-2.5 rounded-xl border border-[#dbe6e3] dark:border-white/10 text-sm font-bold hover:bg-white dark:hover:bg-white/5 transition-all">
                <span class="material-symbols-outlined text-sm">category</span>
                Kelola Kategori
            </button>
            <a href="<?= base_url('dashboard/program/create') ?>" class="inline-flex items-center gap-2 bg-primary text-white px-6 py-4 rounded-2xl font-black shadow-lg shadow-primary/20 hover:bg-emerald-900 transition-all text-sm">
                <span class="material-symbols-outlined">add</span>
                Buat Program Baru
            </a>
        </div>
    </div>

    <!-- Programs Grid -->
    <div class="grid lg:grid-cols-4 gap-8">
        <!-- Sidebar: Categories & Filters -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-white/5 rounded-2xl border border-[#e5e7eb] dark:border-white/10 p-6">
                <h3 class="font-bold mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-sm">filter_list</span>
                    Filter Kegiatan
                </h3>
                <div class="space-y-1">
                    <a href="?" class="flex items-center justify-between p-2 rounded-lg <?= !request()->getGet('category') ? 'bg-primary/10 text-primary font-bold' : 'text-[#608a7e] hover:bg-gray-50' ?>">
                        <span class="text-sm">Semua Program</span>
                        <span class="text-[10px] px-1.5 py-0.5 bg-gray-100 dark:bg-white/10 rounded font-normal"><?= count($programs) ?></span>
                    </a>
                    <?php foreach ($categories as $cat): ?>
                        <a href="?category=<?= $cat['slug'] ?>" class="flex items-center justify-between p-2 rounded-lg text-[#608a7e] hover:bg-gray-50">
                            <span class="text-sm"><?= esc($cat['name']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="lg:col-span-3">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach ($programs as $item): ?>
                    <div class="bg-white dark:bg-white/5 rounded-[2.5rem] border border-[#e5e7eb] dark:border-white/10 overflow-hidden group hover:shadow-2xl transition-all duration-500 flex flex-col">
                        <div class="aspect-video overflow-hidden relative">
                            <?php if (!empty($item['thumbnail'])): ?>
                                <img src="<?= $storage->url($item['thumbnail']) ?>" class="size-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <?php else: ?>
                                <div class="size-full bg-slate-50 dark:bg-white/5 flex items-center justify-center text-slate-300">
                                    <span class="material-symbols-outlined text-5xl">event_available</span>
                                </div>
                            <?php endif; ?>
                            <div class="absolute top-4 left-4">
                                <span class="px-3 py-1 bg-black/60 backdrop-blur-md rounded-full text-[10px] font-bold text-white uppercase tracking-widest border border-white/20">
                                    <?= esc($item['category_name'] ?: 'Umum') ?>
                                </span>
                            </div>
                            <div class="absolute top-4 right-4">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest <?= $item['status'] == 'published' ? 'bg-primary text-white' : 'bg-yellow-400 text-yellow-900 shadow-xl' ?>">
                                    <?= $item['status'] == 'published' ? 'Terbit' : 'Draft' ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="p-6 flex-1 flex flex-col">
                            <h3 class="text-lg font-bold mb-4 line-clamp-2"><?= esc($item['title']) ?></h3>
                            
                            <div class="space-y-3 mb-6">
                                <div class="flex items-center gap-2 text-xs text-[#608a7e]">
                                    <span class="material-symbols-outlined text-sm">calendar_month</span>
                                    <?= date('d M Y, H:i', strtotime($item['date_start'])) ?>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-[#608a7e]">
                                    <span class="material-symbols-outlined text-sm">location_on</span>
                                    <?= esc($item['location']) ?>
                                </div>
                            </div>

                            <div class="mt-auto flex items-center justify-between pt-6 border-t border-gray-100 dark:border-white/5">
                                <div class="flex items-center gap-2">
                                    <a href="<?= base_url('dashboard/program/edit/' . $item['id']) ?>" class="size-10 bg-primary/5 text-primary rounded-xl flex items-center justify-center hover:bg-primary hover:text-white transition-all">
                                        <span class="material-symbols-outlined">edit</span>
                                    </a>
                                    <button onclick="confirmDelete(<?= $item['id'] ?>)" class="size-10 bg-red-50 text-red-500 rounded-xl flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </div>
                                <a href="<?= base_url(session()->get('masjid_username') . '/program/' . $item['slug']) ?>" target="_blank" class="text-xs font-bold text-primary hover:underline flex items-center gap-1">
                                    Pratinjau
                                    <span class="material-symbols-outlined text-xs">open_in_new</span>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($programs)): ?>
                    <div class="md:col-span-2 py-20 bg-white dark:bg-white/5 rounded-[3rem] border border-dashed border-[#dbe6e1] dark:border-white/10 text-center">
                        <span class="material-symbols-outlined text-6xl text-primary/20 mb-4">calendar_add_on</span>
                        <p class="text-[#608a7e] font-medium">Belum ada program atau kegiatan yang dibuat.</p>
                        <a href="<?= base_url('dashboard/program/create') ?>" class="inline-block mt-4 text-primary font-bold hover:underline">Buat program pertama Anda</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Category Modal -->
<div id="categoryModal" class="fixed inset-0 z-[60] bg-black/60 backdrop-blur-sm hidden flex items-center justify-center p-6">
    <div class="bg-white dark:bg-[#11241d] w-full max-w-md rounded-[2.5rem] overflow-hidden shadow-2xl animate-in zoom-in duration-300">
        <div class="p-8 border-b border-gray-100 dark:border-white/10 flex items-center justify-between">
            <h3 class="text-xl font-bold">Kelola Kategori Program</h3>
            <button onclick="closeCategoryModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-8">
            <!-- Add New -->
            <form action="<?= base_url('dashboard/program/category/save') ?>" method="POST" class="flex gap-2 mb-8">
                <?= csrf_field() ?>
                <input type="text" name="name" placeholder="Nama kategori baru..." required class="flex-1 bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl text-sm focus:ring-2 focus:ring-primary h-12">
                <button type="submit" class="size-12 bg-primary text-white rounded-xl flex items-center justify-center hover:bg-emerald-900 transition-all shadow-lg shadow-primary/20">
                    <span class="material-symbols-outlined">add</span>
                </button>
            </form>

            <div class="space-y-4 max-h-[300px] overflow-y-auto pr-2">
                <?php foreach ($categories as $cat): ?>
                    <div class="flex items-center justify-between p-4 bg-[#f0f5f3] dark:bg-white/5 rounded-2xl group">
                        <span class="font-bold text-sm"><?= esc($cat['name']) ?></span>
                        <button onclick="deleteCategory(<?= $cat['id'] ?>)" class="size-8 text-red-400 hover:text-red-500 hover:bg-red-50 rounded-lg flex items-center justify-center transition-all">
                            <span class="material-symbols-outlined text-lg">delete</span>
                        </button>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($categories)): ?>
                    <p class="text-center text-xs text-[#608a7e] py-4 italic">Belum ada kategori kustom.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    function openCategoryModal() {
        document.getElementById('categoryModal').classList.remove('hidden');
        document.getElementById('categoryModal').classList.add('flex');
    }

    function closeCategoryModal() {
        document.getElementById('categoryModal').classList.add('hidden');
        document.getElementById('categoryModal').classList.remove('flex');
    }

    async function deleteCategory(id) {
        if (!confirm('Menghapus kategori akan membuat program di dalamnya menjadi tanpa kategori. Lanjutkan?')) return;

        try {
            const formData = new FormData();
            formData.append('id', id);
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

            const response = await fetch('<?= base_url('dashboard/program/category/delete') ?>', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.status === 'success') {
                location.reload();
            } else {
                alert(result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Gagal menghapus kategori.');
        }
    }

    function confirmDelete(id) {
        if (confirm('Apakah Anda yakin ingin menghapus program ini?')) {
            window.location.href = '<?= base_url('dashboard/program/delete/') ?>' + id;
        }
    }
</script>
<?= $this->endSection() ?>
