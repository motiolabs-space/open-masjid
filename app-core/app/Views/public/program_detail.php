<?= $this->extend('layout/masjid_public') ?>

<?= $this->section('content') ?>
<!-- Hero Section -->
<section class="relative pt-32 pb-20 px-6 bg-white dark:bg-background-dark overflow-hidden">
    <div class="max-w-[1000px] mx-auto">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 mb-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
            <a href="<?= base_url($masjid['username']) ?>" class="text-[10px] font-bold uppercase tracking-wider text-[#608a7e] hover:text-primary transition-colors">Beranda</a>
            <span class="material-symbols-outlined text-xs text-xl text-gray-300">chevron_right</span>
            <a href="<?= base_url($masjid['username'] . '/program') ?>" class="text-[10px] font-bold uppercase tracking-wider text-[#608a7e] hover:text-primary transition-colors">Agenda</a>
            <span class="material-symbols-outlined text-xs text-gray-300">chevron_right</span>
            <span class="text-[10px] font-bold uppercase tracking-wider text-primary">Detail Kegiatan</span>
        </nav>

        <h1 class="text-4xl md:text-6xl font-black mb-8 leading-tight animate-in fade-in slide-in-from-bottom-8 duration-1000">
            <?= esc($program['title']) ?>
        </h1>

        <div class="flex flex-wrap items-center gap-6 text-sm text-[#608a7e] animate-in fade-in duration-1000 delay-300">
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 bg-primary text-white text-[10px] font-black uppercase tracking-widest rounded-lg">
                    <?= esc($program['category_name'] ?: 'Umum') ?>
                </span>
            </div>
            <div class="h-8 w-px bg-gray-100 dark:bg-white/10 hidden sm:block"></div>
            <div class="flex items-center gap-2">
                <div class="size-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-lg">mosque</span>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Penyelenggara</p>
                    <p class="font-bold text-slate-900 dark:text-white"><?= esc($masjid['name']) ?></p>
                </div>
            </div>
            <div class="h-8 w-px bg-gray-100 dark:bg-white/10 hidden sm:block"></div>
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-primary">event</span>
                <span class="font-bold text-slate-900 dark:text-white"><?= date('l, d M Y', strtotime($program['date_start'])) ?></span>
            </div>
        </div>
    </div>
</section>

