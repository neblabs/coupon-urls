<?php

namespace CouponURLs\Tests\Unit\Original\Collections\Fakes;

interface CheckerFake
{
    public function isEnabled(string $theParameter) : bool;
}