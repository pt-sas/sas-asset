<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;

class M_WActivity extends Model
{
	protected $table                = 'sys_wfactivity';
	protected $primaryKey           = 'sys_wfactivity_id';
	protected $allowedFields        = [
		'sys_wfscenario_id',
		'sys_wfresponsible_id',
		'sys_user_id',
		'state',
		'processed',
		'textmsg',
		'table',
		'record_id',
		'menu',
		'isactive',
		'created_by',
		'updated_by',
	];
	protected $useTimestamps        = true;
	protected $returnType           = 'App\Entities\WActivity';
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

	private function getRole()
	{
		$sql = "SELECT sys_user_role.sys_role_id 
				FROM sys_user_role
				WHERE sys_user_role.sys_user_id = ?";

		$query = $this->db->query($sql, [session()->get('sys_user_id')]);

		$role = [];

		if ($query->getNumRows() > 0) {
			foreach ($query->getResult() as $row) :
				$role[] = $row->sys_role_id;
			endforeach;
		}

		return $role;
	}

	public function getActivity()
	{
		$list = $this->findAll();

		$role = $this->getRole();

		foreach ($list as $row) :
			$this->builder->select($this->table . '.*,' .
				$row->getTable() . '.documentno,
			sys_user.name as usercreated_by');

			$dataField = $this->db->getFieldData($row->table);

			foreach ($dataField as $field) :
				if ($field->primary_key == 1)
					$this->builder->join($row->getTable(), $row->getTable() . '.' . $field->name . '=' . $this->table . '.record_id');
			endforeach;

			$this->builder->join('sys_user', 'sys_user.sys_user_id = ' . $this->table . '.created_by');
			$this->builder->join('sys_wfresponsible', 'sys_wfresponsible.sys_wfresponsible_id = ' . $this->table . '.sys_wfresponsible_id');

			// $role = $this->getResponsibleRole($row->getWfResponsibleId());

			$this->builder->where([
				$this->table . '.state'			=> 'OS',
				$this->table . '.processed'		=> 'N'
			]);

			// Saat user mempunyai lebih dari 1 role approval, dokumen yg harus diapprove belum muncul
			if (!empty($role))
				$this->builder->whereIn('sys_wfresponsible.sys_role_id', $role);

			$this->builder->orderBy($this->table . '.created_at', 'ASC');

			$sql = $this->builder->get()->getResult();
		endforeach;

		return $sql;
	}

	public function countData()
	{
		$role = $this->getRole();

		$this->builder->select($this->table . '.*');
		$this->builder->join('sys_wfresponsible', 'sys_wfresponsible.sys_wfresponsible_id = ' . $this->table . '.sys_wfresponsible_id');
		$this->builder->where([
			$this->table . '.state'			=> 'OS',
			$this->table . '.processed'		=> 'N'
		]);

		if (!empty($role))
			$this->builder->whereIn('sys_wfresponsible.sys_role_id', $role);

		return $this->builder->countAllResults();
	}
}
