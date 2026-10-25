<?php

namespace CouponURLs\tests\unit\original\collections\Implementations;

use CouponURLs\Original\Collections\Mapper\Mappable;
use CouponURLs\Original\Collections\Mapper\Types;

Class ConcreteKeysMappable extends Mappable
{
    public $data;

    public function getValuesToUnmap()
    {
    }
    
    protected function getMap()
    {
        return [
            'onlyStringsExpected' => Types::STRING
        ];
    }

    public function __construct($data)
    {
        $this->data = $this->map($data);
    }
}