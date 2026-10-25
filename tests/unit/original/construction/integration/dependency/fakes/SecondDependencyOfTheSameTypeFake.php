<?php

namespace CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes;

use CouponURLs\Original\Abilities\UnCached;
use CouponURLs\Original\Dependency\Abilities\Context;
use CouponURLs\Original\Dependency\Abilities\StaticType;
use CouponURLs\Original\Dependency\Dependency;

class SecondDependencyOfTheSameTypeFake implements UnCached, StaticType, Dependency
{
    static public function type(): string
    {
        return SameTypeFake::class;
    } 

    public function canBeCreated(Context $context): bool
    {
        return $context->nameIs('secondType');
    } 

    public function create(): object
    {
        return new SameTypeFake('second');
    } 
}