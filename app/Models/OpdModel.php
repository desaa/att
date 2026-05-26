<?php

namespace App\Models;

use CodeIgniter\Model;

class OpdModel extends Model
{
    protected $table            = 'opd';
    protected $primaryKey       = 'kode_opd';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = ['kode_opd', 'nama_opd'];
}
