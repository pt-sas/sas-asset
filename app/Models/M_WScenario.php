<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;

class M_WScenario extends Model
{
	protected $table                = 'sys_wfscenario';
	protected $primaryKey           = 'sys_wfscenario_id';
	protected $allowedFields        = [
		'name',
		'lineno',
		'grandtotal',
		'menu',
		'md_status_id',
		'md_branch_id',
		'md_division_id',
		'description',
		'isactive',
		'created_by',
		'updated_by',
	];
	protected $useTimestamps        = true;
	protected $returnType             = 'App\Entities\WScenario';
	protected $allowCallbacks        = true;
	protected $beforeInsert            = [];
	protected $afterInsert            = [];
	protected $beforeUpdate            = [];
	protected $afterUpdate            = [];
	protected $beforeDelete            = [];
	protected $afterDelete            = ['deleteDetail'];
	protected $column_order = [
		'', // Hide column
		'', // Number column
		'm_wscenario.name',
		'm_wscenario.lineno',
		'm_wscenario.grandtotal',
		'm_wscenario.menu',
		'md_status.name',
		'md_branch.name',
		'md_division.name',
		'm_wscenario.description',
		'm_wscenario.isactive'
	];
	protected $column_search = [
		'm_wscenario.name',
		'm_wscenario.lineno',
		'm_wscenario.grandtotal',
		'm_wscenario.menu',
		'md_status.name',
		'md_branch.name',
		'md_division.name',
		'm_wscenario.description',
		'm_wscenario.isactive'
	];
	protected $order = ['name' => 'ASC'];
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
			'md_status.name as status,
			md_branch.name as branch,
			md_division.name as division';

		return $sql;
	}

	public function getJoin()
	{
		$sql = [
			$this->setDataJoin('md_status', 'md_status.md_status_id = ' . $this->table . '.md_status_id', 'left'),
			$this->setDataJoin('md_branch', 'md_branch.md_branch_id = ' . $this->table . '.md_branch_id', 'left'),
			$this->setDataJoin('md_division', 'md_division.md_division_id = ' . $this->table . '.md_division_id', 'left'),
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

	public function deleteDetail(array $rows)
	{
		$scenarioDetail = new M_WScenarioDetail($this->request);
		$scenarioDetail->where($this->primaryKey, $rows['id'])->delete();
	}

	public function getScenario(string $menu, int $md_groupasset_id = null, int $md_status_id = null, int $md_branch_id = null, int $md_division_id = null)
	{
		$this->builder->select('sys_wfscenario_id');
		$this->builder->where('menu', $menu);

		if (!is_null($md_groupasset_id)) {
			$this->builder->where('md_groupasset_id', $md_groupasset_id);
		}

		if (!is_null($md_status_id)) {
			$this->builder->where('md_status_id', $md_status_id);
		}

		if (!is_null($md_branch_id)) {
			$this->builder->where('md_branch_id', $md_branch_id);
		}

		if (!is_null($md_division_id)) {
			$this->builder->where('md_division_id', $md_division_id);
		}

		// if (!empty($grandtotal)) {
		// 	$this->builder->where('grandtotal >=', $grandtotal);
		// }

		$this->builder->orderBy('lineno', 'DESC');

		$sql = $this->builder->get()->getRow();
		return !is_null($sql) ? $sql->sys_wfscenario_id : null;
	}
}
