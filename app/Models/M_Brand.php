<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;

class M_Brand extends Model
{
    protected $table      = 'md_brand';
    protected $primaryKey = 'md_brand_id';
    protected $allowedFields = [
        'value',
        'name',
        'description',
        'isactive',
        'created_by',
        'updated_by'
    ];
    protected $useTimestamps = true;
    protected $returnType = 'App\Entities\Brand';
    protected $column_order = [
        '', // Hide column
        '', // Number column
        'md_brand.value',
        'md_brand.name',
        'md_brand.isactive'
    ];
    protected $column_search = [
        'md_brand.value',
        'md_brand.name',
        'md_brand.isactive'
    ];
    protected $order = ['value' => 'ASC'];
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
