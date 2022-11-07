<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;

class M_Barcode extends Model
{
    protected $table            = 'sys_barcode';
    protected $primaryKey       = 'sys_barcode_id';
    protected $allowedFields    = [
        'barcodetype',
        'generatortype',
        'widthfactor',
        'height',
        'text',
        'iswithtext',
        'positiontext',
        'sizetext',
        'isactive',
        'created_by',
        'updated_by'
    ];
    protected $useTimestamps    = true;
    protected $returnType       = 'App\Entities\Barcode';
    protected $request;
    protected $db;
    protected $builder;

    public function __construct(RequestInterface $request)
    {
        parent::__construct();
        $this->db = db_connect();
        $this->request = $request;
        $this->builder = $this->db->table($this->table);
    }
}
