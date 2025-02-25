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
        'product_category_id',
        'md_brand_id',
        'md_category_id',
        'md_subcategory_id',
        'md_type_id',
        'md_variant_id',
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
        'pc.name',
        'md_sparepart.isactive'
    ];
    protected $column_search    = [
        'md_sparepart.value',
        'md_sparepart.name',
        'pc.name',
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
            pc.name as category';

        return $sql;
    }

    public function getJoin()
    {
        $sql = [
            $this->setDataJoin('md_category pc', 'pc.md_category_id = ' . $this->table . '.product_category_id', 'left'),
            $this->setDataJoin('md_brand pb', 'pb.md_brand_id = ' . $this->table . '.md_brand_id', 'left'),
            $this->setDataJoin('md_category psc', 'psc.md_category_id = ' . $this->table . '.md_category_id', 'left'),
            $this->setDataJoin('md_type pt', 'pt.md_type_id = ' . $this->table . '.md_type_id', 'left'),
            $this->setDataJoin('md_variant pv', 'pv.md_variant_id = ' . $this->table . '.md_variant_id', 'left')

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