<?php

namespace App\Entities;

use CodeIgniter\Entity;

class Movement extends Entity
{
	protected $trx_movement_id;
	protected $documentno;
	protected $movementdate;
	protected $movementtype;
	protected $docstatus;
	protected $description;
	protected $md_branch_id;
	protected $md_branchto_id;
	protected $md_division_id;
	protected $md_divisionto_id;
	protected $created_by;
	protected $updated_by;
	protected $ref_movement_id;
	protected $sys_wfscenario_id;
	protected $md_status_id;

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

	public function getMovementType()
	{
		return $this->attributes['movementtype'];
	}

	public function setMovementType($movementtype)
	{
		$this->attributes['movementtype'] = $movementtype;
	}

	public function getBranchId()
	{
		return $this->attributes['md_branch_id'];
	}

	public function setBranchId($md_branch_id)
	{
		$this->attributes['md_branch_id'] = $md_branch_id;
	}

	public function getBranchToId()
	{
		return $this->attributes['md_branchto_id'];
	}

	public function setBranchToId($md_branchto_id)
	{
		$this->attributes['md_branchto_id'] = $md_branchto_id;
	}

	public function getDivisionId()
	{
		return $this->attributes['md_division_id'];
	}

	public function setDivisionId($md_division_id)
	{
		$this->attributes['md_division_id'] = $md_division_id;
	}

	public function getDivisionToId()
	{
		return $this->attributes['md_divisionto_id'];
	}

	public function setDivisionToId($md_divisionto_id)
	{
		$this->attributes['md_divisionto_id'] = $md_divisionto_id;
	}

	public function getRefMovementId()
	{
		return $this->attributes['ref_movement_id'];
	}

	public function setRefMovementId($ref_movement_id)
	{
		$this->attributes['ref_movement_id'] = $ref_movement_id;
	}

	public function getWfScenarioId()
	{
		return $this->attributes['sys_wfscenario_id'];
	}

	public function setWfScenarioId($sys_wfscenario_id)
	{
		$this->attributes['sys_wfscenario_id'] = $sys_wfscenario_id;
	}

	public function getStatusId()
	{
		return $this->attributes['md_status_id'];
	}

	public function setStatusId($md_status_id)
	{
		$this->attributes['md_status_id'] = $md_status_id;
	}
}
