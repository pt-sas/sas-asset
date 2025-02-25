<?php

namespace App\Models;

use CodeIgniter\Model;

use CodeIgniter\HTTP\RequestInterface;

class M_Spesification extends Model
{
	protected $table      		= 'trx_spesification';
	protected $primaryKey 		= 'trx_spesification_id';
	protected $allowedFields	= [
		'trx_inventory_id',
		'processor_id',
		'motherboard_id',
		'video_graphic_id',
		'diskdrive',
		'case_id',
		'power_supply_id',
		'operation_id',
		'ip_adress',
		'mac_address',
		'isactive',
		'created_by',
		'updated_by',
		'description'
	];
	protected $useTimestamps	= true;
	protected $allowCallbacks	= true;
	protected $returnType		= 'App\Entities\Spesification';
	protected $beforeInsert 	= [];
	protected $afterInsert		= [];
	protected $beforeUpdate		= [];
	protected $afterUpdate		= [];
	protected $beforeDelete		= [];
	protected $afterDelete		= [];
	protected $column_order		= [
		'', // Hide column
		'', // Number column
		'trx_inventory.assetcode',
		'md_product.name',
		'trx_spesification.description',
		'sys_user.name'
	];
	protected $column_search	= [
		'trx_inventory.assetcode',
		'md_product.name',
		'trx_spesification.description',
		'sys_user.name'
	];
	protected $order			= ['assetcode' => 'ASC'];
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

	public function getSelect()
	{
		$sql = $this->table . '.*,
				trx_inventory.assetcode as assetcode,
                pc.name as product,
                proci.name as processor,
				mobo.name as motherboard,
				vga.name as vga,
				case.name as case,
				psu.name as psu,
				os.name as os,
				u.name as created_by';

		return $sql;
	}

	public function getJoin()
	{
		$sql = [
			$this->setDataJoin('trx_inventory', 'trx_inventory.trx_inventory_id = ' . $this->table . '.trx_inventory_id', 'left'),
			$this->setDataJoin('md_product pc', 'pc.md_product_id = trx_inventory.md_product_id', 'left'),
			$this->setDataJoin('md_sparepart proci', 'proci.md_sparepart_id = ' . $this->table . '.processor_id', 'left'),
			$this->setDataJoin('md_sparepart mobo', 'mobo.md_sparepart_id = ' . $this->table . '.motherboard_id', 'left'),
			$this->setDataJoin('md_sparepart vga', 'vga.md_sparepart_id = ' . $this->table . '.video_graphic_id', 'left'),
			$this->setDataJoin('md_sparepart case', 'case.md_sparepart_id = ' . $this->table . '.case_id', 'left'),
			$this->setDataJoin('md_sparepart psu', 'psu.md_sparepart_id = ' . $this->table . '.power_supply_id', 'left'),
			$this->setDataJoin('md_sparepart os', 'os.md_sparepart_id = ' . $this->table . '.operation_id', 'left'),
			$this->setDataJoin('sys_user u', 'u.sys_user_id = ' . $this->table . '.created_by', 'left'),
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
}