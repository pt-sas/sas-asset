<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;

class M_MovementDetail extends Model
{
	protected $table      = 'trx_movement_detail';
	protected $primaryKey = 'trx_movement_detail_id';
	protected $allowedFields = [
		'trx_movement_id',
		'assetcode',
		'md_product_id',
		'employee_from',
		'employee_to',
		'branch_from',
		'branch_to',
		'division_from',
		'division_to',
		'room_from',
		'room_to',
		'created_by',
		'updated_by',
		'description',
		'md_status_id',
		'ref_movement_detail_id',
		'isaccept'
	];
	protected $useTimestamps = true;
	protected $returnType = 'App\Entities\MovementDetail';
	protected $db;
	protected $builder;
	protected $request;

	public function __construct(RequestInterface $request)
	{
		parent::__construct();
		$this->db = db_connect();
		$this->builder = $this->db->table($this->table);
		$this->request = $request;
	}

	public function detail($field = null, $where = null)
	{
		$this->builder->select(
			$this->table . '.*,' .
				'md_status.name as status,
				trx_movement.docstatus'
		);

		$this->builder->join('md_status', 'md_status.md_status_id = ' . $this->table . '.md_status_id', 'left');
		$this->builder->join('trx_movement', 'trx_movement.trx_movement_id = ' . $this->table . '.trx_movement_id', 'right');

		if (!empty($where)) {
			$this->builder->where($field, $where);
		}

		return $this->builder->get();
	}

	public function getMovementDetail()
	{
		$sql = '
        trx_movement.documentno,
        trx_movement.movementdate,
        md_product.name,
        md_employee.name AS employeefrom,
        et.name AS employeeto,
        md_division.name AS divisionfrom,
        dt.name AS divisionto,
        md_branch.name AS branchfrom,
        bt.name AS branchto,
        md_room.name AS roomfrom,
        rt.name AS roomto,
        md_status.name AS status';

		return $sql;
	}

	public function getJoinDetail()
	{
		$sql = [
			$this->setDataJoin('trx_movement', 'trx_movement.trx_movement_id =' . $this->table . '.trx_movement_id', 'left'),
			$this->setDataJoin('md_product', 'md_product.md_product_id =' . $this->table . '.md_product_id', 'left'),
			$this->setDataJoin('md_employee', 'md_employee.md_employee_id =' . $this->table . '.employee_from', 'left'),
			$this->setDataJoin('md_employee et', 'et.md_employee_id =' . $this->table . '.employee_to', 'left'),
			$this->setDataJoin('md_division', 'md_division.md_division_id =' . $this->table . '.division_from', 'left'),
			$this->setDataJoin('md_division dt', 'dt.md_division_id =' . $this->table . '.division_to', 'left'),
			$this->setDataJoin('md_branch', 'md_branch.md_branch_id =' . $this->table . '.branch_from', 'left'),
			$this->setDataJoin('md_branch bt', 'bt.md_branch_id =' . $this->table . '.branch_to', 'left'),
			$this->setDataJoin('md_room', 'md_room.md_room_id =' . $this->table . '.room_from', 'left'),
			$this->setDataJoin('md_room rt', 'md_room.md_room_id =' . $this->table . '.room_to', 'left'),
			$this->setDataJoin('md_status', 'md_status.md_status_id =' . $this->table . '.md_status_id', 'left'),
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
