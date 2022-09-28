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
	];
	protected $useTimestamps = true;
	protected $returnType = 'App\Entities\Movementdetail';
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
}
