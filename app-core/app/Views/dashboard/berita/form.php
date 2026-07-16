<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<div class="px-8 py-8">
    <div class="max-w-4xl mx-auto">
        <?php // Kegagalan simpan mengembalikan pengurus ke sini. Tanpa blok ini
              // pesannya hilang: layar tampak seperti tidak terjadi apa-apa,
              // padahal tulisannya tidak tersimpan. ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-rose-50 text-rose-600 p-4 rounded-xl mb-6 flex items-center gap-3">
                <span class="material-symbols-outlined">error</span>
                <p class="text-sm font-medium"><?= esc(session()->getFlashdata('error')) ?></p>
            </div>
        <?php endif; ?>
        <div class="mb-8 flex items-center gap-4">
            <a href="<?= base_url('dashboard/berita') ?>" class="size-10 bg-white dark:bg-white/5 border border-[#dbe6e3] dark:border-white/10 rounded-xl flex items-center justify-center text-[#608a7e] hover:text-primary transition-all">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div>
                <h1 class="text-3xl font-black text-[#111816] dark:text-white tracking-tight"><?= $news ? 'Edit Berita' : 'Tulis Berita baru' ?></h1>
                <p class="text-[#608a7e]">Informasikan kegiatan dan kabar terbaru masjid Anda.</p>
            </div>
        </div>

        <form action="<?= base_url('dashboard/berita/save') ?>" method="POST" enctype="multipart/form-data" class="space-y-8">
            <?= csrf_field() ?>
            <?php if ($news): ?>
                <input type="hidden" name="id" value="<?= $news['id'] ?>">
            <?php endif; ?>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Main Body -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-white/5 rounded-3xl border border-[#e5e7eb] dark:border-white/10 p-8 space-y-6">
                        <div>
                            <label class="block text-sm font-bold mb-2">Judul Berita</label>
                            <input type="text" name="title" value="<?= old('title', $news['title'] ?? '') ?>" required placeholder="Masukkan judul yang menarik..." class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-2xl text-lg font-bold focus:ring-2 focus:ring-primary p-4">
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-2">Konten Berita</label>
                            <div id="editor-container" class="h-96 bg-[#f0f5f3] dark:bg-white/5 rounded-2xl overflow-hidden border-none text-base">
                                <?= old('content', $news['content'] ?? '') ?>
                            </div>
                            <input type="hidden" name="content" id="content-input" value="<?= old('content', $news['content'] ?? '') ?>">
                        </div>
                    </div>
                </div>

                <!-- Sidebar Settings -->
                <div class="space-y-6">
                    <div class="bg-white dark:bg-white/5 rounded-3xl border border-[#e5e7eb] dark:border-white/10 p-6 space-y-6">
                        <div>
                            <label class="block text-sm font-bold mb-2">Pilih Kategori</label>
                            <select name="category_id" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl text-sm focus:ring-2 focus:ring-primary p-3">
                                <option value="">Tanpa Kategori</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= (old('category_id', $news['category_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>><?= esc($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-2">Foto Sampul (Thumbnail)</label>
                            <div class="relative aspect-video bg-[#f0f5f3] dark:bg-white/5 rounded-2xl border-2 border-dashed border-[#dbe6e3] dark:border-white/10 flex flex-col items-center justify-center text-[#608a7e] overflow-hidden group hover:border-primary transition-colors">
                                <?php if (!empty($news['thumbnail'])): ?>
                                    <img id="thumbPrev" src="<?= $storage->url($news['thumbnail']) ?>" class="absolute inset-0 size-full object-cover">
                                <?php else: ?>
                                    <img id="thumbPrev" src="" class="absolute inset-0 size-full object-cover hidden">
                                <?php endif; ?>
                                <div id="thumbPlaceholder" class="relative z-10 flex flex-col items-center group-hover:scale-110 transition-transform <?= !empty($news['thumbnail']) ? 'opacity-0' : '' ?>">
                                    <span class="material-symbols-outlined text-3xl mb-1">add_a_photo</span>
                                    <span class="text-[10px] font-bold uppercase tracking-wider">Unggah Foto</span>
                                </div>
                                <input type="file" name="thumbnail" id="thumbnailInput" class="absolute inset-0 opacity-0 cursor-pointer z-20" onchange="previewThumb(this)">
                            </div>
                            <p class="text-[10px] text-[#608a7e] mt-2">Format: JPG, PNG, WEBP (Maks 2MB). Rekomendasi 1200x675px.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-2">Status Publikasi</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="cursor-pointer">
                                    <input type="radio" name="status" value="published" <?= (old('status', $news['status'] ?? 'published') == 'published') ? 'checked' : '' ?> class="hidden peer">
                                    <div class="p-3 text-center rounded-xl bg-[#f0f5f3] dark:bg-white/5 text-xs font-bold peer-checked:bg-primary peer-checked:text-white transition-all">Terbitkan</div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="status" value="draft" <?= (old('status', $news['status'] ?? '') == 'draft') ? 'checked' : '' ?> class="hidden peer">
                                    <div class="p-3 text-center rounded-xl bg-[#f0f5f3] dark:bg-white/5 text-xs font-bold peer-checked:bg-yellow-400 peer-checked:text-yellow-900 transition-all">Draft</div>
                                </label>
                            </div>
                        </div>

                        <hr class="border-[#e5e7eb] dark:border-white/10">

                        <button type="submit" class="w-full bg-primary text-white py-4 rounded-2xl font-black shadow-lg shadow-primary/20 hover:bg-emerald-900 transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined">send</span>
                            Simpan Berita
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
        placeholder: 'Tulis isi berita di sini...',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link', 'image', 'video'],
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
