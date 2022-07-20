<?php

namespace App\Models;

use CodeIgniter\Model;

use CodeIgniter\HTTP\RequestInterface;

class M_Supplier extends Model
{
    protected $table      = 'md_supplier';
    protected $primaryKey = 'md_supplier_id';
    protected $allowedFields = [
        'value',
        'name',
        'description',
        'address',
        'owner',
        'email',
        'phone',
        'isvendor',
        'isservice',
        'isactive',
        'created_by',
        'updated_by'
    ];
    protected $useTimestamps = true;
    protected $returnType = 'App\Entities\Supplier';
    protected $column_order = [
        '', // Hide column
        '', // Number column
        'md_supplier.value',
        'md_supplier.name',
        'md_supplier.address',
        'md_supplier.owner',
        'md_supplier.email',
        'md_supplier.phone',
        'md_supplier.isvendor',
        'md_supplier.isservice',
        'md_supplier.isactive'
    ];
    protected $column_search = [
        'md_supplier.value',
        'md_supplier.name',
        'md_supplier.address',
        'md_supplier.owner',
        'md_supplier.email',
        'md_supplier.phone',
        'md_supplier.isvendor',
        'md_supplier.isservice',
        'md_supplier.isactive'
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
