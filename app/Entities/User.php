<?php

namespace App\Entities;

use CodeIgniter\Entity;

class User extends Entity
{
	protected $sys_user_id;
	protected $username;
	protected $name;
	protected $password;
	protected $description;
	protected $email;
	protected $isactive;
	protected $datelastlogin;
	protected $datepasswordchange;
	protected $created_by;
	protected $updated_by;

	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	public function getUserId()
	{
		return $this->attributes['sys_user_id'];
	}

	public function setUserId($sys_user_id)
	{
		$this->attributes['sys_user_id'] = $sys_user_id;
	}

	public function getUserName()
	{
		return $this->attributes['username'];
	}

	public function setUserName($username)
	{
		$this->attributes['username'] = $username;
	}

	public function getName()
	{
		return $this->attributes['name'];
	}

	public function setName($name)
	{
		$this->attributes['name'] = $name;
	}

	public function getPassword()
	{
		return $this->attributes['password'];
	}

	public function setPassword(string $password)
	{
		$this->attributes['password'] = password_hash($password, PASSWORD_BCRYPT);
	}

	public function getDescription()
	{
		return $this->attributes['description'];
	}

	public function setDescription($description)
	{
		$this->attributes['description'] = $description;
	}

	public function getEmail()
	{
		return $this->attributes['email'];
	}

	public function setEmail($email)
	{
		$this->attributes['email'] = $email;
	}

	public function getDateLastLogin()
	{
		return $this->attributes['datelastlogin'];
	}

	public function setDateLastLogin($datelastlogin)
	{
		return $this->attributes['datelastlogin'] = $datelastlogin;
	}

	public function getIsActive()
	{
		return $this->attributes['isactive'];
	}

	public function setIsActive($isactive)
	{
		return $this->attributes['isactive'] = $isactive;
	}

	public function getDatePasswordChange()
	{
		return $this->attributes['isactive'];
	}

	public function setDatePasswordChange($datepasswordchange)
	{
		return $this->attributes['datepasswordchange'] = $datepasswordchange;
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
