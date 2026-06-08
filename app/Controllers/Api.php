<?php

namespace App\Controllers;

class Api extends BaseController
{
    public function getBagian($kodeOpd)
    {
        $db = \Config\Database::connect();
        $bagians = $db->table('bagian')
                      ->where('kode_opd', $kodeOpd)
                      ->orderBy('nama_bagian', 'ASC')
                      ->get()
                      ->getResultArray();
        return $this->response->setJSON($bagians);
    }

    public function getSubbagian($kodeOpd, $kodeBagian)
    {
        $db = \Config\Database::connect();
        $subbagians = $db->table('subbagian')
                         ->where('kode_opd', $kodeOpd)
                         ->where('kode_bagian', $kodeBagian)
                         ->orderBy('nama_subbagian', 'ASC')
                         ->get()
                         ->getResultArray();
        return $this->response->setJSON($subbagians);
    }

    public function getPegawaiByOpd($kodeOpd)
    {
        $db = \Config\Database::connect();
        $pegawai = $db->table('pegawai dp')
                      ->select('dp.nip as id, dp.nama, dp.kode_opd, dp.kode_bagian, dp.kode_subbagian')
                      ->where('dp.kode_opd', $kodeOpd)
                      ->where('dp.status', 'aktif')
                      ->orderBy('dp.nama', 'ASC')
                      ->get()->getResultArray();
        return $this->response->setJSON($pegawai);
    }

    public function getPegawai($kodeOpd, $kodeBagian)
    {
        $db = \Config\Database::connect();
        $pegawai = $db->table('pegawai dp')
                      ->select('dp.nip as id, dp.nama, dp.kode_opd, dp.kode_bagian, dp.kode_subbagian')
                      ->where('dp.kode_opd', $kodeOpd)
                      ->where('dp.kode_bagian', $kodeBagian)
                      ->where('dp.status', 'aktif')
                      ->orderBy('dp.nama', 'ASC')
                      ->get()->getResultArray();
        return $this->response->setJSON($pegawai);
    }

    public function getPegawaiBySubbagian($kodeOpd, $kodeBagian, $kodeSubbagian)
    {
        $db = \Config\Database::connect();
        $pegawai = $db->table('pegawai dp')
                      ->select('dp.nip as id, dp.nama, dp.kode_opd, dp.kode_bagian, dp.kode_subbagian')
                      ->where('dp.kode_opd', $kodeOpd)
                      ->where('dp.kode_bagian', $kodeBagian)
                      ->where('dp.kode_subbagian', $kodeSubbagian)
                      ->where('dp.status', 'aktif')
                      ->orderBy('dp.nama', 'ASC')
                      ->get()->getResultArray();
        return $this->response->setJSON($pegawai);
    }

    public function getPegawaiFiltered()
    {
        $kodeOpd = $this->request->getGet('kode_opd');
        $kodeBagian = $this->request->getGet('kode_bagian');
        $kodeSubbagian = $this->request->getGet('kode_subbagian');

        $db = \Config\Database::connect();
        $builder = $db->table('pegawai dp')
                      ->select('dp.nip as id, dp.nama, dp.kode_opd, dp.kode_bagian, dp.kode_subbagian')
                      ->where('dp.status', 'aktif');

        if ($kodeOpd) {
            $builder->where('dp.kode_opd', $kodeOpd);
        }
        if ($kodeBagian) {
            $builder->where('dp.kode_bagian', $kodeBagian);
        }
        if ($kodeSubbagian) {
            $builder->where('dp.kode_subbagian', $kodeSubbagian);
        }

        $pegawai = $builder->orderBy('dp.nama', 'ASC')->get()->getResultArray();
        return $this->response->setJSON($pegawai);
    }

    public function getPegawaiAll()
    {
        $db = \Config\Database::connect();
        $pegawai = $db->table('pegawai dp')
                      ->select('dp.nip as id, dp.nama, dp.kode_opd, dp.kode_bagian, dp.kode_subbagian')
                      ->where('dp.status', 'aktif')
                      ->orderBy('dp.nama', 'ASC')
                      ->get()->getResultArray();
        return $this->response->setJSON($pegawai);
    }

    public function getPegawaiByNip($nip)
    {
        $db = \Config\Database::connect();
        $pegawai = $db->table('pegawai dp')
                      ->select('dp.nip, dp.nama as nama_lengkap, dp.kode_opd, dp.kode_bagian, dp.kode_subbagian, mo.nama_opd, mb.nama_bagian, ms.nama_subbagian')
                      ->join('opd mo', 'mo.kode_opd = dp.kode_opd', 'left')
                      ->join('bagian mb', 'mb.kode_bagian = dp.kode_bagian AND mb.kode_opd = dp.kode_opd', 'left')
                      ->join('subbagian ms', 'ms.kode_subbagian = dp.kode_subbagian AND ms.kode_bagian = dp.kode_bagian AND ms.kode_opd = dp.kode_opd', 'left')
                      ->where('dp.nip', $nip)
                      ->where('dp.status', 'aktif')
                      ->get()
                      ->getRowArray();
                      
        if ($pegawai) {
            return $this->response->setJSON([
                'status' => 'success',
                'data'   => [
                    'nama'      => $pegawai['nama_lengkap'],
                    'instansi'  => $pegawai['nama_opd'] ?? '',
                    'bidang'    => $pegawai['nama_bagian'] ?? '',
                    'subbidang' => $pegawai['nama_subbagian'] ?? ''
                ]
            ]);
        }
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Pegawai tidak ditemukan'
        ]);
    }
}
