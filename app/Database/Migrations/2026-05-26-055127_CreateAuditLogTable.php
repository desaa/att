<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAuditLogTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'aktivitas' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'tabel_terkait' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'id_record' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('audit_log');

        // Add foreign key constraint pointing to users
        $sql = "ALTER TABLE audit_log 
                ADD CONSTRAINT fk_auditlog_user 
                FOREIGN KEY (user_id) 
                REFERENCES users (id) 
                ON DELETE SET NULL ON UPDATE CASCADE";
        $this->db->query($sql);
    }

    public function down()
    {
        $this->forge->dropTable('audit_log');
    }
}
