<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;
use App\Models\M_MovementDetail;
use stdClass;

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
		'md_division_id',
		'md_branchto_id',
		'md_divisionto_id',
		'movementstatus',
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
		'sys_ref_detail.name',
		'ref.documentno',
		'bfrom.name',
		'bto.name',
		'md_division.name',
		'md_status.name',
		'trx_movement.docstatus',
		'sys_user.name',
		'trx_movement.description'
	];
	protected $column_search = [
		'trx_movement.documentno',
		'trx_movement.movementdate',
		'sys_ref_detail.name',
		'ref.documentno',
		'bfrom.name',
		'bto.name',
		'md_division.name',
		'md_status.name',
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
			md_division.name as divisionto,
			ref.documentno as referenceno,
			sys_ref_detail.name as move_type,
			md_status.name as status';

		return $sql;
	}

	public function getJoin()
	{
		//* WF_Participant Type
		$defaultID = 11;

		$sql = [
			$this->setDataJoin('trx_movement ref', 'ref.trx_movement_id = ' . $this->table . '.ref_movement_id', 'left'),
			$this->setDataJoin('sys_user', 'sys_user.sys_user_id = ' . $this->table . '.created_by', 'left'),
			$this->setDataJoin('md_branch bfrom', 'bfrom.md_branch_id = ' . $this->table . '.md_branch_id', 'left'),
			$this->setDataJoin('md_branch bto', 'bto.md_branch_id = ' . $this->table . '.md_branchto_id', 'left'),
			$this->setDataJoin('md_division', 'md_division.md_division_id = ' . $this->table . '.md_divisionto_id', 'left'),
			$this->setDataJoin('md_status', 'md_status.md_status_id = ' . $this->table . '.movementstatus', 'left'),
			$this->setDataJoin('sys_ref_detail', 'sys_ref_detail.sys_reference_id = ' . $defaultID . ' AND sys_ref_detail.value = ' . $this->table . '.movementtype', 'left')
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
		$post = $this->request->getPost();

		$yearMonth = date("ym", strtotime($post['movementdate']));
		$month = date("m", strtotime($post['movementdate']));

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

		$prefix .= $yearMonth . "-" . $code;

		return $prefix;
	}

	public function deleteDetail(array $rows)
	{
		$moveDetail = new M_MovementDetail($this->request);
		$moveDetail->where($this->primaryKey, $rows['id'])->delete();
	}

	public function getColumnArr($column)
	{
		$list = $this->findAll();

		$result = [];

		foreach ($list as $value) :
			$result[] = $value->{$column};
		endforeach;

		return $result;
	}

	public function createDetail($rows)
	{
		$mTransaction = new M_Transaction();
		$mInventory = new M_Inventory($this->request);
		$mMoveDetail = new M_MovementDetail($this->request);

		if (isset($post['docaction'])) {
			$row = $this->find($post['id']);
			$line = $mMoveDetail->where($this->primaryKey, $post['id'])->findAll();

			// if ($post['docaction'] === "VO" && $row->getMovementType() === "TERIMA") {
			//* Passing data to table transaction
			$arrMoveIn = [];
			$arrMoveOut = [];
			foreach ($line as $key => $value) :
				//? Data movement to
				$arrOut = new stdClass();
				$arrOut->assetcode = $value->assetcode;
				$arrOut->md_product_id = $value->md_product_id;
				$arrOut->md_employee_id = $value->employee_to;
				$arrOut->md_room_id = $value->room_to;
				$arrOut->transactiontype = $this->Movement_Out;
				$arrOut->transactiondate = date("Y-m-d");
				$arrOut->qtyentered = -1;
				$arrOut->trx_movement_detail_id = $value->trx_movement_detail_id;
				$arrMoveOut[$key] = $arrOut;

				//? Data movement from
				$arrIn = new stdClass();
				$arrIn->assetcode = $value->assetcode;
				$arrIn->md_product_id = $value->md_product_id;
				$arrIn->md_employee_id = $value->employee_from;
				$arrIn->md_branch_id = $value->branch_from;
				$arrIn->md_division_id = $value->division_from;
				$arrIn->md_room_id = $value->room_from;
				$arrIn->transactiontype = $this->Movement_In;
				$arrIn->transactiondate = date("Y-m-d");
				$arrIn->qtyentered = 1;
				$arrIn->trx_movement_detail_id = $value->trx_movement_detail_id;
				$arrMoveIn[$key] = $arrIn;
			endforeach;

			$arrInv = (array) array_merge(
				(array) $arrMoveIn
			);

			$arrData = (array) array_merge(
				(array) $arrMoveOut,
				(array) $arrMoveIn
			);

			// $mInventory->edit($arrInv);
			$mTransaction->create($arrData);
			// }
		}

		return $rows;
	}
}
