<?php declare(strict_types=1);

use CouponURLs\Original\Abilities\UnCached;
use CouponURLs\Original\Construction\Dependency\UnCachedInstanceDependencyContainerFactory;
use CouponURLs\Original\Dependency\Abilities\StaticType;
use CouponURLs\Original\Dependency\Dependency;
use CouponURLs\Original\Dependency\UnCachedInstanceDependencyContainer;

it('can create only when given an instane of Dependency and an instance of UnCached', function() {
    (object) $cachedInstanceDependencyContainerFactory = new UnCachedInstanceDependencyContainerFactory;
    /** @var \Mockery\MockInterface&Dependency&StaticType */
    (object) $dependency = Mockery::mock(Dependency::class, UnCached::class, StaticType::class);

    expect($cachedInstanceDependencyContainerFactory->canCreate($dependency))->toBeTrue();
});

it('creates an UnCachedInstanceDependencyContainer', function() {
    (object) $cachedInstanceDependencyContainerFactory = new UnCachedInstanceDependencyContainerFactory;
    /** @var \Mockery\MockInterface&Dependency&StaticType */
    (object) $dependency = Mockery::mock(Dependency::class, Cached::class, StaticType::class);

    expect($cachedInstanceDependencyContainerFactory->create($dependency))
        ->toBeInstanceOf(UnCachedInstanceDependencyContainer::class)
        ->toEqual(new UnCachedInstanceDependencyContainer($dependency));
});