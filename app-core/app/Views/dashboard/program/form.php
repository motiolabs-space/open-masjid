<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<div class="px-8 py-8">
    <div class="max-w-4xl mx-auto">
        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-rose-50 text-rose-600 p-4 rounded-xl mb-6 flex items-center gap-3">
                <span class="material-symbols-outlined">error</span>
                <p class="text-sm font-medium"><?= esc(session()->getFlashdata('error')) ?></p>
            </div>
        <?php endif; ?>
        <div class="mb-8 flex items-center gap-4">
            <a href="<?= base_url('dashboard/program') ?>" class="size-10 bg-white dark:bg-white/5 border border-[#dbe6e3] dark:border-white/10 rounded-xl flex items-center justify-center text-[#608a7e] hover:text-primary transition-all">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div>
                <h1 class="text-3xl font-black text-[#111816] dark:text-white tracking-tight"><?= $program ? 'Edit Program' : 'Buat Program Baru' ?></h1>
                <p class="text-[#608a7e]">Kelola informasi kegiatan masjid Anda secara profesional.</p>
            </div>
        </div>

        <form action="<?= base_url('dashboard/program/save') ?>" method="POST" enctype="multipart/form-data" class="space-y-8">
            <?= csrf_field() ?>
            <?php if ($program): ?>
                <input type="hidden" name="id" value="<?= $program['id'] ?>">
            <?php endif; ?>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Main Body -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-white/5 rounded-3xl border border-[#e5e7eb] dark:border-white/10 p-8 space-y-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold mb-2">Kategori Program</label>
                                <select name="category_id" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-2xl focus:ring-2 focus:ring-primary p-4 text-sm font-bold">
                                    <option value="">-- Tanpa Kategori --</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" <?= (old('category_id', $program['category_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
                                            <?= esc($cat['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold mb-2">Nama Program / Kegiatan</label>
                                <input type="text" name="title" value="<?= old('title', $program['title'] ?? '') ?>" required placeholder="Contoh: Kajian Rutin Ahad Pagi" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-2xl text-lg font-bold focus:ring-2 focus:ring-primary p-4">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-2">Deskripsi Kegiatan</label>
                            <div id="editor-container" class="h-80 bg-[#f0f5f3] dark:bg-white/5 rounded-2xl overflow-hidden border-none text-base">
                                <?= old('description', $program['description'] ?? '') ?>
                            </div>
                            <input type="hidden" name="description" id="content-input" value="<?= old('description', $program['description'] ?? '') ?>">
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold mb-2">Waktu Mulai</label>
                                <input type="datetime-local" name="date_start" value="<?= old('date_start', isset($program['date_start']) ? date('Y-m-d\TH:i', strtotime($program['date_start'])) : '') ?>" required class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl text-sm focus:ring-2 focus:ring-primary p-4">
                            </div>
                            <div>
                                <label class="block text-sm font-bold mb-2">Waktu Selesai (Opsional)</label>
                                <input type="datetime-local" name="date_end" value="<?= old('date_end', isset($program['date_end']) ? date('Y-m-d\TH:i', strtotime($program['date_end'])) : '') ?>" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl text-sm focus:ring-2 focus:ring-primary p-4">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-2">Tempat / Lokasi</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#608a7e]">location_on</span>
                                <input type="text" name="location" value="<?= old('location', $program['location'] ?? '') ?>" required placeholder="Contoh: Ruang Utama Masjid" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-2xl focus:ring-2 focus:ring-primary py-4 pl-12 pr-4">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Settings -->
                <div class="space-y-6">
                    <div class="bg-white dark:bg-white/5 rounded-3xl border border-[#e5e7eb] dark:border-white/10 p-6 space-y-6">
                        <div>
                            <label class="block text-sm font-bold mb-2">Foto / Poster Kegiatan</label>
                            <div class="relative aspect-[3/4] bg-[#f0f5f3] dark:bg-white/5 rounded-2xl border-2 border-dashed border-[#dbe6e3] dark:border-white/10 flex flex-col items-center justify-center text-[#608a7e] overflow-hidden group hover:border-primary transition-colors">
                                <?php if (!empty($program['thumbnail'])): ?>
                                    <img id="thumbPrev" src="<?= $storage->url($program['thumbnail']) ?>" class="absolute inset-0 size-full object-cover">
                                <?php else: ?>
                                    <img id="thumbPrev" src="" class="absolute inset-0 size-full object-cover hidden">
                                <?php endif; ?>
                                <div id="thumbPlaceholder" class="relative z-10 flex flex-col items-center group-hover:scale-110 transition-transform <?= !empty($program['thumbnail']) ? 'opacity-0' : '' ?>">
                                    <span class="material-symbols-outlined text-3xl mb-1">add_a_photo</span>
                                    <span class="text-[10px] font-bold uppercase tracking-wider">Unggah Poster</span>
                                </div>
                                <input type="file" name="thumbnail" class="absolute inset-0 opacity-0 cursor-pointer z-20" onchange="previewThumb(this)">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-2">Link Pendaftaran (Opsional)</label>
                            <input type="url" name="registration_link" value="<?= old('registration_link', $program['registration_link'] ?? '') ?>" placeholder="https://forms.gle/..." class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl text-xs focus:ring-2 focus:ring-primary p-3">
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-2">Kuota (Opsional)</label>
                            <input type="number" name="quota" value="<?= old('quota', $program['quota'] ?? '') ?>" placeholder="Contoh: 100" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl text-xs focus:ring-2 focus:ring-primary p-3">
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-2">Target Donasi (Rp)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[#608a7e] text-xs font-bold">Rp</span>
                                <input type="text" name="target_donation" value="<?= isset($program['target_donation']) && $program['target_donation'] > 0 ? number_format($program['target_donation'], 0, ',', '.') : '' ?>" placeholder="0" onkeyup="formatCurrency(this)" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl text-xs focus:ring-2 focus:ring-primary py-3 pl-8 pr-3">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-2">Status</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="cursor-pointer">
                                    <input type="radio" name="status" value="published" <?= (old('status', $program['status'] ?? 'published') == 'published') ? 'checked' : '' ?> class="hidden peer">
                                    <div class="p-3 text-center rounded-xl bg-[#f0f5f3] dark:bg-white/5 text-xs font-bold peer-checked:bg-primary peer-checked:text-white transition-all">Publish</div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="status" value="draft" <?= (old('status', $program['status'] ?? '') == 'draft') ? 'checked' : '' ?> class="hidden peer">
                                    <div class="p-3 text-center rounded-xl bg-[#f0f5f3] dark:bg-white/5 text-xs font-bold peer-checked:bg-yellow-400 peer-checked:text-yellow-900 transition-all">Draft</div>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-primary text-white py-4 rounded-2xl font-black shadow-lg shadow-primary/20 hover:bg-emerald-900 transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined">save</span>
                            Simpan Program
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    var quill = new Quill('#editor-container', {
        theme: 'snow',
        placeholder: 'Tulis detail kegiatan di sini...',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link', 'image'],
                ['clean']
            ]
        }
    });

    document.querySelector('form').onsubmit = function() {
        var content = document.querySelector('#content-input');
        content.value = quill.root.innerHTML;
    };

    function previewThumb(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('thumbPrev');
                const ph = document.getElementById('thumbPlaceholder');
                img.src = e.target.result;
                img.classList.remove('hidden');
                ph.classList.add('opacity-0');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function formatCurrency(input) {
        let value = input.value.replace(/\D/g, '');
        let formatted = new Intl.NumberFormat('id-ID').format(value);
        input.value = formatted;
    }
</script>
<style>
    .ql-toolbar.ql-snow {
        border: none !important;
        background: #e9eff1;
        border-radius: 1rem 1rem 0 0;
        padding: 12px;
    }
    .dark .ql-toolbar.ql-snow {
        background: rgba(255, 255, 255, 0.05);
    }
    .ql-container.ql-snow {
        border: none !important;
    }
    .ql-editor {
        min-height: 200px;
        padding: 20px;
    }
</style>
<?= $this->endSection() ?>
