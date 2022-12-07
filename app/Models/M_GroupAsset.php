<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;

class M_GroupAsset extends Model
{
    protected $table      = 'md_groupasset';
    protected $primaryKey = 'md_groupasset_id';
    protected $allowedFields = [
        'value',
        'name',
        'description',
        'initialcode',
        'usefullife',
        'isactive',
        'created_by',
        'updated_by',
        'md_sequence_id',
        'depreciationtype'
    ];
    protected $useTimestamps = true;
    protected $returnType = 'App\Entities\GroupAsset';
    protected $column_order = [
        '', // Hide column
        '', // Number column
        'md_groupasset.value',
        'md_groupasset.name',
        'md_groupasset.description',
        'md_groupasset.initialcode',
        'md_groupasset.usefullife',
        'md_groupasset.isactive'
    ];
    protected $column_search = [
        'md_groupasset.value',
        'md_groupasset.name',
        'md_groupasset.description',
        'md_groupasset.initialcode',
        'md_groupasset.usefullife',
        'md_groupasset.isactive'
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

    public function getSelect()
    {
        $sql = $this->table . '.*,
        sys_ref_detail.name as depreciationtype';

        return $sql;
    }

    public function getJoin()
    {
        //* DepreciationType
        $defaultID = 9;

        $sql = [
            $this->setDataJoin('sys_ref_detail', 'sys_ref_detail.sys_reference_id = ' . $defaultID . ' AND sys_ref_detail.value = ' . $this->table . '.depreciationtype', 'left'),
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
