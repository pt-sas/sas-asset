<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;

class M_Room extends Model
{
    protected $table      = 'md_room';
    protected $primaryKey = 'md_room_id';
    protected $allowedFields = [
        'value',
        'name',
        'description',
        'md_branch_id',
        'isactive',
        'created_by',
        'updated_by'
    ];
    protected $useTimestamps = true;
    protected $returnType = 'App\Entities\Room';
    protected $column_order = [
        '', // Hide column
        '', // Number column
        'md_room.value',
        'md_room.name',
        'md_branch.name',
        'md_room.isactive',
    ];
    protected $column_search = [
        'md_room.value',
        'md_room.name',
        'md_branch.name',
        'md_room.isactive',
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
                md_branch.name as branch,';

        return $sql;
    }

    public function getJoin()
    {
        $sql = [
            $this->setDataJoin('md_branch', 'md_branch.md_branch_id = ' . $this->table . '.md_branch_id', 'left')
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
