<?php

namespace CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes;

use CouponURLs\Original\Collections\Collection;

class DependentTargetWithOnArgumentFake
{
    public function __construct(
        protected Collection $registeredComponents = new Collection([])
    ) {}
    
}