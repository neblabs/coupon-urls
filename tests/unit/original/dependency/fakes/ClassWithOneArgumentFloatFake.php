<?php

namespace CouponURLs\Tests\Unit\Original\Dependency\Fakes;

class ClassWithOneArgumentFloatFake
{
    public function __construct(
        protected float $price
    ) {}
}