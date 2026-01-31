<?= $this->extend('layout/masjid_public') ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="relative h-[60vh] md:h-[70vh] flex items-end pb-12 overflow-hidden">
    <?php 
        $photoUrl = !empty($masjid['foto_utama']) ? $storage->url($masjid['foto_utama']) : 'https://images.unsplash.com/photo-1596701062351-8c2c14d1fdd0?q=80&w=1600&auto=format&fit=crop';
    ?>
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat transition-transform duration-[20s] hover:scale-110" style="background-image: url('<?= $photoUrl ?>');"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
    
    <div class="max-w-[1200px] mx-auto px-6 w-full relative z-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div class="flex-1">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/20 backdrop-blur-md border border-white/20 text-white text-[10px] font-bold uppercase tracking-widest mb-4">
                    <span class="size-2 bg-emerald-400 rounded-full animate-pulse"></span>
                    Profil Terverifikasi
                </div>
                <h1 class="text-4xl md:text-6xl font-black text-white leading-tight mb-2 drop-shadow-xl"><?= esc($masjid['name']) ?></h1>
                <?php if (!empty($masjid['nama_resmi'])): ?>
                    <p class="text-white/70 text-lg md:text-xl font-medium mb-4 italic"><?= esc($masjid['nama_resmi']) ?></p>
                <?php endif; ?>
                <div class="flex flex-wrap gap-4 text-white/80 text-sm">
                    <div class="flex items-center gap-1.5 bg-white/10 backdrop-blur-sm px-3 py-1.5 rounded-lg border border-white/5">
                        <span class="material-symbols-outlined text-sm">location_on</span>
                        <?= esc($masjid['kabupaten']) ?>, <?= esc($masjid['provinsi']) ?>
                    </div>
                    <div class="flex items-center gap-1.5 bg-white/10 backdrop-blur-sm px-3 py-1.5 rounded-lg border border-white/5">
                        <span class="material-symbols-outlined text-sm">category</span>
                        <?= esc($masjid['jenis_masjid'] ?? 'Masjid Umum') ?>
                    </div>
                    <?php if (!empty($masjid['tahun_berdiri'])): ?>
                        <div class="flex items-center gap-1.5 bg-white/10 backdrop-blur-sm px-3 py-1.5 rounded-lg border border-white/5">
                            <span class="material-symbols-outlined text-sm">history</span>
                            Berdiri Thn <?= esc($masjid['tahun_berdiri']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="flex flex-col gap-3">
                <a href="#kontak" class="btn-primary-lg flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">volunteer_activism</span>
                    Donasi Sekarang
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Vision & Mission -->
<section id="tentang" class="py-24 px-6 bg-white dark:bg-background-dark">
    <div class="max-w-[1200px] mx-auto">
        <div class="grid lg:grid-cols-2 gap-16 items-start">
            <div class="space-y-8">
                <div>
                    <h2 class="text-sm font-bold text-primary uppercase tracking-[0.2em] mb-3">Tentang Kami</h2>
                    <h3 class="text-3xl md:text-4xl font-black leading-tight mb-6">Membangun Ummat, <br/>Memakmurkan Rumah Allah</h3>
                    <p class="text-[#3d5a4d] dark:text-gray-400 text-lg leading-relaxed">
                        Selamat datang di portal informasi resmi <?= esc($masjid['name']) ?>. Melalui platform Masj.id, kami berupaya menghadirkan transparansi pengelolaan dan kemudahan bagi jamaah dalam berinteraksi dengan program-program edukasi, sosial, dan ibadah kami.
                    </p>
                </div>
                
                <?php if (!empty($wilayah)): ?>
                <div class="bg-primary/5 border border-primary/10 rounded-2xl p-6">
                    <h4 class="font-bold text-primary mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined">map</span>
                        Wilayah Layanan Kami:
                    </h4>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($wilayah as $w): ?>
                            <span class="px-3 py-1 bg-white border border-primary/20 rounded-full text-xs font-semibold text-primary"><?= esc($w['name']) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="grid gap-6">
                <!-- Visi -->
                <div class="bg-background-light dark:bg-[#1a2e25] border border-[#dbe6e1] dark:border-[#1e3a2f] p-8 rounded-[2rem] shadow-sm relative overflow-hidden group">
                    <span class="material-symbols-outlined absolute -top-4 -right-4 text-8xl text-primary/5 transition-transform group-hover:scale-125">visibility</span>
                    <h4 class="text-xl font-bold mb-4 flex items-center gap-2">
                        <span class="size-8 bg-primary rounded-lg flex items-center justify-center text-white">
                            <span class="material-symbols-outlined text-sm">visibility</span>
                        </span>
                        Visi
                    </h4>
                    <p class="text-[#3d5a4d] dark:text-gray-400 font-medium italic">
                        "<?= esc($masjid['visi'] ?? 'Menjadi pusat kegiatan ibadah dan pemberdayaan masyarakat yang mandiri dan profesional.') ?>"
                    </p>
                </div>

                <!-- Misi -->
                <div class="bg-white dark:bg-[#1a2e25] border border-primary/20 p-8 rounded-[2rem] shadow-xl relative overflow-hidden group">
                    <span class="material-symbols-outlined absolute -top-4 -right-4 text-8xl text-primary/5 transition-transform group-hover:scale-125">flag</span>
                    <h4 class="text-xl font-bold mb-4 flex items-center gap-2 text-primary">
                        <span class="size-8 bg-primary rounded-lg flex items-center justify-center text-white shadow-lg">
                            <span class="material-symbols-outlined text-sm">flag</span>
                        </span>
                        Misi
                    </h4>
                    <ul class="space-y-4">
                        <?php 
                            $misiArray = !empty($masjid['misi']) ? explode("\n", $masjid['misi']) : ['Mewujudkan pelayanan ibadah yang nyaman.', 'Mengaktifkan pendidikan berbasis masjid.', 'Memberdayakan ekonomi jamaah sekitarnya.'];
                            foreach ($misiArray as $m): if (empty(trim($m))) continue;
                        ?>
                            <li class="flex gap-3 text-sm font-medium text-[#3d5a4d] dark:text-gray-400">
                                <span class="material-symbols-outlined text-primary">check_circle</span>
                                <?= esc(trim($m)) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Committee Section -->
<?php if (!empty($pengurus)): ?>
<section id="pengurus" class="py-24 px-6 bg-background-light dark:bg-background-dark/50">
    <div class="max-w-[1200px] mx-auto">
        <div class="text-center mb-16">
            <h2 class="text-sm font-bold text-primary uppercase tracking-[0.2em] mb-3">Struktur Organisasi</h2>
            <h3 class="text-3xl md:text-5xl font-black">Pengurus Masjid</h3>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php foreach ($pengurus as $p): ?>
                <div class="bg-white dark:bg-[#1a2e25] border border-[#dbe6e1] dark:border-[#1e3a2f] p-6 rounded-3xl shadow-sm hover:shadow-xl transition-all group flex flex-col items-center text-center">
                    <div class="size-20 bg-primary/10 rounded-full flex items-center justify-center text-primary mb-4 relative overflow-hidden">
                        <span class="material-symbols-outlined text-3xl">person</span>
                        <?php if (($p['is_creator'] ?? 0) == 1): ?>
                            <div class="absolute inset-0 bg-primary/20 flex items-center justify-center">
                                <span class="material-symbols-outlined text-3xl opacity-20">shield</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <h4 class="text-lg font-bold mb-1"><?= esc($p['user_name']) ?></h4>
                    <p class="text-sm font-bold text-primary mb-4 uppercase tracking-wider"><?= esc($p['title'] ?? ucfirst($p['role'])) ?></p>
                    
                    <div class="mt-auto flex gap-2">
                        <?php if (!empty($p['user_phone'])): 
                            $waPhone = preg_replace('/[^0-9]/', '', $p['user_phone']);
                            if (strpos($waPhone, '0') === 0) $waPhone = '62' . substr($waPhone, 1);
                            elseif (strpos($waPhone, '62') !== 0) $waPhone = '62' . $waPhone;
                        ?>
                            <a href="https://wa.me/<?= $waPhone ?>" target="_blank" class="size-10 bg-green-500 rounded-xl flex items-center justify-center text-white hover:scale-110 transition-transform">
                                <span class="material-symbols-outlined">chat</span>
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($p['user_email'])): ?>
                            <a href="mailto:<?= esc($p['user_email']) ?>" class="size-10 bg-primary rounded-xl flex items-center justify-center text-white hover:scale-110 transition-transform">
                                <span class="material-symbols-outlined">mail</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Berita & Kegiatan Section (Placeholder) -->
