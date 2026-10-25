<?php

namespace CouponURLs\tests\unit\original\data\doubles;

use CouponURLs\Original\Data\Model\Domain;
use CouponURLs\Original\Collections\Mapper\Types;

Class DomainDoubleWithMap extends Domain
{
    protected function map()
    {
        return [
            'id' => Types::INTEGER,
            'name' => Types::STRING()->withDefault('the default name'),
            'date' => Types::STRING()->withDefault('default date')
        ];
    }
}