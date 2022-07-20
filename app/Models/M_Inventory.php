<?php

namespace App\Models;

use CodeIgniter\Model;

use App\Models\M_Transaction;

use CodeIgniter\HTTP\RequestInterface;
use stdClass;

class M_Inventory extends Model
{
	protected $table      		= 'trx_inventory';
	protected $primaryKey 		= 'trx_inventory_id';
	protected $allowedFields	= [
		'trx_inventory_id',
		'trx_receipt_detail_id',
		'trx_receipt_id',
		'assetcode',
		'md_product_id',
		'qtyentered',
		'unitprice',
		'md_employee_id',
		'md_branch_id',
		'md_division_id',
		'md_room_id',
		'isspare',
		'isactive',
		'created_by',
		'updated_by'
	];
	protected $useTimestamps	= true;
	protected $allowCallbacks	= true;
	protected $returnType		= 'App\Entities\Inventory';
	protected $beforeInsert 	= [];
	protected $afterInsert		= ['afterInsert'];
	protected $beforeUpdate		= ['beforeUpdate'];
	protected $afterUpdate		= [];
	protected $beforeDelete		= ['beforeDelete'];
	protected $afterDelete		= [];
	protected $column_order		= [
		'', // Hide column
		'', // Number column
		'trx_inventory.assetcode',
		'md_product.name',
		'md_branch.name',
		'md_division.name',
		'md_room.name',
		'md_employee.name',
		'md_status.name',
		'trx_inventory.isactive'
	];
	protected $column_search	= [
		'trx_inventory.assetcode',
		'md_product.name',
		'md_branch.name',
		'md_division.name',
		'md_room.name',
		'md_employee.name',
		'md_status.name',
		'trx_inventory.isactive'
	];
	protected $order			= ['assetcode' => 'ASC'];
	protected $db;
	protected $builder;
	protected $Inventory_In;
	protected $Inventory_Out;

	public function __construct(RequestInterface $request)
	{
		parent::__construct();
		$this->db = db_connect();
		$this->request = $request;
		$this->builder = $this->db->table($this->table);
		/** Inventory In */
		$this->Inventory_In = 'I+';
		/** Inventory Out */
		$this->Inventory_Out = 'I-';
	}

	public function getSelect()
	{
		$sql = $this->table . '.*,
                md_product.name as product,
                md_branch.name as branch,
				md_division.name as division,
				md_room.name as room,
				md_employee.name as employee,
				md_status.name as status';

		return $sql;
	}

