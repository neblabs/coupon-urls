<?php

namespace CouponURLs\Tests\Unit\Original\Dependency\Fakes;

use stdClass;

class ClassWithOneArgumentFake
{
    public function __construct(
        protected stdClass $object
    ) {}
}