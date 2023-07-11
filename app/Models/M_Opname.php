<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;

class M_Opname extends Model
{
	protected $table                = 'trx_opname';
	protected $primaryKey           = 'trx_opname_id';
	protected $allowedFields        = [
		'documentno',
		'opnamedate',
		'docstatus',
		'md_branch_id',
		'md_room_id',
		'md_employee_id',
		'description',
		'startdate',
		'enddate',
		'created_by',
		'updated_by'
	];
	protected $useTimestamps        = true;
	protected $returnType 			= 'App\Entities\Opname';
	protected $allowCallbacks		= true;
	protected $beforeInsert			= [];
	protected $afterInsert			= [];
	protected $beforeUpdate			= [];
	protected $afterUpdate			= [];
	protected $beforeDelete			= [];
	protected $afterDelete			= ['deleteDetail'];
	protected $column_order = [
		'', // Hide column
		'', // Number column
		'trx_opname.documentno',
		'trx_opname.opnamedate',
		'md_branch.name',
		'md_room.name',
		'md_employee.name',
		'trx_opname.docstatus',
		'sys_user.name',
		'trx_opname.description'
	];
	protected $column_search = [
		'trx_opname.documentno',
		'trx_opname.opnamedate',
		'md_branch.name',
		'md_room.name',
		'md_employee.name',
		'trx_opname.docstatus',
		'sys_user.name',
		'trx_opname.description'

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
			'sys_user.name as createdby,
			md_branch.name as branch,
			md_room.name as room,
			md_employee.name as employee';

		return $sql;
	}

	public function getJoin()
	{
		$sql = [
			$this->setDataJoin('sys_user', 'sys_user.sys_user_id = ' . $this->table . '.created_by', 'left'),
			$this->setDataJoin('md_branch', 'md_branch.md_branch_id = ' . $this->table . '.md_branch_id', 'left'),
			$this->setDataJoin('md_room', 'md_room.md_room_id = ' . $this->table . '.md_room_id', 'left'),
			$this->setDataJoin('md_employee', 'md_employee.md_employee_id = ' . $this->table . '.md_employee_id', 'left')
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
		$post = $this->request->getPost();

		$yearMonth = date("ym", strtotime($post['opnamedate']));
		$month = date("m", strtotime($post['opnamedate']));

		$this->builder->select('MAX(RIGHT(documentno,4)) AS documentno');
		$this->builder->where("DATE_FORMAT(opnamedate, '%m')", $month);
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

		$prefix = "OP" . $yearMonth . $code;

		return $prefix;
	}

	public function deleteDetail(array $rows)
	{
		$mODetail = new M_OpnameDetail($this->request);
		$mODetail->where($this->primaryKey, $rows['id'])->delete();
	}

	public function getSelectOpname()
	{
		$sql = $this->table . '.*,' .
			'md_branch.name AS branch,
			md_room.name AS room,
			md_employee.name AS employee,
			trx_opname_detail.assetcode,
			trx_opname_detail.isbranch AS check_branch,
			mdb.name AS branch_scan,
			trx_opname_detail.isroom AS check_room,
			mdr.name AS room_scan,
			trx_opname_detail.isemployee AS check_employee,
			mde.name AS employee_scan,
			trx_opname_detail.isnew,
			trx_opname_detail.nocheck AS noc,
			sys_user.name AS opnamer,
			md_product.name AS product';

		return $sql;
	}

	public function getJoinOpname()
	{
		$sql = [
			$this->setDataJoin('trx_opname_detail', 'trx_opname_detail.trx_opname_id = ' . $this->table . '.trx_opname_id'),
			$this->setDataJoin('md_branch', 'md_branch.md_branch_id = ' . $this->table . '.md_branch_id', 'left'),
			$this->setDataJoin('md_room', 'md_room.md_room_id = ' . $this->table . '.md_room_id', 'left'),
			$this->setDataJoin('md_employee', 'md_employee.md_employee_id = ' . $this->table . '.md_employee_id', 'left'),
			$this->setDataJoin('trx_inventory', 'trx_opname_detail.assetcode = trx_inventory.assetcode', 'left'),
			$this->setDataJoin('md_branch mdb', 'mdb.md_branch_id = trx_inventory.md_branch_id', 'left'),
			$this->setDataJoin('md_room mdr', 'mdr.md_room_id = trx_inventory.md_room_id', 'left'),
			$this->setDataJoin('md_employee mde', 'mde.md_employee_id = trx_inventory.md_employee_id', 'left'),
			$this->setDataJoin('sys_user', 'sys_user.sys_user_id = ' . $this->table . '.created_by', 'left'),
			$this->setDataJoin('md_product', 'md_product.md_product_id = trx_inventory.md_product_id', 'left'),
		];

		return $sql;
	}
}
