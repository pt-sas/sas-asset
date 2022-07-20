<?php

namespace App\Models;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Model;

class M_Subcategory extends Model
{
    protected $table      = 'md_subcategory';
    protected $primaryKey = 'md_subcategory_id';
    protected $allowedFields = [
        'value',
        'name',
        'isactive',
        'created_by',
        'updated_by',
        'md_category_id'
    ];
    protected $useTimestamps = true;
    protected $returnType = 'App\Entities\Subcategory';
    protected $column_order = [
        '', // Hide column
        '', // Number column
        'md_subcategory.value',
        'md_subcategory.name',
        'md_category.name',
        'md_subcategory.isactive'
    ];
    protected $column_search = [
        'md_subcategory.value',
        'md_subcategory.name',
        'md_category.name',
        'md_subcategory.isactive'
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
                md_category.name as category,';

        return $sql;
    }

    public function getJoin()
    {
        $sql = [
            $this->setDataJoin('md_category', 'md_category.md_category_id = ' . $this->table . '.md_category_id', 'left')
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

    public function getListSub($field = null, $where = null, $like = [], $orderBy = [])
    {
        $this->builder->select(
            $this->table . '.*,
                md_category.name as category,'
        );

        $this->builder->join('md_category', 'md_category.md_category_id = ' . $this->table . '.md_category_id', 'left');

        if (!empty($where))
            $this->builder->where($field, $where);

        if (count($like) > 0)
            $this->builder->like($like[0], $like[1]);

        if (count($orderBy) > 0)
            $this->builder->orderBy($orderBy[0], $orderBy[1]);

        return $this->builder->get();
    }
}
