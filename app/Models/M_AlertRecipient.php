<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;

class M_AlertRecipient extends Model
{
	protected $table      = 'md_alertrecipient';
	protected $primaryKey = 'md_alertrecipient_id';
	protected $allowedFields = [
		'record_id',
		'sys_user_id',
		'sys_role_id',
		'isactive',
		'created_by',
		'updated_by'
	];
	protected $useTimestamps = true;
	protected $returnType = 'App\Entities\AlertRecipient';
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
}
