<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex justify-between items-start mb-4">
            <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl text-emerald-600">
                <span class="material-symbols-outlined">groups</span>
            </div>
            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">+12 Aktif</span>
        </div>
        <p class="text-slate-500 text-sm font-medium">Jamaah Terdata</p>
        <h3 class="text-3xl font-bold mt-1">1,482</h3>
    </div>
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex justify-between items-start mb-4">
            <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl text-emerald-600">
                <span class="material-symbols-outlined">groups</span>
            </div>
            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">+12 Aktif</span>
        </div>
        <p class="text-slate-500 text-sm font-medium">Jamaah Terdata</p>
        <h3 class="text-3xl font-bold mt-1">1,482</h3>
    </div>
    
    <?php if (session()->get('role') === 'pengurus'): ?>
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex justify-between items-start mb-4">
            <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl text-blue-600">
                <span class="material-symbols-outlined">account_balance_wallet</span>
            </div>
        </div>
        <p class="text-slate-500 text-sm font-medium">Dana Amanah Aktif</p>
        <h3 class="text-3xl font-bold mt-1">Rp 128.5M</h3>
    </div>
    <?php endif; ?>

    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex justify-between items-start mb-4">
            <div class="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-xl text-amber-600">
                <span class="material-symbols-outlined">auto_graph</span>
            </div>
        </div>
        <p class="text-slate-500 text-sm font-medium">Program Berjalan</p>
        <h3 class="text-3xl font-bold mt-1">14</h3>
    </div>

    <?php if (session()->get('role') === 'pengurus'): ?>
    <div class="bg-red-50 dark:bg-red-950/30 p-6 rounded-xl border-2 border-red-200 dark:border-red-900/50 shadow-sm flex flex-col justify-between">
        <div>
            <div class="flex items-center gap-2 text-red-600 mb-2">
                <span class="material-symbols-outlined text-xl">emergency</span>
                <span class="text-xs font-bold uppercase tracking-wider">Alert Sosial</span>
            </div>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">6 Keluarga</h3>
            <p class="text-xs text-slate-600 dark:text-slate-400 mt-1 italic">Membutuhkan bantuan segera</p>
        </div>
        <button class="mt-4 w-full py-2 bg-red-600 text-white rounded-lg text-xs font-bold hover:bg-red-700 transition-colors flex items-center justify-center gap-2">
            Lihat & Tindak Lanjut <span class="material-symbols-outlined text-sm">arrow_forward</span>
        </button>
    </div>
    <?php else: ?>
    <div class="bg-emerald-50 dark:bg-emerald-950/30 p-6 rounded-xl border-2 border-emerald-200 dark:border-emerald-900/50 shadow-sm flex flex-col justify-between">
        <div>
            <div class="flex items-center gap-2 text-emerald-600 mb-2">
                <span class="material-symbols-outlined text-xl">volunteer_activism</span>
                <span class="text-xs font-bold uppercase tracking-wider">Aksi Kebaikan</span>
            </div>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Mari Berbagi</h3>
            <p class="text-xs text-slate-600 dark:text-slate-400 mt-1 italic">Lihat program bantuan yang tersedia</p>
        </div>
        <a href="<?= base_url('kebaikan') ?>" class="mt-4 w-full py-2 bg-primary text-white rounded-lg text-xs font-bold hover:bg-primary/90 transition-colors flex items-center justify-center gap-2">
            Ikut Berkontribusi <span class="material-symbols-outlined text-sm">favorite</span>
        </a>
    </div>
    <?php endif; ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-8">
        <div class="space-y-4">
            <div class="flex items-center justify-between px-2">
                <h3 class="text-lg font-bold flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">analytics</span>
                    Program & Progress Pendanaan
                </h3>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                            <th class="px-6 py-4 text-xs font-bold uppercase text-slate-500">Program Utama</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase text-slate-500">Progress Dana</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase text-slate-500">Status</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase text-slate-500 text-right">Update Terakhir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <tr>
                            <td class="px-6 py-5">
                                <div class="flex flex-col gap-1">
                                    <span class="font-bold text-sm">Renovasi Menara</span>
                                    <span class="w-fit px-2 py-0.5 bg-blue-50 text-blue-600 text-[10px] font-bold rounded uppercase">Infrastruktur</span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="w-48">
                                    <div class="flex justify-between text-[10px] font-bold mb-1">
                                        <span>78%</span>
                                        <span class="text-slate-400">Rp 150jt / 200jt</span>
                                    </div>
                                    <div class="h-2 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                        <div class="bg-primary h-full rounded-full" style="width: 78%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="inline-flex items-center gap-1 text-emerald-600 bg-emerald-50 px-2 py-1 rounded text-[10px] font-bold">
                                    <span class="size-1.5 rounded-full bg-emerald-600"></span> On Track
                                </span>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <span class="text-xs text-slate-500">2 jam yang lalu</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-5">
                                <div class="flex flex-col gap-1">
                                    <span class="font-bold text-sm">Santunan Anak Yatim</span>
                                    <span class="w-fit px-2 py-0.5 bg-purple-50 text-purple-600 text-[10px] font-bold rounded uppercase">Sosial</span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="w-48">
                                    <div class="flex justify-between text-[10px] font-bold mb-1">
                                        <span>45%</span>
                                        <span class="text-slate-400">Rp 22jt / 50jt</span>
                                    </div>
                                    <div class="h-2 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                        <div class="bg-amber-500 h-full rounded-full" style="width: 45%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="inline-flex items-center gap-1 text-amber-600 bg-amber-50 px-2 py-1 rounded text-[10px] font-bold">
                                    <span class="size-1.5 rounded-full bg-amber-600"></span> Perlu Update
                                </span>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <span class="text-xs text-slate-500">Kemarin, 14:00</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="space-y-4">
            <div class="flex items-center justify-between px-2">
                <h3 class="text-lg font-bold flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">newspaper</span>
                    Kegiatan & Berita Terbaru
                </h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Activity 1 -->
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden group">
                    <div class="h-32 bg-slate-200 relative overflow-hidden">
                        <img alt="Activity" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBNrCVi18zpfFAOGaa579Dvwn6Be1KAzqeT_FFcHDCr03B8CqPnfFuz2qVX-6YPtJdzsBr4jXReo1vDd9tCetJ3orRdYZm1M1FZJ2ZYMXqeZYC4Akd4aoAQx4iOdJlsHTGwPUgwkSaSeeOGNUUJADPlBNGsqyLyGjrEtNBfyw5VsgXDAydysgOuZjz9ESVImpWKXyAFOipHJxNf8oGbCP8tBZKGuIUO0nt1UwIk0dmL_M5Ti9fygSXyOBAZVNCZahzEgXMYpOw5jWzk"/>
                        <span class="absolute top-2 right-2 px-2 py-1 bg-white/90 text-primary text-[10px] font-bold rounded shadow-sm">Dakwah</span>
                    </div>
                    <div class="p-4">
                        <p class="text-[10px] text-slate-400 font-bold mb-1">12 OKT 2023</p>
                        <h4 class="text-sm font-bold line-clamp-2 mb-3">Kajian Spesial: Manajemen Keuangan Keluarga Islami</h4>
                        <a class="text-primary text-[11px] font-bold flex items-center gap-1 hover:underline" href="#">
                            <span class="material-symbols-outlined text-sm">link</span> Dokumentasi
                        </a>
                    </div>
                </div>
                <!-- Activity 2 -->
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden group">
                    <div class="h-32 bg-slate-200 relative overflow-hidden">
                        <img alt="Activity" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuARGYts7CaDoe3vyVUsuzfQ2xeCNG4eJaJvJF-jk0O9n_ymLfImsiL8wVSEqaXzjHluBNj1BZrSJVWnk7GPzGCEG9vMC6eH2qLZIkjywOkRlhut72L6c2LviAFwjc3E90DGrlRKVjodKFnsLlQjLI9meeuTRNHh4CR5nA8mebEQxrPGwQIO-6gFPX7_5u_Kcwu3WZ27C38pc5NyoVCQu05eHDd6_26bPrI4AcJtqJP9L44UJyyA5ZMuQUblfZkFN-rvUXdbWKVKZ72J"/>
                        <span class="absolute top-2 right-2 px-2 py-1 bg-white/90 text-primary text-[10px] font-bold rounded shadow-sm">Sosial</span>
                    </div>
                    <div class="p-4">
                        <p class="text-[10px] text-slate-400 font-bold mb-1">10 OKT 2023</p>
                        <h4 class="text-sm font-bold line-clamp-2 mb-3">Penyaluran Beras Jumat Barokah Tahap III</h4>
                        <a class="text-primary text-[11px] font-bold flex items-center gap-1 hover:underline" href="#">
                            <span class="material-symbols-outlined text-sm">link</span> Dokumentasi
                        </a>
                    </div>
                </div>
                <!-- Activity 3 -->
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden group">
                    <div class="h-32 bg-slate-200 relative overflow-hidden">
                        <img alt="Activity" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuB5JEjMZM8itE_3-59jItq3PmRtYsHmNmek3Lb9CgGj3oCKgrrvkuNWo7CHWh8o7QJ0aUOtvwm4QfCpKzRSyEx4VYSJz-C-dYENTBvM6w_zCx-VL7ubs3jfWZc84XGdXHzZyIZcu_CoUT9u__1LZc3cE72uDRrFvbbzYa7g7_P9bv7hDBwURreUYezs_28--G3awCR5VCNV4b34VYubNtxgs1AWX4YBG7v-brwDmf5CAIdUS1zkJgwsLqbKLBv8_9jSWCS0vFicdJZK"/>
                        <span class="absolute top-2 right-2 px-2 py-1 bg-white/90 text-primary text-[10px] font-bold rounded shadow-sm">Rutin</span>
                    </div>
                    <div class="p-4">
                        <p class="text-[10px] text-slate-400 font-bold mb-1">08 OKT 2023</p>
                        <h4 class="text-sm font-bold line-clamp-2 mb-3">Kerja Bakti Persiapan Maulid Nabi 1445H</h4>
                        <a class="text-primary text-[11px] font-bold flex items-center gap-1 hover:underline" href="#">
                            <span class="material-symbols-outlined text-sm">link</span> Dokumentasi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="space-y-8">
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <span class="font-bold text-sm">Oktober 2023</span>
                <div class="flex gap-2">
                    <button class="size-7 flex items-center justify-center hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors"><span class="material-symbols-outlined text-base">chevron_left</span></button>
                    <button class="size-7 flex items-center justify-center hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors"><span class="material-symbols-outlined text-base">chevron_right</span></button>
                </div>
            </div>
            <div class="grid grid-cols-7 gap-1 text-center text-[10px] font-bold text-slate-400 mb-2 uppercase">
                <div>S</div><div>S</div><div>R</div><div>K</div><div>J</div><div>S</div><div>M</div>
            </div>
            <div class="grid grid-cols-7 gap-1 text-center text-xs font-medium mb-8">
                <div class="p-2 text-slate-300">28</div><div class="p-2 text-slate-300">29</div><div class="p-2 text-slate-300">30</div>
                <div class="p-2">1</div><div class="p-2">2</div><div class="p-2">3</div><div class="p-2">4</div>
                <div class="p-2">5</div><div class="p-2">6</div><div class="p-2 bg-primary text-white rounded-full font-bold shadow-md shadow-primary/20">7</div><div class="p-2">8</div>
                <div class="p-2">9</div><div class="p-2">10</div><div class="p-2">11</div><div class="p-2">12</div>
            </div>
            <div class="space-y-4">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
                    <span class="size-1.5 bg-primary rounded-full"></span> Mendatang
                </p>
                <div class="flex gap-3 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg">
                    <div class="w-1 bg-primary rounded-full shrink-0"></div>
                    <div>
                        <p class="text-sm font-bold">Kajian Maghrib</p>
                        <p class="text-[11px] text-slate-500">Hari ini, 18:30 WIB</p>
                    </div>
                </div>
                <div class="flex gap-3 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg">
                    <div class="w-1 bg-blue-400 rounded-full shrink-0"></div>
                    <div>
                        <p class="text-sm font-bold">Rapat Koordinasi Zakat</p>
                        <p class="text-[11px] text-slate-500">Besok, 09:00 WIB</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-primary/5 dark:bg-primary/10 border border-primary/20 p-6 rounded-2xl">
            <h4 class="text-primary text-sm font-bold mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">assignment_turned_in</span>
                Perlu Tindak Lanjut
            </h4>
            <ul class="space-y-3">
                <li class="flex items-center gap-3">
                    <input class="rounded border-primary/30 text-primary focus:ring-primary/20 size-4" type="checkbox"/>
                    <span class="text-xs text-slate-700 dark:text-slate-300 font-medium">Approve laporan keuangan Sep</span>
                </li>
                <li class="flex items-center gap-3">
                    <input class="rounded border-primary/30 text-primary focus:ring-primary/20 size-4" type="checkbox"/>
                    <span class="text-xs text-slate-700 dark:text-slate-300 font-medium">Update dokumentasi renovasi</span>
                </li>
                <li class="flex items-center gap-3">
                    <input class="rounded border-primary/30 text-primary focus:ring-primary/20 size-4" type="checkbox"/>
                    <span class="text-xs text-slate-700 dark:text-slate-300 font-medium">Verifikasi data 3 warga baru</span>
                </li>
            </ul>
        </div>
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <h4 class="text-sm font-bold mb-4">Ringkasan Keuangan</h4>
            <div class="space-y-3">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-500">Dana Masuk</span>
                    <span class="font-bold text-emerald-600">+ Rp 45.2M</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-500">Dana Tersalurkan</span>
                    <span class="font-bold text-red-500">- Rp 12.8M</span>
                </div>
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center">
                    <span class="text-sm font-bold">Saldo Amanah</span>
                    <span class="text-sm font-bold text-primary">Rp 32.4M</span>
                </div>
            </div>
            <div class="mt-4 p-2 bg-slate-50 dark:bg-slate-800 rounded flex items-start gap-2">
                <span class="material-symbols-outlined text-xs text-slate-400">info</span>
                <p class="text-[9px] text-slate-500 leading-tight italic">Laporan ini bersifat transparan dan dapat diaudit secara publik melalui portal jamaah.</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pb-8">
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="size-12 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500">
                <span class="material-symbols-outlined">support_agent</span>
            </div>
            <div>
                <p class="font-bold text-sm">Butuh bantuan teknis?</p>
                <p class="text-xs text-slate-500">Hubungi tim support Masj.id (Respon &lt; 10mnt)</p>
            </div>
        </div>
        <button class="px-5 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-lg text-xs font-bold hover:bg-slate-200 transition-colors">Hubungi CS</button>
    </div>
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="size-12 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                <span class="material-symbols-outlined">share</span>
            </div>
            <div>
                <p class="font-bold text-sm">Unduh & Bagikan Laporan</p>
                <p class="text-xs text-slate-500">Format PDF, XLS, atau share via WhatsApp</p>
            </div>
        </div>
        <div class="flex gap-2">
            <button class="p-2.5 bg-primary/10 text-primary rounded-lg hover:bg-primary/20 transition-colors">
                <span class="material-symbols-outlined text-sm">download</span>
            </button>
            <button class="px-5 py-2.5 bg-primary text-white rounded-lg text-xs font-bold hover:bg-primary/90 transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">send</span> Bagikan
            </button>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
