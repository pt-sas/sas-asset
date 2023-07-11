<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;

class M_OpnameDetail extends Model
{
	protected $table      = 'trx_opname_detail';
	protected $primaryKey = 'trx_opname_detail_id';
	protected $allowedFields = [
		'trx_opname_id',
		'assetcode',
		'md_product_id',
		'isbranch',
		'isroom',
		'isemployee',
		'isnew',
		'nocheck',
		'created_by',
		'updated_by'
	];
	protected $useTimestamps 	= true;
	protected $returnType 		= 'App\Entities\OpnameDetail';
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

	/**
	 * Change value of field data
	 *
	 * @param array $data Data
	 * @return array
	 */
	public function doChangeValueField(array $data, int $foreignKey): array
	{
		$result = [];

		foreach ($data as $key => $row) :
			$row['isbranch'] = $row['isbranch'] ?? 'N';
			$row['isroom'] = $row['isroom'] ?? 'N';
			$row['isemployee'] = $row['isemployee'] ?? 'N';

			if (!isset($row[$this->primaryKey]) && !empty($foreignKey)) {
				$line = $this->where([
					"trx_opname_id"	=> $foreignKey,
					"assetcode"		=> $row['assetcode']
				])->first();

				$row[$this->primaryKey] = $line->{$this->primaryKey};
			}

			$result[] = $row;
		endforeach;

		return $result;
	}

	public function getSelectOpname()
	{
		$sql = $this->table . '.assetcode,' .
			$this->table . '.isbranch AS check_branch,' .
			$this->table . '.isemployee AS check_employee,' .
			$this->table . '.isroom AS check_room,' .
			$this->table . '.isnew,' .
			$this->table . '.nocheck AS noc,' .
			'trx_opname.*,
			md_branch.name AS branch,
			md_room.name AS room,
			md_employee.name AS employee,
			mdb.name AS branch_scan,
			mdr.name AS room_scan,
			mde.name AS employee_scan,
			sys_user.name AS opnamer,
			md_product.name AS product';

		return $sql;
	}

	public function getJoinOpname()
	{
		$sql = [
			$this->setDataJoin('trx_opname', 'trx_opname.trx_opname_id = ' . $this->table . '.trx_opname_id'),
			$this->setDataJoin('md_branch', 'md_branch.md_branch_id = trx_opname.md_branch_id', 'left'),
			$this->setDataJoin('md_room', 'md_room.md_room_id = trx_opname.md_room_id', 'left'),
			$this->setDataJoin('md_employee', 'md_employee.md_employee_id = trx_opname.md_employee_id', 'left'),
			$this->setDataJoin('trx_inventory', 'trx_inventory.assetcode = ' . $this->table . '.assetcode', 'left'),
			$this->setDataJoin('md_branch mdb', 'mdb.md_branch_id = trx_inventory.md_branch_id', 'left'),
			$this->setDataJoin('md_room mdr', 'mdr.md_room_id = trx_inventory.md_room_id', 'left'),
			$this->setDataJoin('md_employee mde', 'mde.md_employee_id = trx_inventory.md_employee_id', 'left'),
			$this->setDataJoin('sys_user', 'sys_user.sys_user_id = trx_opname.created_by', 'left'),
			$this->setDataJoin('md_product', 'md_product.md_product_id = trx_inventory.md_product_id', 'left'),
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
