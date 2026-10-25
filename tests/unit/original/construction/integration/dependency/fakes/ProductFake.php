<?php

namespace CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes;

class ProductFake
{
    public function __construct(
        protected CustomerFake $customer
    ) {}
        
}