<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;

class M_Category extends Model
{
    protected $table      = 'md_category';
    protected $primaryKey = 'md_category_id';
    protected $allowedFields = [
        'value',
        'name',
        'initialcode',
        'md_groupasset_id',
        'isactive',
        'created_by',
        'updated_by',
        'pic'
    ];
    protected $useTimestamps = true;
    protected $returnType = 'App\Entities\Category';
    protected $column_order = [
        '', // Hide column
        '', // Number column
        'md_category.value',
        'md_category.name',
        'md_category.initialcode',
        'md_groupasset.name',
        'md_employee.name',
        'md_category.isactive'
    ];
    protected $column_search = [
        'md_category.value',
        'md_category.name',
        'md_category.initialcode',
        'md_groupasset.name',
        'md_employee.name',
        'md_category.isactive'
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
        md_groupasset.name as groupasset,
        md_employee.name as pic';

        return $sql;
    }

    public function getJoin()
    {
        $sql = [
            $this->setDataJoin('md_groupasset', 'md_groupasset.md_groupasset_Id = ' . $this->table . '.md_groupasset_Id', 'left'),
            $this->setDataJoin('md_employee', 'md_employee.md_employee_id = ' . $this->table . '.pic', 'left')
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

    public function getSeqNumber()
    {
        $number = $this->builder->countAll();

        $number += 1;

        while (strlen($number) < 5) {
            $number = "0" . $number;
        }

        // Check exist value number
        $row = $this->builder->where("MID(value, 3, 5)", $number)->get()->getRow();

        if ($row) {
            $number += 1;

            while (strlen($number) < 5) {
                $number = "0" . $number;
            }
        }

        return $number;
    }

    public function getByProduct($field, $param)
    {
        $this->builder->join('md_product', 'md_product.md_category_id = ' . $this->table . '.md_category_id', 'left');
        $this->builder->where($field, $param);
        return $this->builder->get()->getRow();
    }
}
