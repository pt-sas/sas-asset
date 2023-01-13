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

	public function getActivity()
	{
		$list = $this->findAll();

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

			$this->builder->where([
				$this->table . '.state'			=> 'OS',
				$this->table . '.processed'		=> 'N',
			]);

			$this->builder->orderBy($this->table . '.created_at', 'ASC');

			$sql = $this->builder->get()->getResult();
		endforeach;

		return $sql;
	}
}
