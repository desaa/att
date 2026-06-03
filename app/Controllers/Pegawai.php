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
        return redirect()->to('pegawai')->with('error', 'Fitur ini dinonaktifkan karena data Pegawai dikelola secara terpusat melalui aplikasi Simpelgan.');
    }

    public function store()
    {
        return redirect()->to('pegawai')->with('error', 'Fitur ini dinonaktifkan karena data Pegawai dikelola secara terpusat melalui aplikasi Simpelgan.');
    }

    public function edit($hash)
    {
        return redirect()->to('pegawai')->with('error', 'Fitur ini dinonaktifkan karena data Pegawai dikelola secara terpusat melalui aplikasi Simpelgan.');
    }

    public function update($hash)
    {
        return redirect()->to('pegawai')->with('error', 'Fitur ini dinonaktifkan karena data Pegawai dikelola secara terpusat melalui aplikasi Simpelgan.');
    }

    public function delete($hash)
    {
        return redirect()->to('pegawai')->with('error', 'Fitur ini dinonaktifkan karena data Pegawai dikelola secara terpusat melalui aplikasi Simpelgan.');
    }

    public function setPassword($hash)
    {
        return redirect()->to('pegawai')->with('error', 'Fitur ini dinonaktifkan karena data Pegawai dikelola secara terpusat melalui aplikasi Simpelgan.');
    }

    public function savePassword($hash)
    {
        return redirect()->to('pegawai')->with('error', 'Fitur ini dinonaktifkan karena data Pegawai dikelola secara terpusat melalui aplikasi Simpelgan.');
    }
}
