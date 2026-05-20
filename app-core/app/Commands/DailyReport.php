<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\MasjidModel;
use App\Models\MasjidFinanceTransactionModel;
use App\Models\MasjidNewsModel;
use App\Models\MasjidProgramModel;
use App\Libraries\TelegramLibrary;

class DailyReport extends BaseCommand
{
    protected $group       = 'Reporting';
    protected $name        = 'report:daily';
    protected $description = 'Generate and send daily masjid report to Telegram.';

    public function run(array $params)
    {
        $masjidModel = new MasjidModel();
        $transModel = new MasjidFinanceTransactionModel();
        $newsModel = new MasjidNewsModel();
        $programModel = new MasjidProgramModel();
        $telegram = new TelegramLibrary();

        $today = date('Y-m-d');
        $sevenDaysAgo = date('Y-m-d H:i:s', strtotime('-7 days'));

        // 1. Total & New Mosques
        $totalMasjid = $masjidModel->countAllResults();
        $newMasjidToday = $masjidModel->where('DATE(created_at)', $today)->countAllResults();

        // 2. Identify Active vs Less Active
        // Active = has transaction OR news OR program in last 7 days
        $allMasjids = $masjidModel->findAll();
        $activeMasjids = [];
        $lessActiveMasjids = [];

        foreach ($allMasjids as $masjid) {
            $hasTrans = $transModel->where('masjid_id', $masjid['id'])
                                  ->where('created_at >=', $sevenDaysAgo)
                                  ->countAllResults();
            
            $hasNews = $newsModel->where('masjid_id', $masjid['id'])
                                ->where('created_at >=', $sevenDaysAgo)
                                ->countAllResults();

            $hasProgram = $programModel->where('masjid_id', $masjid['id'])
                                      ->where('created_at >=', $sevenDaysAgo)
                                      ->countAllResults();

            if ($hasTrans > 0 || $hasNews > 0 || $hasProgram > 0) {
                $activeMasjids[] = $masjid['name'];
            } else {
                $lessActiveMasjids[] = $masjid['name'];
            }
        }

        // 3. Daily Activities
        $totalTransToday = $transModel->where('DATE(created_at)', $today)->countAllResults();
        $sumTransToday = $transModel->where('DATE(created_at)', $today)->selectSum('amount')->get()->getRowArray()['amount'] ?? 0;

        // 4. Compose Message
        $message = "<b>📊 LAPORAN HARIAN MASJ.ID</b>\n";
        $message .= "📅 Tanggal: " . date('d M Y') . "\n\n";
        
        $message .= "<b>🏠 STATISTIK MASJID</b>\n";
        $message .= "• Total Masjid: <b>{$totalMasjid}</b>\n";
        $message .= "• Masjid Baru Hari Ini: <b>{$newMasjidToday}</b>\n";
        $message .= "• Masjid Aktif (7 hr terakhir): <b>" . count($activeMasjids) . "</b>\n";
        $message .= "• Masjid Kurang Aktif: <b>" . count($lessActiveMasjids) . "</b>\n\n";

        $message .= "<b>💰 AKTIVITAS HARI INI</b>\n";
        $message .= "• Total Transaksi: <b>{$totalTransToday}</b>\n";
        $message .= "• Nilai Transaksi: <b>Rp " . number_format($sumTransToday, 0, ',', '.') . "</b>\n\n";

        if (!empty($activeMasjids)) {
            $message .= "<b>✅ TOP AKTIF:</b>\n";
            $message .= "<i>" . implode(', ', array_slice($activeMasjids, 0, 5)) . (count($activeMasjids) > 5 ? '...' : '') . "</i>\n\n";
        }

        if (!empty($lessActiveMasjids)) {
            $message .= "<b>⚠️ PERLU PERHATIAN (KURANG AKTIF):</b>\n";
            $message .= "<i>" . implode(', ', array_slice($lessActiveMasjids, 0, 5)) . (count($lessActiveMasjids) > 5 ? '...' : '') . "</i>\n";
        }

        $message .= "\n🚀 <i>Terus semangat menebar manfaat!</i>";

        // 5. Send to Telegram
        $result = $telegram->sendMessage($message);

        if ($result) {
            CLI::write('Daily report sent successfully!', 'green');
        } else {
            CLI::error('Failed to send daily report.');
        }
    }
}
