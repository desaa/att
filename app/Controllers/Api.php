<?php

namespace App\Controllers;

class Api extends BaseController
{
    public function getBagian($kodeOpd)
    {
        $db = \Config\Database::connect('simpelgan');
        $bagians = $db->table('master_bagian')
                      ->where('id_gov', 'P2300001')
                      ->where('kode_opd', $kodeOpd)
                      ->orderBy('nama_bagian', 'ASC')
                      ->get()
                      ->getResultArray();
        return $this->response->setJSON($bagians);
    }

    public function getSubbagian($kodeOpd, $kodeBagian)
    {
        $db = \Config\Database::connect('simpelgan');
        $subbagians = $db->table('master_subbagian')
                         ->where('id_gov', 'P2300001')
                         ->where('kode_opd', $kodeOpd)
                         ->where('kode_bagian', $kodeBagian)
                         ->orderBy('nama_subbagian', 'ASC')
                         ->get()
                         ->getResultArray();
        return $this->response->setJSON($subbagians);
    }

    public function getPegawai($kodeOpd, $kodeBagian)
    {
        $db = \Config\Database::connect('simpelgan');
        $builder = $db->table('data_pegawai dp')
                      ->select('dp.nip as id, dp.nama_lengkap as nama, dp.kode_opd, dp.kode_bagian, dp.kode_subbagian')
                      ->join('master_jabatan mj', 'mj.kode_jabatan = dp.kode_jabatan AND mj.id_gov = dp.id_gov', 'left')
                      ->join('master_opd mo', 'mo.kode_opd = dp.kode_opd AND mo.id_gov = dp.id_gov', 'left')
                      ->where('dp.kode_opd', $kodeOpd)
                      ->where('dp.kode_bagian', $kodeBagian);
        \App\Helpers\SimpelganSyncHelper::applySimpelganPegawaiScope($builder);
        $pegawai = $builder->orderBy('dp.nama_lengkap', 'ASC')->get()->getResultArray();
        return $this->response->setJSON($pegawai);
    }

    public function getPegawaiFiltered()
    {
        $kodeOpd = $this->request->getGet('kode_opd');
        $kodeBagian = $this->request->getGet('kode_bagian');
        $kodeSubbagian = $this->request->getGet('kode_subbagian');

        $db = \Config\Database::connect('simpelgan');
        $builder = $db->table('data_pegawai dp')
                      ->select('dp.nip as id, dp.nama_lengkap as nama, dp.kode_opd, dp.kode_bagian, dp.kode_subbagian')
                      ->join('master_jabatan mj', 'mj.kode_jabatan = dp.kode_jabatan AND mj.id_gov = dp.id_gov', 'left')
                      ->join('master_opd mo', 'mo.kode_opd = dp.kode_opd AND mo.id_gov = dp.id_gov', 'left');
        \App\Helpers\SimpelganSyncHelper::applySimpelganPegawaiScope($builder);

        if ($kodeOpd) {
            $builder->where('dp.kode_opd', $kodeOpd);
        }
        if ($kodeBagian) {
            $builder->where('dp.kode_bagian', $kodeBagian);
        }
        if ($kodeSubbagian) {
            $builder->where('dp.kode_subbagian', $kodeSubbagian);
        }

        $pegawai = $builder->orderBy('dp.nama_lengkap', 'ASC')->get()->getResultArray();
        return $this->response->setJSON($pegawai);
    }

    public function getPegawaiAll()
    {
        $db = \Config\Database::connect('simpelgan');
        $builder = $db->table('data_pegawai dp')
                      ->select('dp.nip as id, dp.nama_lengkap as nama, dp.kode_opd, dp.kode_bagian, dp.kode_subbagian')
                      ->join('master_jabatan mj', 'mj.kode_jabatan = dp.kode_jabatan AND mj.id_gov = dp.id_gov', 'left')
                      ->join('master_opd mo', 'mo.kode_opd = dp.kode_opd AND mo.id_gov = dp.id_gov', 'left');
        \App\Helpers\SimpelganSyncHelper::applySimpelganPegawaiScope($builder);
        $pegawai = $builder->orderBy('dp.nama_lengkap', 'ASC')->get()->getResultArray();
        return $this->response->setJSON($pegawai);
    }

    public function getPegawaiByNip($nip)
    {
        $db = \Config\Database::connect('simpelgan');
        $pegawai = $db->table('data_pegawai dp')
                      ->join('master_jabatan mj', 'mj.kode_jabatan = dp.kode_jabatan AND mj.id_gov = dp.id_gov', 'left')
                      ->join('master_opd mo', 'mo.kode_opd = dp.kode_opd AND mo.id_gov = dp.id_gov', 'left')
                      ->where('dp.nip', $nip);
        \App\Helpers\SimpelganSyncHelper::applySimpelganPegawaiScope($pegawai);
        $pegawai = $pegawai->get()
                      ->getRowArray();
        if ($pegawai) {
            return $this->response->setJSON([
                'status' => 'success',
                'data'   => [
                    'nama' => $pegawai['nama_lengkap']
                ]
            ]);
        }
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Pegawai tidak ditemukan'
        ]);
    }
}
