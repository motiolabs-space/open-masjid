<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<div class="px-4 sm:px-8 py-8">
    <div class="max-w-3xl mx-auto">

        <?php foreach (['error' => 'rose', 'success' => 'emerald'] as $jenis => $warna): ?>
            <?php if (session()->getFlashdata($jenis)): ?>
                <div class="bg-<?= $warna ?>-50 text-<?= $warna ?>-600 p-4 rounded-xl mb-6 flex items-center gap-3">
                    <span class="material-symbols-outlined"><?= $jenis === 'error' ? 'error' : 'check_circle' ?></span>
                    <p class="text-sm font-medium"><?= esc(session()->getFlashdata($jenis)) ?></p>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <div class="mb-8">
            <a href="<?= base_url('dashboard/program') ?>" class="inline-flex items-center gap-2 text-primary font-bold text-sm mb-3 hover:underline">
                <span class="material-symbols-outlined text-base">arrow_back</span> Kembali ke Program
            </a>
            <h1 class="text-2xl sm:text-3xl font-black text-[#111816] dark:text-white tracking-tight">Laporan Dampak</h1>
            <p class="text-[#608a7e] mt-1"><?= esc($program['title']) ?></p>
        </div>

        <form action="<?= base_url('dashboard/program/dampak/save') ?>" method="post" enctype="multipart/form-data"
              class="bg-white dark:bg-white/5 rounded-2xl border border-[#e5e7eb] dark:border-white/10 p-6 md:p-8 space-y-6">
            <?= csrf_field() ?>
            <input type="hidden" name="program_id" value="<?= $program['id'] ?>">

            <div>
                <label class="block text-sm font-bold mb-2">Jumlah Penerima Manfaat</label>
                <input type="text" name="beneficiaries_count" inputmode="numeric"
                       value="<?= !empty($program['beneficiaries_count']) ? esc($program['beneficiaries_count']) : '' ?>"
                       placeholder="mis. 150" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary">
                <p class="text-xs text-[#608a7e] mt-1">Berapa orang/keluarga yang terbantu oleh program ini.</p>
            </div>

            <div>
                <label class="block text-sm font-bold mb-2">Cerita Dampak</label>
                <textarea name="impact_narrative" rows="5" placeholder="Ceritakan hasil nyata program ini bagi masyarakat: apa yang berubah, siapa yang terbantu, bagaimana kondisinya sebelum & sesudah…"
                          class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary"><?= esc($program['impact_narrative'] ?? '') ?></textarea>
            </div>

            <div>
                <label class="block text-sm font-bold mb-2">Foto Bukti (boleh beberapa)</label>
                <input type="file" name="photos[]" accept="image/*" multiple
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">

                <?php if (! empty($photos)): ?>
                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-3 mt-4">
                        <?php foreach ($photos as $ph): ?>
                            <div class="relative group aspect-square rounded-xl overflow-hidden border border-[#e5e7eb] dark:border-white/10">
                                <img src="<?= esc($storage->url($ph['photo']), 'attr') ?>" class="w-full h-full object-cover">
                                <form action="<?= base_url('dashboard/program/dampak/photo/delete/' . $ph['id']) ?>" method="post"
                                      onsubmit="return confirm('Hapus foto ini?')" class="absolute top-1 right-1">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="size-7 rounded-full bg-black/60 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <span class="material-symbols-outlined text-base">delete</span>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <label class="flex items-center gap-3 p-4 rounded-xl bg-primary/5 border border-primary/15 cursor-pointer">
                <input type="checkbox" name="impact_published" value="1" <?= ($program['impact_published'] ?? 0) ? 'checked' : '' ?>
                       class="size-5 rounded text-primary focus:ring-primary">
                <span class="text-sm font-bold">Tampilkan laporan dampak ini di halaman publik program</span>
            </label>

            <div class="flex justify-end gap-3 pt-2">
                <a href="<?= base_url('dashboard/program') ?>" class="px-6 py-3 rounded-xl font-bold text-sm text-slate-600 hover:bg-slate-100 dark:hover:bg-white/5">Batal</a>
                <button type="submit" class="bg-primary text-white px-6 py-3 rounded-xl font-bold text-sm shadow-lg shadow-primary/30 flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">save</span> Simpan Laporan Dampak
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
