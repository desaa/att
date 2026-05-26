<?php

namespace App\Controllers;

use App\Models\OpdModel;
use App\Models\BagianModel;
use App\Models\SubbagianModel;

class Master extends BaseController
{
    // ==========================================
    // OPD CRUD
    // ==========================================

    public function opdIndex()
    {
        $opdModel = new OpdModel();
        $data['opds'] = $opdModel->orderBy('kode_opd', 'ASC')->findAll();
        return view('master/opd', $data);
    }

    public function opdStore()
    {
        $opdModel = new OpdModel();
        $rules = [
            'kode_opd' => 'required|is_unique[opd.kode_opd]|max_length[50]',
            'nama_opd' => 'required|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode('<br>', $this->validator->getErrors()));
        }

        $data = [
            'kode_opd' => $this->request->getPost('kode_opd'),
            'nama_opd' => $this->request->getPost('nama_opd'),
        ];

        $opdModel->insert($data);
        log_activity('Menambah OPD baru: ' . $data['nama_opd'], 'opd', null);

        return redirect()->to('master/opd')->with('success', 'OPD berhasil ditambahkan!');
    }

    public function opdUpdate($kode_opd)
    {
        $opdModel = new OpdModel();
        $rules = [
            'nama_opd' => 'required|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode('<br>', $this->validator->getErrors()));
        }

        $data = [
            'nama_opd' => $this->request->getPost('nama_opd'),
        ];

        $opdModel->update($kode_opd, $data);
        log_activity('Mengubah OPD dengan Kode: ' . $kode_opd, 'opd', null);

        return redirect()->to('master/opd')->with('success', 'OPD berhasil diperbarui!');
    }

    public function opdDelete($kode_opd)
    {
        $opdModel = new OpdModel();
        $opd = $opdModel->find($kode_opd);
        
        if ($opd) {
            $opdModel->delete($kode_opd);
            log_activity('Menghapus OPD: ' . $opd['nama_opd'], 'opd', null);
            return redirect()->to('master/opd')->with('success', 'OPD berhasil dihapus!');
        }

        return redirect()->to('master/opd')->with('error', 'OPD tidak ditemukan.');
    }

    // ==========================================
    // BAGIAN CRUD
    // ==========================================

    public function bagianIndex()
    {
        $bagianModel = new BagianModel();
        $opdModel = new OpdModel();

        $data['bagians'] = $bagianModel->select('bagian.*, opd.nama_opd')
                                       ->join('opd', 'opd.kode_opd = bagian.kode_opd')
                                       ->orderBy('bagian.kode_opd', 'ASC')
                                       ->orderBy('bagian.kode_bagian', 'ASC')
                                       ->findAll();
                                       
        $data['opds'] = $opdModel->orderBy('kode_opd', 'ASC')->findAll();
        
        return view('master/bagian', $data);
    }

