<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Entities\User;
use App\Models\UserModel;

class AppSeeder extends Seeder
{
    public function run()
    {
        // 1. Seed OPD
        $opdData = [
            [
                'kode_opd' => '05',
                'nama_opd' => 'Dinas Komunikasi dan Informatika',
            ]
        ];
        $this->db->table('opd')->ignore(true)->insertBatch($opdData);

        // 2. Seed Bagian
        $bagianData = [
            [
                'kode_opd'    => '05',
                'kode_bagian' => '01',
                'nama_bagian' => 'Sekretariat',
            ],
            [
                'kode_opd'    => '05',
                'kode_bagian' => '02',
                'nama_bagian' => 'Infrastruktur TIK',
            ]
        ];
        $this->db->table('bagian')->ignore(true)->insertBatch($bagianData);

        // 3. Seed Subbagian
        $subbagianData = [
            [
                'kode_opd'       => '05',
                'kode_bagian'    => '01',
                'kode_subbagian' => '001',
                'nama_subbagian' => 'Umum dan Kepegawaian',
            ],
            [
                'kode_opd'       => '05',
                'kode_bagian'    => '01',
                'kode_subbagian' => '002',
                'nama_subbagian' => 'Perencanaan dan Keuangan',
            ]
        ];
        $this->db->table('subbagian')->ignore(true)->insertBatch($subbagianData);

        // 4. Seed Pegawai
        $pegawaiData = [
            [
                'nip'            => '199001012015011001',
                'nama'           => 'Ahmad Dahlan',
                'kode_opd'       => '05',
                'kode_bagian'    => '01',
                'kode_subbagian' => '001',
                'jabatan'        => 'Kepala Dinas',
                'status'         => 'aktif',
            ],
            [
                'nip'            => '199205122018022002',
                'nama'           => 'Siti Aminah',
                'kode_opd'       => '05',
                'kode_bagian'    => '02',
                'kode_subbagian' => null,
                'jabatan'        => 'Pranata Komputer',
                'status'         => 'aktif',
            ],
            [
                'nip'            => '198808182010031003',
                'nama'           => 'Budi Santoso',
                'kode_opd'       => '05',
                'kode_bagian'    => '01',
                'kode_subbagian' => '002',
                'jabatan'        => 'Sekretaris Dinas',
                'status'         => 'aktif',
            ]
        ];
        $this->db->table('pegawai')->ignore(true)->insertBatch($pegawaiData);

        // 5. Seed Users
        $userModel = new UserModel();

        // Check if superadmin already exists
        $existingSuperadmin = $userModel->where('username', 'superadmin')->first();
        if (!$existingSuperadmin) {
            $superadmin = new User([
                'username' => 'superadmin',
                'email'    => 'superadmin@grobo.go.id',
                'password' => 'password123',
                'nama'     => 'Super Administrator',
                'status_akun'   => 'aktif',
            ]);
            $userModel->save($superadmin);
            $superadmin = $userModel->findById($userModel->getInsertID());
            $superadmin->addGroup('superadmin');
        }

        // Check if admin already exists
        $existingAdmin = $userModel->where('username', 'admin')->first();
        if (!$existingAdmin) {
            $admin = new User([
                'username' => 'admin',
                'email'    => 'admin@grobo.go.id',
                'password' => 'password123',
                'nama'     => 'Admin Diskominfo',
                'kode_opd' => '05',
                'kode_bagian' => '01',
                'kode_subbagian' => '001',
                'status_akun'   => 'aktif',
            ]);
            $userModel->save($admin);
            $admin = $userModel->findById($userModel->getInsertID());
            $admin->addGroup('admin');
        }
    }
}
