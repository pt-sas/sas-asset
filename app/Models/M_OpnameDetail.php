<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;

class M_OpnameDetail extends Model
{
	protected $table      = 'trx_opname_detail';
	protected $primaryKey = 'trx_opname_detail_id';
	protected $allowedFields = [
		'trx_opname_id',
		'assetcode',
		'md_product_id',
		'isbranch',
		'isroom',
		'isemployee',
		'isnew',
		'nocheck',
		'created_by',
		'updated_by'
	];
	protected $useTimestamps 	= true;
	protected $returnType 		= 'App\Entities\OpnameDetail';
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

	/**
	 * Change value of field data
	 *
	 * @param array $data Data
	 * @return array
	 */
	public function doChangeValueField(array $data, int $foreignKey): array
	{
		$result = [];

		foreach ($data as $key => $row) :
			$row['isbranch'] = $row['isbranch'] ?? 'N';
			$row['isroom'] = $row['isroom'] ?? 'N';
			$row['isemployee'] = $row['isemployee'] ?? 'N';

			if (!isset($row[$this->primaryKey]) && !empty($foreignKey)) {
				$line = $this->where([
					"trx_opname_id"	=> $foreignKey,
					"assetcode"		=> $row['assetcode']
				])->first();

				$row[$this->primaryKey] = $line->{$this->primaryKey};
			}

			$result[] = $row;
		endforeach;

		return $result;
	}
}
