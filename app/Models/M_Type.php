<?php

namespace App\Models;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Model;

class M_Type extends Model
{
    protected $table      = 'md_type';
    protected $primaryKey = 'md_type_id';
    protected $allowedFields = [
        'value',
        'name',
        'isactive',
        'created_by',
        'updated_by',
        'md_subcategory_id'
    ];
    protected $useTimestamps = true;
    protected $returnType = 'App\Entities\Type';
    protected $column_order = [
        '', // Hide column
        '', // Number column
        'md_type.value',
        'md_type.name',
        'md_subcategory.name',
        'md_type.isactive'
    ];
    protected $column_search = [
        'md_type.value',
        'md_type.name',
        'md_subcategory.name',
        'md_type.isactive'
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
            md_subcategory.name as subcategory';

        return $sql;
    }

    public function getJoin()
    {
        $sql = [
            $this->setDataJoin('md_subcategory', 'md_subcategory.md_subcategory_id = ' . $this->table . '.md_subcategory_id', 'left')
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

    public function getListType($field = null, $where = null, $like = [], $orderBy = [])
    {
        $this->builder->select(
            $this->table . '.*,
            md_subcategory.name as subcategory'
        );

        $this->builder->join('md_subcategory', 'md_subcategory.md_subcategory_id = ' . $this->table . '.md_subcategory_id', 'left');

        if (!empty($where))
            $this->builder->where($field, $where);

        if (count($like) > 0)
            $this->builder->like($like[0], $like[1]);

        if (count($orderBy) > 0)
            $this->builder->orderBy($orderBy[0], $orderBy[1]);

        return $this->builder->get();
    }
}
