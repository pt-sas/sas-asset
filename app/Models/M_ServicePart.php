<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;

class M_ServicePart extends Model
{
    protected $table            = 'trx_service_part';
    protected $primaryKey       = 'trx_service_part_id';
    protected $allowedFields    = [
        'trx_service_detail_id',
        'md_sparepart_id',
        'qtyentered',
        'unitprice',
        'lineamt',
        'description',
    ];
    protected $useTimestamps    = true;
    protected $returnType       = 'App\Entities\ServicePart';
    protected $db;
    protected $builder;
    protected $request;

    public function __construct(RequestInterface $request)
    {
        parent::__construct();
        $this->db = db_connect();
        $this->builder = $this->db->table($this->table);
        $this->request = $request;
    }
}
