<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPasswordToPegawai extends Migration
{
    public function up()
    {
        $fields = [
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'status',
            ],
        ];

        $this->forge->addColumn('pegawai', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('pegawai', 'password');
    }
}
