<?php

namespace App\Controllers;

use App\Models\MasjidModel;
use App\Models\UserModel;
use App\Models\MasjidFinanceTransactionModel;
use App\Models\MasjidWargaModel;
use App\Models\MasjidProgramModel;

class SuperAdmin extends BaseController
{
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        // Ensure only superadmins can access this controller
        if (session()->get('role') !== 'superadmin') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    public function index(): string
    {
        $masjidModel = new MasjidModel();
        $userModel = new UserModel();
        $financeModel = new MasjidFinanceTransactionModel();
        $wargaModel = new MasjidWargaModel();
        $programModel = new MasjidProgramModel();

        $data = [
            'title' => 'Super Admin Dashboard - Masj.id',
            'stats' => [
                'total_masjid'  => $masjidModel->countAllResults(),
                'total_users'   => $userModel->countAllResults(),
                'total_dana'    => $financeModel->selectSum('amount')->where('type', 'pemasukan')->first()['amount'] ?? 0,
                'total_warga'   => $wargaModel->countAllResults(),
                'active_programs' => $programModel->where('status', 'published')->countAllResults(),
            ],
            'recent_masjids' => $masjidModel->orderBy('created_at', 'DESC')->findAll(5),
        ];

        return view('superadmin/dashboard', $data);
    }

    public function masjid(): string
    {
        $masjidModel = new MasjidModel();
        $data = [
            'title' => 'Manajemen Masjid - Super Admin',
            'masjids' => $masjidModel->orderBy('name', 'ASC')->findAll(),
        ];
        return view('superadmin/masjid_list', $data);
    }

    public function users(): string
    {
        $userModel = new UserModel();
        $data = [
            'title' => 'Manajemen User - Super Admin',
            'users' => $userModel->orderBy('name', 'ASC')->findAll(),
        ];
        return view('superadmin/user_list', $data);
    }
}
