<?php

namespace App\Entities;

use CodeIgniter\Entity;

class SparePart extends Entity
{
	protected $md_sparepart_id;
	protected $value;
	protected $name;
	protected $product_category_id;
	protected $md_brand_id;
	protected $md_category_id;
	protected $md_subcategory_id;
	protected $md_type_id;
	protected $md_variant_id;
	protected $description;
	protected $isactive;
	protected $created_by;
	protected $updated_by;

	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	public function getSparePartId()
	{
		return $this->attributes['md_sparepart_id'];
	}

	public function setSparePartId($md_sparepart_id)
	{
		$this->attributes['md_sparepart_id'] = $md_sparepart_id;
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

	public function getProductCategoryId()
	{
		return $this->attributes['product_category_id'];
	}

	public function setProductCategoryId($product_category_id)
	{
		$this->attributes['product_category_id'] = $product_category_id;
	}

	public function getBrandId()
	{
		return $this->attributes['md_brand_id'];
	}

	public function setBrandId($md_brand_id)
	{
		$this->attributes['md_brand_id'] = $md_brand_id;
	}

	public function getCategoryId()
	{
		return $this->attributes['md_category_id'];
	}

	public function setCategoryId($md_category_id)
	{
		$this->attributes['md_category_id'] = $md_category_id;
	}

	public function getSubCategoryId()
	{
		return $this->attributes['md_subcategory_id'];
	}

	public function setSubCategoryId($md_subcategory_id)
	{
		$this->attributes['md_subcategory_id'] = $md_subcategory_id;
	}

	public function getTypeId()
	{
		return $this->attributes['md_type_id'];
	}

	public function setTypeId($md_type_id)
	{
		$this->attributes['md_type_id'] = $md_type_id;
	}

	public function getVariantId()
	{
		return $this->attributes['md_variant_id'];
	}

	public function setVariantId($md_variant_id)
	{
		$this->attributes['md_variant_id'] = $md_variant_id;
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