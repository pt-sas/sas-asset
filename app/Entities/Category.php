<?php

namespace App\Entities;

use CodeIgniter\Entity;

class Category extends Entity
{
	protected $md_category_id;
	protected $value;
	protected $name;
	protected $description;
	protected $initialcode;
	protected $md_groupasset_id;
	protected $isactive;
	protected $created_by;
	protected $updated_by;
	protected $pic;

	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	public function getCategoryId()
	{
		return $this->attributes['md_category_id'];
	}

	public function setCategoryId($md_category_id)
	{
		$this->attributes['md_category_id'] = $md_category_id;
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

	public function getInitialCode()
	{
		return $this->attributes['initialcode'];
	}

	public function setInitialCode($initialcode)
	{
		$this->attributes['initialcode'] = $initialcode;
	}

	public function getGroupAssetId()
	{
		return $this->attributes['md_groupasset_id'];
	}

	public function setGroupAssetId($md_groupasset_id)
	{
		$this->attributes['md_groupasset_id'] = $md_groupasset_id;
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

	public function getPIC()
	{
		return $this->attributes['pic'];
	}

	public function setPIC($pic)
	{
		$this->attributes['pic'] = $pic;
	}
}
