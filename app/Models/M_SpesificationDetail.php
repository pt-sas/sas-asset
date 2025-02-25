<?php

namespace App\Models;

use CodeIgniter\Model;

use CodeIgniter\HTTP\RequestInterface;

class M_SpesificationDetail extends Model
{
	protected $table      		= 'trx_spesification_detail';
	protected $primaryKey 		= 'trx_spesification_detail_id';
	protected $allowedFields	= [
		'trx_spesification_id',
		'necessary',
		'lineno',
		'md_sparepart_id',
		'isactive',
		'created_by',
		'updated_by',
		'description'
	];
	protected $useTimestamps	= true;
	protected $allowCallbacks	= true;
	protected $returnType		= 'App\Entities\SpesificationDetail';
	protected $beforeInsert 	= [];
	protected $afterInsert		= [];
	protected $beforeUpdate		= [];
	protected $afterUpdate		= [];
	protected $beforeDelete		= [];
	protected $afterDelete		= [];
	protected $db;
	protected $builder;
	protected $Inventory_In;
	protected $Inventory_Out;

	public function __construct(RequestInterface $request)
	{
		parent::__construct();
		$this->db = db_connect();
		$this->request = $request;
		$this->builder = $this->db->table($this->table);
	}

	public function doChangeValueField(array $data, int $foreignKey)
	{
		$mSparePart = new M_SparePart($this->request);
		$mCategory = new M_Category($this->request);

		$result = [];

		foreach ($data as $row) :
			$valPart = $mSparePart->where($mSparePart->primaryKey, $row['md_sparepart_id'])->first();
			$valCat = $mCategory->where($mCategory->primaryKey, $valPart->md_category_id)->first();

			if ($valCat->name === "MEMORY") {
				$row['necessary'] = "MEMORY";
			} else {
				$row['necessary'] = "PENYIMPANAN";
			}

			$result[] = $row;
		endforeach;

		return $result;
	}
}