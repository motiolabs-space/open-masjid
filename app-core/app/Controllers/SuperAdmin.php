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
        $mustahikModel = new \App\Models\MustahikModel();
        $settingModel = new \App\Models\PlatformSettingModel();

        // Get Settings for North Star Metrics
        $settingsRaw = $settingModel->findAll();
        $settings = [];
        foreach ($settingsRaw as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        $yearStart = date('Y-01-01 00:00:00');
        $yearEnd = date('Y-12-31 23:59:59');
        $yearStartObj = date('Y-01-01');
        $yearEndObj = date('Y-12-31');

        $capaian_masjid = $masjidModel->where('created_at >=', $yearStart)->where('created_at <=', $yearEnd)->countAllResults();
        $capaian_program = $programModel->where('created_at >=', $yearStart)->where('created_at <=', $yearEnd)->countAllResults();
        $capaian_jamaah = $userModel->where('created_at >=', $yearStart)->where('created_at <=', $yearEnd)->countAllResults();
        $capaian_mustahik = $mustahikModel->where('created_at >=', $yearStart)->where('created_at <=', $yearEnd)->countAllResults();
        
        $danaRow = $financeModel->selectSum('amount')
            ->where('type', 'pengeluaran')
            ->where('date >=', $yearStartObj)
            ->where('date <=', $yearEndObj)
            ->first();
        $capaian_donasi = $danaRow['amount'] ?? 0;

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
            'north_star' => [
                'masjid' => ['capaian' => $capaian_masjid, 'target' => $settings['target_masjid'] ?? 0],
                'program' => ['capaian' => $capaian_program, 'target' => $settings['target_program'] ?? 0],
                'jamaah' => ['capaian' => $capaian_jamaah, 'target' => $settings['target_jamaah'] ?? 0],
                'mustahik' => ['capaian' => $capaian_mustahik, 'target' => $settings['target_mustahik'] ?? 0],
                'donasi' => ['capaian' => $capaian_donasi, 'target' => $settings['target_donasi'] ?? 0],
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
        $builder->select('masjid.*, u.name as pic_name, u.email as pic_email, u.phone as pic_phone, u.last_login,
            (SELECT COUNT(id) FROM masjid_programs WHERE masjid_programs.masjid_id = masjid.id) as total_programs,
            (SELECT COUNT(id) FROM masjid_warga WHERE masjid_warga.masjid_id = masjid.id) as total_jamaah,
            (SELECT COUNT(id) FROM masjid_mustahik WHERE masjid_mustahik.masjid_id = masjid.id) as total_mustahik,
            (SELECT SUM(amount) FROM masjid_finance_transactions WHERE masjid_finance_transactions.masjid_id = masjid.id AND type="pemasukan") as total_dana
        ');
        // Get the creator/main admin of the masjid
        $builder->join('masjid_pengurus mp', 'mp.masjid_id = masjid.id AND mp.is_creator = 1', 'left');
        $builder->join('users u', 'u.id = mp.user_id', 'left');
        
        $filter = $this->request->getGet('filter') ?? 'all';
        if ($filter == 'active') {
            $builder->groupStart()
                    ->where('u.last_login >=', date('Y-m-d H:i:s', strtotime('-30 days')))
                    ->groupEnd();
        } elseif ($filter == 'inactive') {
            $builder->groupStart()
                    ->where('u.last_login <', date('Y-m-d H:i:s', strtotime('-30 days')))
                    ->orWhere('u.last_login IS NULL')
                    ->groupEnd();
        }

        $search = $this->request->getGet('q');
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('masjid.name', $search)
                    ->orLike('masjid.username', $search)
                    ->orLike('u.name', $search)
                    ->groupEnd();
        }

        $builder->orderBy('masjid.created_at', 'DESC');
        
        $data = [
            'title' => 'Manajemen Masjid - Super Admin',
            'masjids' => $builder->get()->getResultArray(),
            'current_filter' => $filter
        ];
        return view('superadmin/masjid_list', $data);
    }

    public function programs(): string
    {
        $db = \Config\Database::connect();
        $builder = $db->table('masjid_programs');
        $builder->select('masjid_programs.*, masjid.name as masjid_name, masjid.username as masjid_username');
        $builder->join('masjid', 'masjid.id = masjid_programs.masjid_id');
        $builder->orderBy('masjid_programs.created_at', 'DESC');
        
        $data = [
            'title' => 'Monitoring Program - Super Admin',
            'programs' => $builder->get()->getResultArray(),
        ];
        return view('superadmin/program_list', $data);
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

    public function createUser()
    {
        $data = [
            'title' => 'Tambah User - Super Admin',
        ];
        return view('superadmin/user_form', $data);
    }

    public function saveUser()
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'role' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        }

        $userModel = new UserModel();
        $userModel->insert([
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'role' => $this->request->getPost('role'),
            'telegram_chat_id' => $this->request->getPost('telegram_chat_id') ?: null,
        ]);

        return redirect()->to('superadmin/users')->with('success', 'User berhasil ditambahkan.');
    }

    public function editUser($id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);
        
        if (!$user) {
            return redirect()->to('superadmin/users')->with('error', 'User tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit User - Super Admin',
            'user' => $user,
        ];
        return view('superadmin/user_form', $data);
    }

    public function updateUser($id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            return redirect()->to('superadmin/users')->with('error', 'User tidak ditemukan.');
        }

        $rules = [
            'name' => 'required',
            'email' => "required|valid_email|is_unique[users.email,id,{$id}]",
            'role' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        }

        $updateData = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'role' => $this->request->getPost('role'),
            'telegram_chat_id' => $this->request->getPost('telegram_chat_id') ?: null,
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $updateData['password_hash'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $userModel->update($id, $updateData);

        return redirect()->to('superadmin/users')->with('success', 'Data user berhasil diperbarui.');
    }

    public function deleteUser($id)
    {
        if ($id == session()->get('user_id')) {
            return redirect()->to('superadmin/users')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri saat sedang login.');
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);
        
        if ($user) {
            $userModel->delete($id);
            return redirect()->to('superadmin/users')->with('success', 'User berhasil dihapus.');
        }
        
        return redirect()->to('superadmin/users')->with('error', 'User tidak ditemukan.');
    }

    public function userAnalytics($id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            return redirect()->to('superadmin/users')->with('error', 'User tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        
        // Get Masjid Affiliations
        $affiliations = $db->table('masjid_pengurus')
            ->select('masjid_pengurus.*, masjid.name as masjid_name, masjid.username as masjid_username')
            ->join('masjid', 'masjid.id = masjid_pengurus.masjid_id')
            ->where('masjid_pengurus.user_id', $id)
            ->get()->getResultArray();

        $data = [
            'title' => 'User Analytics - Super Admin',
            'user' => $user,
            'affiliations' => $affiliations,
        ];
        
        return view('superadmin/user_analytics', $data);
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

    public function editMasjid($id)
    {
        $masjidModel = new MasjidModel();
        $userModel = new UserModel();
        $masjid = $masjidModel->find($id);

        if (!$masjid) {
            return redirect()->back()->with('error', 'Masjid tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $currentAdmin = $db->table('masjid_pengurus')
            ->where('masjid_id', $id)
            ->where('is_creator', 1)
            ->get()->getRowArray();

        $data = [
            'title' => 'Edit Masjid - Super Admin',
            'masjid' => $masjid,
            'currentAdminId' => $currentAdmin ? $currentAdmin['user_id'] : null,
            'users' => $userModel->findAll(),
        ];
        
        return view('superadmin/masjid_edit', $data);
    }

    public function updateMasjid($id)
    {
        $masjidModel = new MasjidModel();
        $masjid = $masjidModel->find($id);

        if (!$masjid) {
            return redirect()->back()->with('error', 'Masjid tidak ditemukan.');
        }

        $status = $this->request->getPost('status') ?: 'active';
        $newAdminId = $this->request->getPost('admin_id');

        // Update basic masjid data
        $updateData = [
            'name' => $this->request->getPost('name'),
            'username' => $this->request->getPost('username'),
            'status' => $status,
            'phone' => $this->request->getPost('phone'),
            'whatsapp' => $this->request->getPost('whatsapp'),
            'email' => $this->request->getPost('email'),
        ];
        
        // Update password functionality can be added if needed, but not in basic edit.
        $masjidModel->update($id, $updateData);

        // Update Admin
        if ($newAdminId) {
            $db = \Config\Database::connect();
            $currentAdmin = $db->table('masjid_pengurus')
                ->where('masjid_id', $id)
                ->where('is_creator', 1)
                ->get()->getRowArray();

            if (!$currentAdmin || $currentAdmin['user_id'] != $newAdminId) {
                // Remove old creator flag
                $db->table('masjid_pengurus')->where('masjid_id', $id)->update(['is_creator' => 0]);
                
                // Check if new admin is already a pengurus
                $existing = $db->table('masjid_pengurus')->where('masjid_id', $id)->where('user_id', $newAdminId)->get()->getRowArray();
                if ($existing) {
                    $db->table('masjid_pengurus')->where('id', $existing['id'])->update(['is_creator' => 1, 'role' => 'admin']);
                } else {
                    $db->table('masjid_pengurus')->insert([
                        'masjid_id' => $id,
                        'user_id' => $newAdminId,
                        'role' => 'admin',
                        'is_creator' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }

        return redirect()->to('superadmin/masjid')->with('success', 'Data Masjid berhasil diperbarui.');
    }

    public function deleteMasjid($id)
    {
        $masjidModel = new MasjidModel();
        $masjid = $masjidModel->find($id);

        if (!$masjid) {
            return redirect()->back()->with('error', 'Masjid tidak ditemukan.');
        }

        $masjidModel->delete($id);

        return redirect()->to('superadmin/masjid')->with('success', 'Masjid ' . esc($masjid['name']) . ' berhasil dihapus karena terindikasi spam.');
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
    }

    // --------------------------------------------------------------------
    // SETTINGS (SUPER ADMIN)
    // --------------------------------------------------------------------
    public function settings()
    {
        $settingModel = new \App\Models\PlatformSettingModel();
        
        // Fetch all settings and map key-value
        $settingsRaw = $settingModel->findAll();
        $settings = [];
        foreach ($settingsRaw as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        $data = [
            'title' => 'Pengaturan Platform - Super Admin',
            'settings' => $settings
        ];

        return view('superadmin/settings', $data);
    }

    public function saveSettings()
    {
        $settingModel = new \App\Models\PlatformSettingModel();
        
        $keysToUpdate = ['community_wa_link', 'community_tg_link', 'target_masjid', 'target_program', 'target_jamaah', 'target_mustahik', 'target_donasi'];

        foreach ($keysToUpdate as $key) {
            $value = $this->request->getPost($key);
            
            // Check if key exists
            $existing = $settingModel->find($key);
            if ($existing) {
                $settingModel->update($key, ['setting_value' => $value]);
            } else {
                $settingModel->insert([
                    'setting_key' => $key,
                    'setting_value' => $value
                ]);
            }
        }

        return redirect()->to('superadmin/settings')->with('success', 'Pengaturan berhasil disimpan.');
    }
    // --------------------------------------------------------------------
    // LMS MANAGEMENT (SUPER ADMIN)
    // --------------------------------------------------------------------

    public function lmsModules()
    {
        $moduleModel = new \App\Models\LmsModuleModel();
        $masjidModel = new \App\Models\MasjidModel();
        
        $modules = $moduleModel->findAll();
        $masjids = $masjidModel->findAll();
        $masjidMap = [];
        foreach ($masjids as $m) {
            $masjidMap[$m['id']] = $m['name'];
        }

        foreach ($modules as &$mod) {
            if (is_numeric($mod['lembaga_pemateri']) && isset($masjidMap[$mod['lembaga_pemateri']])) {
                $mod['lembaga_nama'] = $masjidMap[$mod['lembaga_pemateri']];
            } else {
                $mod['lembaga_nama'] = $mod['lembaga_pemateri'];
            }
        }

        $data = [
            'title' => 'LMS Modules',
            'modules' => $modules
        ];
        return view('superadmin/lms/modules', $data);
    }

    public function createLmsModule()
    {
        $masjidModel = new \App\Models\MasjidModel();
        return view('superadmin/lms/module_form', [
            'title' => 'Tambah Modul Baru',
            'module' => null,
            'masjids' => $masjidModel->findAll()
        ]);
    }

    public function editLmsModule($id)
    {
        $moduleModel = new \App\Models\LmsModuleModel();
        $masjidModel = new \App\Models\MasjidModel();
        $module = $moduleModel->find($id);

        if (!$module) return redirect()->back()->with('error', 'Modul tidak ditemukan.');

        return view('superadmin/lms/module_form', [
            'title' => 'Edit Modul',
            'module' => $module,
            'masjids' => $masjidModel->findAll()
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
