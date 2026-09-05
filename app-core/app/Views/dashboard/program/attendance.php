<?= $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<div class="px-4 sm:px-8 py-8">
    <div class="max-w-4xl mx-auto">

        <div class="mb-6">
            <a href="<?= base_url('dashboard/program') ?>" class="inline-flex items-center gap-2 text-primary font-bold text-sm mb-3 hover:underline">
                <span class="material-symbols-outlined text-base">arrow_back</span> Kembali ke Program
            </a>
            <h1 class="text-2xl sm:text-3xl font-black text-[#111816] dark:text-white tracking-tight">Kehadiran</h1>
            <p class="text-[#608a7e] mt-1"><?= esc($program['title']) ?></p>
        </div>

        <!-- Ringkasan -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
            <?php
                $kartu = [
                    ['Pendaftar', $ringkasan['pendaftar'], 'sky', 'how_to_reg'],
                    ['Total Tamu', $ringkasan['tamu'], 'violet', 'groups'],
                    ['Hadir', $ringkasan['hadir'], 'emerald', 'check_circle'],
                    ['Tidak Hadir', $ringkasan['tidak_hadir'], 'rose', 'cancel'],
                ];
                foreach ($kartu as [$lbl, $val, $c, $ic]):
            ?>
                <div class="bg-white dark:bg-white/5 rounded-2xl border border-[#e5e7eb] dark:border-white/10 p-5">
                    <span class="material-symbols-outlined text-<?= $c ?>-500 mb-2"><?= $ic ?></span>
                    <p class="text-2xl font-black"><?= number_format($val, 0, ',', '.') ?></p>
                    <p class="text-xs text-[#608a7e] font-medium"><?= $lbl ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="bg-white dark:bg-white/5 rounded-2xl border border-[#e5e7eb] dark:border-white/10 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-[#f0f5f3] dark:bg-white/5 text-[#608a7e] text-xs font-bold uppercase tracking-wider">
                        <tr>
                            <th class="px-4 sm:px-6 py-4">Nama</th>
                            <th class="px-4 sm:px-6 py-4">WhatsApp</th>
                            <th class="px-4 sm:px-6 py-4 text-center">Orang</th>
                            <th class="px-4 sm:px-6 py-4 text-center">Status</th>
                            <th class="px-4 sm:px-6 py-4 text-right">Tandai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e5e7eb] dark:divide-white/10">
                        <?php if (empty($rsvps)): ?>
                            <tr><td colspan="5" class="px-6 py-12 text-center text-[#608a7e]">
                                <span class="material-symbols-outlined text-4xl mb-2 block opacity-40">event_busy</span>
                                Belum ada yang konfirmasi kehadiran.
                            </td></tr>
                        <?php endif; ?>
                        <?php foreach ($rsvps as $r): ?>
                            <?php
                                $badge = ['attended' => ['Hadir', 'bg-emerald-100 text-emerald-700'],
                                          'no_show'  => ['Tidak Hadir', 'bg-rose-100 text-rose-700'],
                                          'registered' => ['Terdaftar', 'bg-slate-100 text-slate-600']][$r['status']] ?? ['Terdaftar', 'bg-slate-100 text-slate-600'];
                                $wa = preg_replace('/[^0-9]/', '', $r['phone']);
                                if (str_starts_with($wa, '0')) $wa = '62' . substr($wa, 1);
                            ?>
                            <tr class="hover:bg-[#f0f5f3]/50 dark:hover:bg-white/5">
                                <td class="px-4 sm:px-6 py-3 font-bold text-[#111816] dark:text-white"><?= esc($r['name']) ?></td>
                                <td class="px-4 sm:px-6 py-3">
                                    <a href="https://wa.me/<?= $wa ?>" target="_blank" class="text-primary hover:underline"><?= esc($r['phone']) ?></a>
                                </td>
                                <td class="px-4 sm:px-6 py-3 text-center"><?= (int) $r['guests'] ?></td>
                                <td class="px-4 sm:px-6 py-3 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $badge[1] ?>"><?= $badge[0] ?></span>
                                </td>
                                <td class="px-4 sm:px-6 py-3">
                                    <div class="flex gap-1 justify-end">
                                        <?php foreach (['attended' => ['check', 'emerald', 'Hadir'], 'no_show' => ['close', 'rose', 'Absen']] as $st => $b): ?>
                                            <form action="<?= base_url('dashboard/program/kehadiran/mark') ?>" method="post">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="rsvp_id" value="<?= $r['id'] ?>">
                                                <input type="hidden" name="status" value="<?= $st ?>">
                                                <button type="submit" title="<?= $b[2] ?>" class="size-8 rounded-lg flex items-center justify-center <?= $r['status'] === $st ? "bg-{$b[1]}-500 text-white" : "bg-{$b[1]}-50 text-{$b[1]}-600 hover:bg-{$b[1]}-100" ?>">
                                                    <span class="material-symbols-outlined text-base"><?= $b[0] ?></span>
                                                </button>
                                            </form>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
