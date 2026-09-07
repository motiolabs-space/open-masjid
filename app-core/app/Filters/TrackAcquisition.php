<?php

namespace App\Filters;

use App\Libraries\Acquisition;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Mencatat asal kunjungan (UTM / rujukan) sebelum halaman dibuka.
 *
 * Dipasang global agar tautan kampanye bisa mengarah ke halaman MANA SAJA —
 * halaman masjid, program, atau laporan — bukan hanya beranda. Dalam praktiknya
 * tautan yang dibagikan di grup justru jarang mengarah ke beranda.
 *
 * Hanya permintaan GET yang diperiksa: penanda kampanye ada di alamat halaman
 * yang diklik, bukan pada pengiriman formulir.
 */
class TrackAcquisition implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (is_cli() || strtoupper($request->getMethod()) !== 'GET') {
            return;
        }

        // Rute mesin (API, webhook, aset) tak pernah menjadi titik masuk
        // seseorang, dan menaruh cookie di baliknya hanya menambah derau.
        $uri = uri_string();
        foreach (['api/', 'push/', 'payment/callback'] as $awalan) {
            if (str_starts_with($uri, $awalan)) {
                return;
            }
        }

        Acquisition::rekam($request);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada yang perlu dilakukan setelah respons disusun.
    }
}
