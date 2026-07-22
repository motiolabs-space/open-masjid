<?php

namespace App\Models;

use CodeIgniter\Model;

class ApiAuditLogModel extends Model
{
    protected $table         = 'api_audit_logs';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'masjid_id', 'source', 'action', 'entity', 'entity_id',
        'payload', 'status', 'message', 'ip', 'created_at',
    ];
    protected $useTimestamps = false;

    /**
     * Mencatat satu percobaan perubahan lewat API/MCP.
     *
     * Dibungkus try/catch dan tidak pernah melempar: pencatatan audit tidak
     * boleh menggagalkan operasi yang sudah berhasil. Bila pencatatan gagal,
     * dicatat ke log aplikasi agar tetap ada jejaknya.
     */
    public function catat(array $data): void
    {
        try {
            $this->insert([
                'masjid_id' => $data['masjid_id'] ?? null,
                'source'    => $data['source'] ?? 'api',
                'action'    => $data['action'] ?? '',
                'entity'    => $data['entity'] ?? '',
                'entity_id' => $data['entity_id'] ?? null,
                // Payload dipangkas: audit untuk menelusuri, bukan menyalin data.
                'payload'   => isset($data['payload'])
                    ? mb_substr(json_encode($data['payload'], JSON_UNESCAPED_UNICODE), 0, 2000)
                    : null,
                'status'    => $data['status'] ?? 'success',
                'message'   => isset($data['message']) ? mb_substr((string) $data['message'], 0, 255) : null,
                'ip'        => $data['ip'] ?? null,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Gagal mencatat audit API: ' . $e->getMessage()
                . ' | data: ' . json_encode($data));
        }
    }

    /** Riwayat audit satu masjid, terbaru dulu. */
    public function riwayat(int $masjidId, int $limit = 100): array
    {
        return $this->where('masjid_id', $masjidId)
            ->orderBy('id', 'DESC')
            ->limit(max(1, min($limit, 500)))
            ->findAll();
    }
}
