<?php
    helper('custom'); // terbilang()
    $nominal   = (float) ($donation['amount'] ?? 0);
    $logoUrl   = ! empty($masjid['logo']) ? $storage->url($masjid['logo']) : null;
    $tglBayar  = ! empty($donation['paid_at']) ? $donation['paid_at'] : $donation['created_at'];
    $bulan     = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
    $ts        = strtotime($tglBayar);
    $tglIndo   = date('d', $ts) . ' ' . ($bulan[date('m', $ts)] ?? '') . ' ' . date('Y', $ts);
    $metode    = ucfirst(str_replace(['_','-'], ' ', $donation['payment_method'] ?? 'manual'));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title><?= esc($title) ?></title>
    <style>
        :root { --primary:#065f46; --ink:#0f1e18; --muted:#5b756b; --line:#e3ebe7; --accent:#eaf3ef; }
        * { box-sizing: border-box; }
        body { margin:0; background:#eef2f0; color:var(--ink); font-family: system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif; padding:1.5rem; -webkit-font-smoothing:antialiased; }
        .sheet { max-width:640px; margin:0 auto; background:#fff; border:1px solid var(--line); border-radius:16px; overflow:hidden; box-shadow:0 20px 50px -30px rgba(6,95,70,.35); }
        .bar { height:6px; background:var(--primary); }
        .pad { padding:2rem 2.25rem; }
        .head { display:flex; align-items:center; gap:1rem; border-bottom:1px solid var(--line); padding-bottom:1.25rem; margin-bottom:1.5rem; }
        .logo { width:56px; height:56px; border-radius:12px; object-fit:cover; background:var(--accent); flex-shrink:0; }
        .logo-fallback { display:flex; align-items:center; justify-content:center; color:var(--primary); font-size:28px; }
        .masjid-name { font-size:1.15rem; font-weight:800; line-height:1.2; }
        .masjid-addr { font-size:.8rem; color:var(--muted); margin-top:.15rem; }
        .title-row { display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; margin-bottom:1.5rem; flex-wrap:wrap; }
        h1 { font-size:1.35rem; margin:0; letter-spacing:.02em; }
        .subtitle { font-size:.78rem; color:var(--muted); font-weight:700; letter-spacing:.16em; text-transform:uppercase; }
        .inv { font-family:ui-monospace,SFMono-Regular,Menlo,monospace; font-size:.85rem; color:var(--ink); background:var(--accent); padding:.35rem .7rem; border-radius:8px; }
        .badge { display:inline-flex; align-items:center; gap:.35rem; font-weight:800; font-size:.8rem; padding:.35rem .8rem; border-radius:999px; }
        .badge.lunas { background:#dcfce7; color:#166534; }
        .badge.belum { background:#fef3c7; color:#92400e; }
        .rows { border:1px solid var(--line); border-radius:12px; overflow:hidden; }
        .row { display:flex; padding:.85rem 1rem; font-size:.92rem; }
        .row:nth-child(odd) { background:#fafcfb; }
        .row .k { width:38%; color:var(--muted); }
        .row .v { width:62%; font-weight:600; }
        .amount-box { margin-top:1.25rem; background:var(--accent); border-radius:12px; padding:1.1rem 1.25rem; }
        .amount-box .lbl { font-size:.72rem; text-transform:uppercase; letter-spacing:.14em; color:var(--muted); font-weight:800; }
        .amount-box .amt { font-size:1.7rem; font-weight:900; color:var(--primary); margin-top:.15rem; }
        .amount-box .terbilang { font-size:.85rem; color:var(--ink); font-style:italic; margin-top:.3rem; }
        .verify { margin-top:1.5rem; padding-top:1.25rem; border-top:1px dashed var(--line); font-size:.78rem; color:var(--muted); line-height:1.55; }
        .verify strong { color:var(--ink); }
        .actions { max-width:640px; margin:1.25rem auto 0; display:flex; gap:.6rem; justify-content:center; flex-wrap:wrap; }
        .btn { display:inline-flex; align-items:center; gap:.45rem; padding:.7rem 1.3rem; border-radius:12px; font-weight:700; font-size:.92rem; text-decoration:none; cursor:pointer; border:1px solid var(--line); background:#fff; color:var(--ink); }
        .btn.primary { background:var(--primary); color:#fff; border-color:var(--primary); }
        .belum-wrap { text-align:center; padding:1rem 0 .5rem; }
        .belum-wrap p { color:var(--muted); margin:.5rem 0 0; }
        @media print {
            body { background:#fff; padding:0; }
            .sheet { border:none; box-shadow:none; max-width:100%; border-radius:0; }
            .actions { display:none; }
            .bar { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
            .amount-box, .badge, .inv, .logo { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
        }
    </style>
</head>
<body>
    <div class="sheet">
        <div class="bar"></div>
        <div class="pad">
            <div class="head">
                <?php if ($logoUrl): ?>
                    <img class="logo" src="<?= esc($logoUrl, 'attr') ?>" alt="Logo">
                <?php else: ?>
                    <div class="logo logo-fallback">
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v3"/><path d="M4 21V11c0-3 3.5-4.5 8-4.5s8 1.5 8 4.5v10"/><path d="M4 21h16"/><path d="M9 21v-4a3 3 0 0 1 6 0v4"/></svg>
                    </div>
                <?php endif; ?>
                <div>
                    <div class="masjid-name"><?= esc($masjid['name'] ?? 'Masjid') ?></div>
                    <?php if (! empty($masjid['address'])): ?>
                        <div class="masjid-addr"><?= esc($masjid['address']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (! $lunas): ?>
                <div class="belum-wrap">
                    <span class="badge belum">Menunggu Pembayaran</span>
                    <h1 style="margin-top:1rem;">Donasi belum lunas</h1>
                    <p>Kwitansi resmi terbit otomatis setelah pembayaran donasi <strong><?= esc($donation['invoice_number']) ?></strong> diterima.</p>
                </div>
            <?php else: ?>
                <div class="title-row">
                    <div>
                        <div class="subtitle">Tanda Terima Donasi</div>
                        <h1>KWITANSI</h1>
                    </div>
                    <div style="text-align:right;">
                        <span class="badge lunas">✓ LUNAS</span>
                        <div style="margin-top:.5rem;"><span class="inv"><?= esc($donation['invoice_number']) ?></span></div>
                    </div>
                </div>

                <div class="rows">
                    <div class="row"><div class="k">Telah diterima dari</div><div class="v"><?= esc($donation['donor_name'] ?: 'Hamba Allah') ?></div></div>
                    <div class="row"><div class="k">Untuk</div><div class="v"><?= esc($programName) ?></div></div>
                    <div class="row"><div class="k">Metode</div><div class="v"><?= esc($metode) ?></div></div>
                    <div class="row"><div class="k">Tanggal</div><div class="v"><?= esc($tglIndo) ?></div></div>
                </div>

                <div class="amount-box">
                    <div class="lbl">Jumlah Donasi</div>
                    <div class="amt">Rp <?= number_format($nominal, 0, ',', '.') ?></div>
                    <div class="terbilang"><?= esc(ucfirst(trim(terbilang($nominal)))) ?> rupiah</div>
                </div>

                <?php if (! empty($donation['message'])): ?>
                    <div class="verify"><strong>Pesan donatur:</strong> &ldquo;<?= esc($donation['message']) ?>&rdquo;</div>
                <?php endif; ?>

                <div class="verify">
                    <strong>Jazakumullah khairan.</strong> Kwitansi ini sah tanpa tanda tangan &mdash;
                    terverifikasi digital melalui <strong>Masj.id</strong> pada nomor
                    <?= esc($donation['invoice_number']) ?>. Dana tercatat otomatis di kas masjid dan
                    dapat ditelusuri pada laporan transparansi
                    <?php if (! empty($masjid['username'])): ?>
                        di <strong><?= esc(rtrim(str_replace(['https://','http://'], '', base_url($masjid['username'] . '/laporan')), '/')) ?></strong>.
                    <?php else: ?>.<?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="actions">
        <?php if ($lunas): ?>
            <button class="btn primary" onclick="window.print()">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 14h12v8H6z"/></svg>
                Cetak / Simpan PDF
            </button>
        <?php endif; ?>
        <a class="btn" href="<?= esc(! empty($masjid['username']) ? base_url($masjid['username']) : base_url('/'), 'attr') ?>">Kembali ke Masjid</a>
    </div>
</body>
</html>
