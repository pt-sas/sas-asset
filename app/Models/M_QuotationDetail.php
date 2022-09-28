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
		'lineamt',
		'isspare',
		'description',
		'specification',
		'md_employee_id',
		'created_by',
		'updated_by'
	];
	protected $useTimestamps = true;
	protected $returnType = 'App\Entities\QuotationDetail';
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

	/**
	 * Change value of field data
	 *
	 * @param array $data Data
	 * @return array
	 */
	public function doChangeValueField(array $data): array
	{
		$product = new M_Product($this->request);
		$result = [];

		foreach ($data as $row) :
			$valPro = $product->where('name', $row['md_product_id'])->first();

			$row['md_product_id'] = $valPro->getProductId();

			$result[] = $row;
		endforeach;

		return $result;
	}
}
