<?php

namespace App\Models;

use CodeIgniter\Model;

class MasjidProgramRsvpModel extends Model
{
    protected $table         = 'masjid_program_rsvps';
    protected $primaryKey    = 'id';
    protected $allowedFields  = ['masjid_id', 'program_id', 'name', 'phone', 'guests', 'note', 'status'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Total tamu terkonfirmasi (mendaftar + hadir) sebuah program — dipakai di
     * halaman publik untuk "sekian orang akan hadir" dan cek kuota. no_show
     * tidak dihitung.
     */
    public function totalTamu(int $programId): int
    {
        $row = $this->selectSum('guests')
            ->where('program_id', $programId)
            ->whereIn('status', ['registered', 'attended'])
            ->get()->getRow();
        return (int) ($row->guests ?? 0);
    }

    /**
     * Ringkasan absensi untuk dashboard: jumlah pendaftar, total tamu, hadir,
     * dan tidak hadir.
     */
    public function ringkasan(int $programId): array
    {
        $rows = $this->select('status, COUNT(*) AS jml, COALESCE(SUM(guests),0) AS tamu')
            ->where('program_id', $programId)
            ->groupBy('status')
            ->get()->getResultArray();

        $out = ['pendaftar' => 0, 'tamu' => 0, 'hadir' => 0, 'tidak_hadir' => 0];
        foreach ($rows as $r) {
            $out['pendaftar'] += (int) $r['jml'];
            $out['tamu']      += (int) $r['tamu'];
            if ($r['status'] === 'attended') $out['hadir'] += (int) $r['jml'];
            if ($r['status'] === 'no_show')  $out['tidak_hadir'] += (int) $r['jml'];
        }
        return $out;
    }
}
