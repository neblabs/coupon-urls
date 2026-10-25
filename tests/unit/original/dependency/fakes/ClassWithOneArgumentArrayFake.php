<?php

namespace CouponURLs\Tests\Unit\Original\Dependency\Fakes;

class ClassWithOneArgumentArrayFake
{
    public function __construct(
        protected array $items        
    ) {}
}