<?php

namespace App\Controllers;

use App\Models\BukuTamuModel;
use App\Models\AgendaModel;
use App\Models\PegawaiModel;
use App\Models\OpdModel;
use App\Models\BagianModel;
use App\Models\SubbagianModel;
use chillerlan\QRCode\QRCode;

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
        $db = \Config\Database::connect();

        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $status = $this->request->getGet('status');
        $pegawaiId = $this->request->getGet('pegawai_id');
        $kodeOpdFilter = $this->request->getGet('kode_opd');
        $kodeBagianFilter = $this->request->getGet('kode_bagian');
        $kodeSubbagianFilter = $this->request->getGet('kode_subbagian');

        $query = $bukuTamuModel->select('buku_tamu.*, pegawai.nama as nama_pegawai, opd.nama_opd, bagian.nama_bagian, subbagian.nama_subbagian, agenda.nama_agenda')
                              ->join('pegawai', 'pegawai.id = buku_tamu.id_pegawai_tujuan', 'left')
                              ->join('opd', 'opd.kode_opd = buku_tamu.kode_opd')
                              ->join('bagian', 'bagian.kode_opd = buku_tamu.kode_opd AND bagian.kode_bagian = buku_tamu.kode_bagian', 'left')
                              ->join('subbagian', 'subbagian.kode_opd = buku_tamu.kode_opd AND subbagian.kode_bagian = buku_tamu.kode_bagian AND subbagian.kode_subbagian = buku_tamu.kode_subbagian', 'left')
                              ->join('agenda', 'agenda.id_agenda = buku_tamu.id_agenda', 'left');

        if (!$isSuperadmin) {
            $query->where('buku_tamu.kode_opd', $user->kode_opd);
            if (!empty($user->kode_bagian)) $query->where('buku_tamu.kode_bagian', $user->kode_bagian);
            if (!empty($user->kode_subbagian)) $query->where('buku_tamu.kode_subbagian', $user->kode_subbagian);
        }

        if ($startDate) $query->where('buku_tamu.waktu_datang >=', $startDate . ' 00:00:00');
        if ($endDate) $query->where('buku_tamu.waktu_datang <=', $endDate . ' 23:59:59');
        if ($status) $query->where('buku_tamu.status_kunjungan', $status);
        if ($pegawaiId) $query->where('buku_tamu.id_pegawai_tujuan', $pegawaiId);
        if ($isSuperadmin && $kodeOpdFilter) $query->where('buku_tamu.kode_opd', $kodeOpdFilter);
        if ($kodeBagianFilter) $query->where('buku_tamu.kode_bagian', $kodeBagianFilter);
        if ($kodeSubbagianFilter) $query->where('buku_tamu.kode_subbagian', $kodeSubbagianFilter);

        $data['tamus'] = $query->orderBy('buku_tamu.waktu_datang', 'DESC')->findAll();
        
        $pegBuilder = $pegawaiModel->where('status', 'aktif');
        if (!$isSuperadmin) {
            $pegBuilder->where('kode_opd', $user->kode_opd);
            if (!empty($user->kode_bagian)) $pegBuilder->where('kode_bagian', $user->kode_bagian);
            if (!empty($user->kode_subbagian)) $pegBuilder->where('kode_subbagian', $user->kode_subbagian);
        }
        if ($isSuperadmin && $kodeOpdFilter) $pegBuilder->where('kode_opd', $kodeOpdFilter);
        if ($kodeBagianFilter) $pegBuilder->where('kode_bagian', $kodeBagianFilter);
        if ($kodeSubbagianFilter) $pegBuilder->where('kode_subbagian', $kodeSubbagianFilter);
        $data['pegawais'] = $pegBuilder->orderBy('nama', 'ASC')->findAll();

        $data['isSuperadmin'] = $isSuperadmin;
        $data['userKodeOpd'] = !$isSuperadmin ? $user->kode_opd : null;
        if ($isSuperadmin) {
            $data['opds'] = $db->table('opd')->orderBy('nama_opd', 'ASC')->get()->getResultArray();
        }
        $data['filters'] = [
            'start_date'     => $startDate,
            'end_date'       => $endDate,
            'status'         => $status,
            'pegawai_id'     => $pegawaiId,
            'kode_opd'       => $kodeOpdFilter,
            'kode_bagian'    => $kodeBagianFilter,
            'kode_subbagian' => $kodeSubbagianFilter,
        ];

        return view('tamu/index', $data);
    }

    public function detail($hash)
    {
        $id = decode_id($hash);
        if (!$id) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $user = auth()->user();
        $isSuperadmin = $user->inGroup('superadmin');
        $bukuTamuModel = new BukuTamuModel();
        
        $tamu = $bukuTamuModel->select('buku_tamu.*, pegawai.nama as nama_pegawai, pegawai.jabatan, opd.nama_opd, bagian.nama_bagian, subbagian.nama_subbagian, agenda.nama_agenda')
                              ->join('pegawai', 'pegawai.id = buku_tamu.id_pegawai_tujuan', 'left')
                              ->join('opd', 'opd.kode_opd = buku_tamu.kode_opd')
                              ->join('bagian', 'bagian.kode_opd = buku_tamu.kode_opd AND bagian.kode_bagian = buku_tamu.kode_bagian', 'left')
                              ->join('subbagian', 'subbagian.kode_opd = buku_tamu.kode_opd AND subbagian.kode_bagian = buku_tamu.kode_bagian AND subbagian.kode_subbagian = buku_tamu.kode_subbagian', 'left')
                              ->join('agenda', 'agenda.id_agenda = buku_tamu.id_agenda', 'left')
                              ->find($id);

        if (!$tamu) return redirect()->to('tamu')->with('error', 'Data tamu tidak ditemukan.');
        if (!$isSuperadmin && $tamu['kode_opd'] !== $user->kode_opd) {
            return redirect()->to('tamu')->with('error', 'Anda tidak memiliki hak untuk melihat data tamu ini.');
        }

        $data['tamu'] = $tamu;
        $data['isSuperadmin'] = $isSuperadmin;
        return view('tamu/detail', $data);
    }

    public function updateStatus($hash)
    {
        $id = decode_id($hash);
        if (!$id) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        $user = auth()->user();
        $isSuperadmin = $user->inGroup('superadmin');
        $bukuTamuModel = new BukuTamuModel();
        $tamu = $bukuTamuModel->find($id);

        if (!$tamu) return redirect()->to('tamu')->with('error', 'Data tamu tidak ditemukan.');
        if (!$isSuperadmin && $tamu['kode_opd'] !== $user->kode_opd) {
            return redirect()->to('tamu')->with('error', 'Anda tidak memiliki hak untuk memperbarui tamu ini.');
        }

        $newStatus = $this->request->getPost('status_kunjungan');
        if (!in_array($newStatus, ['menunggu', 'berlangsung', 'selesai', 'batal'])) {
            return redirect()->back()->with('error', 'Status kunjungan tidak valid.');
        }

        $updateData = ['status_kunjungan' => $newStatus];
        if ($newStatus === 'selesai') $updateData['waktu_pulang'] = date('Y-m-d H:i:s');

        $bukuTamuModel->update($id, $updateData);
        log_activity("Memperbarui status kunjungan Tamu #{$tamu['no_referensi']} ({$tamu['nama_tamu']}) menjadi: $newStatus", 'buku_tamu', $id);

        return redirect()->to('tamu/detail/' . $hash)->with('success', 'Status kunjungan berhasil diperbarui!');
    }

    public function inputManual()
    {
        $user = auth()->user();
        $isSuperadmin = $user->inGroup('superadmin');
        if ($isSuperadmin) {
            return redirect()->to('tamu')->with('error', 'Superadmin tidak dapat mengisi buku tamu secara manual.');
        }

        $pegawaiModel = new PegawaiModel();
        $pegBuilder = $pegawaiModel->where('kode_opd', $user->kode_opd)->where('status', 'aktif');
        if (!empty($user->kode_bagian)) $pegBuilder->where('kode_bagian', $user->kode_bagian);
        if (!empty($user->kode_subbagian)) $pegBuilder->where('kode_subbagian', $user->kode_subbagian);
        $data['pegawais'] = $pegBuilder->orderBy('nama', 'ASC')->findAll();

        $agendaModel = new AgendaModel();
        $agendaBuilder = $agendaModel->where('kode_opd', $user->kode_opd)->where('status', 'aktif');
        if (!empty($user->kode_bagian)) $agendaBuilder->where('kode_bagian', $user->kode_bagian);
        if (!empty($user->kode_subbagian)) $agendaBuilder->where('kode_subbagian', $user->kode_subbagian);
        $data['agendas'] = $agendaBuilder->orderBy('nama_agenda', 'ASC')->findAll();

        return view('tamu/input_manual', $data);
    }

    public function storeManual()
    {
        $user = auth()->user();
        if ($user->inGroup('superadmin')) {
            return redirect()->to('tamu')->with('error', 'Aksi tidak diizinkan.');
        }

        $rules = [
            'nama_tamu'         => 'required|max_length[255]',
            'nik'               => 'required|numeric|min_length[16]|max_length[18]',
            'instansi'          => 'required|max_length[255]',
            'no_hp'             => 'required|max_length[50]',
            'alamat'            => 'required',
            'keperluan'         => 'required',
            'dokumen_pendukung' => 'max_size[dokumen_pendukung,1024]|ext_in[dokumen_pendukung,pdf,jpg,jpeg,png,docx]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode('<br>', $this->validator->getErrors()));
        }

        $sigUploadPath   = getUploadPath('ttd');
        $photoUploadPath = getUploadPath('foto');
        $docUploadPath   = getUploadPath('file');
        $noReferensi     = 'REG-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

        // Decode Sucuri WAF bypass
        $ttdRaw   = $this->decodeWafBypass($this->request->getPost('tanda_tangan'));
        $fotoRaw  = $this->decodeWafBypass($this->request->getPost('foto_tamu'));

        $sigFile   = $this->saveCompressedImage($ttdRaw, $sigUploadPath, 'sig');
        $photoFile = $this->saveCompressedImage($fotoRaw, $photoUploadPath, 'photo');

        $docFile  = null;
        $document = $this->request->getFile('dokumen_pendukung');
        if ($document && $document->isValid() && !$document->hasMoved()) {
            $docFile = $document->getRandomName();
            $document->move($docUploadPath, $docFile);
        }

        $pegawaiModel    = new PegawaiModel();
        $idPegawaiTujuan = $this->request->getPost('id_pegawai_tujuan') ?: null;
        $pegawai         = $idPegawaiTujuan ? $pegawaiModel->find($idPegawaiTujuan) : null;
        $idAgenda        = $this->request->getPost('id_agenda') ?: null;

        $bukuTamuModel = new BukuTamuModel();
        $data = [
            'id_agenda'         => $idAgenda,
            'nama_tamu'         => $this->request->getPost('nama_tamu'),
            'nik'               => $this->request->getPost('nik'),
            'instansi'          => $this->request->getPost('instansi'),
            'no_hp'             => $this->request->getPost('no_hp'),
            'alamat'            => $this->request->getPost('alamat'),
            'keperluan'         => $this->request->getPost('keperluan'),
            'id_pegawai_tujuan' => $idPegawaiTujuan,
            'kode_opd'          => $pegawai ? $pegawai['kode_opd'] : $user->kode_opd,
            'kode_bagian'       => $pegawai ? $pegawai['kode_bagian'] : null,
            'kode_subbagian'    => ($pegawai && $pegawai['kode_subbagian']) ? $pegawai['kode_subbagian'] : null,
            'waktu_datang'      => date('Y-m-d H:i:s'),
            'foto'              => $photoFile,
            'tanda_tangan'      => $sigFile,
            'dokumen_pendukung' => $docFile,
            'no_referensi'      => $noReferensi,
            'status_kunjungan'  => empty($idAgenda) ? 'menunggu' : 'berlangsung',
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
        $agenda = $agendaModel->select('agenda.*, opd.nama_opd, bagian.nama_bagian, subbagian.nama_subbagian')
                              ->join('opd', 'opd.kode_opd = agenda.kode_opd')
                              ->join('bagian', 'bagian.kode_opd = agenda.kode_opd AND bagian.kode_bagian = agenda.kode_bagian', 'left')
                              ->join('subbagian', 'subbagian.kode_opd = agenda.kode_opd AND subbagian.kode_bagian = agenda.kode_bagian AND subbagian.kode_subbagian = agenda.kode_subbagian', 'left')
                              ->where('qr_code', $token)
                              ->first();

        if (!$agenda) return view('tamu/public_error', ['message' => 'Agenda tidak ditemukan atau tautan tidak valid.']);
        if ($agenda['status'] !== 'aktif') return view('tamu/public_error', ['message' => 'Agenda ini saat ini sedang tidak aktif.']);

        $now = date('Y-m-d H:i:s');
        $registrationStartTime = date('Y-m-d H:i:s', strtotime($agenda['tanggal_mulai'] . ' -2 hours'));
        if ($now < $registrationStartTime) {
            return view('tamu/public_error', ['message' => 'Pendaftaran belum dibuka. Dibuka pada: ' . date('d F Y, H:i', strtotime($registrationStartTime))]);
        }
        if ($now > $agenda['tanggal_selesai']) {
            return view('tamu/public_error', ['message' => 'Agenda ini telah berakhir pada: ' . date('d F Y, H:i', strtotime($agenda['tanggal_selesai']))]);
        }

        return view('tamu/self_service', ['agenda' => $agenda, 'token' => $token]);
    }

    public function storeSelfService($token)
    {
        $agendaModel = new AgendaModel();
        $agenda = $agendaModel->where('qr_code', $token)->first();

        if (!$agenda || $agenda['status'] !== 'aktif') {
            return redirect()->back()->with('error', 'Aksi tidak diizinkan. Agenda tidak aktif.');
        }

        $now = date('Y-m-d H:i:s');
        $registrationStartTime = date('Y-m-d H:i:s', strtotime($agenda['tanggal_mulai'] . ' -2 hours'));
        if ($now < $registrationStartTime || $now > $agenda['tanggal_selesai']) {
            return redirect()->back()->with('error', 'Waktu pendaftaran telah ditutup.');
        }

        $rules = [
            'nama_tamu' => 'required|max_length[255]',
            'nik'       => 'required|numeric|min_length[16]|max_length[18]',
            'instansi'  => 'required|max_length[255]',
            'no_hp'     => 'required|max_length[50]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode('<br>', $this->validator->getErrors()));
        }

        $sigUploadPath = getUploadPath('ttd');
        $noReferensi   = 'REG-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

        // Decode Sucuri bypass
        $ttdRaw  = $this->decodeWafBypass($this->request->getPost('tanda_tangan'));
        $sigFile = $this->saveCompressedImage($ttdRaw, $sigUploadPath, 'sig');

        $pegawaiInstansi = $this->getPegawaiInstansiByNip($this->request->getPost('nik'));
        $finalInstansi   = '';
        if ($pegawaiInstansi) {
            $parts = array_filter([$pegawaiInstansi['instansi'] ?? null, $pegawaiInstansi['bidang'] ?? null, $pegawaiInstansi['subbidang'] ?? null]);
            $finalInstansi = implode(' - ', $parts);
        } else {
            $finalInstansi = $this->request->getPost('instansi');
        }

        $bukuTamuModel = new BukuTamuModel();
        $data = [
            'id_agenda'         => $agenda['id_agenda'],
            'nama_tamu'         => $this->request->getPost('nama_tamu'),
            'nik'               => $this->request->getPost('nik'),
            'instansi'          => $finalInstansi,
            'no_hp'             => $this->request->getPost('no_hp'),
            'alamat'            => '-',
            'keperluan'         => null,
            'id_pegawai_tujuan' => null,
            'kode_opd'          => $agenda['kode_opd'],
            'kode_bagian'       => $agenda['kode_bagian'],
            'kode_subbagian'    => null,
            'waktu_datang'      => date('Y-m-d H:i:s'),
            'foto'              => null,
            'tanda_tangan'      => $sigFile,
            'dokumen_pendukung' => null,
            'no_referensi'      => $noReferensi,
            'status_kunjungan'  => 'berlangsung',
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
        $tamu = $bukuTamuModel->select('buku_tamu.*, pegawai.nama as nama_pegawai, opd.nama_opd, bagian.nama_bagian, subbagian.nama_subbagian, agenda.nama_agenda')
                              ->join('pegawai', 'pegawai.id = buku_tamu.id_pegawai_tujuan', 'left')
                              ->join('opd', 'opd.kode_opd = buku_tamu.kode_opd', 'left')
                              ->join('bagian', 'bagian.kode_opd = buku_tamu.kode_opd AND bagian.kode_bagian = buku_tamu.kode_bagian', 'left')
                              ->join('subbagian', 'subbagian.kode_opd = buku_tamu.kode_opd AND subbagian.kode_bagian = buku_tamu.kode_bagian AND subbagian.kode_subbagian = buku_tamu.kode_subbagian', 'left')
                              ->join('agenda', 'agenda.id_agenda = buku_tamu.id_agenda', 'left')
                              ->where('no_referensi', $noReferensi)
                              ->first();

        if (!$tamu) return view('tamu/public_error', ['message' => 'Nomor referensi pendaftaran tidak ditemukan.']);

        return view('tamu/konfirmasi', ['tamu' => $tamu]);
    }

    public function qrUmum()
    {
        $user = auth()->user();
        $isSuperadmin = $user->inGroup('superadmin');
        $db = \Config\Database::connect();
        
        $data['isSuperadmin'] = $isSuperadmin;
        $selectedOpd = $isSuperadmin ? $this->request->getGet('kode_opd') : $user->kode_opd;
        $selectedBagian = $this->request->getGet('kode_bagian');
        if (!$isSuperadmin && !empty($user->kode_bagian)) $selectedBagian = $user->kode_bagian;
        $selectedSubbagian = $this->request->getGet('kode_subbagian');
        if (!$isSuperadmin && !empty($user->kode_subbagian)) $selectedSubbagian = $user->kode_subbagian;
        
        $data['selected_opd']      = $selectedOpd;
        $data['selected_bagian']   = $selectedBagian;
        $data['selected_subbagian']= $selectedSubbagian;
        
        if ($isSuperadmin) {
            $data['opds'] = $db->table('opd')->orderBy('kode_opd', 'ASC')->get()->getResultArray();
        } else {
            $data['opd'] = $db->table('opd')->where('kode_opd', $user->kode_opd)->get()->getRowArray();
        }
        
        $data['bagians']    = [];
        $data['subbagians'] = [];
        
        if ($selectedOpd) {
            $data['bagians'] = $db->table('bagian')->where('kode_opd', $selectedOpd)->orderBy('nama_bagian', 'ASC')->get()->getResultArray();
            if ($selectedBagian) {
                $data['subbagians'] = $db->table('subbagian')->where('kode_opd', $selectedOpd)->where('kode_bagian', $selectedBagian)->orderBy('nama_subbagian', 'ASC')->get()->getResultArray();
            }
        }
        
        $data['qr_image'] = null;
        $data['qr_url']   = null;
        
        if ($selectedOpd) {
            $params = ['kode_opd' => $selectedOpd];
            if ($selectedBagian)    $params['kode_bagian']    = $selectedBagian;
            if ($selectedSubbagian) $params['kode_subbagian'] = $selectedSubbagian;
            
            $url = base_url('tamu/register-umum?' . http_build_query(['q' => $this->encryptQrParams($params)]));
            $data['qr_image'] = (new QRCode())->render($url);
            $data['qr_url']   = $url;
            
            $opdObj = $db->table('opd')->where('kode_opd', $selectedOpd)->get()->getRowArray();
            $data['nama_opd'] = $opdObj ? $opdObj['nama_opd'] : '';
            
            $data['nama_bagian'] = '';
            if ($selectedBagian) {
                $bagianObj = $db->table('bagian')->where('kode_opd', $selectedOpd)->where('kode_bagian', $selectedBagian)->get()->getRowArray();
                $data['nama_bagian'] = $bagianObj ? $bagianObj['nama_bagian'] : '';
            }
            
            $data['nama_subbagian'] = '';
            if ($selectedSubbagian) {
                $subbagianObj = $db->table('subbagian')->where('kode_opd', $selectedOpd)->where('kode_bagian', $selectedBagian)->where('kode_subbagian', $selectedSubbagian)->get()->getRowArray();
                $data['nama_subbagian'] = $subbagianObj ? $subbagianObj['nama_subbagian'] : '';
            }
        }
        
        return view('tamu/qr_umum', $data);
    }

    public function registerUmum($kodeOpd = null, $kodeBagian = null)
    {
        $db = \Config\Database::connect();
        $qrParams = $this->getQrParamsFromRequest();
        
        $kodeOpd      = $qrParams['kode_opd']      ?? ($this->request->getGet('kode_opd') ?: $kodeOpd);
        $kodeBagian   = $qrParams['kode_bagian']   ?? ($this->request->getGet('kode_bagian') ?: $kodeBagian);
        $kodeSubbagian= $qrParams['kode_subbagian']?? $this->request->getGet('kode_subbagian');
        
        if (!$kodeOpd) return view('tamu/public_error', ['message' => 'OPD tidak valid.']);
        
        $opd = $db->table('opd')->where('kode_opd', $kodeOpd)->get()->getRowArray();
        if (!$opd) return view('tamu/public_error', ['message' => 'OPD tidak valid.']);
        
        $bagian = null;
        if ($kodeBagian) {
            $bagian = $db->table('bagian')->where('kode_opd', $kodeOpd)->where('kode_bagian', $kodeBagian)->get()->getRowArray();
        }
        
        $subbagian = null;
        if ($kodeOpd && $kodeBagian && $kodeSubbagian) {
            $subbagian = $db->table('subbagian')->where('kode_opd', $kodeOpd)->where('kode_bagian', $kodeBagian)->where('kode_subbagian', $kodeSubbagian)->get()->getRowArray();
        }
        
        $builder = $db->table('pegawai dp')
                      ->select('dp.nip as id, dp.nama, dp.kode_opd, dp.kode_bagian, dp.kode_subbagian, dp.jabatan')
                      ->join('opd mo', 'mo.kode_opd = dp.kode_opd', 'left')
                      ->where('dp.kode_opd', $kodeOpd)
                      ->where('dp.status', 'aktif');
        if ($kodeBagian)    $builder->where('dp.kode_bagian', $kodeBagian);
        if ($kodeSubbagian) $builder->where('dp.kode_subbagian', $kodeSubbagian);
        $pegawais = $builder->orderBy('dp.nama', 'ASC')->get()->getResultArray();
                                 
        return view('tamu/register_umum', [
            'opd'            => $opd,
            'bagian'         => $bagian,
            'subbagian'      => $subbagian,
            'pegawais'       => $pegawais,
            'kode_opd'       => $kodeOpd,
            'kode_bagian'    => $kodeBagian,
            'kode_subbagian' => $kodeSubbagian,
            'qr_token'       => $this->request->getGet('q')
        ]);
    }

    public function storeRegisterUmum($kodeOpd = null, $kodeBagian = null)
    {
        $qrParams = $this->getQrParamsFromRequest();
        $q = $this->request->getGet('q') ?? '';

        $kodeOpd       = $qrParams['kode_opd']       ?? ($this->request->getGet('kode_opd') ?: $kodeOpd);
        $kodeBagian    = $qrParams['kode_bagian']    ?? ($this->request->getGet('kode_bagian') ?: $kodeBagian);
        $kodeSubbagian = $qrParams['kode_subbagian'] ?? $this->request->getGet('kode_subbagian');
        
        $db  = \Config\Database::connect();
        $opd = $db->table('opd')->where('kode_opd', $kodeOpd)->get()->getRowArray();
        
        if (!$opd) {
            return redirect()->to('tamu/register-umum?q=' . urlencode($q))->with('error', 'Aksi tidak diizinkan. OPD tidak valid.');
        }
        
        $rules = [
            'nama_tamu'         => 'required|max_length[255]',
            'nik'               => 'required|numeric|min_length[16]|max_length[18]',
            'instansi'          => 'required|max_length[255]',
            'no_hp'             => 'required|max_length[50]',
            'alamat'            => 'required',
            'keperluan'         => 'required',
            'dokumen_pendukung' => 'max_size[dokumen_pendukung,1024]|ext_in[dokumen_pendukung,pdf,jpg,jpeg,png,docx]',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->to('tamu/register-umum?q=' . urlencode($q))->with('error', implode('<br>', $this->validator->getErrors()));
        }

        $sigUploadPath   = getUploadPath('ttd');
        $photoUploadPath = getUploadPath('foto');
        $docUploadPath   = getUploadPath('file');
        $noReferensi     = 'REG-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

        // =============================================
        // DECODE SUCURI WAF BYPASS (foto & tanda tangan)
        // =============================================
        $ttdRaw   = $this->decodeWafBypass($this->request->getPost('tanda_tangan'));
        $fotoRaw  = $this->decodeWafBypass($this->request->getPost('foto_tamu'));

        $sigFile = $this->saveCompressedImage($ttdRaw, $sigUploadPath, 'sig');
        if (!$sigFile) {
            return redirect()->to('tamu/register-umum?q=' . urlencode($q))->with('error', 'Tanda tangan wajib diisi.');
        }

        $photoFile = $this->saveCompressedImage($fotoRaw, $photoUploadPath, 'photo');
        if (!$photoFile) {
            return redirect()->to('tamu/register-umum?q=' . urlencode($q))->with('error', 'Foto wajib diisi.');
        }

        $docFile  = null;
        $document = $this->request->getFile('dokumen_pendukung');
        if ($document && $document->isValid() && !$document->hasMoved()) {
            $docFile = $document->getRandomName();
            $document->move($docUploadPath, $docFile);
        }
        
        $idPegawaiTujuan = $this->request->getPost('id_pegawai_tujuan') ?: null;
        $pegawaiModel    = new PegawaiModel();
        $pegawai         = $idPegawaiTujuan ? $pegawaiModel->find($idPegawaiTujuan) : null;

        $finalBagian    = $kodeBagian    ?: ($pegawai ? $pegawai['kode_bagian'] : null);
        $finalSubbagian = $kodeSubbagian ?: (($pegawai && $pegawai['kode_subbagian']) ? $pegawai['kode_subbagian'] : null);

        $pegawaiInstansi = $this->getPegawaiInstansiByNip($this->request->getPost('nik'));
        $finalInstansi   = '';
        if ($pegawaiInstansi) {
            $parts = array_filter([$pegawaiInstansi['instansi'] ?? null, $pegawaiInstansi['bidang'] ?? null, $pegawaiInstansi['subbidang'] ?? null]);
            $finalInstansi = implode(' - ', $parts);
        } else {
            $parts = array_filter([$this->request->getPost('instansi'), $this->request->getPost('bidang'), $this->request->getPost('subbidang')]);
            $finalInstansi = implode(' - ', $parts);
        }

        $bukuTamuModel = new BukuTamuModel();
        $data = [
            'id_agenda'         => null,
            'nama_tamu'         => $this->request->getPost('nama_tamu'),
            'nik'               => $this->request->getPost('nik'),
            'instansi'          => $finalInstansi,
            'no_hp'             => $this->request->getPost('no_hp'),
            'alamat'            => $this->request->getPost('alamat'),
            'keperluan'         => $this->request->getPost('keperluan'),
            'id_pegawai_tujuan' => $idPegawaiTujuan,
            'kode_opd'          => $kodeOpd,
            'kode_bagian'       => $finalBagian,
            'kode_subbagian'    => $finalSubbagian,
            'waktu_datang'      => date('Y-m-d H:i:s'),
            'foto'              => $photoFile,
            'tanda_tangan'      => $sigFile,
            'dokumen_pendukung' => $docFile,
            'no_referensi'      => $noReferensi,
            'status_kunjungan'  => 'menunggu',
            'created_by'        => null,
        ];
        
        $bukuTamuModel->insert($data);
        $insertId = $bukuTamuModel->getInsertID();
        log_activity("Pendaftaran Tamu Umum Mandiri: {$data['nama_tamu']} (#{$noReferensi})", 'buku_tamu', $insertId);
        
        return redirect()->to('tamu/konfirmasi/' . $noReferensi);
    }

    // ==========================================
    // PRIVATE HELPERS
    // ==========================================

    /**
     * Decode data yang di-encode di frontend untuk bypass Sucuri WAF.
     * Jika sudah berupa data:image langsung, kembalikan apa adanya.
     */
    private function decodeWafBypass(?string $encoded): ?string
{
    if (!$encoded) return null;

    // Sudah data:image lengkap — tidak perlu decode
    if (str_starts_with($encoded, 'data:image')) return $encoded;

    // Format baru: IMG:base64data (strip prefix, reconstruct data URI)
    if (str_starts_with($encoded, 'IMG:')) {
        $base64 = substr($encoded, 4);
        return 'data:image/png;base64,' . $base64;
    }

    // Fallback lama: url-safe base64
    $padded  = $encoded . str_repeat('=', (4 - strlen($encoded) % 4) % 4);
    $decoded = base64_decode(strtr($padded, '-_', '+/'), true);
    if ($decoded === false) return $encoded;
    return urldecode($decoded);
}

    private function encryptQrParams(array $params): string
    {
        $plainText  = json_encode(array_filter($params), JSON_UNESCAPED_SLASHES);
        $key        = $this->getQrEncryptionKey();
        $iv         = random_bytes(16);
        $cipherText = openssl_encrypt($plainText, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        $mac        = hash_hmac('sha256', $iv . $cipherText, $key, true);

        return rtrim(strtr(base64_encode($iv . $mac . $cipherText), '+/', '-_'), '=');
    }

    private function getQrParamsFromRequest(): array
    {
        $token = $this->request->getGet('q');
        if (!$token) return [];

        $base64 = strtr($token, '-_', '+/');
        $base64 .= str_repeat('=', (4 - strlen($base64) % 4) % 4);
        $raw = base64_decode($base64, true);
        if ($raw === false || strlen($raw) <= 48) return [];

        $iv         = substr($raw, 0, 16);
        $mac        = substr($raw, 16, 32);
        $cipherText = substr($raw, 48);
        $key        = $this->getQrEncryptionKey();

        if (!hash_equals($mac, hash_hmac('sha256', $iv . $cipherText, $key, true))) return [];

        $plainText = openssl_decrypt($cipherText, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        $params    = json_decode($plainText ?: '', true);

        return is_array($params) ? $params : [];
    }

    private function getQrEncryptionKey(): string
    {
        $configuredKey = (string) (config('Encryption')->key ?: env('encryption.key') ?: '');
        if (str_starts_with($configuredKey, 'hex2bin:')) {
            $configuredKey = hex2bin(substr($configuredKey, 8)) ?: '';
        } elseif (str_starts_with($configuredKey, 'base64:')) {
            $configuredKey = base64_decode(substr($configuredKey, 7), true) ?: '';
        }

        return hash('sha256', $configuredKey ?: base_url() . 'qr-tamu-umum', true);
    }

    private function getPegawaiInstansiByNip(?string $nip): ?array
    {
        if (!$nip || !preg_match('/^\d{18}$/', $nip)) return null;

        $db     = \Config\Database::connect();
        $pegawai = $db->table('pegawai dp')
                      ->select('mo.nama_opd, mb.nama_bagian, ms.nama_subbagian')
                      ->join('opd mo', 'mo.kode_opd = dp.kode_opd', 'left')
                      ->join('bagian mb', 'mb.kode_bagian = dp.kode_bagian AND mb.kode_opd = dp.kode_opd', 'left')
                      ->join('subbagian ms', 'ms.kode_subbagian = dp.kode_subbagian AND ms.kode_bagian = dp.kode_bagian AND ms.kode_opd = dp.kode_opd', 'left')
                      ->where('dp.nip', $nip)
                      ->where('dp.status', 'aktif')
                      ->get()->getRowArray();

        if ($pegawai) {
            return [
                'instansi'  => $pegawai['nama_opd']      ?? null,
                'bidang'    => $pegawai['nama_bagian']    ?? null,
                'subbidang' => $pegawai['nama_subbagian'] ?? null,
            ];
        }

        return null;
    }

    private function saveCompressedImage($base64Data, $uploadPath, $prefix = 'img')
    {
        if ($base64Data && strpos($base64Data, 'data:image') === 0) {
            list($type, $data) = explode(';', $base64Data);
            list(, $data)      = explode(',', $data);
            $decodedData       = base64_decode($data);

            $fileName = $prefix . '_' . uniqid() . '.jpg';
            $filePath = rtrim($uploadPath, '/') . '/' . $fileName;

            $img = @imagecreatefromstring($decodedData);
            if ($img !== false) {
                $bg    = imagecreatetruecolor(imagesx($img), imagesy($img));
                $white = imagecolorallocate($bg, 255, 255, 255);
                imagefill($bg, 0, 0, $white);
                imagecopy($bg, $img, 0, 0, 0, 0, imagesx($img), imagesy($img));
                imagejpeg($bg, $filePath, 60);
                imagedestroy($img);
                imagedestroy($bg);
                return $fileName;
            } else {
                file_put_contents($filePath, $decodedData);
                return $fileName;
            }
        }
        return null;
    }

    public function serveUpload(string $type, string $year, string $month, string $filename): \CodeIgniter\HTTP\Response
    {
        if (!in_array($type, ['foto', 'ttd', 'file'], true)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $filename = basename(rawurldecode($filename));
        $year     = preg_replace('/[^0-9]/', '', $year);
        $month    = preg_replace('/[^0-9]/', '', $month);

        $filePath = dirname(FCPATH)
            . DIRECTORY_SEPARATOR . 'uploads'
            . DIRECTORY_SEPARATOR . $year
            . DIRECTORY_SEPARATOR . $month
            . DIRECTORY_SEPARATOR . $type
            . DIRECTORY_SEPARATOR . $filename;
            
            

        if (!is_file($filePath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ($type === 'file' && !auth()->loggedIn()) {
            return $this->response->setStatusCode(403)->setBody('Akses ditolak.');
        }

        $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';
        $fileSize = filesize($filePath);

        return $this->response
            ->setHeader('Content-Type', $mimeType)
            ->setHeader('Content-Length', (string) $fileSize)
            ->setHeader('Cache-Control', 'public, max-age=86400')
            ->setHeader('Pragma', 'public')
            ->setBody(file_get_contents($filePath));
    }
}