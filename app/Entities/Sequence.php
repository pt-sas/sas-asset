<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Sequence extends Entity
{
    protected $md_sequence_id;
    protected $name;
    protected $description;
    protected $isactive;
    protected $vformat;
    protected $isautosequence;
    protected $incrementno;
    protected $startno;
    protected $currentnext;
    protected $prefix;
    protected $suffix;
    protected $startnewyear;
    protected $datecolumn;
    protected $decimalpattern;
    protected $startnewmonth;
    protected $isgassetlevelsequence;
    protected $gassetcolumn;
    protected $iscategorylevelsequence;
    protected $categorycolumn;
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

    public function getIsActive()
    {
        return $this->attributes['isactive'];
    }

    public function setIsActive($isactive)
    {
        $this->attributes['isactive'] = $isactive;
    }

    public function getVFormat()
    {
        return $this->attributes['vformat'];
    }

    public function setVFormat($vformat)
    {
        $this->attributes['vformat'] = $vformat;
    }

    public function getIsAutoSequence()
    {
        return $this->attributes['isautosequence'];
    }

    public function setIsAutoSequence($isautosequence)
    {
        $this->attributes['isautosequence'] = $isautosequence;
    }

    public function getIncrementNo()
    {
        return $this->attributes['incrementno'];
    }

    public function setIncrementNo($incrementno)
    {
        $this->attributes['incrementno'] = $incrementno;
    }

    public function getStartNo()
    {
        return $this->attributes['startno'];
    }

    public function setStartNo($startno)
    {
        $this->attributes['startno'] = $startno;
    }

    public function getCurrentNext()
    {
        return $this->attributes['currentnext'];
    }

    public function setCurrentNext($currentnext)
    {
        $this->attributes['currentnext'] = $currentnext;
    }

    public function getPrefix()
    {
        return $this->attributes['prefix'];
    }

    public function setPrefix($prefix)
    {
        $this->attributes['prefix'] = $prefix;
    }

    public function getSuffix()
    {
        return $this->attributes['suffix'];
    }

    public function setSuffix($suffix)
    {
        $this->attributes['suffix'] = $suffix;
    }

    public function getStartNewYear()
    {
        return $this->attributes['startnewyear'];
    }

    public function setStartNewYear($startnewyear)
    {
        $this->attributes['startnewyear'] = $startnewyear;
    }

    public function getDateColumn()
    {
        return $this->attributes['datecolumn'];
    }

    public function setDateColumn($datecolumn)
    {
        $this->attributes['datecolumn'] = $datecolumn;
    }

    public function getDecimalPattern()
    {
        return $this->attributes['decimalpattern'];
    }

    public function setDecimalPattern($decimalpattern)
    {
        $this->attributes['decimalpattern'] = $decimalpattern;
    }

    public function getStartNewMonth()
    {
        return $this->attributes['startnewmonth'];
    }

    public function setStartNewMonth($startnewmonth)
    {
        $this->attributes['startnewmonth'] = $startnewmonth;
    }

    public function getIsGAssetLevelSequence()
    {
        return $this->attributes['isgassetlevelsequence'];
    }

    public function setIsGAssetLevelSequence($isgassetlevelsequence)
    {
        $this->attributes['isgassetlevelsequence'] = $isgassetlevelsequence;
    }

    public function getGAssetColumn()
    {
        return $this->attributes['gassetcolumn'];
    }

    public function setGAssetColumn($gassetcolumn)
    {
        $this->attributes['gassetcolumn'] = $gassetcolumn;
    }

    public function getIsCategoryLevelSequence()
    {
        return $this->attributes['iscategorylevelsequence'];
    }

    public function setIsCategoryLevelSequence($iscategorylevelsequence)
    {
        $this->attributes['iscategorylevelsequence'] = $iscategorylevelsequence;
    }

    public function getCategoryColumn()
    {
        return $this->attributes['categorycolumn'];
    }

    public function setCategoryColumn($categorycolumn)
    {
        $this->attributes['categorycolumn'] = $categorycolumn;
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
