<?php declare(strict_types=1);

use CouponURLs\Original\Construction\Dependency\DependencyContainerFactory;
use CouponURLs\Original\Construction\Dependency\DependencyFactory;
use CouponURLs\Original\Construction\Dependency\DependencyTypeToContainerFactory;
use CouponURLs\Original\Dependency\Container;
use CouponURLs\Original\Dependency\Dependency;
use CouponURLs\Original\Dependency\DependencyContainer;

it('creates a Container from a depency type (class Name)', function() {
    /** @var \Mockery\MockInterface&DependencyFactory */
    (object) $dependencyFactory = Mockery::mock(DependencyFactory::class);
    /** @var \Mockery\MockInterface&DependencyContainerFactory */
    (object) $dependencyContainerFactory = Mockery::mock(DependencyContainerFactory::class);
    /** @var \Mockery\MockInterface&Dependency */
    (object) $dependency = Mockery::mock(Dependency::class);
    /** @var \Mockery\MockInterface&DependencyContainer */
    (object) $dependencyContainer = Mockery::mock(DependencyContainer::class);

    (object) $dependencyTypeToContainerFactory = new DependencyTypeToContainerFactory(
        $dependencyFactory,
        $dependencyContainerFactory
    );

    $dependencyFactory->shouldReceive('create')->once()->with('the type')->andReturn($dependency);

    $dependencyContainerFactory->shouldReceive('create')->once()->with($dependency)->andReturn($dependencyContainer);

    expect($dependencyTypeToContainerFactory->create('the type'))
        ->toBeInstanceOf(DependencyContainer::class)
        ->toBe($dependencyContainer);
});
