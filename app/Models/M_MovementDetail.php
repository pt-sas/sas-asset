<?php

namespace App\Models;

use CodeIgniter\Model;

use CodeIgniter\HTTP\RequestInterface;

class M_MovementDetail extends Model
{
	protected $table      = 'trx_movement_detail';
	protected $primaryKey = 'trx_movement_detail_id';
	protected $allowedFields = [
		'trx_receipt_id',
		'md_product_id',
		'description',
		'employee_from',
		'employee_to',
		'branch_from',
		'branch_to',
		'division_from',
		'division_to',
		'room_from',
		'room_to',
		'created_by',
		'updated_by'
	];
	protected $useTimestamps = true;
	protected $returnType = 'App\Entities\Movementdetail';
	protected $db;
	protected $builder;

	public function __construct()
	{
		parent::__construct();
		$this->db = db_connect();
		$this->builder = $this->db->table($this->table);
	}

	public function create($post)
	{
		$table = json_decode($post['table']);

		foreach ($table as $row) :
			$data = [
				'assetcode'			=> $row[0]->assetcode,
				'md_product_id'     => $row[1]->product_id,
				'md_status_id'	    => $row[2]->status_id,
				'employee_from'     => $row[3]->employee_from,
				'employee_to'     	=> $row[4]->employee_to,
				'branch_from'     	=> $row[5]->branch_from,
				'branch_to'    		=> $row[6]->branch_to,
				'division_from'     => $row[7]->division_from,
				'division_to'     	=> $row[8]->division_to,
				'room_from'    		=> $row[9]->room_from,
				'room_to'    		=> $row[10]->room_to,
				'description'       => $row[11]->desc,
				'trx_movement_id'   => $post['trx_movement_id']
			];

			if (!empty($row[12]->delete)) {
				$data['updated_at'] = date('Y-m-d H:i:s');
				$data['updated_by'] = session()->get('sys_user_id');

				$result = $this->builder->where($this->primaryKey, $row[12]->delete)->update($data);
			} else {
				$data['created_at'] = date('Y-m-d H:i:s');
				$data['created_by'] = session()->get('sys_user_id');
				$data['updated_at'] = date('Y-m-d H:i:s');
				$data['updated_by'] = session()->get('sys_user_id');

				$result = $this->builder->insert($data);
			}
		endforeach;

		return $result;
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
