<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;

class M_Responsible extends Model
{
    protected $table      = 'sys_wfresponsible';
    protected $primaryKey = 'sys_wfresponsible_id';
    protected $allowedFields = [
        'name',
        'description',
        'responsibletype',
        'sys_role_id',
        'sys_user_id',
        'isactive',
        'created_by',
        'updated_by'
    ];
    protected $useTimestamps = true;
    protected $returnType = 'App\Entities\Responsible';
    protected $column_order = [
        '', // Hide column
        '', // Number column
        'sys_wfresponsible.name',
        'sys_wfresponsible.description',
        'sys_ref_detail.name',
        'sys_role.name',
        'sys_user.name',
        'sys_wfresponsible.isactive'
    ];
    protected $column_search = [
        'sys_wfresponsible.name',
        'sys_wfresponsible.description',
        'sys_ref_detail.name',
        'sys_role.name',
        'sys_user.name',
        'sys_wfresponsible.isactive'
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
        $sql = $this->table . '.*,' .
            'sys_role.name as role,
			sys_user.name as user,
            sys_ref_detail.name as res_type';

        return $sql;
    }

    public function getJoin()
    {
        //* WF_Participant Type
        $defaultID = 3;

        $sql = [
            $this->setDataJoin('sys_role', 'sys_role.sys_role_id = ' . $this->table . '.sys_role_id', 'left'),
            $this->setDataJoin('sys_user', 'sys_user.sys_user_id = ' . $this->table . '.sys_user_id', 'left'),
            $this->setDataJoin('sys_ref_detail', 'sys_ref_detail.sys_reference_id = ' . $defaultID . ' AND sys_ref_detail.value = ' . $this->table . '.responsibletype', 'left'),
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
