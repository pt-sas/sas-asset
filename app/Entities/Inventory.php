<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Inventory extends Entity
{
    protected $trx_inventory_id;
    protected $assetcode;
    protected $inventorydate;
    protected $qtyentered;
    protected $unitprice;
    protected $md_product_id;
    protected $md_branch_id;
    protected $md_division_id;
    protected $md_room_id;
    protected $md_employee_id;
    protected $isspare;
    protected $isactive;
    protected $created_by;
    protected $updated_by;
    protected $md_groupasset_id;
    protected $residualvalue;
    protected $numberPlate;

    protected $dates   = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getInventoryId()
    {
        return $this->attributes['trx_inventory_id'];
    }

    public function setInventoryId($trx_inventory_id)
    {
        $this->attributes['trx_inventory_id'] = $trx_inventory_id;
    }

    public function getAssetCode()
    {
        return $this->attributes['assetcode'];
    }

    public function setAssetCode($assetcode)
    {
        $this->attributes['assetcode'] = $assetcode;
    }

    public function getInventoryDate()
    {
        return $this->attributes['inventorydate'];
    }

    public function setInventoryDate($inventorydate)
    {
        $this->attributes['inventorydate'] = $inventorydate;
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

    public function getProductId()
    {
        return $this->attributes['md_product_id'];
    }

    public function setProductId($md_product_id)
    {
        $this->attributes['md_product_id'] = $md_product_id;
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

    public function getIsSpare()
    {
        return $this->attributes['isspare'];
    }

    public function setIsSpare($isspare)
    {
        $this->attributes['isspare'] = $isspare;
    }

    public function getIsActive()
    {
        return $this->attributes['isactive'];
    }

    public function setIsActive($isactive)
    {
        $this->attributes['isactive'] = $isactive;
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

    public function getGroupAssetId()
    {
        return $this->attributes['md_groupasset_id'];
    }

    public function setGroupAssetId($md_groupasset_id)
    {
        $this->attributes['md_groupasset_id'] = $md_groupasset_id;
    }

    public function getResidualValue()
    {
        return $this->attributes['residualvalue'];
    }

    public function setResidualValue($residualvalue)
    {
        $this->attributes['residualvalue'] = $residualvalue;
    }

    public function getNumberPlate()
    {
        return $this->attributes['numberplate'];
    }

    public function setNumberPlate($numberPlate)
    {
        $this->attributes['numberplate'] = $numberPlate;
    }

    public function getIsNew()
    {
        return $this->attributes['isnew'];
    }

    public function setIsNew($isnew)
    {
        $this->attributes['isnew'] = $isnew;
    }
}
