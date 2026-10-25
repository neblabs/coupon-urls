<?php

namespace CouponURLs\Tests\Unit\Original\Dependency\Fakes;

class ClassWithOneValidArgumentAndAnotherStringFake
{
    public function __construct(
        protected ClassWithNoConstrutorFake $valid,
        protected string $invalid
    ) {}
}