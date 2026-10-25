<?php

namespace CouponURLs\Tests\Unit\Original\Dependency\Fakes;

class ClassWithOneArgumentBooleanFake
{
    public function __construct(
        protected bool $isEnabled
    ) {}
}