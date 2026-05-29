<?php

namespace App\Controllers;

use App\Models\BukuTamuModel;
use App\Models\AgendaModel;
use App\Models\PegawaiModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $user = auth()->user();
        $isSuperadmin = $user->inGroup('superadmin');
        
        $bukuTamuModel = new BukuTamuModel();
        $agendaModel = new AgendaModel();

        // Base builders for filtering
        $tamuBuilder = $bukuTamuModel->builder();
        $agendaBuilder = $agendaModel->builder();

        // Determine filters based on role
        if (!$isSuperadmin) {
            $kodeOpd = $user->kode_opd;
            $kodeBagian = $user->kode_bagian;

            $tamuBuilder->where('buku_tamu.kode_opd', $kodeOpd);
            $agendaBuilder->where('agenda.kode_opd', $kodeOpd);

            if ($kodeBagian) {
                $tamuBuilder->where('buku_tamu.kode_bagian', $kodeBagian);
                $agendaBuilder->where('agenda.kode_bagian', $kodeBagian);
            }
        }

        // 1. Stats - Total Today
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd = date('Y-m-d 23:59:59');
        
        $todayQuery = clone $tamuBuilder;
        $totalToday = $todayQuery->where('waktu_datang >=', $todayStart)
                                 ->where('waktu_datang <=', $todayEnd)
                                 ->countAllResults(false);

        // 2. Stats - Total Month
        $monthStart = date('Y-m-01 00:00:00');
        $monthEnd = date('Y-m-t 23:59:59');
        
        $monthQuery = clone $tamuBuilder;
        $totalMonth = $monthQuery->where('waktu_datang >=', $monthStart)
                                 ->where('waktu_datang <=', $monthEnd)
                                 ->countAllResults(false);

        // 3. Stats - Active Agendas
        $agendaQuery = clone $agendaBuilder;
        $totalAgendas = $agendaQuery->where('status', 'aktif')->countAllResults();

        // 4. Stats - Pending (menunggu) Guests
        $pendingQuery = clone $tamuBuilder;
        $totalPending = $pendingQuery->where('status_kunjungan', 'menunggu')->countAllResults(false);

        // 5. Recent Guests list
        $recentQuery = clone $tamuBuilder;
        $recentGuests = $recentQuery->select('buku_tamu.*, pegawai.nama as nama_pegawai, opd.nama_opd, bagian.nama_bagian')
                                    ->join('pegawai', 'pegawai.id = buku_tamu.id_pegawai_tujuan', 'left')
                                    ->join('opd', 'opd.kode_opd = buku_tamu.kode_opd')
                                    ->join('bagian', 'bagian.kode_opd = buku_tamu.kode_opd AND bagian.kode_bagian = buku_tamu.kode_bagian')
                                    ->orderBy('buku_tamu.waktu_datang', 'DESC')
                                    ->limit(5)
                                    ->get()
                                    ->getResultArray();

        // 6. Chart Trend (Visitors last 7 days)
        $trendLabels = [];
        $trendData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $trendLabels[] = date('d M', strtotime($date));
            
            $dayQuery = clone $tamuBuilder;
            $count = $dayQuery->where('waktu_datang >=', $date . ' 00:00:00')
                              ->where('waktu_datang <=', $date . ' 23:59:59')
                              ->countAllResults(false);
            $trendData[] = $count;
        }

        // 7. Superadmin-only Chart: Most visited OPDs
        $opdLabels = [];
        $opdData = [];
        if ($isSuperadmin) {
            $opdQuery = $bukuTamuModel->builder()
                                      ->select('opd.nama_opd, COUNT(buku_tamu.id) as total')
                                      ->join('opd', 'opd.kode_opd = buku_tamu.kode_opd')
                                      ->groupBy('buku_tamu.kode_opd')
                                      ->orderBy('total', 'DESC')
                                      ->limit(5)
                                      ->get()
                                      ->getResultArray();
            foreach ($opdQuery as $row) {
                $opdLabels[] = $row['nama_opd'];
                $opdData[] = $row['total'];
            }
        }

        $db = \Config\Database::connect();
        $lastSync = $db->table('settings')->where('class', 'Simpelgan')->where('key', 'last_sync')->get()->getRowArray();
        $lastSyncTime = $lastSync ? $lastSync['value'] : 'Belum pernah';

        return view('dashboard/index', [
            'totalToday'   => $totalToday,
            'totalMonth'   => $totalMonth,
            'totalAgendas' => $totalAgendas,
            'totalPending' => $totalPending,
            'recentGuests' => $recentGuests,
            'trendLabels'  => $trendLabels,
            'trendData'    => $trendData,
            'opdLabels'    => $isSuperadmin ? $opdLabels : [],
            'opdData'      => $isSuperadmin ? $opdData : [],
            'isSuperadmin' => $isSuperadmin,
            'lastSyncTime' => $lastSyncTime,
        ]);
    }

    public function changePassword()
    {
        return view('dashboard/change_password');
    }

    public function saveChangePassword()
    {
        $rules = [
            'old_password'     => 'required',
            'new_password'     => 'required|min_length[8]',
            'confirm_password' => 'required|matches[new_password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode('<br>', $this->validator->getErrors()));
        }

        $user = auth()->user();
        
        // Verify old password using Shield check
        $credentials = [
            'email'    => $user->email,
            'password' => $this->request->getPost('old_password'),
        ];

        if (!auth()->check($credentials)->isOK()) {
            return redirect()->back()->withInput()->with('error', 'Password lama salah.');
        }

        // Fill and save new password
        $user->fill([
            'password' => $this->request->getPost('new_password'),
        ]);

        $userModel = new \App\Models\UserModel();
        $userModel->save($user);

        log_activity('Mengubah password sendiri', 'users', $user->id);

        return redirect()->to('dashboard')->with('success', 'Password berhasil diubah!');
    }

    public function syncSimpelgan()
    {
        $user = auth()->user();
        if (!$user->inGroup('superadmin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $result = \App\Helpers\SimpelganSyncHelper::syncAll();
        
        if ($result['status'] === 'success') {
            log_activity('Melakukan sinkronisasi data dari Simpelgan', 'settings', $user->id);
            return redirect()->to('dashboard')->with('success', $result['message']);
        } else {
            return redirect()->to('dashboard')->with('error', $result['message']);
        }
    }
}