<?php if ($masjid['menu_berita'] ?? 1): ?>
<section id="berita" class="py-24 px-6 bg-background-light dark:bg-background-dark/50 overflow-hidden">
    <div class="max-w-[1200px] mx-auto text-center">
        <h2 class="text-sm font-bold text-primary uppercase tracking-[0.2em] mb-3">Warta Masjid</h2>
        <h3 class="text-3xl md:text-5xl font-black mb-8">Berita & Kegiatan</h3>
        <div class="p-12 bg-white dark:bg-[#1a2e25] rounded-[2.5rem] border border-dashed border-[#dbe6e1] dark:border-[#1e3a2f]">
            <span class="material-symbols-outlined text-6xl text-primary/20 mb-4 font-light">edit_calendar</span>
            <p class="text-[#608a7e] font-medium">Belum ada berita atau kegiatan terbaru yang dipublikasikan.</p>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Program Section (Placeholder) -->
<?php if ($masjid['menu_program'] ?? 1): ?>
<section id="program" class="py-24 px-6 bg-white dark:bg-background-dark overflow-hidden">
    <div class="max-w-[1200px] mx-auto text-center">
        <h2 class="text-sm font-bold text-primary uppercase tracking-[0.2em] mb-3">Layanan Kami</h2>
        <h3 class="text-3xl md:text-5xl font-black mb-8">Program Unggulan</h3>
        <div class="p-12 bg-background-light dark:bg-[#11241d] rounded-[2.5rem] border border-dashed border-[#dbe6e1] dark:border-[#1e3a2f]">
            <span class="material-symbols-outlined text-6xl text-primary/20 mb-4 font-light">volunteer_activism</span>
            <p class="text-[#608a7e] font-medium">Informasi program sedang disiapkan oleh pengurus masjid.</p>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Laporan Section (Placeholder) -->
