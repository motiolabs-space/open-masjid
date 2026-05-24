<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<div class="mb-6 flex justify-between items-center">
    <a href="<?= base_url('dashboard/lms/module/' . $module['slug']) ?>" class="text-slate-500 hover:text-slate-800 font-medium text-sm flex items-center gap-1">
        <span class="material-symbols-outlined text-sm">arrow_back</span> Kembali ke Silabus
    </a>
</div>

<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden mb-6">
    <div class="p-6 border-b border-slate-200 dark:border-slate-800">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <p class="text-xs text-primary font-bold uppercase mb-1"><?= esc($module['title']) ?></p>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white"><?= esc($material['title']) ?></h2>
            </div>
            
            <?php if ($isCompleted): ?>
                <div class="bg-emerald-50 text-emerald-600 px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-bold border border-emerald-100">
                    <span class="material-symbols-outlined text-lg">verified</span>
                    Selesai Dipelajari
                </div>
            <?php else: ?>
                <form action="<?= base_url('dashboard/lms/mark-completed/' . $material['id']) ?>" method="POST">
                    <?= csrf_field() ?>
                    <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-6 py-2.5 rounded-lg font-bold text-sm shadow-sm transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">check_circle</span>
                        Tandai Selesai
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="p-0 md:p-6 bg-slate-50 dark:bg-slate-900/50">
        <div class="max-w-4xl mx-auto bg-white dark:bg-slate-900 md:rounded-2xl md:shadow-sm md:border border-slate-200 dark:border-slate-800 overflow-hidden">
            
            <?php if ($material['type'] == 'video'): ?>
                <?php 
                    // Extract YouTube ID
                    $url = $material['content'];
                    $videoId = '';
                    if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=|shorts/|live/)|youtu\.be/)([^"&?/\s]{11})%i', $url, $match)) {
                        $videoId = $match[1];
                    }
                ?>
                <div class="aspect-video w-full">
                    <?php if ($videoId): ?>
                        <iframe class="w-full h-full" src="https://www.youtube.com/embed/<?= $videoId ?>?rel=0&modestbranding=1" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                    <?php else: ?>
                        <div class="w-full h-full flex flex-col items-center justify-center bg-slate-100 text-slate-500 p-6 text-center">
                            <span class="material-symbols-outlined text-4xl mb-2">error</span>
                            <p>Link Video Tidak Valid.</p>
                            <a href="<?= esc($material['content']) ?>" target="_blank" class="text-primary hover:underline text-sm mt-2">Buka link eksternal</a>
                        </div>
                    <?php endif; ?>
                </div>
                
            <?php elseif ($material['type'] == 'pdf'): ?>
                <div class="h-[600px] w-full">
                    <?php if (str_starts_with($material['content'], 'http')): ?>
                        <iframe src="<?= esc($material['content']) ?>" class="w-full h-full" frameborder="0"></iframe>
                    <?php else: ?>
                        <?php 
                            $storage = new \App\Libraries\Storage(); 
                            // Backward compatibility for old files that don't have paths in their DB entry
                            $pdfPath = (strpos($material['content'], '/') === false) ? 'uploads/lms/' . $material['content'] : $material['content'];
                        ?>
                        <iframe src="<?= $storage->url($pdfPath) ?>" class="w-full h-full" frameborder="0"></iframe>
                    <?php endif; ?>
                </div>

            <?php elseif ($material['type'] == 'html'): ?>
                <div class="p-8 prose dark:prose-invert max-w-none prose-slate">
                    <?= $material['content'] ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>
<?= $this->endSection() ?>
