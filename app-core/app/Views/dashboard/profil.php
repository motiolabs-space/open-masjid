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
            <div class="mt-8 pt-8 border-t border-[#e5e7eb] dark:border-white/10 px-8 pb-8">
                <h3 class="text-sm font-bold text-[#111816] dark:text-white mb-4">Wilayah Layanan</h3>
                <div id="serviceAreaContainer" class="flex flex-wrap gap-2 mb-6">
                    <?php foreach ($wilayah as $w): ?>
                    <span class="service-area-badge px-4 py-1.5 bg-primary/10 text-primary border border-primary/20 rounded-full text-sm font-medium flex items-center gap-2">
                        <?= esc($w['name']) ?>
                        <input type="hidden" name="wilayah[]" value="<?= esc($w['name']) ?>">
                        <span class="material-symbols-outlined text-sm cursor-pointer hover:text-red-500" onclick="this.parentElement.remove()">close</span>
                    </span>
                    <?php endforeach; ?>
                    
                    <button type="button" onclick="addServiceArea(this)" class="px-4 py-1.5 border border-dashed border-[#dbe6e3] rounded-full text-sm font-medium text-[#608a7e] hover:bg-[#f0f5f3] flex items-center gap-1 transition-colors">
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
                        <input name="is_external_service" value="1" <?= ($masjid['is_external_service'] ?? 0) == 1 ? 'checked' : '' ?> class="sr-only peer" type="checkbox"/>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                    </label>
                </div>
            </div>
            <script>
                function addServiceArea(btn) {
                    const name = prompt("Masukkan nama wilayah (contoh: RW 01 Selong):");
                    if (name && name.trim() !== "") {
                        const container = document.getElementById('serviceAreaContainer');
                        
                        const span = document.createElement('span');
                        span.className = "service-area-badge px-4 py-1.5 bg-primary/10 text-primary border border-primary/20 rounded-full text-sm font-medium flex items-center gap-2";
                        span.innerHTML = `${name.trim()} <input type="hidden" name="wilayah[]" value="${name.trim()}"> <span class="material-symbols-outlined text-sm cursor-pointer hover:text-red-500" onclick="this.parentElement.remove()">close</span>`;
                        
                        container.insertBefore(span, btn);
                    }
                }
            </script>
        </div>

    <!-- Section 3: Pengurus Masjid -->
    <div class="bg-white dark:bg-white/5 rounded-xl border border-[#e5e7eb] dark:border-white/10 overflow-hidden mb-8">
        <div class="p-6 border-b border-[#e5e7eb] dark:border-white/10 flex justify-between items-center">
            <h2 class="text-xl font-bold text-[#111816] dark:text-white">Pengurus Masjid</h2>
            <button type="button" onclick="openAddPengurusModal()" class="bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 transition-all">
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
                        <th class="px-6 py-4">Kontak</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e5e7eb] dark:divide-white/10">
                    <?php foreach ($pengurus as $p): ?>
                    <tr class="hover:bg-[#f0f5f3]/50 dark:hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-center bg-no-repeat bg-cover rounded-full size-8 bg-slate-200 flex items-center justify-center relative">
                                    <span class="material-symbols-outlined text-slate-400 text-sm">person</span>
                                    <?php if (($p['is_creator'] ?? 0) == 1): ?>
                                        <div class="absolute -top-1 -right-1 size-3 bg-primary border-2 border-white rounded-full title="Admin Utama"></div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-[#111816] dark:text-white flex items-center gap-1">
                                        <?= esc($p['user_name']) ?>
                                        <?php if (($p['is_creator'] ?? 0) == 1): ?>
                                            <span class="text-[10px] px-1.5 py-0.5 bg-primary/10 text-primary rounded font-bold uppercase">Utama</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-[#608a7e]">
                            <span class="flex flex-col">
                                <span><?= esc($p['title'] ?? (ucfirst($p['role']))) ?></span>
                                <span class="text-[10px] text-slate-400 uppercase font-bold"><?= esc($p['role']) ?></span>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-[#111816] dark:text-white">
                            <div class="flex flex-col gap-1">
                                <?php if (!empty($p['user_phone'])): 
                                    $waPhone = preg_replace('/[^0-9]/', '', $p['user_phone']);
                                    if (strpos($waPhone, '0') === 0) $waPhone = '62' . substr($waPhone, 1);
                                    elseif (strpos($waPhone, '62') !== 0) $waPhone = '62' . $waPhone;
                                ?>
                                    <a href="https://wa.me/<?= $waPhone ?>" target="_blank" class="flex items-center gap-1.5 text-green-600 hover:underline">
                                        <span class="material-symbols-outlined text-sm">chat</span>
                                        <?= esc($p['user_phone']) ?>
                                    </a>
                                <?php endif; ?>
                                
                                <?php if (!empty($p['user_email'])): ?>
                                    <a href="mailto:<?= esc($p['user_email']) ?>" class="flex items-center gap-1.5 text-primary hover:underline">
                                        <span class="material-symbols-outlined text-sm">mail</span>
                                        <?= esc($p['user_email']) ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center">
                                <span class="px-2.5 py-1 bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-400 text-xs font-bold rounded-full">Aktif</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <?php if (($p['is_creator'] ?? 0) == 0): ?>
                            <div class="flex justify-end gap-2">
                                <button type="button" 
                                    onclick='openEditPengurusModal(<?= json_encode([
                                        "id" => $p["id"],
                                        "name" => $p["user_name"],
                                        "phone" => $p["user_phone"] ?? "-",
                                        "title" => $p["title"],
                                        "role" => $p["role"]
                                    ]) ?>)' 
                                    class="text-[#608a7e] hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined">edit</span>
                                </button>
                                <button type="button" onclick="confirmDeletePengurus(<?= $p['id'] ?>, '<?= esc($p['user_name']) ?>')" class="text-[#608a7e] hover:text-red-500 transition-colors">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </div>
                            <?php else: ?>
                                <span class="text-xs text-slate-400 italic">Terkunci</span>
                            <?php endif; ?>
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
    <div class="space-y-8 pb-24">
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
<!-- Modal Tambah Pengurus -->
<div id="addPengurusModal" class="fixed inset-0 bg-black/50 z-[100] hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-[#1a2e28] rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all">
        <div class="p-6 border-b border-[#e5e7eb] dark:border-white/10 flex justify-between items-center bg-primary/5">
            <h3 class="text-lg font-bold text-[#111816] dark:text-white flex items-center gap-2">
                <span class="material-symbols-outlined text-primary" id="modalIcon">person_add</span>
                <span id="modalTitle">Tambah Pengurus Baru</span>
            </h3>
            <button onclick="closeAddPengurusModal()" class="text-[#608a7e] hover:text-red-500 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6 space-y-5">
            <input type="hidden" id="editPengurusId">
            <!-- Search User Section -->
            <div id="searchUserSection">
                <label class="block text-sm font-semibold text-[#111816] dark:text-white mb-2">Cari Jamaah (Nama/HP)</label>
                <div class="relative">
                    <input type="text" id="userSearchInput" oninput="searchUsers(this.value)" class="w-full rounded-xl border-[#dbe6e3] dark:bg-white/5 dark:border-white/10 focus:border-primary focus:ring-primary pl-10" placeholder="Ketik nama atau nomor HP...">
                    <span class="material-symbols-outlined absolute left-3 top-2.5 text-[#608a7e] text-xl">search</span>
                </div>
                <div id="searchResults" class="mt-2 max-h-40 overflow-y-auto border border-[#dbe6e3] dark:border-white/10 rounded-xl hidden bg-white dark:bg-[#111816] divide-y dark:divide-white/5 shadow-lg">
                    <!-- Results will appear here -->
                </div>
            </div>

            <!-- Selected User Display -->
            <div id="selectedUserDisplay" class="hidden p-4 bg-primary/5 border border-primary/20 rounded-xl items-center gap-3">
                <div class="size-10 bg-primary/10 rounded-full flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined">person</span>
                </div>
                <div class="flex-1">
                    <p id="selectedUserName" class="text-sm font-bold text-[#111816] dark:text-white"></p>
                    <p id="selectedUserPhone" class="text-xs text-[#608a7e]"></p>
                    <input type="hidden" id="selectedUserId">
                </div>
                <button onclick="clearSelectedUser()" class="text-red-400 hover:text-red-600">
                    <span class="material-symbols-outlined text-sm">close</span>
                </button>
            </div>

            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-[#111816] dark:text-white mb-2">Jabatan</label>
                    <input type="text" id="pengurusTitle" list="jabatanList" class="w-full rounded-xl border-[#dbe6e3] dark:bg-white/5 dark:border-white/10 focus:border-primary focus:ring-primary" placeholder="Contoh: Ketua, Sekretaris, Bendahara...">
                    <datalist id="jabatanList">
                        <option value="Ketua">
                        <option value="Sekretaris">
                        <option value="Bendahara">
                        <option value="Penasihat">
                        <option value="Humas">
                        <option value="Imam Besar">
                    </datalist>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#111816] dark:text-white mb-2">Role Akses</label>
                    <select id="pengurusRole" class="w-full rounded-xl border-[#dbe6e3] dark:bg-white/5 dark:border-white/10 focus:border-primary focus:ring-primary">
                        <option value="pengurus">Pengurus (Hanya Lihat)</option>
                        <option value="admin">Admin (Bisa Edit Profil)</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="p-6 bg-slate-50 dark:bg-white/5 flex gap-3">
            <button onclick="closeAddPengurusModal()" class="flex-1 px-4 py-2.5 text-[#608a7e] font-bold text-sm hover:bg-[#f0f5f3] dark:hover:bg-white/5 rounded-xl transition-all border border-[#dbe6e3] dark:border-white/10">Batal</button>
            <button onclick="submitAddPengurus()" id="submitBtn" class="flex-1 px-4 py-2.5 bg-primary hover:bg-primary/90 text-white font-bold text-sm rounded-xl shadow-lg shadow-primary/20 transition-all flex items-center justify-center gap-2">
                Simpan
            </button>
        </div>
    </div>
</div>

<script>
    function openAddPengurusModal() {
        document.getElementById('modalTitle').innerText = 'Tambah Pengurus Baru';
        document.getElementById('modalIcon').innerText = 'person_add';
        document.getElementById('editPengurusId').value = '';
        document.getElementById('searchUserSection').classList.remove('hidden');
        document.getElementById('selectedUserDisplay').classList.add('hidden');
        document.getElementById('addPengurusModal').classList.remove('hidden');
        document.getElementById('addPengurusModal').classList.add('flex');
    }

    function openEditPengurusModal(data) {
        document.getElementById('modalTitle').innerText = 'Edit Pengurus';
        document.getElementById('modalIcon').innerText = 'edit';
        document.getElementById('editPengurusId').value = data.id;
        
        // Setup selected user display
        document.getElementById('selectedUserName').innerText = data.name;
        document.getElementById('selectedUserPhone').innerText = data.phone;
        document.getElementById('selectedUserId').value = ''; // Not needed for update
        
        document.getElementById('searchUserSection').classList.add('hidden');
        document.getElementById('selectedUserDisplay').classList.remove('hidden');
        document.getElementById('selectedUserDisplay').classList.add('flex');
        
        // Set values
        document.getElementById('pengurusTitle').value = data.title || '';
        document.getElementById('pengurusRole').value = data.role;
        
        document.getElementById('addPengurusModal').classList.remove('hidden');
        document.getElementById('addPengurusModal').classList.add('flex');
    }

    function closeAddPengurusModal() {
        document.getElementById('addPengurusModal').classList.add('hidden');
        document.getElementById('addPengurusModal').classList.remove('flex');
        clearAddPengurusForm();
    }

    function clearAddPengurusForm() {
        document.getElementById('userSearchInput').value = '';
        document.getElementById('searchResults').classList.add('hidden');
        clearSelectedUser();
        document.getElementById('pengurusTitle').value = '';
        document.getElementById('pengurusRole').value = 'pengurus';
        document.getElementById('editPengurusId').value = '';
    }

    function clearSelectedUser() {
        document.getElementById('selectedUserDisplay').classList.add('hidden');
        document.getElementById('selectedUserDisplay').classList.remove('flex');
        document.getElementById('selectedUserId').value = '';
        document.getElementById('userSearchInput').parentElement.classList.remove('hidden');
        document.getElementById('searchUserSection').classList.remove('hidden');
    }

    let searchTimeout;
    async function searchUsers(q) {
        clearTimeout(searchTimeout);
        if (q.length < 2) {
            document.getElementById('searchResults').classList.add('hidden');
            return;
        }

        searchTimeout = setTimeout(async () => {
            try {
                const response = await fetch('<?= base_url('dashboard/users/search') ?>?q=' + encodeURIComponent(q));
                const users = await response.json();
                
                const resultsDiv = document.getElementById('searchResults');
                resultsDiv.innerHTML = '';
                
                if (users.length > 0) {
                    users.forEach(user => {
                        const div = document.createElement('div');
                        div.className = 'p-3 hover:bg-primary/5 cursor-pointer transition-colors';
                        div.innerHTML = `
                            <p class="text-sm font-bold text-[#111816] dark:text-white">${user.name}</p>
                            <p class="text-xs text-[#608a7e]">${user.phone || '-'}</p>
                        `;
                        div.onclick = () => selectUser(user);
                        resultsDiv.appendChild(div);
                    });
                    resultsDiv.classList.remove('hidden');
                } else {
                    resultsDiv.innerHTML = '<p class="p-3 text-xs text-slate-500 italic">User tidak ditemukan.</p>';
                    resultsDiv.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Error searching users:', error);
            }
        }, 300);
    }

    function selectUser(user) {
        document.getElementById('selectedUserId').value = user.id;
        document.getElementById('selectedUserName').innerText = user.name;
        document.getElementById('selectedUserPhone').innerText = user.phone || '-';
        
        document.getElementById('selectedUserDisplay').classList.remove('hidden');
        document.getElementById('selectedUserDisplay').classList.add('flex');
        document.getElementById('searchUserSection').classList.add('hidden');
        document.getElementById('searchResults').classList.add('hidden');
    }

    async function submitAddPengurus() {
        const editId = document.getElementById('editPengurusId').value;
        const userId = document.getElementById('selectedUserId').value;
        const role = document.getElementById('pengurusRole').value;
        const title = document.getElementById('pengurusTitle').value;
        const submitBtn = document.getElementById('submitBtn');

        if (!editId && !userId) {
            alert('Pilih jamaah terlebih dahulu.');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerText = 'Menyimpan...';

        try {
            const formData = new FormData();
            const url = editId ? '<?= base_url('dashboard/pengurus/update') ?>' : '<?= base_url('dashboard/pengurus/add') ?>';
            
            if (editId) {
                formData.append('id', editId);
            } else {
                formData.append('user_id', userId);
            }
            
            formData.append('role', role);
            formData.append('title', title);
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            
            if (result.status === 'success') {
                location.reload();
            } else {
                alert(result.message);
                submitBtn.disabled = false;
                submitBtn.innerText = 'Simpan';
            }
        } catch (error) {
            console.error('Error saving pengurus:', error);
            alert('Gagal menyimpan data pengurus.');
            submitBtn.disabled = false;
            submitBtn.innerText = 'Simpan';
        }
    }

    async function confirmDeletePengurus(id, name) {
        if (confirm(`Apakah Anda yakin ingin menghapus ${name} dari daftar pengurus?`)) {
            try {
                const formData = new FormData();
                formData.append('id', id);
                formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

                const response = await fetch('<?= base_url('dashboard/pengurus/delete') ?>', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                if (result.status === 'success') {
                    location.reload();
                } else {
                    alert(result.message);
                }
            } catch (error) {
                console.error('Error deleting pengurus:', error);
                alert('Gagal menghapus pengurus.');
            }
        }
    }
</script>
<?= $this->endSection() ?>
