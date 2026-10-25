<?php

namespace CouponURLs\Tests\Unit\Original\Dependency\Fakes;

class ClassWithOneValidArgumentAndAnotherBooleanFake
{
    public function __construct(
        protected ClassWithNoConstrutorFake $correctArgument,
        protected bool $iAmNotTyped
    ) {}
}