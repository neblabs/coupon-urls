<?php

namespace CouponURLs\Tests\Unit\Original\Dependency\Fakes;

class ClassWithOneValidArgumentAndAnotherArrayFake
{
    public function __construct(
        protected ClassWithNoConstrutorFake $correctArgument,
        protected array $iAmNotTyped
    ) {}
}