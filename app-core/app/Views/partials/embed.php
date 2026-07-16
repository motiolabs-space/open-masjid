<?php
/**
 * Menggambar hasil App\Libraries\Embed::baca().
 *
 * Butuh $embed. Semua alamat iframe disusun di pustaka itu dari daftar host
 * tetap — jangan pernah mengambil src langsung dari tautan pengurus di sini.
 *
 * @var array $embed
 */

if (empty($embed)) {
    return;
}

// Tiap penyedia punya bentuk aslinya sendiri; memaksakan satu rasio membuat
// video TikTok tergunting atau menyisakan pita hitam lebar.
$bingkai = [
    'youtube'   => 'w-full aspect-video',
    'tiktok'    => 'w-full max-w-[325px] mx-auto aspect-[9/16]',
    'instagram' => 'w-full max-w-[540px] mx-auto h-[680px]',
];
?>

<?php if ($embed['jenis'] === 'iframe'): ?>
    <div class="<?= $bingkai[$embed['penyedia']] ?? 'w-full aspect-video' ?> rounded-[2.5rem] overflow-hidden bg-black shadow-2xl mb-12">
        <?php // esc() biasa, bukan konteks 'attr': alamat ini disusun sendiri
              // oleh Embed dari daftar host tetap, dan 'attr' mengubah setiap
              // ':' dan '/' menjadi entitas sehingga src-nya sulit dibaca saat
              // ditelusuri. ?>
        <iframe
            src="<?= esc($embed['src']) ?>"
            class="size-full"
            loading="lazy"
            referrerpolicy="strict-origin-when-cross-origin"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen></iframe>
    </div>
<?php else: ?>
    <?php // Tautan artikel: kartu sederhana. Judul dan gambarnya tidak diambil
          // dari situs sumber — lihat aturan 4 pada App\Libraries\Embed. ?>
    <a href="<?= esc($embed['url'], 'attr') ?>" target="_blank" rel="noopener noreferrer nofollow"
       class="group flex items-center gap-4 p-5 mb-12 bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-3xl hover:border-primary hover:shadow-lg transition-all">
        <div class="size-12 flex-shrink-0 rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
            <span class="material-symbols-outlined">link</span>
        </div>
        <div class="min-w-0">
            <p class="text-xs font-bold text-[#608a7e] uppercase tracking-widest mb-0.5">Sumber Tautan</p>
            <p class="font-bold text-[#111816] dark:text-white truncate group-hover:text-primary transition-colors">
                <?= esc($embed['domain']) ?>
            </p>
        </div>
        <span class="material-symbols-outlined ml-auto text-[#608a7e] group-hover:translate-x-1 transition-transform">open_in_new</span>
    </a>
<?php endif; ?>
