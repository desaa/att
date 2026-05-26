<?php

namespace App\Controllers;

use App\Models\BukuTamuModel;
use App\Models\AgendaModel;
use App\Models\PegawaiModel;
use App\Models\OpdModel;
use App\Models\BagianModel;
use App\Models\SubbagianModel;

class Tamu extends BaseController
{
    // ==========================================
    // ADMIN FUNCTIONS
    // ==========================================

    public function index()
    {
        $user = auth()->user();
        $isSuperadmin = $user->inGroup('superadmin');
        
        $bukuTamuModel = new BukuTamuModel();
        $pegawaiModel = new PegawaiModel();

        // Filter parameters
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $status = $this->request->getGet('status');
        $pegawaiId = $this->request->getGet('pegawai_id');

        $query = $bukuTamuModel->select('buku_tamu.*, pegawai.nama as nama_pegawai, opd.nama_opd, bagian.nama_bagian, subbagian.nama_subbagian, agenda.nama_agenda')
                              ->join('pegawai', 'pegawai.id = buku_tamu.id_pegawai_tujuan')
                              ->join('opd', 'opd.kode_opd = buku_tamu.kode_opd')
                              ->join('bagian', 'bagian.kode_opd = buku_tamu.kode_opd AND bagian.kode_bagian = buku_tamu.kode_bagian')
                              ->join('subbagian', 'subbagian.kode_opd = buku_tamu.kode_opd AND subbagian.kode_bagian = buku_tamu.kode_bagian AND subbagian.kode_subbagian = buku_tamu.kode_subbagian', 'left')
                              ->join('agenda', 'agenda.id_agenda = buku_tamu.id_agenda', 'left');

        // Scope by department if Admin
        if (!$isSuperadmin) {
            $query->where('buku_tamu.kode_opd', $user->kode_opd);
        }

        // Apply filters
        if ($startDate) {
            $query->where('buku_tamu.waktu_datang >=', $startDate . ' 00:00:00');
        }
        if ($endDate) {
            $query->where('buku_tamu.waktu_datang <=', $endDate . ' 23:59:59');
        }
        if ($status) {
            $query->where('buku_tamu.status_kunjungan', $status);
        }
        if ($pegawaiId) {
            $query->where('buku_tamu.id_pegawai_tujuan', $pegawaiId);
        }

        $data['tamus'] = $query->orderBy('buku_tamu.waktu_datang', 'DESC')->findAll();
        
        // Fetch target employees for filter dropdown
        $pegBuilder = $pegawaiModel->where('status', 'aktif');
        if (!$isSuperadmin) {
            $pegBuilder->where('kode_opd', $user->kode_opd);
        }
        $data['pegawais'] = $pegBuilder->orderBy('nama', 'ASC')->findAll();

        $data['isSuperadmin'] = $isSuperadmin;
        $data['filters'] = [
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'status'     => $status,
            'pegawai_id' => $pegawaiId,
        ];

        return view('tamu/index', $data);
    }

    public function detail($id)
    {
        $user = auth()->user();
        $isSuperadmin = $user->inGroup('superadmin');
        
        $bukuTamuModel = new BukuTamuModel();
        
        $tamu = $bukuTamuModel->select('buku_tamu.*, pegawai.nama as nama_pegawai, pegawai.jabatan, opd.nama_opd, bagian.nama_bagian, subbagian.nama_subbagian, agenda.nama_agenda')
                              ->join('pegawai', 'pegawai.id = buku_tamu.id_pegawai_tujuan')
                              ->join('opd', 'opd.kode_opd = buku_tamu.kode_opd')
                              ->join('bagian', 'bagian.kode_opd = buku_tamu.kode_opd AND bagian.kode_bagian = buku_tamu.kode_bagian')
                              ->join('subbagian', 'subbagian.kode_opd = buku_tamu.kode_opd AND subbagian.kode_bagian = buku_tamu.kode_bagian AND subbagian.kode_subbagian = buku_tamu.kode_subbagian', 'left')
                              ->join('agenda', 'agenda.id_agenda = buku_tamu.id_agenda', 'left')
                              ->find($id);

        if (!$tamu) {
            return redirect()->to('tamu')->with('error', 'Data tamu tidak ditemukan.');
        }

        // Access check
        if (!$isSuperadmin && $tamu['kode_opd'] !== $user->kode_opd) {
            return redirect()->to('tamu')->with('error', 'Anda tidak memiliki hak untuk melihat data tamu ini.');
        }

        $data['tamu'] = $tamu;
        $data['isSuperadmin'] = $isSuperadmin;

        return view('tamu/detail', $data);
    }

