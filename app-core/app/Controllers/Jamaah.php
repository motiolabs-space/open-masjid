<?php

namespace App\Controllers;

use App\Models\MasjidModel;
use App\Models\MasjidFollowerModel;
use App\Models\MasjidProgramModel;
use App\Models\MasjidDonationModel;

/**
 * Area jamaah (pengguna umum): mencari & mengikuti masjid, melihat program
 * dari masjid yang diikuti, serta riwayat donasi pribadi.
 *
 * Semua query di-scope ke user yang sedang login (user_id / email dari session),
 * bukan ke masjid_id — karena jamaah tidak terikat pada satu masjid.
 */
class Jamaah extends BaseController
{
    /** Daftar semua masjid + kotak pencarian, dengan penanda "sudah diikuti". */
    public function cariMasjid(): string
    {
        $userId = session()->get('user_id');
        $masjidModel = new MasjidModel();
        $followerModel = new MasjidFollowerModel();

        $q = trim((string) $this->request->getGet('q'));
        $builder = $masjidModel->orderBy('name', 'ASC');
        if ($q !== '') {
            $builder->groupStart()
                    ->like('name', $q)
                    ->orLike('username', $q)
                    ->orLike('address', $q)
                    ->groupEnd();
        }
        $masjids = $builder->findAll(60);

        // Set id masjid yang sudah diikuti user, untuk menandai tombol.
        $followedIds = array_map(
            'intval',
            array_column($followerModel->where('user_id', $userId)->findAll(), 'masjid_id')
        );

        return view('dashboard/jamaah/cari_masjid', [
            'title'       => 'Cari Masjid - Masj.id',
            'masjids'     => $masjids,
            'followedIds' => $followedIds,
            'keyword'     => $q,
            'storage'     => new \App\Libraries\Storage(),
        ]);
    }

    /** Ikuti sebuah masjid (idempotent — tidak menduplikasi). */
    public function follow($masjidId)
    {
        $userId = session()->get('user_id');
        $masjidModel = new MasjidModel();
        $followerModel = new MasjidFollowerModel();

        if (!$masjidModel->find($masjidId)) {
            return redirect()->back()->with('error', 'Masjid tidak ditemukan.');
        }

        $already = $followerModel->where(['user_id' => $userId, 'masjid_id' => $masjidId])->first();
        if (!$already) {
            $followerModel->insert(['user_id' => $userId, 'masjid_id' => $masjidId]);
        }

        return redirect()->back()->with('success', 'Anda sekarang mengikuti masjid ini.');
    }

    /** Berhenti mengikuti sebuah masjid. */
    public function unfollow($masjidId)
    {
        $userId = session()->get('user_id');
        $followerModel = new MasjidFollowerModel();

        $followerModel->where(['user_id' => $userId, 'masjid_id' => $masjidId])->delete();

        return redirect()->back()->with('success', 'Anda berhenti mengikuti masjid ini.');
    }

    /** Masjid yang diikuti jamaah. */
    public function masjidSaya(): string
    {
        $userId = session()->get('user_id');
        $db = \Config\Database::connect();

        $masjids = $db->table('masjid_followers mf')
            ->select('masjid.*, mf.created_at as followed_at')
            ->join('masjid', 'masjid.id = mf.masjid_id')
            ->where('mf.user_id', $userId)
            ->orderBy('mf.created_at', 'DESC')
            ->get()->getResultArray();

        return view('dashboard/jamaah/masjid_saya', [
            'title'   => 'Masjid Saya - Masj.id',
            'masjids' => $masjids,
            'storage' => new \App\Libraries\Storage(),
        ]);
    }

    /** Program aktif (published) dari masjid-masjid yang diikuti jamaah. */
    public function programDiikuti(): string
    {
        $userId = session()->get('user_id');
        $followerModel = new MasjidFollowerModel();

        $followedIds = array_map(
            'intval',
            array_column($followerModel->where('user_id', $userId)->findAll(), 'masjid_id')
        );

        $programs = [];
        if (!empty($followedIds)) {
            $db = \Config\Database::connect();
            $programs = $db->table('masjid_programs p')
                ->select("p.*, masjid.name as masjid_name, masjid.username as masjid_username,
                    (SELECT COALESCE(SUM(ft.amount), 0) FROM masjid_finance_transactions ft
                     WHERE ft.program_id = p.id AND ft.type = 'pemasukan') as collected_amount")
                ->join('masjid', 'masjid.id = p.masjid_id')
                ->whereIn('p.masjid_id', $followedIds)
                ->where('p.status', 'published')
                ->orderBy('p.date_start', 'ASC')
                ->get()->getResultArray();
        }

        return view('dashboard/jamaah/program_diikuti', [
            'title'       => 'Program Diikuti - Masj.id',
            'programs'    => $programs,
            'hasFollowed' => !empty($followedIds),
            'storage'     => new \App\Libraries\Storage(),
        ]);
    }

    /** Riwayat donasi jamaah (dicocokkan berdasarkan email pendonor). */
    public function riwayatDonasi(): string
    {
        $email = session()->get('user_email');
        $db = \Config\Database::connect();

        $donations = [];
        $totalSukses = 0;
        if ($email) {
            $donations = $db->table('masjid_donations d')
                ->select('d.*, masjid.name as masjid_name, masjid.username as masjid_username, p.title as program_title')
                ->join('masjid', 'masjid.id = d.masjid_id', 'left')
                ->join('masjid_programs p', 'p.id = d.program_id', 'left')
                ->where('d.donor_email', $email)
                ->orderBy('d.created_at', 'DESC')
                ->get()->getResultArray();

            foreach ($donations as $d) {
                if ($d['status'] === 'success') {
                    $totalSukses += (float) $d['amount'];
                }
            }
        }

        return view('dashboard/jamaah/riwayat_donasi', [
            'title'       => 'Riwayat Donasi - Masj.id',
            'donations'   => $donations,
            'totalSukses' => $totalSukses,
        ]);
    }
}
