<?php

namespace CouponURLs\Tests\Unit\Original\Dependency\Fakes;

use CouponURLs\Original\Abilities\UnCached;
use CouponURLs\Original\Dependency\Abilities\StaticType;
use CouponURLs\Original\Dependency\Dependency;
use CouponURLs\Original\Dependency\Abilities\Context;

class UnCachedDependencyFake implements UnCached, Dependency, StaticType
{
    static public function type(): string
    {
        
    } 

    public function canBeCreated(Context $context): bool
    {
        
    } 

    public function create(): object
    {
        
    } 
}