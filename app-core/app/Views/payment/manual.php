<?= $this->extend('layout/landing') ?>

<?= $this->section('content') ?>
<div class="min-h-screen pt-32 pb-20 bg-[#f0f5f3] dark:bg-gray-900">
    <div class="container mx-auto px-6">
        <div class="max-w-xl mx-auto">
            <div class="bg-white dark:bg-white/5 rounded-3xl p-8 shadow-xl shadow-gray-200/50 dark:shadow-none border border-white/50 dark:border-white/10 text-center">
                
                <div class="size-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="material-symbols-outlined text-4xl">inventory_2</span>
                </div>

                <h1 class="text-2xl font-black text-[#111816] dark:text-white mb-2">Instruksi Pembayaran</h1>
                <p class="text-[#608a7e] mb-8">Silakan selesaikan pembayaran donasi Anda melalui transfer bank atau scan QRIS berikut.</p>

                <div class="bg-[#f0f5f3] dark:bg-white/5 rounded-2xl p-6 mb-8 border border-[#dbe6e3] dark:border-white/10">
                    <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-1">Total Donasi</p>
                    <p class="text-3xl font-black text-primary">Rp <?= number_format($donation['amount'], 0, ',', '.') ?></p>
                    <p class="text-xs text-gray-500 mt-2">Invoice: #<?= $donation['invoice_number'] ?></p>
                </div>

                <?php if (!empty($paymentSettings['qris_image'])): ?>
                    <div class="mb-8">
                        <p class="text-sm font-bold text-[#111816] dark:text-white mb-4">Scan QRIS</p>
                        <div class="bg-white p-4 rounded-2xl border border-gray-200 inline-block shadow-sm">
                            <img src="<?= $storage->url($paymentSettings['qris_image']) ?>" alt="QRIS Code" class="size-48 object-contain">
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($paymentSettings['bank_name'])): ?>
                    <div class="text-left space-y-4">
                        <div class="bg-white dark:bg-white/5 p-4 rounded-xl border border-[#dbe6e3] dark:border-white/10 flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 font-bold mb-1">Bank Tujuan</p>
                                <p class="font-bold text-[#111816] dark:text-white"><?= esc($paymentSettings['bank_name']) ?></p>
                            </div>
                            <span class="material-symbols-outlined text-gray-400">account_balance</span>
                        </div>
                        <div class="bg-white dark:bg-white/5 p-4 rounded-xl border border-[#dbe6e3] dark:border-white/10 flex items-center justify-between group cursor-pointer" onclick="copyText('<?= esc($paymentSettings['bank_account_number']) ?>')">
                            <div>
                                <p class="text-xs text-gray-500 font-bold mb-1">Nomor Rekening</p>
                                <p class="font-bold text-lg text-[#111816] dark:text-white"><?= esc($paymentSettings['bank_account_number']) ?></p>
                                <p class="text-xs text-[#608a7e] mt-1">a.n <?= esc($paymentSettings['bank_account_name']) ?></p>
                            </div>
                            <span class="material-symbols-outlined text-gray-400 group-hover:text-primary transition-colors">content_copy</span>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mt-8 pt-8 border-t border-dashed border-gray-200 dark:border-white/10">
                    <p class="text-sm text-gray-500 mb-6">Setelah melakukan transfer, mohon konfirmasi ke pengurus masjid melalui WhatsApp.</p>
                    
                    <a href="https://wa.me/?text=Assalamualaikum, saya sudah transfer donasi sebesar Rp <?= number_format($donation['amount'], 0, ',', '.') ?> untuk Invoice #<?= $donation['invoice_number'] ?>" target="_blank" class="block w-full py-4 bg-emerald-500 text-white rounded-xl font-bold shadow-lg shadow-emerald-500/30 hover:bg-emerald-600 transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined">chat</span>
                        Konfirmasi via WhatsApp
                    </a>
                    
                    <a href="<?= base_url() ?>" class="block mt-4 text-[#608a7e] font-bold text-sm hover:underline">Kembali ke Beranda</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyText(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Nomor rekening disalin!');
    });
}
</script>
<?= $this->endSection() ?>
