<?php

namespace App\Models;

use CodeIgniter\Model;

class BukuTamuModel extends Model
{
    protected $table            = 'buku_tamu';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id_agenda', 'nama_tamu', 'nik', 'instansi', 'no_hp', 'alamat', 
        'keperluan', 'id_pegawai_tujuan', 'kode_opd', 'kode_bagian', 
        'kode_subbagian', 'waktu_datang', 'waktu_pulang', 'foto', 
        'tanda_tangan', 'dokumen_pendukung', 'no_referensi', 
        'status_kunjungan', 'created_by', 'created_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
}
