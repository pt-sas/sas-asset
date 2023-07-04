<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Opname extends Entity
{
    protected $trx_opname_id;
    protected $documentno;
    protected $opnamedate;
    protected $docstatus;
    protected $md_branch_id;
    protected $md_room_id;
    protected $md_employee_id;
    protected $startdate;
    protected $enddate;
    protected $description;
    protected $created_by;
    protected $updated_by;

    protected $dates   = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getOpnameId()
    {
        return $this->attributes['trx_opname_id'];
    }

    public function setOpnameId($trx_opname_id)
    {
        $this->attributes['trx_opname_id'] = $trx_opname_id;
    }

    public function getDocumentNo()
    {
        return $this->attributes['documentno'];
    }

    public function setDocumentNo($documentno)
    {
        $this->attributes['documentno'] = $documentno;
    }

    public function getOpnameDate()
    {
        return $this->attributes['opnamedate'];
    }

    public function setOpnameDate($opnamedate)
    {
        $this->attributes['opnamedate'] = $opnamedate;
    }

    public function getBranchId()
    {
        return $this->attributes['md_branch_id'];
    }

    public function setBranchId($md_branch_id)
    {
        $this->attributes['md_branch_id'] = $md_branch_id;
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

    public function getDocStatus()
    {
        return $this->attributes['docstatus'];
    }

    public function setDocStatus($docstatus)
    {
        $this->attributes['docstatus'] = $docstatus;
    }

    public function getStartDate()
    {
        return $this->attributes['startdate'];
    }

    public function setStartDate($startdate)
    {

        $this->attributes['startdate'] = $startdate;
    }

    public function getEndDate()
    {
        return $this->attributes['enddate'];
    }

    public function setEndDate($enddate)
    {
        $this->attributes['enddate'] = $enddate;
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
