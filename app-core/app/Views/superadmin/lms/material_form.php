<?= $this->extend('layout/superadmin') ?>

<?= $this->section('content') ?>
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white"><?= $title ?></h2>
        <p class="text-slate-500 text-sm">Modul: <?= esc($module['title']) ?></p>
    </div>
    <a href="<?= base_url('superadmin/lms/' . $module['id'] . '/materials') ?>" class="text-slate-500 hover:text-slate-800 font-medium text-sm flex items-center gap-1">
        <span class="material-symbols-outlined text-sm">arrow_back</span> Kembali
    </a>
</div>

<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
    <form action="<?= base_url('superadmin/lms/materials/save') ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="module_id" value="<?= $module['id'] ?>">
        <?php if ($material): ?>
            <input type="hidden" name="id" value="<?= $material['id'] ?>">
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Judul Materi</label>
                    <input type="text" name="title" value="<?= esc($material['title'] ?? '') ?>" required class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:outline-none">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Urutan (Angka)</label>
                    <input type="number" name="order_number" value="<?= esc($material['order_number'] ?? '0') ?>" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Tipe Materi</label>
                    <select name="type" id="material_type" onchange="toggleContentField()" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:outline-none">
                        <option value="video" <?= ($material['type'] ?? '') == 'video' ? 'selected' : '' ?>>Video (YouTube Link)</option>
                        <option value="pdf" <?= ($material['type'] ?? '') == 'pdf' ? 'selected' : '' ?>>PDF Document</option>
                        <option value="html" <?= ($material['type'] ?? '') == 'html' ? 'selected' : '' ?>>HTML / Teks Artikel</option>
                    </select>
                </div>
            </div>

            <div class="space-y-4">
                <div id="field_content">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2" id="label_content">Konten (URL / Teks)</label>
                    <textarea name="content" id="input_content" rows="6" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:outline-none"><?= esc($material['content'] ?? '') ?></textarea>
                    <p class="text-xs text-slate-500 mt-1" id="help_content">Untuk video, masukkan link YouTube. Untuk artikel, masukkan format HTML/Markdown.</p>
                </div>

                <div id="field_pdf" style="display: none;">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Upload PDF File</label>
                    <input type="file" name="pdf_file" accept=".pdf" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:outline-none">
                    <?php if (($material['type'] ?? '') == 'pdf'): ?>
                        <p class="text-xs text-slate-500 mt-2 mb-1">File saat ini: <?= esc($material['content']) ?></p>
                        <?php 
                            $storage = new \App\Libraries\Storage(); 
                            $pdfPath = (strpos($material['content'], '/') === false) ? 'uploads/lms/' . $material['content'] : $material['content'];
                        ?>
                        <div class="border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden h-64 mt-2">
                            <iframe src="<?= $storage->url($pdfPath) ?>" class="w-full h-full" frameborder="0"></iframe>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1 italic">* Unggah file baru untuk mengganti PDF yang sudah ada.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="mt-8 border-t border-slate-200 dark:border-slate-800 pt-6">
            <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-6 py-2.5 rounded-lg font-medium text-sm transition-colors">
                Simpan Materi
            </button>
        </div>
    </form>
</div>

<!-- Load TinyMCE from CDN -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<script>
function toggleContentField() {
    const type = document.getElementById('material_type').value;
    const fieldContent = document.getElementById('field_content');
    const fieldPdf = document.getElementById('field_pdf');
    const labelContent = document.getElementById('label_content');
    const helpContent = document.getElementById('help_content');

    if (type === 'video') {
        fieldContent.style.display = 'block';
        fieldPdf.style.display = 'none';
        labelContent.innerText = 'Link YouTube Video';
        helpContent.innerText = 'Masukkan full URL YouTube (contoh: https://www.youtube.com/watch?v=...)';
        if (tinymce.get('input_content')) {
            tinymce.get('input_content').remove();
        }
    } else if (type === 'html') {
        fieldContent.style.display = 'block';
        fieldPdf.style.display = 'none';
        labelContent.innerText = 'Konten Artikel (HTML)';
        helpContent.innerText = 'Masukkan teks artikel/materi Anda di bawah ini.';
        
        // Initialize TinyMCE
        if (!tinymce.get('input_content')) {
            tinymce.init({
                selector: '#input_content',
                height: 400,
                plugins: 'lists link image table code',
                toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | link image table | code',
                menubar: false,
                promotion: false,
                skin: window.matchMedia('(prefers-color-scheme: dark)').matches ? 'oxide-dark' : 'oxide',
                content_css: window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'default',
            });
        }
    } else if (type === 'pdf') {
        fieldContent.style.display = 'none';
        fieldPdf.style.display = 'block';
        if (tinymce.get('input_content')) {
            tinymce.get('input_content').remove();
        }
    }
}

// Run on load
document.addEventListener('DOMContentLoaded', toggleContentField);
</script>
<?= $this->endSection() ?>
