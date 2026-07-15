<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<div class="px-8 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8 flex items-center gap-4">
            <a href="<?= base_url('dashboard') ?>" class="size-10 bg-white dark:bg-white/5 border border-[#dbe6e3] dark:border-white/10 rounded-xl flex items-center justify-center text-[#608a7e] hover:text-primary transition-all">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div>
                <h1 class="text-3xl font-black text-[#111816] dark:text-white tracking-tight">Pengaturan Pembayaran</h1>
                <p class="text-[#608a7e]">Konfigurasi metode penerimaan donasi masjid.</p>
            </div>
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

        <form action="<?= base_url('dashboard/pembayaran/save') ?>" method="POST" enctype="multipart/form-data" class="space-y-8">
            <?= csrf_field() ?>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="md:col-span-2 space-y-6">
                    
                    <!-- Mode Selection -->
                    <div class="bg-white dark:bg-white/5 rounded-3xl border border-[#e5e7eb] dark:border-white/10 p-8 space-y-6">
                        <h3 class="font-bold text-lg border-b border-[#e5e7eb] pb-4">Mode Pembayaran</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="cursor-pointer relative">
                                <input type="radio" name="payment_mode" value="manual" <?= ($settings['payment_mode'] ?? 'manual') == 'manual' ? 'checked' : '' ?> class="peer sr-only" onchange="toggleMode()">
                                <div class="p-6 rounded-2xl border-2 border-[#dbe6e3] peer-checked:border-primary peer-checked:bg-primary/5 transition-all text-center h-full flex flex-col items-center justify-center gap-4 hover:border-primary/50">
                                    <div class="size-16 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 peer-checked:bg-primary peer-checked:text-white transition-colors">
                                        <span class="material-symbols-outlined text-3xl">account_balance</span>
                                    </div>
                                    <div class="space-y-1">
                                        <h4 class="font-bold text-lg">Transfer Manual / QRIS Statis</h4>
                                        <p class="text-xs text-gray-500">Donatur transfer ke rekening masjid & konfirmasi manual.</p>
                                    </div>
                                </div>
                                <div class="absolute top-4 right-4 text-primary opacity-0 peer-checked:opacity-100 transition-opacity">
                                    <span class="material-symbols-outlined">check_circle</span>
                                </div>
                            </label>

                            <label class="cursor-pointer relative">
                                <input type="radio" name="payment_mode" value="multipay" <?= ($settings['payment_mode'] ?? '') == 'multipay' ? 'checked' : '' ?> class="peer sr-only" onchange="toggleMode()">
                                <div class="p-6 rounded-2xl border-2 border-[#dbe6e3] peer-checked:border-primary peer-checked:bg-primary/5 transition-all text-center h-full flex flex-col items-center justify-center gap-4 hover:border-primary/50">
                                    <div class="size-16 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 peer-checked:bg-primary peer-checked:text-white transition-colors">
                                        <span class="material-symbols-outlined text-3xl">payments</span>
                                    </div>
                                    <div class="space-y-1">
                                        <h4 class="font-bold text-lg">Payment Gateway (Multipay)</h4>
                                        <p class="text-xs text-gray-500">Otomatisasi pembayaran via VA, E-Wallet, QRIS Dinamis.</p>
                                    </div>
                                </div>
                                <div class="absolute top-4 right-4 text-primary opacity-0 peer-checked:opacity-100 transition-opacity">
                                    <span class="material-symbols-outlined">check_circle</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Manual Settings -->
                    <div id="manual-section" class="bg-white dark:bg-white/5 rounded-3xl border border-[#e5e7eb] dark:border-white/10 p-8 space-y-6 <?= ($settings['payment_mode'] ?? 'manual') != 'manual' ? 'hidden' : '' ?>">
                        <h3 class="font-bold text-lg border-b border-[#e5e7eb] pb-4">Konfigurasi Manual</h3>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold mb-2">Nama Bank</label>
                                <input type="text" name="bank_name" value="<?= esc($settings['bank_name'] ?? '') ?>" placeholder="Contoh: BSI, BCA, Mandiri" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl focus:ring-2 focus:ring-primary p-4 font-bold">
                            </div>
                            <div>
                                <label class="block text-sm font-bold mb-2">Nomor Rekening</label>
                                <input type="text" name="bank_account_number" value="<?= esc($settings['bank_account_number'] ?? '') ?>" placeholder="1234xxxxxx" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl focus:ring-2 focus:ring-primary p-4 font-bold">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-2">Atas Nama Rekening</label>
                            <input type="text" name="bank_account_name" value="<?= esc($settings['bank_account_name'] ?? '') ?>" placeholder="Contoh: DKM Masjid Al-Falah" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl focus:ring-2 focus:ring-primary p-4 font-bold">
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-2">Upload QRIS (Opsional)</label>
                            <div class="flex items-center gap-4">
                                <?php if (!empty($settings['qris_image'])): ?>
                                    <div class="size-24 rounded-xl border border-gray-200 p-2 bg-white flex-shrink-0">
                                        <img src="<?= $storage->url($settings['qris_image']) ?>" class="w-full h-full object-contain">
                                    </div>
                                <?php endif; ?>
                                <input type="file" name="qris_image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                            </div>
                            <p class="text-xs text-gray-400 mt-2">Upload gambar QR Code statis dari bank Anda.</p>
                        </div>
                    </div>

                    <!-- Multipay Settings -->
                    <div id="multipay-section" class="bg-white dark:bg-white/5 rounded-3xl border border-[#e5e7eb] dark:border-white/10 p-8 space-y-6 <?= ($settings['payment_mode'] ?? '') != 'multipay' ? 'hidden' : '' ?>">
                        <div class="flex items-center justify-between border-b border-[#e5e7eb] pb-4">
                            <h3 class="font-bold text-lg">Konfigurasi Multipay</h3>
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-[10px] font-black uppercase">Automated</span>
                        </div>
                        
                        <div class="bg-blue-50 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300 p-6 rounded-2xl text-sm space-y-4 border border-blue-100 dark:border-blue-900/50">
                            <div class="flex gap-3">
                                <span class="material-symbols-outlined">info</span>
                                <div>
                                    <p class="font-bold">Informasi Integrasi</p>
                                    <p class="opacity-80">Gunakan detail di bawah ini untuk menghubungkan masjid Anda dengan gateway Multipay.</p>
                                </div>
                            </div>
                            <div class="p-4 bg-white/50 dark:bg-black/20 rounded-xl space-y-2">
                                <p class="text-[10px] font-bold uppercase opacity-60">Webhook / Callback URL</p>
                                <div class="flex items-center justify-between gap-4">
                                    <code class="text-primary font-bold break-all"><?= base_url('payment/callback') ?></code>
                                    <button type="button" onclick="copyToClipboard('<?= base_url('payment/callback') ?>')" class="flex-shrink-0 text-primary hover:text-emerald-700">
                                        <span class="material-symbols-outlined text-sm">content_copy</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="grid gap-6">
                            <div>
                                <label class="block text-sm font-bold mb-2">API Key / Merchant ID</label>
                                <input type="text" name="multipay_api_key" value="<?= esc($settings['multipay_api_key'] ?? '') ?>" placeholder="Masukkan API Key dari dashboard Multipay" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl focus:ring-2 focus:ring-primary p-4 font-bold">
                            </div>
                            <div>
                                <label class="block text-sm font-bold mb-2">Secret Key</label>
                                <input type="password" name="multipay_secret_key" value="<?= esc($settings['multipay_secret_key'] ?? '') ?>" placeholder="••••••••" class="w-full bg-[#f0f5f3] dark:bg-white/5 border-none rounded-xl focus:ring-2 focus:ring-primary p-4 font-bold">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Save Action -->
                <div class="space-y-6">
                    <div class="bg-white dark:bg-white/5 rounded-3xl border border-[#e5e7eb] dark:border-white/10 p-6 sticky top-8">
                        <h3 class="font-bold mb-4">Simpan Pengaturan</h3>
                        <p class="text-sm text-gray-500 mb-6">Pastikan data yang Anda masukkan benar agar donasi dapat diterima dengan lancar.</p>
                        
                        <button type="submit" class="w-full bg-primary text-white py-4 rounded-2xl font-black shadow-lg shadow-primary/20 hover:bg-emerald-900 transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined">save</span>
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleMode() {
        const mode = document.querySelector('input[name="payment_mode"]:checked').value;
        const manualSec = document.getElementById('manual-section');
        const multipaySec = document.getElementById('multipay-section');

        if (mode === 'manual') {
            manualSec.classList.remove('hidden');
            multipaySec.classList.add('hidden');
        } else {
            manualSec.classList.add('hidden');
            multipaySec.classList.remove('hidden');
        }
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('Callback URL berhasil disalin!');
        });
    }
</script>
<?= $this->endSection() ?>
