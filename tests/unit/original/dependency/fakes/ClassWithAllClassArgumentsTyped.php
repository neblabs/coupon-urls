<?php

namespace CouponURLs\Tests\Unit\Original\Dependency\Fakes;

use stdClass;

class ClassWithAllClassArgumentsTyped
{
    public function __construct(
        protected stdClass $builtinclass,
        protected CustomClassFake $customClassFake,
        protected CustomInterfaceFake $CustomInterface
    ) {}
    
}