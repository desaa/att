<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAgendaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_agenda' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_agenda' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'tanggal_mulai' => [
                'type' => 'DATETIME',
            ],
            'tanggal_selesai' => [
                'type' => 'DATETIME',
            ],
            'lokasi' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'penanggung_jawab' => [
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
            'qr_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'unique'     => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['aktif', 'nonaktif', 'selesai'],
                'default'    => 'aktif',
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
        $this->forge->addKey('id_agenda', true);
        $this->forge->createTable('agenda');

        // Add composite foreign key constraint and user constraint
        $sql = "ALTER TABLE agenda 
                ADD CONSTRAINT fk_agenda_bagian 
                FOREIGN KEY (kode_opd, kode_bagian) 
                REFERENCES bagian (kode_opd, kode_bagian) 
                ON DELETE CASCADE ON UPDATE CASCADE,
                ADD CONSTRAINT fk_agenda_user 
                FOREIGN KEY (created_by) 
                REFERENCES users (id) 
                ON DELETE SET NULL ON UPDATE CASCADE";
        $this->db->query($sql);
    }

    public function down()
    {
        $this->forge->dropTable('agenda');
    }
}
