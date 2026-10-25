<?php

namespace CouponURLs\Tests\Unit\Original\Dependency\Fakes;

class ClassWithOneValidArgumentAndAnotherIntegerFake
{
    public function __construct(
        protected ClassWithNoConstrutorFake $valid,
        protected int $invalid
    ) {}
}