    public function updateStatus($id)
    {
        $user = auth()->user();
        $isSuperadmin = $user->inGroup('superadmin');
        
        $bukuTamuModel = new BukuTamuModel();
        $tamu = $bukuTamuModel->find($id);

        if (!$tamu) {
            return redirect()->to('tamu')->with('error', 'Data tamu tidak ditemukan.');
        }

        // Access check
        if (!$isSuperadmin && $tamu['kode_opd'] !== $user->kode_opd) {
            return redirect()->to('tamu')->with('error', 'Anda tidak memiliki hak untuk memperbarui tamu ini.');
        }

        $newStatus = $this->request->getPost('status_kunjungan');
        
        if (!in_array($newStatus, ['menunggu', 'berlangsung', 'selesai', 'batal'])) {
            return redirect()->back()->with('error', 'Status kunjungan tidak valid.');
        }

        $updateData = [
            'status_kunjungan' => $newStatus
        ];

        // If status completed, set check-out time
        if ($newStatus === 'selesai') {
            $updateData['waktu_pulang'] = date('Y-m-d H:i:s');
        }

        $bukuTamuModel->update($id, $updateData);
        log_activity("Memperbarui status kunjungan Tamu #{$tamu['no_referensi']} ({$tamu['nama_tamu']}) menjadi: $newStatus", 'buku_tamu', $id);

        return redirect()->to('tamu/detail/' . $id)->with('success', 'Status kunjungan berhasil diperbarui!');
    }

    public function inputManual()
    {
        $user = auth()->user();
        $isSuperadmin = $user->inGroup('superadmin');
        
        // Manual input is only for Unit Admins
        if ($isSuperadmin) {
            return redirect()->to('tamu')->with('error', 'Superadmin tidak dapat mengisi buku tamu secara manual. Silakan gunakan akun Admin Unit.');
        }

        $pegawaiModel = new PegawaiModel();
        $data['pegawais'] = $pegawaiModel->where('kode_opd', $user->kode_opd)
                                        ->where('status', 'aktif')
                                        ->orderBy('nama', 'ASC')
                                        ->findAll();

        // Fetch active agendas for this OPD
        $agendaModel = new AgendaModel();
        $data['agendas'] = $agendaModel->where('kode_opd', $user->kode_opd)
                                       ->where('status', 'aktif')
                                       ->orderBy('nama_agenda', 'ASC')
                                       ->findAll();

        return view('tamu/input_manual', $data);
    }

    public function storeManual()
    {
        $user = auth()->user();
        $isSuperadmin = $user->inGroup('superadmin');
        
        if ($isSuperadmin) {
            return redirect()->to('tamu')->with('error', 'Aksi tidak diizinkan.');
        }

        $rules = [
            'nama_tamu'         => 'required|max_length[255]',
            'nik'               => 'required|numeric|min_length[16]|max_length[18]',
            'instansi'          => 'required|max_length[255]',
            'no_hp'             => 'required|max_length[50]',
            'alamat'            => 'required',
            'keperluan'         => 'required',
            'id_pegawai_tujuan' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode('<br>', $this->validator->getErrors()));
        }

        // Generate uploads directory
        $uploadPath = FCPATH . 'uploads/tamu/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // Generate reference number
        $noReferensi = 'REG-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

        // Handle signature
        $sigFile = null;
        $sigData = $this->request->getPost('tanda_tangan');
        if ($sigData && strpos($sigData, 'data:image') === 0) {
            list($type, $data) = explode(';', $sigData);
            list(, $data)      = explode(',', $data);
            $decodedData       = base64_decode($data);
            
            $sigFile = 'sig_' . uniqid() . '.png';
            file_put_contents($uploadPath . $sigFile, $decodedData);
        }

        // Handle selfie/photo
        $photoFile = null;
        $photoData = $this->request->getPost('foto_tamu');
        if ($photoData && strpos($photoData, 'data:image') === 0) {
            list($type, $data) = explode(';', $photoData);
            list(, $data)      = explode(',', $data);
            $decodedData       = base64_decode($data);
            
            $photoFile = 'photo_' . uniqid() . '.png';
            file_put_contents($uploadPath . $photoFile, $decodedData);
        }

