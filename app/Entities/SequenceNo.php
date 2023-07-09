<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class SequenceNo extends Entity
{
    protected $md_sequence_id;
    protected $calendaryearmonth;
    protected $md_groupasset_id;
    protected $categorycode;
    protected $isactive;
    protected $currentnext;
    protected $maxvalue;
    protected $created_by;
    protected $updated_by;

    protected $dates   = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getSequenceId()
    {
        return $this->attributes['md_sequence_id'];
    }

    public function setSequenceId($md_sequence_id)
    {
        $this->attributes['md_sequence_id'] = $md_sequence_id;
    }

    public function getCalendarYearMonth()
    {
        return $this->attributes['calendaryearmonth'];
    }

    public function setCalendarYearMonth($calendaryearmonth)
    {
        $this->attributes['calendaryearmonth'] = $calendaryearmonth;
    }

    public function getGroupAssetId()
    {
        return $this->attributes['md_groupasset_id'];
    }

    public function setGroupAssetId($md_groupasset_id)
    {
        $this->attributes['md_groupasset_id'] = $md_groupasset_id;
    }

    public function getCategoryCode()
    {
        return $this->attributes['categorycode'];
    }

    public function setCategoryCode($categorycode)
    {
        $this->attributes['categorycode'] = $categorycode;
    }

    public function getIsActive()
    {
        return $this->attributes['isactive'];
    }

    public function setIsActive($isactive)
    {
        $this->attributes['isactive'] = $isactive;
    }

    public function getCurrentNext()
    {
        return $this->attributes['currentnext'];
    }

    public function setCurrentNext($currentnext)
    {
        $this->attributes['currentnext'] = $currentnext;
    }

    public function getMaxValue()
    {
        return $this->attributes['maxvalue'];
    }

    public function setMaxValue($maxvalue)
    {
        $this->attributes['maxvalue'] = $maxvalue;
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