<?php if ($masjid['menu_laporan'] ?? 1): ?>
<section id="laporan" class="py-24 px-6 bg-background-light dark:bg-background-dark/50 overflow-hidden">
    <div class="max-w-[1200px] mx-auto text-center">
        <h2 class="text-sm font-bold text-primary uppercase tracking-[0.2em] mb-3">Transparansi</h2>
        <h3 class="text-3xl md:text-5xl font-black mb-8">Laporan Keuangan</h3>
        <div class="p-12 bg-white dark:bg-[#1a2e25] rounded-[2.5rem] border border-dashed border-[#dbe6e1] dark:border-[#1e3a2f]">
            <span class="material-symbols-outlined text-6xl text-primary/20 mb-4 font-light">account_balance_wallet</span>
            <p class="text-[#608a7e] font-medium">Laporan keuangan bulanan akan segera ditampilkan di sini.</p>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Gallery Section -->
<?php if (!empty($gallery)): ?>
<section id="galeri" class="py-24 px-6 bg-white dark:bg-background-dark overflow-hidden">
    <div class="max-w-[1200px] mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-end gap-8 mb-16">
            <div>
                <h2 class="text-sm font-bold text-primary uppercase tracking-[0.2em] mb-3">Galeri & Fasilitas</h2>
                <h3 class="text-3xl md:text-5xl font-black italic">Dokumentasi Kami</h3>
            </div>
            
            <?php 
                $uniqueCats = array_unique(array_column($gallery, 'category'));
            ?>
            <div class="flex flex-wrap gap-2">
                <button onclick="filterPublicGallery('all')" class="pub-gallery-btn active px-5 py-2 rounded-full text-xs font-bold border border-primary bg-primary text-white transition-all shadow-lg" data-category="all">Semua</button>
                <?php foreach ($uniqueCats as $cat): ?>
                    <button onclick="filterPublicGallery('<?= esc($cat) ?>')" class="pub-gallery-btn px-5 py-2 rounded-full text-xs font-bold border border-[#dbe6e3] text-[#608a7e] hover:border-primary hover:text-primary transition-all" data-category="<?= esc($cat) ?>"><?= esc($cat) ?></button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6" id="publicGalleryGrid">
            <?php foreach ($gallery as $img): ?>
                <div class="photo-item-pub aspect-square rounded-3xl overflow-hidden relative group cursor-pointer shadow-lg shadow-black/5" data-category="<?= esc($img['category']) ?>">
                    <img src="<?= $storage->url($img['image_path']) ?>" alt="Gallery" class="size-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent opacity-0 group-hover:opacity-100 transition-opacity p-6 flex items-end">
                        <span class="text-white text-[10px] font-bold uppercase tracking-widest px-2 py-1 bg-primary rounded"><?= esc($img['category']) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script>
    function filterPublicGallery(category) {
        const photos = document.querySelectorAll('.photo-item-pub');
        const buttons = document.querySelectorAll('.pub-gallery-btn');
        
        buttons.forEach(btn => {
            if (btn.dataset.category === category) {
                btn.classList.add('active', 'bg-primary', 'text-white', 'shadow-lg');
                btn.classList.remove('border-[#dbe6e3]', 'text-[#608a7e]');
            } else {
                btn.classList.remove('active', 'bg-primary', 'text-white', 'shadow-lg');
                btn.classList.add('border-[#dbe6e3]', 'text-[#608a7e]');
            }
        });

        photos.forEach(photo => {
            if (category === 'all' || photo.dataset.category === category) {
                photo.style.display = 'block';
                photo.classList.add('animate-in', 'fade-in', 'zoom-in');
            } else {
                photo.style.display = 'none';
            }
        });
    }
