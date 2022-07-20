<?php

namespace App\Entities;

use CodeIgniter\Entity;

class Movement extends Entity
{
	protected $trx_movement_id;
	protected $documentno;
	protected $movementdate;
	protected $docstatus;
	protected $description;
	protected $created_by;
	protected $updated_by;

	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	public function getMovementId()
	{
		return $this->attributes['trx_movement_id'];
	}

	public function setMovementId($trx_movement_id)
	{
		$this->attributes['trx_movement_id'] = $trx_movement_id;
	}

	public function getDocumentNo()
	{
		return $this->attributes['documentno'];
	}

	public function setDocumentNo($documentno)
	{
		$this->attributes['documentno'] = $documentno;
	}

	public function getMovementDate()
	{
		return $this->attributes['movementdate'];
	}

	public function setMovementDate($movementdate)
	{
		$this->attributes['movementdate'] = $movementdate;
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
}
