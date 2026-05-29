<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ReplaceTablesWithViews extends Migration
{
    public function up()
    {
        // 1. Drop foreign keys pointing to pegawai, bagian, opd
        $this->db->query("ALTER TABLE buku_tamu DROP FOREIGN KEY fk_bukutamu_pegawai");
        $this->db->query("ALTER TABLE buku_tamu DROP FOREIGN KEY fk_bukutamu_bagian");
        $this->db->query("ALTER TABLE agenda DROP FOREIGN KEY fk_agenda_bagian");
        
        // Check if users table exists and has fk_users_opd
        try {
            $this->db->query("ALTER TABLE users DROP FOREIGN KEY fk_users_opd");
        } catch (\Exception $e) {
            // Ignore if key does not exist or users table hasn't been migrated
        }

        // 2. Drop the tables
        $this->db->query("DROP TABLE IF EXISTS pegawai");
        $this->db->query("DROP TABLE IF EXISTS subbagian");
        $this->db->query("DROP TABLE IF EXISTS bagian");
        $this->db->query("DROP TABLE IF EXISTS opd");

        // 3. Change buku_tamu.id_pegawai_tujuan type to VARCHAR(50)
        $this->db->query("ALTER TABLE buku_tamu MODIFY id_pegawai_tujuan VARCHAR(50) NULL");

        // 4. Create the views
        // OPD view
        $this->db->query("
            CREATE OR REPLACE VIEW opd AS 
            SELECT kode_opd, nama_opd 
            FROM simpelgan.master_opd 
            WHERE id_gov = 'P2300001'
        ");

        // Bagian view
        $this->db->query("
            CREATE OR REPLACE VIEW bagian AS 
            SELECT kode_opd, kode_bagian, nama_bagian 
            FROM simpelgan.master_bagian 
            WHERE id_gov = 'P2300001'
        ");

        // Subbagian view
        $this->db->query("
            CREATE OR REPLACE VIEW subbagian AS 
            SELECT kode_opd, kode_bagian, kode_subbagian, nama_subbagian 
            FROM simpelgan.master_subbagian 
            WHERE id_gov = 'P2300001'
        ");

        // Pegawai view (with NIP mapped as ID)
        $this->db->query("
            CREATE OR REPLACE VIEW pegawai AS
            SELECT 
                dp.nip AS id,
                dp.nip,
                dp.nama_lengkap AS nama,
                dp.kode_opd,
                dp.kode_bagian,
                dp.kode_subbagian,
                (SELECT mj.nama FROM simpelgan.master_jabatan mj WHERE mj.kode_jabatan = dp.kode_jabatan AND mj.id_gov = 'P2300001' LIMIT 1) AS jabatan,
                IF(dp.flag_aktif = '1', 'aktif', 'nonaktif') AS status,
                u.password AS password
            FROM simpelgan.data_pegawai dp
            LEFT JOIN simpelgan.users u ON u.nip = dp.nip AND u.id_gov = 'P2300001'
            WHERE dp.id_gov = 'P2300001'
        ");
    }

    public function down()
    {
        // 1. Drop views
        $this->db->query("DROP VIEW IF EXISTS pegawai");
        $this->db->query("DROP VIEW IF EXISTS subbagian");
        $this->db->query("DROP VIEW IF EXISTS bagian");
        $this->db->query("DROP VIEW IF EXISTS opd");

        // 2. Re-create tables
        // OPD Table
        $this->forge->addField([
            'kode_opd' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'nama_opd' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
        ]);
        $this->forge->addKey('kode_opd', true);
        $this->forge->createTable('opd', true);

        // Bagian Table
        $this->forge->addField([
            'kode_opd' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'kode_bagian' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'nama_bagian' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
        ]);
        $this->forge->addKey(['kode_opd', 'kode_bagian'], true);
        $this->forge->createTable('bagian', true);

        // Subbagian Table
        $this->forge->addField([
            'kode_opd' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'kode_bagian' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'kode_subbagian' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'nama_subbagian' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
        ]);
        $this->forge->addKey(['kode_opd', 'kode_bagian', 'kode_subbagian'], true);
        $this->forge->createTable('subbagian', true);

        // Pegawai Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nip' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'kode_opd' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'kode_bagian' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'kode_subbagian' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'jabatan' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['aktif', 'nonaktif'],
                'default'    => 'aktif',
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('pegawai', true);

        // 3. Revert buku_tamu.id_pegawai_tujuan type back to INT
        $this->db->query("ALTER TABLE buku_tamu MODIFY id_pegawai_tujuan INT UNSIGNED NULL");

        // 4. Re-add foreign keys
        $this->db->query("
            ALTER TABLE buku_tamu 
            ADD CONSTRAINT fk_bukutamu_pegawai 
            FOREIGN KEY (id_pegawai_tujuan) REFERENCES pegawai (id) 
            ON DELETE RESTRICT ON UPDATE CASCADE
        ");
        $this->db->query("
            ALTER TABLE buku_tamu 
            ADD CONSTRAINT fk_bukutamu_bagian 
            FOREIGN KEY (kode_opd, kode_bagian) REFERENCES bagian (kode_opd, kode_bagian) 
            ON DELETE CASCADE ON UPDATE CASCADE
        ");
        $this->db->query("
            ALTER TABLE agenda 
            ADD CONSTRAINT fk_agenda_bagian 
            FOREIGN KEY (kode_opd, kode_bagian) REFERENCES bagian (kode_opd, kode_bagian) 
            ON DELETE CASCADE ON UPDATE CASCADE
        ");
        
        try {
            $this->db->query("
                ALTER TABLE users 
                ADD CONSTRAINT fk_users_opd 
                FOREIGN KEY (kode_opd) REFERENCES opd (kode_opd) 
                ON DELETE SET NULL ON UPDATE CASCADE
            ");
        } catch (\Exception $e) {
            // Ignore
        }
    }
}
