<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;

class M_ReceiptDetail extends Model
{
	protected $table      = 'trx_receipt_detail';
	protected $primaryKey = 'trx_receipt_detail_id';
	protected $allowedFields = [
		'trx_receipt_id',
		'assetcode',
		'md_product_id',
		'qtyentered',
		'unitprice',
		'priceaftertax',
		'md_branch_id',
		'md_division_id',
		'md_room_id',
		'md_employee_id',
		'description',
		'isspare',
		'trx_quotation_detail_id',
		'created_by',
		'updated_by'
	];
	protected $useTimestamps = true;
	protected $returnType = 'App\Entities\ReceiptDetail';
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

	public function getSumQtyGroup($trx_receipt_id)
	{
		$this->builder->selectSum('qtyentered');
		$this->builder->select('trx_quotation_detail_id');
		$this->builder->where('trx_receipt_id', $trx_receipt_id);
		$this->builder->groupBy('trx_quotation_detail_id');

		return $this->builder->get();
	}

	public function edit($arrData)
	{
		foreach ($arrData as $row) :
			$data['assetcode'] = $row['sequence'];
			$data['updated_at'] = date('Y-m-d H:i:s');
			$data['updated_by'] = session()->get('sys_user_id');

			$result = $this->builder->where($this->primaryKey, $row['line_id'])->update($data);
		endforeach;
		return $result;
	}
}
