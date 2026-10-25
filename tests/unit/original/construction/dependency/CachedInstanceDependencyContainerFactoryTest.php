<?php declare(strict_types=1);

use CouponURLs\Original\Abilities\Cached;
use CouponURLs\Original\Construction\Dependency\CachedInstanceDependencyContainerFactory;
use CouponURLs\Original\Dependency\Abilities\StaticType;
use CouponURLs\Original\Dependency\CachedInstanceDependencyContainer;
use CouponURLs\Original\Dependency\Dependency;

it('can create only when given an instane of Dependency and an instance of Cached', function() {
    (object) $cachedInstanceDependencyContainerFactory = new CachedInstanceDependencyContainerFactory;
    /** @var \Mockery\MockInterface&Dependency&StaticType */
    (object) $dependency = Mockery::mock(Dependency::class, Cached::class, StaticType::class);

    expect($cachedInstanceDependencyContainerFactory->canCreate($dependency))->toBeTrue();
});

it('creates a CachedInstanceDependencyContainer', function() {
    (object) $cachedInstanceDependencyContainerFactory = new CachedInstanceDependencyContainerFactory;
    /** @var \Mockery\MockInterface&Dependency&StaticType */
    (object) $dependency = Mockery::mock(Dependency::class, Cached::class, StaticType::class);

    expect($cachedInstanceDependencyContainerFactory->create($dependency))
        ->toBeInstanceOf(CachedInstanceDependencyContainer::class)
        ->toEqual(new CachedInstanceDependencyContainer($dependency));
});