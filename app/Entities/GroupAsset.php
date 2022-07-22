<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class GroupAsset extends Entity
{
    protected $md_groupasset_id;
    protected $value;
    protected $name;
    protected $description;
    protected $initialcode;
    protected $usefullife;
    protected $isactive;
    protected $created_by;
    protected $updated_by;

    protected $dates   = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getGroupAssetId()
    {
        return $this->attributes['md_groupasset_id'];
    }

    public function setGroupAssetId($md_groupasset_id)
    {
        $this->attributes['md_groupasset_id'] = $md_groupasset_id;
    }

    public function getValue()
    {
        return $this->attributes['value'];
    }

    public function setValue($value)
    {
        $this->attributes['value'] = $value;
    }

    public function getName()
    {
        return $this->attributes['name'];
    }

    public function setName($name)
    {
        $this->attributes['name'] = $name;
    }

    public function getDescription()
    {
        return $this->attributes['description'];
    }

    public function setDescription($description)
    {
        $this->attributes['description'] = $description;
    }

    public function getInitialCode()
    {
        return $this->attributes['initialcode'];
    }

    public function setInitialCode($initialcode)
    {
        $this->attributes['initialcode'] = $initialcode;
    }

    public function getUsefulLife()
    {
        return $this->attributes['usefullife'];
    }

    public function setUsefulLife($usefullife)
    {
        $this->attributes['usefullife'] = $usefullife;
    }

    public function getIsActive()
    {
        return $this->attributes['isactive'];
    }

    public function setIsActive($isactive)
    {
        return $this->attributes['isactive'] = $isactive;
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
