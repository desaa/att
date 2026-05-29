<?php

namespace App\Models;

use CodeIgniter\Model;

class AgendaModel extends Model
{
    protected $table            = 'agenda';
    protected $primaryKey       = 'id_agenda';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'nama_agenda', 'deskripsi', 'tanggal_mulai', 'tanggal_selesai', 
        'lokasi', 'penanggung_jawab', 'kode_opd', 'kode_bagian', 'kode_subbagian', 
        'qr_code', 'status', 'created_by', 'created_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
}
