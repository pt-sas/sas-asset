<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Disposal extends Entity
{
    protected $trx_disposal_id;
    protected $documentno;
    protected $disposaldate;
    protected $disposaltype;
    protected $grandtotal;
    protected $docstatus;
    protected $md_supplier_id;
    protected $description;
    protected $sys_wfscenario_id;
    protected $created_by;
    protected $updated_by;

    protected $dates   = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getDisposalId()
    {
        return $this->attributes['trx_disposal_id'];
    }

    public function setDisposalId($trx_disposal_id)
    {
        $this->attributes['trx_disposal_id'] = $trx_disposal_id;
    }

    public function getDocumentNo()
    {
        return $this->attributes['documentno'];
    }

    public function setDocumentNo($documentno)
    {
        $this->attributes['documentno'] = $documentno;
    }

    public function getDisposalDate()
    {
        return $this->attributes['disposaldate'];
    }

    public function setDisposalDate($disposaldate)
    {
        $this->attributes['disposaldate'] = $disposaldate;
    }

    public function getDisposalType()
    {
        return $this->attributes['disposaltype'];
    }

    public function setDisposalType($disposaltype)
    {
        $this->attributes['disposaltype'] = $disposaltype;
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

    public function getSupplierId()
    {
        return $this->attributes['md_supplier_id'];
    }

    public function setSupplierId($md_supplier_id)
    {
        $this->attributes['md_supplier_id'] = $md_supplier_id;
    }

    public function getWfScenarioId()
    {
        return $this->attributes['sys_wfscenario_id'];
    }

    public function setWfScenarioId($sys_wfscenario_id)
    {
        $this->attributes['sys_wfscenario_id'] = $sys_wfscenario_id;
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
