<?php

namespace CouponURLs\Tests\Unit\Original\Dependency\Fakes;

class ClassWithOneArgumentIntegerFake
{
    public function __construct(
        protected int $count
    ) {}
}