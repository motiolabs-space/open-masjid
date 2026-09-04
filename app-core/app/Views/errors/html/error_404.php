<?php
// Halaman 404 Masj.id. Ditampilkan CI4 di luar layout normal, jadi HARUS
// mandiri (tanpa Tailwind/layout). base_url() dijaga function_exists agar
// halaman error tak ikut error bila url helper belum termuat.
$home = function_exists('base_url') ? base_url('/') : '/';
$dash = function_exists('base_url') ? base_url('dashboard') : '/dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>404 — Halaman tidak ditemukan · Masj.id</title>
    <style>
        :root {
            --bg: #f4f7f5; --card: #ffffff; --ink: #0f1e18; --muted: #5b756b;
            --line: #e3ebe7; --primary: #065f46; --primary-ink: #ffffff;
            --ring: rgba(6,95,70,.14); --accent: #eaf3ef;
        }
        @media (prefers-color-scheme: dark) {
            :root {
                --bg: #0a1512; --card: #0f1e18; --ink: #eef5f1; --muted: #8fb0a4;
                --line: #1e332b; --primary: #34d399; --primary-ink: #04120c;
                --ring: rgba(52,211,153,.18); --accent: #12241d;
            }
        }
        * { box-sizing: border-box; }
        html, body { height: 100%; }
        body {
            margin: 0; background: var(--bg); color: var(--ink);
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            display: flex; align-items: center; justify-content: center; padding: 1.5rem;
            -webkit-font-smoothing: antialiased;
        }
        .card {
            width: 100%; max-width: 520px; background: var(--card);
            border: 1px solid var(--line); border-radius: 24px;
            padding: 2.5rem 2rem; text-align: center;
            box-shadow: 0 24px 60px -30px var(--ring);
        }
        .badge {
            width: 84px; height: 84px; margin: 0 auto 1.5rem; border-radius: 24px;
            background: var(--accent); color: var(--primary);
            display: flex; align-items: center; justify-content: center;
        }
        .badge svg { width: 44px; height: 44px; }
        .code {
            font-size: .8rem; font-weight: 700; letter-spacing: .18em;
            text-transform: uppercase; color: var(--primary); margin: 0 0 .35rem;
        }
        h1 { font-size: 1.75rem; line-height: 1.2; margin: 0 0 .6rem; font-weight: 800; }
        p { color: var(--muted); font-size: .98rem; line-height: 1.6; margin: 0 auto 1.75rem; max-width: 40ch; }
        .actions { display: flex; gap: .75rem; justify-content: center; flex-wrap: wrap; }
        a.btn {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: .7rem 1.3rem; border-radius: 14px; font-weight: 700;
            font-size: .95rem; text-decoration: none; border: 1px solid var(--line);
            transition: transform .06s ease, box-shadow .15s ease;
        }
        a.btn:active { transform: translateY(1px); }
        a.primary { background: var(--primary); color: var(--primary-ink); border-color: var(--primary); }
        a.ghost { background: transparent; color: var(--ink); }
        a.ghost:hover { border-color: var(--primary); color: var(--primary); }
        .foot { margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid var(--line); font-size: .82rem; color: var(--muted); }
        .foot strong { color: var(--ink); }
        <?php if (ENVIRONMENT !== 'production'): ?>
        .debug { margin-top: 1.25rem; text-align: left; background: var(--accent); border-radius: 12px; padding: .85rem 1rem; font-size: .78rem; color: var(--muted); word-break: break-word; }
        .debug b { color: var(--ink); }
        <?php endif; ?>
    </style>
</head>
<body>
    <main class="card">
        <div class="badge" aria-hidden="true">
            <!-- Kubah masjid -->
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2v3"/><path d="M10.5 3.5h3"/>
                <path d="M4 21V11c0-3 3.5-4.5 8-4.5s8 1.5 8 4.5v10"/>
                <path d="M4 21h16"/>
                <path d="M9 21v-4a3 3 0 0 1 6 0v4"/>
                <path d="M7 11v10"/><path d="M17 11v10"/>
            </svg>
        </div>

        <p class="code">Error 404</p>
        <h1>Halaman tidak ditemukan</h1>
        <p>
            <?php if (ENVIRONMENT !== 'production' && ! empty($message) && $message !== '(null)'): ?>
                Alamat yang Anda tuju tidak ada atau sudah dipindahkan.
            <?php else: ?>
                Maaf, halaman yang Anda cari tidak ada atau sudah dipindahkan.
                Mari kembali ke tempat yang benar.
            <?php endif; ?>
        </p>

        <div class="actions">
            <a class="btn primary" href="<?= esc($home, 'attr') ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/></svg>
                Beranda
            </a>
            <a class="btn ghost" href="<?= esc($dash, 'attr') ?>">Dashboard Pengurus</a>
        </div>

        <?php if (ENVIRONMENT !== 'production' && ! empty($message) && $message !== '(null)'): ?>
            <div class="debug"><b>Pesan (dev):</b> <?= nl2br(esc($message)) ?></div>
        <?php endif; ?>

        <div class="foot">
            <strong>Masj.id</strong> — kelola masjid dengan transparan &amp; mudah.
        </div>
    </main>
</body>
</html>
