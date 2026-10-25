<?php

namespace CouponURLs\Tests\Unit\Original\Dependency\Fakes;

use CouponURLs\Original\Dependency\Dependency;
use CouponURLs\Original\Dependency\Abilities\Context;

class ClassThatImplementsTheInterface implements Dependency
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