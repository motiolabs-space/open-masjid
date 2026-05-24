<?php

namespace App\Controllers;

class Lms extends BaseController
{
    public function index()
    {
        $moduleModel = new \App\Models\LmsModuleModel();
        $masjidModel = new \App\Models\MasjidModel();
        
        $modules = $moduleModel->where('status', 'published')->orderBy('created_at', 'DESC')->findAll();
        
        $masjids = $masjidModel->findAll();
        $masjidMap = [];
        foreach ($masjids as $m) {
            $masjidMap[$m['id']] = $m['name'];
        }

        $materialModel = new \App\Models\LmsMaterialModel();

        foreach ($modules as &$mod) {
            if (is_numeric($mod['lembaga_pemateri']) && isset($masjidMap[$mod['lembaga_pemateri']])) {
                $mod['lembaga_nama'] = $masjidMap[$mod['lembaga_pemateri']];
            } else {
                $mod['lembaga_nama'] = $mod['lembaga_pemateri'];
            }
            
            // Check materials count
            $materials = $materialModel->where('module_id', $mod['id'])->orderBy('order_number', 'ASC')->findAll();
            $mod['material_count'] = count($materials);
            if ($mod['material_count'] == 1) {
                $mod['first_material_id'] = $materials[0]['id'];
            }
        }

        $data = [
            'title' => 'E-Learning (LMS) - Masj.id',
            'modules' => $modules,
        ];
        
        return view('dashboard/lms/index', $data);
    }

    public function module($slug)
    {
        $moduleModel = new \App\Models\LmsModuleModel();
        $materialModel = new \App\Models\LmsMaterialModel();
        $progressModel = new \App\Models\LmsProgressModel();
        
        $userId = session()->get('user_id');
        
        $module = $moduleModel->where('slug', $slug)->first();
        if (!$module) return redirect()->to('dashboard/lms')->with('error', 'Modul tidak ditemukan.');

        $masjidModel = new \App\Models\MasjidModel();
        if (is_numeric($module['lembaga_pemateri'])) {
            $masjid = $masjidModel->find($module['lembaga_pemateri']);
            $module['lembaga_nama'] = $masjid ? $masjid['name'] : $module['lembaga_pemateri'];
        } else {
            $module['lembaga_nama'] = $module['lembaga_pemateri'];
        }

        $materials = $materialModel->where('module_id', $module['id'])->orderBy('order_number', 'ASC')->findAll();
        
        // Calculate progress
        $totalMaterials = count($materials);
        $completedMaterials = 0;
        
        $progress = $progressModel->where('user_id', $userId)->findAll();
        $completedIds = array_column($progress, 'material_id');

        foreach ($materials as &$m) {
            $m['is_completed'] = in_array($m['id'], $completedIds);
            if ($m['is_completed']) $completedMaterials++;
        }

        $progressPercentage = $totalMaterials > 0 ? round(($completedMaterials / $totalMaterials) * 100) : 0;

        $data = [
            'title' => $module['title'] . ' - E-Learning',
            'module' => $module,
            'materials' => $materials,
            'progress' => $progressPercentage
        ];
        
        return view('dashboard/lms/module', $data);
    }

    public function material($id)
    {
        $materialModel = new \App\Models\LmsMaterialModel();
        $moduleModel = new \App\Models\LmsModuleModel();
        $progressModel = new \App\Models\LmsProgressModel();
        
        $userId = session()->get('user_id');
        $masjidId = session()->get('masjid_id');

        $material = $materialModel->find($id);
        if (!$material) return redirect()->to('dashboard/lms')->with('error', 'Materi tidak ditemukan.');

        $module = $moduleModel->find($material['module_id']);
        
        $masjidModel = new \App\Models\MasjidModel();
        if (is_numeric($module['lembaga_pemateri'])) {
            $masjid = $masjidModel->find($module['lembaga_pemateri']);
            $module['lembaga_nama'] = $masjid ? $masjid['name'] : $module['lembaga_pemateri'];
        } else {
            $module['lembaga_nama'] = $module['lembaga_pemateri'];
        }
        // Check if completed
        $isCompleted = $progressModel->where(['user_id' => $userId, 'material_id' => $id])->first() ? true : false;

        $data = [
            'title' => $material['title'] . ' - ' . $module['title'],
            'module' => $module,
            'material' => $material,
            'isCompleted' => $isCompleted
        ];
        
        return view('dashboard/lms/material', $data);
    }

    public function markCompleted($id)
    {
        $materialModel = new \App\Models\LmsMaterialModel();
        $progressModel = new \App\Models\LmsProgressModel();
        
        $userId = session()->get('user_id');
        $masjidId = session()->get('masjid_id');

        $material = $materialModel->find($id);
        if (!$material) return redirect()->back()->with('error', 'Materi tidak ditemukan.');

        $exists = $progressModel->where(['user_id' => $userId, 'material_id' => $id])->first();
        if (!$exists) {
            $progressModel->insert([
                'user_id' => $userId,
                'masjid_id' => $masjidId,
                'material_id' => $id,
                'completed_at' => date('Y-m-d H:i:s')
            ]);
        }

        $moduleModel = new \App\Models\LmsModuleModel();
        $module = $moduleModel->find($material['module_id']);

        return redirect()->to("dashboard/lms/module/{$module['slug']}")->with('success', 'Materi ditandai selesai.');
    }
}