        // Handle document upload
        $docFile = null;
        $document = $this->request->getFile('dokumen_pendukung');
        if ($document && $document->isValid() && !$document->hasMoved()) {
            $docFile = $document->getRandomName();
            $document->move($uploadPath, $docFile);
        }

        // Fetch visited employee details to store their exact department/unit
        $pegawaiModel = new PegawaiModel();
        $pegawai = $pegawaiModel->find($this->request->getPost('id_pegawai_tujuan'));

        $idAgenda = $this->request->getPost('id_agenda');
        $idAgenda = empty($idAgenda) ? null : $idAgenda;

        $bukuTamuModel = new BukuTamuModel();
        $data = [
            'id_agenda'         => $idAgenda,
            'nama_tamu'         => $this->request->getPost('nama_tamu'),
            'nik'               => $this->request->getPost('nik'),
            'instansi'          => $this->request->getPost('instansi'),
            'no_hp'             => $this->request->getPost('no_hp'),
            'alamat'            => $this->request->getPost('alamat'),
            'keperluan'         => $this->request->getPost('keperluan'),
            'id_pegawai_tujuan' => $this->request->getPost('id_pegawai_tujuan'),
            'kode_opd'         => $pegawai['kode_opd'],
            'kode_bagian'      => $pegawai['kode_bagian'],
            'kode_subbagian'   => $pegawai['kode_subbagian'] ?: null,
            'waktu_datang'      => date('Y-m-d H:i:s'),
            'foto'              => $photoFile,
            'tanda_tangan'      => $sigFile,
            'dokumen_pendukung' => $docFile,
            'no_referensi'      => $noReferensi,
            'status_kunjungan'  => 'berlangsung', // Manual entry starts in-progress
            'created_by'        => $user->id,
        ];

        $bukuTamuModel->insert($data);
        $insertId = $bukuTamuModel->getInsertID();
        
        log_activity("Mencatat Kunjungan Tamu secara Manual: {$data['nama_tamu']} (#{$noReferensi})", 'buku_tamu', $insertId);

