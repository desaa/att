<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBukuTamuTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_agenda' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'nama_tamu' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'nik' => [
                'type'       => 'CHAR',
                'constraint' => '16',
            ],
            'instansi' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'no_hp' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'alamat' => [
                'type' => 'TEXT',
            ],
            'keperluan' => [
                'type' => 'TEXT',
            ],
            'id_pegawai_tujuan' => [
                'type'     => 'INT',
                'unsigned' => true,
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
            'waktu_datang' => [
                'type' => 'DATETIME',
            ],
            'waktu_pulang' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'foto' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'tanda_tangan' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'dokumen_pendukung' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'no_referensi' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'unique'     => true,
            ],
            'status_kunjungan' => [
                'type'       => 'ENUM',
                'constraint' => ['menunggu', 'berlangsung', 'selesai', 'batal'],
                'default'    => 'menunggu',
            ],
            'created_by' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('buku_tamu');

        // Add foreign keys using raw SQL
        $sql = "ALTER TABLE buku_tamu 
                ADD CONSTRAINT fk_bukutamu_agenda 
                FOREIGN KEY (id_agenda) 
                REFERENCES agenda (id_agenda) 
                ON DELETE SET NULL ON UPDATE CASCADE,
                ADD CONSTRAINT fk_bukutamu_pegawai 
                FOREIGN KEY (id_pegawai_tujuan) 
                REFERENCES pegawai (id) 
                ON DELETE RESTRICT ON UPDATE CASCADE,
                ADD CONSTRAINT fk_bukutamu_bagian 
                FOREIGN KEY (kode_opd, kode_bagian) 
                REFERENCES bagian (kode_opd, kode_bagian) 
                ON DELETE CASCADE ON UPDATE CASCADE,
                ADD CONSTRAINT fk_bukutamu_user 
                FOREIGN KEY (created_by) 
                REFERENCES users (id) 
                ON DELETE SET NULL ON UPDATE CASCADE";
        $this->db->query($sql);
    }

    public function down()
    {
        $this->forge->dropTable('buku_tamu');
    }
}
