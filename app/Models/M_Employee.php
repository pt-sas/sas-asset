<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;

class M_Employee extends Model
{
	protected $table                = 'md_employee';
	protected $primaryKey           = 'md_employee_id';
	protected $allowedFields        = [
		'value',
		'name',
		'description',
		'md_branch_id',
		'md_division_id',
		'md_room_id',
		'sys_user_id',
		'isactive',
		'created_by',
		'updated_by'
	];
	protected $useTimestamps        = true;
	protected $returnType = 'App\Entities\Employee';
	protected $column_order = [
		'', // Hide column
		'', // Number column
		'md_employee.value',
		'md_employee.name',
		'md_branch.name',
		'md_division.name',
		'md_room.name',
		'md_employee.isactive'
	];
	protected $column_search = [
		'md_employee.value',
		'md_employee.name',
		'md_branch.name',
		'md_division.name',
		'md_room.name',
		'md_employee.isactive'
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
                md_branch.name as branch,
                md_division.name as division,
                md_room.name as room,
				sys_user.name as user';

		return $sql;
	}

	public function getJoin()
	{
		$sql = [
			$this->setDataJoin('md_branch', 'md_branch.md_branch_id = ' . $this->table . '.md_branch_id', 'left'),
			$this->setDataJoin('md_division', 'md_division.md_division_id = ' . $this->table . '.md_division_id', 'left'),
			$this->setDataJoin('md_room', 'md_room.md_room_id = ' . $this->table . '.md_room_id', 'left'),
			$this->setDataJoin('sys_user', 'sys_user.sys_user_id = ' . $this->table . '.sys_user_id', 'left')
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
