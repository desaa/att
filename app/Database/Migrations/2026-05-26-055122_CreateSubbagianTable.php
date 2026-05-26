<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSubbagianTable extends Migration
{
    public function up()
    {
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
        $this->forge->createTable('subbagian');

        // Add composite foreign key constraint using raw SQL
        $sql = "ALTER TABLE subbagian 
                ADD CONSTRAINT fk_subbagian_bagian 
                FOREIGN KEY (kode_opd, kode_bagian) 
                REFERENCES bagian (kode_opd, kode_bagian) 
                ON DELETE CASCADE ON UPDATE CASCADE";
        $this->db->query($sql);
    }

    public function down()
    {
        $this->forge->dropTable('subbagian');
    }
}
