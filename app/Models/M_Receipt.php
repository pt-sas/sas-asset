<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;
use App\Models\M_ReceiptDetail;

class M_Receipt extends Model
{
	protected $table                = 'trx_receipt';
	protected $primaryKey           = 'trx_receipt_id';
	protected $allowedFields        = [
		'documentno',
		'receiptdate',
		'description',
		'docstatus',
		'invoiceno',
		'grandtotal',
		'md_supplier_id',
		'md_status_id',
		'trx_quotation_id',
		'created_by',
		'updated_by'
	];
	protected $useTimestamps        = true;
	protected $returnType 			= 'App\Entities\Receipt';
	protected $allowCallbacks		= true;
	protected $beforeInsert			= [];
	protected $afterInsert			= ['createDetail'];
	protected $beforeUpdate			= [];
	protected $afterUpdate			= ['createDetail'];
	protected $beforeDelete			= ['beforeDelete'];
	protected $afterDelete			= ['deleteDetail'];
	protected $column_order = [
		'', // Hide column
		'', // Number column
		'trx_receipt.documentno',
		'trx_receipt.receiptdate',
		'supplier.name',
		'md_status.name',
		'trx_receipt.invoiceno',
		'trx_receipt.grandtotal',
		'trx_receipt.docstatus',
		'sys_user.name',
		'trx_receipt.description'
	];
	protected $column_search = [
		'trx_receipt.documentno',
		'trx_receipt.description',
		'trx_receipt.receiptdate',
		'trx_receipt.description',
		'trx_receipt.docstatus',
		'trx_receipt.invoiceno',
		'trx_receipt.grandtotal',
		'trx_receipt.isactive',
		'md_supplier.name',
		'md_status.name',
		'sys_user.name'

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
			'md_supplier.name as supplier,
            md_status.name as status,
			sys_user.name as createdby';

		return $sql;
	}

	public function getJoin()
	{
		$sql = [
			$this->setDataJoin('md_supplier', 'md_supplier.md_supplier_id = ' . $this->table . '.md_supplier_id', 'left'),
			$this->setDataJoin('md_status', 'md_status.md_status_id = ' . $this->table . '.md_status_id', 'left'),
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
		$this->builder->where("DATE_FORMAT(receiptdate, '%m')", $month);
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

		$prefix = "RC" . date('ym') . $code;

		return $prefix;
	}

	public function mandatoryLogic($table)
	{
		$result = [];

		foreach ($table as $row) :
			// Condition to check isspare
			if ($row[4]->isspare)
				$row[5]->employee_id = 0;

			// convert format rupiah on the field unitprice
			if (isset($row[3]->unitprice))
				$row[3]->unitprice = replaceFormat($row[3]->unitprice);

			$result[] = $row;
		endforeach;

		return $result;
	}

	public function getDetail($field = null, $where = null)
	{
		$this->builder->join('trx_receipt_detail', 'trx_receipt_detail.trx_receipt_id = ' . $this->table . '.trx_receipt_id', 'left');

		if (!empty($field) && !empty($where)) {
			$this->builder->where($field, $where);
		}

		$this->builder->orderBy($this->table . '.created_at', 'DESC');

		return $this->builder->get();
	}

	public function createDetail(array $rows)
	{
		$receiptDetail = new M_ReceiptDetail();

		$post = $this->request->getVar();

		if (isset($post['table'])) {
			$post['trx_receipt_id'] = $rows['id'];
			$receiptDetail->create($post);
		}
	}

	public function beforeDelete(array $rows)
	{
		$receiptDetail = new M_ReceiptDetail();
		$inventory = new M_Inventory($this->request);
		$quotationDetail = new M_QuotationDetail();

		$this->builder->where($this->primaryKey, $rows['id']);
		$sql = $this->builder->get();

		$row = $sql->getRow();

		if ($row->docstatus == 'CO') {
			$arrQuoDetail = $receiptDetail->getSumQtyGroup($rows['id'])->getResult();

			foreach ($arrQuoDetail as $value) :
				$row = [];
				$row[] = $value->qtyentered = 0;
				$row[] = $value->trx_quotation_detail_id;
			endforeach;

			// Update qtyreceipt on the table quotation detail to 0
			$quotationDetail->updateQty($arrQuoDetail, 'qtyreceipt');

			// Delete data inventory
			$arrDetail = $inventory->where($this->primaryKey, $rows['id'])->findAll();

			foreach ($arrDetail as $value) :
				$inventory->delete($value->getInventoryId());
			endforeach;
		}
	}

	public function deleteDetail(array $rows)
	{
		$receiptDetail = new M_ReceiptDetail();
		$receiptDetail->where($this->primaryKey, $rows['id'])->delete();
	}
}
