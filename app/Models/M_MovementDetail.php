<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;

class M_MovementDetail extends Model
{
	protected $table      = 'trx_movement_detail';
	protected $primaryKey = 'trx_movement_detail_id';
	protected $allowedFields = [
		'trx_movement_id',
		'assetcode',
		'md_product_id',
		'employee_from',
		'employee_to',
		'branch_from',
		'branch_to',
		'division_from',
		'division_to',
		'room_from',
		'room_to',
		'created_by',
		'updated_by',
		'description',
		'md_status_id',
		'ref_movement_detail_id',
		'isaccept',
		'isnew'
	];
	protected $useTimestamps = true;
	protected $returnType = 'App\Entities\MovementDetail';
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

	public function detail($field = null, $where = null)
	{
		$this->builder->select(
			$this->table . '.*,' .
				'md_status.name as status,
				trx_movement.docstatus'
		);

		$this->builder->join('md_status', 'md_status.md_status_id = ' . $this->table . '.md_status_id', 'left');
		$this->builder->join('trx_movement', 'trx_movement.trx_movement_id = ' . $this->table . '.trx_movement_id', 'right');

		if (!empty($where)) {
			$this->builder->where($field, $where);
		}

		return $this->builder->get();
	}

	public function getMovementDetail()
	{
		$sql = $this->table . '.assetcode,' .
			'trx_movement.*,
			ref.documentno AS no_terima,
			ref.movementdate AS tgl_terima,
			md_product.name AS product,
			mdef.name AS employeefrom,
			mdet.name AS employeeto,
			mdf.name AS divisionfrom,
			mdt.name AS divisionto,
			mbf.name AS branchfrom,
			mbt.name AS branchto,
			mrf.name AS roomfrom,
			mrt.name AS roomto,
			md_status.name AS status';

		return $sql;
	}

	public function getJoinDetail()
	{
		$sql = [
			$this->setDataJoin('trx_movement', 'trx_movement.trx_movement_id =' . $this->table . '.trx_movement_id'),
			$this->setDataJoin('trx_movement ref', 'trx_movement.trx_movement_id = ref.ref_movement_id', 'left'),
			$this->setDataJoin('md_product', 'md_product.md_product_id = ' . $this->table . '.md_product_id', 'left'),
			$this->setDataJoin('md_branch mbf', 'mbf.md_branch_id =' . $this->table . '.branch_from', 'left'),
			$this->setDataJoin('md_branch mbt', 'mbt.md_branch_id =' . $this->table . '.branch_to', 'left'),
			$this->setDataJoin('md_division mdf', 'mdf.md_division_id =' . $this->table . '.division_from', 'left'),
			$this->setDataJoin('md_division mdt', 'mdt.md_division_id =' . $this->table . '.division_to', 'left'),
			$this->setDataJoin('md_room mrf', 'mrf.md_room_id =' . $this->table . '.room_from', 'left'),
			$this->setDataJoin('md_room mrt', 'mrt.md_room_id =' . $this->table . '.room_to', 'left'),
			$this->setDataJoin('md_employee mdef', 'mdef.md_employee_id =' . $this->table . '.employee_from', 'left'),
			$this->setDataJoin('md_employee mdet', 'mdet.md_employee_id =' . $this->table . '.employee_to', 'left'),
			$this->setDataJoin('md_status', 'md_status.md_status_id =' . $this->table . '.md_status_id', 'left'),
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

	/**
	 * Change value of field data
	 *
	 * @param array $data Data
	 * @return array
	 */
	public function doChangeValueField(array $data): array
	{
		$product = new M_Product($this->request);
		$branch = new M_Branch($this->request);
		$employee = new M_Employee($this->request);
		$division = new M_Division($this->request);
		$room = new M_Room($this->request);

		$result = [];

		foreach ($data as $row) :
			$valPro = $product->where('name', $row['md_product_id'])->first();
			$valBranch = $branch->where('name', $row['branch_from'])->first();
			$valEmp = $employee->where('name', $row['employee_from'])->first();
			$valDiv = $division->where('name', $row['division_from'])->first();
			$valRoom = $room->where('name', replaceStrBracket($row['room_from']))->first();

			$row['md_product_id'] = $valPro->getProductId();
			$row['employee_from'] = $valEmp->getEmployeeId();
			$row['branch_from'] = $valBranch->getBranchId();
			$row['division_from'] = $valDiv->getDivisionId();
			$row['room_from'] = $valRoom->getRoomId();

			$result[] = $row;
		endforeach;

		return $result;
	}

	public function getEmployeeToArr($field, $arr_id, $employee_id, $column)
	{
		$list = $this->whereIn($field, $arr_id)
			->where([
				'employee_to'   => $employee_id
			])->findAll();

		$result = [];

		foreach ($list as $value) :
			$result[] = $value->{$column};
		endforeach;

		return $result;
	}
}
