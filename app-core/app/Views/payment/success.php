<?= $this->extend('layout/public') ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-neutral-50 py-12">
    <div class="container mx-auto px-4 max-w-lg">
        <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 overflow-hidden text-center p-12">
            <div class="inline-flex items-center justify-center w-24 h-24 bg-green-100 rounded-full mb-6">
                <span class="material-symbols-outlined text-5xl text-green-600">check_circle</span>
            </div>
            
            <h1 class="text-3xl font-black text-gray-900 mb-2">Terima Kasih!</h1>
            <p class="text-gray-500 mb-8">Donasi Anda telah berhasil kami terima.</p>

            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 mb-8">
                <p class="text-xs text-gray-400 uppercase tracking-widest font-bold mb-1">ID Transaksi</p>
                <p class="text-xl font-mono font-bold text-gray-900"><?= esc($invoice) ?></p>
            </div>

            <p class="text-sm text-gray-500 mb-8 max-w-xs mx-auto">
                Semoga Allah membalas kebaikan Anda dengan pahala yang berlipat ganda. Aamiin.
            </p>

            <a href="<?= base_url() ?>" class="inline-flex items-center justify-center w-full py-4 bg-primary text-white font-bold rounded-xl shadow-lg shadow-primary/30 hover:shadow-xl hover:-translate-y-1 transition-all">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
