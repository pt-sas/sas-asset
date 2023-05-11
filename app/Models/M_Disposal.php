<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;

class M_Disposal extends Model
{
	protected $table                = 'trx_disposal';
	protected $primaryKey           = 'trx_disposal_id';
	protected $allowedFields        = [
		'documentno',
		'disposaldate',
		'disposaltype',
		'grandtotal',
		'docstatus',
		'description',
		'md_supplier_id',
		'created_by',
		'updated_by'
	];
	protected $useTimestamps        = true;
	protected $returnType 			= 'App\Entities\Disposal';
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
		'trx_disposal.documentno',
		'trx_disposal.disposaldate',
		'trx_disposal.disposaltype',
		'md_supplier.name',
		'trx_disposal.grandtotal',
		'trx_disposal.docstatus',
		'trx_disposal.description'
	];
	protected $column_search = [
		'trx_disposal.documentno',
		'trx_disposal.disposaldate',
		'trx_disposal.disposaltype',
		'md_supplier.name',
		'trx_disposal.grandtotal',
		'trx_disposal.docstatus',
		'trx_disposal.description'
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
			sys_ref_detail.name as type';

		return $sql;
	}

	public function getJoin()
	{
		//* WF_Participant Type
		$defaultID = 10;

		$sql = [
			$this->setDataJoin('md_supplier', 'md_supplier.md_supplier_id = ' . $this->table . '.md_supplier_id', 'left'),
			$this->setDataJoin('sys_ref_detail', 'sys_ref_detail.sys_reference_id = ' . $defaultID . ' AND sys_ref_detail.value = ' . $this->table . '.disposaltype', 'left'),
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
		$this->builder->where("DATE_FORMAT(disposaldate, '%m')", $month);
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

		$first = "DP";
		$prefix = $first . date('ym') . $code;
		return $prefix;
	}

	public function deleteDetail(array $rows)
	{
		$disposalDetail = new M_DisposalDetail($this->request);
		$disposalDetail->where($this->primaryKey, $rows['id'])->delete();
	}
}
