<?php

namespace App\Helpers;

class SimpelganSyncHelper
{
    public static function syncAll(): array
    {
        try {
            $simpelganDb = \Config\Database::connect('simpelgan');
            $localDb     = \Config\Database::connect('default');

            // 1. Sync master_opd -> opd
            $opds = $simpelganDb->table('master_opd')
                                ->where('id_gov', 'P2300001')
                                ->get()
                                ->getResultArray();

            $localDb->table('opd')->truncate();
            if (!empty($opds)) {
                $opdData = [];
                foreach ($opds as $row) {
                    $opdData[] = [
                        'kode_opd' => $row['kode_opd'],
                        'nama_opd' => $row['nama_opd'],
                    ];
                }
                $localDb->table('opd')->insertBatch($opdData);
            }

            // 2. Sync master_bagian -> bagian
            $bagians = $simpelganDb->table('master_bagian')
                                  ->where('id_gov', 'P2300001')
                                  ->get()
                                  ->getResultArray();

            $localDb->table('bagian')->truncate();
            if (!empty($bagians)) {
                $bagianData = [];
                foreach ($bagians as $row) {
                    $bagianData[] = [
                        'kode_opd'    => $row['kode_opd'],
                        'kode_bagian' => $row['kode_bagian'],
                        'nama_bagian' => $row['nama_bagian'],
                    ];
                }
                $localDb->table('bagian')->insertBatch($bagianData);
            }

            // 3. Sync master_subbagian -> subbagian
            $subbagians = $simpelganDb->table('master_subbagian')
                                     ->where('id_gov', 'P2300001')
                                     ->get()
                                     ->getResultArray();

            $localDb->table('subbagian')->truncate();
            if (!empty($subbagians)) {
                $subData = [];
                foreach ($subbagians as $row) {
                    $subData[] = [
                        'kode_opd'       => $row['kode_opd'],
                        'kode_bagian'    => $row['kode_bagian'],
                        'kode_subbagian' => $row['kode_subbagian'],
                        'nama_subbagian' => $row['nama_subbagian'],
                    ];
                }
                $localDb->table('subbagian')->insertBatch($subData);
            }

            // 4. Sync data_pegawai + users + master_jabatan -> pegawai
            $pegawais = $simpelganDb->table('data_pegawai dp')
                                   ->select('dp.nip, dp.nama_lengkap, dp.kode_opd, dp.kode_bagian, dp.kode_subbagian, dp.kode_jabatan, dp.flag_aktif, u.password')
                                   ->join('users u', 'u.nip = dp.nip AND u.id_gov = dp.id_gov', 'left')
                                   ->where('dp.id_gov', 'P2300001')
                                   ->get()
                                   ->getResultArray();

            // Fetch jabatans to map them
            $jabatans = $simpelganDb->table('master_jabatan')
                                   ->where('id_gov', 'P2300001')
                                   ->get()
                                   ->getResultArray();
            $jabatanMap = [];
            foreach ($jabatans as $j) {
                $jabatanMap[$j['kode_jabatan']] = $j['nama'];
            }

            $localDb->table('pegawai')->truncate();
            if (!empty($pegawais)) {
                $pegData = [];
                foreach ($pegawais as $row) {
                    $jabatanName = isset($jabatanMap[$row['kode_jabatan']]) ? $jabatanMap[$row['kode_jabatan']] : 'Pegawai';
                    $pegData[] = [
                        'id'             => $row['nip'],
                        'nip'            => $row['nip'],
                        'nama'           => $row['nama_lengkap'],
                        'kode_opd'       => $row['kode_opd'],
                        'kode_bagian'    => $row['kode_bagian'],
                        'kode_subbagian' => $row['kode_subbagian'] ?: null,
                        'jabatan'        => $jabatanName,
                        'status'         => $row['flag_aktif'] === '1' ? 'aktif' : 'nonaktif',
                        'password'       => $row['password'] ?: null,
                    ];
                }
                $localDb->table('pegawai')->insertBatch($pegData);
            }

            // Record last sync time in settings
            $now = date('Y-m-d H:i:s');
            $existing = $localDb->table('settings')->where('class', 'Simpelgan')->where('key', 'last_sync')->get()->getRowArray();
            if ($existing) {
                $localDb->table('settings')->where('class', 'Simpelgan')->where('key', 'last_sync')->update(['value' => $now]);
            } else {
                $localDb->table('settings')->insert([
                    'class' => 'Simpelgan',
                    'key'   => 'last_sync',
                    'value' => $now,
                    'type'  => 'string',
                ]);
            }

            return ['status' => 'success', 'message' => 'Sinkronisasi berhasil! Berhasil menyinkronkan ' . count($pegawais) . ' data pegawai.'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Gagal sinkronisasi: ' . $e->getMessage()];
        }
    }

    public static function syncSinglePegawai($nip): bool
    {
        try {
            $simpelganDb = \Config\Database::connect('simpelgan');
            $localDb     = \Config\Database::connect('default');

            $row = $simpelganDb->table('data_pegawai dp')
                               ->select('dp.nip, dp.nama_lengkap, dp.kode_opd, dp.kode_bagian, dp.kode_subbagian, dp.kode_jabatan, dp.flag_aktif, u.password')
                               ->join('users u', 'u.nip = dp.nip AND u.id_gov = dp.id_gov', 'left')
                               ->where('dp.nip', $nip)
                               ->where('dp.id_gov', 'P2300001')
                               ->get()
                               ->getRowArray();

            if (!$row) {
                return false;
            }

            // Fetch jabatan
            $jab = $simpelganDb->table('master_jabatan')
                               ->where('kode_jabatan', $row['kode_jabatan'])
                               ->where('id_gov', 'P2300001')
                               ->get()
                               ->getRowArray();
            $jabatanName = $jab ? $jab['nama'] : 'Pegawai';

            $pegData = [
                'id'             => $row['nip'],
                'nip'            => $row['nip'],
                'nama'           => $row['nama_lengkap'],
                'kode_opd'       => $row['kode_opd'],
                'kode_bagian'    => $row['kode_bagian'],
                'kode_subbagian' => $row['kode_subbagian'] ?: null,
                'jabatan'        => $jabatanName,
                'status'         => $row['flag_aktif'] === '1' ? 'aktif' : 'nonaktif',
                'password'       => $row['password'] ?: null,
            ];

            $existing = $localDb->table('pegawai')->where('id', $nip)->get()->getRowArray();
            if ($existing) {
                $localDb->table('pegawai')->where('id', $nip)->update($pegData);
            } else {
                $localDb->table('pegawai')->insert($pegData);
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
