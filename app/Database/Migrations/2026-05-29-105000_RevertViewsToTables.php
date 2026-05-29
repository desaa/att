<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RevertViewsToTables extends Migration
{
    public function up()
    {
        // 1. Drop views if they exist
        $this->db->query("DROP VIEW IF EXISTS pegawai");
        $this->db->query("DROP VIEW IF EXISTS subbagian");
        $this->db->query("DROP VIEW IF EXISTS bagian");
        $this->db->query("DROP VIEW IF EXISTS opd");

        // 2. Re-create local tables
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
                'type'       => 'VARCHAR',
                'constraint' => '50', // Stores NIP as string
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
                'null'       => true,
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
    }

    public function down()
    {
        // Simply drop tables if rolled back
        $this->db->query("DROP TABLE IF EXISTS pegawai");
        $this->db->query("DROP TABLE IF EXISTS subbagian");
        $this->db->query("DROP TABLE IF EXISTS bagian");
        $this->db->query("DROP TABLE IF EXISTS opd");
    }
}