	public function getJoin()
	{
		$sql = [
			$this->setDataJoin('md_product', 'md_product.md_product_id = ' . $this->table . '.md_product_id', 'left'),
			$this->setDataJoin('md_branch', 'md_branch.md_branch_id = ' . $this->table . '.md_branch_id', 'left'),
			$this->setDataJoin('md_division', 'md_division.md_division_id = ' . $this->table . '.md_division_id', 'left'),
			$this->setDataJoin('md_room', 'md_room.md_room_id = ' . $this->table . '.md_room_id', 'left'),
			$this->setDataJoin('md_employee', 'md_employee.md_employee_id = ' . $this->table . '.md_employee_id', 'left'),
			$this->setDataJoin('md_status', 'md_status.md_status_id = ' . $this->table . '.md_status_id', 'left')
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

	public function create($arrData)
	{
		foreach ($arrData as $row) :
			$data = [
				'assetcode'					=> $row->assetcode,
				'md_product_id'     		=> $row->md_product_id,
				'isspare'		     		=> $row->isspare,
				'qtyentered'        		=> $row->qtyentered,
				'unitprice'         		=> $row->unitprice,
				'md_employee_id'			=> $row->md_employee_id,
				'md_branch_id'     			=> $row->md_branch_id,
				'md_division_id'    		=> $row->md_division_id,
				'md_room_id'     			=> $row->md_room_id,
				'md_status_id'				=> $row->md_status_id,
				'trx_receipt_id'			=> $row->trx_receipt_id,
				'trx_receipt_detail_id'    	=> $row->trx_receipt_detail_id,
				'created_at'    			=> date('Y-m-d H:i:s'),
				'created_by'    			=> session()->get('sys_user_id'),
				'updated_at'    			=> date('Y-m-d H:i:s'),
				'updated_by'    			=> session()->get('sys_user_id')
			];

			$result = $this->builder->insert($data);
		endforeach;

		return $result;
	}

	public function edit($arrData)
	{
		foreach ($arrData as $row) :
			$data = [
				'md_employee_id'			=> $row->employee_to,
				'md_branch_id'     			=> $row->branch_to,
				'md_division_id'    		=> $row->division_to,
				'md_room_id'     			=> $row->room_to,
				'updated_at'    			=> date('Y-m-d H:i:s'),
				'updated_by'    			=> session()->get('sys_user_id')
			];

			//* ROOM RUANG IT - BARANG BAGUS
			if ($row->room_to == 100040)
				$data['isspare']	= 'Y';
			else if ($row->room_to == 100041) //* ROOM RUANG IT - BARANG RUSAK
				$data['isspare']	= 'N';
			else
				$data['isspare']	= 'N';

			$result = $this->builder->where('assetcode', $row->assetcode)->update($data);
		endforeach;

		return $result;
	}

	public function afterInsert(array $rows)
	{
		$transaction = new M_Transaction();

		$obj = new stdClass();
		$obj->assetcode = $rows['data']['assetcode'];
		$obj->md_product_id = $rows['data']['md_product_id'];
		$obj->transactiontype = $this->Inventory_In;
		$obj->transactiondate = date('Y-m-d');
		$obj->md_room_id = $rows['data']['md_room_id'];
		$obj->md_employee_id = $rows['data']['md_employee_id'];
		$obj->qtyentered = 1;
		$obj->trx_inventory_id = $rows['id'];
		$data[] = $obj;

		$transaction->create($data);
	}

	public function beforeUpdate(array $rows)
	{
		$transaction = new M_Transaction();

		$this->builder->where('trx_inventory_id', $rows['id']);
		$field = $this->builder->get()->getRow();

		if ($field->md_room_id != $rows['data']['md_room_id'] && $field->md_employee_id !=  $rows['data']['md_employee_id']) {
			$in = new stdClass();
			$in->assetcode = $rows['data']['assetcode'];
			$in->md_product_id = $rows['data']['md_product_id'];
			$in->transactiontype = $this->Inventory_In;
			$in->transactiondate = date('Y-m-d');
			$in->md_room_id = $rows['data']['md_room_id'];
			$in->md_employee_id = $rows['data']['md_employee_id'];
			$in->qtyentered = 1;
			$in->trx_inventory_id = $rows['id'];
			$arrInvIn[] = $in;

			$out = new stdClass();
			$out->assetcode = $field->assetcode;
			$out->md_product_id = $field->md_product_id;
			$out->transactiontype = $this->Inventory_Out;
			$out->transactiondate = date('Y-m-d');
			$out->md_room_id = $field->md_room_id;
			$out->md_employee_id = $field->md_employee_id;
			$out->qtyentered = - ($field->qtyentered);
			$out->trx_inventory_id = $rows['id'];
			$arrInvOut[] = $out;

			$arrData = (array) array_merge(
				(array) $arrInvIn,
				(array) $arrInvOut
			);

			$transaction->create($arrData);
		}

		return $rows;
	}

	public function beforeDelete(array $rows)
	{
		$transaction = new M_Transaction();

		$this->builder->where($this->primaryKey, $rows['id']);
		$field = $this->builder->get()->getRow();

		$obj = new stdClass();
		$obj->assetcode = $field->assetcode;
		$obj->md_product_id = $field->md_product_id;
		$obj->transactiontype = $this->Inventory_Out;
		$obj->transactiondate = date('Y-m-d');
		$obj->md_room_id = $field->md_room_id;
		$obj->md_employee_id = $field->md_employee_id;
		$obj->qtyentered = - ($field->qtyentered);
		$obj->trx_inventory_id = $rows['id'];
		$data[] = $obj;

		$transaction->create($data);
	}
}
