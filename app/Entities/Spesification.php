<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Spesification extends Entity
{
    protected $trx_spesification_id;
    protected $trx_inventory_id;
    protected $processor_id;
    protected $motherboard_id;
    protected $video_graphic_id;
    protected $diskdrive;
    protected $case_id;
    protected $power_supply_id;
    protected $operation_id;
    protected $ip_adress;
    protected $mac_address;
    protected $isactive;
    protected $created_by;
    protected $updated_by;
    protected $description;

    protected $dates   = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getSpesificationId()
    {
        return $this->attributes['trx_spesification_id'];
    }

    public function setSpesificationId($trx_spesification_id)
    {
        $this->attributes['trx_spesification_id'] = $trx_spesification_id;
    }

    public function getInventoryId()
    {
        return $this->attributes['trx_inventory_id'];
    }

    public function setInventoryId($trx_inventory_id)
    {
        $this->attributes['trx_inventory_id'] = $trx_inventory_id;
    }

    public function getProcessorId()
    {
        return $this->attributes['processor_id'];
    }

    public function setProcessorId($processor_id)
    {
        $this->attributes['processor_id'] = $processor_id;
    }

    public function getMotherboardId()
    {
        return $this->attributes['motherboard_id'];
    }

    public function setMotherboardId($motherboard_id)
    {
        $this->attributes['motherboard_id'] = $motherboard_id;
    }

    public function getVideoGraphicId()
    {
        return $this->attributes['video_graphic_id'];
    }

    public function setVideoGraphicId($video_graphic_id)
    {
        $this->attributes['video_graphic_id'] = $video_graphic_id;
    }

    public function getDiskDrive()
    {
        return $this->attributes['diskdrive'];
    }

    public function setDiskDrive($diskdrive)
    {
        $this->attributes['diskdrive'] = $diskdrive;
    }

    public function getCaseId()
    {
        return $this->attributes['case_id'];
    }

    public function setCaseId($case_id)
    {
        $this->attributes['case_id'] = $case_id;
    }

    public function getPowerSupplyId()
    {
        return $this->attributes['power_supply_id'];
    }

    public function setPowerSupplyId($power_supply_id)
    {
        $this->attributes['power_supply_id'] = $power_supply_id;
    }

    public function getOperationId()
    {
        return $this->attributes['operation_id'];
    }

    public function setOperationId($operation_id)
    {
        $this->attributes['operation_id'] = $operation_id;
    }

    public function getIpAddress()
    {
        return $this->attributes['ip_adress'];
    }

    public function setIpAddress($ip_adress)
    {
        $this->attributes['ip_adress'] = $ip_adress;
    }

    public function getMacAddress()
    {
        return $this->attributes['mac_address'];
    }

    public function setMacAddress($mac_address)
    {
        $this->attributes['mac_address'] = $mac_address;
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

    public function getDescription()
    {
        return $this->attributes['description'];
    }

    public function setDescription($description)
    {
        $this->attributes['description'] = $description;
    }
}
