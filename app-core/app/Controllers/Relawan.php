<?php

namespace App\Controllers;

use App\Models\MasjidVolunteerModel;
use App\Models\MasjidVolunteerPointModel;

/**
 * Manajemen relawan: rekrut, peran, poin partisipasi, sertifikat.
 * Semua rute berada di bawah 'dashboard/*' sehingga sudah digerbang
 * dashboardGuard (login + konteks masjid). Setiap operasi memakai
 * session('masjid_id') dan memeriksa kepemilikan sebelum menulis.
 */
class Relawan extends BaseController
{
    public function index()
    {
        $masjidId = session()->get('masjid_id');
        $model = new MasjidVolunteerModel();

        $volunteers = $model->where('masjid_id', $masjidId)
            ->orderBy('points', 'DESC')->orderBy('name', 'ASC')->findAll();

        $aktif = array_filter($volunteers, fn ($v) => $v['status'] === 'active');

        return view('dashboard/volunteers/manage', [
            'title'       => 'Relawan - Masj.id',
            'volunteers'  => $volunteers,
            'totalAktif'  => count($aktif),
            'totalPoin'   => array_sum(array_column($volunteers, 'points')),
        ]);
    }

    public function save()
    {
        $masjidId = session()->get('masjid_id');
        $id = $this->request->getPost('id');
        $model = new MasjidVolunteerModel();

        // id dari POST tak dipercaya: pastikan milik masjid ini sebelum menimpa.
        if ($id) {
            $milik = $model->where(['id' => $id, 'masjid_id' => $masjidId])->first();
            if (! $milik) {
                return redirect()->to('dashboard/relawan')->with('error', 'Relawan tidak ditemukan.');
            }
        }

        $nama = trim((string) $this->request->getPost('name'));
        if ($nama === '') {
            return redirect()->back()->withInput()->with('error', 'Nama relawan wajib diisi.');
        }

        $data = [
            'masjid_id' => $masjidId,
            'name'      => $nama,
            'phone'     => $this->request->getPost('phone') ?: null,
            'role'      => $this->request->getPost('role') ?: null,
            'status'    => in_array($this->request->getPost('status'), ['active', 'inactive'], true)
                            ? $this->request->getPost('status') : 'active',
        ];
        if (! $id) {
            $data['joined_at'] = date('Y-m-d');
        }

        $ok = $id ? $model->update($id, $data) : $model->insert($data);
        if (! $ok) {
            return redirect()->back()->withInput()
                ->with('error', 'Gagal menyimpan: ' . implode(' ', $model->errors()));
        }

        return redirect()->to('dashboard/relawan')->with('success', 'Data relawan tersimpan.');
    }

    /**
     * Hapus relawan. Lewat POST (bukan GET) + token CSRF: tautan GET yang
     * merusak data bisa dipicu diam-diam dari halaman lain selama pengurus
     * masih login.
     */
    public function delete()
    {
        $masjidId = session()->get('masjid_id');
        $id = (int) $this->request->getPost('id');
        $model = new MasjidVolunteerModel();
        $vol = $model->where(['id' => $id, 'masjid_id' => $masjidId])->first();
        if ($vol) {
            // Log poin ikut dibersihkan agar tak menggantung.
            (new MasjidVolunteerPointModel())->where(['volunteer_id' => $id, 'masjid_id' => $masjidId])->delete();
            $model->delete($id);
            return redirect()->to('dashboard/relawan')->with('success', 'Relawan dihapus.');
        }
        return redirect()->to('dashboard/relawan')->with('error', 'Relawan tidak ditemukan.');
    }

    /**
     * Beri poin partisipasi. Dicatat di log (jejak & alasan) lalu total di
     * relawan disegarkan dari jumlah log — bukan sekadar ditambah — agar cache
     * poin tak pernah menyimpang bila ada penghapusan log kelak.
     */
    public function awardPoints()
    {
        $masjidId = session()->get('masjid_id');
        $volId  = $this->request->getPost('volunteer_id');
        $points = (int) $this->request->getPost('points');
        $reason = trim((string) $this->request->getPost('reason'));

        $model = new MasjidVolunteerModel();
        $vol = $model->where(['id' => $volId, 'masjid_id' => $masjidId])->first();
        if (! $vol) {
            return redirect()->to('dashboard/relawan')->with('error', 'Relawan tidak ditemukan.');
        }
        if ($points === 0) {
            return redirect()->to('dashboard/relawan')->with('error', 'Poin tidak boleh 0.');
        }

        $pointModel = new MasjidVolunteerPointModel();
        $pointModel->insert([
            'masjid_id'    => $masjidId,
            'volunteer_id' => $volId,
            'points'       => $points,
            'reason'       => $reason ?: null,
        ]);

        $row = $pointModel->selectSum('points')->where(['volunteer_id' => $volId, 'masjid_id' => $masjidId])->get()->getRow();
        $model->update($volId, ['points' => (int) ($row->points ?? 0)]);

        // Tanpa esc(): view meng-esc() flashdata saat menampilkannya, dan
        // meloloskan dua kali membuat nama ber-apostrof tampil sebagai &#039;.
        return redirect()->to('dashboard/relawan')->with('success', 'Poin diberikan kepada ' . $vol['name'] . '.');
    }

    public function certificate($id)
    {
        $masjidId = session()->get('masjid_id');
        $vol = (new MasjidVolunteerModel())->where(['id' => $id, 'masjid_id' => $masjidId])->first();
        if (! $vol) {
            return redirect()->to('dashboard/relawan')->with('error', 'Relawan tidak ditemukan.');
        }
        $riwayat = (new MasjidVolunteerPointModel())
            ->where(['volunteer_id' => $id, 'masjid_id' => $masjidId])
            ->orderBy('created_at', 'DESC')->findAll();

        return view('dashboard/volunteers/certificate', [
            'title'     => 'Sertifikat - ' . $vol['name'],
            'volunteer' => $vol,
            'masjid'    => (new \App\Models\MasjidModel())->find($masjidId),
            'riwayat'   => $riwayat,
        ]);
    }
}
