<?php

namespace CouponURLs\Tests\Unit\Original\Construction\Fakes;

class ClassWithStringParameter
{
    public function __construct(
        protected string $theText
    ) {}
    
}