<?php

namespace CouponURLs\Tests\Unit\Original\Construction\Integration\Dependency\Fakes;

use CouponURLs\Original\Abilities\Cached;
use CouponURLs\Original\Dependency\Abilities\StaticType;
use CouponURLs\Original\Dependency\DependenciesContainer;
use CouponURLs\Original\Dependency\Dependency;
use CouponURLs\Original\Dependency\Abilities\Context;

class CachedDependencyRequiresDependenciesContainerFake implements Cached, Dependency, StaticType
{
    public function __construct(
        protected DependenciesContainer $dependenciesContainer
    ) {}
    
    static public function type(): string
    {
        return DynamicClassFake::class;
    } 

    public function canBeCreated(Context $context): bool
    {
        return true;  
    } 

    public function create(): object
    {
        (object) $dataObject = new DynamicClassFake;

        $dataObject->container = $this->dependenciesContainer;

        return $dataObject;
    } 
}