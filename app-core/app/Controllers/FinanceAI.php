<?php

namespace App\Controllers;

use App\Models\MasjidFinanceTransactionModel;
use App\Models\MasjidFinanceCategoryModel;
use App\Models\MasjidProgramModel;
use App\Libraries\SumoPodAI;

class FinanceAI extends BaseController
{
    public function importCSV()
    {
        $masjidId = session()->get('masjid_id');
        return view('dashboard/keuangan/import_csv', [
            'title' => 'Import Mutasi Bank (AI) - Masj.id'
        ]);
    }

    public function processCSV()
    {
        $masjidId = session()->get('masjid_id');
        $file = $this->request->getFile('csv_file');

        if (!$file || !$file->isValid() || $file->getExtension() !== 'csv') {
            return redirect()->back()->with('error', 'Silakan unggah file CSV yang valid.');
        }

        $csvData = array_map('str_getcsv', file($file->getTempName()));
        if (count($csvData) < 2) {
            return redirect()->back()->with('error', 'File CSV kosong atau tidak valid.');
        }

        // Asumsi format CSV: Tanggal, Deskripsi, Jumlah, Tipe (Masuk/Keluar)
        // Kita hanya butuh list transaksi untuk dianalisis AI
        $header = array_shift($csvData);
        
        $transactions = [];
        foreach ($csvData as $idx => $row) {
            if (count($row) >= 4) {
                $transactions[] = [
                    'id' => $idx,
                    'date' => trim($row[0]),
                    'description' => trim($row[1]),
                    'amount' => (float) str_replace(['Rp', '.', ','], '', trim($row[2])),
                    'type' => strtolower(trim($row[3])) === 'keluar' ? 'pengeluaran' : 'pemasukan'
                ];
            }
        }

        if (empty($transactions)) {
            return redirect()->back()->with('error', 'Tidak ada data transaksi yang dapat diproses.');
        }

        // Get Categories
        $categoryModel = new MasjidFinanceCategoryModel();
        $categories = $categoryModel->where('masjid_id', $masjidId)->findAll();
        $catMap = [];
        foreach ($categories as $cat) {
            $catMap[$cat['type']][] = ['id' => $cat['id'], 'name' => $cat['name']];
        }

        // Call AI
        $sumoPod = new SumoPodAI();
        $prompt = "Anda adalah AI Akuntan Masjid. Tugas Anda adalah mengkategorikan transaksi bank berikut ke dalam kategori yang tepat.\n\n";
        $prompt .= "Kategori Pemasukan Tersedia: " . json_encode($catMap['pemasukan'] ?? []) . "\n";
        $prompt .= "Kategori Pengeluaran Tersedia: " . json_encode($catMap['pengeluaran'] ?? []) . "\n\n";
        $prompt .= "Data Transaksi:\n" . json_encode($transactions) . "\n\n";
        $prompt .= "Output HANYA dalam format array JSON valid dengan key:\n";
        $prompt .= "- 'id' (id dari input)\n";
        $prompt .= "- 'category_id' (id kategori yang paling cocok, jika tidak ada berikan null)\n";
        $prompt .= "- 'suggested_category_name' (nama kategori yang dipilih atau saran kategori baru)\n";
        $prompt .= "TIDAK ADA teks lain selain JSON.";

        $response = $sumoPod->chatCompletion($prompt, [
            'temperature' => 0.1,
            'max_tokens' => 1500
        ]);

        $aiResults = [];
        if ($response) {
            $response = str_replace(['```json', '```'], '', trim($response));
            $aiResults = json_decode($response, true) ?? [];
        }

        // Merge AI Results with Transactions
        $mergedTransactions = [];
        foreach ($transactions as $t) {
            $aiMatch = array_filter($aiResults, fn($r) => $r['id'] === $t['id']);
            $aiMatch = reset($aiMatch);
            
            $t['category_id'] = $aiMatch['category_id'] ?? null;
            $t['suggested_category_name'] = $aiMatch['suggested_category_name'] ?? 'Lainnya';
            $mergedTransactions[] = $t;
        }

        // Store temporarily in session for review page
        session()->set('temp_csv_transactions', $mergedTransactions);

        return redirect()->to('/dashboard/keuangan/review-csv');
    }

    public function reviewCSV()
    {
        $transactions = session()->get('temp_csv_transactions');
        if (!$transactions) {
            return redirect()->to('/dashboard/keuangan')->with('error', 'Tidak ada data CSV yang sedang direview.');
        }

        $masjidId = session()->get('masjid_id');
        $categoryModel = new MasjidFinanceCategoryModel();
        $categories = $categoryModel->where('masjid_id', $masjidId)->findAll();

        return view('dashboard/keuangan/review_csv', [
            'title' => 'Review Mutasi Bank - Masj.id',
            'transactions' => $transactions,
            'categories' => $categories
        ]);
    }

