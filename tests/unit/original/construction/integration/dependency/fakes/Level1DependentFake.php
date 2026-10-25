<?php

namespace CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes;

class Level1DependentFake
{
    public function __construct(
        protected string $name
    ) {}
    
}