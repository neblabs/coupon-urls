<?php

namespace CouponURLs\Tests\Unit\Original\Dependency\Fakes;

class ClassWithOneArgumentNoTypeHintFake
{
    public function __construct(
        protected $iHaveNoConceptOfTypes
    ) {}
}