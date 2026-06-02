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
            $opds = self::uniqueRows($opds, ['kode_opd']);

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
            $bagians = self::uniqueRows($bagians, ['kode_opd', 'kode_bagian']);

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
            $subbagians = self::uniqueRows($subbagians, ['kode_opd', 'kode_bagian', 'kode_subbagian']);

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
            $usersSubquery = '(SELECT nip, id_gov, MAX(password) AS password FROM users GROUP BY nip, id_gov) u';
            $pegawais = $simpelganDb->table('data_pegawai dp')
                                   ->select('dp.nip, dp.nama_lengkap, dp.kode_opd, dp.kode_bagian, dp.kode_subbagian, dp.kode_jabatan, dp.flag_aktif, mj.nama AS jabatan, u.password')
                                   ->join('master_jabatan mj', 'mj.kode_jabatan = dp.kode_jabatan AND mj.id_gov = dp.id_gov', 'left')
                                   ->join('master_opd mo', 'mo.kode_opd = dp.kode_opd AND mo.id_gov = dp.id_gov', 'left')
                                   ->join($usersSubquery, 'u.nip = dp.nip AND u.id_gov = dp.id_gov', 'left');
            self::applySimpelganPegawaiScope($pegawais);
            $pegawais = $pegawais->get()
                                   ->getResultArray();
            $pegawais = self::uniqueRows($pegawais, ['nip']);

            $localDb->table('pegawai')->truncate();
            if (!empty($pegawais)) {
                $pegData = [];
                foreach ($pegawais as $row) {
                    $pegData[] = [
                        'id'             => $row['nip'],
                        'nip'            => $row['nip'],
                        'nama'           => $row['nama_lengkap'],
                        'kode_opd'       => $row['kode_opd'],
                        'kode_bagian'    => $row['kode_bagian'],
                        'kode_subbagian' => $row['kode_subbagian'] ?: null,
                        'jabatan'        => $row['jabatan'] ?: 'Pegawai',
                        'status'         => in_array($row['flag_aktif'], ['1', '2'], true) ? 'aktif' : 'nonaktif',
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

            $usersSubquery = '(SELECT nip, id_gov, MAX(password) AS password FROM users GROUP BY nip, id_gov) u';
            $row = $simpelganDb->table('data_pegawai dp')
                               ->select('dp.nip, dp.nama_lengkap, dp.kode_opd, dp.kode_bagian, dp.kode_subbagian, dp.kode_jabatan, dp.flag_aktif, mj.nama AS jabatan, u.password')
                               ->join('master_jabatan mj', 'mj.kode_jabatan = dp.kode_jabatan AND mj.id_gov = dp.id_gov', 'left')
                               ->join('master_opd mo', 'mo.kode_opd = dp.kode_opd AND mo.id_gov = dp.id_gov', 'left')
                               ->join($usersSubquery, 'u.nip = dp.nip AND u.id_gov = dp.id_gov', 'left')
                               ->where('dp.nip', $nip);
            self::applySimpelganPegawaiScope($row);
            $row = $row->get()
                               ->getRowArray();

            if (!$row) {
                return false;
            }

            $pegData = [
                'id'             => $row['nip'],
                'nip'            => $row['nip'],
                'nama'           => $row['nama_lengkap'],
                'kode_opd'       => $row['kode_opd'],
                'kode_bagian'    => $row['kode_bagian'],
                'kode_subbagian' => $row['kode_subbagian'] ?: null,
                'jabatan'        => $row['jabatan'] ?: 'Pegawai',
                'status'         => in_array($row['flag_aktif'], ['1', '2'], true) ? 'aktif' : 'nonaktif',
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

    private static function uniqueRows(array $rows, array $keyColumns): array
    {
        $unique = [];

        foreach ($rows as $row) {
            $keyParts = [];
            foreach ($keyColumns as $column) {
                $keyParts[] = (string) ($row[$column] ?? '');
            }

            $unique[implode('|', $keyParts)] = $row;
        }

        return array_values($unique);
    }

    public static function applySimpelganPegawaiScope($builder, string $pegawaiAlias = 'dp', string $jabatanAlias = 'mj')
    {
        return $builder->where("{$pegawaiAlias}.id_gov", 'P2300001')
                       ->whereNotIn("{$pegawaiAlias}.kode_opd", ['01', '80', 'TOPD'])
                       ->whereIn("{$pegawaiAlias}.flag_aktif", ['1', '2'])
                       ->whereIn("{$pegawaiAlias}.status", ['1', '2', '3', '4'])
                       ->whereIn("{$jabatanAlias}.level_user", ['5', '7', '9', '10']);
    }

}
