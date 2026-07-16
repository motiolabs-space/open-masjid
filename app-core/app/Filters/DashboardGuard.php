<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardGuard implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $role = session()->get('role');

        if ($role === 'superadmin') {
            // Akses ke 'superadmin/*' tetap dibatasi ulang di
            // SuperAdmin::initController.
            return;
        }

        if ($role === 'pengurus') {
            // Keanggotaan diperiksa ulang ke basis data setiap permintaan.
            // session('role') hanya salinan dari saat login: tanpa pemeriksaan
            // ini, pengurus yang sudah DICOPOT lewat menu admin tetap bisa
            // mengelola masjid sampai ia logout sendiri — dan tidak ada yang
            // bisa memaksanya keluar.
            helper('custom');

            if (pengurus_saat_ini() !== null) {
                return;
            }

            // Sudah bukan pengurus, tetapi akunnya tetap sah: perlakukan sebagai
            // jamaah biasa dengan jatuh ke daftar izin di bawah. JANGAN
            // mengalihkan ke 'dashboard' dari sini — halaman itu dijaga filter
            // ini juga, sehingga pengalihannya akan berputar tanpa henti.
            session()->remove(['masjid_id', 'masjid_name', 'masjid_username']);
            session()->set('role', 'jamaah');
        }

        // Selain itu (mis. jamaah) hanya boleh membuka halaman non-manajemen.
        // Deny-by-default: halaman manajemen apa pun ditolak agar tidak ada
        // penulisan data dengan masjid_id kosong milik jamaah.
        $uri = uri_string();

        // Halaman yang boleh diakses jamaah (dashboard utama + fitur jamaah).
        $allowedExact = ['dashboard', 'dashboard/'];
        $allowedPrefix = [
            'dashboard/lms',            // modul pembelajaran (LMS)
            'dashboard/cari-masjid',    // cari & ikuti masjid
            'dashboard/masjid-saya',    // masjid yang diikuti (+ follow/unfollow)
            'dashboard/program-diikuti',
            'dashboard/riwayat-donasi',
        ];

        $allowed = in_array($uri, $allowedExact, true);
        foreach ($allowedPrefix as $prefix) {
            if ($uri === $prefix || str_starts_with($uri, $prefix . '/')) {
                $allowed = true;
                break;
            }
        }

        if (!$allowed) {
            return redirect()->to('dashboard')
                ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing here
    }
}
