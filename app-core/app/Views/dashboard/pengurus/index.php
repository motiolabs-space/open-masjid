<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<div class="px-8 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="mb-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-black text-[#111816] dark:text-white tracking-tight">Manajemen Pengurus</h1>
                <p class="text-[#608a7e]">Kelola struktur organisasi dan hak akses pengelola masjid.</p>
            </div>
            
            <button onclick="openAddModal()" class="bg-primary text-white px-5 py-3 rounded-xl font-bold hover:bg-emerald-900 transition-all shadow-lg shadow-primary/20 flex items-center gap-2">
                <span class="material-symbols-outlined text-xl">person_add</span>
                <span>Tambah Pengurus</span>
            </button>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="bg-emerald-50 text-emerald-800 px-4 py-3 rounded-xl mb-6 flex items-center gap-2 font-bold border border-emerald-100">
                <span class="material-symbols-outlined">check_circle</span>
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-red-50 text-red-800 px-4 py-3 rounded-xl mb-6 flex items-center gap-2 font-bold border border-red-100">
                <span class="material-symbols-outlined">error</span>
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($pengurus as $p): ?>
                <div class="bg-white dark:bg-white/5 rounded-3xl border border-[#e5e7eb] dark:border-white/10 p-6 flex flex-col relative group hover:shadow-xl transition-all">
                    <?php if ($p['is_creator']): ?>
                        <div class="absolute top-4 right-4 text-xs font-bold bg-amber-100 text-amber-800 px-2 py-1 rounded-lg border border-amber-200">
                            PEMBUAT
                        </div>
                    <?php endif; ?>

                    <div class="flex items-center gap-4 mb-4">
                        <div class="size-16 rounded-full bg-gray-100 dark:bg-white/10 overflow-hidden">
                            <?php if (!empty($p['avatar'])): ?>
                                <img src="<?= base_url($p['avatar']) ?>" class="size-full object-cover">
                            <?php else: ?>
                                <div class="size-full flex items-center justify-center text-gray-400">
                                    <span class="material-symbols-outlined text-3xl">person</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h4 class="font-bold text-[#111816] dark:text-white line-clamp-1"><?= esc($p['name']) ?></h4>
                            <p class="text-xs text-[#608a7e]"><?= esc($p['email']) ?></p>
                            <p class="text-xs text-[#608a7e]"><?= esc($p['phone']) ?></p>
                        </div>
                    </div>

                    <div class="mt-auto pt-4 border-t border-gray-100 dark:border-white/5 space-y-3">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">JABATAN</p>
                            <p class="font-bold text-[#111816] dark:text-white"><?= esc($p['title'] ?? '-') ?></p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">WEB ROLE</p>
                            <span class="px-3 py-1 bg-primary/10 text-primary text-xs font-bold rounded-full capitalize">
                                <?= esc($p['role']) ?>
                            </span>
                        </div>
                    </div>

                    <?php if (!$p['is_creator']): ?>
                        <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity flex gap-2 bg-white dark:bg-gray-800 p-1 rounded-xl shadow-sm border border-gray-100 dark:border-white/10">
                            <button onclick='openEditModal(<?= json_encode($p) ?>)' class="size-8 flex items-center justify-center text-gray-500 hover:text-primary hover:bg-gray-50 rounded-lg">
                                <span class="material-symbols-outlined text-lg">edit</span>
                            </button>
                            <form action="<?= base_url('dashboard/pengurus/delete') ?>" method="POST" onsubmit="return confirm('Hapus pengurus ini?');">
                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                <button type="submit" class="size-8 flex items-center justify-center text-gray-500 hover:text-red-500 hover:bg-red-50 rounded-lg">
                                    <span class="material-symbols-outlined text-lg">delete</span>
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div id="addModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeAddModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg bg-white dark:bg-[#1a2e25] rounded-3xl p-8 shadow-2xl">
        <h3 class="text-xl font-bold mb-6 text-[#111816] dark:text-white">Tambah Pengurus Baru</h3>
        
        <form action="<?= base_url('dashboard/pengurus/add') ?>" method="POST" class="space-y-4">
            <!-- User Search -->
            <div class="relative">
                <label class="block text-sm font-bold mb-2 text-[#111816] dark:text-white">Cari User (Email/Nama)</label>
                <input type="text" id="userSearch" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl focus:ring-2 focus:ring-primary p-4 font-bold text-[#111816] dark:text-white" placeholder="Ketik minimal 3 karakter..." autocomplete="off">
                <input type="hidden" name="user_id" id="selectedUserId" required>
                
                <!-- Dropdown Results -->
                <div id="searchResults" class="absolute left-0 right-0 top-full mt-2 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-white/10 max-h-60 overflow-y-auto hidden z-10"></div>
            </div>

            <!-- Selected User Preview -->
            <div id="selectedUserPreview" class="hidden p-4 bg-primary/5 rounded-xl border border-primary/10 flex items-center gap-3">
                <div class="size-10 bg-primary/10 rounded-full flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined">person</span>
                </div>
                <div>
                    <h4 id="selName" class="font-bold text-sm"></h4>
                    <p id="selEmail" class="text-xs text-gray-500"></p>
                </div>
                <button type="button" onclick="clearSelection()" class="ml-auto text-red-500 hover:bg-red-50 p-1 rounded">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div>
                <label class="block text-sm font-bold mb-2 text-[#111816] dark:text-white">Jabatan (Title)</label>
                <input type="text" name="title" required placeholder="Contoh: Ketua DKM, Bendahara" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl focus:ring-2 focus:ring-primary p-4 font-bold text-[#111816] dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-bold mb-2 text-[#111816] dark:text-white">Role Website</label>
                <select name="role" required class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl focus:ring-2 focus:ring-primary p-4 font-bold text-[#111816] dark:text-white">
                    <option value="admin">Admin (Akses Penuh)</option>
                    <option value="pengurus">Pengurus (Non-Delete)</option>
                    <option value="marbot">Marbot (Terbatas)</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">*Admin memiliki akses penuh ke dashboard masjid ini.</p>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" onclick="closeAddModal()" class="px-5 py-3 rounded-xl font-bold text-gray-500 hover:bg-gray-100 dark:hover:bg-white/5">Batal</button>
                <button type="submit" class="bg-primary text-white px-5 py-3 rounded-xl font-bold hover:bg-emerald-900 shadow-lg shadow-primary/20">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeEditModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg bg-white dark:bg-[#1a2e25] rounded-3xl p-8 shadow-2xl">
        <h3 class="text-xl font-bold mb-6 text-[#111816] dark:text-white">Edit Pengurus</h3>
        
        <form action="<?= base_url('dashboard/pengurus/update') ?>" method="POST" class="space-y-4">
            <input type="hidden" name="id" id="editId">
            
            <div class="p-4 bg-gray-50 dark:bg-white/5 rounded-xl border border-gray-100 dark:border-white/10 mb-4">
                <h4 id="editName" class="font-bold text-[#111816] dark:text-white"></h4>
                <p id="editEmail" class="text-xs text-gray-500"></p>
            </div>

            <div>
                <label class="block text-sm font-bold mb-2 text-[#111816] dark:text-white">Jabatan (Title)</label>
                <input type="text" name="title" id="editTitle" required class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl focus:ring-2 focus:ring-primary p-4 font-bold text-[#111816] dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-bold mb-2 text-[#111816] dark:text-white">Role Website</label>
                <select name="role" id="editRole" required class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl focus:ring-2 focus:ring-primary p-4 font-bold text-[#111816] dark:text-white">
                    <option value="admin">Admin</option>
                    <option value="pengurus">Pengurus</option>
                    <option value="marbot">Marbot</option>
                </select>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" onclick="closeEditModal()" class="px-5 py-3 rounded-xl font-bold text-gray-500 hover:bg-gray-100 dark:hover:bg-white/5">Batal</button>
                <button type="submit" class="bg-primary text-white px-5 py-3 rounded-xl font-bold hover:bg-emerald-900 shadow-lg shadow-primary/20">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
    // --- Add Modal ---
    function openAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
    }
    function closeAddModal() {
        document.getElementById('addModal').classList.add('hidden');
        clearSelection();
        document.getElementById('userSearch').value = '';
    }

    // --- Edit Modal ---
    function openEditModal(data) {
        document.getElementById('editModal').classList.remove('hidden');
        document.getElementById('editId').value = data.id;
        document.getElementById('editName').innerText = data.name;
        document.getElementById('editEmail').innerText = data.email;
        document.getElementById('editTitle').value = data.title;
        document.getElementById('editRole').value = data.role;
    }
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    // --- User Search Logic ---
    const searchInput = document.getElementById('userSearch');
    const resultsDiv = document.getElementById('searchResults');
    const hiddenInput = document.getElementById('selectedUserId');
    const previewDiv = document.getElementById('selectedUserPreview');
    let debounceTimer;

    searchInput.addEventListener('input', function(e) {
        clearTimeout(debounceTimer);
        const term = e.target.value;
        
        if (term.length < 3) {
            resultsDiv.classList.add('hidden');
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`<?= base_url('dashboard/users/search') ?>?term=${term}`)
                .then(res => res.json())
                .then(data => {
                    resultsDiv.innerHTML = '';
                    if (data.length === 0) {
                        resultsDiv.innerHTML = '<div class="p-4 text-center text-gray-500 text-sm">User tidak ditemukan. Pastikan sudah terdaftar.</div>';
                    } else {
                        data.forEach(user => {
                            const div = document.createElement('div');
                            div.className = 'p-3 hover:bg-gray-50 dark:hover:bg-white/5 cursor-pointer flex items-center gap-3 border-b border-gray-50 dark:border-white/5 last:border-0';
                            div.onclick = () => selectUser(user);
                            div.innerHTML = `
                                <div class="size-8 bg-gray-200 rounded-full flex items-center justify-center text-xs font-bold text-gray-500">${user.name.charAt(0)}</div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800 dark:text-white">${user.name}</p>
                                    <p class="text-[10px] text-gray-500">${user.email || user.phone}</p>
                                </div>
                            `;
                            resultsDiv.appendChild(div);
                        });
                    }
                    resultsDiv.classList.remove('hidden');
                });
        }, 500);
    });

    function selectUser(user) {
        hiddenInput.value = user.id;
        document.getElementById('selName').innerText = user.name;
        document.getElementById('selEmail').innerText = user.email || user.phone;
        
        searchInput.classList.add('hidden');
        resultsDiv.classList.add('hidden');
        previewDiv.classList.remove('hidden');
    }

    function clearSelection() {
        hiddenInput.value = '';
        searchInput.value = '';
        searchInput.classList.remove('hidden');
        previewDiv.classList.add('hidden');
    }
</script>
<?= $this->endSection() ?>
