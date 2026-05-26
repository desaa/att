<?php

namespace App\Controllers;

use App\Models\AgendaModel;
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
        
        $query = $agendaModel->select('agenda.*, opd.nama_opd, bagian.nama_bagian, users.username as pembuat')
                            ->join('opd', 'opd.kode_opd = agenda.kode_opd')
                            ->join('bagian', 'bagian.kode_opd = agenda.kode_opd AND bagian.kode_bagian = agenda.kode_bagian')
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
        
        $opdModel = new OpdModel();
        $bagianModel = new BagianModel();

        if ($isSuperadmin) {
            $data['opds'] = $opdModel->orderBy('kode_opd', 'ASC')->findAll();
        } else {
            $data['opd'] = $opdModel->find($user->kode_opd);
            $data['bagians'] = $bagianModel->where('kode_opd', $user->kode_opd)->orderBy('nama_bagian', 'ASC')->findAll();
        }

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
            'kode_bagian'      => 'required',
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
            'kode_bagian'      => $this->request->getPost('kode_bagian'),
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

        $opdModel = new OpdModel();
        $bagianModel = new BagianModel();

        if ($isSuperadmin) {
            $data['opds'] = $opdModel->orderBy('kode_opd', 'ASC')->findAll();
            $data['bagians'] = $bagianModel->where('kode_opd', $agenda['kode_opd'])->orderBy('nama_bagian', 'ASC')->findAll();
        } else {
            $data['opd'] = $opdModel->find($user->kode_opd);
            $data['bagians'] = $bagianModel->where('kode_opd', $user->kode_opd)->orderBy('nama_bagian', 'ASC')->findAll();
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
            'kode_bagian'      => 'required',
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
            'kode_bagian'      => $this->request->getPost('kode_bagian'),
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
}
