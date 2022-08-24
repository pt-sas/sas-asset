<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;

class M_Quotation extends Model
{
	protected $table                = 'trx_quotation';
	protected $primaryKey           = 'trx_quotation_id';
	protected $allowedFields        = [
		'documentno',
		'description',
		'docstatus',
		'quotationdate',
		'md_supplier_id',
		'md_status_id',
		'created_by',
		'updated_by',
		'grandtotal',
		'isinternaluse',
		'md_employee_id'
	];
	protected $useTimestamps        = true;
	protected $returnType 			= 'App\Entities\Quotation';
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
		'trx_quotation.documentno',
		'trx_quotation.quotationdate',
		'md_supplier.name' || 'md_employee.name',
		'md_status.name',
		'trx_quotation.grandtotal',
		'trx_quotation.docstatus',
		'sys_user.name',
		'trx_quotation.description'
	];
	protected $column_search = [
		'trx_quotation.documentno',
		'trx_quotation.description',
		'trx_quotation.quotationdate',
		'trx_quotation.docstatus',
		'trx_quotation.grandtotal',
		'md_supplier.name' || 'md_employee.name',
		'md_status.name',
		'sys_user.name',
		'md_employee.name'
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
			sys_user.name as createdby,
			md_employee.name as employee';

		return $sql;
	}

	public function getJoin()
	{
		$sql = [
			$this->setDataJoin('md_supplier', 'md_supplier.md_supplier_id = ' . $this->table . '.md_supplier_id', 'left'),
			$this->setDataJoin('md_employee', 'md_employee.md_employee_id = ' . $this->table . '.md_employee_id', 'left'),
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

	public function getInvNumber($field, $where)
	{
		$month = date('m');

		$this->builder->select('MAX(RIGHT(documentno,4)) AS documentno');
		$this->builder->where("DATE_FORMAT(quotationdate, '%m')", $month);
		$this->builder->where($field, $where);
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

		$first = "IU";

		if ($where === 'N')
			$first = "QU";

		$prefix = $first . date('ym') . $code;

		return $prefix;
	}

	public function mandatoryLogic($table)
	{
		$result = [];

		foreach ($table as $row) :

			// Condition to check isspare
			if ($row[4]->isspare)
				$row[5]->employee_id = 0;

			$result[] = $row;
		endforeach;

		return $result;
	}

	public function getDetail($field = null, $where = null)
	{
		$this->builder->join('trx_quotation_detail', 'trx_quotation_detail.trx_quotation_id = ' . $this->table . '.trx_quotation_id', 'left');

		if (!empty($field) && !empty($where)) {
			$this->builder->where($field, $where);
		}

		$this->builder->orderBy($this->table . '.created_at', 'DESC');

		return $this->builder->get();
	}

	public function checkExistQuotation($where = null, $like = [])
	{

		$sql = "SELECT q.*,
			p.name as supplier,
			e.name as employee
		FROM trx_quotation q
		LEFT JOIN md_supplier p ON p.md_supplier_id = q.md_supplier_id
		LEFT JOIN md_employee e ON e.md_employee_id = q.md_employee_id
		WHERE q.docstatus = 'CO' ";

		if (!empty($where)) {
			$sql .= "AND NOT EXISTS(SELECT 1 FROM trx_receipt r 
								WHERE r.trx_quotation_id = q.trx_quotation_id
								AND q.trx_quotation_id <> (SELECT re.trx_quotation_id 
										FROM trx_receipt re 
										WHERE re.trx_receipt_id = ?))";
		} else {
			$sql .= "AND NOT EXISTS(SELECT 1 FROM trx_receipt r WHERE r.trx_quotation_id = q.trx_quotation_id)";
		}

		if (count($like) > 0) {
			foreach ($like as $key => $row) :
				$sql .= "AND $key LIKE '%" . $row . "%'";
			endforeach;
		}


		$sql .= "ORDER BY q.created_at DESC";
		return $this->db->query($sql, $where);
	}

	public function createDetail(array $rows)
	{
		$quotationDetail = new M_QuotationDetail($this->request);

		$post = $this->request->getVar();

		if (isset($post['table'])) {
			$post['trx_quotation_id'] = $rows['id'];
			$quotationDetail->create($post);
		}
	}

	public function deleteDetail(array $rows)
	{
		$quotationDetail = new M_QuotationDetail($this->request);
		$quotationDetail->where($this->primaryKey, $rows['id'])->delete();
	}
}
