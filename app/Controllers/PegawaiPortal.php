<?php

namespace App\Controllers;

use App\Models\BukuTamuModel;
use App\Models\PegawaiModel;

class PegawaiPortal extends BaseController
{
    protected $pegawaiId;
    protected $pegawaiData;
    protected $pegawaiKodeOpd;

    public function __construct()
    {
        $this->pegawaiId = session()->get('pegawai_id');
        $this->pegawaiKodeOpd = session()->get('pegawai_kode_opd');
    }

    private function applyPegawaiTamuScope($query)
    {
        return $query->where('buku_tamu.kode_opd', $this->pegawaiKodeOpd)
                     ->groupStart()
                         ->where('buku_tamu.id_pegawai_tujuan', $this->pegawaiId)
                         ->orGroupStart()
                             ->where('buku_tamu.id_pegawai_tujuan', null)
                             ->orWhere('buku_tamu.id_pegawai_tujuan', '')
                             ->orWhere('buku_tamu.id_pegawai_tujuan', '0')
                         ->groupEnd()
                     ->groupEnd();
    }

    private function canAccessTamu(array $tamu): bool
    {
        return $tamu['id_pegawai_tujuan'] == $this->pegawaiId || $this->canClaimTamu($tamu);
    }

    private function canClaimTamu(array $tamu): bool
    {
        return empty($tamu['id_pegawai_tujuan']) && $tamu['kode_opd'] == $this->pegawaiKodeOpd;
    }

    private function countScopedGuests(?string $status = null, ?string $start = null, ?string $end = null): int
    {
        $bukuTamuModel = new BukuTamuModel();
        $query = $bukuTamuModel;
        $this->applyPegawaiTamuScope($query);

        if ($status) {
            $query->where('status_kunjungan', $status);
        }
        if ($start) {
            $query->where('waktu_datang >=', $start);
        }
        if ($end) {
            $query->where('waktu_datang <=', $end);
        }

        return $query->countAllResults();
    }

    public function dashboard()
    {
        $bukuTamuModel = new BukuTamuModel();

        // Today
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd   = date('Y-m-d 23:59:59');

        // Stats: own guests plus unassigned guests in the same OPD.
        $totalToday = $this->countScopedGuests(null, $todayStart, $todayEnd);

        // Stats: Menunggu verifikasi
        $totalPending = $this->countScopedGuests('menunggu');

        // Stats: Sedang berlangsung
        $totalOngoing = $this->countScopedGuests('berlangsung');

        // Stats: Selesai bulan ini
        $monthStart = date('Y-m-01 00:00:00');
        $monthEnd   = date('Y-m-t 23:59:59');
        $totalCompleted = $this->countScopedGuests('selesai', $monthStart, $monthEnd);

        // Recent guests (last 5 pending + ongoing)
        $recentGuestsQuery = $bukuTamuModel->select('buku_tamu.*, opd.nama_opd, bagian.nama_bagian')
                                           ->join('opd', 'opd.kode_opd = buku_tamu.kode_opd', 'left')
                                           ->join('bagian', 'bagian.kode_opd = buku_tamu.kode_opd AND bagian.kode_bagian = buku_tamu.kode_bagian', 'left')
                                           ->whereIn('buku_tamu.status_kunjungan', ['menunggu', 'berlangsung']);
        $this->applyPegawaiTamuScope($recentGuestsQuery);
        $recentGuests = $recentGuestsQuery->orderBy('buku_tamu.waktu_datang', 'DESC')
                                          ->limit(5)
                                          ->findAll();

        // Trend data (7 days)
        $trendLabels = [];
        $trendData   = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $trendLabels[] = date('d M', strtotime($date));

            $count = $this->countScopedGuests(null, $date . ' 00:00:00', $date . ' 23:59:59');
            $trendData[] = $count;
        }