    public function bagianStore()
    {
        $bagianModel = new BagianModel();
        
        $rules = [
            'kode_opd'    => 'required',
            'kode_bagian' => 'required|max_length[50]',
            'nama_bagian' => 'required|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode('<br>', $this->validator->getErrors()));
        }

        $kodeOpd = $this->request->getPost('kode_opd');
        $kodeBagian = $this->request->getPost('kode_bagian');

        // Check composite uniqueness
        $existing = $bagianModel->where('kode_opd', $kodeOpd)->where('kode_bagian', $kodeBagian)->first();
        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'Kode Bagian untuk OPD ini sudah ada.');
        }

        $data = [
            'kode_opd'    => $kodeOpd,
            'kode_bagian' => $kodeBagian,
            'nama_bagian' => $this->request->getPost('nama_bagian'),
        ];

        $bagianModel->insert($data);
        log_activity('Menambah Bagian baru: ' . $data['nama_bagian'], 'bagian', null);

        return redirect()->to('master/bagian')->with('success', 'Bagian berhasil ditambahkan!');
    }

    public function bagianUpdate($kode_opd, $kode_bagian)
    {
        $bagianModel = new BagianModel();
        $rules = [
            'nama_bagian' => 'required|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode('<br>', $this->validator->getErrors()));
        }

        $namaBagian = $this->request->getPost('nama_bagian');
        
        // Custom update using query builder due to composite primary key
        $bagianModel->builder()
                    ->where('kode_opd', $kode_opd)
                    ->where('kode_bagian', $kode_bagian)
                    ->update(['nama_bagian' => $namaBagian]);

        log_activity("Mengubah nama bagian $kode_bagian ($kode_opd) menjadi: $namaBagian", 'bagian', null);

        return redirect()->to('master/bagian')->with('success', 'Bagian berhasil diperbarui!');
    }

    public function bagianDelete($kode_opd, $kode_bagian)
    {
        $bagianModel = new BagianModel();
        
        // Custom delete due to composite primary key
        $bagianModel->builder()
                    ->where('kode_opd', $kode_opd)
                    ->where('kode_bagian', $kode_bagian)
                    ->delete();

        log_activity("Menghapus Bagian Kode: $kode_bagian dari OPD: $kode_opd", 'bagian', null);

        return redirect()->to('master/bagian')->with('success', 'Bagian berhasil dihapus!');
    }

    // ==========================================
    // SUBBAGIAN CRUD
    // ==========================================

    public function subbagianIndex()
    {
        $subbagianModel = new SubbagianModel();
        $bagianModel = new BagianModel();
        $opdModel = new OpdModel();

        $data['subbagians'] = $subbagianModel->select('subbagian.*, opd.nama_opd, bagian.nama_bagian')
                                             ->join('opd', 'opd.kode_opd = subbagian.kode_opd')
                                             ->join('bagian', 'bagian.kode_opd = subbagian.kode_opd AND bagian.kode_bagian = subbagian.kode_bagian')
                                             ->orderBy('subbagian.kode_opd', 'ASC')
                                             ->orderBy('subbagian.kode_bagian', 'ASC')
                                             ->orderBy('subbagian.kode_subbagian', 'ASC')
                                             ->findAll();

        $data['opds'] = $opdModel->orderBy('kode_opd', 'ASC')->findAll();
        
        return view('master/subbagian', $data);
    }

    public function subbagianStore()
    {
        $subbagianModel = new SubbagianModel();
        
        $rules = [
            'kode_opd'       => 'required',
            'kode_bagian'    => 'required',
            'kode_subbagian' => 'required|max_length[50]',
            'nama_subbagian' => 'required|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode('<br>', $this->validator->getErrors()));
        }

        $kodeOpd = $this->request->getPost('kode_opd');
        $kodeBagian = $this->request->getPost('kode_bagian');
        $kodeSubbagian = $this->request->getPost('kode_subbagian');

        // Check composite uniqueness
        $existing = $subbagianModel->where('kode_opd', $kodeOpd)
                                   ->where('kode_bagian', $kodeBagian)
                                   ->where('kode_subbagian', $kodeSubbagian)
                                   ->first();
        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'Kode Subbagian untuk Bagian ini sudah ada.');
        }

        $data = [
            'kode_opd'       => $kodeOpd,
            'kode_bagian'    => $kodeBagian,
            'kode_subbagian' => $kodeSubbagian,
            'nama_subbagian' => $this->request->getPost('nama_subbagian'),
        ];

        $subbagianModel->insert($data);
        log_activity('Menambah Subbagian baru: ' . $data['nama_subbagian'], 'subbagian', null);

        return redirect()->to('master/subbagian')->with('success', 'Subbagian berhasil ditambahkan!');
    }

    public function subbagianUpdate($kode_opd, $kode_bagian, $kode_subbagian)
    {
        $subbagianModel = new SubbagianModel();
        $rules = [
            'nama_subbagian' => 'required|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode('<br>', $this->validator->getErrors()));
        }

        $namaSubbagian = $this->request->getPost('nama_subbagian');
        
        // Custom update
        $subbagianModel->builder()
                       ->where('kode_opd', $kode_opd)
                       ->where('kode_bagian', $kode_bagian)
                       ->where('kode_subbagian', $kode_subbagian)
                       ->update(['nama_subbagian' => $namaSubbagian]);

        log_activity("Mengubah nama subbagian $kode_subbagian ($kode_opd - $kode_bagian) menjadi: $namaSubbagian", 'subbagian', null);

        return redirect()->to('master/subbagian')->with('success', 'Subbagian berhasil diperbarui!');
    }

    public function subbagianDelete($kode_opd, $kode_bagian, $kode_subbagian)
    {
        $subbagianModel = new SubbagianModel();
        
        // Custom delete
        $subbagianModel->builder()
                       ->where('kode_opd', $kode_opd)
                       ->where('kode_bagian', $kode_bagian)
                       ->where('kode_subbagian', $kode_subbagian)
                       ->delete();

        log_activity("Menghapus Subbagian Kode: $kode_subbagian ($kode_opd - $kode_bagian)", 'subbagian', null);

        return redirect()->to('master/subbagian')->with('success', 'Subbagian berhasil dihapus!');
    }
}
