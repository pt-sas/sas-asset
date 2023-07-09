<?php

namespace App\Entities;

use CodeIgniter\Entity;

class Quotation extends Entity
{
	protected $trx_quotation_id;
	protected $documentno;
	protected $quotationdate;
	protected $grandtotal;
	protected $docstatus;
	protected $md_supplier_id;
	protected $md_status_id;
	protected $description;
	protected $created_by;
	protected $updated_by;
	protected $isinternaluse;
	protected $md_employee_id;
	protected $docreference;
	protected $isfrom;
	protected $md_groupasset_id;
	protected $sys_wfscenario_id;

	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	public function getQuotationId()
	{
		return $this->attributes['trx_quotation_id'];
	}

	public function setQuotationId($trx_quotation_id)
	{
		$this->attributes['trx_quotation_id'] = $trx_quotation_id;
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

	public function getQuotationDate()
	{
		return $this->attributes['quotationdate'];
	}

	public function setQuotationDate($quotationdate)
	{
		$this->attributes['quotationdate'] = $quotationdate;
	}

	public function getStatusId()
	{
		return $this->attributes['md_status_id'];
	}

	public function setStatusId($md_status_id)
	{
		$this->attributes['md_status_id'] = $md_status_id;
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

	public function getEmployeeId()
	{
		return $this->attributes['md_employee_id'];
	}

	public function setEmployeeId($md_employee_id)
	{
		$this->attributes['md_employee_id'] = $md_employee_id;
	}

	public function getDocReference()
	{
		return $this->attributes['docreference'];
	}

	public function setDocReference($docreference)
	{
		$this->attributes['docreference'] = $docreference;
	}

	public function getIsFrom()
	{
		return $this->attributes['isfrom'];
	}

	public function setIsFrom($isfrom)
	{
		$this->attributes['isfrom'] = $isfrom;
	}

	public function getGroupAssetId()
	{
		return $this->attributes['md_groupasset_id'];
	}

	public function setGroupAssetId($md_groupasset_id)
	{
		$this->attributes['md_groupasset_id'] = $md_groupasset_id;
	}

	public function getWfScenarioId()
	{
		return $this->attributes['sys_wfscenario_id'];
	}

	public function setWfScenarioId($sys_wfscenario_id)
	{
		$this->attributes['sys_wfscenario_id'] = $sys_wfscenario_id;
	}
}
