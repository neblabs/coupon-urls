<?php

namespace CouponURLs\tests\unit\original\collections\Implementations;

use CouponURLs\Original\Collections\JSONObjectsContainer;

Class JSONObjectsContainerDouble extends JSONObjectsContainer
{
    const FIELD_TO_SEARCH = 'theKey';

    protected static function objects()       
    {
        return [
            [
                'theKey' => 'theValue'
            ],
            [
                'theKey' => 'anotherValue'
            ]
        ];
    }
}
