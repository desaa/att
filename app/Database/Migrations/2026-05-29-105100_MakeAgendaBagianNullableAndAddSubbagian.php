<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MakeAgendaBagianNullableAndAddSubbagian extends Migration
{
    public function up()
    {
        // 1. Add kode_subbagian to agenda
        if (!$this->db->fieldExists('kode_subbagian', 'agenda')) {
            $this->db->query("ALTER TABLE agenda ADD kode_subbagian VARCHAR(50) NULL AFTER kode_bagian");
        }

        // 2. Make kode_bagian nullable in agenda
        $this->db->query("ALTER TABLE agenda MODIFY kode_bagian VARCHAR(50) NULL");

        // 3. Make kode_bagian nullable in buku_tamu
        $this->db->query("ALTER TABLE buku_tamu MODIFY kode_bagian VARCHAR(50) NULL");
    }

    public function down()
    {
        // 1. Drop kode_subbagian from agenda
        if ($this->db->fieldExists('kode_subbagian', 'agenda')) {
            $this->db->query("ALTER TABLE agenda DROP COLUMN kode_subbagian");
        }

        // 2. Make kode_bagian not null in agenda
        $this->db->query("ALTER TABLE agenda MODIFY kode_bagian VARCHAR(50) NOT NULL");

        // 3. Make kode_bagian not null in buku_tamu
        $this->db->query("ALTER TABLE buku_tamu MODIFY kode_bagian VARCHAR(50) NOT NULL");
    }
}