        return redirect()->to('tamu/detail/' . $insertId)->with('success', 'Kunjungan tamu berhasil dicatat secara manual!');
    }

    // ==========================================
    // PUBLIC SELF-SERVICE FUNCTIONS (QR CODE)
    // ==========================================

    public function selfService($token)
    {
        $agendaModel = new AgendaModel();
        $agenda = $agendaModel->select('agenda.*, opd.nama_opd, bagian.nama_bagian')
                              ->join('opd', 'opd.kode_opd = agenda.kode_opd')
                              ->join('bagian', 'bagian.kode_opd = agenda.kode_opd AND bagian.kode_bagian = agenda.kode_bagian')
                              ->where('qr_code', $token)
                              ->first();

        // 1. Verify agenda exists
        if (!$agenda) {
            return view('tamu/public_error', ['message' => 'Agenda tidak ditemukan atau tautan tidak valid.']);
        }

        // 2. Verify status is active
        if ($agenda['status'] !== 'aktif') {
            return view('tamu/public_error', ['message' => 'Agenda ini saat ini sedang tidak aktif.']);
        }

        // 3. Verify date range
        $now = date('Y-m-d H:i:s');
        if ($now < $agenda['tanggal_mulai']) {
            return view('tamu/public_error', ['message' => 'Agenda ini belum dimulai. Masa aktif dimulai pada: ' . date('d F Y, H:i', strtotime($agenda['tanggal_mulai']))]);
        }
        if ($now > $agenda['tanggal_selesai']) {
            return view('tamu/public_error', ['message' => 'Agenda ini telah berakhir pada: ' . date('d F Y, H:i', strtotime($agenda['tanggal_selesai']))]);
        }

        // Fetch list of active employees for this department as targets
        $pegawaiModel = new PegawaiModel();
        $pegawais = $pegawaiModel->where('kode_opd', $agenda['kode_opd'])
                                 ->where('kode_bagian', $agenda['kode_bagian'])
                                 ->where('status', 'aktif')
                                 ->orderBy('nama', 'ASC')
                                 ->findAll();

        return view('tamu/self_service', [
            'agenda'   => $agenda,
            'pegawais' => $pegawais,
            'token'    => $token
        ]);
    }

    public function storeSelfService($token)
    {
        $agendaModel = new AgendaModel();
        $agenda = $agendaModel->where('qr_code', $token)->first();

        if (!$agenda || $agenda['status'] !== 'aktif') {
            return redirect()->back()->with('error', 'Aksi tidak diizinkan. Agenda tidak aktif.');
        }

        $rules = [
            'nama_tamu'         => 'required|max_length[255]',
            'nik'               => 'required|numeric|min_length[16]|max_length[18]',
            'instansi'          => 'required|max_length[255]',
            'no_hp'             => 'required|max_length[50]',
            'alamat'            => 'required',
            'keperluan'         => 'required',
            'id_pegawai_tujuan' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode('<br>', $this->validator->getErrors()));
        }

        // Generate uploads directory
        $uploadPath = FCPATH . 'uploads/tamu/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // Generate reference number
        $noReferensi = 'REG-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

        // Handle signature (base64 PNG)
        $sigFile = null;
        $sigData = $this->request->getPost('tanda_tangan');
        if ($sigData && strpos($sigData, 'data:image') === 0) {
            list($type, $data) = explode(';', $sigData);
            list(, $data)      = explode(',', $data);
            $decodedData       = base64_decode($data);
            
            $sigFile = 'sig_' . uniqid() . '.png';
            file_put_contents($uploadPath . $sigFile, $decodedData);
        }

        // Handle photo (base64 PNG)
        $photoFile = null;
        $photoData = $this->request->getPost('foto_tamu');
        if ($photoData && strpos($photoData, 'data:image') === 0) {
            list($type, $data) = explode(';', $photoData);
            list(, $data)      = explode(',', $data);
            $decodedData       = base64_decode($data);
            
            $photoFile = 'photo_' . uniqid() . '.png';
            file_put_contents($uploadPath . $photoFile, $decodedData);
        }

        // Handle document upload
        $docFile = null;
        $document = $this->request->getFile('dokumen_pendukung');
        if ($document && $document->isValid() && !$document->hasMoved()) {
            $docFile = $document->getRandomName();
            $document->move($uploadPath, $docFile);
        }

        $bukuTamuModel = new BukuTamuModel();
        
        $data = [
            'id_agenda'         => $agenda['id_agenda'],
            'nama_tamu'         => $this->request->getPost('nama_tamu'),
            'nik'               => $this->request->getPost('nik'),
            'instansi'          => $this->request->getPost('instansi'),
            'no_hp'             => $this->request->getPost('no_hp'),
            'alamat'            => $this->request->getPost('alamat'),
            'keperluan'         => $this->request->getPost('keperluan'),
            'id_pegawai_tujuan' => $this->request->getPost('id_pegawai_tujuan'),
            'kode_opd'         => $agenda['kode_opd'],
            'kode_bagian'      => $agenda['kode_bagian'],
            'kode_subbagian'   => null,
            'waktu_datang'      => date('Y-m-d H:i:s'),
            'foto'              => $photoFile,
            'tanda_tangan'      => $sigFile,
            'dokumen_pendukung' => $docFile,
            'no_referensi'      => $noReferensi,
            'status_kunjungan'  => 'menunggu', // Self service starts as "menunggu" approval
            'created_by'        => null,
        ];

        $bukuTamuModel->insert($data);
        $insertId = $bukuTamuModel->getInsertID();

        log_activity("Pendaftaran Tamu Mandiri: {$data['nama_tamu']} (#{$noReferensi})", 'buku_tamu', $insertId);

        return redirect()->to('tamu/konfirmasi/' . $noReferensi);
    }

    public function konfirmasi($noReferensi)
    {
        $bukuTamuModel = new BukuTamuModel();
        $tamu = $bukuTamuModel->select('buku_tamu.*, pegawai.nama as nama_pegawai, opd.nama_opd, bagian.nama_bagian')
                              ->join('pegawai', 'pegawai.id = buku_tamu.id_pegawai_tujuan')
                              ->join('opd', 'opd.kode_opd = buku_tamu.kode_opd')
                              ->join('bagian', 'bagian.kode_opd = buku_tamu.kode_opd AND bagian.kode_bagian = buku_tamu.kode_bagian')
                              ->where('no_referensi', $noReferensi)
                              ->first();

        if (!$tamu) {
            return view('tamu/public_error', ['message' => 'Nomor referensi pendaftaran tidak ditemukan.']);
        }

        return view('tamu/konfirmasi', ['tamu' => $tamu]);
    }
}
