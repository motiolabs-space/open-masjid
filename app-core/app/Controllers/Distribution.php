<?php

namespace App\Controllers;

use App\Models\MustahikModel;
use App\Models\MustahikDistributionModel;
use App\Libraries\SumoPodAI;

class Distribution extends BaseController
{
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        // Ensure user is pengurus
        if (session()->get('role') !== 'pengurus') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    // -------------------------------------------------------------------------
    // MUSTAHIK MANAGEMENT
    // -------------------------------------------------------------------------

    public function index()
    {
        $mustahikModel = new MustahikModel();
        
        $data = [
            'title' => 'Penyaluran & Mustahik - Masj.id',
            // Order by AI Score DESC so the most eligible are on top
            'mustahiks' => $mustahikModel->where('masjid_id', session()->get('masjid_id'))
                                         ->orderBy('ai_score', 'DESC')
                                         ->orderBy('created_at', 'DESC')
                                         ->findAll()
        ];
        
        return view('dashboard/distribution/index', $data);
    }

    public function createMustahik()
    {
        $data = [
            'title'    => 'Tambah Mustahik',
            'mustahik' => null
        ];
        return view('dashboard/distribution/mustahik_form', $data);
    }

    public function editMustahik($id)
    {
        $mustahikModel = new MustahikModel();
        $mustahik = $mustahikModel->where(['id' => $id, 'masjid_id' => session()->get('masjid_id')])->first();
        
        if (!$mustahik) return redirect()->to('dashboard/distribution')->with('error', 'Mustahik tidak ditemukan.');

        $data = [
            'title'    => 'Edit Mustahik',
            'mustahik' => $mustahik
        ];
        return view('dashboard/distribution/mustahik_form', $data);
    }

    public function saveMustahik()
    {
        $mustahikModel = new MustahikModel();
        $id = $this->request->getPost('id');
        
        $data = [
            'masjid_id'        => session()->get('masjid_id'),
            'name'             => $this->request->getPost('name'),
            'nik'              => $this->request->getPost('nik'),
            'phone'            => $this->request->getPost('phone'),
            'address'          => $this->request->getPost('address'),
            'income_per_month' => $this->request->getPost('income_per_month') ?: 0,
            'dependents_count' => $this->request->getPost('dependents_count') ?: 0,
            'house_ownership'  => $this->request->getPost('house_ownership') ?: 'lainnya',
            'status'           => $this->request->getPost('status') ?: 'active'
        ];

        // Generate AI Score synchronously
        $sumoPod = new SumoPodAI();
        $aiResult = $sumoPod->scoreMustahik($data);

        if ($aiResult) {
            $data['ai_score'] = $aiResult['score'];
            $data['ai_reasoning'] = $aiResult['reasoning'];
        } else {
            // Keep existing if any, or null
            if (!$id) {
                $data['ai_score'] = null;
                $data['ai_reasoning'] = 'Gagal generate AI Score. Silakan klik Generate Manual.';
            }
        }

        if ($id) {
            $mustahikModel->update($id, $data);
            $msg = 'Data Mustahik berhasil diperbarui.';
        } else {
            $mustahikModel->insert($data);
            $msg = 'Data Mustahik baru berhasil ditambahkan.';
        }

        if ($aiResult) {
            $msg .= ' AI telah memberikan penilaian skoring terbaru.';
        }

        return redirect()->to('dashboard/distribution')->with('success', $msg);
    }

    public function deleteMustahik($id)
    {
        $mustahikModel = new MustahikModel();
        // Security check
        $exists = $mustahikModel->where(['id' => $id, 'masjid_id' => session()->get('masjid_id')])->first();
        if ($exists) {
            $mustahikModel->delete($id);
            return redirect()->to('dashboard/distribution')->with('success', 'Mustahik berhasil dihapus.');
        }
        return redirect()->to('dashboard/distribution')->with('error', 'Akses ditolak.');
    }

    public function generateScore($id)
    {
        $mustahikModel = new MustahikModel();
        $mustahik = $mustahikModel->where(['id' => $id, 'masjid_id' => session()->get('masjid_id')])->first();
        
        if (!$mustahik) return redirect()->to('dashboard/distribution')->with('error', 'Mustahik tidak ditemukan.');

        $sumoPod = new SumoPodAI();
        $aiResult = $sumoPod->scoreMustahik($mustahik);

        if ($aiResult) {
            $mustahikModel->update($id, [
                'ai_score'     => $aiResult['score'],
                'ai_reasoning' => $aiResult['reasoning']
            ]);
            return redirect()->back()->with('success', 'Skor AI berhasil diperbarui.');
        }

        return redirect()->back()->with('error', 'Gagal terhubung ke AI. Coba lagi nanti.');
    }

    // -------------------------------------------------------------------------
    // DISTRIBUTION HISTORY
    // -------------------------------------------------------------------------

    public function history()
    {
        $distModel = new MustahikDistributionModel();
        $mustahikModel = new MustahikModel();

        // Get distributions joined with mustahik name
        $db = \Config\Database::connect();
        $builder = $db->table('masjid_mustahik_distributions d');
        $builder->select('d.*, m.name as mustahik_name');
        $builder->join('masjid_mustahik m', 'm.id = d.mustahik_id');
        $builder->where('d.masjid_id', session()->get('masjid_id'));
        $builder->orderBy('d.date', 'DESC');
        $builder->orderBy('d.created_at', 'DESC');

        $data = [
            'title'   => 'Histori Penyaluran',
            'history' => $builder->get()->getResultArray()
        ];

        return view('dashboard/distribution/history', $data);
    }

    public function createDistribution($mustahikId = null)
    {
        $mustahikModel = new MustahikModel();
        $data = [
            'title' => 'Catat Penyaluran',
            'mustahiks' => $mustahikModel->where(['masjid_id' => session()->get('masjid_id'), 'status' => 'active'])->orderBy('name', 'ASC')->findAll(),
            'selected_mustahik_id' => $mustahikId
        ];
        return view('dashboard/distribution/distribution_form', $data);
    }

    public function saveDistribution()
    {
        $distModel = new MustahikDistributionModel();
        $data = [
            'masjid_id'   => session()->get('masjid_id'),
            'mustahik_id' => $this->request->getPost('mustahik_id'),
            'date'        => $this->request->getPost('date'),
            'amount'      => $this->request->getPost('amount'),
            'description' => $this->request->getPost('description')
        ];

        $distModel->insert($data);
        return redirect()->to('dashboard/distribution/history')->with('success', 'Penyaluran berhasil dicatat.');
    }
}
