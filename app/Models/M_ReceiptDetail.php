<?php

namespace App\Models;

use CodeIgniter\Model;

class M_ReceiptDetail extends Model
{
	protected $table      = 'trx_receipt_detail';
	protected $primaryKey = 'trx_receipt_detail_id';
	protected $allowedFields = [
		'trx_receipt_id',
		'md_product_id',
		'qtyentered',
		'unitprice',
		'description',
		'md_employee_id',
		'md_branch_id',
		'md_division_id',
		'md_room_id',
		'created_by',
		'updated_by'
	];
	protected $useTimestamps = true;
	protected $returnType = 'App\Entities\ReceiptDetail';
	protected $db;
	protected $builder;

	public function __construct()
	{
		parent::__construct();
		$this->db = db_connect();
		$this->builder = $this->db->table($this->table);
	}

	public function create($post)
	{
		$table = json_decode($post['table']);

		$result = false;

		$sumLineAmt = 0;

		foreach ($table as $row) :
			$data = [
				// 'assetcode'			=> strtoupper($row[0]->assetcode),
				'md_product_id'     => $row[1]->product_id,
				'qtyentered'        => $row[2]->qtyentered,
				'unitprice'         => replaceFormat($row[3]->unitprice),
				'priceaftertax'     => replaceFormat($row[4]->priceaftertax),
				'isspare'		    => setCheckbox($row[5]->isspare),
				'md_employee_id'    => $row[6]->employee_id,
				'md_branch_id'     	=> $row[7]->branch_id,
				'md_division_id'    => $row[8]->division_id,
				'md_room_id'     	=> $row[9]->room_id,
				'description'       => $row[10]->desc,
				'trx_receipt_id'    => $post['trx_receipt_id']
			];

			if (!empty($row[11]->delete)) {
				$data['updated_at'] = date('Y-m-d H:i:s');
				$data['updated_by'] = session()->get('sys_user_id');

				$result = $this->builder->where($this->primaryKey, $row[11]->delete)->update($data);
			} else {
				$data['trx_quotation_detail_id'] = $row[11]->ref_id;
				$data['created_at'] = date('Y-m-d H:i:s');
				$data['created_by'] = session()->get('sys_user_id');
				$data['updated_at'] = date('Y-m-d H:i:s');
				$data['updated_by'] = session()->get('sys_user_id');

				$result = $this->builder->insert($data);
			}

			$sumLineAmt += replaceFormat($row[3]->unitprice);

			// Update grand total receipt header
			if ($result) {
				$tableHeader = $this->db->table('trx_receipt');

				$arrData = [
					'grandtotal' => $sumLineAmt
				];

				$tableHeader->where('trx_receipt_id', $post['trx_receipt_id'])->update($arrData);
			}
		endforeach;

		return $result;
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
