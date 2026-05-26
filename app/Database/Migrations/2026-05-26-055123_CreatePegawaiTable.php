<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePegawaiTable extends Migration
{
    public function up()
    {
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
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('pegawai');

        // Add composite foreign key constraint using raw SQL
        $sql = "ALTER TABLE pegawai 
                ADD CONSTRAINT fk_pegawai_bagian 
                FOREIGN KEY (kode_opd, kode_bagian) 
                REFERENCES bagian (kode_opd, kode_bagian) 
                ON DELETE CASCADE ON UPDATE CASCADE";
        $this->db->query($sql);
    }

    public function down()
    {
        $this->forge->dropTable('pegawai');
    }
}
