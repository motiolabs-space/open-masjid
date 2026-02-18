<!-- Modern Footer -->
<footer class="bg-white dark:bg-background-dark border-t border-[#dbe6e1] dark:border-[#1e3a2f] py-16 px-6">
    <div class="max-w-[1200px] mx-auto grid md:grid-cols-4 gap-12">
        <div class="col-span-1 md:col-span-2 flex flex-col gap-6">
            <a href="<?= base_url() ?>" class="flex items-center gap-2">
                <img src="<?= asset_url('logo.png') ?>" alt="Logo Masj.id" class="h-6">
            </a>
            <p class="text-[#3d5a4d] dark:text-gray-400 max-w-[320px]">
                Platform manajemen masjid berbasis cloud untuk transparansi keuangan dan pemberdayaan umat di era digital.
            </p>
        </div>
        <div class="flex flex-col gap-4">
            <h4 class="font-bold">Informasi</h4>
            <a class="text-[#3d5a4d] dark:text-gray-400 hover:text-primary transition-colors" href="<?= base_url('bantuan') ?>">Bantuan</a>
            <a class="text-[#3d5a4d] dark:text-gray-400 hover:text-primary transition-colors" href="<?= base_url('laporan') ?>">Laporan</a>
            <a class="text-[#3d5a4d] dark:text-gray-400 hover:text-primary transition-colors" href="<?= base_url('kontak') ?>">Kontak Kami</a>
        </div>
        <div class="flex flex-col gap-4">
            <h4 class="font-bold">Legal</h4>
            <a class="text-[#3d5a4d] dark:text-gray-400 hover:text-primary transition-colors" href="<?= base_url('privacy-policy') ?>">Privacy</a>
            <a class="text-[#3d5a4d] dark:text-gray-400 hover:text-primary transition-colors" href="<?= base_url('term') ?>">Term & Condition</a>
        </div>
    </div>
    <div class="max-w-[1200px] mx-auto mt-16 pt-8 border-t border-[#dbe6e1] dark:border-[#1e3a2f] flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-[#3d5a4d]">
        <p>© <?= date('Y') ?> Masj.id - Dikelola secara amanah untuk kemajuan ummat.</p>
        <div class="flex gap-6">
            <a class="hover:text-primary transition-colors" href="#">Instagram</a>
            <a class="hover:text-primary transition-colors" href="#">Twitter</a>
            <a class="hover:text-primary transition-colors" href="#">LinkedIn</a>
        </div>
    </div>
</footer>
