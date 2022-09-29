<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;
use App\Models\M_ReceiptDetail;
use App\Models\M_Sequence;
use App\Models\M_QuotationDetail;
use App\Models\M_Inventory;
use App\Models\M_Transaction;
use App\Libraries\Field;

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
		'updated_by',
		'expenseno',
		'isinternaluse',
		'md_employee_id'
	];
	protected $useTimestamps        = true;
	protected $returnType 			= 'App\Entities\Receipt';
	protected $allowCallbacks		= true;
	protected $beforeInsert			= [];
	protected $afterInsert			= [];
	protected $beforeUpdate			= ['createCodeAsset'];
	protected $afterUpdate			= ['createDetail'];
	protected $beforeDelete			= ['beforeDelete'];
	protected $afterDelete			= ['deleteDetail'];
	protected $column_order = [
		'', // Hide column
		'', // Number column
		'trx_receipt.documentno',
		'trx_receipt.receiptdate',
		'md_supplier.name' || 'md_employee.name',
		'md_status.name',
		'trx_receipt.expenseno',
		'trx_receipt.invoiceno',
		'trx_receipt.grandtotal',
		'trx_receipt.docstatus',
		'sys_user.name',
		'trx_receipt.description'
	];
	protected $column_search = [
		'trx_receipt.documentno',
		'trx_receipt.receiptdate',
		'md_supplier.name',
		'md_status.name',
		'trx_receipt.expenseno',
		'trx_receipt.invoiceno',
		'trx_receipt.grandtotal',
		'trx_receipt.docstatus',
		'sys_user.name',
		'trx_receipt.description'

	];
	protected $order = ['created_at' => 'DESC'];
	protected $request;
	protected $db;
	protected $builder;
	protected $field;
	/** Drafted = DR */
	protected $DOCSTATUS_Drafted = "DR";
	/** Completed = CO */
	protected $DOCSTATUS_Completed = "CO";
	/** Approved = AP */
	protected $DOCSTATUS_Approved = "AP";
	/** Not Approved = NA */
	protected $DOCSTATUS_NotApproved = "NA";
	/** Voided = VO */
	protected $DOCSTATUS_Voided = "VO";
	/** Invalid = IN */
	protected $DOCSTATUS_Invalid = "IN";
	/** In Progress = IP */
	protected $DOCSTATUS_Inprogress = "IP";
	/** Inventory In */
	protected $Inventory_In = 'I+';
	/** Inventory Out */
	protected $Inventory_Out = 'I-';

	public function __construct(RequestInterface $request)
	{
		parent::__construct();
		$this->db = db_connect();
		$this->request = $request;
		$this->builder = $this->db->table($this->table);
		$this->field = new Field();
	}

	public function getSelect()
	{
		$sql = $this->table . '.*,' .
			'md_supplier.name as supplier,
            md_status.name as status,
			sys_user.name as createdby,
			md_employee.name as employee';

		return $sql;
	}

	public function getJoin()
	{
		$sql = [
			$this->setDataJoin('md_supplier', 'md_supplier.md_supplier_id = ' . $this->table . '.md_supplier_id', 'left'),
			$this->setDataJoin('md_status', 'md_status.md_status_id = ' . $this->table . '.md_status_id', 'left'),
			$this->setDataJoin('sys_user', 'sys_user.sys_user_id = ' . $this->table . '.created_by', 'left'),
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

	public function getDetail($field = null, $where = null)
	{
		$this->builder->join('trx_receipt_detail', 'trx_receipt_detail.trx_receipt_id = ' . $this->table . '.trx_receipt_id', 'left');

		if (!empty($field) && !empty($where)) {
			$this->builder->where($field, $where);
		}

		$this->builder->orderBy($this->table . '.created_at', 'DESC');

		return $this->builder->get();
	}

	public function createDetail($rows)
	{
		$receiptDetail = new M_ReceiptDetail($this->request);
		$quotationDetail = new M_QuotationDetail($this->request);
		$inventory = new M_Inventory($this->request);
		$transaction = new M_Transaction();
		$changelog = new M_ChangeLog($this->request);

		$post = $this->request->getVar();

		if (isset($post['docaction'])) {
			$row = $this->find($post['id']);
			$line = $receiptDetail->where($this->primaryKey, $post['id'])->findAll();

			//? Exists data line and docstatus Completed
			if (count($line) > 0 && $post['docaction'] === $this->DOCSTATUS_Completed) {
				$arrQuoDetail = $receiptDetail->getSumQtyGroup($post['id'])->getResult();

				//TODO: Insert Change Log 
				foreach ($arrQuoDetail as $value) :
					$primaryID = $value->trx_quotation_detail_id;
					$old = $quotationDetail->find($primaryID);

					$data = (array) $value;

					foreach (array_keys($data) as $key) :
						if ($key !== $quotationDetail->primaryKey)
							$changelog->insertLog($quotationDetail->table, $key, $primaryID, $old->{$key}, $value->$key, 'U');
					endforeach;
				endforeach;

				//* Update qtyreceipt table trx_quotation_detail
				$quotationDetail->updateQty($arrQuoDetail, 'qtyreceipt');

				//* Passing data to table inventory
				$line = $this->field->mergeArrObject($line, [
					'md_status_id'      => $row->getStatusId(),
					'receiptdate'		=> $row->getReceiptDate()
				]);

				$inventory->create($line);

				//* Passing data to table transaction
				$line = $this->field->mergeArrObject($line, [
					'transactiontype'   => $this->Inventory_In,
					'transactiondate'   => $row->getReceiptDate()
				]);

				$transaction->create($line);
			}
		}

		return $rows;
	}

	public function beforeDelete(array $rows)
	{
		$receiptDetail = new M_ReceiptDetail($this->request);
		$inventory = new M_Inventory($this->request);
		$quotationDetail = new M_QuotationDetail($this->request);

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
		$receiptDetail = new M_ReceiptDetail($this->request);
		$receiptDetail->where($this->primaryKey, $rows['id'])->delete();
	}

	public function createCodeAsset($rows)
	{
		$sequence = new M_Sequence($this->request);
		$receiptDetail = new M_ReceiptDetail($this->request);
		$changelog = new M_ChangeLog($this->request);

		$post = $this->request->getVar();

		if (isset($post['docaction'])) {
			$header = $this->find($post['id']);
			$line = $receiptDetail->where($this->primaryKey, $post['id'])->findAll();

			if (count($line) > 0) {
				$data = $sequence->getDocumentNoFromSeq($header, $line);

				//TODO: Insert Change Log 
				foreach ($data as $value) :
					$primaryID = $value['line_id'];

					$arrData = (array) $value;

					foreach (array_keys($arrData) as $key) :
						if ($key !== 'line_id')
							$changelog->insertLog($receiptDetail->table, 'assetcode', $primaryID, null, $value[$key], 'U');
					endforeach;
				endforeach;

				//TODO: Update Field Assetcode Receipt Detail 
				$receiptDetail->edit($data);
			}
		}

		return $rows;
	}

	public function getQuotationReceipt($field = null, $where = null)
	{
		$this->builder->select('trx_quotation.*,
			md_supplier.name as supplier,
			md_employee.name as employee');

		$this->builder->join('trx_quotation', 'trx_quotation.trx_quotation_id = ' . $this->table . '.trx_quotation_id', 'left');
		$this->builder->join('md_supplier', 'md_supplier.md_supplier_id = ' . $this->table . '.md_supplier_id', 'left');
		$this->builder->join('md_employee', 'md_employee.md_employee_id = ' . $this->table . '.md_employee_id', 'left');

		if (!empty($field) && !empty($where)) {
			$this->builder->where($field, $where);
		}

		return $this->builder->get();
	}
}
