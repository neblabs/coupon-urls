<?php

namespace CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes;

use CouponURLs\Original\Abilities\Cached;
use CouponURLs\Original\Dependency\Abilities\StaticType;
use CouponURLs\Original\Dependency\Dependency;
use CouponURLs\Original\Dependency\Abilities\Context;
use stdClass;

use function CouponURLs\Original\Utilities\Collection\o;

class CachedDependencyWithOneResgiteredDependency implements Cached, Dependency, StaticType
{
    public function __construct(
        protected UserDataFake $userDataFake
    ) {}
    
    static public function type(): string
    {
        return UserFake::class;
    } 

    public function canBeCreated(Context $context): bool
    {
        return true;
    } 

    public function create(): object
    {
        return new UserFake($this->userDataFake);
    } 
}