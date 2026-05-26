<?php

namespace App\Models;

use CodeIgniter\Model;

class BagianModel extends Model
{
    protected $table            = 'bagian';
    protected $primaryKey       = 'kode_bagian'; // Single key fallback
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = ['kode_opd', 'kode_bagian', 'nama_bagian'];
}
