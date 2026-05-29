<?php

namespace App\Controllers;

use App\Models\AgendaModel;
use App\Models\BukuTamuModel;
use App\Models\OpdModel;
use App\Models\BagianModel;
use Ramsey\Uuid\Uuid;
use chillerlan\QRCode\QRCode;

class Agenda extends BaseController
{
    public function index()
    {
        $user = auth()->user();
        $isSuperadmin = $user->inGroup('superadmin');
        
        $agendaModel = new AgendaModel();
        
        $query = $agendaModel->select('agenda.*, opd.nama_opd, bagian.nama_bagian, subbagian.nama_subbagian, users.username as pembuat')
                            ->join('opd', 'opd.kode_opd = agenda.kode_opd')
                            ->join('bagian', 'bagian.kode_opd = agenda.kode_opd AND bagian.kode_bagian = agenda.kode_bagian', 'left')
                            ->join('subbagian', 'subbagian.kode_opd = agenda.kode_opd AND subbagian.kode_bagian = agenda.kode_bagian AND subbagian.kode_subbagian = agenda.kode_subbagian', 'left')
                            ->join('users', 'users.id = agenda.created_by', 'left');

        if (!$isSuperadmin) {
            $query->where('agenda.kode_opd', $user->kode_opd);
        }

        $data['agendas'] = $query->orderBy('agenda.created_at', 'DESC')->findAll();
        $data['isSuperadmin'] = $isSuperadmin;

        // Generate QR code base64 strings for each agenda to show in list/modals
        foreach ($data['agendas'] as &$agenda) {
            $url = base_url('tamu/agenda/' . $agenda['qr_code']);
            $agenda['qr_image'] = (new QRCode())->render($url);
        }

        return view('agenda/index', $data);
    }

    public function create()
    {
        $user = auth()->user();
        $isSuperadmin = $user->inGroup('superadmin');
        
        $db = \Config\Database::connect('simpelgan');

        if ($isSuperadmin) {
            $data['opds'] = $db->table('master_opd')->where('id_gov', 'P2300001')->orderBy('kode_opd', 'ASC')->get()->getResultArray();
        } else {
            $opdModel = new OpdModel();
            $bagianModel = new BagianModel();
            $data['opd'] = $opdModel->find($user->kode_opd);
            $data['bagians'] = $bagianModel->where('kode_opd', $user->kode_opd)->orderBy('nama_bagian', 'ASC')->findAll();
        }

        $data['subbagians'] = [];
        $data['isSuperadmin'] = $isSuperadmin;
        return view('agenda/create', $data);
    }

    public function store()
    {
        $user = auth()->user();
        $isSuperadmin = $user->inGroup('superadmin');
        $agendaModel = new AgendaModel();

        $rules = [
            'nama_agenda'      => 'required|max_length[255]',
            'tanggal_mulai'    => 'required|valid_date[Y-m-d H:i]',
            'tanggal_selesai'  => 'required|valid_date[Y-m-d H:i]',
            'lokasi'           => 'required|max_length[255]',
            'penanggung_jawab' => 'required|max_length[255]',
            'kode_bagian'      => 'permit_empty',
            'kode_subbagian'   => 'permit_empty',
            'status'           => 'required',
        ];

        if ($isSuperadmin) {
            $rules['kode_opd'] = 'required';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode('<br>', $this->validator->getErrors()));
        }

        $kodeOpd = $isSuperadmin ? $this->request->getPost('kode_opd') : $user->kode_opd;
        
        // Generate unique UUID token for QR
        $qrToken = Uuid::uuid4()->toString();

