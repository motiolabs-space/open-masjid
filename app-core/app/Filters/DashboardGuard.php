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

        // Pengurus & superadmin boleh mengakses seluruh area dashboard/superadmin.
        // (Akses ke 'superadmin/*' tetap dibatasi ulang di SuperAdmin::initController.)
        if (in_array($role, ['pengurus', 'superadmin'], true)) {
            return;
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
