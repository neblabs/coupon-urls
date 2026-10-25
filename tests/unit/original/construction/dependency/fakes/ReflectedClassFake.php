<?php

namespace CouponURLs\Tests\Unit\Original\Construction\Dependency\Fakes;

class ReflectedClassFake
{
    public function __construct(
        protected ParameterFake $theFakeName
    ) {}
    
}

