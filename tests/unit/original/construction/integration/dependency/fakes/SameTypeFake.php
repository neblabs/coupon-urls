<?php

namespace CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes;

class SameTypeFake
{
    public function __construct(
        public readonly string $position
    ) {}
}