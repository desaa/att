<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MakeGuestFieldsNullable extends Migration
{
    public function up()
    {
        // Make keperluan nullable
        $this->forge->modifyColumn('buku_tamu', [
            'keperluan' => [
                'type'    => 'TEXT',
                'null'    => true,
                'default' => null,
            ],
            'id_pegawai_tujuan' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'default'  => null,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('buku_tamu', [
            'keperluan' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'id_pegawai_tujuan' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
        ]);
    }
}
