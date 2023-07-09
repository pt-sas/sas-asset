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
}