        $data = [
            'nama_agenda'      => $this->request->getPost('nama_agenda'),
            'deskripsi'        => $this->request->getPost('deskripsi') ?: null,
            'tanggal_mulai'    => $this->request->getPost('tanggal_mulai') . ':00',
            'tanggal_selesai'  => $this->request->getPost('tanggal_selesai') . ':00',
            'lokasi'           => $this->request->getPost('lokasi'),
            'penanggung_jawab' => $this->request->getPost('penanggung_jawab'),
            'kode_opd'         => $kodeOpd,
            'kode_bagian'      => $this->request->getPost('kode_bagian') ?: null,
            'kode_subbagian'   => $this->request->getPost('kode_subbagian') ?: null,
            'qr_code'          => $qrToken,
            'status'           => $this->request->getPost('status'),
            'created_by'       => $user->id,
        ];

        $agendaModel->insert($data);
        log_activity('Membuat Agenda baru: ' . $data['nama_agenda'], 'agenda', $agendaModel->getInsertID());

        return redirect()->to('agenda')->with('success', 'Agenda berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $user = auth()->user();
        $isSuperadmin = $user->inGroup('superadmin');
        $agendaModel = new AgendaModel();
        
        $agenda = $agendaModel->find($id);

        if (!$agenda) {
            return redirect()->to('agenda')->with('error', 'Agenda tidak ditemukan.');
        }

        // Access check
        if (!$isSuperadmin && $agenda['kode_opd'] !== $user->kode_opd) {
            return redirect()->to('agenda')->with('error', 'Anda tidak memiliki hak untuk mengubah agenda ini.');
        }

        $db = \Config\Database::connect('simpelgan');

        if ($isSuperadmin) {
            $data['opds'] = $db->table('master_opd')->where('id_gov', 'P2300001')->orderBy('kode_opd', 'ASC')->get()->getResultArray();
            $data['bagians'] = $db->table('master_bagian')->where('id_gov', 'P2300001')->where('kode_opd', $agenda['kode_opd'])->orderBy('nama_bagian', 'ASC')->get()->getResultArray();
            if ($agenda['kode_bagian']) {
                $data['subbagians'] = $db->table('master_subbagian')->where('id_gov', 'P2300001')->where('kode_opd', $agenda['kode_opd'])->where('kode_bagian', $agenda['kode_bagian'])->orderBy('nama_subbagian', 'ASC')->get()->getResultArray();
            } else {
                $data['subbagians'] = [];
            }
        } else {
            $opdModel = new OpdModel();
            $bagianModel = new BagianModel();
            $subbagianModel = new \App\Models\SubbagianModel();
            $data['opd'] = $opdModel->find($user->kode_opd);
            $data['bagians'] = $bagianModel->where('kode_opd', $user->kode_opd)->orderBy('nama_bagian', 'ASC')->findAll();
            if ($agenda['kode_bagian']) {
                $data['subbagians'] = $subbagianModel->where('kode_opd', $user->kode_opd)->where('kode_bagian', $agenda['kode_bagian'])->orderBy('nama_subbagian', 'ASC')->findAll();
            } else {
                $data['subbagians'] = [];
            }
        }

        $data['agenda'] = $agenda;
        $data['isSuperadmin'] = $isSuperadmin;

        return view('agenda/edit', $data);
    }

    public function update($id)
    {
        $user = auth()->user();
        $isSuperadmin = $user->inGroup('superadmin');
        $agendaModel = new AgendaModel();

        $agenda = $agendaModel->find($id);
        if (!$agenda) {
            return redirect()->to('agenda')->with('error', 'Agenda tidak ditemukan.');
        }

        // Access check
        if (!$isSuperadmin && $agenda['kode_opd'] !== $user->kode_opd) {
            return redirect()->to('agenda')->with('error', 'Anda tidak memiliki hak untuk mengubah agenda ini.');
        }

        $rules = [
            'nama_agenda'      => 'required|max_length[255]',
            'tanggal_mulai'    => 'required|valid_date[Y-m-d H:i]',
            'tanggal_selesai'  => 'required|valid_date[Y-m-d H:i]',
            'lokasi'           => 'required|max_length[255]',
            'penanggung_jawab' => 'required|max_length[255]',
            'kode_bagian'      => 'permit_empty',
            'kode_subbagian'   => 'permit_empty',
            'status'           => 'required',
        ];

        if ($isSuperadmin) {
            $rules['kode_opd'] = 'required';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode('<br>', $this->validator->getErrors()));
        }

