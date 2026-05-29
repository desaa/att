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
        $pegawai = $db->table('data_pegawai')
                      ->select('nip as id, nama_lengkap as nama, kode_opd, kode_bagian, kode_subbagian')
                      ->where('id_gov', 'P2300001')
                      ->where('kode_opd', $kodeOpd)
                      ->where('kode_bagian', $kodeBagian)
                      ->where('flag_aktif', '1')
                      ->orderBy('nama_lengkap', 'ASC')
                      ->get()
                      ->getResultArray();
        return $this->response->setJSON($pegawai);
    }

    public function getPegawaiFiltered()
    {
        $kodeOpd = $this->request->getGet('kode_opd');
        $kodeBagian = $this->request->getGet('kode_bagian');
        $kodeSubbagian = $this->request->getGet('kode_subbagian');

        $db = \Config\Database::connect('simpelgan');
        $builder = $db->table('data_pegawai')
                      ->select('nip as id, nama_lengkap as nama, kode_opd, kode_bagian, kode_subbagian')
                      ->where('id_gov', 'P2300001')
                      ->where('flag_aktif', '1');

        if ($kodeOpd) {
            $builder->where('kode_opd', $kodeOpd);
        }
        if ($kodeBagian) {
            $builder->where('kode_bagian', $kodeBagian);
        }
        if ($kodeSubbagian) {
            $builder->where('kode_subbagian', $kodeSubbagian);
        }

        $pegawai = $builder->orderBy('nama_lengkap', 'ASC')->get()->getResultArray();
        return $this->response->setJSON($pegawai);
    }

    public function getPegawaiAll()
    {
        $db = \Config\Database::connect('simpelgan');
        $pegawai = $db->table('data_pegawai')
                      ->select('nip as id, nama_lengkap as nama, kode_opd, kode_bagian, kode_subbagian')
                      ->where('id_gov', 'P2300001')
                      ->where('flag_aktif', '1')
                      ->orderBy('nama_lengkap', 'ASC')
                      ->get()
                      ->getResultArray();
        return $this->response->setJSON($pegawai);
    }

    public function getPegawaiByNip($nip)
    {
        $db = \Config\Database::connect('simpelgan');
        $pegawai = $db->table('data_pegawai')
                      ->where('nip', $nip)
                      ->where('id_gov', 'P2300001')
                      ->where('flag_aktif', '1')
                      ->get()
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
