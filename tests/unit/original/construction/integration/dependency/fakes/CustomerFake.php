<?php

namespace CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes;

class CustomerFake
{
    public function __construct(
        protected AccountFake $account
    ) {}
            
}