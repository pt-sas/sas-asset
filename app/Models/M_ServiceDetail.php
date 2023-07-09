<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;

class M_ServiceDetail extends Model
{
    protected $table      = 'trx_service_detail';
    protected $primaryKey = 'trx_service_detail_id';
    protected $allowedFields = [
        'trx_service_id',
        'assetcode',
        'qtyentered',
        'unitprice',
        'lineamt',
        'description',
        'md_status_id',
        'md_product_id'
    ];
    protected $useTimestamps = true;
    protected $returnType = 'App\Entities\ServiceDetail';
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
