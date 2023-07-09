<?php

namespace App\Models;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Model;

class M_Variant extends Model
{
    protected $table      = 'md_variant';
    protected $primaryKey = 'md_variant_id';
    protected $allowedFields = [
        'value',
        'name',
        'isactive',
        'created_by',
        'updated_by'
    ];
    protected $useTimestamps = true;
    protected $returnType = 'App\Entities\Variant';
    protected $column_order = [
        '', // Hide column
        '', // Number column
        'md_variant.value',
        'md_variant.name',
        'md_variant.isactive'
    ];
    protected $column_search = [
        'md_variant.value',
        'md_variant.name',
        'md_variant.isactive'
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
