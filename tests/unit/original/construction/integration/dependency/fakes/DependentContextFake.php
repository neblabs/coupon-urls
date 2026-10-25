<?php

namespace CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes;

class DependentContextFake
{
    public function __construct(
        protected SameTypeFake $secondType
    ) {}
    
}