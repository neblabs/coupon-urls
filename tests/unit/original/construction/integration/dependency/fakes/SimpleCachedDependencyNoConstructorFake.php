<?php

namespace CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes;

use CouponURLs\Original\Abilities\Cached;
use CouponURLs\Original\Dependency\Abilities\StaticType;
use CouponURLs\Original\Dependency\Dependency;
use CouponURLs\Original\Dependency\Abilities\Context;
use stdClass;

use function CouponURLs\Original\Utilities\Collection\o;

class SimpleCachedDependencyNoConstructorFake implements Cached, Dependency, StaticType
{
    static public function type(): string
    {
        return stdClass::class;
    } 

    public function canBeCreated(Context $context): bool
    {
        return true;
    } 

    public function create(): object
    {
        return o(
            id: 10,
            name: 'rafael'
        );
    } 
}