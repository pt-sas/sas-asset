<?php

namespace App\Entities;

use CodeIgniter\Entity;

class Employee extends Entity
{
	protected $md_employee_id;
	protected $value;
	protected $name;
	protected $md_branch_id;
	protected $md_division_id;
	protected $md_room_id;
	protected $sys_user_id;
	protected $isactive;
	protected $created_by;
	protected $updated_by;

	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	public function getEmployeeId()
	{
		return $this->attributes['md_employee_id'];
	}

	public function setEmployeeId($md_employee_id)
	{
		$this->attributes['md_employee_id'] = $md_employee_id;
	}

	public function getValue()
	{
		return $this->attributes['value'];
	}

	public function setValue($value)
	{
		$this->attributes['value'] = $value;
	}

	public function getName()
	{
		return $this->attributes['name'];
	}

	public function setName($name)
	{
		$this->attributes['name'] = $name;
	}

	public function getBranchId()
	{
		return $this->attributes['md_branch_id'];
	}

	public function setBranchId($md_branch_id)
	{
		$this->attributes['md_branch_id'] = $md_branch_id;
	}

	public function getDivisionId()
	{
		return $this->attributes['md_division_id'];
	}

	public function setDivisionId($md_division_id)
	{
		$this->attributes['md_division_id'] = $md_division_id;
	}

	public function getRoomId()
	{
		return $this->attributes['md_room_id'];
	}

	public function setRoomId($md_room_id)
	{
		$this->attributes['md_room_id'] = $md_room_id;
	}

	public function getUserId()
	{
		return $this->attributes['sys_user_id'];
	}

	public function setUserId($sys_user_id)
	{
		$this->attributes['sys_user_id'] = $sys_user_id;
	}

	public function getDescription()
	{
		return $this->attributes['description'];
	}

	public function setDescription($description)
	{
		$this->attributes['description'] = $description;
	}

	public function getIsActive()
	{
		return $this->attributes['isactive'];
	}

	public function setIsActive($isactive)
	{
		return $this->attributes['isactive'] = $isactive;
	}

	public function getCreatedAt()
	{
		return $this->attributes['created_at'];
	}

	public function getCreatedBy()
	{
		return $this->attributes['created_by'];
	}

	public function setCreatedBy($created_by)
	{
		$this->attributes['created_by'] = $created_by;
	}

	public function getUpdatedAt()
	{
		return $this->attributes['updated_at'];
	}

	public function getUpdatedBy()
	{
		return $this->attributes['updated_by'];
	}

	public function setUpdatedBy($updated_by)
	{
		$this->attributes['updated_by'] = $updated_by;
	}
}
