<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBagianTable extends Migration
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
            'nama_bagian' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
        ]);
        $this->forge->addKey(['kode_opd', 'kode_bagian'], true);
        $this->forge->addForeignKey('kode_opd', 'opd', 'kode_opd', 'CASCADE', 'CASCADE');
        $this->forge->createTable('bagian');
    }

    public function down()
    {
        $this->forge->dropTable('bagian');
    }
}