    public function saveCSV()
    {
        $masjidId = session()->get('masjid_id');
        $data = $this->request->getPost('transactions');
        
        if (empty($data)) {
            return redirect()->to('/dashboard/keuangan')->with('error', 'Tidak ada data yang disimpan.');
        }

        $transactionModel = new MasjidFinanceTransactionModel();
        $categoryModel = new MasjidFinanceCategoryModel();
        
        $insertData = [];
        foreach ($data as $t) {
            if (!empty($t['date']) && !empty($t['amount'])) {
                // If AI suggested a new category or missing category
                $catId = $t['category_id'];
                if (empty($catId) && !empty($t['suggested_category_name'])) {
                    $catType = $t['type'];
                    $slug = url_title($t['suggested_category_name'], '-', true);
                    
                    // Check if exists
                    $exist = $categoryModel->where('masjid_id', $masjidId)->where('slug', $slug)->first();
                    if ($exist) {
                        $catId = $exist['id'];
                    } else {
                        // Create new category
                        $catId = $categoryModel->insert([
                            'masjid_id' => $masjidId,
                            'name' => $t['suggested_category_name'],
                            'slug' => $slug,
                            'type' => $catType
                        ]);
                    }
                }

                // Format date from dd/mm/yyyy or yyyy-mm-dd to yyyy-mm-dd
                $dateRaw = $t['date'];
                if (strpos($dateRaw, '/') !== false) {
                    $parts = explode('/', $dateRaw);
                    if (count($parts) === 3) {
                        $dateRaw = $parts[2] . '-' . $parts[1] . '-' . $parts[0];
                    }
                }
                
                $insertData[] = [
                    'masjid_id' => $masjidId,
                    'category_id' => $catId,
                    'date' => $dateRaw,
                    'amount' => $t['amount'],
                    'type' => $t['type'],
                    'description' => $t['description']
                ];
            }
        }

        if (!empty($insertData)) {
            $transactionModel->insertBatch($insertData);
            session()->remove('temp_csv_transactions');
            return redirect()->to('/dashboard/keuangan')->with('success', count($insertData) . ' Transaksi berhasil disimpan.');
        }

        return redirect()->back()->with('error', 'Gagal menyimpan transaksi.');
    }

    public function generateReport()
    {
        $masjidId = session()->get('masjid_id');
        $transactionModel = new MasjidFinanceTransactionModel();
        $programModel = new MasjidProgramModel();

        // Get this month's data
        $currentMonth = date('Y-m');
        $transactions = $transactionModel->where('masjid_id', $masjidId)->like('date', $currentMonth)->findAll();
        
        $totalPemasukan = 0;
        $totalPengeluaran = 0;
        foreach ($transactions as $t) {
            if ($t['type'] === 'pemasukan') $totalPemasukan += $t['amount'];
            if ($t['type'] === 'pengeluaran') $totalPengeluaran += $t['amount'];
        }

        $activePrograms = $programModel->where('masjid_id', $masjidId)
            ->where('date_end >=', date('Y-m-d'))
            ->countAllResults();

        if ($this->request->getMethod() === 'POST' || $this->request->getMethod() === 'post') {
            $sumoPod = new SumoPodAI();
            
            $prompt = "Kamu adalah Sekretaris Masjid yang profesional, hangat, dan komunikatif.\n";
            $prompt .= "Buat draf narasi/copywriting Laporan Keuangan dan Kegiatan Bulanan yang cocok dikirim melalui WhatsApp Broadcast ke jamaah.\n";
            $prompt .= "Data Bulan Ini (" . date('F Y') . "):\n";
            $prompt .= "- Total Pemasukan: Rp " . number_format($totalPemasukan, 0, ',', '.') . "\n";
            $prompt .= "- Total Pengeluaran: Rp " . number_format($totalPengeluaran, 0, ',', '.') . "\n";
            $prompt .= "- Saldo Tersisa: Rp " . number_format($totalPemasukan - $totalPengeluaran, 0, ',', '.') . "\n";
            $prompt .= "- Jumlah Program/Kegiatan Aktif: $activePrograms\n\n";
            $prompt .= "Instruksi:\n";
            $prompt .= "1. Gunakan gaya bahasa yang sopan, bersyukur (Alhamdulillah), dan mengapresiasi donatur.\n";
            $prompt .= "2. Jangan terlalu kaku, gunakan sedikit emoji.\n";
            $prompt .= "3. Output HARUS BERUPA TEKS LANGSUNG yang siap di-copy-paste, JANGAN format JSON.";

            $response = $sumoPod->chatCompletion($prompt, [
                'temperature' => 0.7,
                'max_tokens' => 800
            ]);

            return $this->response->setJSON(['status' => 'success', 'data' => $response]);
        }

        return view('dashboard/keuangan/report_generator', [
            'title' => 'Generate Laporan (AI) - Masj.id',
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'activePrograms' => $activePrograms
        ]);
    }
}
