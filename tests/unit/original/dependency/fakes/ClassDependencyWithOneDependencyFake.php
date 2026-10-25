<?php

namespace CouponURLs\Tests\Unit\Original\Dependency\Fakes;

use stdClass;

class ClassDependencyWithOneDependencyFake
{
    public function __construct(
        protected stdClass $stdClass
    ) {}
    
}