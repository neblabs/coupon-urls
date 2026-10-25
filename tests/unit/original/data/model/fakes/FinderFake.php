<?php

namespace CouponURLs\Tests\Unit\Original\Data\Model\Fakes;

use CouponURLs\Original\Data\Model\Finder;

class FinderFake extends Finder
{
    public function withName(string $name) : static
    {
        $this->parameters->name = $name;

        return $this;
    }

    public function withId(int $id) : static
    {
        $this->parameters->id = $id;
        
        return $this;
    }
}