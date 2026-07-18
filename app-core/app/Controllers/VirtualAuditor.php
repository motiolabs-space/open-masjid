<?php

namespace App\Controllers;

use App\Models\MasjidFinanceTransactionModel;
use App\Models\MasjidFinanceCategoryModel;
use App\Libraries\SumoPodAI;

class VirtualAuditor extends BaseController
{
    public function index()
    {
        $masjidId = session()->get('masjid_id');
        if (!$masjidId) return redirect()->to('auth/select-masjid');

        $transactionModel = new MasjidFinanceTransactionModel();
        
        // Get available months based on transactions
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT DISTINCT DATE_FORMAT(date, '%Y-%m') as month_year 
            FROM masjid_finance_transactions 
            WHERE masjid_id = ? 
            ORDER BY month_year DESC
        ", [$masjidId]);
        
        $months = $query->getResultArray();

        return view('dashboard/auditor/index', [
            'title'  => 'Virtual Auditor (AI) - Masj.id',
            'months' => $months
        ]);
    }

    public function runAudit()
    {
        $masjidId = session()->get('masjid_id');
        if (!$masjidId) return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);

        $monthYear = $this->request->getPost('month_year'); // e.g., '2023-10'
        if (!$monthYear) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Pilih bulan audit terlebih dahulu.']);
        }

        // 1. Get Current Month Data
        $currentData = $this->getAggregatedExpenses($masjidId, $monthYear);
        
        if (empty($currentData)) {
            return $this->response->setJSON([
                'status' => 'success',
                'anomalies' => [],
                'message' => 'Tidak ada pengeluaran di bulan ini untuk diaudit.'
            ]);
        }

        // 2. Get Past 3 Months Data for Baseline
        $historicalData = $this->getHistoricalAverages($masjidId, $monthYear);

        // 3. Send to AI (tingkat 'berat' diatur di dalam runFinancialAudit)
        $ai = new SumoPodAI((int) $masjidId);
        $anomalies = $ai->runFinancialAudit($currentData, $historicalData);

        if ($anomalies === null) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'AI gagal memproses data audit. Silakan coba lagi.']);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'anomalies' => $anomalies,
            'current_data' => $currentData,
            'historical_data' => $historicalData
        ]);
    }

    private function getAggregatedExpenses($masjidId, $monthYear)
    {
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT c.name as category_name, SUM(t.amount) as total_amount
            FROM masjid_finance_transactions t
            LEFT JOIN masjid_finance_categories c ON t.category_id = c.id
            WHERE t.masjid_id = ? 
              AND t.type = 'pengeluaran'
              AND DATE_FORMAT(t.date, '%Y-%m') = ?
            GROUP BY t.category_id, c.name
        ", [$masjidId, $monthYear]);

        return $query->getResultArray();
    }

    private function getHistoricalAverages($masjidId, $currentMonthYear)
    {
        // Get the previous 3 months
        $currentDate = $currentMonthYear . '-01';
        $endDate = date('Y-m-d', strtotime($currentDate . ' -1 day'));
        $startDate = date('Y-m-d', strtotime($currentDate . ' -3 months'));

        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT c.name as category_name, (SUM(t.amount) / 3) as avg_monthly_amount
            FROM masjid_finance_transactions t
            LEFT JOIN masjid_finance_categories c ON t.category_id = c.id
            WHERE t.masjid_id = ? 
              AND t.type = 'pengeluaran'
              AND t.date BETWEEN ? AND ?
            GROUP BY t.category_id, c.name
        ", [$masjidId, $startDate, $endDate]);

        return $query->getResultArray();
    }
}
