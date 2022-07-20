<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;
use App\Models\M_MovementDetail;

class M_Movement extends Model
{
	protected $table                = 'trx_movement';
	protected $primaryKey           = 'trx_movement_id';
	protected $allowedFields        = [
		'documentno',
		'movementdate',
		'description',
		'docstatus',
		'created_by',
		'updated_by'
	];
	protected $useTimestamps        = true;
	protected $returnType 			= 'App\Entities\Movement';
	protected $allowCallbacks		= true;
	protected $beforeInsert			= [];
	protected $afterInsert			= ['createDetail'];
	protected $beforeUpdate			= [];
	protected $afterUpdate			= ['createDetail'];
	protected $beforeDelete			= [];
	protected $afterDelete			= ['deleteDetail'];
	protected $column_order = [
		'', // Hide column
		'', // Number column
		'trx_movement.documentno',
		'trx_movement.movementdate',
		'trx_movement.docstatus',
		'sys_user.name',
		'trx_movement.description'
	];
	protected $column_search = [
		'trx_movement.documentno',
		'trx_movement.movementdate',
		'trx_movement.docstatus',
		'sys_user.name',
		'trx_movement.description'
	];
	protected $order = ['created_at' => 'DESC'];
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
			'sys_user.name as createdby';

		return $sql;
	}

	public function getJoin()
	{
		$sql = [
			$this->setDataJoin('sys_user', 'sys_user.sys_user_id = ' . $this->table . '.created_by', 'left')
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

	public function getInvNumber()
	{
		$month = date('m');

		$this->builder->select('MAX(RIGHT(documentno,4)) AS documentno');
		$this->builder->where("DATE_FORMAT(movementdate, '%m')", $month);
		$sql = $this->builder->get();

		$code = "";
		if ($sql->getNumRows() > 0) {
			foreach ($sql->getResult() as $row) {
				$doc = ((int)$row->documentno + 1);
				$code = sprintf("%04s", $doc);
			}
		} else {
			$code = "0001";
		}

		$prefix = "MV" . date('ym') . $code;

		return $prefix;
	}

	public function createDetail(array $rows)
	{
		$moveDetail = new M_MovementDetail();

		$post = $this->request->getVar();

		if (isset($post['table'])) {
			$post['trx_movement_id'] = $rows['id'];
			$moveDetail->create($post);
		}
	}

	public function deleteDetail(array $rows)
	{
		$moveDetail = new M_MovementDetail();
		$moveDetail->where($this->primaryKey, $rows['id'])->delete();
	}
}
