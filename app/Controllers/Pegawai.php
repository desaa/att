<?php

namespace App\Controllers;

use App\Models\PegawaiModel;
use App\Models\OpdModel;
use App\Models\BagianModel;
use App\Models\SubbagianModel;

class Pegawai extends BaseController
{
    public function index()
    {
        $user = auth()->user();
        $isSuperadmin = $user->inGroup('superadmin');
        
        $pegawaiModel = new PegawaiModel();
        
        $query = $pegawaiModel->select('pegawai.*, opd.nama_opd, bagian.nama_bagian, subbagian.nama_subbagian')
                              ->join('opd', 'opd.kode_opd = pegawai.kode_opd')
                              ->join('bagian', 'bagian.kode_opd = pegawai.kode_opd AND bagian.kode_bagian = pegawai.kode_bagian')
                              ->join('subbagian', 'subbagian.kode_opd = pegawai.kode_opd AND subbagian.kode_bagian = pegawai.kode_bagian AND subbagian.kode_subbagian = pegawai.kode_subbagian', 'left');

        if (!$isSuperadmin) {
            $query->where('pegawai.kode_opd', $user->kode_opd);
        }

        $data['pegawais'] = $query->orderBy('pegawai.nama', 'ASC')->findAll();
        $data['isSuperadmin'] = $isSuperadmin;

        return view('pegawai/index', $data);
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
        return view('pegawai/create', $data);
    }

    public function store()
    {
        $user = auth()->user();
        $isSuperadmin = $user->inGroup('superadmin');
        $pegawaiModel = new PegawaiModel();

        $rules = [
            'nip'         => 'required|is_unique[pegawai.nip]|numeric|min_length[18]|max_length[18]',
            'nama'        => 'required|max_length[255]',
            'jabatan'     => 'required|max_length[255]',
            'kode_bagian' => 'required',
            'status'      => 'required',
        ];

        if ($isSuperadmin) {
            $rules['kode_opd'] = 'required';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode('<br>', $this->validator->getErrors()));
        }

        $kodeOpd = $isSuperadmin ? $this->request->getPost('kode_opd') : $user->kode_opd;

        $data = [
            'nip'            => $this->request->getPost('nip'),
            'nama'           => $this->request->getPost('nama'),
            'kode_opd'       => $kodeOpd,
            'kode_bagian'    => $this->request->getPost('kode_bagian'),
            'kode_subbagian' => $this->request->getPost('kode_subbagian') ?: null,
            'jabatan'        => $this->request->getPost('jabatan'),
            'status'         => $this->request->getPost('status'),
        ];

        $pegawaiModel->insert($data);
        log_activity('Menambah Pegawai baru: ' . $data['nama'] . ' (NIP: ' . $data['nip'] . ')', 'pegawai', $pegawaiModel->getInsertID());

        return redirect()->to('pegawai')->with('success', 'Pegawai berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $user = auth()->user();
        $isSuperadmin = $user->inGroup('superadmin');
        $pegawaiModel = new PegawaiModel();
        
        $pegawai = $pegawaiModel->find($id);

        if (!$pegawai) {
            return redirect()->to('pegawai')->with('error', 'Pegawai tidak ditemukan.');
        }

        // Access check
        if (!$isSuperadmin && $pegawai['kode_opd'] !== $user->kode_opd) {
            return redirect()->to('pegawai')->with('error', 'Anda tidak memiliki hak untuk mengubah data pegawai ini.');
        }

        $opdModel = new OpdModel();
        $bagianModel = new BagianModel();
        $subbagianModel = new SubbagianModel();

        if ($isSuperadmin) {
            $data['opds'] = $opdModel->orderBy('kode_opd', 'ASC')->findAll();
            $data['bagians'] = $bagianModel->where('kode_opd', $pegawai['kode_opd'])->orderBy('nama_bagian', 'ASC')->findAll();
        } else {
            $data['opd'] = $opdModel->find($user->kode_opd);
            $data['bagians'] = $bagianModel->where('kode_opd', $user->kode_opd)->orderBy('nama_bagian', 'ASC')->findAll();
        }

        if ($pegawai['kode_bagian']) {
            $data['subbagians'] = $subbagianModel->where('kode_opd', $pegawai['kode_opd'])
                                                 ->where('kode_bagian', $pegawai['kode_bagian'])
                                                 ->orderBy('nama_subbagian', 'ASC')
                                                 ->findAll();
        } else {
            $data['subbagians'] = [];
        }

        $data['pegawai'] = $pegawai;
        $data['isSuperadmin'] = $isSuperadmin;

        return view('pegawai/edit', $data);
    }

    public function update($id)
    {
        $user = auth()->user();
        $isSuperadmin = $user->inGroup('superadmin');
        $pegawaiModel = new PegawaiModel();

        $pegawai = $pegawaiModel->find($id);
        if (!$pegawai) {
            return redirect()->to('pegawai')->with('error', 'Pegawai tidak ditemukan.');
        }

        // Access check
        if (!$isSuperadmin && $pegawai['kode_opd'] !== $user->kode_opd) {
            return redirect()->to('pegawai')->with('error', 'Anda tidak memiliki hak untuk mengubah data pegawai ini.');
        }

        $rules = [
            'nip'         => "required|numeric|min_length[18]|max_length[18]|is_unique[pegawai.nip,id,$id]",
            'nama'        => 'required|max_length[255]',
            'jabatan'     => 'required|max_length[255]',
            'kode_bagian' => 'required',
            'status'      => 'required',
        ];

        if ($isSuperadmin) {
            $rules['kode_opd'] = 'required';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode('<br>', $this->validator->getErrors()));
        }

        $kodeOpd = $isSuperadmin ? $this->request->getPost('kode_opd') : $user->kode_opd;

        $data = [
            'nip'            => $this->request->getPost('nip'),
            'nama'           => $this->request->getPost('nama'),
            'kode_opd'       => $kodeOpd,
            'kode_bagian'    => $this->request->getPost('kode_bagian'),
            'kode_subbagian' => $this->request->getPost('kode_subbagian') ?: null,
            'jabatan'        => $this->request->getPost('jabatan'),
            'status'         => $this->request->getPost('status'),
        ];

        $pegawaiModel->update($id, $data);
        log_activity('Memperbarui data Pegawai: ' . $data['nama'] . ' (ID: ' . $id . ')', 'pegawai', $id);

        return redirect()->to('pegawai')->with('success', 'Pegawai berhasil diperbarui!');
    }

    public function delete($id)
    {
        $user = auth()->user();
        $isSuperadmin = $user->inGroup('superadmin');
        $pegawaiModel = new PegawaiModel();

        $pegawai = $pegawaiModel->find($id);
        if (!$pegawai) {
            return redirect()->to('pegawai')->with('error', 'Pegawai tidak ditemukan.');
        }

        // Only superadmin can delete or let unit admin delete their unit's pegawai too. Since routes allow delete, we can verify unit
        if (!$isSuperadmin && $pegawai['kode_opd'] !== $user->kode_opd) {
            return redirect()->to('pegawai')->with('error', 'Anda tidak memiliki hak untuk menghapus data pegawai ini.');
        }

        // Try deleting. Will fail if restricted by guestbook records
        try {
            $pegawaiModel->delete($id);
            log_activity('Menghapus Pegawai: ' . $pegawai['nama'] . ' (NIP: ' . $pegawai['nip'] . ')', 'pegawai', $id);
            return redirect()->to('pegawai')->with('success', 'Pegawai berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->to('pegawai')->with('error', 'Gagal menghapus pegawai. Data ini masih digunakan di Buku Tamu.');
        }
    }
}
