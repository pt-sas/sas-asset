<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Barcode extends Entity
{
    protected $sys_barcode_id;
    protected $barcodetype;
    protected $generatortype;
    protected $widthfactor;
    protected $height;
    protected $text;
    protected $iswithtext;
    protected $positiontext;
    protected $sizetext;
    protected $isactive;
    protected $created_by;
    protected $updated_by;

    protected $dates   = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getBarcodeId()
    {
        return $this->attributes['sys_barcode_id'];
    }

    public function setBarcodeId($sys_barcode_id)
    {
        $this->attributes['sys_barcode_id'] = $sys_barcode_id;
    }

    public function getBarcodeType()
    {
        return $this->attributes['barcodetype'];
    }

    public function setBarcodeType($barcodetype)
    {
        $this->attributes['barcodetype'] = $barcodetype;
    }

    public function getGeneratorType()
    {
        return $this->attributes['generatortype'];
    }

    public function setGeneratorType($generatortype)
    {
        $this->attributes['generatortype'] = $generatortype;
    }

    public function getWidthFactor()
    {
        return $this->attributes['widthfactor'];
    }

    public function setWidthFactor($widthfactor)
    {
        $this->attributes['widthfactor'] = $widthfactor;
    }

    public function getHeight()
    {
        return $this->attributes['height'];
    }

    public function setHeight($height)
    {
        $this->attributes['height'] = $height;
    }

    public function getText()
    {
        return $this->attributes['text'];
    }

    public function setText($text)
    {
        $this->attributes['text'] = $text;
    }

    public function getIsWithText()
    {
        return $this->attributes['iswithtext'];
    }

    public function setIsWithText($iswithtext)
    {
        $this->attributes['iswithtext'] = $iswithtext;
    }

    public function getPositionText()
    {
        return $this->attributes['positiontext'];
    }

    public function setPositionText($positiontext)
    {
        $this->attributes['positiontext'] = $positiontext;
    }

    public function getSizeText()
    {
        return $this->attributes['sizetext'];
    }

    public function setSizeText($sizetext)
    {
        $this->attributes['sizetext'] = $sizetext;
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