        return view('pegawai_portal/dashboard', [
            'totalToday'     => $totalToday,
            'totalPending'   => $totalPending,
            'totalOngoing'   => $totalOngoing,
            'totalCompleted' => $totalCompleted,
            'recentGuests'   => $recentGuests,
            'trendLabels'    => $trendLabels,
            'trendData'      => $trendData,
        ]);
    }

    public function tamu()
    {
        $bukuTamuModel = new BukuTamuModel();
        // Filter
        $status = $this->request->getGet('status') ?: 'menunggu';

        $query = $bukuTamuModel->select('buku_tamu.*, opd.nama_opd, bagian.nama_bagian')
                                ->join('opd', 'opd.kode_opd = buku_tamu.kode_opd', 'left')
                                ->join('bagian', 'bagian.kode_opd = buku_tamu.kode_opd AND bagian.kode_bagian = buku_tamu.kode_bagian', 'left');
        $this->applyPegawaiTamuScope($query);

        if ($status !== 'semua') {
            $query->where('buku_tamu.status_kunjungan', $status);
        }

        $data['tamus']  = $query->orderBy('buku_tamu.waktu_datang', 'DESC')->findAll();
        $data['status'] = $status;

        return view('pegawai_portal/tamu', $data);
    }

    public function detail($hash)
    {
        $id = decode_id($hash);
        if (!$id) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $bukuTamuModel = new BukuTamuModel();

        $tamu = $bukuTamuModel->select('buku_tamu.*, opd.nama_opd, bagian.nama_bagian, subbagian.nama_subbagian, agenda.nama_agenda')
                               ->join('opd', 'opd.kode_opd = buku_tamu.kode_opd', 'left')
                               ->join('bagian', 'bagian.kode_opd = buku_tamu.kode_opd AND bagian.kode_bagian = buku_tamu.kode_bagian', 'left')
                               ->join('subbagian', 'subbagian.kode_opd = buku_tamu.kode_opd AND subbagian.kode_bagian = buku_tamu.kode_bagian AND subbagian.kode_subbagian = buku_tamu.kode_subbagian', 'left')
                               ->join('agenda', 'agenda.id_agenda = buku_tamu.id_agenda', 'left')
                               ->find($id);

        if (!$tamu) {
            return redirect()->to('pegawai-portal/tamu')->with('error', 'Data tamu tidak ditemukan.');
        }

        // Access check: own guests or unassigned guests in the same OPD.
        if (!$this->canAccessTamu($tamu)) {
            return redirect()->to('pegawai-portal/tamu')->with('error', 'Anda tidak memiliki akses ke data tamu ini.');
        }

        $data['tamu'] = $tamu;

        return view('pegawai_portal/detail', $data);
    }

    public function konfirmasiTamu($hash)
    {
        $id = decode_id($hash);
        if (!$id) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $bukuTamuModel = new BukuTamuModel();
        $tamu = $bukuTamuModel->find($id);

        if (!$tamu) {
            return redirect()->to('pegawai-portal/tamu')->with('error', 'Data tamu tidak ditemukan.');
        }

        // Access check: own guests or unassigned guests in the same OPD.
        if (!$this->canAccessTamu($tamu)) {
            return redirect()->to('pegawai-portal/tamu')->with('error', 'Anda tidak memiliki akses.');
        }

        if ($tamu['status_kunjungan'] !== 'menunggu') {
            return redirect()->to('pegawai-portal/tamu')->with('error', 'Tamu ini sudah bukan berstatus menunggu.');
        }

        $updateData = ['status_kunjungan' => 'berlangsung'];
        if ($this->canClaimTamu($tamu)) {
            $updateData['id_pegawai_tujuan'] = $this->pegawaiId;
        }

        $bukuTamuModel->update($id, $updateData);
        log_activity("Pegawai mengambil dan mengkonfirmasi Tamu #{$tamu['no_referensi']} ({$tamu['nama_tamu']})", 'buku_tamu', $id);

        return redirect()->to('pegawai-portal/tamu')->with('success', 'Tamu berhasil dikonfirmasi! Status: Berlangsung.');
    }

    public function updateStatus($hash)
    {
        $id = decode_id($hash);
        if (!$id) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $bukuTamuModel = new BukuTamuModel();
        $tamu = $bukuTamuModel->find($id);

        if (!$tamu) {
            return redirect()->to('pegawai-portal/tamu')->with('error', 'Data tamu tidak ditemukan.');
        }

        // Access check
        if ($tamu['id_pegawai_tujuan'] != $this->pegawaiId) {
            return redirect()->to('pegawai-portal/tamu')->with('error', 'Anda tidak memiliki akses.');
        }

        $newStatus = $this->request->getPost('status_kunjungan');
        if (!in_array($newStatus, ['berlangsung', 'selesai'])) {
            return redirect()->back()->with('error', 'Status tidak valid.');
        }

        $updateData = ['status_kunjungan' => $newStatus];
        
        if ($newStatus === 'selesai') {
            $updateData['waktu_pulang'] = date('Y-m-d H:i:s');
        }

        $bukuTamuModel->update($id, $updateData);
        log_activity("Pegawai mengubah status Tamu #{$tamu['no_referensi']} ({$tamu['nama_tamu']}) menjadi: $newStatus", 'buku_tamu', $id);

        return redirect()->to('pegawai-portal/tamu')->with('success', 'Status tamu berhasil diperbarui: ' . ucfirst($newStatus));
    }

    public function changePassword()
    {
        return redirect()->to('pegawai-portal/dashboard')->with('error', 'Ganti password dinonaktifkan karena akun menggunakan kredensial terintegrasi Simpelgan.');
    }

    public function saveChangePassword()
    {
        return redirect()->to('pegawai-portal/dashboard')->with('error', 'Ganti password dinonaktifkan karena akun menggunakan kredensial terintegrasi Simpelgan.');
    }
}
