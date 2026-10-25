<?php

namespace CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes;

use CouponURLs\Original\Abilities\Cached;
use CouponURLs\Original\Dependency\Abilities\Context;
use CouponURLs\Original\Dependency\Abilities\StaticType;
use CouponURLs\Original\Dependency\Dependency;
use stdClass;

class DependencyThatHasAnotherDependencyButDesNotUseContextFake  implements Cached, StaticType, Dependency
{
    public function __construct(
    ) {}
    
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
        return new stdClass;
    } 
}