</script>
<?php endif; ?>

<!-- Location Section -->
<?php if ($masjid['menu_kontak'] ?? 1): ?>
<section id="kontak" class="py-24 px-6 bg-background-light dark:bg-background-dark/50">
    <div class="max-w-[1200px] mx-auto">
        <div class="flex flex-col lg:flex-row gap-16">
            <div class="flex-1 space-y-8">
                <div>
                    <h2 class="text-sm font-bold text-primary uppercase tracking-[0.2em] mb-3">Informasi Kontak</h2>
                    <h3 class="text-3xl font-black mb-6">Lokasi & Alamat</h3>
                    <p class="text-[#3d5a4d] dark:text-gray-400 text-lg mb-8 leading-relaxed">
                        Kami mengundang Anda untuk bersilaturahmi langsung ke <?= esc($masjid['name']) ?>. Berikut adalah informasi alamat dan titik lokasi tepat kami:
                    </p>
                </div>

                <div class="grid sm:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-[#1a2e25] p-6 rounded-2xl border border-[#dbe6e1] dark:border-[#1e3a2f] shadow-sm">
                        <div class="size-10 bg-primary/10 rounded-lg flex items-center justify-center text-primary mb-4">
                            <span class="material-symbols-outlined">location_on</span>
                        </div>
                        <h4 class="font-bold mb-2">Alamat Lengkap</h4>
                        <p class="text-sm text-gray-500 leading-relaxed"><?= esc($masjid['address']) ?></p>
                    </div>
                    <div class="bg-white dark:bg-[#1a2e25] p-6 rounded-2xl border border-[#dbe6e1] dark:border-[#1e3a2f] shadow-sm">
                        <div class="size-10 bg-primary/10 rounded-lg flex items-center justify-center text-primary mb-4">
                            <span class="material-symbols-outlined">mail</span>
                        </div>
                        <h4 class="font-bold mb-2">Korespondensi</h4>
                        <p class="text-sm text-gray-500"><?= esc($masjid['username']) ?>@masj.id</p>
                    </div>
                </div>

                <!-- Call to Action -->
                <div class="p-8 bg-primary rounded-[2.5rem] text-white relative overflow-hidden shadow-2xl shadow-primary/30">
                    <div class="relative z-10">
                        <h4 class="text-2xl font-black mb-4">Ingin Berkolaborasi?</h4>
                        <p class="text-white/70 mb-8 max-w-[400px]">Hubungi sekretariat kami untuk pengajuan kerjasama, program sosial, atau informasi lainnya.</p>
                        <a href="mailto:<?= esc($masjid['username']) ?>@masj.id" class="inline-flex items-center gap-2 bg-white text-primary px-8 py-3 rounded-xl font-bold hover:bg-emerald-50 transition-all">
                            <span class="material-symbols-outlined text-sm">send</span>
                            Kirim Pesan
                        </a>
                    </div>
                    <span class="material-symbols-outlined absolute -bottom-10 -right-10 text-[12rem] opacity-10">forum</span>
                </div>
            </div>

            <div class="flex-1 min-h-[400px] h-full rounded-[2.5rem] overflow-hidden border-8 border-white dark:border-white/5 shadow-2xl relative">
                <div id="map" class="size-full bg-slate-100"></div>
                <!-- Pin Detail Overlay -->
                <div class="absolute bottom-6 left-6 right-6 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md p-5 rounded-2xl border border-[#dbe6e1] dark:border-white/10 shadow-lg flex items-center gap-4">
                    <div class="size-12 bg-primary rounded-xl flex items-center justify-center text-white flex-shrink-0">
                        <span class="material-symbols-outlined">mosque</span>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1 text-left">Titik Koordinat</p>
                        <p class="text-xs font-bold truncate text-left"><?= $masjid['latitude'] ?>, <?= $masjid['longitude'] ?></p>
                    </div>
                    <a href="https://www.google.com/maps/search/?api=1&query=<?= $masjid['latitude'] ?>,<?= $masjid['longitude'] ?>" target="_blank" class="ml-auto size-10 bg-primary/10 rounded-full flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-colors">
                        <span class="material-symbols-outlined text-lg">directions</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Implementation -->
<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?= env('GOOGLE_MAPS_API_KEY') ?>&callback=initMap"></script>
<script>
    function initMap() {
        const loc = { lat: <?= $masjid['latitude'] ?? 0 ?>, lng: <?= $masjid['longitude'] ?? 0 ?> };
        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 15,
            center: loc,
            styles: [
                {
                    "featureType": "all",
                    "elementType": "labels.text.fill",
                    "stylers": [{ "saturation": 36 }, { "color": "#000000" }, { "lightness": 40 }]
                },
                {
                    "featureType": "water",
                    "elementType": "all",
                    "stylers": [{ "color": "#e9f3f1" }, { "visibility": "on" }]
                }
            ],
            disableDefaultUI: true,
            zoomControl: true,
        });
        const marker = new google.maps.Marker({
            position: loc,
            map: map,
            icon: {
                url: "https://maps.google.com/mapfiles/ms/icons/green-dot.png"
            }
        });
    }
</script>
<?php endif; ?>

<?= $this->endSection() ?>
