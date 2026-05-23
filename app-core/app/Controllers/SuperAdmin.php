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

        // Calculate Social Impact Metrics
        $db = \Config\Database::connect();
        
        // DAU / MAU based on users.last_login
        $dau = $userModel->where('last_login >=', date('Y-m-d H:i:s', strtotime('-1 days')))->countAllResults();
        $mau = $userModel->where('last_login >=', date('Y-m-d H:i:s', strtotime('-30 days')))->countAllResults();

        // MRR (Monthly Recurring ZIS) - Total Pemasukan in the last 30 days
        $mrrRow = $financeModel->selectSum('amount')
            ->where('type', 'pemasukan')
            ->where('date >=', date('Y-m-d', strtotime('-30 days')))
            ->first();
        $mrr = $mrrRow['amount'] ?? 0;

        // LTV (Life Time Value / Total Penyaluran/Pengeluaran all time)
        $ltvRow = $financeModel->selectSum('amount')
            ->where('type', 'pengeluaran')
            ->first();
        $ltv = $ltvRow['amount'] ?? 0;

        // Programs
        $totalPrograms = $programModel->countAllResults();
        $activePrograms = $programModel->where('status', 'published')->countAllResults();

        // Chart Data: Masjid Registration per month (last 6 months)
        $chartLabels = [];
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = date('Y-m-01', strtotime("-$i months"));
            $monthEnd = date('Y-m-t', strtotime("-$i months"));
            $chartLabels[] = date('M Y', strtotime($monthStart));
            $count = $masjidModel->where('created_at >=', $monthStart . ' 00:00:00')
                                 ->where('created_at <=', $monthEnd . ' 23:59:59')
                                 ->countAllResults();
            $chartData[] = $count;
        }

        $data = [
            'title' => 'Super Admin Dashboard - Masj.id',
            'stats' => [
                'total_masjid'  => $masjidModel->countAllResults(),
                'total_users'   => $userModel->countAllResults(),
                'dau'           => $dau,
                'mau'           => $mau,
                'mrr'           => $mrr,
                'ltv'           => $ltv,
                'total_programs'  => $totalPrograms,
                'active_programs' => $activePrograms,
            ],
            'chart' => [
                'labels' => json_encode($chartLabels),
                'data'   => json_encode($chartData)
            ],
            'recent_masjids' => $masjidModel->orderBy('created_at', 'DESC')->findAll(5),
        ];

        return view('superadmin/dashboard', $data);
    }

    public function masjid(): string
    {
        $db = \Config\Database::connect();
        $builder = $db->table('masjid');
        $builder->select('masjid.*, u.name as pic_name, u.email as pic_email, u.phone as pic_phone, u.last_login');
        // Get the creator/main admin of the masjid
        $builder->join('masjid_pengurus mp', 'mp.masjid_id = masjid.id AND mp.is_creator = 1', 'left');
        $builder->join('users u', 'u.id = mp.user_id', 'left');
        $builder->orderBy('masjid.name', 'ASC');
        
        $data = [
            'title' => 'Manajemen Masjid - Super Admin',
            'masjids' => $builder->get()->getResultArray(),
        ];
        return view('superadmin/masjid_list', $data);
    }

    public function users(): string
    {
        $userModel = new UserModel();
        $data = [
            'title' => 'Manajemen User - Super Admin',
            'users' => $userModel->orderBy('created_at', 'DESC')->findAll(),
        ];
        return view('superadmin/user_list', $data);
    }

    // Impersonate Masjid Management
    public function manageMasjid($id)
    {
        $masjidModel = new MasjidModel();
        $masjid = $masjidModel->find($id);

        if (!$masjid) {
            return redirect()->back()->with('error', 'Masjid tidak ditemukan.');
        }

        // Set masjid context in session
        session()->set([
            'masjid_id'       => $masjid['id'],
            'masjid_name'     => $masjid['name'],
            'masjid_username' => $masjid['username'],
            // Keep role as superadmin so we can still access superadmin panel
        ]);

        return redirect()->to('dashboard')->with('success', 'Sekarang mengelola: ' . $masjid['name']);
    }

    public function profile()
    {
        $data = [
            'title' => 'Profil Saya - Super Admin',
            'user'  => (new UserModel())->find(session()->get('user_id'))
        ];
        return view('superadmin/profile', $data);
    }

    public function updatePassword()
    {
        $rules = [
            'current_password' => 'required',
            'new_password'     => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $user = $userModel->find(session()->get('user_id'));

        if (!password_verify($this->request->getPost('current_password'), $user['password_hash'])) {
            return redirect()->back()->with('error', 'Password saat ini salah.');
        }

        $userModel->update($user['id'], [
            'password_hash' => password_hash($this->request->getPost('new_password'), PASSWORD_DEFAULT)
        ]);

        return redirect()->to('superadmin/profile')->with('success', 'Password berhasil diperbarui.');
    // --------------------------------------------------------------------
    // LMS MANAGEMENT (SUPER ADMIN)
    // --------------------------------------------------------------------

    public function lmsModules()
    {
        $moduleModel = new \App\Models\LmsModuleModel();
        $data = [
            'title' => 'Manajemen LMS Modul - Super Admin',
            'modules' => $moduleModel->orderBy('created_at', 'DESC')->findAll(),
        ];
        return view('superadmin/lms/modules', $data);
    }

    public function createLmsModule()
    {
        return view('superadmin/lms/module_form', [
            'title' => 'Buat Modul Baru - Super Admin',
            'module' => null
        ]);
    }

    public function editLmsModule($id)
    {
        $moduleModel = new \App\Models\LmsModuleModel();
        $module = $moduleModel->find($id);

        if (!$module) return redirect()->to('superadmin/lms')->with('error', 'Modul tidak ditemukan.');

        return view('superadmin/lms/module_form', [
            'title' => 'Edit Modul - Super Admin',
            'module' => $module
        ]);
    }

    public function saveLmsModule()
    {
        $moduleModel = new \App\Models\LmsModuleModel();
        $id = $this->request->getPost('id');
        $title = $this->request->getPost('title');

        $data = [
            'title' => $title,
            'slug' => url_title($title, '-', true),
            'description' => $this->request->getPost('description'),
            'lembaga_pemateri' => $this->request->getPost('lembaga_pemateri'),
            'status' => $this->request->getPost('status')
        ];

        // Handle Thumbnail Upload
        $file = $this->request->getFile('thumbnail');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $storage = new \App\Libraries\Storage();
            if ($id) {
                $oldItem = $moduleModel->find($id);
                if (!empty($oldItem['thumbnail'])) $storage->delete($oldItem['thumbnail']);
            }
            $fileName = $storage->upload($file, 'lms');
            if ($fileName) $data['thumbnail'] = $fileName;
        }

        if ($id) {
            $moduleModel->update($id, $data);
        } else {
            $moduleModel->insert($data);
        }

        return redirect()->to('superadmin/lms')->with('success', 'Modul berhasil disimpan.');
    }

    public function deleteLmsModule($id)
    {
        $moduleModel = new \App\Models\LmsModuleModel();
        $module = $moduleModel->find($id);
        if ($module) {
            if (!empty($module['thumbnail'])) (new \App\Libraries\Storage())->delete($module['thumbnail']);
            $moduleModel->delete($id);
            return redirect()->to('superadmin/lms')->with('success', 'Modul dihapus.');
        }
        return redirect()->to('superadmin/lms')->with('error', 'Modul tidak ditemukan.');
    }

    // --- MATERIALS ---

    public function lmsMaterials($moduleId)
    {
        $moduleModel = new \App\Models\LmsModuleModel();
        $materialModel = new \App\Models\LmsMaterialModel();
        
        $module = $moduleModel->find($moduleId);
        if (!$module) return redirect()->to('superadmin/lms')->with('error', 'Modul tidak ditemukan.');

        $data = [
            'title' => 'Materi: ' . $module['title'],
            'module' => $module,
            'materials' => $materialModel->where('module_id', $moduleId)->orderBy('order_number', 'ASC')->findAll(),
        ];
        return view('superadmin/lms/materials', $data);
    }

    public function createLmsMaterial($moduleId)
    {
        $moduleModel = new \App\Models\LmsModuleModel();
        return view('superadmin/lms/material_form', [
            'title' => 'Tambah Materi Baru',
            'module' => $moduleModel->find($moduleId),
            'material' => null
        ]);
    }

    public function editLmsMaterial($id)
    {
        $materialModel = new \App\Models\LmsMaterialModel();
        $moduleModel = new \App\Models\LmsModuleModel();
        $material = $materialModel->find($id);

        if (!$material) return redirect()->back()->with('error', 'Materi tidak ditemukan.');

        return view('superadmin/lms/material_form', [
            'title' => 'Edit Materi',
            'module' => $moduleModel->find($material['module_id']),
            'material' => $material
        ]);
    }

    public function saveLmsMaterial()
    {
        $materialModel = new \App\Models\LmsMaterialModel();
        $id = $this->request->getPost('id');
        $moduleId = $this->request->getPost('module_id');
        $type = $this->request->getPost('type');
        $content = $this->request->getPost('content');

        // Handle PDF Upload if type is PDF and a file is provided
        $file = $this->request->getFile('pdf_file');
        if ($type == 'pdf' && $file && $file->isValid() && !$file->hasMoved()) {
            $storage = new \App\Libraries\Storage();
            if ($id) {
                $oldItem = $materialModel->find($id);
                if ($oldItem['type'] == 'pdf' && !empty($oldItem['content'])) {
                    $storage->delete($oldItem['content']);
                }
            }
            $fileName = $storage->upload($file, 'lms');
            if ($fileName) $content = $fileName;
        }

        $data = [
            'module_id' => $moduleId,
            'title' => $this->request->getPost('title'),
            'type' => $type,
            'content' => $content,
            'order_number' => $this->request->getPost('order_number') ?: 0
        ];

        if ($id) {
            $materialModel->update($id, $data);
        } else {
            $materialModel->insert($data);
        }

        return redirect()->to("superadmin/lms/{$moduleId}/materials")->with('success', 'Materi berhasil disimpan.');
    }

    public function deleteLmsMaterial($id)
    {
        $materialModel = new \App\Models\LmsMaterialModel();
        $material = $materialModel->find($id);
        
        if ($material) {
            if ($material['type'] == 'pdf' && !empty($material['content'])) {
                // Ignore external URLs if any, just attempt to delete from storage
                if (!str_starts_with($material['content'], 'http')) {
                    (new \App\Libraries\Storage())->delete($material['content']);
                }
            }
            $moduleId = $material['module_id'];
            $materialModel->delete($id);
            return redirect()->to("superadmin/lms/{$moduleId}/materials")->with('success', 'Materi dihapus.');
        }
        return redirect()->back()->with('error', 'Materi tidak ditemukan.');
    }
}
