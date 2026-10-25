<?php

namespace CouponURLs\Tests\Unit\Original\Dependency;

class ClassWithOneArgumentStringFake
{
    public function __construct(
        protected string $name
    ) {}
}