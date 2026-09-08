<?php
/**
 * Pengingat konfirmasi email, tampil di dashboard sampai emailnya disahkan,
 * sekaligus tempat menampilkan hasil dari klik tautan dan kirim ulang.
 *
 * SENGAJA TIDAK MENGHALANGI APA PUN. Verifikasi di sini bersifat lunak: tak ada
 * fitur yang dikunci, tak ada modal yang menutup layar. Data aktivasi platform
 * ini menunjukkan mayoritas masjid berhenti tepat setelah mendaftar, dan
 * memasang tembok di titik itu akan memperburuknya.
 *
 * Nadanya pun bukan peringatan melainkan bantuan — sebab manfaatnya memang
 * untuk pemakainya sendiri: tanpa alamat yang sah, pemulihan kata sandi tak
 * akan pernah sampai.
 *
 * Kunci flash-nya khusus ('pesan_verifikasi'/'galat_verifikasi'), bukan
 * 'success'/'error' umum: partial ini ikut di layout dashboard, sedangkan
 * puluhan halaman di dalamnya sudah merender kunci umum sendiri — memakai kunci
 * yang sama akan memunculkan pesan dua kali.
 */
$pesan  = session()->getFlashdata('pesan_verifikasi');
$galat  = session()->getFlashdata('galat_verifikasi');
$selesai = \App\Libraries\EmailVerification::sudahTerverifikasi();

// Tak ada yang perlu dikatakan dan tak ada yang perlu diingatkan.
if (! $pesan && ! $galat && $selesai) {
    return;
}
?>

<?php if ($pesan || $galat): ?>
    <?php $sukses = (bool) $pesan; ?>
    <div id="kabarVerifikasi"
         class="fixed bottom-4 right-4 z-50 max-w-sm rounded-2xl shadow-xl p-4 border
                <?= $sukses
                    ? 'bg-emerald-50 border-emerald-200 dark:bg-emerald-900/20 dark:border-emerald-800/50'
                    : 'bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-800/50' ?>">
        <div class="flex gap-3">
            <span class="material-symbols-outlined shrink-0 <?= $sukses ? 'text-emerald-600' : 'text-red-600' ?>">
                <?= $sukses ? 'check_circle' : 'error' ?>
            </span>
            <p class="text-sm font-bold leading-relaxed <?= $sukses ? 'text-emerald-800 dark:text-emerald-200' : 'text-red-800 dark:text-red-200' ?>">
                <?= esc($pesan ?: $galat) ?>
            </p>
            <button type="button" onclick="document.getElementById('kabarVerifikasi').remove()"
                    class="material-symbols-outlined text-slate-400 hover:text-slate-600 shrink-0 text-base">close</button>
        </div>
    </div>
    <?php if ($sukses): ?>
    <script>
        // Kabar baik tak perlu menetap. Kabar buruk dibiarkan sampai ditutup,
        // sebab di situ ada petunjuk yang mungkin masih perlu dibaca.
        setTimeout(function () {
            var k = document.getElementById('kabarVerifikasi');
            if (k) { k.remove(); }
        }, 6000);
    </script>
    <?php endif; ?>
<?php endif; ?>

<?php
// Bila layanan email belum diatur, tak ada tautan yang pernah sampai dan tombol
// "Kirim Ulang" pun tak bisa berbuat apa-apa. Menampilkan kartu yang berkata
// "kami mengirim tautan konfirmasi" dalam keadaan itu hanya menyesatkan.
$bisaKirim = false;
try {
    $bisaKirim = (new \App\Libraries\Mailer())->siap();
} catch (\Throwable $e) {
    $bisaKirim = false;
}
?>
<?php if (! $selesai && $bisaKirim): ?>
<div id="pengingatVerifikasi"
     class="fixed <?= ($pesan || $galat) ? 'bottom-28' : 'bottom-4' ?> right-4 z-40 max-w-sm bg-white dark:bg-slate-900 border border-amber-200 dark:border-amber-800/50 rounded-2xl shadow-xl p-4">
    <div class="flex gap-3">
        <span class="material-symbols-outlined text-amber-500 shrink-0">mark_email_unread</span>
        <div class="min-w-0">
            <p class="font-bold text-sm text-slate-800 dark:text-slate-100">Konfirmasi alamat email Anda</p>
            <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                Kami mengirim tautan konfirmasi ke
                <strong class="break-all"><?= esc(session()->get('user_email')) ?></strong>.
                Ini yang membuat pemulihan kata sandi bisa sampai bila suatu saat Anda lupa.
            </p>
            <div class="flex items-center gap-3 mt-3">
                <form action="<?= base_url('verifikasi-email/kirim-ulang') ?>" method="post">
                    <?= csrf_field() ?>
                    <button type="submit"
                            class="px-3 py-1.5 rounded-lg bg-primary text-white text-xs font-bold hover:bg-emerald-900 transition-colors">
                        Kirim Ulang
                    </button>
                </form>
                <button type="button" onclick="document.getElementById('pengingatVerifikasi').remove()"
                        class="text-xs font-bold text-slate-400 hover:text-slate-600">
                    Nanti saja
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
