<?php

namespace CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes;

use stdClass;

class UserFake
{
    public function __construct(
        protected UserDataFake $data
    ) {}
}