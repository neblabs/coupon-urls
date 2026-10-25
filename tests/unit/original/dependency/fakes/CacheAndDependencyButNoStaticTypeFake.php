<?php

namespace CouponURLs\Tests\Unit\Original\Dependency\Fakes;

use CouponURLs\Original\Abilities\Cached;
use CouponURLs\Original\Dependency\Dependency;
use CouponURLs\Original\Dependency\Abilities\Context;

class CacheAndDependencyButNoStaticTypeFake implements Dependency, Cached
{
    public function canBeCreated(Context $context): bool
    {
        
    } 

    public function create(): object
    {
        
    } 
}