<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;

class M_DepreciationDetail extends Model
{
    protected $table            = 'trx_depreciation_detail';
    protected $primaryKey       = 'trx_depreciation_detail_id';
    protected $allowedFields    = [
        'assetcode',
        'transactiondate',
        'totalyear',
        'period',
        'residualvalue',
        'costdepreciation',
        'newcostdepreciation',
        'accumulateddepreciation',
        'newaccumulateddepreciation',
        'bookvalue',
        'newbookvalue',
        'currentmonth',
        'depreciationtype',
        'created_by',
        'updated_by'
    ];
    protected $useTimestamps    = true;
    protected $returnType       = 'App\Entities\DepreciationDetail';
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
        $sql = $this->table . ".*,
        sys_ref_detail.name as depreciationtype,
        md_product.name as product,
        md_branch.name as branch,
        md_division.name as division,
        md_room.room as room,
        md_room.description as room_name,
        ABS(PERIOD_DIFF(
                DATE_FORMAT(CONCAT(" . $this->table . ".period, '-01'), '%Y%m'),
                DATE_FORMAT(CONCAT(
                    (
                        SELECT MAX(tdd.period)
                        FROM trx_depreciation_detail tdd
                        WHERE tdd.assetcode = " . $this->table . ".assetcode
                    ),
                    '-01'),'%Y%m'
                )
            )
        ) as sisa_waktu";

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
            $this->setDataJoin('md_branch', 'md_branch.md_branch_id = trx_inventory.md_branch_id', 'left'),
            $this->setDataJoin('md_room', 'md_room.md_room_id = trx_inventory.md_room_id', 'left'),
            $this->setDataJoin('md_division', 'md_division.md_division_id = trx_inventory.md_division_id', 'left'),
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
