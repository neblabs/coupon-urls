<?php

namespace CouponURLs\Tests\Unit\Original\Construction\Dependency\Fakes;

use CouponURLs\Original\Dependency\Abilities\Context;
use CouponURLs\Original\Dependency\Dependency;

class SimpleDependencyFake implements Dependency
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