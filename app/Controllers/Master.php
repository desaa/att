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
        return redirect()->to('master/opd')->with('error', 'Fitur ini dinonaktifkan karena data OPD dikelola secara terpusat melalui aplikasi Simpelgan.');
    }

    public function opdUpdate($kode_opd)
    {
        return redirect()->to('master/opd')->with('error', 'Fitur ini dinonaktifkan karena data OPD dikelola secara terpusat melalui aplikasi Simpelgan.');
    }

    public function opdDelete($kode_opd)
    {
        return redirect()->to('master/opd')->with('error', 'Fitur ini dinonaktifkan karena data OPD dikelola secara terpusat melalui aplikasi Simpelgan.');
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
        return redirect()->to('master/bagian')->with('error', 'Fitur ini dinonaktifkan karena data Bagian dikelola secara terpusat melalui aplikasi Simpelgan.');
    }

    public function bagianUpdate($kode_opd, $kode_bagian)
    {
        return redirect()->to('master/bagian')->with('error', 'Fitur ini dinonaktifkan karena data Bagian dikelola secara terpusat melalui aplikasi Simpelgan.');
    }

    public function bagianDelete($kode_opd, $kode_bagian)
    {
        return redirect()->to('master/bagian')->with('error', 'Fitur ini dinonaktifkan karena data Bagian dikelola secara terpusat melalui aplikasi Simpelgan.');
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
        return redirect()->to('master/subbagian')->with('error', 'Fitur ini dinonaktifkan karena data Subbagian dikelola secara terpusat melalui aplikasi Simpelgan.');
    }

    public function subbagianUpdate($kode_opd, $kode_bagian, $kode_subbagian)
    {
        return redirect()->to('master/subbagian')->with('error', 'Fitur ini dinonaktifkan karena data Subbagian dikelola secara terpusat melalui aplikasi Simpelgan.');
    }

    public function subbagianDelete($kode_opd, $kode_bagian, $kode_subbagian)
    {
        return redirect()->to('master/subbagian')->with('error', 'Fitur ini dinonaktifkan karena data Subbagian dikelola secara terpusat melalui aplikasi Simpelgan.');
    }
}
