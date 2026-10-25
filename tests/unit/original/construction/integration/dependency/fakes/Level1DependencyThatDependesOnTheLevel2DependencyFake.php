<?php

namespace CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes;

use CouponURLs\Original\Abilities\Cached;
use CouponURLs\Original\Dependency\Abilities\Context;
use CouponURLs\Original\Dependency\Abilities\StaticType;
use CouponURLs\Original\Dependency\Dependency;

class Level1DependencyThatDependesOnTheLevel2DependencyFake implements Cached, StaticType, Dependency
{
    public function __construct(
        protected DependencyThatHasAnotherDependencyButDesNotUseContextFake $dependencyThatHasAnotherDependencyButDesNotUseContextFake,
        protected DependentTargetWithOnArgumentFake $one
    ) {}
    
    static public function type(): string
    {
        return Level1DependentFake::class;
    } 

    public function canBeCreated(Context $context): bool
    {
        return true;
    } 

    public function create(): object
    {
        return new Level1DependentFake('rafa');
    } 
}