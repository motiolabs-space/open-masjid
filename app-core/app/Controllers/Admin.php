<?php

namespace App\Controllers;

class Admin extends BaseController
{
    public function index(): string
    {
        $name = session()->get('masjid_name') ?? session()->get('user_name') ?? 'User';
        $data = [
            'title' => 'Dashboard Utama - ' . $name
        ];
        return view('dashboard/index', $data);
    }
    public function profil(): string
    {
        return view('dashboard/profil', ['title' => 'Profil Masjid - Masj.id']);
    }

    public function program(): string
    {
        return view('dashboard/program', ['title' => 'Manajemen Program & Kegiatan - Masj.id']);
    }

    public function berita(): string
    {
        return view('dashboard/berita', ['title' => 'Berita & Dokumentasi - Masj.id']);
    }

    public function keuangan(): string
    {
        return view('dashboard/keuangan', ['title' => 'Manajemen Keuangan - Masj.id']);
    }
}
