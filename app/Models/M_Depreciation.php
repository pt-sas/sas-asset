<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;

class M_Depreciation extends Model
{
    protected $table      = 'trx_depreciation';
    protected $primaryKey = 'trx_depreciation_id';
    protected $useTimestamps = true;
    protected $returnType = 'App\Entities\Depreciation';
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

    public function doInsert(array $data)
    {
        if (!is_array($data))
            return false;

        if (empty($data))
            return false;

        if (is_array($data)) {
            $result = $this->builder->insertBatch($data);

            return $result > 0 ? true : false;
        }

        return false;
    }

    public function getSelect()
    {
        $sql = $this->table . '.*,
        sys_ref_detail.name as depreciationtype,
        md_product.name as product';

        return $sql;
    }

    public function getJoin()
    {
        //* DepreciationType
        $defaultID = 9;

        $sql = [
            $this->setDataJoin('sys_ref_detail', 'sys_ref_detail.sys_reference_id = ' . $defaultID . ' AND sys_ref_detail.value = ' . $this->table . '.depreciationtype', 'left'),
            $this->setDataJoin('trx_inventory', 'trx_inventory.assetcode = ' . $this->table . '.assetcode', 'left'),
            $this->setDataJoin('md_product', 'md_product.md_product_id = trx_inventory.md_product_id', 'left'),
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
