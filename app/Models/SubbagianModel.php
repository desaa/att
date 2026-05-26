<?php

namespace App\Models;

use CodeIgniter\Model;

class SubbagianModel extends Model
{
    protected $table            = 'subbagian';
    protected $primaryKey       = 'kode_subbagian';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = ['kode_opd', 'kode_bagian', 'kode_subbagian', 'nama_subbagian'];
}
