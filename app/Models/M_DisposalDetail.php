<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;

class M_DisposalDetail extends Model
{
	protected $table      		= 'trx_disposal_detail';
	protected $primaryKey 		= 'trx_disposal_detail_id';
	protected $allowedFields 	= [
		'trx_disposal_id',
		'assetcode',
		'md_product_id',
		'unitprice',
		'condition',
		'created_by',
		'updated_by'
	];
	protected $useTimestamps 	= true;
	protected $returnType 		= 'App\Entities\DisposalDetail';
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

	public function getJournalDisposal(String $whereClause)
	{
		$this->builder->select("trx_inventory.*,
		disposal.period,
		trx_depreciation_detail.costdepreciation,
		trx_depreciation_detail.accumulateddepreciation,
		trx_depreciation_detail.bookvalue,
		trx_disposal_detail.unitprice as sellprice");

		$subQuery1 = "(SELECT trx_disposal_id,
					   documentno,
					   DATE_FORMAT(disposaldate, '%Y-%m') as period,
					   docstatus,
					   disposaltype
					   FROM trx_disposal) disposal";

		$subQuery2 = "(SELECT period
					FROM trx_depreciation_detail trd
					WHERE trd.assetcode = {$this->table}.assetcode
					AND trd.period <= disposal.period
					ORDER BY period DESC
					LIMIT 1)";

		$this->builder->join($subQuery1, "disposal.trx_disposal_id = {$this->table}.trx_disposal_id");
		$this->builder->join("trx_inventory", "trx_inventory.assetcode = {$this->table}.assetcode");
		$this->builder->join('trx_depreciation_detail', "trx_depreciation_detail.assetcode = {$this->table}.assetcode AND trx_depreciation_detail.period = {$subQuery2}", "left", false);

		$this->builder->where($whereClause);

		return $this->builder->get()->getResult();
	}
}
