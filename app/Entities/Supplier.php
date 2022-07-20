<?php

namespace App\Entities;

use CodeIgniter\Entity;

class Supplier extends Entity
{
	protected $md_supplier_id;
	protected $value;
	protected $name;
	protected $description;
	protected $isactive;
	protected $isvendor;
	protected $isservice;
	protected $created_by;
	protected $updated_by;

	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	public function getSupplierId()
	{
		return $this->attributes['md_supplier_id'];
	}

	public function setSupplierId($md_supplier_id)
	{
		$this->attributes['md_supplier_id'] = $md_supplier_id;
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

	public function getIsVendor()
	{
		return $this->attributes['isvendor'];
	}

	public function setIsVendor($isvendor)
	{
		return $this->attributes['isvendor'] = $isvendor;
	}

	public function getIsService()
	{
		return $this->attributes['isservice'];
	}

	public function setIsService($isservice)
	{
		return $this->attributes['isservice'] = $isservice;
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
