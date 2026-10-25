<?php declare(strict_types=1);

use CouponURLs\Original\Abilities\Cached;
use CouponURLs\Original\Abilities\UnCached;
use CouponURLs\Original\Construction\Dependency\DependencyContainerFactory;
use CouponURLs\Original\Construction\Dependency\DependencyInspectorFactory;
use CouponURLs\Original\Dependency\Abilities\StaticType;
use CouponURLs\Original\Dependency\CachedInstanceDependencyContainer;
use CouponURLs\Original\Dependency\Dependency;
use CouponURLs\Original\Dependency\UnCachedInstanceDependencyContainer;

it('creates a CachedInstanceDependencyContainer when the Dependency is an instance of Cached', function() {
    /** @var \Mockery\MockInterface&Dependency&StaticType */
    (object) $dependency = Mockery::mock(Dependency::class, Cached::class, StaticType::class);

    (object) $DependencyContainerFactory = new DependencyContainerFactory(
        new DependencyInspectorFactory
    );

    expect($DependencyContainerFactory->create($dependency))
        ->toBeInstanceOf(CachedInstanceDependencyContainer::class)
        ->toEqual(new CachedInstanceDependencyContainer($dependency));
});

it('creates an UnCachedInstanceDependencyContainer when the Dependency is an instance of UnCached', function() {
    /** @var \Mockery\MockInterface&Dependency&StaticType */
    (object) $dependency = Mockery::mock(Dependency::class, UnCached::class, StaticType::class);
    (object) $DependencyContainerFactory = new DependencyContainerFactory(
        new DependencyInspectorFactory
    );

    expect($DependencyContainerFactory->create($dependency))
        ->toBeInstanceOf(UnCachedInstanceDependencyContainer::class)
        ->toEqual(new UnCachedInstanceDependencyContainer($dependency));
});