        $kodeOpd = $isSuperadmin ? $this->request->getPost('kode_opd') : $user->kode_opd;

        $data = [
            'nama_agenda'      => $this->request->getPost('nama_agenda'),
            'deskripsi'        => $this->request->getPost('deskripsi') ?: null,
            'tanggal_mulai'    => $this->request->getPost('tanggal_mulai') . ':00',
            'tanggal_selesai'  => $this->request->getPost('tanggal_selesai') . ':00',
            'lokasi'           => $this->request->getPost('lokasi'),
            'penanggung_jawab' => $this->request->getPost('penanggung_jawab'),
            'kode_opd'         => $kodeOpd,
            'kode_bagian'      => $this->request->getPost('kode_bagian') ?: null,
            'kode_subbagian'   => $this->request->getPost('kode_subbagian') ?: null,
            'status'           => $this->request->getPost('status'),
        ];

        $agendaModel->update($id, $data);
        log_activity('Memperbarui data Agenda: ' . $data['nama_agenda'] . ' (ID: ' . $id . ')', 'agenda', $id);

        return redirect()->to('agenda')->with('success', 'Agenda berhasil diperbarui!');
    }

    public function delete($id)
    {
        $user = auth()->user();
        $isSuperadmin = $user->inGroup('superadmin');
        $agendaModel = new AgendaModel();

        $agenda = $agendaModel->find($id);
        if (!$agenda) {
            return redirect()->to('agenda')->with('error', 'Agenda tidak ditemukan.');
        }

        // Access check
        if (!$isSuperadmin && $agenda['kode_opd'] !== $user->kode_opd) {
            return redirect()->to('agenda')->with('error', 'Anda tidak memiliki hak untuk menghapus agenda ini.');
        }

        try {
            $agendaModel->delete($id);
            log_activity('Menghapus Agenda: ' . $agenda['nama_agenda'] . ' (ID: ' . $id . ')', 'agenda', $id);
            return redirect()->to('agenda')->with('success', 'Agenda berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->to('agenda')->with('error', 'Gagal menghapus agenda. Data ini masih memiliki referensi di Buku Tamu.');
        }
    }

    public function complete($id)
    {
        $user = auth()->user();
        $isSuperadmin = $user->inGroup('superadmin');
        
        $agendaModel = new AgendaModel();
        $agenda = $agendaModel->find($id);

        if (!$agenda) {
            return redirect()->to('agenda')->with('error', 'Agenda tidak ditemukan.');
        }

        // Access check: non-superadmin can only complete agendas from their own OPD
        if (!$isSuperadmin && $agenda['kode_opd'] !== $user->kode_opd) {
            return redirect()->to('agenda')->with('error', 'Anda tidak memiliki hak untuk menyelesaikan agenda ini.');
        }

        // Update agenda status to selesai
        $agendaModel->update($id, ['status' => 'selesai']);

        // Update all tamu attending this agenda
        $bukuTamuModel = new BukuTamuModel();
        $now = date('Y-m-d H:i:s');
        
        // Update tamu status to selesai and set waktu_pulang
        $bukuTamuModel->where('id_agenda', $id)
                      ->whereIn('status_kunjungan', ['menunggu', 'berlangsung'])
                      ->set([
                          'status_kunjungan' => 'selesai',
                          'waktu_pulang' => $now
                      ])
                      ->update();

        log_activity('Menyelesaikan Agenda: ' . $agenda['nama_agenda'] . ' (ID: ' . $id . ')', 'agenda', $id);

        return redirect()->to('agenda')->with('success', 'Agenda berhasil diselesaikan dan semua tamu di dalamnya telah diselesaikan.');
    }
}
