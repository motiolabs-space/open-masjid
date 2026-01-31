<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<div class="max-w-5xl mx-auto">
    <!-- Page Heading & Progress -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
            <div>
                <h1 class="text-4xl font-black tracking-tight text-[#111816] dark:text-white mb-2">Profil Masjid</h1>
                <p class="text-[#608a7e] text-base">Kelola informasi dasar dan identitas masjid Anda di platform Masj.id.</p>
            </div>
            <div class="bg-white dark:bg-white/5 p-4 rounded-xl border border-[#e5e7eb] dark:border-white/10 min-w-[320px]">
                <div class="flex justify-between items-center mb-2">
                    <p class="text-[#111816] dark:text-white text-sm font-semibold">Kelengkapan Profil</p>
                    <p class="text-primary text-sm font-bold"><?= $percentage ?>%</p>
                </div>
                <div class="h-2 w-full bg-[#dbe6e3] dark:bg-white/10 rounded-full overflow-hidden">
                    <div class="h-full bg-primary" style="width: <?= $percentage ?>%;"></div>
                </div>
                <p class="text-[#608a7e] text-xs mt-2 italic">Semakin lengkap profil, semakin mudah transparansi & koordinasi.</p>
            </div>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm font-medium">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm font-medium">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>
    </div>

    <form action="<?= base_url('dashboard/profil') ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>

    <!-- Section 1: Data Utama -->
    <div class="bg-white dark:bg-white/5 rounded-xl border border-[#e5e7eb] dark:border-white/10 overflow-hidden mb-8">
        <div class="p-6 border-b border-[#e5e7eb] dark:border-white/10">
            <h2 class="text-xl font-bold text-[#111816] dark:text-white">Data Utama</h2>
        </div>
        <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-[#111816] dark:text-white mb-1.5">Nama Masjid <span class="text-red-500">*</span></label>
                    <input name="name" class="w-full rounded-lg border-[#dbe6e3] dark:bg-white/5 dark:border-white/10 focus:border-primary focus:ring-primary" type="text" value="<?= esc($masjid['name'] ?? '') ?>" required/>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#111816] dark:text-white mb-1.5">Nama Resmi (Sesuai SK)</label>
                    <input name="nama_resmi" class="w-full rounded-lg border-[#dbe6e3] dark:bg-white/5 dark:border-white/10 focus:border-primary focus:ring-primary" placeholder="Masukkan nama resmi..." type="text" value="<?= esc($masjid['nama_resmi'] ?? '') ?>"/>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-[#111816] dark:text-white mb-1.5">Tahun Berdiri</label>
                        <div class="relative">
                            <input name="tahun_berdiri" class="w-full rounded-lg border-[#dbe6e3] dark:bg-white/5 dark:border-white/10 focus:border-primary focus:ring-primary" type="text" value="<?= esc($masjid['tahun_berdiri'] ?? '') ?>"/>
                            <span class="material-symbols-outlined absolute right-3 top-2 text-[#608a7e] text-xl">calendar_month</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-[#111816] dark:text-white mb-1.5">Jenis Masjid</label>
                        <select name="jenis_masjid" class="w-full rounded-lg border-[#dbe6e3] dark:bg-white/5 dark:border-white/10 focus:border-primary focus:ring-primary">
                            <option value="">Pilih Jenis</option>
                            <option value="Masjid Raya / Agung (Wilayah)" <?= ($masjid['jenis_masjid'] ?? '') == 'Masjid Raya / Agung (Wilayah)' ? 'selected' : '' ?>>Masjid Raya / Agung (Regional/Pusat)</option>
                            <option value="Masjid Besar / Jami (Lokal)" <?= ($masjid['jenis_masjid'] ?? '') == 'Masjid Besar / Jami (Lokal)' ? 'selected' : '' ?>>Masjid Besar / Jami (Kecamatan/Kelurahan)</option>
                            <option value="Masjid Perumahan" <?= ($masjid['jenis_masjid'] ?? '') == 'Masjid Perumahan' ? 'selected' : '' ?>>Masjid Perumahan</option>
                            <option value="Masjid Kampung" <?= ($masjid['jenis_masjid'] ?? '') == 'Masjid Kampung' ? 'selected' : '' ?>>Masjid Kampung</option>
                            <option value="Masjid Tempat Publik (Kantor/Umum)" <?= ($masjid['jenis_masjid'] ?? '') == 'Masjid Tempat Publik (Kantor/Umum)' ? 'selected' : '' ?>>Masjid Tempat Publik (Kantor/Mall/RS)</option>
                            <option value="Musholla / Langgar" <?= ($masjid['jenis_masjid'] ?? '') == 'Musholla / Langgar' ? 'selected' : '' ?>>Musholla / Langgar</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#111816] dark:text-white mb-1.5">Nomor SK/Legalitas</label>
                    <input name="no_sk" class="w-full rounded-lg border-[#dbe6e3] dark:bg-white/5 dark:border-white/10 focus:border-primary focus:ring-primary" type="text" value="<?= esc($masjid['no_sk'] ?? '') ?>"/>
                </div>
            </div>
            <div class="space-y-4">
                <label class="block text-sm font-semibold text-[#111816] dark:text-white">Foto Utama Masjid</label>
                <div class="relative group cursor-pointer border-2 border-dashed border-[#dbe6e3] dark:border-white/10 rounded-xl overflow-hidden aspect-video flex items-center justify-center bg-[#f0f5f3] dark:bg-white/5">
                    <?php 
                        $photoUrl = !empty($masjid['foto_utama']) ? $storage->url($masjid['foto_utama']) : 'https://images.unsplash.com/photo-1596701062351-8c2c14d1fdd0?q=80&w=1600&auto=format&fit=crop';
                    ?>
                    <div id="photoPreview" class="absolute inset-0 bg-center bg-no-repeat bg-cover opacity-80" style='background-image: url("<?= $photoUrl ?>");'></div>
                    <div class="absolute inset-0 bg-black/40 group-hover:bg-black/50 transition-all flex flex-col items-center justify-center text-white opacity-0 group-hover:opacity-100">
                        <span class="material-symbols-outlined text-4xl mb-2">upload</span>
                        <p class="font-bold">Ubah Foto</p>
                        <p class="text-xs opacity-80">Rasio disarankan 16:9</p>
                    </div>
                    <input type="file" name="foto_utama" class="absolute inset-0 opacity-0 cursor-pointer" onchange="previewImage(this)">
                    <div class="z-[1] flex flex-col items-center group-hover:hidden <?= !empty($masjid['foto_utama']) ? 'hidden' : '' ?>">
                        <span class="material-symbols-outlined text-[#608a7e] text-3xl">add_a_photo</span>
                    </div>
                </div>
                <p class="text-xs text-[#608a7e]">Maksimal 5MB (JPG, PNG). Gunakan foto terbaik masjid Anda tampak depan.</p>
            </div>
            <script>
                function previewImage(input) {
                    if (input.files && input.files[0]) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            document.getElementById('photoPreview').style.backgroundImage = 'url(' + e.target.result + ')';
                        }
                        reader.readAsDataURL(input.files[0]);
                    }
                }
            </script>
        </div>
    </div>

    <!-- Section 2: Lokasi & Wilayah -->
    <div class="bg-white dark:bg-white/5 rounded-xl border border-[#e5e7eb] dark:border-white/10 overflow-hidden mb-8">
        <div class="p-6 border-b border-[#e5e7eb] dark:border-white/10">
            <h2 class="text-xl font-bold text-[#111816] dark:text-white">Lokasi & Wilayah</h2>
        </div>
        <div class="p-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <div class="lg:col-span-7 space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-[#111816] dark:text-white mb-1.5">Alamat Lengkap</label>
                        <textarea name="address" class="w-full rounded-lg border-[#dbe6e3] dark:bg-white/5 dark:border-white/10 focus:border-primary focus:ring-primary" rows="3"><?= esc($masjid['address'] ?? '') ?></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-[#111816] dark:text-white mb-1.5">Provinsi</label>
                            <select id="provinceSelect" name="provinsi" class="w-full rounded-lg border-[#dbe6e3] dark:bg-white/5 dark:border-white/10 focus:border-primary focus:ring-primary" onchange="loadRegencies(this.value)">
                                <option value="">Pilih Provinsi</option>
                                <?php foreach ($provinces as $p): ?>
                                    <option value="<?= $p['id'] ?>" data-name="<?= esc($p['name']) ?>" <?= ($masjid['provinsi'] ?? '') == $p['name'] ? 'selected' : '' ?>><?= esc($p['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-[#111816] dark:text-white mb-1.5">Kota/Kabupaten</label>
                            <select id="regencySelect" name="kabupaten" class="w-full rounded-lg border-[#dbe6e3] dark:bg-white/5 dark:border-white/10 focus:border-primary focus:ring-primary">
                                <option value="">Pilih Kota/Kabupaten</option>
                                <?php if (!empty($masjid['kabupaten'])): ?>
                                    <option value="<?= esc($masjid['kabupaten']) ?>" selected><?= esc($masjid['kabupaten']) ?></option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-[#111816] dark:text-white mb-1.5">Kecamatan</label>
                            <input name="kecamatan" class="w-full rounded-lg border-[#dbe6e3] dark:bg-white/5 dark:border-white/10 focus:border-primary focus:ring-primary" type="text" value="<?= esc($masjid['kecamatan'] ?? '') ?>"/>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-[#111816] dark:text-white mb-1.5">Kelurahan</label>
                            <input name="kelurahan" class="w-full rounded-lg border-[#dbe6e3] dark:bg-white/5 dark:border-white/10 focus:border-primary focus:ring-primary" type="text" value="<?= esc($masjid['kelurahan'] ?? '') ?>"/>
                        </div>
                    </div>
                </div>
                <div class="lg:col-span-5 space-y-6">
                    <label class="block text-sm font-semibold text-[#111816] dark:text-white mb-1.5">Titik Lokasi (Pin Map)</label>
                    <div id="map" class="relative rounded-xl overflow-hidden h-[240px] bg-[#f0f5f3] dark:bg-white/5 border border-[#e5e7eb] dark:border-white/10">
                        <div class="absolute inset-0 flex items-center justify-center bg-slate-100 dark:bg-white/5">
                            <p class="text-xs text-[#608a7e]">Memuat Peta...</p>
                        </div>
                    </div>
                    <input type="hidden" name="latitude" id="latInput" value="<?= esc($masjid['latitude'] ?? '') ?>">
                    <input type="hidden" name="longitude" id="lngInput" value="<?= esc($masjid['longitude'] ?? '') ?>">
                    
                    <button type="button" onclick="getCurrentLocation()" class="w-full bg-white dark:bg-white/5 border border-[#e5e7eb] dark:border-white/10 px-3 py-2.5 rounded-lg text-xs font-bold flex items-center justify-center gap-1.5 hover:bg-[#f0f5f3] transition-all">
                        <span class="material-symbols-outlined text-sm">my_location</span>
                        Gunakan Lokasi Saat Ini
                    </button>
                    <p class="text-[10px] text-[#608a7e] italic mt-1">Klik pada peta untuk menggeser PIN ke lokasi tepat masjid Anda.</p>
                </div>
            </div>
            <script>
                let map;
                let marker;

                async function loadRegencies(provinceId, selectedName = null) {
                    const regencySelect = document.getElementById('regencySelect');
                    if (!provinceId) {
                        regencySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
                        return;
                    }

                    try {
                        const response = await fetch('<?= base_url('dashboard/regencies') ?>/' + provinceId);
                        const regencies = await response.json();
                        
                        regencySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
                        regencies.forEach(r => {
                            const option = document.createElement('option');
                            option.value = r.name;
                            option.textContent = r.name;
                            if (selectedName && r.name === selectedName) {
                                option.selected = true;
                            }
                            regencySelect.appendChild(option);
                        });
                    } catch (error) {
                        console.error('Error loading regencies:', error);
                    }
                }

                function initMap() {
                    const latInput = document.getElementById('latInput');
                    const lngInput = document.getElementById('lngInput');
                    const lat = parseFloat(latInput.value) || -6.2088; 
                    const lng = parseFloat(lngInput.value) || 106.8456;
                    
                    const myLatLng = { lat: lat, lng: lng };

                    map = new google.maps.Map(document.getElementById("map"), {
                        zoom: 15,
                        center: myLatLng,
                        mapTypeControl: false,
                        streetViewControl: false,
                        fullscreenControl: false
                    });

                    marker = new google.maps.Marker({
                        position: myLatLng,
                        map,
                        draggable: true,
                        title: "Lokasi Masjid",
                    });

                    marker.addListener("dragend", () => {
                        const position = marker.getPosition();
                        latInput.value = position.lat().toFixed(8);
                        lngInput.value = position.lng().toFixed(8);
                    });

                    map.addListener("click", (mapsMouseEvent) => {
                        const position = mapsMouseEvent.latLng;
                        marker.setPosition(position);
                        latInput.value = position.lat().toFixed(8);
                        lngInput.value = position.lng().toFixed(8);
                    });
                }

                function getCurrentLocation() {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                const pos = {
                                    lat: position.coords.latitude,
                                    lng: position.coords.longitude,
                                };
                                map.setCenter(pos);
                                marker.setPosition(pos);
                                document.getElementById('latInput').value = pos.lat.toFixed(8);
                                document.getElementById('lngInput').value = pos.lng.toFixed(8);
                            },
                            () => {
                                alert("Error: The Geolocation service failed.");
                            }
                        );
                    } else {
                        alert("Error: Your browser doesn't support geolocation.");
                    }
                }

                document.addEventListener('DOMContentLoaded', function() {
                    const provinceSelect = document.getElementById('provinceSelect');
                    if (provinceSelect && provinceSelect.value) {
                        loadRegencies(provinceSelect.value, '<?= esc($masjid['kabupaten'] ?? '') ?>');
                    }
                });
            </script>
            <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?= env('GOOGLE_MAPS_API_KEY') ?>&callback=initMap"></script>
            </div>
            <div class="mt-8 pt-8 border-t border-[#e5e7eb] dark:border-white/10">
                <h3 class="text-sm font-bold text-[#111816] dark:text-white mb-4">Wilayah Layanan</h3>
                <div class="flex flex-wrap gap-2 mb-6">
                    <span class="px-4 py-1.5 bg-primary/10 text-primary border border-primary/20 rounded-full text-sm font-medium flex items-center gap-2">
                        RW 01 Selong <span class="material-symbols-outlined text-sm cursor-pointer">close</span>
                    </span>
                    <span class="px-4 py-1.5 bg-primary/10 text-primary border border-primary/20 rounded-full text-sm font-medium flex items-center gap-2">
                        RW 02 Selong <span class="material-symbols-outlined text-sm cursor-pointer">close</span>
                    </span>
                    <span class="px-4 py-1.5 bg-primary/10 text-primary border border-primary/20 rounded-full text-sm font-medium flex items-center gap-2">
                        RW 03 Selong <span class="material-symbols-outlined text-sm cursor-pointer">close</span>
                    </span>
                    <button class="px-4 py-1.5 border border-dashed border-[#dbe6e3] rounded-full text-sm font-medium text-[#608a7e] hover:bg-[#f0f5f3] flex items-center gap-1 transition-colors">
                        <span class="material-symbols-outlined text-sm">add</span> Tambah Wilayah
                    </button>
                </div>
                <div class="flex items-center justify-between p-4 bg-background-light dark:bg-white/5 rounded-lg">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary">public</span>
                        <div>
                            <p class="text-sm font-bold text-[#111816] dark:text-white">Melayani warga di luar lingkungan</p>
                            <p class="text-xs text-[#608a7e]">Izinkan pendaftar/pemohon layanan dari luar wilayah RW utama.</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input checked="" class="sr-only peer" type="checkbox"/>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 3: Pengurus Masjid -->
    <div class="bg-white dark:bg-white/5 rounded-xl border border-[#e5e7eb] dark:border-white/10 overflow-hidden mb-8">
        <div class="p-6 border-b border-[#e5e7eb] dark:border-white/10 flex justify-between items-center">
            <h2 class="text-xl font-bold text-[#111816] dark:text-white">Pengurus Masjid</h2>
            <button class="bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 transition-all">
                <span class="material-symbols-outlined text-lg">person_add</span>
                Tambah Pengurus
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-[#f0f5f3] dark:bg-white/5 text-[#608a7e] text-xs font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Nama Lengkap</th>
                        <th class="px-6 py-4">Jabatan</th>
                        <th class="px-6 py-4">WhatsApp</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e5e7eb] dark:divide-white/10">
                    <?php foreach ($pengurus as $p): ?>
                    <tr class="hover:bg-[#f0f5f3]/50 dark:hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-center bg-no-repeat bg-cover rounded-full size-8 bg-slate-200 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-slate-400 text-sm">person</span>
                                </div>
                                <span class="text-sm font-semibold text-[#111816] dark:text-white"><?= esc($p['user_name']) ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-[#608a7e]"><?= esc($p['role'] == 'pengurus' ? 'Pengurus' : $p['role']) ?></td>
                        <td class="px-6 py-4 text-sm text-[#111816] dark:text-white"><?= esc($p['user_phone'] ?? '-') ?></td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center">
                                <span class="px-2.5 py-1 bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-400 text-xs font-bold rounded-full">Aktif</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button class="text-[#608a7e] hover:text-primary transition-colors">
                                <span class="material-symbols-outlined">edit</span>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($pengurus)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500 text-sm">Belum ada pengurus terdaftar.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 4: Informasi Pendukung -->
    <div class="space-y-6 pb-24">
        <!-- Vision & Mission -->
        <div class="bg-white dark:bg-white/5 rounded-xl border border-[#e5e7eb] dark:border-white/10 overflow-hidden">
            <div class="p-6 border-b border-[#e5e7eb] dark:border-white/10 flex items-center justify-between cursor-pointer">
                <h2 class="text-xl font-bold text-[#111816] dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">visibility</span>
                    Visi & Misi
                </h2>
                <span class="material-symbols-outlined text-[#608a7e]">expand_more</span>
            </div>
            <div class="p-8 space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-[#111816] dark:text-white mb-2">Visi Masjid</label>
                    <textarea name="visi" class="w-full rounded-lg border-[#dbe6e3] dark:bg-white/5 dark:border-white/10 focus:border-primary focus:ring-primary" placeholder="Tuliskan visi utama masjid..." rows="3"><?= esc($masjid['visi'] ?? '') ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#111816] dark:text-white mb-2">Misi Masjid</label>
                    <textarea name="misi" class="w-full rounded-lg border-[#dbe6e3] dark:bg-white/5 dark:border-white/10 focus:border-primary focus:ring-primary" placeholder="Tuliskan misi-misi masjid (pisahkan dengan baris baru)..." rows="4"><?= esc($masjid['misi'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Facilities & Gallery -->
        <div class="bg-white dark:bg-white/5 rounded-xl border border-[#e5e7eb] dark:border-white/10 overflow-hidden">
            <div class="p-6 border-b border-[#e5e7eb] dark:border-white/10">
                <h2 class="text-xl font-bold text-[#111816] dark:text-white">Galeri Foto & Fasilitas</h2>
            </div>
            <div class="p-8">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
                    <div class="aspect-square bg-center bg-cover rounded-lg border border-[#e5e7eb] dark:border-white/10 relative group" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAp0qv3finh4CU-nKuk34pn-F4T751xNGsz0FrDr755NHqu0We8JUI8dhUAPIpqyx3NKQ_dvWpNOMDVNHPzkEse09iknxQXohnFd01vCkSWTywISbGkKEqmXtJBlz0fbycZjHvFDn7ipnN3ZbkG7oY3plwjJ5Brb3UvumFr6mZI1n-JN0fmV602TXRvleZNA1I7JDVOxzu97hF8GYa3PFP4qXNO2CWBpr9490ApxZKhWz-2Rfi8fWoZiNGAv2AK9odDeTbFljjLNNSP");'>
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded-lg">
                            <span class="material-symbols-outlined text-white">delete</span>
                        </div>
                    </div>
                    <button class="aspect-square border-2 border-dashed border-[#dbe6e3] dark:border-white/10 rounded-lg flex flex-col items-center justify-center text-[#608a7e] hover:bg-[#f0f5f3] dark:hover:bg-white/5 transition-colors">
                        <span class="material-symbols-outlined text-2xl mb-1">add_photo_alternate</span>
                        <span class="text-[10px] font-bold">TAMBAH</span>
                    </button>
                </div>
                <div class="space-y-4">
                    <label class="block text-sm font-semibold text-[#111816] dark:text-white">Area Fokus & Fasilitas Utama</label>
                    <div class="flex flex-wrap gap-3">
                        <label class="flex items-center gap-2 px-4 py-2 bg-[#f0f5f3] dark:bg-white/5 rounded-lg cursor-pointer hover:bg-primary/5 transition-colors">
                            <input checked="" class="rounded text-primary focus:ring-primary border-[#dbe6e3]" type="checkbox"/>
                            <span class="text-sm font-medium">Pendidikan</span>
                        </label>
                        <label class="flex items-center gap-2 px-4 py-2 bg-[#f0f5f3] dark:bg-white/5 rounded-lg cursor-pointer hover:bg-primary/5 transition-colors">
                            <input checked="" class="rounded text-primary focus:ring-primary border-[#dbe6e3]" type="checkbox"/>
                            <span class="text-sm font-medium">Sosial/Santunan</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sticky Bottom Bar -->
<footer class="fixed bottom-0 left-72 right-0 bg-white dark:bg-[#0f172a] border-t border-[#e5e7eb] dark:border-white/10 px-8 py-4 z-20 shadow-[0_-4px_12px_rgba(0,0,0,0.05)]">
    <div class="max-w-5xl mx-auto flex items-center justify-between">
        <div class="flex items-center gap-6">
            <div class="hidden sm:flex items-center gap-3">
                <div class="w-24 h-2 bg-[#dbe6e3] dark:bg-white/10 rounded-full overflow-hidden">
                    <div class="h-full bg-primary" style="width: <?= $percentage ?>%;"></div>
                </div>
                <span class="text-sm font-bold text-primary"><?= $percentage ?>% Lengkap</span>
            </div>
            <a class="text-sm font-bold text-primary hover:underline flex items-center gap-1.5" href="<?= base_url('/') ?>">
                <span class="material-symbols-outlined text-lg">visibility</span>
                Lihat Halaman Publik
            </a>
        </div>
        <div class="flex items-center gap-4">
            <button type="button" class="px-6 py-2.5 text-[#608a7e] font-bold text-sm hover:bg-[#f0f5f3] dark:hover:bg-white/5 rounded-lg transition-colors">
                Batalkan
            </button>
            <button type="submit" class="px-8 py-2.5 bg-primary hover:bg-primary/90 text-white font-bold text-sm rounded-lg shadow-lg shadow-primary/20 transition-all">
                Simpan Perubahan
            </button>
        </div>
    </div>
</footer>
</form>
<?= $this->endSection() ?>
