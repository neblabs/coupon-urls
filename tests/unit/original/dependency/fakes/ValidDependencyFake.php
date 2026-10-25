<?php

namespace CouponURLs\Tests\Unit\Original\Dependency\Fakes;

use CouponURLs\Original\Abilities\Cached;
use CouponURLs\Original\Dependency\Abilities\StaticType;
use CouponURLs\Original\Dependency\Dependency;
use CouponURLs\Original\Dependency\Abilities\Context;

class ValidDependencyFake implements Dependency, Cached, StaticType
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