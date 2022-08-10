<?php

namespace App\Entities;

use CodeIgniter\Entity;

class Service extends Entity
{
	protected $trx_service_id;
	protected $documentno;
	protected $servicedate;
	protected $grandtotal;
	protected $docstatus;
	protected $md_supplier_id;
	protected $description;
	protected $created_by;
	protected $updated_by;

	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	public function getServiceId()
	{
		return $this->attributes['trx_service_id'];
	}

	public function setServiceId($trx_service_id)
	{
		$this->attributes['trx_service_id'] = $trx_service_id;
	}

	public function getDocumentNo()
	{
		return $this->attributes['documentno'];
	}

	public function setDocumentNo($documentno)
	{
		$this->attributes['documentno'] = $documentno;
	}

	public function getSupplierId()
	{
		return $this->attributes['md_supplier_id'];
	}

	public function setSupplierId($md_supplier_id)
	{
		$this->attributes['md_supplier_id'] = $md_supplier_id;
	}

	public function getServiceDate()
	{
		return $this->attributes['servicedate'];
	}

	public function setServiceDate($servicedate)
	{
		$this->attributes['servicedate'] = $servicedate;
	}

	public function getGrandTotal()
	{
		return $this->attributes['grandtotal'];
	}

	public function setGrandTotal($grandtotal)
	{
		$this->attributes['grandtotal'] = $grandtotal;
	}

	public function getDocStatus()
	{
		return $this->attributes['docstatus'];
	}

	public function setDocStatus($docstatus)
	{
		$this->attributes['docstatus'] = $docstatus;
	}

	public function getDescription()
	{
		return $this->attributes['description'];
	}

	public function setDescription($description)
	{
		$this->attributes['description'] = $description;
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

	public function getIsInternalUse()
	{
		return $this->attributes['isinternaluse'];
	}

	public function setIsInternalUse($isinternaluse)
	{
		$this->attributes['isinternaluse'] = $isinternaluse;
	}
}
