<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Membatasi aksi yang hanya boleh dilakukan ADMIN masjid.
 *
 * Ada tiga hal berbeda yang sama-sama bernama "role" di aplikasi ini — mudah
 * tertukar, jadi dicatat di sini:
 *
 *   users.role             'superadmin' | 'admin' | 'user' — tingkat platform.
 *   session('role')        'superadmin' | 'pengurus' | 'jamaah' — dipakai
 *                          DashboardGuard. Nilai 'pengurus' di sini hanya
 *                          berarti "orang ini mengurus suatu masjid", BUKAN
 *                          jabatannya.
 *   masjid_pengurus.role   'admin' | 'pengurus' — jabatan sesungguhnya di
 *                          masjid yang sedang dibuka. Inilah yang diperiksa
 *                          filter ini, dibaca langsung dari basis data lewat
 *                          is_admin_masjid() supaya perubahan dari menu admin
 *                          langsung berlaku (lihat catatan di helper itu).
 *
 * Sebelumnya masjid_pengurus.role tersimpan dan bisa diubah lewat UI, tetapi
 * tidak pernah dibaca untuk apa pun — admin dan pengurus praktis punya akses
 * sama, padahal daftar pilihannya menjanjikan "Pengurus (Non-Delete)".
 */
class MasjidAdmin implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Filter berjalan sebelum controller, jadi helper belum termuat sendiri.
        helper('custom');

        if (is_admin_masjid()) {
            return;
        }

        $pesan = 'Aksi ini hanya untuk Admin Masjid. '
               . 'Silakan hubungi Admin Masjid Anda bila memang diperlukan.';

        // Aksi hapus umumnya dipanggil lewat fetch() dan hasilnya dibaca sebagai
        // JSON; balasan redirect HTML akan gagal di-parse dan tampak sebagai
        // "error jaringan" yang membingungkan.
        if ($request->isAJAX()) {
            return service('response')->setStatusCode(403)->setJSON([
                'status'  => 'error',
                'message' => $pesan,
            ]);
        }

        return redirect()->back()->with('error', $pesan);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada
    }
}
