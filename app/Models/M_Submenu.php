<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;

class M_Submenu extends Model
{
    protected $table      = 'sys_submenu';
    protected $primaryKey = 'sys_submenu_id';
    protected $allowedFields = [
        'name',
        'sequence',
        'url',
        'status',
        'sys_menu_id',
        'isactive',
        'created_by',
        'updated_by'
    ];
    protected $useTimestamps = true;
    protected $returnType = 'App\Entities\Submenu';
    protected $column_order = [
        '', // Hide column
        '', // Number column
        'sys_submenu.name',
        'sys_menu.name',
        'sys_submenu.url',
        'sys_submenu.sequence',
        'sys_submenu.isactive'
    ];
    protected $column_search = [
        'sys_submenu.name',
        'sys_menu.name',
        'sys_submenu.url',
        'sys_submenu.sequence',
        'sys_submenu.isactive'
    ];
    protected $order = ['name' => 'ASC'];
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
                    sys_menu.name as parent';

        return $sql;
    }

    public function getJoin()
    {
        $sql = [
            $this->setDataJoin('sys_menu', 'sys_menu.sys_menu_id = ' . $this->table . '.sys_menu_id', 'left')
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

    public function detail($field = null, $where = null)
    {
        $this->builder->select(
            $this->table . '.*,
            sys_menu.name as parent'
        );

        $this->builder->join('sys_menu', 'sys_menu.sys_menu_id = ' . $this->table . '.sys_menu_id', 'left');

        if (!empty($where))
            $this->builder->where($field, $where);

        $query = $this->builder->get();
        return $query;
    }
}
