<?php

namespace App\Entities;

use CodeIgniter\Entity;

class Receipt extends Entity
{
	protected $trx_receipt_id;
	protected $documentno;
	protected $receiptdate;
	protected $grandtotal;
	protected $docstatus;
	protected $trx_quotation_id;
	protected $md_supplier_id;
	protected $md_status_id;
	protected $description;
	protected $created_by;
	protected $updated_by;
	protected $docreference;
	protected $invoiceno;
	protected $isinternaluse;
	protected $md_employee_id;
	protected $expenseno;
	protected $invoicedate;

	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	public function getReceiptId()
	{
		return $this->attributes['trx_receipt_id'];
	}

	public function setReceiptId($trx_receipt_id)
	{
		$this->attributes['trx_receipt_id'] = $trx_receipt_id;
	}

	public function getDocumentNo()
	{
		return $this->attributes['documentno'];
	}

	public function setDocumentNo($documentno)
	{
		$this->attributes['documentno'] = $documentno;
	}

	public function getQuotationId()
	{
		return $this->attributes['trx_quotation_id'];
	}

	public function setQuotationId($trx_quotation_id)
	{
		$this->attributes['trx_quotation_id'] = $trx_quotation_id;
	}

	public function getReceiptDate()
	{
		return $this->attributes['receiptdate'];
	}

	public function setReceiptDate($receiptdate)
	{
		$this->attributes['receiptdate'] = $receiptdate;
	}

	public function getSupplierId()
	{
		return $this->attributes['md_supplier_id'];
	}

	public function setSupplierId($md_supplier_id)
	{
		$this->attributes['md_supplier_id'] = $md_supplier_id;
	}

	public function getGrandTotal()
	{
		return $this->attributes['grandtotal'];
	}

	public function setGrandTotal($grandtotal)
	{
		$this->attributes['grandtotal'] = $grandtotal;
	}

	public function getStatusId()
	{
		return $this->attributes['md_status_id'];
	}

	public function setStatusId($md_status_id)
	{
		$this->attributes['md_status_id'] = $md_status_id;
	}

	public function getDocStatus()
	{
		return $this->attributes['docstatus'];
	}

	public function setDocStatus($docstatus)
	{
		$this->attributes['docstatus'] = $docstatus;
	}

	public function getDocReference()
	{
		return $this->attributes['docreference'];
	}

	public function setDocReference($docreference)
	{
		$this->attributes['docreference'] = $docreference;
	}

	public function getInvoiceNo()
	{
		return $this->attributes['invoiceno'];
	}

	public function setInvoiceNo($invoiceno)
	{
		$this->attributes['invoiceno'] = $invoiceno;
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

	public function getExpenseNo()
	{
		return $this->attributes['expenseno'];
	}

	public function setExpenseNo($expenseno)
	{
		$this->attributes['expenseno'] = $expenseno;
	}

	public function getInvoiceDate()
	{
		return $this->attributes['invoicedate'];
	}

	public function setInvoiceDate($invoicedate)
	{
		$this->attributes['invoicedate'] = $invoicedate;
	}
}
