<?php

namespace App\Entities;

use CodeIgniter\Entity;

class ReceiptDetail extends Entity
{
	protected $trx_receipt_detail_id;
	protected $trx_receipt_id;
	protected $assetcode;
	protected $md_product_id;
	protected $qtyentered;
	protected $unitprice;
	protected $md_branch_id;
	protected $md_division_id;
	protected $md_room_id;
	protected $md_employee_id;
	protected $isspare;
	protected $description;
	protected $trx_quotation_detail_id;
	protected $created_by;
	protected $updated_by;

	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	public function getReceiptDetailId()
	{
		return $this->attributes['trx_receipt_detail_id'];
	}

	public function setReceiptDetailId($trx_receipt_detail_id)
	{
		$this->attributes['trx_receipt_detail_id'] = $trx_receipt_detail_id;
	}

	public function getReceiptId()
	{
		return $this->attributes['trx_receipt_id'];
	}

	public function setReceiptId($trx_receipt_id)
	{
		$this->attributes['trx_receipt_id'] = $trx_receipt_id;
	}

	public function getAssetCode()
	{
		return $this->attributes['assetcode'];
	}

	public function setAssetCode($assetcode)
	{
		$this->attributes['assetcode'] = $assetcode;
	}

	public function getProductId()
	{
		return $this->attributes['md_product_id'];
	}

	public function setProductId($md_product_id)
	{
		$this->attributes['md_product_id'] = $md_product_id;
	}

	public function getQtyEntered()
	{
		return $this->attributes['qtyentered'];
	}

	public function setQtyEntered($qtyentered)
	{
		$this->attributes['qtyentered'] = $qtyentered;
	}

	public function getUnitPrice()
	{
		return $this->attributes['unitprice'];
	}

	public function setUnitPrice($unitprice)
	{
		$this->attributes['unitprice'] = $unitprice;
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

	public function getEmployeeId()
	{
		return $this->attributes['md_employee_id'];
	}

	public function setEmployeeId($md_employee_id)
	{
		$this->attributes['md_employee_id'] = $md_employee_id;
	}

	public function getIsSpareId()
	{
		return $this->attributes['isspare'];
	}

	public function setIsSpareId($isspare)
	{
		$this->attributes['isspare'] = $isspare;
	}

	public function getDescription()
	{
		return $this->attributes['description'];
	}

	public function setDescription($description)
	{
		$this->attributes['description'] = $description;
	}

	public function getQuotationDetailId()
	{
		return $this->attributes['trx_quotation_detail_id'];
	}

	public function setQuotationDetailId($trx_quotation_detail_id)
	{
		$this->attributes['trx_quotation_detail_id'] = $trx_quotation_detail_id;
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
