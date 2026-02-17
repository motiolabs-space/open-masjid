<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulasi Pembayaran - Masj.id</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-2xl shadow-xl max-w-md w-full border border-gray-100">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Simulasi Payment Gateway</h1>
            <p class="text-gray-500 text-sm mt-2">Environment: Sandbox (Dummy)</p>
        </div>

        <div class="bg-gray-50 p-6 rounded-xl border border-gray-200 mb-8 space-y-3">
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Invoice ID</span>
                <span class="font-mono font-bold text-gray-900"><?= $donation['invoice_number'] ?></span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Total Tagihan</span>
                <span class="font-bold text-blue-600 text-lg">Rp <?= number_format($donation['amount'], 0, ',', '.') ?></span>
            </div>
            <div class="border-t border-dashed border-gray-200 my-2"></div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Nama Donatur</span>
                <span class="font-medium text-gray-900"><?= esc($donation['donor_name']) ?></span>
            </div>
        </div>

        <form action="<?= base_url('payment/callback') ?>" method="post" class="space-y-3">
            <input type="hidden" name="invoice_number" value="<?= $donation['invoice_number'] ?>">
            
            <button type="submit" name="status" value="success" class="w-full py-3 px-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-green-600/20 active:scale-95">
                Bayar Sekarang (Sukses)
            </button>
            <button type="submit" name="status" value="failed" class="w-full py-3 px-4 bg-red-50 hover:bg-red-100 text-red-600 font-bold rounded-xl transition-all border border-red-200">
                Simulasi Gagal / Batal
            </button>
        </form>

        <div class="mt-8 text-center">
            <a href="<?= base_url() ?>" class="text-sm text-gray-400 hover:text-gray-600">Batalkan & Kembali ke Masj.id</a>
        </div>
    </div>

</body>
</html>