<!-- Content Section -->
<section class="pb-32 px-6">
    <div class="max-w-[1000px] mx-auto lg:grid lg:grid-cols-12 gap-16">
        <div class="lg:col-span-7">
            <!-- Poster -->
            <?php if (!empty($program['thumbnail'])): ?>
                <div class="rounded-[2.5rem] overflow-hidden bg-gray-100 shadow-2xl mb-12 animate-in fade-in zoom-in duration-1000">
                    <img src="<?= $storage->url($program['thumbnail']) ?>" class="w-full object-cover">
                </div>
            <?php endif; ?>

            <!-- Description -->
            <?php
                // tautan_aman(): tautan diisi pengurus & dipasang sebagai href ke
                // pengunjung, jadi skema non-http(s) (mis. javascript:) dibuang di
                // sini juga — baris lama bisa saja tersimpan sebelum penyaringan
                // di sisi penyimpanan dipasang.
                $streamUrl   = tautan_aman($program['stream_url'] ?? '');
                $daftarUrl   = tautan_aman($program['registration_link'] ?? '');
                $streamEmbed = null;
                if ($streamUrl && preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|live/|embed/|shorts/))([\w-]{11})~', $streamUrl, $mm)) {
                    $streamEmbed = 'https://www.youtube.com/embed/' . $mm[1];
                }
            ?>
            <?php if ($streamUrl): ?>
                <div class="mb-10">
                    <h3 class="text-2xl font-black mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-red-500">live_tv</span> Live Streaming
                    </h3>
                    <?php if ($streamEmbed): ?>
                        <div class="aspect-video rounded-2xl overflow-hidden border border-[#dbe6e3] dark:border-white/10 shadow-lg">
                            <iframe src="<?= esc($streamEmbed, 'attr') ?>" class="w-full h-full" title="Live Streaming" frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                        </div>
                    <?php else: ?>
                        <a href="<?= esc($streamUrl, 'attr') ?>" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-2 px-6 py-3 bg-red-500 text-white rounded-xl font-bold hover:bg-red-600 transition-all">
                            <span class="material-symbols-outlined">play_circle</span> Tonton Siaran
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="article-content prose prose-lg dark:prose-invert max-w-none prose-headings:font-black prose-p:text-[#4a5568] dark:prose-p:text-gray-300 prose-a:text-primary animate-in fade-in slide-in-from-bottom-8 duration-1000">
                <h3 class="text-2xl font-black mb-4">Tentang Kegiatan</h3>
                <?= $program['description'] ?>
            </div>

            <?php if (!empty($program['impact_published'])): ?>
                <div class="mt-12 bg-primary/[0.04] border border-primary/15 rounded-3xl p-6 md:p-8">
                    <div class="flex items-center gap-2 mb-5">
                        <span class="material-symbols-outlined text-primary">volunteer_activism</span>
                        <h3 class="text-2xl font-black text-[#111816] dark:text-white">Dampak Program</h3>
                    </div>

                    <?php if (!empty($program['beneficiaries_count'])): ?>
                        <div class="inline-flex items-baseline gap-2 bg-white dark:bg-white/5 rounded-2xl px-5 py-3 mb-5 border border-primary/10">
                            <span class="text-3xl font-black text-primary"><?= number_format($program['beneficiaries_count'], 0, ',', '.') ?></span>
                            <span class="text-sm font-bold text-[#608a7e]">penerima manfaat</span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($program['impact_narrative'])): ?>
                        <p class="text-[#4a5568] dark:text-gray-300 leading-relaxed whitespace-pre-line mb-6"><?= esc($program['impact_narrative']) ?></p>
                    <?php endif; ?>

                    <?php if (!empty($impactPhotos)): ?>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <?php foreach ($impactPhotos as $ph): ?>
                                <a href="<?= esc($storage->url($ph['photo']), 'attr') ?>" target="_blank" class="aspect-square rounded-2xl overflow-hidden border border-primary/10 group">
                                    <img src="<?= esc($storage->url($ph['photo']), 'attr') ?>" loading="lazy" alt="Bukti dampak"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar Info -->
        <div class="lg:col-span-5 mt-12 lg:mt-0">
            <div class="sticky top-28 space-y-8 animate-in fade-in slide-in-from-right-8 duration-1000">
                <!-- Registration Card -->
                <div class="p-10 bg-white dark:bg-white/5 rounded-[3rem] border border-[#dbe6e1] dark:border-white/10 shadow-xl relative overflow-hidden">
                    <div class="relative z-10">
                        <h3 class="text-xl font-black mb-8">Detail Pendaftaran</h3>
                        
                        <div class="space-y-6 mb-10">
                            <div class="flex items-start gap-4">
                                <div class="size-10 bg-primary/10 rounded-xl flex items-center justify-center text-primary shrink-0">
                                    <span class="material-symbols-outlined text-lg">schedule</span>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Waktu</p>
                                    <p class="text-sm font-bold"><?= date('H:i', strtotime($program['date_start'])) ?> WIB - Selesai</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="size-10 bg-primary/10 rounded-xl flex items-center justify-center text-primary shrink-0">
                                    <span class="material-symbols-outlined text-lg">location_on</span>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Lokasi</p>
                                    <p class="text-sm font-bold"><?= esc($program['location']) ?></p>
                                </div>
                            </div>

                            <?php if ($program['quota']): ?>
                            <div class="flex items-start gap-4">
                                <div class="size-10 bg-primary/10 rounded-xl flex items-center justify-center text-primary shrink-0">
                                    <span class="material-symbols-outlined text-lg">groups</span>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Kuota Peserta</p>
                                    <p class="text-sm font-bold"><?= esc($program['quota']) ?> Orang</p>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($daftarUrl !== ''): ?>
                            <a href="<?= esc($daftarUrl, 'attr') ?>" target="_blank" rel="noopener noreferrer" class="w-full h-16 bg-primary text-white rounded-2xl font-black flex items-center justify-center gap-2 hover:bg-emerald-900 transition-all shadow-lg shadow-primary/20 mb-4">
                                <span class="material-symbols-outlined text-sm">how_to_reg</span>
                                Daftar Sekarang
                            </a>
                        <?php else: ?>
                            <?php
                                $kuota = (int) ($program['quota'] ?? 0);
                                $tamu  = (int) ($rsvpTamu ?? 0);
                                $penuh = $kuota > 0 && $tamu >= $kuota;
                            ?>
                            <div class="w-full p-5 bg-emerald-50/60 dark:bg-primary/10 border border-primary/20 rounded-2xl mb-4">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="material-symbols-outlined text-primary">how_to_reg</span>
                                    <h4 class="font-black text-[#111816] dark:text-white">Konfirmasi Kehadiran</h4>
                                </div>

                                <p class="text-sm text-[#608a7e] mb-3">
                                    <strong class="text-primary"><?= number_format($tamu, 0, ',', '.') ?></strong> orang akan hadir<?= $kuota > 0 ? ' dari kuota ' . number_format($kuota, 0, ',', '.') : '' ?>.
                                </p>

                                <?php if (session()->getFlashdata('rsvp_ok')): ?>
                                    <div class="bg-white dark:bg-white/10 border border-emerald-200 text-emerald-700 rounded-xl p-3 text-sm font-medium mb-3"><?= esc(session()->getFlashdata('rsvp_ok')) ?></div>
                                <?php endif; ?>
                                <?php if (session()->getFlashdata('rsvp_error')): ?>
                                    <div class="bg-white dark:bg-white/10 border border-rose-200 text-rose-600 rounded-xl p-3 text-sm font-medium mb-3"><?= esc(session()->getFlashdata('rsvp_error')) ?></div>
                                <?php endif; ?>

                                <?php if ($penuh): ?>
                                    <div class="text-center py-2 text-sm font-bold text-rose-500">Kuota telah penuh.</div>
                                <?php else: ?>
                                    <form action="<?= base_url('program-rsvp/simpan') ?>" method="post" class="space-y-2">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="masjid_id" value="<?= $masjid['id'] ?>">
                                        <input type="hidden" name="program_id" value="<?= $program['id'] ?>">
                                        <input type="text" name="name" required placeholder="Nama Anda" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:bg-white/5 dark:border-white/10 text-sm">
                                        <div class="flex gap-2">
                                            <input type="tel" name="phone" required placeholder="No. WhatsApp" class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 dark:bg-white/5 dark:border-white/10 text-sm">
                                            <input type="number" name="guests" min="1" value="1" title="Jumlah orang" class="w-20 px-3 py-2.5 rounded-xl border border-gray-200 dark:bg-white/5 dark:border-white/10 text-sm text-center">
                                        </div>
                                        <button type="submit" class="w-full h-12 bg-primary text-white rounded-xl font-bold flex items-center justify-center gap-2 hover:bg-emerald-900 transition-all">
                                            <span class="material-symbols-outlined text-base">event_available</span> Saya Akan Hadir
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Fundraising Progress -->
                        <?php if (isset($program['target_donation']) && $program['target_donation'] > 0): ?>
                            <?php 
                                // Calculate Progress (This should ideally be from controller/model, but for now query here or pass from controller)
                                // We need to fetch total donations for this program
                                $db = \Config\Database::connect();
                                $builder = $db->table('masjid_donations');
                                $builder->selectSum('amount');
                                $builder->where('program_id', $program['id']);
                                $builder->where('status', 'success');
                                $query = $builder->get();
                                $collected = $query->getRow()->amount ?? 0;
                                
                                $percentage = min(100, round(($collected / $program['target_donation']) * 100));
                            ?>
                            <div class="mb-6">
                                <div class="flex justify-between items-end mb-2">
                                    <div>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Terkumpul</p>
                                        <p class="text-lg font-black text-primary">Rp <?= number_format($collected, 0, ',', '.') ?></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Target</p>
                                        <p class="text-sm font-bold text-gray-900">Rp <?= number_format($program['target_donation'], 0, ',', '.') ?></p>
                                    </div>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                                    <div class="bg-primary h-3 rounded-full transition-all duration-1000" style="width: <?= $percentage ?>%"></div>
                                </div>
                                <p class="text-right text-xs font-bold text-primary mt-1"><?= $percentage ?>% Terpenuhi</p>
                            </div>
                        <?php endif; ?>

                        <!-- Donation CTA -->
                        <div class="border-t border-dashed border-gray-200 pt-6 mt-2">
                            <h4 class="font-bold mb-3 text-center">Ingin Berkontribusi?</h4>
                            <p class="text-xs text-gray-500 text-center mb-4 px-4">Salurkan infaq terbaik Anda untuk mendukung program kegiatan ini.</p>
                            <a href="<?= base_url('donation/' . $masjid['username'] . '/form/' . $program['slug']) ?>" class="w-full h-14 bg-white border-2 border-primary text-primary rounded-2xl font-black flex items-center justify-center gap-2 hover:bg-primary hover:text-white transition-all">
                                <span class="material-symbols-outlined text-sm">volunteer_activism</span>
                                Donasi Sekarang
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Organizer & Share -->
                <div class="p-8 bg-background-light dark:bg-white/5 rounded-[2.5rem] border border-[#dbe6e1] dark:border-white/10">
                    <h4 class="font-bold mb-4">Bagikan Kegiatan</h4>
                    <div class="flex items-center gap-3">
                        <a href="https://wa.me/?text=<?= urlencode('Yuk ikuti kegiatan ' . $program['title'] . ' di ' . $masjid['name'] . '. Info selengkapnya: ' . current_url()) ?>" target="_blank" class="size-12 rounded-xl bg-[#25D366] text-white flex items-center justify-center hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined">share</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .article-content img {
        border-radius: 1.5rem;
    }
</style>
<?= $this->endSection() ?>
