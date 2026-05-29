<?php

namespace App\Controllers;

use App\Models\BukuTamuModel;
use App\Models\PegawaiModel;

class PegawaiPortal extends BaseController
{
    protected $pegawaiId;
    protected $pegawaiData;

    public function __construct()
    {
        $this->pegawaiId = session()->get('pegawai_id');
    }

    public function dashboard()
    {
        $bukuTamuModel = new BukuTamuModel();
        $pegawaiId = $this->pegawaiId;

        // Today
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd   = date('Y-m-d 23:59:59');

        // Stats: Tamu hari ini (untuk pegawai ini)
        $totalToday = $bukuTamuModel->where('id_pegawai_tujuan', $pegawaiId)
                                     ->where('waktu_datang >=', $todayStart)
                                     ->where('waktu_datang <=', $todayEnd)
                                     ->countAllResults(false);

        // Stats: Menunggu verifikasi
        $totalPending = $bukuTamuModel->where('id_pegawai_tujuan', $pegawaiId)
                                       ->where('status_kunjungan', 'menunggu')
                                       ->countAllResults(false);

        // Stats: Sedang berlangsung
        $totalOngoing = $bukuTamuModel->where('id_pegawai_tujuan', $pegawaiId)
                                       ->where('status_kunjungan', 'berlangsung')
                                       ->countAllResults(false);

        // Stats: Selesai bulan ini
        $monthStart = date('Y-m-01 00:00:00');
        $monthEnd   = date('Y-m-t 23:59:59');
        $totalCompleted = $bukuTamuModel->where('id_pegawai_tujuan', $pegawaiId)
                                         ->where('status_kunjungan', 'selesai')
                                         ->where('waktu_datang >=', $monthStart)
                                         ->where('waktu_datang <=', $monthEnd)
                                         ->countAllResults(false);

        // Recent guests (last 5 pending + ongoing)
        $recentGuests = $bukuTamuModel->select('buku_tamu.*, opd.nama_opd, bagian.nama_bagian')
                                       ->join('opd', 'opd.kode_opd = buku_tamu.kode_opd')
                                       ->join('bagian', 'bagian.kode_opd = buku_tamu.kode_opd AND bagian.kode_bagian = buku_tamu.kode_bagian')
                                       ->where('buku_tamu.id_pegawai_tujuan', $pegawaiId)
                                       ->whereIn('buku_tamu.status_kunjungan', ['menunggu', 'berlangsung'])
                                       ->orderBy('buku_tamu.waktu_datang', 'DESC')
                                       ->limit(5)
                                       ->findAll();

        // Trend data (7 days)
        $trendLabels = [];
        $trendData   = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $trendLabels[] = date('d M', strtotime($date));

            $count = $bukuTamuModel->where('id_pegawai_tujuan', $pegawaiId)
                                    ->where('waktu_datang >=', $date . ' 00:00:00')
                                    ->where('waktu_datang <=', $date . ' 23:59:59')
                                    ->countAllResults();
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
        $pegawaiId = $this->pegawaiId;

        // Filter
        $status = $this->request->getGet('status') ?: 'menunggu';

        $query = $bukuTamuModel->select('buku_tamu.*, opd.nama_opd, bagian.nama_bagian')
                                ->join('opd', 'opd.kode_opd = buku_tamu.kode_opd')
                                ->join('bagian', 'bagian.kode_opd = buku_tamu.kode_opd AND bagian.kode_bagian = buku_tamu.kode_bagian')
                                ->where('buku_tamu.id_pegawai_tujuan', $pegawaiId);

        if ($status !== 'semua') {
            $query->where('buku_tamu.status_kunjungan', $status);
        }

        $data['tamus']  = $query->orderBy('buku_tamu.waktu_datang', 'DESC')->findAll();
        $data['status'] = $status;

        return view('pegawai_portal/tamu', $data);
    }

    public function detail($id)
    {
        $bukuTamuModel = new BukuTamuModel();

        $tamu = $bukuTamuModel->select('buku_tamu.*, opd.nama_opd, bagian.nama_bagian, subbagian.nama_subbagian, agenda.nama_agenda')
                               ->join('opd', 'opd.kode_opd = buku_tamu.kode_opd')
                               ->join('bagian', 'bagian.kode_opd = buku_tamu.kode_opd AND bagian.kode_bagian = buku_tamu.kode_bagian')
                               ->join('subbagian', 'subbagian.kode_opd = buku_tamu.kode_opd AND subbagian.kode_bagian = buku_tamu.kode_bagian AND subbagian.kode_subbagian = buku_tamu.kode_subbagian', 'left')
                               ->join('agenda', 'agenda.id_agenda = buku_tamu.id_agenda', 'left')
                               ->find($id);

        if (!$tamu) {
            return redirect()->to('pegawai-portal/tamu')->with('error', 'Data tamu tidak ditemukan.');
        }

        // Access check: only tamu for this pegawai
        if ($tamu['id_pegawai_tujuan'] != $this->pegawaiId) {
            return redirect()->to('pegawai-portal/tamu')->with('error', 'Anda tidak memiliki akses ke data tamu ini.');
        }

        $data['tamu'] = $tamu;

        return view('pegawai_portal/detail', $data);
    }

    public function konfirmasiTamu($id)
    {
        $bukuTamuModel = new BukuTamuModel();
        $tamu = $bukuTamuModel->find($id);

        if (!$tamu) {
            return redirect()->to('pegawai-portal/tamu')->with('error', 'Data tamu tidak ditemukan.');
        }

        // Access check
        if ($tamu['id_pegawai_tujuan'] != $this->pegawaiId) {
            return redirect()->to('pegawai-portal/tamu')->with('error', 'Anda tidak memiliki akses.');
        }

        if ($tamu['status_kunjungan'] !== 'menunggu') {
            return redirect()->to('pegawai-portal/tamu')->with('error', 'Tamu ini sudah bukan berstatus menunggu.');
        }

        $bukuTamuModel->update($id, ['status_kunjungan' => 'berlangsung']);
        log_activity("Pegawai mengkonfirmasi Tamu #{$tamu['no_referensi']} ({$tamu['nama_tamu']})", 'buku_tamu', $id);

        return redirect()->to('pegawai-portal/tamu')->with('success', 'Tamu berhasil dikonfirmasi! Status: Berlangsung.');
    }

    public function updateStatus($id)
    {
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
