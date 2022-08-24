<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;
use App\Models\M_Product;

class M_QuotationDetail extends Model
{
	protected $table      = 'trx_quotation_detail';
	protected $primaryKey = 'trx_quotation_detail_id';
	protected $allowedFields = [
		'trx_quotation_id',
		'md_product_id',
		'qtyentered',
		'unitprice',
		'description',
		'spesification',
		'md_employee_id',
		'created_by',
		'updated_by'
	];
	protected $useTimestamps = true;
	protected $returnType = 'App\Entities\Quotationdetail';
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

	public function create($post)
	{
		$product = new M_Product($this->request);

		$table = json_decode($post['table']);

		$result = false;

		$sumLineAmt = 0;

		foreach ($table as $row) :
			$valPro = $product->where('name', $row[0]->product_id)->first();

			$data = [
				'md_product_id'     => $valPro->getProductId(),
				'qtyentered'        => $row[1]->qtyentered,
				'unitprice'         => replaceFormat($row[2]->unitprice),
				'lineamt'         	=> replaceFormat($row[3]->lineamt),
				'isspare'			=> setCheckbox($row[4]->isspare),
				'md_employee_id'	=> $row[5]->employee_id,
				'specification'		=> $row[6]->spek,
				'description'       => $row[7]->desc,
				'trx_quotation_id'	=> $post['trx_quotation_id']
			];

			if (!empty($row[8]->delete)) {
				$data['updated_at'] = date('Y-m-d H:i:s');
				$data['updated_by'] = session()->get('sys_user_id');

				$result = $this->builder->where($this->primaryKey, $row[8]->delete)->update($data);
			} else {
				$data['created_at'] = date('Y-m-d H:i:s');
				$data['created_by'] = session()->get('sys_user_id');
				$data['updated_at'] = date('Y-m-d H:i:s');
				$data['updated_by'] = session()->get('sys_user_id');

				$result = $this->builder->insert($data);
			}

			$sumLineAmt += replaceFormat($row[3]->lineamt);

			// Update grand total quotation header
			if ($result) {
				$tableHeader = $this->db->table('trx_quotation');

				$arrData = [
					'grandtotal' => $sumLineAmt
				];

				$tableHeader->where('trx_quotation_id', $post['trx_quotation_id'])->update($arrData);
			}

		endforeach;

		return $result;
	}

	public function updateQty($arrData, $field)
	{
		$result = false;

		foreach ($arrData as $row) :
			$data[$field] = $row->qtyentered;
			$data['updated_at'] = date('Y-m-d H:i:s');
			$data['updated_by'] = session()->get('sys_user_id');

			$result = $this->builder->where($this->primaryKey, $row->trx_quotation_detail_id)->update($data);
		endforeach;

		return $result;
	}
}
