<?php

namespace CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes;

use CouponURLs\Original\Abilities\UnCached;
use CouponURLs\Original\Dependency\Abilities\Context;
use CouponURLs\Original\Dependency\Abilities\StaticType;
use CouponURLs\Original\Dependency\Dependency;

use function CouponURLs\Original\Utilities\Collection\_;

class Level2DependencyWithAnotherDependencyThatUsesContextFake implements UnCached, StaticType, Dependency
{
    static public function type(): string
    {
        return DependentTargetWithOnArgumentFake::class;    
    } 

    public function canBeCreated(Context $context): bool
    {
        return $context->nameIs('one');
    } 

    public function create(): object
    {
        return new DependentTargetWithOnArgumentFake(_('one'));
    } 
}
