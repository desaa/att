<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOpdTable extends Migration
{
    public function up()
    {
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
        $this->forge->createTable('opd');
    }

    public function down()
    {
        $this->forge->dropTable('opd');
    }
}
