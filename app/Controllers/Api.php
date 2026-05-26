<?php

namespace App\Controllers;

use App\Models\BagianModel;
use App\Models\SubbagianModel;
use App\Models\PegawaiModel;

class Api extends BaseController
{
    public function getBagian($kodeOpd)
    {
        $bagianModel = new BagianModel();
        $bagians = $bagianModel->where('kode_opd', $kodeOpd)->orderBy('nama_bagian', 'ASC')->findAll();
        return $this->response->setJSON($bagians);
    }

    public function getSubbagian($kodeOpd, $kodeBagian)
    {
        $subbagianModel = new SubbagianModel();
        $subbagians = $subbagianModel->where('kode_opd', $kodeOpd)
                                     ->where('kode_bagian', $kodeBagian)
                                     ->orderBy('nama_subbagian', 'ASC')
                                     ->findAll();
        return $this->response->setJSON($subbagians);
    }

    public function getPegawai($kodeOpd, $kodeBagian)
    {
        $pegawaiModel = new PegawaiModel();
        $pegawai = $pegawaiModel->where('kode_opd', $kodeOpd)
                                ->where('kode_bagian', $kodeBagian)
                                ->where('status', 'aktif')
                                ->orderBy('nama', 'ASC')
                                ->findAll();
        return $this->response->setJSON($pegawai);
    }

    public function getPegawaiAll()
    {
        $pegawaiModel = new PegawaiModel();
        $pegawai = $pegawaiModel->where('status', 'aktif')->orderBy('nama', 'ASC')->findAll();
        return $this->response->setJSON($pegawai);
    }
}
