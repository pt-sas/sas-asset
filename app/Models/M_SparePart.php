<?php

namespace App\Models;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Model;

class M_SparePart extends Model
{
    protected $table            = 'md_sparepart';
    protected $primaryKey       = 'md_sparepart_id';
    protected $allowedFields    = [
        'value',
        'name',
        'md_product_id',
        'isactive',
        'created_by',
        'updated_by',
    ];
    protected $useTimestamps    = true;
    protected $returnType       = 'App\Entities\SparePart';
    protected $column_order     = [
        '', // Hide column
        '', // Number column
        'md_sparepart.value',
        'md_sparepart.name',
        'md_product.name',
        'md_sparepart.isactive'
    ];
    protected $column_search    = [
        'md_sparepart.value',
        'md_sparepart.name',
        'md_product.name',
        'md_sparepart.isactive'
    ];
    protected $order            = ['value' => 'ASC'];
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

    public function getSelect()
    {
        $sql = $this->table . '.*,
            md_product.name as product';

        return $sql;
    }

    public function getJoin()
    {
        $sql = [
            $this->setDataJoin('md_product', 'md_product.md_product_id = ' . $this->table . '.md_product_id', 'left')
        ];

        return $sql;
    }

    private function setDataJoin($tableJoin, $columnJoin, $typeJoin = "inner")
    {
        return [
            "tableJoin" => $tableJoin,
            "columnJoin" => $columnJoin,
            "typeJoin" => $typeJoin
        ];
    }
}
