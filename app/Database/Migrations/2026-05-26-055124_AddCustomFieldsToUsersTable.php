<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCustomFieldsToUsersTable extends Migration
{
    public function up()
    {
        $fields = [
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'after'      => 'username',
            ],
            'kode_opd' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'after'      => 'nama',
            ],
            'kode_bagian' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'after'      => 'kode_opd',
            ],
            'kode_subbagian' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'after'      => 'kode_bagian',
            ],
            'status_akun' => [
                'type'       => 'ENUM',
                'constraint' => ['aktif', 'nonaktif'],
                'default'    => 'aktif',
                'after'      => 'kode_subbagian',
            ],
        ];

        $this->forge->addColumn('users', $fields);

        // Add foreign key constraint for kode_opd pointing to opd
        $sql = "ALTER TABLE users 
                ADD CONSTRAINT fk_users_opd 
                FOREIGN KEY (kode_opd) 
                REFERENCES opd (kode_opd) 
                ON DELETE SET NULL ON UPDATE CASCADE";
        $this->db->query($sql);
    }

    public function down()
    {
        $this->forge->dropForeignKey('users', 'fk_users_opd');
        $this->forge->dropColumn('users', ['nama', 'kode_opd', 'kode_bagian', 'kode_subbagian', 'status_akun']);
    }
}
