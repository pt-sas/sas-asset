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
		'movementtype',
		'md_branch_id',
		'md_branchto_id',
		'md_division_id',
		'sys_wfscenario_id',
		'ref_movement_id',
		'created_by',
		'updated_by'
	];
	protected $useTimestamps        = true;
	protected $returnType 			= 'App\Entities\Movement';
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
			'sys_user.name as createdby,
			bfrom.name as branch,
			bto.name as branchto,
			md_division.name as division,
			ref.documentno as referenceno';

		return $sql;
	}

	public function getJoin()
	{
		$sql = [
			$this->setDataJoin('trx_movement ref', 'ref.trx_movement_id = ' . $this->table . '.ref_movement_id', 'left'),
			$this->setDataJoin('sys_user', 'sys_user.sys_user_id = ' . $this->table . '.created_by', 'left'),
			$this->setDataJoin('md_branch bfrom', 'bfrom.md_branch_id = ' . $this->table . '.md_branch_id', 'left'),
			$this->setDataJoin('md_branch bto', 'bto.md_branch_id = ' . $this->table . '.md_branchto_id', 'left'),
			$this->setDataJoin('md_division', 'md_division.md_division_id = ' . $this->table . '.md_division_id', 'left')
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

	public function getInvNumber($type)
	{
		$month = date('m');

		$this->builder->select('MAX(RIGHT(documentno,4)) AS documentno');
		$this->builder->where([
			"DATE_FORMAT(movementdate, '%m')" 	=> $month,
			"movementtype"						=> $type
		]);
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

		$prefix = $type === "KIRIM" ? "MK" : "MT";

		$prefix .= date('ym') . $code;

		return $prefix;
	}

	public function deleteDetail(array $rows)
	{
		$moveDetail = new M_MovementDetail($this->request);
		$moveDetail->where($this->primaryKey, $rows['id'])->delete();
	}
}
